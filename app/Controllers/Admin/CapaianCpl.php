<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CplModel;
use App\Models\MahasiswaModel;
use App\Models\NilaiCpmkMahasiswaModel;

class CapaianCpl extends BaseController
{
	protected $cplModel;
	protected $mahasiswaModel;
	protected $nilaiCpmkModel;

	public function __construct()
	{
		$this->cplModel = new CplModel();
		$this->mahasiswaModel = new MahasiswaModel();
		$this->nilaiCpmkModel = new NilaiCpmkMahasiswaModel();
	}

	public function index()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return redirect()->to('/')->with('error', 'Akses ditolak.');
		}

		// Get CPL passing threshold
		$standarCplModel = new \App\Models\StandarMinimalCplModel();
		$passingThreshold = $standarCplModel->getPersentase();

		$data = [
			'title' => 'Capaian CPL',
			'programStudi' => $this->getProgramStudi(),
			'tahunAngkatan' => $this->getTahunAngkatan(),
			'semesterList' => $this->getSemesterList(),
			'tahunAkademikList' => $this->getTahunAkademikList(),
			'mahasiswa' => [], // Will be loaded via AJAX
			'passing_threshold' => $passingThreshold
		];

		return view('admin/capaian_cpl/index', $data);
	}

	public function getChartData()
	{
		$mahasiswaId = $this->request->getGet('mahasiswa_id');
		$programStudi = $this->request->getGet('program_studi');
		$tahunAngkatan = $this->request->getGet('tahun_angkatan');
		$semester = $this->request->getGet('semester');
		$tahunAkademik = $this->request->getGet('tahun_akademik');

		if (!$mahasiswaId) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Mahasiswa harus dipilih'
			]);
		}

		// Get mahasiswa info
		$mahasiswa = $this->mahasiswaModel->find($mahasiswaId);
		if (!$mahasiswa) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Data mahasiswa tidak ditemukan'
			]);
		}

		// Get all CPL
		$db = \Config\Database::connect();
		$cplList = $db->table('cpl')
			->orderBy('kode_cpl', 'ASC')
			->get()
			->getResultArray();

		if (empty($cplList)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Tidak ada data CPL'
			]);
		}

		// Calculate CPL achievement for the student
		$chartData = [
			'labels' => [],
			'data' => [],
			'details' => []
		];

		foreach ($cplList as $cpl) {
			// Get all CPMK linked to this CPL
			$cpmkLinked = $db->table('cpl_cpmk')
				->select('cpmk_id')
				->where('cpl_id', $cpl['id'])
				->get()
				->getResultArray();

			if (empty($cpmkLinked)) {
				// No CPMK linked to this CPL, skip or set to 0
				$chartData['labels'][] = $cpl['kode_cpl'];
				$chartData['data'][] = 0;
				$chartData['details'][] = [
					'cpl_id' => $cpl['id'],
					'kode_cpl' => $cpl['kode_cpl'],
					'deskripsi' => $cpl['deskripsi'],
					'jenis_cpl' => $cpl['jenis_cpl'],
					'rata_rata' => 0,
					'jumlah_cpmk' => 0,
					'jumlah_mk' => 0
				];
				continue;
			}

			$cpmkIds = array_column($cpmkLinked, 'cpmk_id');

			// Get all assessment scores from nilai_teknik_penilaian for this student and these CPMK
			$nilaiBuilder = $db->table('nilai_teknik_penilaian ntp')
				->select('ntp.nilai, ntp.teknik_penilaian_key, rm.cpmk_id, rm.teknik_penilaian, jm.mata_kuliah_id, jm.id as jadwal_mengajar_id')
				->join('rps_mingguan rm', 'rm.id = ntp.rps_mingguan_id')
				->join('jadwal_mengajar jm', 'jm.id = ntp.jadwal_mengajar_id')
				->where('ntp.mahasiswa_id', $mahasiswaId)
				->whereIn('rm.cpmk_id', $cpmkIds);

			// Apply semester and tahun akademik filters
			if ($semester && $tahunAkademik) {
				$nilaiBuilder->where('jm.tahun_akademik', $tahunAkademik . ' ' . $semester);
			} elseif ($semester) {
				$nilaiBuilder->like('jm.tahun_akademik', $semester, 'after');
			} elseif ($tahunAkademik) {
				$nilaiBuilder->like('jm.tahun_akademik', $tahunAkademik, 'after');
			}

			$nilaiList = $nilaiBuilder->get()->getResultArray();

			// Calculate CPMK scores using weighted formula: (Σ(nilai × bobot / 100) / Σ(bobot)) × 100
			$cpmkScores = [];
			$distinctCpmk = [];
			$distinctMk = [];

			foreach ($nilaiList as $row) {
				$cpmkId = $row['cpmk_id'];

				// Decode the weight (bobot) from JSON
				$teknikData = json_decode($row['teknik_penilaian'], true);
				$bobot = isset($teknikData[$row['teknik_penilaian_key']]) ? floatval($teknikData[$row['teknik_penilaian_key']]) : 0;

				if ($bobot > 0 && $row['nilai'] !== null) {
					if (!isset($cpmkScores[$cpmkId])) {
						$cpmkScores[$cpmkId] = [
							'total_weighted' => 0,
							'total_bobot' => 0
						];
					}

					$cpmkScores[$cpmkId]['total_weighted'] += ($row['nilai'] * $bobot / 100);
					$cpmkScores[$cpmkId]['total_bobot'] += $bobot;
				}

				$distinctCpmk[$cpmkId] = true;
				$distinctMk[$row['jadwal_mengajar_id']] = true;
			}

			// Calculate average CPL from CPMK percentages
			$totalCpmkPercentage = 0;
			$cpmkCount = 0;

			foreach ($cpmkScores as $cpmkId => $scoreData) {
				if ($scoreData['total_bobot'] > 0) {
					// Calculate CPMK percentage
					$cpmkPercentage = ($scoreData['total_weighted'] / $scoreData['total_bobot']) * 100;
					$totalCpmkPercentage += $cpmkPercentage;
					$cpmkCount++;
				}
			}

			// Average CPL = Average of all CPMK percentages
			$average = $cpmkCount > 0 ? round($totalCpmkPercentage / $cpmkCount, 2) : 0;
			$jumlahCpmk = count($distinctCpmk);
			$jumlahMk = count($distinctMk);

			$chartData['labels'][] = $cpl['kode_cpl'];
			$chartData['data'][] = $average;
			$chartData['details'][] = [
				'cpl_id' => $cpl['id'],
				'kode_cpl' => $cpl['kode_cpl'],
				'deskripsi' => $cpl['deskripsi'],
				'jenis_cpl' => $this->getJenisCplLabel($cpl['jenis_cpl']),
				'rata_rata' => $average,
				'jumlah_cpmk' => $jumlahCpmk,
				'jumlah_mk' => $jumlahMk
			];
		}

		return $this->response->setJSON([
			'success' => true,
			'chartData' => $chartData,
			'mahasiswa' => $mahasiswa
		]);
	}

	public function getDetailData()
	{
		$mahasiswaId = $this->request->getGet('mahasiswa_id');
		$cplId = $this->request->getGet('cpl_id');

		if (!$mahasiswaId || !$cplId) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Parameter tidak lengkap'
			]);
		}

		$db = \Config\Database::connect();

		// Get CPL info
		$cpl = $this->cplModel->find($cplId);
		if (!$cpl) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'CPL tidak ditemukan'
			]);
		}

		// Get all CPMK linked to this CPL
		$cpmkLinked = $db->table('cpl_cpmk')
			->select('cpmk.id, cpmk.kode_cpmk, cpmk.deskripsi')
			->join('cpmk', 'cpmk.id = cpl_cpmk.cpmk_id')
			->where('cpl_cpmk.cpl_id', $cplId)
			->orderBy('cpmk.kode_cpmk', 'ASC')
			->get()
			->getResultArray();

		if (empty($cpmkLinked)) {
			return $this->response->setJSON([
				'success' => true,
				'data' => [],
				'cpl' => $cpl,
				'message' => 'Tidak ada CPMK yang terkait dengan CPL ini'
			]);
		}

		// Get nilai for each CPMK
		$detailData = [];
		foreach ($cpmkLinked as $cpmk) {
			// Get all assessment scores from nilai_teknik_penilaian for this CPMK and mahasiswa
			$nilaiList = $db->table('nilai_teknik_penilaian ntp')
				->select('ntp.nilai, ntp.teknik_penilaian_key, rm.teknik_penilaian, mk.kode_mk, mk.nama_mk, jm.tahun_akademik, jm.kelas, jm.id as jadwal_id')
				->join('rps_mingguan rm', 'rm.id = ntp.rps_mingguan_id')
				->join('jadwal_mengajar jm', 'jm.id = ntp.jadwal_mengajar_id')
				->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id')
				->where('ntp.mahasiswa_id', $mahasiswaId)
				->where('rm.cpmk_id', $cpmk['id'])
				->get()
				->getResultArray();

			// Group by mata kuliah and calculate weighted scores
			$mkGroups = [];
			foreach ($nilaiList as $row) {
				$mkKey = $row['kode_mk'];

				if (!isset($mkGroups[$mkKey])) {
					$mkGroups[$mkKey] = [
						'kode_mk' => $row['kode_mk'],
						'nama_mk' => $row['nama_mk'],
						'tahun_akademik' => $row['tahun_akademik'],
						'kelas' => $row['kelas'],
						'total_weighted' => 0,
						'total_bobot' => 0
					];
				}

				// Decode the weight (bobot) from JSON
				$teknikData = json_decode($row['teknik_penilaian'], true);
				$bobot = isset($teknikData[$row['teknik_penilaian_key']]) ? floatval($teknikData[$row['teknik_penilaian_key']]) : 0;

				if ($bobot > 0 && $row['nilai'] !== null) {
					$mkGroups[$mkKey]['total_weighted'] += ($row['nilai'] * $bobot / 100);
					$mkGroups[$mkKey]['total_bobot'] += $bobot;
				}
			}

			// Calculate percentage for each mata kuliah and overall average
			$totalPercentage = 0;
			$mkCount = 0;
			$detailMk = [];

			foreach ($mkGroups as $mkData) {
				if ($mkData['total_bobot'] > 0) {
					$percentage = ($mkData['total_weighted'] / $mkData['total_bobot']) * 100;
					$totalPercentage += $percentage;
					$mkCount++;

					$detailMk[] = [
						'kode_mk' => $mkData['kode_mk'],
						'nama_mk' => $mkData['nama_mk'],
						'tahun_akademik' => $mkData['tahun_akademik'],
						'kelas' => $mkData['kelas'],
						'nilai_cpmk' => round($percentage, 2)
					];
				}
			}

			$rataCpmk = $mkCount > 0 ? round($totalPercentage / $mkCount, 2) : 0;

			$detailData[] = [
				'kode_cpmk' => $cpmk['kode_cpmk'],
				'deskripsi_cpmk' => $cpmk['deskripsi'],
				'rata_rata' => $rataCpmk,
				'jumlah_nilai' => $mkCount,
				'detail_mk' => $detailMk
			];
		}

		return $this->response->setJSON([
			'success' => true,
			'data' => $detailData,
			'cpl' => $cpl
		]);
	}

	public function getMahasiswaByFilter()
	{
		$programStudi = $this->request->getGet('program_studi');
		$tahunAngkatan = $this->request->getGet('tahun_angkatan');
		$semester = $this->request->getGet('semester');
		$tahunAkademik = $this->request->getGet('tahun_akademik');

		$db = \Config\Database::connect();

		// If semester or tahun_akademik filters are applied, we need to filter mahasiswa who have grades in matching jadwal
		if ($semester || $tahunAkademik) {
			$builder = $db->table('mahasiswa m')
				->select('DISTINCT m.id, m.nim, m.nama_lengkap, m.program_studi, m.tahun_angkatan')
				->join('nilai_cpmk_mahasiswa ncm', 'ncm.mahasiswa_id = m.id')
				->join('jadwal_mengajar jm', 'jm.id = ncm.jadwal_mengajar_id')
				->where('m.status_mahasiswa', 'Aktif');

			if ($programStudi) {
				$builder->where('m.program_studi', $programStudi);
			}

			if ($tahunAngkatan) {
				$builder->where('m.tahun_angkatan', $tahunAngkatan);
			}

			// Filter by semester and/or tahun akademik in jadwal_mengajar
			if ($semester && $tahunAkademik) {
				// Both filters: tahun_akademik should be "YYYY/YYYY Semester"
				$builder->where('jm.tahun_akademik', $tahunAkademik . ' ' . $semester);
			} elseif ($semester) {
				// Only semester: tahun_akademik should end with " Semester"
				$builder->like('jm.tahun_akademik', $semester, 'after');
			} elseif ($tahunAkademik) {
				// Only tahun akademik: tahun_akademik should start with "YYYY/YYYY"
				$builder->like('jm.tahun_akademik', $tahunAkademik, 'after');
			}

			$mahasiswa = $builder
				->orderBy('m.nama_lengkap', 'ASC')
				->get()
				->getResultArray();
		} else {
			// No semester/tahun_akademik filter, use simple query
			$builder = $this->mahasiswaModel
				->select('id, nim, nama_lengkap, program_studi, tahun_angkatan')
				->where('status_mahasiswa', 'Aktif');

			if ($programStudi) {
				$builder->where('program_studi', $programStudi);
			}

			if ($tahunAngkatan) {
				$builder->where('tahun_angkatan', $tahunAngkatan);
			}

			$mahasiswa = $builder
				->orderBy('nama_lengkap', 'ASC')
				->findAll();
		}

		return $this->response->setJSON($mahasiswa);
	}

	public function getComparativeData()
	{
		$programStudi = $this->request->getGet('program_studi');
		$tahunAngkatan = $this->request->getGet('tahun_angkatan');
		$semester = $this->request->getGet('semester');
		$tahunAkademik = $this->request->getGet('tahun_akademik');

		if (!$programStudi || !$tahunAngkatan) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Program studi dan tahun angkatan harus dipilih'
			]);
		}

		$db = \Config\Database::connect();

		// Get all mahasiswa in this filter
		$mahasiswaList = $this->mahasiswaModel
			->where('program_studi', $programStudi)
			->where('tahun_angkatan', $tahunAngkatan)
			->where('status_mahasiswa', 'Aktif')
			->findAll();

		if (empty($mahasiswaList)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Tidak ada mahasiswa aktif'
			]);
		}

		$mahasiswaIds = array_column($mahasiswaList, 'id');

		// Get all CPL
		$cplList = $db->table('cpl')
			->orderBy('kode_cpl', 'ASC')
			->get()
			->getResultArray();

		$chartData = [
			'labels' => [],
			'data' => [],
			'details' => []
		];

		foreach ($cplList as $cpl) {
			// Get all CPMK linked to this CPL
			$cpmkLinked = $db->table('cpl_cpmk')
				->select('cpmk_id')
				->where('cpl_id', $cpl['id'])
				->get()
				->getResultArray();

			if (empty($cpmkLinked)) {
				$chartData['labels'][] = $cpl['kode_cpl'];
				$chartData['data'][] = 0;
				$chartData['details'][] = [
					'cpl_id' => $cpl['id'],
					'kode_cpl' => $cpl['kode_cpl'],
					'deskripsi' => $cpl['deskripsi'],
					'jenis_cpl' => $this->getJenisCplLabel($cpl['jenis_cpl']),
					'rata_rata' => 0,
					'jumlah_mahasiswa' => 0
				];
				continue;
			}

			$cpmkIds = array_column($cpmkLinked, 'cpmk_id');

			// Get all assessment scores from nilai_teknik_penilaian for all students and these CPMK
			$nilaiBuilder = $db->table('nilai_teknik_penilaian ntp')
				->select('ntp.nilai, ntp.teknik_penilaian_key, ntp.mahasiswa_id, rm.cpmk_id, rm.teknik_penilaian')
				->join('rps_mingguan rm', 'rm.id = ntp.rps_mingguan_id')
				->join('jadwal_mengajar jm', 'jm.id = ntp.jadwal_mengajar_id')
				->whereIn('ntp.mahasiswa_id', $mahasiswaIds)
				->whereIn('rm.cpmk_id', $cpmkIds);

			// Apply semester and tahun akademik filters
			if ($semester && $tahunAkademik) {
				$nilaiBuilder->where('jm.tahun_akademik', $tahunAkademik . ' ' . $semester);
			} elseif ($semester) {
				$nilaiBuilder->like('jm.tahun_akademik', $semester, 'after');
			} elseif ($tahunAkademik) {
				$nilaiBuilder->like('jm.tahun_akademik', $tahunAkademik, 'after');
			}

			$nilaiList = $nilaiBuilder->get()->getResultArray();

			// Calculate CPMK scores for each student using weighted formula
			$studentCpmkScores = [];

			foreach ($nilaiList as $row) {
				$mhsId = $row['mahasiswa_id'];
				$cpmkId = $row['cpmk_id'];

				// Decode the weight (bobot) from JSON
				$teknikData = json_decode($row['teknik_penilaian'], true);
				$bobot = isset($teknikData[$row['teknik_penilaian_key']]) ? floatval($teknikData[$row['teknik_penilaian_key']]) : 0;

				if ($bobot > 0 && $row['nilai'] !== null) {
					if (!isset($studentCpmkScores[$mhsId][$cpmkId])) {
						$studentCpmkScores[$mhsId][$cpmkId] = [
							'total_weighted' => 0,
							'total_bobot' => 0
						];
					}

					$studentCpmkScores[$mhsId][$cpmkId]['total_weighted'] += ($row['nilai'] * $bobot / 100);
					$studentCpmkScores[$mhsId][$cpmkId]['total_bobot'] += $bobot;
				}
			}

			// Calculate CPL average from CPMK percentages for each student
			$totalCplScore = 0;
			$studentCount = 0;

			foreach ($studentCpmkScores as $mhsId => $cpmkScores) {
				$totalCpmkPercentage = 0;
				$cpmkCount = 0;

				foreach ($cpmkScores as $cpmkId => $scoreData) {
					if ($scoreData['total_bobot'] > 0) {
						// Calculate CPMK percentage
						$cpmkPercentage = ($scoreData['total_weighted'] / $scoreData['total_bobot']) * 100;
						$totalCpmkPercentage += $cpmkPercentage;
						$cpmkCount++;
					}
				}

				if ($cpmkCount > 0) {
					// Calculate this student's CPL as average of their CPMK percentages
					$cplScore = $totalCpmkPercentage / $cpmkCount;
					$totalCplScore += $cplScore;
					$studentCount++;
				}
			}

			$average = $studentCount > 0 ? round($totalCplScore / $studentCount, 2) : 0;
			$jumlahMhs = $studentCount;

			$chartData['labels'][] = $cpl['kode_cpl'];
			$chartData['data'][] = $average;
			$chartData['details'][] = [
				'cpl_id' => $cpl['id'],
				'kode_cpl' => $cpl['kode_cpl'],
				'deskripsi' => $cpl['deskripsi'],
				'jenis_cpl' => $this->getJenisCplLabel($cpl['jenis_cpl']),
				'rata_rata' => $average,
				'jumlah_mahasiswa' => $jumlahMhs
			];
		}

		return $this->response->setJSON([
			'success' => true,
			'chartData' => $chartData,
			'programStudi' => $programStudi,
			'tahunAngkatan' => $tahunAngkatan,
			'totalMahasiswa' => count($mahasiswaList)
		]);
	}

	private function getProgramStudi()
	{
		$db = \Config\Database::connect();
		$result = $db->table('mahasiswa')
			->select('program_studi')
			->distinct()
			->where('status_mahasiswa', 'Aktif')
			->orderBy('program_studi', 'ASC')
			->get()
			->getResultArray();

		return array_column($result, 'program_studi');
	}

	private function getTahunAngkatan()
	{
		$db = \Config\Database::connect();
		$result = $db->table('mahasiswa')
			->select('tahun_angkatan')
			->distinct()
			->where('status_mahasiswa', 'Aktif')
			->orderBy('tahun_angkatan', 'DESC')
			->get()
			->getResultArray();

		return array_column($result, 'tahun_angkatan');
	}

	private function getJenisCplLabel($jenis)
	{
		$labels = [
			'P' => 'Pengetahuan',
			'KK' => 'Keterampilan Khusus',
			'S' => 'Sikap',
			'KU' => 'Keterampilan Umum'
		];

		return $labels[$jenis] ?? $jenis;
	}

	private function getSemesterList()
	{
		$db = \Config\Database::connect();
		$builder = $db->table('jadwal_mengajar');
		$result = $builder
			->select('tahun_akademik')
			->distinct()
			->orderBy('tahun_akademik', 'DESC')
			->get()
			->getResultArray();

		// Extract semester part (e.g., "Ganjil" or "Genap" from "2024/2025 Ganjil")
		$semesterList = [];
		foreach ($result as $row) {
			$semesterList[] = $row['tahun_akademik'];	
		}

		return $semesterList;
	}

	private function getTahunAkademikList()
	{
		$db = \Config\Database::connect();
		$builder = $db->table('jadwal_mengajar');
		$result = $builder
			->select('tahun_akademik')
			->distinct()
			->orderBy('tahun_akademik', 'DESC')
			->get()
			->getResultArray();

		// Extract just the year part (e.g., "2024/2025" from "2024/2025 Ganjil")
		$tahunAkademikList = [];
		foreach ($result as $row) {
			$tahunAkademik = $row['tahun_akademik'];
			// Remove " Ganjil" or " Genap" from the end
			$yearOnly = preg_replace('/ (Ganjil|Genap)$/', '', $tahunAkademik);
			if (!in_array($yearOnly, $tahunAkademikList)) {
				$tahunAkademikList[] = $yearOnly;
			}
		}

		return $tahunAkademikList;
	}

	public function getComparativeDetailCalculation()
	{
		$cplId = $this->request->getGet('cpl_id');
		$programStudi = $this->request->getGet('program_studi');
		$tahunAngkatan = $this->request->getGet('tahun_angkatan');
		$semester = $this->request->getGet('semester');
		$tahunAkademik = $this->request->getGet('tahun_akademik');

		if (!$cplId || !$programStudi || !$tahunAngkatan) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Parameter tidak lengkap'
			]);
		}

		$db = \Config\Database::connect();

		// Get CPL info
		$cpl = $this->cplModel->find($cplId);
		if (!$cpl) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'CPL tidak ditemukan'
			]);
		}

		// Get all mahasiswa in this angkatan
		$mahasiswaList = $this->mahasiswaModel
			->select('id, nim, nama_lengkap')
			->where('program_studi', $programStudi)
			->where('tahun_angkatan', $tahunAngkatan)
			->where('status_mahasiswa', 'Aktif')
			->orderBy('nim', 'ASC')
			->findAll();

		if (empty($mahasiswaList)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Tidak ada mahasiswa aktif'
			]);
		}

		$mahasiswaIds = array_column($mahasiswaList, 'id');

		// Get all CPMK linked to this CPL
		$cpmkLinked = $db->table('cpl_cpmk')
			->select('cpmk_id')
			->where('cpl_id', $cplId)
			->get()
			->getResultArray();

		if (empty($cpmkLinked)) {
			return $this->response->setJSON([
				'success' => true,
				'data' => [],
				'summary' => [
					'total_cpl' => 0,
					'jumlah_mahasiswa' => 0,
					'rata_rata' => 0
				],
				'message' => 'Tidak ada CPMK yang terkait dengan CPL ini'
			]);
		}

		$cpmkIds = array_column($cpmkLinked, 'cpmk_id');

		// Get all assessment scores from nilai_teknik_penilaian for all students and these CPMK
		$nilaiBuilder = $db->table('nilai_teknik_penilaian ntp')
			->select('ntp.nilai, ntp.teknik_penilaian_key, ntp.mahasiswa_id, rm.cpmk_id, rm.teknik_penilaian')
			->join('rps_mingguan rm', 'rm.id = ntp.rps_mingguan_id')
			->join('jadwal_mengajar jm', 'jm.id = ntp.jadwal_mengajar_id')
			->whereIn('ntp.mahasiswa_id', $mahasiswaIds)
			->whereIn('rm.cpmk_id', $cpmkIds);

		// Apply semester and tahun akademik filters
		if ($semester && $tahunAkademik) {
			$nilaiBuilder->where('jm.tahun_akademik', $tahunAkademik . ' ' . $semester);
		} elseif ($semester) {
			$nilaiBuilder->like('jm.tahun_akademik', $semester, 'after');
		} elseif ($tahunAkademik) {
			$nilaiBuilder->like('jm.tahun_akademik', $tahunAkademik, 'after');
		}

		$nilaiList = $nilaiBuilder->get()->getResultArray();

		// Calculate CPMK scores for each student
		$studentCpmkScores = [];
		foreach ($mahasiswaList as $mhs) {
			$studentCpmkScores[$mhs['id']] = [
				'mahasiswa_id' => $mhs['id'],
				'nim' => $mhs['nim'],
				'nama_lengkap' => $mhs['nama_lengkap'],
				'cpmk_scores' => []
			];
		}

		foreach ($nilaiList as $row) {
			$mhsId = $row['mahasiswa_id'];
			$cpmkId = $row['cpmk_id'];

			// Decode the weight (bobot) from JSON
			$teknikData = json_decode($row['teknik_penilaian'], true);
			$bobot = isset($teknikData[$row['teknik_penilaian_key']]) ? floatval($teknikData[$row['teknik_penilaian_key']]) : 0;

			if ($bobot > 0 && $row['nilai'] !== null) {
				if (!isset($studentCpmkScores[$mhsId]['cpmk_scores'][$cpmkId])) {
					$studentCpmkScores[$mhsId]['cpmk_scores'][$cpmkId] = [
						'total_weighted' => 0,
						'total_bobot' => 0
					];
				}

				$studentCpmkScores[$mhsId]['cpmk_scores'][$cpmkId]['total_weighted'] += ($row['nilai'] * $bobot / 100);
				$studentCpmkScores[$mhsId]['cpmk_scores'][$cpmkId]['total_bobot'] += $bobot;
			}
		}

		// Calculate CPL for each student as average of their CPMK percentages
		$detailData = [];
		$totalCplScore = 0;
		$studentCount = 0;

		foreach ($studentCpmkScores as $mhsId => $studentData) {
			$totalCpmkPercentage = 0;
			$cpmkCount = 0;

			foreach ($studentData['cpmk_scores'] as $cpmkId => $scoreData) {
				if ($scoreData['total_bobot'] > 0) {
					// Calculate CPMK percentage
					$cpmkPercentage = ($scoreData['total_weighted'] / $scoreData['total_bobot']) * 100;
					$totalCpmkPercentage += $cpmkPercentage;
					$cpmkCount++;
				}
			}

			if ($cpmkCount > 0) {
				// Calculate this student's CPL as average of their CPMK percentages
				$capaianCpl = $totalCpmkPercentage / $cpmkCount;
				$totalCplScore += $capaianCpl;
				$studentCount++;

				$detailData[] = [
					'mahasiswa_id' => $studentData['mahasiswa_id'],
					'nim' => $studentData['nim'],
					'nama_lengkap' => $studentData['nama_lengkap'],
					'nilai_cpl' => round($totalCpmkPercentage, 2),
					'total_bobot' => $cpmkCount,
					'capaian_cpl' => round($capaianCpl, 2)
				];
			}
		}

		$rataRata = $studentCount > 0 ? round($totalCplScore / $studentCount, 2) : 0;

		return $this->response->setJSON([
			'success' => true,
			'data' => $detailData,
			'summary' => [
				'total_cpl' => round($totalCplScore, 2),
				'jumlah_mahasiswa' => $studentCount,
				'rata_rata' => $rataRata
			],
			'cpl' => $cpl
		]);
	}

	public function getDetailCalculation()
	{
		$mahasiswaId = $this->request->getGet('mahasiswa_id');
		$cplId = $this->request->getGet('cpl_id');
		$semester = $this->request->getGet('semester');
		$tahunAkademik = $this->request->getGet('tahun_akademik');

		if (!$mahasiswaId || !$cplId) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Parameter tidak lengkap'
			]);
		}

		$db = \Config\Database::connect();

		// Get CPL info
		$cpl = $this->cplModel->find($cplId);
		if (!$cpl) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'CPL tidak ditemukan'
			]);
		}

		// Get all CPMK linked to this CPL
		$cpmkLinked = $db->table('cpl_cpmk')
			->select('cpmk.id, cpmk.kode_cpmk')
			->join('cpmk', 'cpmk.id = cpl_cpmk.cpmk_id')
			->where('cpl_cpmk.cpl_id', $cplId)
			->orderBy('cpmk.kode_cpmk', 'ASC')
			->get()
			->getResultArray();

		if (empty($cpmkLinked)) {
			return $this->response->setJSON([
				'success' => true,
				'data' => [],
				'summary' => [
					'nilai_cpl' => 0,
					'total_bobot' => 0,
					'capaian_cpl' => 0
				],
				'message' => 'Tidak ada CPMK yang terkait dengan CPL ini'
			]);
		}

		$cpmkIds = array_column($cpmkLinked, 'id');

		// Get all assessment scores from nilai_teknik_penilaian for this student and these CPMK
		$nilaiBuilder = $db->table('nilai_teknik_penilaian ntp')
			->select('ntp.nilai, ntp.teknik_penilaian_key, rm.cpmk_id, rm.teknik_penilaian,
			         cpmk.kode_cpmk, mk.kode_mk, mk.nama_mk, jm.tahun_akademik, jm.kelas')
			->join('rps_mingguan rm', 'rm.id = ntp.rps_mingguan_id')
			->join('jadwal_mengajar jm', 'jm.id = ntp.jadwal_mengajar_id')
			->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id')
			->join('cpmk', 'cpmk.id = rm.cpmk_id')
			->where('ntp.mahasiswa_id', $mahasiswaId)
			->whereIn('rm.cpmk_id', $cpmkIds);

		// Apply semester and tahun akademik filters
		if ($semester && $tahunAkademik) {
			$nilaiBuilder->where('jm.tahun_akademik', $tahunAkademik . ' ' . $semester);
		} elseif ($semester) {
			$nilaiBuilder->like('jm.tahun_akademik', $semester, 'after');
		} elseif ($tahunAkademik) {
			$nilaiBuilder->like('jm.tahun_akademik', $tahunAkademik, 'after');
		}

		$nilaiList = $nilaiBuilder
			->orderBy('cpmk.kode_cpmk', 'ASC')
			->orderBy('jm.tahun_akademik', 'DESC')
			->get()
			->getResultArray();

		// Group by CPMK+MK combination and calculate weighted scores
		$cpmkMkGroups = [];

		foreach ($nilaiList as $row) {
			$key = $row['kode_cpmk'] . '_' . $row['kode_mk'];

			if (!isset($cpmkMkGroups[$key])) {
				$cpmkMkGroups[$key] = [
					'kode_cpmk' => $row['kode_cpmk'],
					'kode_mk' => $row['kode_mk'],
					'nama_mk' => $row['nama_mk'],
					'tahun_akademik' => $row['tahun_akademik'],
					'kelas' => $row['kelas'],
					'cpmk_id' => $row['cpmk_id'],
					'total_weighted' => 0,
					'total_bobot' => 0
				];
			}

			// Decode the weight (bobot) from JSON
			$teknikData = json_decode($row['teknik_penilaian'], true);
			$bobot = isset($teknikData[$row['teknik_penilaian_key']]) ? floatval($teknikData[$row['teknik_penilaian_key']]) : 0;

			if ($bobot > 0 && $row['nilai'] !== null) {
				$cpmkMkGroups[$key]['total_weighted'] += ($row['nilai'] * $bobot / 100);
				$cpmkMkGroups[$key]['total_bobot'] += $bobot;
			}
		}

		// Calculate percentage for each CPMK+MK and prepare detail data
		$detailData = [];
		$totalCpmkPercentage = 0;
		$cpmkCount = 0;

		foreach ($cpmkMkGroups as $data) {
			if ($data['total_bobot'] > 0) {
				// Calculate CPMK percentage for this mata kuliah
				$cpmkPercentage = ($data['total_weighted'] / $data['total_bobot']) * 100;

				$detailData[] = [
					'kode_cpmk' => $data['kode_cpmk'],
					'kode_mk' => $data['kode_mk'],
					'nama_mk' => $data['nama_mk'],
					'tahun_akademik' => $data['tahun_akademik'],
					'kelas' => $data['kelas'],
					'nilai_cpmk' => round($cpmkPercentage, 2),
					'bobot' => $data['total_bobot']
				];

				$totalCpmkPercentage += $cpmkPercentage;
				$cpmkCount++;
			}
		}

		// Calculate average CPL from CPMK percentages
		$capaianCpl = $cpmkCount > 0 ? round($totalCpmkPercentage / $cpmkCount, 2) : 0;

		return $this->response->setJSON([
			'success' => true,
			'data' => $detailData,
			'summary' => [
				'nilai_cpl' => round($totalCpmkPercentage, 2),
				'total_bobot' => $cpmkCount,
				'capaian_cpl' => $capaianCpl
			]
		]);
	}

	// New
	public function getSubjectsList()
	{
		$programStudi = $this->request->getGet('program_studi');
		$tahunAkademik = $this->request->getGet('tahun_akademik');

		$db = \Config\Database::connect();

		$builder = $db->table('jadwal_mengajar jm')
			->select('jm.id as jadwal_id, mk.id as mata_kuliah_id, mk.kode_mk, mk.nama_mk, mk.semester, jm.kelas, jm.tahun_akademik, jm.program_studi')
			->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id')
			->where('jm.status', 'active')
			->orderBy('mk.semester', 'ASC')
			->orderBy('mk.nama_mk', 'ASC');

		if ($programStudi) {
			$builder->where('jm.program_studi', $programStudi);
		}

		if ($tahunAkademik) {
			$builder->where('jm.tahun_akademik', $tahunAkademik);
		}

		$subjects = $builder->get()->getResultArray();

		return $this->response->setJSON($subjects);
	}

	public function getSubjectData()
	{
		$jadwalId = $this->request->getGet('jadwal_id');

		if (!$jadwalId) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Jadwal mengajar harus dipilih'
			]);
		}

		$db = \Config\Database::connect();

		// Get jadwal info
		$jadwal = $db->table('jadwal_mengajar jm')
			->select('jm.*, mk.kode_mk, mk.nama_mk, mk.semester, mk.sks')
			->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id')
			->where('jm.id', $jadwalId)
			->get()
			->getRowArray();

		if (!$jadwal) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Data jadwal tidak ditemukan'
			]);
		}

		// Get all CPL
		$cplList = $db->table('cpl')
			->orderBy('kode_cpl', 'ASC')
			->get()
			->getResultArray();

		if (empty($cplList)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Tidak ada data CPL'
			]);
		}

		// Get all students in this class
		$studentsInClass = $db->table('nilai_cpmk_mahasiswa')
			->select('DISTINCT mahasiswa_id')
			->where('jadwal_mengajar_id', $jadwalId)
			->get()
			->getResultArray();

		$studentIds = array_column($studentsInClass, 'mahasiswa_id');

		if (empty($studentIds)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Belum ada mahasiswa yang memiliki nilai di kelas ini'
			]);
		}

		// Calculate CPL achievement for students in this class
		$chartData = [
			'labels' => [],
			'data' => [],
			'details' => []
		];

		foreach ($cplList as $cpl) {
			// Get all CPMK linked to this CPL
			$cpmkLinked = $db->table('cpl_cpmk')
				->select('cpmk_id')
				->where('cpl_id', $cpl['id'])
				->get()
				->getResultArray();

			if (empty($cpmkLinked)) {
				$chartData['labels'][] = $cpl['kode_cpl'];
				$chartData['data'][] = 0;
				$chartData['details'][] = [
					'cpl_id' => $cpl['id'],
					'kode_cpl' => $cpl['kode_cpl'],
					'deskripsi' => $cpl['deskripsi'],
					'jenis_cpl' => $this->getJenisCplLabel($cpl['jenis_cpl']),
					'rata_rata' => 0,
					'jumlah_mahasiswa' => 0,
					'jumlah_cpmk' => 0
				];
				continue;
			}

			$cpmkIds = array_column($cpmkLinked, 'cpmk_id');

			// Get average nilai_cpmk for students in this class for this CPL
			$result = $db->table('nilai_cpmk_mahasiswa')
				->select('AVG(nilai_cpmk) as rata_rata, COUNT(DISTINCT mahasiswa_id) as jumlah_mahasiswa, COUNT(DISTINCT cpmk_id) as jumlah_cpmk')
				->where('jadwal_mengajar_id', $jadwalId)
				->whereIn('mahasiswa_id', $studentIds)
				->whereIn('cpmk_id', $cpmkIds)
				->get()
				->getRowArray();

			$average = $result['rata_rata'] ? round($result['rata_rata'], 2) : 0;
			$jumlahMhs = $result['jumlah_mahasiswa'] ?? 0;
			$jumlahCpmk = $result['jumlah_cpmk'] ?? 0;

			$chartData['labels'][] = $cpl['kode_cpl'];
			$chartData['data'][] = $average;
			$chartData['details'][] = [
				'cpl_id' => $cpl['id'],
				'kode_cpl' => $cpl['kode_cpl'],
				'deskripsi' => $cpl['deskripsi'],
				'jenis_cpl' => $this->getJenisCplLabel($cpl['jenis_cpl']),
				'rata_rata' => $average,
				'jumlah_mahasiswa' => $jumlahMhs,
				'jumlah_cpmk' => $jumlahCpmk
			];
		}

		return $this->response->setJSON([
			'success' => true,
			'chartData' => $chartData,
			'jadwal' => $jadwal,
			'totalMahasiswa' => count($studentIds)
		]);
	}

	public function getComparativeSubjects()
	{
		$mataKuliahIds = $this->request->getGet('mata_kuliah_ids');
		$programStudi = $this->request->getGet('program_studi');
		$tahunAkademik = $this->request->getGet('tahun_akademik');

		if (!$mataKuliahIds) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Mata kuliah harus dipilih'
			]);
		}

		// Convert comma-separated string to array
		$mkIds = explode(',', $mataKuliahIds);

		$db = \Config\Database::connect();

		// Get jadwal for selected mata kuliah
		$jadwalBuilder = $db->table('jadwal_mengajar')
			->whereIn('mata_kuliah_id', $mkIds)
			->where('status', 'active');

		if ($programStudi) {
			$jadwalBuilder->where('program_studi', $programStudi);
		}

		if ($tahunAkademik) {
			$jadwalBuilder->where('tahun_akademik', $tahunAkademik);
		}

		$jadwalList = $jadwalBuilder->get()->getResultArray();

		if (empty($jadwalList)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Tidak ada jadwal aktif untuk mata kuliah yang dipilih'
			]);
		}

		$jadwalIds = array_column($jadwalList, 'id');

		// Get all CPL
		$cplList = $db->table('cpl')
			->orderBy('kode_cpl', 'ASC')
			->get()
			->getResultArray();

		// Get mata kuliah info
		$mataKuliahList = $db->table('mata_kuliah')
			->whereIn('id', $mkIds)
			->get()
			->getResultArray();

		$chartData = [
			'labels' => [],
			'datasets' => []
		];

		// Prepare datasets for each mata kuliah
		$colors = [
			['rgba(13, 110, 253, 0.8)', 'rgba(13, 110, 253, 1)'],
			['rgba(25, 135, 84, 0.8)', 'rgba(25, 135, 84, 1)'],
			['rgba(220, 53, 69, 0.8)', 'rgba(220, 53, 69, 1)'],
			['rgba(255, 193, 7, 0.8)', 'rgba(255, 193, 7, 1)'],
			['rgba(13, 202, 240, 0.8)', 'rgba(13, 202, 240, 1)'],
		];

		foreach ($mataKuliahList as $index => $mk) {
			$mkJadwalIds = array_column(
				array_filter($jadwalList, function ($j) use ($mk) {
					return $j['mata_kuliah_id'] == $mk['id'];
				}),
				'id'
			);

			if (empty($mkJadwalIds)) continue;

			$dataPoints = [];

			foreach ($cplList as $cpl) {
				// Get all CPMK linked to this CPL
				$cpmkLinked = $db->table('cpl_cpmk')
					->select('cpmk_id')
					->where('cpl_id', $cpl['id'])
					->get()
					->getResultArray();

				if (empty($cpmkLinked)) {
					$dataPoints[] = 0;
					continue;
				}

				$cpmkIds = array_column($cpmkLinked, 'cpmk_id');

				// Get average for this mata kuliah
				$result = $db->table('nilai_cpmk_mahasiswa')
					->select('AVG(nilai_cpmk) as rata_rata')
					->whereIn('jadwal_mengajar_id', $mkJadwalIds)
					->whereIn('cpmk_id', $cpmkIds)
					->get()
					->getRowArray();

				$average = $result['rata_rata'] ? round($result['rata_rata'], 2) : 0;
				$dataPoints[] = $average;
			}

			$colorIndex = $index % count($colors);
			$chartData['datasets'][] = [
				'label' => $mk['kode_mk'] . ' - ' . $mk['nama_mk'],
				'data' => $dataPoints,
				'backgroundColor' => $colors[$colorIndex][0],
				'borderColor' => $colors[$colorIndex][1],
				'borderWidth' => 2,
				'borderRadius' => 5
			];
		}

		// Set labels (CPL codes)
		$chartData['labels'] = array_column($cplList, 'kode_cpl');

		return $this->response->setJSON([
			'success' => true,
			'chartData' => $chartData,
			'mataKuliah' => $mataKuliahList
		]);
	}

	public function getKeseluruhanData()
	{
		$programStudi = $this->request->getGet('program_studi');
		$semester = $this->request->getGet('semester');
		$tahunAkademik = $this->request->getGet('tahun_akademik');

		if (!$programStudi) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Program studi harus dipilih'
			]);
		}

		$db = \Config\Database::connect();

		// Get all active mahasiswa in this program studi (across all angkatan)
		$mahasiswaList = $this->mahasiswaModel
			->where('program_studi', $programStudi)
			->where('status_mahasiswa', 'Aktif')
			->findAll();

		if (empty($mahasiswaList)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Tidak ada mahasiswa aktif'
			]);
		}

		$mahasiswaIds = array_column($mahasiswaList, 'id');

		// Get all CPL
		$cplList = $db->table('cpl')
			->orderBy('kode_cpl', 'ASC')
			->get()
			->getResultArray();

		$chartData = [
			'labels' => [],
			'data' => [],
			'details' => []
		];

		foreach ($cplList as $cpl) {
			// Get all CPMK linked to this CPL
			$cpmkLinked = $db->table('cpl_cpmk')
				->select('cpmk_id')
				->where('cpl_id', $cpl['id'])
				->get()
				->getResultArray();

			if (empty($cpmkLinked)) {
				$chartData['labels'][] = $cpl['kode_cpl'];
				$chartData['data'][] = 0;
				$chartData['details'][] = [
					'cpl_id' => $cpl['id'],
					'kode_cpl' => $cpl['kode_cpl'],
					'deskripsi' => $cpl['deskripsi'],
					'jenis_cpl' => $this->getJenisCplLabel($cpl['jenis_cpl']),
					'rata_rata' => 0,
					'jumlah_mahasiswa' => 0
				];
				continue;
			}

			$cpmkIds = array_column($cpmkLinked, 'cpmk_id');

			// Get all nilai_cpmk for all students for these CPMK
			$nilaiBuilder = $db->table('nilai_cpmk_mahasiswa ncm')
				->select('ncm.nilai_cpmk, ncm.cpmk_id, ncm.mahasiswa_id, jm.mata_kuliah_id')
				->join('jadwal_mengajar jm', 'jm.id = ncm.jadwal_mengajar_id')
				->whereIn('ncm.mahasiswa_id', $mahasiswaIds)
				->whereIn('ncm.cpmk_id', $cpmkIds);

			// Apply semester and tahun akademik filters
			if ($semester && $tahunAkademik) {
				$nilaiBuilder->where('jm.tahun_akademik', $tahunAkademik . ' ' . $semester);
			} elseif ($semester) {
				$nilaiBuilder->like('jm.tahun_akademik', $semester, 'after');
			} elseif ($tahunAkademik) {
				$nilaiBuilder->like('jm.tahun_akademik', $tahunAkademik, 'after');
			}

			$nilaiList = $nilaiBuilder->get()->getResultArray();

			// Calculate CPL for each student using formula:
			// Nilai CPL = Σ(CPMK scores), Capaian CPL (%) = (Nilai CPL / Total Bobot) × 100
			$studentScores = [];

			foreach ($nilaiList as $nilai) {
				// Get bobot from rps_mingguan
				$rps = $db->table('rps')
					->select('id')
					->where('mata_kuliah_id', $nilai['mata_kuliah_id'])
					->orderBy('created_at', 'DESC')
					->get()
					->getRowArray();

				$bobot = 0;
				if ($rps) {
					// Sum bobot across all weeks for this CPMK
					$bobotResult = $db->table('rps_mingguan')
						->selectSum('bobot')
						->where('rps_id', $rps['id'])
						->where('cpmk_id', $nilai['cpmk_id'])
						->get()
						->getRowArray();

					$bobot = $bobotResult['bobot'] ?? 0;
				}

				$mhsId = $nilai['mahasiswa_id'];
				if (!isset($studentScores[$mhsId])) {
					$studentScores[$mhsId] = [
						'nilaiCpl' => 0,
						'totalBobot' => 0
					];
				}

				// Sum CPMK scores and bobot
				if ($bobot > 0) {
					$studentScores[$mhsId]['nilaiCpl'] += $nilai['nilai_cpmk'];
					$studentScores[$mhsId]['totalBobot'] += $bobot;
				}
			}

			// Calculate average Capaian CPL (%) across all students
			$totalCplScore = 0;
			$studentCount = 0;

			foreach ($studentScores as $mhsId => $scores) {
				if ($scores['totalBobot'] > 0) {
					// Capaian CPL (%) = (Nilai CPL / Total Bobot) × 100
					$cplScore = ($scores['nilaiCpl'] / $scores['totalBobot']) * 100;
					$totalCplScore += $cplScore;
					$studentCount++;
				}
			}

			$average = $studentCount > 0 ? round($totalCplScore / $studentCount, 2) : 0;
			$jumlahMhs = $studentCount;

			$chartData['labels'][] = $cpl['kode_cpl'];
			$chartData['data'][] = $average;
			$chartData['details'][] = [
				'cpl_id' => $cpl['id'],
				'kode_cpl' => $cpl['kode_cpl'],
				'deskripsi' => $cpl['deskripsi'],
				'jenis_cpl' => $this->getJenisCplLabel($cpl['jenis_cpl']),
				'rata_rata' => $average,
				'jumlah_mahasiswa' => $jumlahMhs
			];
		}

		// Get unique angkatan
		$angkatanList = array_unique(array_column($mahasiswaList, 'tahun_angkatan'));
		sort($angkatanList);

		return $this->response->setJSON([
			'success' => true,
			'chartData' => $chartData,
			'programStudi' => $programStudi,
			'totalMahasiswa' => count($mahasiswaList),
			'angkatanList' => $angkatanList
		]);
	}

	public function getKeseluruhanDetailCalculation()
	{
		$cplId = $this->request->getGet('cpl_id');
		$programStudi = $this->request->getGet('program_studi');
		$semester = $this->request->getGet('semester');
		$tahunAkademik = $this->request->getGet('tahun_akademik');

		if (!$cplId || !$programStudi) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Parameter tidak lengkap'
			]);
		}

		$db = \Config\Database::connect();

		// Get CPL info
		$cpl = $this->cplModel->find($cplId);
		if (!$cpl) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'CPL tidak ditemukan'
			]);
		}

		// Get all mahasiswa in this program studi (across all angkatan)
		$mahasiswaList = $this->mahasiswaModel
			->select('id, nim, nama_lengkap, tahun_angkatan')
			->where('program_studi', $programStudi)
			->where('status_mahasiswa', 'Aktif')
			->orderBy('tahun_angkatan', 'DESC')
			->orderBy('nim', 'ASC')
			->findAll();

		if (empty($mahasiswaList)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Tidak ada mahasiswa aktif'
			]);
		}

		$mahasiswaIds = array_column($mahasiswaList, 'id');

		// Get all CPMK linked to this CPL
		$cpmkLinked = $db->table('cpl_cpmk')
			->select('cpmk_id')
			->where('cpl_id', $cplId)
			->get()
			->getResultArray();

		if (empty($cpmkLinked)) {
			return $this->response->setJSON([
				'success' => true,
				'data' => [],
				'summary' => [
					'total_cpl' => 0,
					'jumlah_mahasiswa' => 0,
					'rata_rata' => 0
				],
				'message' => 'Tidak ada CPMK yang terkait dengan CPL ini'
			]);
		}

		$cpmkIds = array_column($cpmkLinked, 'cpmk_id');

		// Get all nilai_cpmk for all students for these CPMK
		$nilaiBuilder = $db->table('nilai_cpmk_mahasiswa ncm')
			->select('ncm.nilai_cpmk, ncm.cpmk_id, ncm.mahasiswa_id, jm.mata_kuliah_id')
			->join('jadwal_mengajar jm', 'jm.id = ncm.jadwal_mengajar_id')
			->whereIn('ncm.mahasiswa_id', $mahasiswaIds)
			->whereIn('ncm.cpmk_id', $cpmkIds);

		// Apply semester and tahun akademik filters
		if ($semester && $tahunAkademik) {
			$nilaiBuilder->where('jm.tahun_akademik', $tahunAkademik . ' ' . $semester);
		} elseif ($semester) {
			$nilaiBuilder->like('jm.tahun_akademik', $semester, 'after');
		} elseif ($tahunAkademik) {
			$nilaiBuilder->like('jm.tahun_akademik', $tahunAkademik, 'after');
		}

		$nilaiList = $nilaiBuilder->get()->getResultArray();

		// Calculate CPL for each student
		$studentScores = [];
		$mahasiswaMap = [];
		foreach ($mahasiswaList as $mhs) {
			$mahasiswaMap[$mhs['id']] = $mhs;
			$studentScores[$mhs['id']] = [
				'mahasiswa_id' => $mhs['id'],
				'nim' => $mhs['nim'],
				'nama_lengkap' => $mhs['nama_lengkap'],
				'tahun_angkatan' => $mhs['tahun_angkatan'],
				'nilaiCpl' => 0,
				'totalBobot' => 0,
				'capaianCpl' => 0
			];
		}

		foreach ($nilaiList as $nilai) {
			// Get bobot from rps_mingguan
			$rps = $db->table('rps')
				->select('id')
				->where('mata_kuliah_id', $nilai['mata_kuliah_id'])
				->orderBy('created_at', 'DESC')
				->get()
				->getRowArray();

			$bobot = 0;
			if ($rps) {
				$bobotResult = $db->table('rps_mingguan')
					->selectSum('bobot')
					->where('rps_id', $rps['id'])
					->where('cpmk_id', $nilai['cpmk_id'])
					->get()
					->getRowArray();

				$bobot = $bobotResult['bobot'] ?? 0;
			}

			$mhsId = $nilai['mahasiswa_id'];
			if (isset($studentScores[$mhsId]) && $bobot > 0) {
				$studentScores[$mhsId]['nilaiCpl'] += $nilai['nilai_cpmk'];
				$studentScores[$mhsId]['totalBobot'] += $bobot;
			}
		}

		// Calculate capaian CPL for each student
		$detailData = [];
		$totalCplScore = 0;
		$studentCount = 0;

		foreach ($studentScores as $mhsId => $scores) {
			if ($scores['totalBobot'] > 0) {
				$capaianCpl = ($scores['nilaiCpl'] / $scores['totalBobot']) * 100;
				$studentScores[$mhsId]['capaianCpl'] = $capaianCpl;
				$totalCplScore += $capaianCpl;
				$studentCount++;

				$detailData[] = [
					'mahasiswa_id' => $scores['mahasiswa_id'],
					'nim' => $scores['nim'],
					'nama_lengkap' => $scores['nama_lengkap'],
					'tahun_angkatan' => $scores['tahun_angkatan'],
					'nilai_cpl' => round($scores['nilaiCpl'], 2),
					'total_bobot' => round($scores['totalBobot'], 2),
					'capaian_cpl' => round($capaianCpl, 2)
				];
			}
		}

		$rataRata = $studentCount > 0 ? round($totalCplScore / $studentCount, 2) : 0;

		return $this->response->setJSON([
			'success' => true,
			'data' => $detailData,
			'summary' => [
				'total_cpl' => round($totalCplScore, 2),
				'jumlah_mahasiswa' => $studentCount,
				'rata_rata' => $rataRata
			],
			'cpl' => $cpl
		]);
	}

	public function getAllSubjectsData()
	{
		$programStudi = $this->request->getGet('program_studi');

		if (!$programStudi) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Program studi harus dipilih'
			]);
		}

		$db = \Config\Database::connect();

		// Get all active jadwal for the selected program studi (across all years)
		$jadwalList = $db->table('jadwal_mengajar jm')
			->select('jm.*, mk.kode_mk, mk.nama_mk, mk.semester')
			->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id')
			->where('jm.program_studi', $programStudi)
			->where('jm.status', 'active')
			->orderBy('mk.semester', 'ASC')
			->orderBy('mk.nama_mk', 'ASC')
			->orderBy('jm.tahun_akademik', 'DESC')
			->get()
			->getResultArray();

		if (empty($jadwalList)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Tidak ada jadwal aktif untuk program studi yang dipilih'
			]);
		}

		// Get all CPL
		$cplList = $db->table('cpl')
			->orderBy('kode_cpl', 'ASC')
			->get()
			->getResultArray();

		if (empty($cplList)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Tidak ada data CPL'
			]);
		}

		$chartData = [
			'labels' => [],
			'datasets' => []
		];

		// Color palette for different subjects
		$colors = [
			['rgba(13, 110, 253, 0.8)', 'rgba(13, 110, 253, 1)'],
			['rgba(25, 135, 84, 0.8)', 'rgba(25, 135, 84, 1)'],
			['rgba(220, 53, 69, 0.8)', 'rgba(220, 53, 69, 1)'],
			['rgba(255, 193, 7, 0.8)', 'rgba(255, 193, 7, 1)'],
			['rgba(13, 202, 240, 0.8)', 'rgba(13, 202, 240, 1)'],
			['rgba(108, 117, 125, 0.8)', 'rgba(108, 117, 125, 1)'],
			['rgba(111, 66, 193, 0.8)', 'rgba(111, 66, 193, 1)'],
			['rgba(214, 51, 132, 0.8)', 'rgba(214, 51, 132, 1)'],
			['rgba(253, 126, 20, 0.8)', 'rgba(253, 126, 20, 1)'],
			['rgba(32, 201, 151, 0.8)', 'rgba(32, 201, 151, 1)'],
		];

		// Prepare datasets for each mata kuliah
		foreach ($jadwalList as $index => $jadwal) {
			$dataPoints = [];

			foreach ($cplList as $cpl) {
				// Get all CPMK linked to this CPL
				$cpmkLinked = $db->table('cpl_cpmk')
					->select('cpmk_id')
					->where('cpl_id', $cpl['id'])
					->get()
					->getResultArray();

				if (empty($cpmkLinked)) {
					$dataPoints[] = 0;
					continue;
				}

				$cpmkIds = array_column($cpmkLinked, 'cpmk_id');

				// Get all nilai_cpmk for this jadwal for these CPMK
				$nilaiList = $db->table('nilai_cpmk_mahasiswa ncm')
					->select('ncm.nilai_cpmk, ncm.cpmk_id, ncm.mahasiswa_id')
					->where('ncm.jadwal_mengajar_id', $jadwal['id'])
					->whereIn('ncm.cpmk_id', $cpmkIds)
					->get()
					->getResultArray();

				// Calculate CPL for each student using formula:
				// Nilai CPL = Σ(CPMK scores), Capaian CPL (%) = (Nilai CPL / Total Bobot) × 100
				$studentScores = [];

				foreach ($nilaiList as $nilai) {
					// Get bobot from rps_mingguan
					$rps = $db->table('rps')
						->select('id')
						->where('mata_kuliah_id', $jadwal['mata_kuliah_id'])
						->orderBy('created_at', 'DESC')
						->get()
						->getRowArray();

					$bobot = 0;
					if ($rps) {
						// Sum bobot across all weeks for this CPMK
						$bobotResult = $db->table('rps_mingguan')
							->selectSum('bobot')
							->where('rps_id', $rps['id'])
							->where('cpmk_id', $nilai['cpmk_id'])
							->get()
							->getRowArray();

						$bobot = $bobotResult['bobot'] ?? 0;
					}

					$mhsId = $nilai['mahasiswa_id'];
					if (!isset($studentScores[$mhsId])) {
						$studentScores[$mhsId] = [
							'nilaiCpl' => 0,
							'totalBobot' => 0
						];
					}

					// Sum CPMK scores and bobot
					if ($bobot > 0) {
						$studentScores[$mhsId]['nilaiCpl'] += $nilai['nilai_cpmk'];
						$studentScores[$mhsId]['totalBobot'] += $bobot;
					}
				}

				// Calculate average Capaian CPL (%) across all students
				$totalCplScore = 0;
				$studentCount = 0;

				foreach ($studentScores as $mhsId => $scores) {
					if ($scores['totalBobot'] > 0) {
						// Capaian CPL (%) = (Nilai CPL / Total Bobot) × 100
						$cplScore = ($scores['nilaiCpl'] / $scores['totalBobot']) * 100;
						$totalCplScore += $cplScore;
						$studentCount++;
					}
				}

				$average = $studentCount > 0 ? round($totalCplScore / $studentCount, 2) : 0;
				$dataPoints[] = $average;
			}

			$colorIndex = $index % count($colors);
			$chartData['datasets'][] = [
				'label' => $jadwal['kode_mk'] . ' - ' . $jadwal['nama_mk'] . ' (Kelas ' . $jadwal['kelas'] . ', ' . $jadwal['tahun_akademik'] . ')',
				'data' => $dataPoints,
				'backgroundColor' => $colors[$colorIndex][0],
				'borderColor' => $colors[$colorIndex][1],
				'borderWidth' => 2,
				'borderRadius' => 5
			];
		}

		// Set labels (CPL codes)
		$chartData['labels'] = array_column($cplList, 'kode_cpl');

		// Create summary table data
		$summaryData = [];
		foreach ($jadwalList as $jadwal) {
			$totalCpl = 0;
			$countCpl = 0;

			foreach ($cplList as $cpl) {
				$cpmkLinked = $db->table('cpl_cpmk')
					->select('cpmk_id')
					->where('cpl_id', $cpl['id'])
					->get()
					->getResultArray();

				if (!empty($cpmkLinked)) {
					$cpmkIds = array_column($cpmkLinked, 'cpmk_id');

					// Get all nilai_cpmk for this jadwal for these CPMK
					$nilaiList = $db->table('nilai_cpmk_mahasiswa ncm')
						->select('ncm.nilai_cpmk, ncm.cpmk_id, ncm.mahasiswa_id')
						->where('ncm.jadwal_mengajar_id', $jadwal['id'])
						->whereIn('ncm.cpmk_id', $cpmkIds)
						->get()
						->getResultArray();

					// Calculate CPL for each student using formula:
					// Nilai CPL = Σ(CPMK scores), Capaian CPL (%) = (Nilai CPL / Total Bobot) × 100
					$studentScores = [];

					foreach ($nilaiList as $nilai) {
						// Get bobot from rps_mingguan
						$rps = $db->table('rps')
							->select('id')
							->where('mata_kuliah_id', $jadwal['mata_kuliah_id'])
							->orderBy('created_at', 'DESC')
							->get()
							->getRowArray();

						$bobot = 0;
						if ($rps) {
							// Sum bobot across all weeks for this CPMK
							$bobotResult = $db->table('rps_mingguan')
								->selectSum('bobot')
								->where('rps_id', $rps['id'])
								->where('cpmk_id', $nilai['cpmk_id'])
								->get()
								->getRowArray();

							$bobot = $bobotResult['bobot'] ?? 0;
						}

						$mhsId = $nilai['mahasiswa_id'];
						if (!isset($studentScores[$mhsId])) {
							$studentScores[$mhsId] = [
								'nilaiCpl' => 0,
								'totalBobot' => 0
							];
						}

						// Sum CPMK scores and bobot
						if ($bobot > 0) {
							$studentScores[$mhsId]['nilaiCpl'] += $nilai['nilai_cpmk'];
							$studentScores[$mhsId]['totalBobot'] += $bobot;
						}
					}

					// Calculate average Capaian CPL (%) across all students
					$totalCplScore = 0;
					$studentCount = 0;

					foreach ($studentScores as $mhsId => $scores) {
						if ($scores['totalBobot'] > 0) {
							// Capaian CPL (%) = (Nilai CPL / Total Bobot) × 100
							$cplScore = ($scores['nilaiCpl'] / $scores['totalBobot']) * 100;
							$totalCplScore += $cplScore;
							$studentCount++;
						}
					}

					$average = $studentCount > 0 ? ($totalCplScore / $studentCount) : 0;

					if ($average > 0) {
						$totalCpl += $average;
						$countCpl++;
					}
				}
			}

			// Get student count
			$studentCount = $db->table('nilai_cpmk_mahasiswa')
				->select('COUNT(DISTINCT mahasiswa_id) as total')
				->where('jadwal_mengajar_id', $jadwal['id'])
				->get()
				->getRowArray();

			$summaryData[] = [
				'kode_mk' => $jadwal['kode_mk'],
				'nama_mk' => $jadwal['nama_mk'],
				'kelas' => $jadwal['kelas'],
				'semester' => $jadwal['semester'],
				'tahun_akademik' => $jadwal['tahun_akademik'],
				'rata_rata_keseluruhan' => $countCpl > 0 ? round($totalCpl / $countCpl, 2) : 0,
				'jumlah_mahasiswa' => $studentCount['total'] ?? 0
			];
		}

		return $this->response->setJSON([
			'success' => true,
			'chartData' => $chartData,
			'summaryData' => $summaryData,
			'programStudi' => $programStudi,
			'totalMataKuliah' => count($jadwalList)
		]);
	}
}

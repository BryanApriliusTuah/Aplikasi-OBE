<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CpmkModel;
use App\Models\NilaiCpmkMahasiswaModel;
use App\Models\MataKuliahModel;
use App\Models\MengajarModel;

class CapaianCpmk extends BaseController
{
	protected $cpmkModel;
	protected $nilaiCpmkModel;
	protected $jadwalMengajarModel;
	protected $mataKuliahModel;

	public function __construct()
	{
		$this->cpmkModel = new CpmkModel();
		$this->nilaiCpmkModel = new NilaiCpmkMahasiswaModel();
		$this->jadwalMengajarModel = new MengajarModel();
		$this->mataKuliahModel = new MataKuliahModel();
	}

	public function index()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return redirect()->to('/')->with('error', 'Akses ditolak.');
		}

		// Get CPMK passing threshold
		$standarCpmkModel = new \App\Models\StandarMinimalCpmkModel();
		$passingThreshold = $standarCpmkModel->getPersentase();

		$data = [
			'title' => 'Capaian CPMK',
			'mataKuliah' => $this->mataKuliahModel->findAll(),
			'tahunAkademik' => $this->getTahunAkademik(),
			'programStudi' => $this->getProgramStudi(),
			'tahunAngkatan' => $this->getTahunAngkatan(),
			'semesterList' => $this->getSemesterList(),
			'tahunAkademikList' => $this->getTahunAkademikList(),
			'passing_threshold' => $passingThreshold
		];

		return view('admin/capaian_cpmk/index', $data);
	}

	public function getChartData()
	{
		$mataKuliahId = $this->request->getGet('mata_kuliah_id');
		$tahunAkademik = $this->request->getGet('tahun_akademik');
		$kelas = $this->request->getGet('kelas');

		if (!$mataKuliahId) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Mata kuliah harus dipilih'
			]);
		}

		// Get jadwal mengajar based on filters
		$jadwalBuilder = $this->jadwalMengajarModel
			->where('mata_kuliah_id', $mataKuliahId);

		if ($tahunAkademik) {
			$jadwalBuilder->like('tahun_akademik', $tahunAkademik, 'after');
		}

		if ($kelas) {
			$jadwalBuilder->where('kelas', $kelas);
		}

		$jadwal = $jadwalBuilder->first();

		if (!$jadwal) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Jadwal mengajar tidak ditemukan'
			]);
		}

		// Get all CPMK for this mata kuliah
		$db = \Config\Database::connect();
		$builder = $db->table('cpmk_mk');
		$cpmkList = $builder
			->select('cpmk.id, cpmk.kode_cpmk, cpmk.deskripsi')
			->join('cpmk', 'cpmk.id = cpmk_mk.cpmk_id')
			->where('cpmk_mk.mata_kuliah_id', $mataKuliahId)
			->orderBy('cpmk.kode_cpmk', 'ASC')
			->get()
			->getResultArray();

		if (empty($cpmkList)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Tidak ada CPMK untuk mata kuliah ini'
			]);
		}

		// Calculate average percentage for each CPMK
		// Formula: Capaian CPMK (%) = (nilai_cpmk / bobot) × 100, then average across students
		$chartData = [
			'labels' => [],
			'data' => [],
			'details' => []
		];

		foreach ($cpmkList as $cpmk) {
			// Get bobot for this CPMK in this jadwal
			$bobot = $this->getCpmkBobotForJadwal($cpmk['id'], $jadwal['id']);

			$nilaiBuilder = $db->table('nilai_cpmk_mahasiswa');
			$nilaiRows = $nilaiBuilder
				->select('nilai_cpmk')
				->where('cpmk_id', $cpmk['id'])
				->where('jadwal_id', $jadwal['id'])
				->get()
				->getResultArray();

			$jumlahMhs = count($nilaiRows);
			$totalCapaian = 0;

			if ($bobot > 0 && $jumlahMhs > 0) {
				foreach ($nilaiRows as $nilaiRow) {
					$totalCapaian += (floatval($nilaiRow['nilai_cpmk']) / $bobot) * 100;
				}
			}

			$average = $jumlahMhs > 0 ? round($totalCapaian / $jumlahMhs, 2) : 0;

			$chartData['labels'][] = $cpmk['kode_cpmk'];
			$chartData['data'][] = $average;
			$chartData['details'][] = [
				'cpmk_id' => $cpmk['id'],
				'kode_cpmk' => $cpmk['kode_cpmk'],
				'deskripsi' => $cpmk['deskripsi'],
				'rata_rata' => $average,
				'jumlah_mahasiswa' => $jumlahMhs
			];
		}

		// Get mata kuliah info
		$mataKuliah = $this->mataKuliahModel->find($mataKuliahId);

		return $this->response->setJSON([
			'success' => true,
			'chartData' => $chartData,
			'mataKuliah' => $mataKuliah,
			'jadwal' => $jadwal
		]);
	}

	public function getDetailData()
	{
		$mataKuliahId = $this->request->getGet('mata_kuliah_id');
		$tahunAkademik = $this->request->getGet('tahun_akademik');
		$kelas = $this->request->getGet('kelas');
		$cpmkId = $this->request->getGet('cpmk_id');

		if (!$mataKuliahId || !$cpmkId) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Parameter tidak lengkap'
			]);
		}

		// Get jadwal mengajar
		$jadwalBuilder = $this->jadwalMengajarModel
			->where('mata_kuliah_id', $mataKuliahId);

		if ($tahunAkademik) {
			$jadwalBuilder->like('tahun_akademik', $tahunAkademik, 'after');
		}

		if ($kelas) {
			$jadwalBuilder->where('kelas', $kelas);
		}

		$jadwal = $jadwalBuilder->first();

		if (!$jadwal) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Jadwal tidak ditemukan'
			]);
		}

		// Get detail nilai mahasiswa
		$db = \Config\Database::connect();
		$builder = $db->table('nilai_cpmk_mahasiswa');
		$nilaiDetail = $builder
			->select('mahasiswa.nim, mahasiswa.nama_lengkap, nilai_cpmk_mahasiswa.nilai_cpmk')
			->join('mahasiswa', 'mahasiswa.id = nilai_cpmk_mahasiswa.mahasiswa_id')
			->where('nilai_cpmk_mahasiswa.cpmk_id', $cpmkId)
			->where('nilai_cpmk_mahasiswa.jadwal_id', $jadwal['id'])
			->orderBy('mahasiswa.nama_lengkap', 'ASC')
			->get()
			->getResultArray();

		// Get bobot for percentage calculation
		$bobot = $this->getCpmkBobotForJadwal($cpmkId, $jadwal['id']);

		// Add capaian percentage to each student's record
		foreach ($nilaiDetail as &$detail) {
			$detail['capaian'] = ($bobot > 0)
				? round((floatval($detail['nilai_cpmk']) / $bobot) * 100, 2)
				: 0;
		}
		unset($detail);

		// Get CPMK info
		$cpmk = $this->cpmkModel->find($cpmkId);

		return $this->response->setJSON([
			'success' => true,
			'data' => $nilaiDetail,
			'cpmk' => $cpmk,
			'bobot' => $bobot
		]);
	}

	/**
	 * Get total bobot (weight) for a specific CPMK in a specific jadwal
	 * Used to calculate CPMK percentage: (nilai_cpmk / bobot) × 100
	 *
	 * @param int $cpmk_id CPMK ID
	 * @param int $jadwal_id Jadwal ID
	 * @return float Total bobot from rps_mingguan
	 */
	private function getCpmkBobotForJadwal($cpmk_id, $jadwal_id)
	{
		$db = \Config\Database::connect();

		// Get mata_kuliah_id from jadwal
		$jadwal = $db->table('jadwal')
			->select('mata_kuliah_id')
			->where('id', $jadwal_id)
			->get()
			->getRowArray();

		if (!$jadwal) {
			return 0;
		}

		// Get latest RPS for this mata kuliah
		$rps = $db->table('rps')
			->select('id')
			->where('mata_kuliah_id', $jadwal['mata_kuliah_id'])
			->orderBy('created_at', 'DESC')
			->get()
			->getRowArray();

		if (!$rps) {
			return 0;
		}

		// Get sum of bobot from rps_mingguan for this CPMK
		$result = $db->table('rps_mingguan')
			->selectSum('bobot')
			->where('rps_id', $rps['id'])
			->where('cpmk_id', $cpmk_id)
			->get()
			->getRowArray();

		return floatval($result['bobot'] ?? 0);
	}

	private function getTahunAkademik()
	{
		$db = \Config\Database::connect();
		$builder = $db->table('jadwal');
		$result = $builder
			->select('tahun_akademik')
			->distinct()
			->orderBy('tahun_akademik', 'DESC')
			->get()
			->getResultArray();

		return array_column($result, 'tahun_akademik');
	}

	private function getProgramStudi()
	{
		$db = \Config\Database::connect();
		$builder = $db->table('program_studi');
		$result = $builder
			->select('kode, nama_resmi')
			->distinct()
			->orderBy('nama_resmi', 'ASC')
			->get()
			->getResultArray();

		return array_column($result, 'nama_resmi', 'kode');
	}

	private function getTahunAngkatan()
	{
		$db = \Config\Database::connect();
		$builder = $db->table('mahasiswa');
		$result = $builder
			->select('tahun_angkatan')
			->distinct()
			->orderBy('tahun_angkatan', 'DESC')
			->get()
			->getResultArray();

		return array_column($result, 'tahun_angkatan');
	}

	private function getSemesterList()
	{
		$db = \Config\Database::connect();
		$builder = $db->table('jadwal');
		$result = $builder
			->select('tahun_akademik')
			->distinct()
			->orderBy('tahun_akademik', 'DESC')
			->get()
			->getResultArray();

		// tahun_akademik already contains the semester (e.g., "2024/2025 Ganjil")
		return array_column($result, 'tahun_akademik');
	}

	private function getTahunAkademikList()
	{
		$db = \Config\Database::connect();
		$builder = $db->table('jadwal');
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

	public function getKelasByMataKuliah()
	{
		$mataKuliahId = $this->request->getGet('mata_kuliah_id');
		$tahunAkademik = $this->request->getGet('tahun_akademik');

		if (!$mataKuliahId) {
			return $this->response->setJSON([]);
		}

		$db = \Config\Database::connect();
		$builder = $db->table('jadwal');
		$builder->select('kelas')
			->where('mata_kuliah_id', $mataKuliahId);

		if ($tahunAkademik) {
			$builder->like('tahun_akademik', $tahunAkademik, 'after');
		}

		$result = $builder
			->distinct()
			->orderBy('kelas', 'ASC')
			->get()
			->getResultArray();

		return $this->response->setJSON(array_column($result, 'kelas'));
	}

	public function getComparativeSubjects()
	{
		$mataKuliahIds = $this->request->getGet('mata_kuliah_ids');
		$tahunAkademik = $this->request->getGet('tahun_akademik');
		$kelas = $this->request->getGet('kelas');

		if (!$mataKuliahIds) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Mata kuliah harus dipilih'
			]);
		}

		$mataKuliahArray = explode(',', $mataKuliahIds);

		$db = \Config\Database::connect();
		$datasets = [];
		$allCpmkCodes = [];

		// Define colors for different subjects
		$colors = [
			['rgba(54, 162, 235, 0.8)', 'rgba(54, 162, 235, 1)'],
			['rgba(255, 99, 132, 0.8)', 'rgba(255, 99, 132, 1)'],
			['rgba(75, 192, 192, 0.8)', 'rgba(75, 192, 192, 1)'],
			['rgba(255, 159, 64, 0.8)', 'rgba(255, 159, 64, 1)'],
			['rgba(153, 102, 255, 0.8)', 'rgba(153, 102, 255, 1)']
		];

		foreach ($mataKuliahArray as $index => $mkId) {
			$mataKuliah = $this->mataKuliahModel->find($mkId);

			if (!$mataKuliah) continue;

			// Get jadwal for this mata kuliah
			$jadwalBuilder = $this->jadwalMengajarModel
				->where('mata_kuliah_id', $mkId);

			if ($tahunAkademik) {
				$jadwalBuilder->like('tahun_akademik', $tahunAkademik, 'after');
			}

			if ($kelas) {
				$jadwalBuilder->where('kelas', $kelas);
			}

			$jadwal = $jadwalBuilder->first();

			if (!$jadwal) continue;

			// Get CPMK list for this mata kuliah
			$cpmkList = $db->table('cpmk_mk')
				->select('cpmk.id, cpmk.kode_cpmk, cpmk.deskripsi')
				->join('cpmk', 'cpmk.id = cpmk_mk.cpmk_id')
				->where('cpmk_mk.mata_kuliah_id', $mkId)
				->orderBy('cpmk.kode_cpmk', 'ASC')
				->get()
				->getResultArray();

			$data = [];
			foreach ($cpmkList as $cpmk) {
				$allCpmkCodes[] = $cpmk['kode_cpmk'];

				$avgBuilder = $db->table('nilai_cpmk_mahasiswa');
				$result = $avgBuilder
					->select('AVG(nilai_cpmk) as rata_rata')
					->where('cpmk_id', $cpmk['id'])
					->where('jadwal_id', $jadwal['id'])
					->get()
					->getRowArray();

				$data[] = $result['rata_rata'] ? round($result['rata_rata'], 2) : 0;
			}

			$colorIndex = $index % count($colors);
			$datasets[] = [
				'label' => $mataKuliah['kode_mk'] . ' - ' . $mataKuliah['nama_mk'],
				'data' => $data,
				'backgroundColor' => $colors[$colorIndex][0],
				'borderColor' => $colors[$colorIndex][1],
				'borderWidth' => 2,
				'borderRadius' => 5
			];
		}

		// Get unique CPMK codes
		$uniqueCpmkCodes = array_unique($allCpmkCodes);
		sort($uniqueCpmkCodes);

		return $this->response->setJSON([
			'success' => true,
			'chartData' => [
				'labels' => $uniqueCpmkCodes,
				'datasets' => $datasets
			]
		]);
	}

	public function getAllSubjectsData()
	{
		$tahunAkademik = $this->request->getGet('tahun_akademik');

		$db = \Config\Database::connect();

		// Get all jadwal that have CPMK data
		$jadwalBuilder = $db->table('jadwal jm')
			->select('jm.id as jadwal_id, jm.mata_kuliah_id, jm.tahun_akademik, jm.kelas, 
                  mk.kode_mk, mk.nama_mk, mk.semester')
			->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id')
			->join('nilai_cpmk_mahasiswa ncm', 'ncm.jadwal_id = jm.id')
			->groupBy('jm.id');

		if ($tahunAkademik) {
			$jadwalBuilder->where('jm.tahun_akademik', $tahunAkademik);
		}

		$jadwalList = $jadwalBuilder
			->orderBy('mk.semester', 'ASC')
			->orderBy('mk.kode_mk', 'ASC')
			->get()
			->getResultArray();

		if (empty($jadwalList)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Tidak ada data CPMK untuk kriteria yang dipilih'
			]);
		}

		$datasets = [];
		$allCpmkCodes = [];
		$summaryData = [];

		// Define colors
		$colors = [
			['rgba(54, 162, 235, 0.8)', 'rgba(54, 162, 235, 1)'],
			['rgba(255, 99, 132, 0.8)', 'rgba(255, 99, 132, 1)'],
			['rgba(75, 192, 192, 0.8)', 'rgba(75, 192, 192, 1)'],
			['rgba(255, 159, 64, 0.8)', 'rgba(255, 159, 64, 1)'],
			['rgba(153, 102, 255, 0.8)', 'rgba(153, 102, 255, 1)'],
			['rgba(255, 205, 86, 0.8)', 'rgba(255, 205, 86, 1)'],
			['rgba(201, 203, 207, 0.8)', 'rgba(201, 203, 207, 1)']
		];

		foreach ($jadwalList as $index => $jadwal) {
			// Get CPMK list for this jadwal
			$cpmkList = $db->table('cpmk_mk')
				->select('cpmk.id, cpmk.kode_cpmk, cpmk.deskripsi')
				->join('cpmk', 'cpmk.id = cpmk_mk.cpmk_id')
				->where('cpmk_mk.mata_kuliah_id', $jadwal['mata_kuliah_id'])
				->orderBy('cpmk.kode_cpmk', 'ASC')
				->get()
				->getResultArray();

			$data = [];
			$totalNilai = 0;
			$countNilai = 0;
			$totalMahasiswa = 0;

			foreach ($cpmkList as $cpmk) {
				$allCpmkCodes[] = $cpmk['kode_cpmk'];

				$avgBuilder = $db->table('nilai_cpmk_mahasiswa');
				$result = $avgBuilder
					->select('AVG(nilai_cpmk) as rata_rata, COUNT(DISTINCT mahasiswa_id) as jumlah')
					->where('cpmk_id', $cpmk['id'])
					->where('jadwal_id', $jadwal['jadwal_id'])
					->get()
					->getRowArray();

				$average = $result['rata_rata'] ? round($result['rata_rata'], 2) : 0;
				$data[] = $average;

				if ($average > 0) {
					$totalNilai += $average;
					$countNilai++;
				}

				$totalMahasiswa = max($totalMahasiswa, $result['jumlah'] ?? 0);
			}

			$colorIndex = $index % count($colors);
			$label = $jadwal['kode_mk'] . ' (' . $jadwal['kelas'] . ')';

			$datasets[] = [
				'label' => $label,
				'data' => $data,
				'backgroundColor' => $colors[$colorIndex][0],
				'borderColor' => $colors[$colorIndex][1],
				'borderWidth' => 2,
				'borderRadius' => 5
			];

			// Add to summary data
			$summaryData[] = [
				'kode_mk' => $jadwal['kode_mk'],
				'nama_mk' => $jadwal['nama_mk'],
				'kelas' => $jadwal['kelas'],
				'semester' => $jadwal['semester'],
				'tahun_akademik' => $jadwal['tahun_akademik'],
				'jumlah_cpmk' => count($cpmkList),
				'jumlah_mahasiswa' => $totalMahasiswa,
				'rata_rata' => $countNilai > 0 ? round($totalNilai / $countNilai, 2) : 0
			];
		}

		// Get unique CPMK codes
		$uniqueCpmkCodes = array_unique($allCpmkCodes);
		sort($uniqueCpmkCodes);

		return $this->response->setJSON([
			'success' => true,
			'chartData' => [
				'labels' => $uniqueCpmkCodes,
				'datasets' => $datasets
			],
			'summaryData' => $summaryData
		]);
	}

	public function mahasiswa()
	{
		$programStudi = $this->request->getGet('program_studi');
		$tahunAngkatan = $this->request->getGet('tahun_angkatan');

		$db = \Config\Database::connect();
		$builder = $db->table('mahasiswa');
		$builder->select('id, nim, nama_lengkap, program_studi_kode, tahun_angkatan')
			->where('status_mahasiswa', 'Aktif');

		if ($programStudi) {
			$builder->where('program_studi_kode', $programStudi);
		}

		if ($tahunAngkatan) {
			$builder->where('tahun_angkatan', $tahunAngkatan);
		}

		$result = $builder
			->orderBy('nama_lengkap', 'ASC')
			->get()
			->getResultArray();

		return $this->response->setJSON($result);
	}

	public function chartDataIndividual()
	{
		$mahasiswaId = $this->request->getGet('mahasiswa_id');
		$semester = $this->request->getGet('semester');
		$tahunAkademik = $this->request->getGet('tahun_akademik');

		if (!$mahasiswaId) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Mahasiswa harus dipilih'
			]);
		}

		$db = \Config\Database::connect();

		// Get mahasiswa info
		$mahasiswa = $db->table('mahasiswa')->where('id', $mahasiswaId)->get()->getRowArray();

		if (!$mahasiswa) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Mahasiswa tidak ditemukan'
			]);
		}

		// Get all CPMK scores from nilai_cpmk_mahasiswa (combines regular and MBKM)
		// Formula: Capaian CPMK (%) = (Σ(nilai_cpmk) / Σ(bobot)) × 100
		$builder = $db->table('nilai_cpmk_mahasiswa ncm')
			->select('ncm.cpmk_id, ncm.jadwal_id, ncm.nilai_cpmk,
			         c.kode_cpmk, c.deskripsi,
			         mk.kode_mk, mk.nama_mk,
			         jm.tahun_akademik, jm.kelas')
			->join('jadwal jm', 'jm.id = ncm.jadwal_id')
			->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id')
			->join('cpmk c', 'c.id = ncm.cpmk_id')
			->where('ncm.mahasiswa_id', $mahasiswaId);

		// Apply semester filter if provided
		if ($semester) {
			$builder->where('jm.tahun_akademik', $semester);
		}

		// Apply tahun akademik filter if provided
		if ($tahunAkademik) {
			$builder->like('jm.tahun_akademik', $tahunAkademik, 'both');
		}

		$nilaiData = $builder
			->orderBy('c.kode_cpmk', 'ASC')
			->get()
			->getResultArray();

		if (empty($nilaiData)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Belum ada data CPMK untuk mahasiswa ini'
			]);
		}

		// Group by CPMK and calculate percentage
		$cpmkGroups = [];
		$courseDetails = [];

		foreach ($nilaiData as $row) {
			$kodeCpmk = $row['kode_cpmk'];
			$cpmkId = $row['cpmk_id'];
			$jadwalId = $row['jadwal_id'];
			$nilaiCpmk = floatval($row['nilai_cpmk']);

			// Get bobot for this CPMK in this jadwal using helper function
			$bobot = $this->getCpmkBobotForJadwal($cpmkId, $jadwalId);

			if ($bobot > 0) {
				// Group by CPMK code for chart (combines all jadwal/courses for same CPMK)
				if (!isset($cpmkGroups[$kodeCpmk])) {
					$cpmkGroups[$kodeCpmk] = [
						'cpmk_id' => $cpmkId,
						'kode_cpmk' => $kodeCpmk,
						'deskripsi' => $row['deskripsi'],
						'total_nilai_cpmk' => 0,
						'total_bobot' => 0
					];
				}

				$cpmkGroups[$kodeCpmk]['total_nilai_cpmk'] += $nilaiCpmk;
				$cpmkGroups[$kodeCpmk]['total_bobot'] += $bobot;

				// Store course details for breakdown table
				$key = $kodeCpmk . '_' . $row['kode_mk'] . '_' . $jadwalId;
				if (!isset($courseDetails[$key])) {
					$courseDetails[$key] = [
						'cpmk_id' => $cpmkId,
						'kode_cpmk' => $kodeCpmk,
						'deskripsi' => $row['deskripsi'],
						'kode_mk' => $row['kode_mk'],
						'nama_mk' => $row['nama_mk'],
						'tahun_akademik' => $row['tahun_akademik'],
						'kelas' => $row['kelas'],
						'nilai_cpmk' => $nilaiCpmk,
						'bobot' => $bobot
					];
				}
			}
		}

		$chartData = [
			'labels' => [],
			'data' => [],
			'details' => []
		];

		// Calculate final percentage for each CPMK
		foreach ($cpmkGroups as $kodeCpmk => $group) {
			$capaianPersen = $group['total_bobot'] > 0
				? round(($group['total_nilai_cpmk'] / $group['total_bobot']) * 100, 2)
				: 0;

			$chartData['labels'][] = $kodeCpmk;
			$chartData['data'][] = $capaianPersen;
		}

		// Add course-level details
		foreach ($courseDetails as $detail) {
			$nilai = $detail['bobot'] > 0
				? round(($detail['nilai_cpmk'] / $detail['bobot']) * 100, 2)
				: 0;

			$chartData['details'][] = [
				'cpmk_id' => $detail['cpmk_id'],
				'kode_cpmk' => $detail['kode_cpmk'],
				'deskripsi' => $detail['deskripsi'],
				'kode_mk' => $detail['kode_mk'],
				'nama_mk' => $detail['nama_mk'],
				'tahun_akademik' => $detail['tahun_akademik'],
				'kelas' => $detail['kelas'],
				'nilai' => $nilai
			];
		}

		return $this->response->setJSON([
			'success' => true,
			'mahasiswa' => $mahasiswa,
			'chartData' => $chartData
		]);
	}

	public function comparativeData()
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

		// Get students in this cohort
		$mahasiswaList = $db->table('mahasiswa')
			->select('id')
			->where('program_studi_kode', $programStudi)
			->where('tahun_angkatan', $tahunAngkatan)
			->where('status_mahasiswa', 'Aktif')
			->get()
			->getResultArray();

		if (empty($mahasiswaList)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Tidak ada mahasiswa untuk angkatan ini'
			]);
		}

		$mahasiswaIds = array_column($mahasiswaList, 'id');

		// Get all CPMK scores from nilai_cpmk_mahasiswa (combines regular and MBKM)
		// Formula: Capaian CPMK (%) = (Σ(nilai_cpmk) / Σ(bobot)) × 100
		$builder = $db->table('nilai_cpmk_mahasiswa ncm')
			->select('ncm.cpmk_id, ncm.jadwal_id, ncm.nilai_cpmk, ncm.mahasiswa_id,
			         c.kode_cpmk, c.deskripsi,
			         mk.kode_mk, mk.nama_mk')
			->join('jadwal jm', 'jm.id = ncm.jadwal_id')
			->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id')
			->join('cpmk c', 'c.id = ncm.cpmk_id')
			->whereIn('ncm.mahasiswa_id', $mahasiswaIds);

		// Apply semester filter if provided
		if ($semester) {
			$builder->where('jm.tahun_akademik', $semester);
		}

		// Apply tahun akademik filter if provided
		if ($tahunAkademik) {
			$builder->like('jm.tahun_akademik', $tahunAkademik, 'both');
		}

		$nilaiData = $builder
			->orderBy('c.kode_cpmk', 'ASC')
			->get()
			->getResultArray();

		if (empty($nilaiData)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Belum ada data CPMK untuk angkatan ini'
			]);
		}

		// Cache bobot lookups to avoid redundant queries
		$bobotCache = [];

		// Calculate scores for each student per CPMK+MK and per CPMK only
		$studentCpmkMkScores = []; // Per CPMK+MK (for detail table)
		$studentCpmkOnlyScores = []; // Per CPMK across all courses (for chart)

		foreach ($nilaiData as $row) {
			$mahasiswaId = $row['mahasiswa_id'];
			$kodeCpmk = $row['kode_cpmk'];
			$cpmkId = $row['cpmk_id'];
			$jadwalId = $row['jadwal_id'];
			$nilaiCpmk = floatval($row['nilai_cpmk']);
			$keyMk = $kodeCpmk . '_' . $row['kode_mk'];

			// Get bobot with caching
			$bobotKey = $cpmkId . '_' . $jadwalId;
			if (!isset($bobotCache[$bobotKey])) {
				$bobotCache[$bobotKey] = $this->getCpmkBobotForJadwal($cpmkId, $jadwalId);
			}
			$bobot = $bobotCache[$bobotKey];

			if ($bobot > 0) {
				// Aggregate by CPMK+MK for detail table
				if (!isset($studentCpmkMkScores[$mahasiswaId][$keyMk])) {
					$studentCpmkMkScores[$mahasiswaId][$keyMk] = [
						'cpmk_id' => $cpmkId,
						'kode_cpmk' => $kodeCpmk,
						'deskripsi' => $row['deskripsi'],
						'kode_mk' => $row['kode_mk'],
						'nama_mk' => $row['nama_mk'],
						'total_nilai_cpmk' => 0,
						'total_bobot' => 0
					];
				}
				$studentCpmkMkScores[$mahasiswaId][$keyMk]['total_nilai_cpmk'] += $nilaiCpmk;
				$studentCpmkMkScores[$mahasiswaId][$keyMk]['total_bobot'] += $bobot;

				// Aggregate by CPMK only (across all courses) for chart
				if (!isset($studentCpmkOnlyScores[$mahasiswaId][$kodeCpmk])) {
					$studentCpmkOnlyScores[$mahasiswaId][$kodeCpmk] = [
						'cpmk_id' => $cpmkId,
						'kode_cpmk' => $kodeCpmk,
						'deskripsi' => $row['deskripsi'],
						'total_nilai_cpmk' => 0,
						'total_bobot' => 0
					];
				}
				$studentCpmkOnlyScores[$mahasiswaId][$kodeCpmk]['total_nilai_cpmk'] += $nilaiCpmk;
				$studentCpmkOnlyScores[$mahasiswaId][$kodeCpmk]['total_bobot'] += $bobot;
			}
		}

		// Calculate average CPMK value for chart (across all courses)
		$cpmkOnlyGroups = [];
		foreach ($studentCpmkOnlyScores as $mahasiswaId => $cpmkData) {
			foreach ($cpmkData as $kodeCpmk => $data) {
				if (!isset($cpmkOnlyGroups[$kodeCpmk])) {
					$cpmkOnlyGroups[$kodeCpmk] = [
						'cpmk_id' => $data['cpmk_id'],
						'kode_cpmk' => $data['kode_cpmk'],
						'deskripsi' => $data['deskripsi'],
						'total_capaian' => 0,
						'mahasiswa_count' => 0
					];
				}

				// Calculate this student's CPMK capaian (across all courses)
				$capaian = $data['total_bobot'] > 0
					? ($data['total_nilai_cpmk'] / $data['total_bobot']) * 100
					: 0;

				$cpmkOnlyGroups[$kodeCpmk]['total_capaian'] += $capaian;
				$cpmkOnlyGroups[$kodeCpmk]['mahasiswa_count']++;
			}
		}

		// Calculate average CPMK value per MK for detail table
		$cpmkMkGroups = [];
		foreach ($studentCpmkMkScores as $mahasiswaId => $cpmkData) {
			foreach ($cpmkData as $key => $data) {
				if (!isset($cpmkMkGroups[$key])) {
					$cpmkMkGroups[$key] = [
						'cpmk_id' => $data['cpmk_id'],
						'kode_cpmk' => $data['kode_cpmk'],
						'deskripsi' => $data['deskripsi'],
						'kode_mk' => $data['kode_mk'],
						'nama_mk' => $data['nama_mk'],
						'total_capaian' => 0,
						'mahasiswa_count' => 0
					];
				}

				$capaian = $data['total_bobot'] > 0
					? ($data['total_nilai_cpmk'] / $data['total_bobot']) * 100
					: 0;

				$cpmkMkGroups[$key]['total_capaian'] += $capaian;
				$cpmkMkGroups[$key]['mahasiswa_count']++;
			}
		}

		$chartData = [
			'labels' => [],
			'data' => [],
			'details' => []
		];

		// Build chart data (CPMK average across all courses)
		foreach ($cpmkOnlyGroups as $group) {
			$rataRata = $group['mahasiswa_count'] > 0
				? round($group['total_capaian'] / $group['mahasiswa_count'], 2)
				: 0;

			$chartData['labels'][] = $group['kode_cpmk'];
			$chartData['data'][] = $rataRata;
		}

		// Build detail table data (same as chart - one row per CPMK)
		foreach ($cpmkOnlyGroups as $group) {
			$rataRata = $group['mahasiswa_count'] > 0
				? round($group['total_capaian'] / $group['mahasiswa_count'], 2)
				: 0;

			$chartData['details'][] = [
				'cpmk_id' => $group['cpmk_id'],
				'kode_cpmk' => $group['kode_cpmk'],
				'deskripsi' => $group['deskripsi'],
				'rata_rata' => $rataRata,
				'jumlah_mahasiswa' => $group['mahasiswa_count']
			];
		}

		// Build breakdown by mata kuliah (for detail popup)
		$chartData['breakdown_by_mk'] = [];
		foreach ($cpmkMkGroups as $group) {
			$rataRata = $group['mahasiswa_count'] > 0
				? round($group['total_capaian'] / $group['mahasiswa_count'], 2)
				: 0;

			$chartData['breakdown_by_mk'][] = [
				'cpmk_id' => $group['cpmk_id'],
				'kode_cpmk' => $group['kode_cpmk'],
				'deskripsi' => $group['deskripsi'],
				'kode_mk' => $group['kode_mk'],
				'nama_mk' => $group['nama_mk'],
				'rata_rata' => $rataRata,
				'jumlah_mahasiswa' => $group['mahasiswa_count']
			];
		}

		return $this->response->setJSON([
			'success' => true,
			'programStudi' => $programStudi,
			'tahunAngkatan' => $tahunAngkatan,
			'totalMahasiswa' => count($mahasiswaIds),
			'chartData' => $chartData
		]);
	}

	public function keseluruhanData()
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

		// Get all active students in this program
		$mahasiswaList = $db->table('mahasiswa')
			->select('id, tahun_angkatan')
			->where('program_studi_kode', $programStudi)
			->where('status_mahasiswa', 'Aktif')
			->get()
			->getResultArray();

		if (empty($mahasiswaList)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Tidak ada mahasiswa untuk program studi ini'
			]);
		}

		$mahasiswaIds = array_column($mahasiswaList, 'id');
		$angkatanList = array_unique(array_column($mahasiswaList, 'tahun_angkatan'));
		sort($angkatanList);

		// Get all CPMK scores from nilai_cpmk_mahasiswa (combines regular and MBKM)
		// Formula: Capaian CPMK (%) = (Σ(nilai_cpmk) / Σ(bobot)) × 100
		$builder = $db->table('nilai_cpmk_mahasiswa ncm')
			->select('ncm.cpmk_id, ncm.jadwal_id, ncm.nilai_cpmk, ncm.mahasiswa_id,
			         c.kode_cpmk, c.deskripsi,
			         mk.kode_mk, mk.nama_mk')
			->join('jadwal jm', 'jm.id = ncm.jadwal_id')
			->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id')
			->join('cpmk c', 'c.id = ncm.cpmk_id')
			->whereIn('ncm.mahasiswa_id', $mahasiswaIds);

		// Apply semester filter if provided
		if ($semester) {
			$builder->where('jm.tahun_akademik', $semester);
		}

		// Apply tahun akademik filter if provided
		if ($tahunAkademik) {
			$builder->like('jm.tahun_akademik', $tahunAkademik, 'both');
		}

		$nilaiData = $builder
			->orderBy('c.kode_cpmk', 'ASC')
			->get()
			->getResultArray();

		if (empty($nilaiData)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Belum ada data CPMK untuk program studi ini'
			]);
		}

		// Cache bobot lookups to avoid redundant queries
		$bobotCache = [];

		// Calculate scores for each student per CPMK+MK and per CPMK only
		$studentCpmkMkScores = []; // Per CPMK+MK (for detail table)
		$studentCpmkOnlyScores = []; // Per CPMK across all courses (for chart)

		foreach ($nilaiData as $row) {
			$mahasiswaId = $row['mahasiswa_id'];
			$kodeCpmk = $row['kode_cpmk'];
			$cpmkId = $row['cpmk_id'];
			$jadwalId = $row['jadwal_id'];
			$nilaiCpmk = floatval($row['nilai_cpmk']);
			$keyMk = $kodeCpmk . '_' . $row['kode_mk'];

			// Get bobot with caching
			$bobotKey = $cpmkId . '_' . $jadwalId;
			if (!isset($bobotCache[$bobotKey])) {
				$bobotCache[$bobotKey] = $this->getCpmkBobotForJadwal($cpmkId, $jadwalId);
			}
			$bobot = $bobotCache[$bobotKey];

			if ($bobot > 0) {
				// Aggregate by CPMK+MK for detail table
				if (!isset($studentCpmkMkScores[$mahasiswaId][$keyMk])) {
					$studentCpmkMkScores[$mahasiswaId][$keyMk] = [
						'cpmk_id' => $cpmkId,
						'kode_cpmk' => $kodeCpmk,
						'deskripsi' => $row['deskripsi'],
						'kode_mk' => $row['kode_mk'],
						'nama_mk' => $row['nama_mk'],
						'total_nilai_cpmk' => 0,
						'total_bobot' => 0
					];
				}
				$studentCpmkMkScores[$mahasiswaId][$keyMk]['total_nilai_cpmk'] += $nilaiCpmk;
				$studentCpmkMkScores[$mahasiswaId][$keyMk]['total_bobot'] += $bobot;

				// Aggregate by CPMK only (across all courses) for chart
				if (!isset($studentCpmkOnlyScores[$mahasiswaId][$kodeCpmk])) {
					$studentCpmkOnlyScores[$mahasiswaId][$kodeCpmk] = [
						'cpmk_id' => $cpmkId,
						'kode_cpmk' => $kodeCpmk,
						'deskripsi' => $row['deskripsi'],
						'total_nilai_cpmk' => 0,
						'total_bobot' => 0
					];
				}
				$studentCpmkOnlyScores[$mahasiswaId][$kodeCpmk]['total_nilai_cpmk'] += $nilaiCpmk;
				$studentCpmkOnlyScores[$mahasiswaId][$kodeCpmk]['total_bobot'] += $bobot;
			}
		}

		// Calculate average CPMK value for chart (across all courses)
		$cpmkOnlyGroups = [];
		foreach ($studentCpmkOnlyScores as $mahasiswaId => $cpmkData) {
			foreach ($cpmkData as $kodeCpmk => $data) {
				if (!isset($cpmkOnlyGroups[$kodeCpmk])) {
					$cpmkOnlyGroups[$kodeCpmk] = [
						'cpmk_id' => $data['cpmk_id'],
						'kode_cpmk' => $data['kode_cpmk'],
						'deskripsi' => $data['deskripsi'],
						'total_capaian' => 0,
						'mahasiswa_count' => 0
					];
				}

				// Calculate this student's CPMK capaian (across all courses)
				$capaian = $data['total_bobot'] > 0
					? ($data['total_nilai_cpmk'] / $data['total_bobot']) * 100
					: 0;

				$cpmkOnlyGroups[$kodeCpmk]['total_capaian'] += $capaian;
				$cpmkOnlyGroups[$kodeCpmk]['mahasiswa_count']++;
			}
		}

		// Calculate average CPMK value per MK for detail table
		$cpmkMkGroups = [];
		foreach ($studentCpmkMkScores as $mahasiswaId => $cpmkData) {
			foreach ($cpmkData as $key => $data) {
				if (!isset($cpmkMkGroups[$key])) {
					$cpmkMkGroups[$key] = [
						'cpmk_id' => $data['cpmk_id'],
						'kode_cpmk' => $data['kode_cpmk'],
						'deskripsi' => $data['deskripsi'],
						'kode_mk' => $data['kode_mk'],
						'nama_mk' => $data['nama_mk'],
						'total_capaian' => 0,
						'mahasiswa_count' => 0
					];
				}

				$capaian = $data['total_bobot'] > 0
					? ($data['total_nilai_cpmk'] / $data['total_bobot']) * 100
					: 0;

				$cpmkMkGroups[$key]['total_capaian'] += $capaian;
				$cpmkMkGroups[$key]['mahasiswa_count']++;
			}
		}

		$chartData = [
			'labels' => [],
			'data' => [],
			'details' => []
		];

		// Build chart data (CPMK average across all courses)
		foreach ($cpmkOnlyGroups as $group) {
			$rataRata = $group['mahasiswa_count'] > 0
				? round($group['total_capaian'] / $group['mahasiswa_count'], 2)
				: 0;

			$chartData['labels'][] = $group['kode_cpmk'];
			$chartData['data'][] = $rataRata;
		}

		// Build detail table data (same as chart - one row per CPMK)
		foreach ($cpmkOnlyGroups as $group) {
			$rataRata = $group['mahasiswa_count'] > 0
				? round($group['total_capaian'] / $group['mahasiswa_count'], 2)
				: 0;

			$chartData['details'][] = [
				'cpmk_id' => $group['cpmk_id'],
				'kode_cpmk' => $group['kode_cpmk'],
				'deskripsi' => $group['deskripsi'],
				'rata_rata' => $rataRata,
				'jumlah_mahasiswa' => $group['mahasiswa_count']
			];
		}

		// Build breakdown by mata kuliah (for detail popup)
		$chartData['breakdown_by_mk'] = [];
		foreach ($cpmkMkGroups as $group) {
			$rataRata = $group['mahasiswa_count'] > 0
				? round($group['total_capaian'] / $group['mahasiswa_count'], 2)
				: 0;

			$chartData['breakdown_by_mk'][] = [
				'cpmk_id' => $group['cpmk_id'],
				'kode_cpmk' => $group['kode_cpmk'],
				'deskripsi' => $group['deskripsi'],
				'kode_mk' => $group['kode_mk'],
				'nama_mk' => $group['nama_mk'],
				'rata_rata' => $rataRata,
				'jumlah_mahasiswa' => $group['mahasiswa_count']
			];
		}

		return $this->response->setJSON([
			'success' => true,
			'programStudi' => $programStudi,
			'totalMahasiswa' => count($mahasiswaIds),
			'angkatanList' => $angkatanList,
			'chartData' => $chartData
		]);
	}

	public function comparativeDetailCalculation()
	{
		$cpmkId = $this->request->getGet('cpmk_id');
		$programStudi = $this->request->getGet('program_studi');
		$tahunAngkatan = $this->request->getGet('tahun_angkatan');
		$semester = $this->request->getGet('semester');
		$tahunAkademik = $this->request->getGet('tahun_akademik');

		if (!$cpmkId || !$programStudi || !$tahunAngkatan) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Parameter tidak lengkap'
			]);
		}

		$db = \Config\Database::connect();

		// Get students in this cohort
		$mahasiswaList = $db->table('mahasiswa')
			->select('id, nim, nama_lengkap')
			->where('program_studi_kode', $programStudi)
			->where('tahun_angkatan', $tahunAngkatan)
			->where('status_mahasiswa', 'Aktif')
			->get()
			->getResultArray();

		$mahasiswaIds = array_column($mahasiswaList, 'id');

		// Get CPMK scores from nilai_cpmk_mahasiswa (combines regular and MBKM)
		$builder = $db->table('nilai_cpmk_mahasiswa ncm')
			->select('ncm.mahasiswa_id, ncm.jadwal_id, ncm.nilai_cpmk')
			->join('jadwal jm', 'jm.id = ncm.jadwal_id')
			->where('ncm.cpmk_id', $cpmkId)
			->whereIn('ncm.mahasiswa_id', $mahasiswaIds);

		// Apply semester filter if provided
		if ($semester) {
			$builder->where('jm.tahun_akademik', $semester);
		}

		// Apply tahun akademik filter if provided
		if ($tahunAkademik) {
			$builder->like('jm.tahun_akademik', $tahunAkademik, 'both');
		}

		$nilaiData = $builder->get()->getResultArray();

		// Cache bobot lookups
		$bobotCache = [];

		// Calculate scores for each student
		$studentScores = [];
		foreach ($nilaiData as $row) {
			$mahasiswaId = $row['mahasiswa_id'];
			$jadwalId = $row['jadwal_id'];

			// Get bobot with caching
			$bobotKey = $cpmkId . '_' . $jadwalId;
			if (!isset($bobotCache[$bobotKey])) {
				$bobotCache[$bobotKey] = $this->getCpmkBobotForJadwal($cpmkId, $jadwalId);
			}
			$bobot = $bobotCache[$bobotKey];

			if ($bobot > 0) {
				if (!isset($studentScores[$mahasiswaId])) {
					$studentScores[$mahasiswaId] = [
						'total_nilai_cpmk' => 0,
						'total_bobot' => 0
					];
				}

				$studentScores[$mahasiswaId]['total_nilai_cpmk'] += floatval($row['nilai_cpmk']);
				$studentScores[$mahasiswaId]['total_bobot'] += $bobot;
			}
		}

		// Merge with student info and calculate final scores
		$data = [];
		$totalCapaian = 0;
		foreach ($mahasiswaList as $mhs) {
			if (isset($studentScores[$mhs['id']])) {
				$score = $studentScores[$mhs['id']];
				$capaian = $score['total_bobot'] > 0
					? round(($score['total_nilai_cpmk'] / $score['total_bobot']) * 100, 2)
					: 0;

				$data[] = [
					'nim' => $mhs['nim'],
					'nama_lengkap' => $mhs['nama_lengkap'],
					'nilai_cpmk' => $capaian
				];
				$totalCapaian += $capaian;
			}
		}

		$summary = [
			'jumlah_mahasiswa' => count($data),
			'total_nilai' => round($totalCapaian, 2),
			'rata_rata' => count($data) > 0 ? round($totalCapaian / count($data), 2) : 0
		];

		return $this->response->setJSON([
			'success' => true,
			'data' => $data,
			'summary' => $summary
		]);
	}

	public function keseluruhanDetailCalculation()
	{
		$cpmkId = $this->request->getGet('cpmk_id');
		$programStudi = $this->request->getGet('program_studi');
		$semester = $this->request->getGet('semester');
		$tahunAkademik = $this->request->getGet('tahun_akademik');

		if (!$cpmkId || !$programStudi) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Parameter tidak lengkap'
			]);
		}

		$db = \Config\Database::connect();

		// Get all active students in this program
		$mahasiswaList = $db->table('mahasiswa')
			->select('id, nim, nama_lengkap, tahun_angkatan')
			->where('program_studi_kode', $programStudi)
			->where('status_mahasiswa', 'Aktif')
			->get()
			->getResultArray();

		$mahasiswaIds = array_column($mahasiswaList, 'id');

		// Get CPMK scores from nilai_cpmk_mahasiswa (combines regular and MBKM)
		$builder = $db->table('nilai_cpmk_mahasiswa ncm')
			->select('ncm.mahasiswa_id, ncm.jadwal_id, ncm.nilai_cpmk')
			->join('jadwal jm', 'jm.id = ncm.jadwal_id')
			->where('ncm.cpmk_id', $cpmkId)
			->whereIn('ncm.mahasiswa_id', $mahasiswaIds);

		// Apply semester filter if provided
		if ($semester) {
			$builder->where('jm.tahun_akademik', $semester);
		}

		// Apply tahun akademik filter if provided
		if ($tahunAkademik) {
			$builder->like('jm.tahun_akademik', $tahunAkademik, 'both');
		}

		$nilaiData = $builder->get()->getResultArray();

		// Cache bobot lookups
		$bobotCache = [];

		// Calculate scores for each student
		$studentScores = [];
		foreach ($nilaiData as $row) {
			$mahasiswaId = $row['mahasiswa_id'];
			$jadwalId = $row['jadwal_id'];

			// Get bobot with caching
			$bobotKey = $cpmkId . '_' . $jadwalId;
			if (!isset($bobotCache[$bobotKey])) {
				$bobotCache[$bobotKey] = $this->getCpmkBobotForJadwal($cpmkId, $jadwalId);
			}
			$bobot = $bobotCache[$bobotKey];

			if ($bobot > 0) {
				if (!isset($studentScores[$mahasiswaId])) {
					$studentScores[$mahasiswaId] = [
						'total_nilai_cpmk' => 0,
						'total_bobot' => 0
					];
				}

				$studentScores[$mahasiswaId]['total_nilai_cpmk'] += floatval($row['nilai_cpmk']);
				$studentScores[$mahasiswaId]['total_bobot'] += $bobot;
			}
		}

		// Merge with student info and calculate final scores
		$data = [];
		$totalCapaian = 0;
		foreach ($mahasiswaList as $mhs) {
			if (isset($studentScores[$mhs['id']])) {
				$score = $studentScores[$mhs['id']];
				$capaian = $score['total_bobot'] > 0
					? round(($score['total_nilai_cpmk'] / $score['total_bobot']) * 100, 2)
					: 0;

				$data[] = [
					'nim' => $mhs['nim'],
					'nama_lengkap' => $mhs['nama_lengkap'],
					'tahun_angkatan' => $mhs['tahun_angkatan'],
					'nilai_cpmk' => $capaian
				];
				$totalCapaian += $capaian;
			}
		}

		$summary = [
			'jumlah_mahasiswa' => count($data),
			'total_nilai' => round($totalCapaian, 2),
			'rata_rata' => count($data) > 0 ? round($totalCapaian / count($data), 2) : 0
		];

		return $this->response->setJSON([
			'success' => true,
			'data' => $data,
			'summary' => $summary
		]);
	}

	public function individualCpmkDetailCalculation()
	{
		$mahasiswaId = $this->request->getGet('mahasiswa_id');
		$kodeCpmk = $this->request->getGet('kode_cpmk');
		$semester = $this->request->getGet('semester');
		$tahunAkademik = $this->request->getGet('tahun_akademik');

		if (!$mahasiswaId || !$kodeCpmk) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Parameter tidak lengkap'
			]);
		}

		$db = \Config\Database::connect();

		// Get CPMK ID
		$cpmk = $db->table('cpmk')
			->where('kode_cpmk', $kodeCpmk)
			->get()
			->getRowArray();

		if (!$cpmk) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'CPMK tidak ditemukan'
			]);
		}

		// Get CPMK scores from nilai_cpmk_mahasiswa (combines regular and MBKM)
		$builder = $db->table('nilai_cpmk_mahasiswa ncm')
			->select('ncm.nilai_cpmk, ncm.jadwal_id,
			         mk.kode_mk, mk.nama_mk,
			         jm.tahun_akademik, jm.kelas')
			->join('jadwal jm', 'jm.id = ncm.jadwal_id')
			->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id')
			->where('ncm.mahasiswa_id', $mahasiswaId)
			->where('ncm.cpmk_id', $cpmk['id']);

		// Apply semester filter if provided
		if ($semester) {
			$builder->where('jm.tahun_akademik', $semester);
		}

		// Apply tahun akademik filter if provided
		if ($tahunAkademik) {
			$builder->like('jm.tahun_akademik', $tahunAkademik, 'both');
		}

		$nilaiData = $builder
			->orderBy('mk.kode_mk', 'ASC')
			->get()
			->getResultArray();

		// Group by course
		$courseGroups = [];
		$grandTotalNilaiCpmk = 0;
		$grandTotalBobot = 0;

		foreach ($nilaiData as $row) {
			$courseKey = $row['kode_mk'] . '_' . $row['jadwal_id'];
			$nilaiCpmk = floatval($row['nilai_cpmk']);
			$bobot = $this->getCpmkBobotForJadwal($cpmk['id'], $row['jadwal_id']);

			if ($bobot > 0) {
				$capaianPersen = round(($nilaiCpmk / $bobot) * 100, 2);

				$courseGroups[$courseKey] = [
					'kode_mk' => $row['kode_mk'],
					'nama_mk' => $row['nama_mk'],
					'tahun_akademik' => $row['tahun_akademik'],
					'kelas' => $row['kelas'],
					'nilai_cpmk' => $nilaiCpmk,
					'bobot' => $bobot,
					'capaian' => $capaianPersen
				];

				$grandTotalNilaiCpmk += $nilaiCpmk;
				$grandTotalBobot += $bobot;
			}
		}

		// Calculate final capaian
		$capaian = $grandTotalBobot > 0 ? round(($grandTotalNilaiCpmk / $grandTotalBobot) * 100, 2) : 0;

		return $this->response->setJSON([
			'success' => true,
			'kode_cpmk' => $kodeCpmk,
			'deskripsi_cpmk' => $cpmk['deskripsi'],
			'courses' => array_values($courseGroups),
			'summary' => [
				'grand_total_nilai_cpmk' => round($grandTotalNilaiCpmk, 2),
				'grand_total_bobot' => $grandTotalBobot,
				'capaian' => $capaian
			]
		]);
	}
}

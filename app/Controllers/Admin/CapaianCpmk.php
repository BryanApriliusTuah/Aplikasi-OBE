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

		// Get dynamic passing threshold from grade configuration
		$gradeConfigModel = new \App\Models\GradeConfigModel();
		$passingThreshold = $gradeConfigModel->getPassingThreshold();

		$data = [
			'title' => 'Capaian CPMK',
			'mataKuliah' => $this->mataKuliahModel->findAll(),
			'tahunAkademik' => $this->getTahunAkademik(),
			'programStudi' => $this->getProgramStudi(),
			'tahunAngkatan' => $this->getTahunAngkatan(),
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
			$jadwalBuilder->where('tahun_akademik', $tahunAkademik);
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

		// Calculate average for each CPMK
		$chartData = [
			'labels' => [],
			'data' => [],
			'details' => []
		];

		foreach ($cpmkList as $cpmk) {
			$avgBuilder = $db->table('nilai_cpmk_mahasiswa');
			$result = $avgBuilder
				->select('AVG(nilai_cpmk) as rata_rata, COUNT(*) as jumlah_mahasiswa')
				->where('cpmk_id', $cpmk['id'])
				->where('jadwal_mengajar_id', $jadwal['id'])
				->get()
				->getRowArray();

			$average = $result['rata_rata'] ? round($result['rata_rata'], 2) : 0;
			$jumlahMhs = $result['jumlah_mahasiswa'] ?? 0;

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
			$jadwalBuilder->where('tahun_akademik', $tahunAkademik);
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
			->where('nilai_cpmk_mahasiswa.jadwal_mengajar_id', $jadwal['id'])
			->orderBy('mahasiswa.nama_lengkap', 'ASC')
			->get()
			->getResultArray();

		// Get CPMK info
		$cpmk = $this->cpmkModel->find($cpmkId);

		return $this->response->setJSON([
			'success' => true,
			'data' => $nilaiDetail,
			'cpmk' => $cpmk
		]);
	}

	private function getTahunAkademik()
	{
		$db = \Config\Database::connect();
		$builder = $db->table('jadwal_mengajar');
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
		$builder = $db->table('mahasiswa');
		$result = $builder
			->select('program_studi')
			->distinct()
			->orderBy('program_studi', 'ASC')
			->get()
			->getResultArray();

		return array_column($result, 'program_studi');
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

	public function getKelasByMataKuliah()
	{
		$mataKuliahId = $this->request->getGet('mata_kuliah_id');
		$tahunAkademik = $this->request->getGet('tahun_akademik');

		if (!$mataKuliahId) {
			return $this->response->setJSON([]);
		}

		$db = \Config\Database::connect();
		$builder = $db->table('jadwal_mengajar');
		$builder->select('kelas')
			->where('mata_kuliah_id', $mataKuliahId);

		if ($tahunAkademik) {
			$builder->where('tahun_akademik', $tahunAkademik);
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
				$jadwalBuilder->where('tahun_akademik', $tahunAkademik);
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
					->where('jadwal_mengajar_id', $jadwal['id'])
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
		$jadwalBuilder = $db->table('jadwal_mengajar jm')
			->select('jm.id as jadwal_id, jm.mata_kuliah_id, jm.tahun_akademik, jm.kelas, 
                  mk.kode_mk, mk.nama_mk, mk.semester')
			->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id')
			->join('nilai_cpmk_mahasiswa ncm', 'ncm.jadwal_mengajar_id = jm.id')
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
					->where('jadwal_mengajar_id', $jadwal['jadwal_id'])
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
		$builder->select('id, nim, nama_lengkap, program_studi, tahun_angkatan')
			->where('status_mahasiswa', 'Aktif');

		if ($programStudi) {
			$builder->where('program_studi', $programStudi);
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

		// Get all assessment scores with weights for this student
		// Using new formula: Capaian CPMK (%) = (Σ(nilai × bobot) / Σ(bobot))
		$nilaiData = $db->table('nilai_teknik_penilaian ntp')
			->select('ntp.nilai, ntp.teknik_penilaian_key,
			         rm.cpmk_id, rm.teknik_penilaian, rm.id as rps_mingguan_id,
			         cpmk.kode_cpmk, cpmk.deskripsi,
			         mk.kode_mk, mk.nama_mk, jm.tahun_akademik, jm.kelas,
			         jm.mata_kuliah_id')
			->join('rps_mingguan rm', 'rm.id = ntp.rps_mingguan_id')
			->join('cpmk', 'cpmk.id = rm.cpmk_id')
			->join('jadwal_mengajar jm', 'jm.id = ntp.jadwal_mengajar_id')
			->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id')
			->where('ntp.mahasiswa_id', $mahasiswaId)
			->orderBy('cpmk.kode_cpmk', 'ASC')
			->get()
			->getResultArray();

		if (empty($nilaiData)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Belum ada data CPMK untuk mahasiswa ini'
			]);
		}

		// Group by CPMK and calculate weighted percentage
		$cpmkGroups = [];
		$courseDetails = [];

		foreach ($nilaiData as $row) {
			$kodeCpmk = $row['kode_cpmk'];
			$cpmkId = $row['cpmk_id'];

			// Decode the weight (bobot) from JSON
			$teknikData = json_decode($row['teknik_penilaian'], true);
			$bobot = isset($teknikData[$row['teknik_penilaian_key']]) ? floatval($teknikData[$row['teknik_penilaian_key']]) : 0;

			if ($bobot > 0 && $row['nilai'] !== null) {
				// Group by CPMK code for chart
				if (!isset($cpmkGroups[$kodeCpmk])) {
					$cpmkGroups[$kodeCpmk] = [
						'cpmk_id' => $cpmkId,
						'kode_cpmk' => $kodeCpmk,
						'deskripsi' => $row['deskripsi'],
						'total_weighted' => 0,
						'total_bobot' => 0
					];
				}

				$cpmkGroups[$kodeCpmk]['total_weighted'] += ($row['nilai'] * $bobot / 100);
				$cpmkGroups[$kodeCpmk]['total_bobot'] += $bobot;

				// Store course details for breakdown table
				$key = $kodeCpmk . '_' . $row['kode_mk'];
				if (!isset($courseDetails[$key])) {
					$courseDetails[$key] = [
						'cpmk_id' => $cpmkId,
						'kode_cpmk' => $kodeCpmk,
						'deskripsi' => $row['deskripsi'],
						'kode_mk' => $row['kode_mk'],
						'nama_mk' => $row['nama_mk'],
						'tahun_akademik' => $row['tahun_akademik'],
						'kelas' => $row['kelas'],
						'total_weighted' => 0,
						'total_bobot' => 0
					];
				}

				$courseDetails[$key]['total_weighted'] += ($row['nilai'] * $bobot / 100);
				$courseDetails[$key]['total_bobot'] += $bobot;
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
				? round(($group['total_weighted'] / $group['total_bobot']) * 100, 2)
				: 0;

			$chartData['labels'][] = $kodeCpmk;
			$chartData['data'][] = $capaianPersen;
		}

		// Add course-level details
		foreach ($courseDetails as $detail) {
			$nilai = $detail['total_bobot'] > 0
				? round(($detail['total_weighted'] / $detail['total_bobot']) * 100, 2)
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
			->where('program_studi', $programStudi)
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

		// Get all assessment scores with weights for these students
		// Using new formula: Capaian CPMK (%) = (Σ(nilai × bobot) / Σ(bobot))
		$nilaiData = $db->table('nilai_teknik_penilaian ntp')
			->select('ntp.nilai, ntp.teknik_penilaian_key, ntp.mahasiswa_id,
			         rm.cpmk_id, rm.teknik_penilaian,
			         cpmk.kode_cpmk, cpmk.deskripsi,
			         mk.kode_mk, mk.nama_mk')
			->join('rps_mingguan rm', 'rm.id = ntp.rps_mingguan_id')
			->join('cpmk', 'cpmk.id = rm.cpmk_id')
			->join('jadwal_mengajar jm', 'jm.id = ntp.jadwal_mengajar_id')
			->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id')
			->whereIn('ntp.mahasiswa_id', $mahasiswaIds)
			->orderBy('cpmk.kode_cpmk', 'ASC')
			->get()
			->getResultArray();

		if (empty($nilaiData)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Belum ada data CPMK untuk angkatan ini'
			]);
		}

		// First, calculate weighted scores for each student per CPMK+MK and per CPMK only
		$studentCpmkMkScores = []; // Per CPMK+MK (for detail table)
		$studentCpmkOnlyScores = []; // Per CPMK across all courses (for chart)

		foreach ($nilaiData as $row) {
			$mahasiswaId = $row['mahasiswa_id'];
			$kodeCpmk = $row['kode_cpmk'];
			$cpmkId = $row['cpmk_id'];
			$keyMk = $kodeCpmk . '_' . $row['kode_mk'];

			// Decode the weight (bobot) from JSON
			$teknikData = json_decode($row['teknik_penilaian'], true);
			$bobot = isset($teknikData[$row['teknik_penilaian_key']]) ? floatval($teknikData[$row['teknik_penilaian_key']]) : 0;

			if ($bobot > 0 && $row['nilai'] !== null) {
				// Aggregate by CPMK+MK for detail table
				if (!isset($studentCpmkMkScores[$mahasiswaId][$keyMk])) {
					$studentCpmkMkScores[$mahasiswaId][$keyMk] = [
						'cpmk_id' => $cpmkId,
						'kode_cpmk' => $kodeCpmk,
						'deskripsi' => $row['deskripsi'],
						'kode_mk' => $row['kode_mk'],
						'nama_mk' => $row['nama_mk'],
						'total_weighted' => 0,
						'total_bobot' => 0
					];
				}
				$studentCpmkMkScores[$mahasiswaId][$keyMk]['total_weighted'] += ($row['nilai'] * $bobot / 100);
				$studentCpmkMkScores[$mahasiswaId][$keyMk]['total_bobot'] += $bobot;

				// Aggregate by CPMK only (across all courses) for chart
				if (!isset($studentCpmkOnlyScores[$mahasiswaId][$kodeCpmk])) {
					$studentCpmkOnlyScores[$mahasiswaId][$kodeCpmk] = [
						'cpmk_id' => $cpmkId,
						'kode_cpmk' => $kodeCpmk,
						'deskripsi' => $row['deskripsi'],
						'total_weighted' => 0,
						'total_bobot' => 0
					];
				}
				$studentCpmkOnlyScores[$mahasiswaId][$kodeCpmk]['total_weighted'] += ($row['nilai'] * $bobot / 100);
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

				// Calculate this student's CPMK value (across all courses)
				$capaian = $data['total_bobot'] > 0
					? ($data['total_weighted'] / $data['total_bobot']) * 100
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

				// Calculate this student's CPMK value for this specific MK
				$capaian = $data['total_bobot'] > 0
					? ($data['total_weighted'] / $data['total_bobot']) * 100
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
			->where('program_studi', $programStudi)
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

		// Get all assessment scores with weights for these students
		// Using new formula: Capaian CPMK (%) = (Σ(nilai × bobot) / Σ(bobot))
		$nilaiData = $db->table('nilai_teknik_penilaian ntp')
			->select('ntp.nilai, ntp.teknik_penilaian_key, ntp.mahasiswa_id,
			         rm.cpmk_id, rm.teknik_penilaian,
			         cpmk.kode_cpmk, cpmk.deskripsi,
			         mk.kode_mk, mk.nama_mk')
			->join('rps_mingguan rm', 'rm.id = ntp.rps_mingguan_id')
			->join('cpmk', 'cpmk.id = rm.cpmk_id')
			->join('jadwal_mengajar jm', 'jm.id = ntp.jadwal_mengajar_id')
			->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id')
			->whereIn('ntp.mahasiswa_id', $mahasiswaIds)
			->orderBy('cpmk.kode_cpmk', 'ASC')
			->get()
			->getResultArray();

		if (empty($nilaiData)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Belum ada data CPMK untuk program studi ini'
			]);
		}

		// First, calculate weighted scores for each student per CPMK+MK and per CPMK only
		$studentCpmkMkScores = []; // Per CPMK+MK (for detail table)
		$studentCpmkOnlyScores = []; // Per CPMK across all courses (for chart)

		foreach ($nilaiData as $row) {
			$mahasiswaId = $row['mahasiswa_id'];
			$kodeCpmk = $row['kode_cpmk'];
			$cpmkId = $row['cpmk_id'];
			$keyMk = $kodeCpmk . '_' . $row['kode_mk'];

			// Decode the weight (bobot) from JSON
			$teknikData = json_decode($row['teknik_penilaian'], true);
			$bobot = isset($teknikData[$row['teknik_penilaian_key']]) ? floatval($teknikData[$row['teknik_penilaian_key']]) : 0;

			if ($bobot > 0 && $row['nilai'] !== null) {
				// Aggregate by CPMK+MK for detail table
				if (!isset($studentCpmkMkScores[$mahasiswaId][$keyMk])) {
					$studentCpmkMkScores[$mahasiswaId][$keyMk] = [
						'cpmk_id' => $cpmkId,
						'kode_cpmk' => $kodeCpmk,
						'deskripsi' => $row['deskripsi'],
						'kode_mk' => $row['kode_mk'],
						'nama_mk' => $row['nama_mk'],
						'total_weighted' => 0,
						'total_bobot' => 0
					];
				}
				$studentCpmkMkScores[$mahasiswaId][$keyMk]['total_weighted'] += ($row['nilai'] * $bobot / 100);
				$studentCpmkMkScores[$mahasiswaId][$keyMk]['total_bobot'] += $bobot;

				// Aggregate by CPMK only (across all courses) for chart
				if (!isset($studentCpmkOnlyScores[$mahasiswaId][$kodeCpmk])) {
					$studentCpmkOnlyScores[$mahasiswaId][$kodeCpmk] = [
						'cpmk_id' => $cpmkId,
						'kode_cpmk' => $kodeCpmk,
						'deskripsi' => $row['deskripsi'],
						'total_weighted' => 0,
						'total_bobot' => 0
					];
				}
				$studentCpmkOnlyScores[$mahasiswaId][$kodeCpmk]['total_weighted'] += ($row['nilai'] * $bobot / 100);
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

				// Calculate this student's CPMK value (across all courses)
				$capaian = $data['total_bobot'] > 0
					? ($data['total_weighted'] / $data['total_bobot']) * 100
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

				// Calculate this student's CPMK value for this specific MK
				$capaian = $data['total_bobot'] > 0
					? ($data['total_weighted'] / $data['total_bobot']) * 100
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
			->where('program_studi', $programStudi)
			->where('tahun_angkatan', $tahunAngkatan)
			->where('status_mahasiswa', 'Aktif')
			->get()
			->getResultArray();

		$mahasiswaIds = array_column($mahasiswaList, 'id');

		// Get assessment scores with weights for these students
		// Using new formula: Capaian CPMK (%) = (Σ(nilai × bobot) / Σ(bobot))
		$nilaiData = $db->table('nilai_teknik_penilaian ntp')
			->select('ntp.nilai, ntp.teknik_penilaian_key, ntp.mahasiswa_id,
			         rm.teknik_penilaian')
			->join('rps_mingguan rm', 'rm.id = ntp.rps_mingguan_id')
			->where('rm.cpmk_id', $cpmkId)
			->whereIn('ntp.mahasiswa_id', $mahasiswaIds)
			->get()
			->getResultArray();

		// Calculate weighted scores for each student
		$studentScores = [];
		foreach ($nilaiData as $row) {
			$mahasiswaId = $row['mahasiswa_id'];

			// Decode the weight (bobot) from JSON
			$teknikData = json_decode($row['teknik_penilaian'], true);
			$bobot = isset($teknikData[$row['teknik_penilaian_key']]) ? floatval($teknikData[$row['teknik_penilaian_key']]) : 0;

			if ($bobot > 0 && $row['nilai'] !== null) {
				if (!isset($studentScores[$mahasiswaId])) {
					$studentScores[$mahasiswaId] = [
						'total_weighted' => 0,
						'total_bobot' => 0
					];
				}

				$studentScores[$mahasiswaId]['total_weighted'] += ($row['nilai'] * $bobot / 100);
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
					? round(($score['total_weighted'] / $score['total_bobot']) * 100, 2)
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
			->where('program_studi', $programStudi)
			->where('status_mahasiswa', 'Aktif')
			->get()
			->getResultArray();

		$mahasiswaIds = array_column($mahasiswaList, 'id');

		// Get assessment scores with weights for these students
		// Using new formula: Capaian CPMK (%) = (Σ(nilai × bobot) / Σ(bobot))
		$nilaiData = $db->table('nilai_teknik_penilaian ntp')
			->select('ntp.nilai, ntp.teknik_penilaian_key, ntp.mahasiswa_id,
			         rm.teknik_penilaian')
			->join('rps_mingguan rm', 'rm.id = ntp.rps_mingguan_id')
			->where('rm.cpmk_id', $cpmkId)
			->whereIn('ntp.mahasiswa_id', $mahasiswaIds)
			->get()
			->getResultArray();

		// Calculate weighted scores for each student
		$studentScores = [];
		foreach ($nilaiData as $row) {
			$mahasiswaId = $row['mahasiswa_id'];

			// Decode the weight (bobot) from JSON
			$teknikData = json_decode($row['teknik_penilaian'], true);
			$bobot = isset($teknikData[$row['teknik_penilaian_key']]) ? floatval($teknikData[$row['teknik_penilaian_key']]) : 0;

			if ($bobot > 0 && $row['nilai'] !== null) {
				if (!isset($studentScores[$mahasiswaId])) {
					$studentScores[$mahasiswaId] = [
						'total_weighted' => 0,
						'total_bobot' => 0
					];
				}

				$studentScores[$mahasiswaId]['total_weighted'] += ($row['nilai'] * $bobot / 100);
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
					? round(($score['total_weighted'] / $score['total_bobot']) * 100, 2)
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

		// Get all assessment scores with weights for this student and CPMK
		$nilaiData = $db->table('nilai_teknik_penilaian ntp')
			->select('ntp.nilai, ntp.teknik_penilaian_key,
			         rm.minggu, rm.teknik_penilaian,
			         mk.kode_mk, mk.nama_mk,
			         jm.tahun_akademik, jm.kelas')
			->join('rps_mingguan rm', 'rm.id = ntp.rps_mingguan_id')
			->join('jadwal_mengajar jm', 'jm.id = ntp.jadwal_mengajar_id')
			->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id')
			->where('ntp.mahasiswa_id', $mahasiswaId)
			->where('rm.cpmk_id', $cpmk['id'])
			->orderBy('mk.kode_mk', 'ASC')
			->orderBy('rm.minggu', 'ASC')
			->get()
			->getResultArray();

		// Define technique labels
		$teknikLabels = [
			'partisipasi'   => 'Partisipasi',
			'observasi'     => 'Observasi',
			'unjuk_kerja'   => 'Unjuk Kerja',
			'proyek'        => 'Proyek',
			'tes_tulis_uts' => 'UTS',
			'tes_tulis_uas' => 'UAS',
			'tes_lisan'     => 'Tes Lisan'
		];

		// Group by course
		$courseGroups = [];
		$grandTotalWeighted = 0;
		$grandTotalBobot = 0;

		foreach ($nilaiData as $row) {
			$courseKey = $row['kode_mk'];

			// Decode the weight (bobot) from JSON
			$teknikData = json_decode($row['teknik_penilaian'], true);
			$bobot = isset($teknikData[$row['teknik_penilaian_key']]) ? floatval($teknikData[$row['teknik_penilaian_key']]) : 0;

			if ($bobot > 0 && $row['nilai'] !== null) {
				if (!isset($courseGroups[$courseKey])) {
					$courseGroups[$courseKey] = [
						'kode_mk' => $row['kode_mk'],
						'nama_mk' => $row['nama_mk'],
						'tahun_akademik' => $row['tahun_akademik'],
						'kelas' => $row['kelas'],
						'assessments' => [],
						'total_weighted' => 0,
						'total_bobot' => 0
					];
				}

				$weighted = ($row['nilai'] * $bobot) / 100;
				$teknikLabel = $teknikLabels[$row['teknik_penilaian_key']] ?? ucfirst(str_replace('_', ' ', $row['teknik_penilaian_key']));

				$courseGroups[$courseKey]['assessments'][] = [
					'minggu' => $row['minggu'],
					'teknik' => $teknikLabel,
					'teknik_key' => $row['teknik_penilaian_key'],
					'nilai' => floatval($row['nilai']),
					'bobot' => $bobot,
					'weighted' => $weighted
				];

				$courseGroups[$courseKey]['total_weighted'] += $weighted;
				$courseGroups[$courseKey]['total_bobot'] += $bobot;

				$grandTotalWeighted += $weighted;
				$grandTotalBobot += $bobot;
			}
		}

		// Calculate final capaian
		$capaian = $grandTotalBobot > 0 ? round(($grandTotalWeighted / $grandTotalBobot) * 100, 2) : 0;

		return $this->response->setJSON([
			'success' => true,
			'kode_cpmk' => $kodeCpmk,
			'deskripsi_cpmk' => $cpmk['deskripsi'],
			'courses' => array_values($courseGroups),
			'summary' => [
				'grand_total_weighted' => round($grandTotalWeighted, 2),
				'grand_total_bobot' => $grandTotalBobot,
				'capaian' => $capaian
			]
		]);
	}
}

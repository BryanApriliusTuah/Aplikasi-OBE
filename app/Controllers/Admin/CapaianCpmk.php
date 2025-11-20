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
}

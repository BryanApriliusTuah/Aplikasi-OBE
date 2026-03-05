<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MbkmModel;
use App\Models\TahunAkademikModel;

class MbkmController extends BaseController
{
	protected $mbkmModel;
	protected $db;

	public function __construct()
	{
		$this->mbkmModel = new MbkmModel();
		$this->db = \Config\Database::connect();
	}

	/**
	 * Check if user is admin
	 */
	private function isAdmin()
	{
		return session()->get('role') === 'admin';
	}

	/**
	 * Redirect non-admin users with error message
	 */
	private function unauthorizedAccess()
	{
		return redirect()->to('/admin/mbkm')->with('error', 'Anda tidak memiliki akses untuk melakukan operasi ini');
	}

	// Index - List all MBKM activities
	public function index()
	{
		// Load tahun akademik early so we can derive the default filter
		$tahunAkademikModel  = new TahunAkademikModel();
		$tahun_akademik_rows = $tahunAkademikModel->getAllForDisplay();

		if ($this->request->getGet('reset') === '1') {
			session()->remove('mbkm_filters');
			return redirect()->to('admin/mbkm');
		}

		$isFormSubmitted = $this->request->getGet('tahun') !== null
			|| $this->request->getGet('semester') !== null
			|| $this->request->getGet('status') !== null
			|| $this->request->getGet('cari') !== null;

		if ($isFormSubmitted) {
			$saved = [
				'tahun'    => $this->request->getGet('tahun'),
				'semester' => $this->request->getGet('semester'),
				'status'   => $this->request->getGet('status'),
				'cari'     => $this->request->getGet('cari'),
			];
			session()->set('mbkm_filters', $saved);
		} else {
			$saved = session()->get('mbkm_filters');
			// Default to the newest tahun/semester when no filter has been chosen yet
			if (empty($saved) && !empty($tahun_akademik_rows)) {
				$newest = $tahun_akademik_rows[0];
				$saved  = [
					'tahun'    => $newest['tahun'],
					'semester' => $newest['semester'],
				];
			}
			$saved = $saved ?? [];
		}

		// Program Studi is always locked to Teknik Informatika
		$filters = [
			'program_studi' => 'Teknik Informatika',
			'tahun'         => $saved['tahun'] ?? null,
			'semester'      => $saved['semester'] ?? null,
			'status'        => $saved['status'] ?? null,
			'cari'          => $saved['cari'] ?? null,
		];

		$modelFilters = [];

		if (!empty($filters['tahun'])) {
			$modelFilters['tahun'] = $filters['tahun'];
		}
		if (!empty($filters['semester'])) {
			$modelFilters['semester'] = $filters['semester'];
		}
		if (!empty($filters['status'])) {
			$modelFilters['status_kegiatan'] = $filters['status'];
		}
		if (!empty($filters['cari'])) {
			$modelFilters['cari'] = $filters['cari'];
		}

		$kegiatan = $this->mbkmModel->getKegiatanLengkap($modelFilters);

		// Group by status
		$kegiatan_by_status = [];
		foreach ($kegiatan as $k) {
			$status = $k['status_kegiatan'] ?? 'diajukan';
			$kegiatan_by_status[$status][] = $k;
		}

		$tahun_list = $this->db->table('tahun_akademik')
			->select('tahun')
			->distinct()
			->orderBy('tahun', 'DESC')
			->get()
			->getResultArray();

		$semester_list = ['Ganjil', 'Genap', 'Antara'];

		$status_list = array_column(
			$this->db->table('mbkm')
				->select('status_kegiatan')
				->distinct()
				->orderBy('status_kegiatan', 'ASC')
				->get()
				->getResultArray(),
			'status_kegiatan'
		);

		// Get MBKM mata kuliah for the export modal:
		// Only jadwal with kelas='KM' that are linked to at least one MBKM kegiatan
		$mkBuilder = $this->db->table('jadwal j')
			->select('j.id as jadwal_id, mk.kode_mk, mk.nama_mk, j.tahun_akademik')
			->join('mata_kuliah mk', 'mk.id = j.mata_kuliah_id')
			->join('mbkm_jadwal mj', 'mj.jadwal_id = j.id')
			->where('j.kelas', 'KM')
			->groupBy('j.id')
			->orderBy('j.tahun_akademik', 'DESC')
			->orderBy('mk.nama_mk', 'ASC');

		if (!empty($filters['tahun'])) {
			$mkBuilder->like('j.tahun_akademik', $filters['tahun'], 'after');
		}
		if (!empty($filters['semester'])) {
			$mkBuilder->like('j.tahun_akademik', $filters['semester'], 'before');
		}

		$mbkm_mk_list = $mkBuilder->get()->getResultArray();

		$data = [
			'kegiatan_by_status' => $kegiatan_by_status,
			'filters'            => $filters,
			'tahun_list'         => array_column($tahun_list, 'tahun'),
			'semester_list'      => $semester_list,
			'status_list'        => $status_list,
			'mbkm_mk_list'       => $mbkm_mk_list,
		];

		return view('admin/mbkm/index', $data);
	}

	// Create - Show form (Admin Only)
	public function create()
	{
		if (!$this->isAdmin()) {
			return $this->unauthorizedAccess();
		}

		$mahasiswa = $this->db->table('mahasiswa')
			->where('status_mahasiswa', 'Aktif')
			->orderBy('nama_lengkap', 'ASC')
			->get()
			->getResultArray();

		$dosen = $this->db->table('dosen')
			->where('status_keaktifan', 'Aktif')
			->orderBy('nama_lengkap', 'ASC')
			->get()
			->getResultArray();

		$data = [
			'mahasiswa' => $mahasiswa,
			'dosen' => $dosen
		];

		return view('admin/mbkm/create', $data);
	}

	// Store - Save new activity (Admin Only)
	public function store()
	{
		if (!$this->isAdmin()) {
			return $this->unauthorizedAccess();
		}

		$validation = \Config\Services::validation();

		$rules = [
			'mahasiswa_ids' => 'required'
		];

		if (!$this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $validation->getErrors());
		}

		// Get mahasiswa IDs array
		$mahasiswa_ids = $this->request->getPost('mahasiswa_ids');

		if (empty($mahasiswa_ids) || !is_array($mahasiswa_ids)) {
			return redirect()->back()->withInput()->with('error', 'Pilih minimal satu mahasiswa');
		}

		// Prepare kegiatan data
		$data = [
			'nim' => implode(',', array_map(function ($id) {
				$mahasiswa = $this->db->table('mahasiswa')->select('nim')->where('id', $id)->get()->getRowArray();
				return $mahasiswa ? $mahasiswa['nim'] : '';
			}, $mahasiswa_ids)),
			'program' => $this->request->getPost('program'),
			'sub_program' => $this->request->getPost('sub_program'),
			'tujuan' => $this->request->getPost('tujuan'),
			'status_kegiatan' => 'berlangsung'
		];

		$this->db->transBegin();

		// Insert kegiatan
		if (!$this->mbkmModel->insert($data)) {
			$this->db->transRollback();
			return redirect()->back()->withInput()->with('error', 'Gagal menambahkan kegiatan MBKM');
		}

		if ($this->db->transStatus() === false) {
			$this->db->transRollback();
			return redirect()->back()->withInput()->with('error', 'Gagal menambahkan kegiatan MBKM');
		}

		$this->db->transCommit();

		return redirect()->back()->with('success', 'Kegiatan MBKM berhasil ditambahkan dengan ' . count($mahasiswa_ids) . ' mahasiswa');
	}

	// Edit - Show form (Admin Only)
	public function edit($id)
	{
		if (!$this->isAdmin()) {
			return $this->unauthorizedAccess();
		}

		$kegiatan = $this->mbkmModel->find($id);

		if (!$kegiatan) {
			return redirect()->to('/admin/mbkm')->with('error', 'Kegiatan tidak ditemukan');
		}

		// Get selected mahasiswa IDs from nim column
		$selected_mahasiswa_ids = [];
		if (!empty($kegiatan['nim'])) {
			$nims = array_map('trim', explode(',', $kegiatan['nim']));
			$selected_mhs = $this->db->table('mahasiswa')
				->select('id')
				->whereIn('nim', $nims)
				->get()
				->getResultArray();
			$selected_mahasiswa_ids = array_column($selected_mhs, 'id');
		}

		$mahasiswa = $this->db->table('mahasiswa')
			->where('status_mahasiswa', 'Aktif')
			->orderBy('nama_lengkap', 'ASC')
			->get()
			->getResultArray();

		$dosen = $this->db->table('dosen')
			->where('status_keaktifan', 'Aktif')
			->orderBy('nama_lengkap', 'ASC')
			->get()
			->getResultArray();

		$data = [
			'kegiatan' => $kegiatan,
			'selected_mahasiswa_ids' => $selected_mahasiswa_ids,
			'mahasiswa' => $mahasiswa,
			'dosen' => $dosen,
		];

		return view('admin/mbkm/edit', $data);
	}

	// Update - Save changes (Admin Only)
	public function update($id)
	{
		if (!$this->isAdmin()) {
			return $this->unauthorizedAccess();
		}

		$validation = \Config\Services::validation();

		$rules = [
			'mahasiswa_ids' => 'required'
		];

		if (!$this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $validation->getErrors());
		}

		// Get mahasiswa IDs array
		$mahasiswa_ids = $this->request->getPost('mahasiswa_ids');

		if (empty($mahasiswa_ids) || !is_array($mahasiswa_ids)) {
			return redirect()->back()->withInput()->with('error', 'Pilih minimal satu mahasiswa');
		}

		// Build nim string from mahasiswa_ids
		$nim_string = implode(',', array_map(function ($id) {
			$mahasiswa = $this->db->table('mahasiswa')->select('nim')->where('id', $id)->get()->getRowArray();
			return $mahasiswa ? $mahasiswa['nim'] : '';
		}, $mahasiswa_ids));

		$data = [
			'nim' => $nim_string,
			'program' => $this->request->getPost('program'),
			'sub_program' => $this->request->getPost('sub_program'),
			'tujuan' => $this->request->getPost('tujuan'),
			'status_kegiatan' => $this->request->getPost('status_kegiatan'),
		];

		// Update kegiatan
		if (!$this->mbkmModel->update($id, $data)) {
			return redirect()->back()->withInput()->with('error', 'Gagal memperbarui kegiatan MBKM');
		}

		return redirect()->to('/admin/mbkm')->with('success', 'Kegiatan MBKM berhasil diperbarui dengan ' . count($mahasiswa_ids) . ' mahasiswa');
	}

	// Delete (Admin Only)
	public function delete($id)
	{
		if (!$this->isAdmin()) {
			return $this->unauthorizedAccess();
		}

		if ($this->mbkmModel->delete($id)) {
			return redirect()->to('/admin/mbkm')->with('success', 'Kegiatan MBKM berhasil dihapus');
		} else {
			return redirect()->to('/admin/mbkm')->with('error', 'Gagal menghapus kegiatan MBKM');
		}
	}

	// Redirect to first MBKM kegiatan for tour/tutorial purposes.
	public function inputNilaiFirst()
	{
		if (!$this->isAdmin()) {
			return $this->unauthorizedAccess();
		}
		$first = $this->db->table('mbkm')->select('id')->limit(1)->get()->getRowArray();
		if ($first) {
			return redirect()->to('admin/mbkm/input-nilai/' . $first['id'] . '?tour=1&chain=1');
		}
		return redirect()->to('admin/mbkm')->with('info', 'Belum ada data MBKM untuk ditampilkan.');
	}

	// Input nilai - Show form (Admin Only)
	public function inputNilai($kegiatan_id)
	{
		if (!$this->isAdmin()) {
			return $this->unauthorizedAccess();
		}

		// Get kegiatan
		$kegiatan = $this->db->table('mbkm k')
			->select('k.*')
			->where('k.id', $kegiatan_id)
			->get()
			->getRowArray();

		if (!$kegiatan) {
			return redirect()->to('/admin/mbkm')->with('error', 'Kegiatan tidak ditemukan');
		}

		// Get mahasiswa info (only one per MBKM)
		$mahasiswa = $this->db->table('mahasiswa')
			->where('nim', trim($kegiatan['nim']))
			->get()
			->getRowArray();

		$programStudi = $this->db->table('program_studi')
			->where('kode', $mahasiswa['program_studi_kode'])
			->get()
			->getRowArray();

		// Get grade configuration for dynamic grading
		$gradeConfigModel = new \App\Models\GradeConfigModel();
		$grade_config = $gradeConfigModel->getActiveGrades();

		// Get konversi MK data — filtered to jadwal linked to this specific kegiatan
		$konversi_mk = $this->mbkmModel->getKonversiMkByMahasiswaList($kegiatan['nim'], $kegiatan_id);

		// Get existing CPMK scores (weighted scores from database)
		$existing_scores = $this->mbkmModel->getNilaiCpmk($kegiatan['nim']);

		// Convert weighted scores back to raw scores for display (one score per MK)
		// All CPMKs for a given MK share the same raw input score, so just reverse one
		$display_scores = [];
		foreach ($konversi_mk as $mk) {
			$mk_id = $mk['mata_kuliah_id'];
			foreach ($mk['cpmk_list'] as $cpmk) {
				$cpmk_id = $cpmk['cpmk_id'];
				$bobot = $cpmk['bobot'];

				if (isset($existing_scores[$mk_id][$cpmk_id]) && $bobot > 0) {
					$weighted_score = $existing_scores[$mk_id][$cpmk_id];
					// Convert back to raw score: weighted_score * 100 / bobot
					$raw_score = ($weighted_score * 100) / $bobot;
					$display_scores[$mk_id] = round($raw_score, 2);
					break; // One score per MK is enough
				}
			}
		}

		$data = [
			'kegiatan' => $kegiatan,
			'mahasiswa' => $mahasiswa,
			'program_studi' => $programStudi,
			'grade_config' => $grade_config,
			'konversi_mk' => $konversi_mk,
			'existing_scores' => $display_scores // One raw score per MK
		];

		// dd($data);

		return view('admin/mbkm/input_nilai', $data);
	}

	// Save nilai (Admin Only)
	public function saveNilai($kegiatan_id)
	{
		if (!$this->isAdmin()) {
			return $this->unauthorizedAccess();
		}

		// Get kegiatan
		$kegiatan = $this->mbkmModel->find($kegiatan_id);
		if (!$kegiatan) {
			return redirect()->to('/admin/mbkm')->with('error', 'Kegiatan tidak ditemukan');
		}

		$nim = trim($kegiatan['nim']);

		// Get mahasiswa
		$mahasiswa = $this->db->table('mahasiswa')
			->where('nim', $nim)
			->get()
			->getRowArray();

		if (!$mahasiswa) {
			return redirect()->back()->withInput()->with('error', 'Mahasiswa tidak ditemukan');
		}

		$mahasiswa_id = $mahasiswa['id'];
		$nilai_mk = $this->request->getPost('nilai_mk'); // One score per MK

		if (empty($nilai_mk) || !is_array($nilai_mk)) {
			return redirect()->back()->withInput()->with('error', 'Data nilai tidak valid');
		}

		$this->db->transStart();

		$valid_count = 0;

		foreach ($nilai_mk as $mk_id => $nilai) {
			// Clean and validate nilai
			$nilai = str_replace(',', '.', trim($nilai));

			if ($nilai === '' || $nilai === null) {
				continue; // Skip empty values
			}

			$nilai_float = (float)$nilai;

			if ($nilai_float < 0 || $nilai_float > 100) {
				$this->db->transRollback();
				return redirect()->back()->withInput()->with('error', 'Nilai harus antara 0-100');
			}

			// Get jadwal_id for this mata_kuliah with kelas = 'KM', filtered to this kegiatan
			$jadwal = $this->db->table('jadwal j')
				->select('j.id')
				->join('jadwal_mahasiswa jm', 'jm.jadwal_id = j.id')
				->join('mbkm_jadwal mj', 'mj.jadwal_id = j.id')
				->where('j.mata_kuliah_id', $mk_id)
				->where('j.kelas', 'KM')
				->where('jm.nim', $nim)
				->where('mj.mbkm_id', (int) $kegiatan_id)
				->get()
				->getRowArray();

			if (!$jadwal) {
				continue;
			}

			$jadwal_id = $jadwal['id'];

			// Get RPS for this mata kuliah
			$rps = $this->db->table('rps')
				->select('id')
				->where('mata_kuliah_id', $mk_id)
				->orderBy('created_at', 'DESC')
				->get()
				->getRowArray();

			if (!$rps) {
				continue;
			}

			// Get all CPMKs with their bobot for this MK
			$cpmk_list = $this->db->query("
				SELECT c.id as cpmk_id, COALESCE(SUM(rm.bobot), 0) as bobot
				FROM cpmk c
				INNER JOIN cpmk_mk cm ON cm.cpmk_id = c.id
				LEFT JOIN rps_mingguan rm ON rm.cpmk_id = c.id AND rm.rps_id = ?
				WHERE cm.mata_kuliah_id = ?
				GROUP BY c.id
			", [$rps['id'], $mk_id])->getResultArray();

			// Apply the single input score to each CPMK weighted by its bobot
			foreach ($cpmk_list as $cpmk) {
				$bobot = (float)($cpmk['bobot'] ?? 0);

				if ($bobot <= 0) {
					continue;
				}

				// weighted_score = nilai * bobot / 100
				$weighted_score = ($nilai_float * $bobot) / 100;

				$this->mbkmModel->saveNilaiCpmk($mahasiswa_id, $jadwal_id, $cpmk['cpmk_id'], $weighted_score);
				$valid_count++;
			}
		}

		$this->db->transComplete();

		if ($this->db->transStatus() === false) {
			return redirect()->back()->with('error', 'Gagal menyimpan nilai');
		}

		if ($valid_count === 0) {
			return redirect()->back()->withInput()->with('error', 'Tidak ada nilai CPMK yang valid untuk disimpan');
		}

		return redirect()->to('/admin/mbkm')->with('success', 'Nilai CPMK berhasil disimpan untuk ' . $valid_count . ' CPMK');
	}

	// Sync MBKM data from API (Admin Only)
	public function syncFromApi()
	{
		if (!$this->isAdmin()) {
			return $this->unauthorizedAccess();
		}

		$semesterId = $this->request->getPost('semester_id');
		if (empty($semesterId) || !preg_match('/^\d{5}$/', $semesterId)) {
			return redirect()->back()->with('error', 'Semester ID tidak valid. Gunakan format 5 digit, misalnya 20251.');
		}

		$apiUrl = 'https://api.siuber.upr.ac.id/api/siuber/jadwal?klsSemester=' . $semesterId . '&prodiKode=58&fakKode=5&klsJenis=Merdeka';
		$apiKey = 'XT)+KVdVT]Z]1-p8<tIz/H0W5}_z%@KS';

		$client = \Config\Services::curlrequest();

		try {
			$response = $client->request('GET', $apiUrl, [
				'headers' => [
					'x-api-key' => $apiKey,
					'Accept'    => 'application/json',
				],
				'timeout' => 60,
			]);

			$body = json_decode($response->getBody(), true);
			$jadwalRaw = $body['jadwal'] ?? [];

			// Flatten and filter for Merdeka only, propagating dosen_pengajar from jadwal-item level
			$merdekaList = [];
			foreach ($jadwalRaw as $item) {
				if (isset($item['kelas']) && is_array($item['kelas'])) {
					foreach ($item['kelas'] as $k) {
						if (($k['kelas']['klsJenis'] ?? '') === 'Merdeka') {
							if (!isset($k['dosen_pengajar']) && isset($item['dosen_pengajar'])) {
								$k['dosen_pengajar'] = $item['dosen_pengajar'];
							}
							$merdekaList[] = $k;
						}
					}
				}
			}

			if (empty($merdekaList)) {
				return redirect()->back()->with('error', 'Tidak ada data kelas Merdeka ditemukan dari API.');
			}

			$jadwalInserted   = 0;
			$jadwalUpdated    = 0;
			$studentsInserted = 0;

			// Build set of valid kelas_ids to avoid FK constraint violation on jadwal.kelas_id
			$validKelasIds = [];
			$allKelas = $this->db->table('kelas')->select('kelas_id')->get()->getResultArray();
			foreach ($allKelas as $k) {
				$validKelasIds[$k['kelas_id']] = true;
			}

			foreach ($merdekaList as $kelas) {
				$mkKode        = $kelas['mata_kuliah']['mkKode'] ?? null;
				$kelasId       = $kelas['kelas']['klsId'] ?? null;
				if ($kelasId && !isset($validKelasIds[$kelasId])) {
					$kelasId = null;
				}
				$kelasSemester = $kelas['kelas']['klsSemester'] ?? null;
				$kelasStatus   = $kelas['kelas']['klsStatus'] ?? 'Aktif';
				$kelasJenis    = $kelas['kelas']['klsJenis'] ?? 'Merdeka';
				$hari          = $kelas['perkuliahan'][0]['pHari'] ?? null;
				$jamMulai      = $kelas['perkuliahan'][0]['pJam']['jMulai'] ?? null;
				$jamSelesai    = $kelas['perkuliahan'][0]['pJam']['jSelesai'] ?? null;
				$ruangKelas    = $kelas['perkuliahan'][0]['pRuangan']['rRuang'] ?? null;
				$gedung        = $kelas['perkuliahan'][0]['pRuangan']['rGedung'] ?? null;
				$mahasiswaData  = $kelas['mahasiswa'] ?? [];
				$totalMahasiswa = $mahasiswaData['mhsTotal'] ?? 0;
				$mahasiswaList  = $mahasiswaData['data'] ?? [];

				if (!$mkKode) {
					continue;
				}

				// Find mata_kuliah by kode_mk
				$mataKuliah = $this->db->table('mata_kuliah')
					->where('kode_mk', $mkKode)
					->get()
					->getRowArray();

				if (!$mataKuliah) {
					continue; // Skip if MK not in our database
				}

				$mataKuliahId     = $mataKuliah['id'];
				$programStudiKode = $mataKuliah['program_studi_kode'] ?? null;
				$tahunAkademik    = $this->deriveTahunAkademik($kelasSemester);
				$ruang            = $ruangKelas ? ($gedung ? "$gedung - $ruangKelas" : $ruangKelas) : null;

				// Check by kelas_id first, then fall back to MK + 'KM' + tahun_akademik
				$existing = null;
				if ($kelasId) {
					$existing = $this->db->table('jadwal')
						->where('kelas_id', $kelasId)
						->get()
						->getRowArray();
				}
				if (!$existing) {
					$existing = $this->db->table('jadwal')
						->where('mata_kuliah_id', $mataKuliahId)
						->where('kelas', 'KM')
						->where('tahun_akademik', $tahunAkademik)
						->get()
						->getRowArray();
				}

				if ($existing) {
					$this->db->table('jadwal')->where('id', $existing['id'])->update([
						'kelas_id'          => $kelasId,
						'kelas_jenis'       => $kelasJenis,
						'kelas_semester'    => $kelasSemester,
						'kelas_status'      => $kelasStatus,
						'mk_kurikulum_kode' => $mkKode,
						'total_mahasiswa'   => $totalMahasiswa,
						'ruang'             => $ruang,
						'hari'              => $hari,
						'jam_mulai'         => $jamMulai,
						'jam_selesai'       => $jamSelesai,
					]);
					$jadwalId = $existing['id'];
					$jadwalUpdated++;
				} else {
					$this->db->table('jadwal')->insert([
						'mata_kuliah_id'    => $mataKuliahId,
						'program_studi_kode' => $programStudiKode,
						'tahun_akademik'    => $tahunAkademik,
						'kelas'             => 'KM',
						'ruang'             => $ruang,
						'hari'              => $hari,
						'jam_mulai'         => $jamMulai,
						'jam_selesai'       => $jamSelesai,
						'status'            => 'active',
						'kelas_id'          => $kelasId,
						'kelas_jenis'       => $kelasJenis,
						'kelas_semester'    => $kelasSemester,
						'kelas_status'      => $kelasStatus,
						'mk_kurikulum_kode' => $mkKode,
						'total_mahasiswa'   => $totalMahasiswa,
					]);
					$jadwalId = $this->db->insertID();
					$jadwalInserted++;
				}

				// Sync dosen from API dosen_pengajar (runs on both insert and update)
				$this->syncDosenFromApi($jadwalId, $kelas['dosen_pengajar'] ?? []);

				// Sync mahasiswa and their nilai for this jadwal
				if (!empty($mahasiswaList)) {
					// Deduplicate by NIM to handle API returning the same student twice
					$seenNims = [];
					$mahasiswaList = array_filter($mahasiswaList, function ($mhsData) use (&$seenNims) {
						$nim = $mhsData['nim'] ?? null;
						if (!$nim || isset($seenNims[$nim])) return false;
						$seenNims[$nim] = true;
						return true;
					});

					$this->db->table('jadwal_mahasiswa')->where('jadwal_id', $jadwalId)->delete();

					// Get RPS and CPMK list for scoring
					$rps = $this->db->table('rps')
						->select('id')
						->where('mata_kuliah_id', $mataKuliahId)
						->orderBy('created_at', 'DESC')
						->get()->getRowArray();

					$cpmkList = [];
					if ($rps) {
						$cpmkList = $this->db->query("
							SELECT c.id as cpmk_id, COALESCE(SUM(rm.bobot), 0) as bobot
							FROM cpmk c
							INNER JOIN cpmk_mk cm ON cm.cpmk_id = c.id
							LEFT JOIN rps_mingguan rm ON rm.cpmk_id = c.id AND rm.rps_id = ?
							WHERE cm.mata_kuliah_id = ?
							GROUP BY c.id
						", [$rps['id'], $mataKuliahId])->getResultArray();
					}

					foreach ($mahasiswaList as $mhsData) {
						$nim        = $mhsData['nim'] ?? null;
						$nilaiTotal = $mhsData['nilai_total'];

						if (!$nim) continue;

						$mahasiswa = $this->db->table('mahasiswa')->where('nim', $nim)->get()->getRowArray();
						if (!$mahasiswa) continue;

						$this->db->query(
							'INSERT IGNORE INTO jadwal_mahasiswa (jadwal_id, nim) VALUES (?, ?)',
							[$jadwalId, $nim]
						);
						$studentsInserted++;

						// Save nilai_total to CPMK scores
						if ($nilaiTotal !== null && !empty($cpmkList)) {
							$nilaiFloat = (float) $nilaiTotal;
							foreach ($cpmkList as $cpmk) {
								$bobot = (float) ($cpmk['bobot'] ?? 0);
								if ($bobot <= 0) continue;
								$weightedScore = ($nilaiFloat * $bobot) / 100;
								$this->mbkmModel->saveNilaiCpmk($mahasiswa['id'], $jadwalId, $cpmk['cpmk_id'], $weightedScore);
							}
						}
					}
				}
			}

			// Generate mbkm records from mahasiswa API (same logic as generateFromApi)
			$mbkmInserted = 0;
			$mbkmUpdated  = 0;

			$mbkmApiUrl   = 'https://api.siuber.upr.ac.id/api/siuber/mahasiswa?fakKode=5&prodiKode=58';
			$mbkmResponse = $client->request('GET', $mbkmApiUrl, [
				'headers' => [
					'x-api-key' => $apiKey,
					'Accept'    => 'application/json',
				],
				'timeout' => 60,
			]);

			$mbkmBody = json_decode($mbkmResponse->getBody(), true);

			if (isset($mbkmBody['data']) && !empty($mbkmBody['data'])) {
				foreach ($mbkmBody['data'] as $mhs) {
					$nim      = $mhs['mhsNim'] ?? null;
					$mbkmList = $mhs['mbkm'] ?? [];

					if (!$nim || empty($mbkmList)) {
						continue;
					}

					foreach ($mbkmList as $mbkm) {
						$program    = $mbkm['program']['nama'] ?? null;
						$subProgram = $mbkm['sub_program']['nama'] ?? null;
						$tujuan     = $mbkm['tujuan'] ?? null;
						$status     = strtolower($mbkm['status'] ?? 'berlangsung');
						$semesterRaw = $mbkm['semester'] ?? null;
						$semester    = $semesterRaw ? $this->deriveTahunAkademik($semesterRaw) : null;

						$existingMbkm = $this->db->table('mbkm')
							->where('nim', $nim)
							->where('program', $program)
							->where('sub_program', $subProgram)
							->get()
							->getRowArray();

						$mbkmData = [
							'nim'             => $nim,
							'program'         => $program,
							'sub_program'     => $subProgram,
							'tujuan'          => $tujuan,
							'status_kegiatan' => $status,
							'semester'        => $semester,
							'created_at'      => date('Y-m-d H:i:s'),
							'updated_at'      => date('Y-m-d H:i:s'),
						];

						if ($existingMbkm) {
							$this->db->table('mbkm')->where('id', $existingMbkm['id'])->update($mbkmData);
							$mbkmUpdated++;
						} else {
							$this->db->table('mbkm')->insert($mbkmData);
							$mbkmInserted++;
						}
					}
				}
			}

			// Link mbkm records to their jadwal based on student enrollment in this semester
			$mbkmLinked = 0;

			$semesterJadwals = $this->db->table('jadwal')
				->select('id')
				->where('kelas', 'KM')
				->where('kelas_semester', $semesterId)
				->get()
				->getResultArray();

			if (!empty($semesterJadwals)) {
				$semesterJadwalIds = array_column($semesterJadwals, 'id');

				// Remove stale links for this semester's jadwal so we rebuild fresh
				$this->db->table('mbkm_jadwal')->whereIn('jadwal_id', $semesterJadwalIds)->delete();

				// Derive the tahun_akademik string for this semester to filter MBKM records
				$semesterTahunAkademik = $this->deriveTahunAkademik($semesterId);

				foreach ($semesterJadwalIds as $jId) {
					$nimsInJadwal = $this->db->table('jadwal_mahasiswa')
						->select('nim')
						->where('jadwal_id', $jId)
						->get()
						->getResultArray();

					foreach ($nimsInJadwal as $nimRow) {
						$activeMbkms = $this->db->table('mbkm')
							->select('id')
							->where('nim', $nimRow['nim'])
							->groupStart()
								->where('semester', $semesterTahunAkademik)
								->orWhere('semester IS NULL', null, false)
							->groupEnd()
							->get()
							->getResultArray();

						foreach ($activeMbkms as $mbkmRow) {
							$this->db->query(
								'INSERT IGNORE INTO mbkm_jadwal (mbkm_id, jadwal_id) VALUES (?, ?)',
								[$mbkmRow['id'], $jId]
							);
							$mbkmLinked++;
						}
					}
				}
			}

			$message = "Sinkronisasi MBKM berhasil! $jadwalInserted jadwal baru, $jadwalUpdated diperbarui, $studentsInserted mahasiswa disinkronkan, $mbkmInserted kegiatan MBKM baru dibuat, $mbkmUpdated diperbarui, $mbkmLinked tautan mbkm-jadwal dibuat.";
			return redirect()->to('/admin/mbkm')->with('success', $message);
		} catch (\Exception $e) {
			log_message('error', 'MBKM sync error: ' . $e->getMessage());
			return redirect()->back()->with('error', 'Gagal mengambil data dari API: ' . $e->getMessage());
		}
	}

	/**
	 * Sync jadwal_dosen from API dosen_pengajar array.
	 * First entry → leader (dosen koordinator), rest → member (dosen pengampu).
	 */
	private function syncDosenFromApi(int $jadwalId, array $dosenPengajar): void
	{
		if (empty($dosenPengajar)) return;

		$this->db->table('jadwal_dosen')->where('jadwal_id', $jadwalId)->delete();
		foreach ($dosenPengajar as $index => $dosen) {
			$nip = $dosen['nip'] ?? null;
			if (!$nip) continue;
			$local = $this->db->table('dosen')->where('nip', $nip)->get()->getRowArray();
			if (!$local) continue;
			$this->db->table('jadwal_dosen')->insert([
				'jadwal_id' => $jadwalId,
				'dosen_id'  => $local['id'],
				'role'      => ($index === 0) ? 'leader' : 'member',
			]);
		}
	}

	/**
	 * Derive tahun_akademik string from API semester code (e.g. 20252 -> "2025 Genap")
	 */
	private function deriveTahunAkademik($semesterCode)
	{
		if (!$semesterCode) {
			return date('Y') . ' Ganjil';
		}

		$code = (string) $semesterCode;
		$year = (int) substr($code, 0, 4);
		$term = substr($code, 4, 1);

		return $term === '1' ? $year . ' Ganjil' : $year . ' Genap';
	}

	// Detail nilai (AJAX) - Accessible to all authenticated users
	public function detailNilai($kegiatan_id)
	{
		if (!$this->request->isAJAX()) {
			return $this->response->setStatusCode(403)->setJSON(['error' => 'Invalid request']);
		}

		// Get kegiatan with full details including CPL/CPMK from kegiatan table
		$kegiatan = $this->db->table('mbkm k')
			->select('k.*,
					d.nama_lengkap as nama_dosen_pembimbing, d.nip,
					na.nilai_angka, na.nilai_huruf, na.status_kelulusan, na.catatan_akhir,
					cpmk.kode_cpmk, cpmk.deskripsi as cpmk_deskripsi,
					cpl.kode_cpl, cpl.deskripsi as cpl_deskripsi')
			->join('dosen d', 'd.id = k.dosen_pembimbing_id', 'left')
			->join('mbkm_nilai_akhir na', 'na.kegiatan_id = k.id', 'left')
			->join('cpmk', 'cpmk.id = k.cpmk_id', 'left')
			->join('cpl', 'cpl.id = k.cpl_id', 'left')
			->where('k.id', $kegiatan_id)
			->get()
			->getRowArray();

		if (!$kegiatan) {
			return $this->response->setStatusCode(404)->setJSON(['error' => 'Kegiatan tidak ditemukan']);
		}

		// Add mahasiswa info to kegiatan array
		if (!empty($mahasiswa_list)) {
			$kegiatan['nim'] = implode(', ', array_column($mahasiswa_list, 'nim'));
			$kegiatan['nama_mahasiswa'] = implode(', ', array_column($mahasiswa_list, 'nama_lengkap'));
			$kegiatan['program_studi_kode'] = $mahasiswa_list[0]['program_studi_kode'] ?? '-';
		} else {
			$kegiatan['nim'] = '-';
			$kegiatan['nama_mahasiswa'] = '-';
			$kegiatan['program_studi_kode'] = '-';
		}

		// Build capaian info from kegiatan (not nilai_akhir)
		$capaian = null;
		if (!empty($kegiatan['nilai_type'])) {
			if ($kegiatan['nilai_type'] === 'cpmk' && !empty($kegiatan['kode_cpmk'])) {
				$capaian = [
					'type' => 'CPMK',
					'kode' => $kegiatan['kode_cpmk'],
					'deskripsi' => $kegiatan['cpmk_deskripsi']
				];
			} elseif ($kegiatan['nilai_type'] === 'cpl' && !empty($kegiatan['kode_cpl'])) {
				$capaian = [
					'type' => 'CPL',
					'kode' => $kegiatan['kode_cpl'],
					'deskripsi' => $kegiatan['cpl_deskripsi']
				];
			}
		}

		return $this->response->setJSON([
			'kegiatan' => $kegiatan,
			'capaian' => $capaian,
		]);
	}

	/**
	 * Export CPMK scores (all students) per mata kuliah linked to a kegiatan
	 * Each mata kuliah gets its own sheet in the Excel file
	 */
	public function exportCpmkExcel($kegiatan_id)
	{
		$kegiatan = $this->mbkmModel->find($kegiatan_id);
		if (!$kegiatan) {
			return redirect()->to('/admin/mbkm')->with('error', 'Kegiatan tidak ditemukan');
		}

		// Get all jadwal (mata kuliah KM) linked to this kegiatan
		$jadwal_list = $this->db->table('mbkm_jadwal mj')
			->select('j.id as jadwal_id, mk.kode_mk, mk.nama_mk, mk.sks, j.kelas, j.tahun_akademik, j.kelas_semester, ps.nama_resmi as program_studi')
			->join('jadwal j', 'j.id = mj.jadwal_id')
			->join('mata_kuliah mk', 'mk.id = j.mata_kuliah_id')
			->join('program_studi ps', 'ps.kode = j.program_studi_kode', 'left')
			->where('mj.mbkm_id', (int) $kegiatan_id)
			->where('j.kelas', 'KM')
			->get()
			->getResultArray();

		if (empty($jadwal_list)) {
			return redirect()->back()->with('error', 'Tidak ada mata kuliah MBKM yang terhubung dengan kegiatan ini.');
		}

		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$spreadsheet->getProperties()
			->setCreator('OBE System')
			->setTitle('Nilai CPMK MBKM')
			->setSubject('Nilai CPMK MBKM');

		$sheetIndex    = 0;
		$cpmkModel     = new \App\Models\CpmkModel();
		$nilaiCpmkModel = new \App\Models\NilaiCpmkMahasiswaModel();
		$gradeConfigModel = new \App\Models\GradeConfigModel();

		foreach ($jadwal_list as $jadwal) {
			$jadwal_id = $jadwal['jadwal_id'];

			// All students enrolled in this MBKM jadwal
			$students = $this->db->table('jadwal_mahasiswa jm')
				->select('m.id, m.nim, m.nama_lengkap')
				->join('mahasiswa m', 'm.nim = jm.nim')
				->where('jm.jadwal_id', $jadwal_id)
				->orderBy('m.nim', 'ASC')
				->get()
				->getResultArray();

			$cpmk_list      = $cpmkModel->getCpmkByJadwal($jadwal_id);
			$existing_scores = $nilaiCpmkModel->getScoresByJadwalForInput($jadwal_id);

			// Create or reuse sheet
			if ($sheetIndex === 0) {
				$sheet = $spreadsheet->getActiveSheet();
			} else {
				$sheet = $spreadsheet->createSheet();
			}
			$sheetName = substr(preg_replace('/[^A-Za-z0-9 ]/', '', $jadwal['nama_mk']), 0, 30);
			$sheet->setTitle($sheetName ?: 'Sheet' . ($sheetIndex + 1));
			$sheetIndex++;

			// Logo
			$logoPath = FCPATH . 'img/Logo UPR.png';
			if (file_exists($logoPath)) {
				$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
				$drawing->setName('Logo');
				$drawing->setPath($logoPath);
				$drawing->setCoordinates('A1');
				$drawing->setHeight(50);
				$drawing->setOffsetX(10);
				$drawing->setOffsetY(5);
				$drawing->setWorksheet($sheet);
			}

			preg_match('/(Ganjil|Genap|Antara)/', $jadwal['tahun_akademik'] ?? '', $semMatch);
			$semester_type = $semMatch[1] ?? '';
			$tahun = trim(preg_replace('/(Ganjil|Genap|Antara)/', '', $jadwal['tahun_akademik'] ?? ''));

			// Columns: No, NIM, Nama + (Skor + Capaian) per CPMK + Angka + Huruf + Keterangan
			$totalColumns = 3 + (count($cpmk_list) * 2) + 3;
			$lastCol      = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns);
			$headerEndCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns - 1);

			// Row 1 – Ministry
			$sheet->getRowDimension(1)->setRowHeight(50);
			$sheet->setCellValue('B1', "KEMENTERIAN PENDIDIKAN TINGGI, SAINS, \nDAN TEKNOLOGI");
			$sheet->mergeCells('B1:' . $headerEndCol . '1');
			$sheet->getStyle('B1')->getFont()->setBold(true)->setSize(14);
			$sheet->getStyle('B1')->getAlignment()
				->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
				->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
				->setWrapText(true);
			$sheet->setCellValue($lastCol . '1', "NILAI CPMK MBKM\nSemester {$semester_type} {$tahun}");
			$sheet->getStyle($lastCol . '1')->getFont()->setBold(true)->setSize(13);
			$sheet->getStyle($lastCol . '1')->getAlignment()
				->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
				->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
				->setWrapText(true);

			// Row 2 – University
			$sheet->getRowDimension(2)->setRowHeight(20);
			$sheet->setCellValue('B2', 'UNIVERSITAS PALANGKA RAYA');
			$sheet->mergeCells('B2:' . $headerEndCol . '2');
			$sheet->getStyle('B2')->getFont()->setBold(true)->setSize(14);
			$sheet->getStyle('B2')->getAlignment()
				->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
				->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

			// Course info
			$row = 4;
			$sheet->setCellValue('B' . $row, 'MATA KULIAH');
			$sheet->setCellValue('C' . $row, strtoupper($jadwal['nama_mk']));
			$row++;
			$sheet->setCellValue('B' . $row, 'KELAS / PROGRAM STUDI');
			$sheet->setCellValue('C' . $row, 'KM / ' . strtoupper($jadwal['program_studi'] ?? ''));
			$row++;
			$sheet->setCellValue('B' . $row, 'TAHUN AKADEMIK');
			$sheet->setCellValue('C' . $row, $jadwal['tahun_akademik']);
			$sheet->getStyle('B4:C' . $row)->getFont()->setBold(true)->setSize(11);

			// Table header – row 1
			$row += 2;
			$headerRow = $row;
			$col = 1;

			$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, 'No');
			$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, 'NIM');
			$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, 'Nama');

			foreach ($cpmk_list as $cpmk) {
				$startL = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
				$endL   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
				$sheet->setCellValue($startL . $row, $cpmk['kode_cpmk']);
				$sheet->mergeCells($startL . $row . ':' . $endL . $row);
				$col += 2;
			}

			$naStart = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
			$naEnd   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
			$sheet->setCellValue($naStart . $row, 'Nilai Akhir');
			$sheet->mergeCells($naStart . $row . ':' . $naEnd . $row);
			$ketL = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 2);
			$sheet->setCellValue($ketL . $row, 'Keterangan');
			$sheet->mergeCells($ketL . $row . ':' . $ketL . ($row + 1));

			$sheet->mergeCells('A' . $row . ':A' . ($row + 1));
			$sheet->mergeCells('B' . $row . ':B' . ($row + 1));
			$sheet->mergeCells('C' . $row . ':C' . ($row + 1));

			// Table header – row 2 (sub-headers)
			$row++;
			$col = 4;
			foreach ($cpmk_list as $ignored) {
				$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, 'Skor');
				$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, 'Capaian (%)');
			}
			$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, 'Angka');
			$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col)   . $row, 'Huruf');

			// Style header rows
			$headerStyle = $sheet->getStyle('A' . $headerRow . ':' . $lastCol . $row);
			$headerStyle->getFont()->setBold(true);
			$headerStyle->getFill()
				->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
				->getStartColor()->setARGB('FF4472C4');
			$headerStyle->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
			$headerStyle->getAlignment()
				->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
				->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
				->setWrapText(true);

			// Data rows
			$row++;
			$no = 1;

			foreach ($students as $student) {
				$col = 1;
				$mahasiswa_id = $student['id'];

				$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, $no++);
				$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, $student['nim']);
				$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, $student['nama_lengkap']);

				$student_scores = [];
				foreach ($cpmk_list as $cpmk) {
					$score = $existing_scores[$mahasiswa_id][$cpmk['id']] ?? null;
					if ($score !== null && $score !== '') {
						$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, $score);
						$capaian = $cpmk['bobot_cpmk'] > 0 ? ($score / $cpmk['bobot_cpmk']) * 100 : 0;
						$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, number_format($capaian, 2));
						$student_scores[] = (float) $score;
					} else {
						$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, '-');
						$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, '-');
					}
				}

				if (!empty($student_scores)) {
					$total       = array_sum($student_scores);
					$grade_data  = $gradeConfigModel->getGradeByScore($total);
					$nilai_huruf = $grade_data ? $grade_data['grade_letter'] : 'E';
					$is_passing  = $grade_data ? (bool) $grade_data['is_passing'] : false;
					$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, number_format($total, 2));
					$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, $nilai_huruf);
					$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col)   . $row, $is_passing ? 'Lulus' : 'Tidak Lulus');
				} else {
					$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, '-');
					$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, '-');
					$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col)   . $row, '-');
				}

				$sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				$sheet->getStyle('D' . $row . ':' . $lastCol . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				$row++;
			}

			// Borders on table
			$lastRow = $row - 1;
			if ($lastRow >= $headerRow) {
				$sheet->getStyle('A' . $headerRow . ':' . $lastCol . $lastRow)->applyFromArray([
					'borders' => [
						'allBorders' => [
							'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
							'color'       => ['argb' => 'FF000000'],
						],
					],
				]);
			}

			// Auto-size columns
			for ($c = 1; $c <= $totalColumns; $c++) {
				$sheet->getColumnDimension(
					\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c)
				)->setAutoSize(true);
			}

			// Signature block
			$row = $lastRow + 3;
			$sheet->setCellValue($lastCol . $row, 'Palangka Raya, ' . date('d F Y'));
			$row++;
			$sheet->setCellValue($lastCol . $row, 'Mengetahui');
			$row++;
			$sheet->setCellValue($lastCol . $row, 'Dosen Koordinator Mata Kuliah');
			$row += 4;

			$dosenNip = $this->db->table('jadwal_dosen jd')
				->select('d.nama_lengkap, d.nip')
				->join('dosen d', 'd.id = jd.dosen_id')
				->where('jd.jadwal_id', $jadwal_id)
				->where('jd.role', 'leader')
				->get()
				->getRowArray();

			$sheet->setCellValue($lastCol . $row, $dosenNip['nama_lengkap'] ?? '');
			$sheet->getStyle($lastCol . $row)->getFont()->setBold(true);
			$row++;
			$sheet->setCellValue($lastCol . $row, 'NIP. ' . ($dosenNip['nip'] ?? ''));
		}

		$spreadsheet->setActiveSheetIndex(0);

		$filename = 'Nilai_CPMK_MBKM_' . date('YmdHis') . '.xlsx';
		$writer   = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		exit;
	}
}

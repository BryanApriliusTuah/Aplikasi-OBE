<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MbkmModel;

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
		// Program Studi is always locked to Teknik Informatika
		$filters = [
			'program_studi' => 'Teknik Informatika',
			'tahun'         => $this->request->getGet('tahun'),
			'semester'      => $this->request->getGet('semester'),
		];

		$modelFilters = [];

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

		$data = [
			'kegiatan_by_status' => $kegiatan_by_status,
			'filters'            => $filters,
			'tahun_list'         => array_column($tahun_list, 'tahun'),
			'semester_list'      => $semester_list,
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

		return redirect()->to('/admin/mbkm')->with('success', 'Kegiatan MBKM berhasil ditambahkan dengan ' . count($mahasiswa_ids) . ' mahasiswa');
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

		// Get konversi MK data from jadwal with kelas = 'KM'
		$konversi_mk = $this->mbkmModel->getKonversiMkByMahasiswaList($kegiatan['nim']);

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

			// Get jadwal_id for this mata_kuliah with kelas = 'KM' for this mahasiswa
			$jadwal = $this->db->table('jadwal j')
				->select('j.id')
				->join('jadwal_mahasiswa jm', 'jm.jadwal_id = j.id')
				->where('j.mata_kuliah_id', $mk_id)
				->where('j.kelas', 'KM')
				->where('jm.nim', $nim)
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

		$apiUrl = 'https://tik.upr.ac.id/api/siuber/jadwal?prodiKode=58';
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

			// Flatten and filter for Merdeka only
			$merdekaList = [];
			foreach ($jadwalRaw as $item) {
				if (isset($item['kelas']) && is_array($item['kelas'])) {
					foreach ($item['kelas'] as $k) {
						if (($k['kelas']['klsJenis'] ?? '') === 'Merdeka') {
							$merdekaList[] = $k;
						}
					}
				}
			}

			if (empty($merdekaList)) {
				return redirect()->back()->with('error', 'Tidak ada data kelas Merdeka ditemukan dari API.');
			}

			$jadwalInserted = 0;
			$jadwalUpdated  = 0;
			$studentsInserted = 0;
			$mbkmCreated = 0;
			$allMerdekaNims = []; // unique NIMs found in any Merdeka class

			foreach ($merdekaList as $kelas) {
				$mkKode         = $kelas['mata_kuliah']['mkKode'] ?? null;
				$kelasId        = $kelas['kelas']['klsId'] ?? null;
				$kelasSemester  = $kelas['kelas']['klsSemester'] ?? null;
				$kelasStatus    = $kelas['kelas']['klsStatus'] ?? 'Aktif';
				$hari           = $kelas['perkuliahan'][0]['pHari'] ?? null;
				$jamMulai       = $kelas['perkuliahan'][0]['pJam']['jMulai'] ?? null;
				$jamSelesai     = $kelas['perkuliahan'][0]['pJam']['jSelesai'] ?? null;
				$ruangKelas     = $kelas['perkuliahan'][0]['pRuangan']['rRuang'] ?? null;
				$gedung         = $kelas['perkuliahan'][0]['pRuangan']['rGedung'] ?? null;
				$mahasiswaData  = $kelas['mahasiswa'] ?? [];
				$totalMahasiswa = $mahasiswaData['mhsTotal'] ?? 0;
				$nimList        = $mahasiswaData['mhsNim'] ?? [];

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

				$mataKuliahId    = $mataKuliah['id'];
				$programStudiKode = $mataKuliah['program_studi_kode'] ?? null;
				$tahunAkademik   = $this->deriveTahunAkademik($kelasSemester);
				$ruang           = $ruangKelas ? ($gedung ? "$gedung - $ruangKelas" : $ruangKelas) : null;

				// Check if a KM jadwal already exists for this MK + prodi + semester
				$existing = $this->db->table('jadwal')
					->where('mata_kuliah_id', $mataKuliahId)
					->where('kelas', 'KM')
					->where('tahun_akademik', $tahunAkademik)
					->get()
					->getRowArray();

				if ($existing) {
					$this->db->table('jadwal')->where('id', $existing['id'])->update([
						'kelas_id'       => $kelasId,
						'kelas_jenis'    => 'Merdeka',
						'kelas_semester' => $kelasSemester,
						'kelas_status'   => $kelasStatus,
						'mk_kurikulum_kode' => $mkKode,
						'total_mahasiswa' => $totalMahasiswa,
						'ruang'          => $ruang,
						'hari'           => $hari,
						'jam_mulai'      => $jamMulai,
						'jam_selesai'    => $jamSelesai,
					]);
					$jadwalId = $existing['id'];
					$jadwalUpdated++;
				} else {
					$this->db->table('jadwal')->insert([
						'mata_kuliah_id'   => $mataKuliahId,
						'program_studi_kode' => $programStudiKode,
						'tahun_akademik'   => $tahunAkademik,
						'kelas'            => 'KM',
						'ruang'            => $ruang,
						'hari'             => $hari,
						'jam_mulai'        => $jamMulai,
						'jam_selesai'      => $jamSelesai,
						'status'           => 'active',
						'kelas_id'         => $kelasId,
						'kelas_jenis'      => 'Merdeka',
						'kelas_semester'   => $kelasSemester,
						'kelas_status'     => $kelasStatus,
						'mk_kurikulum_kode' => $mkKode,
						'total_mahasiswa'  => $totalMahasiswa,
					]);
					$jadwalId = $this->db->insertID();
					$jadwalInserted++;
				}

				// Sync mahasiswa for this jadwal
				if (!empty($nimList)) {
					$this->db->table('jadwal_mahasiswa')->where('jadwal_id', $jadwalId)->delete();

					foreach ($nimList as $nim) {
						$mhsExists = $this->db->table('mahasiswa')->where('nim', $nim)->countAllResults();
						if ($mhsExists > 0) {
							$this->db->table('jadwal_mahasiswa')->insert([
								'jadwal_id' => $jadwalId,
								'nim'       => $nim,
							]);
							$studentsInserted++;
							$allMerdekaNims[$nim] = true;
						}
					}
				}
			}

			// Create mbkm records for each unique Merdeka student (if not already exists)
			foreach ($allMerdekaNims as $nim => $_) {
				$existingMbkm = $this->db->table('mbkm')
					->where('nim', $nim)
					->get()
					->getRowArray();

				if (!$existingMbkm) {
					$this->db->table('mbkm')->insert([
						'nim'             => $nim,
						'program'         => null,
						'sub_program'     => null,
						'tujuan'          => null,
						'status_kegiatan' => 'berlangsung',
						'created_at'      => date('Y-m-d H:i:s'),
						'updated_at'      => date('Y-m-d H:i:s'),
					]);
					$mbkmCreated++;
				}
			}

			$message = "Sinkronisasi MBKM berhasil! $jadwalInserted jadwal baru, $jadwalUpdated diperbarui, $studentsInserted mahasiswa disinkronkan, $mbkmCreated kegiatan MBKM baru dibuat.";
			return redirect()->to('/admin/mbkm')->with('success', $message);
		} catch (\Exception $e) {
			log_message('error', 'MBKM sync error: ' . $e->getMessage());
			return redirect()->back()->with('error', 'Gagal mengambil data dari API: ' . $e->getMessage());
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
}

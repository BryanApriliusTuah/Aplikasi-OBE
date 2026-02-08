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
		$filters = [
			'program_studi' => $this->request->getGet('program_studi'),
			'tahun_akademik' => $this->request->getGet('tahun_akademik'),
			'status_kegiatan' => $this->request->getGet('status_kegiatan')
		];

		$kegiatan = $this->mbkmModel->getKegiatanLengkap($filters);

		// Group by status
		$kegiatan_by_status = [];
		foreach ($kegiatan as $k) {
			$status = $k['status_kegiatan'] ?? 'diajukan';
			$kegiatan_by_status[$status][] = $k;
		}

		$data = [
			'kegiatan_by_status' => $kegiatan_by_status,
			'filters' => $filters
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

		// Convert weighted scores back to raw scores for display
		// This prevents double-weighting when user saves again
		$display_scores = [];
		foreach ($konversi_mk as $mk) {
			$mk_id = $mk['mata_kuliah_id'];
			foreach ($mk['cpmk_list'] as $cpmk) {
				$cpmk_id = $cpmk['cpmk_id'];
				$bobot = $cpmk['bobot'];

				if (isset($existing_scores[$mk_id][$cpmk_id]) && $bobot > 0) {
					$weighted_score = $existing_scores[$mk_id][$cpmk_id];
					// Convert back to raw score: weighted_score * 100 / bobot
					// Example: 20 * 100 / 20 = 100
					$raw_score = ($weighted_score * 100) / $bobot;
					$display_scores[$mk_id][$cpmk_id] = $raw_score;
				}
			}
		}

		$data = [
			'kegiatan' => $kegiatan,
			'mahasiswa' => $mahasiswa,
			'program_studi' => $programStudi,
			'grade_config' => $grade_config,
			'konversi_mk' => $konversi_mk,
			'existing_scores' => $display_scores // Use raw scores for display
		];

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
		$nilai_cpmk = $this->request->getPost('nilai_cpmk'); // Array of CPMK scores

		// Validate and save CPMK scores
		if (empty($nilai_cpmk) || !is_array($nilai_cpmk)) {
			return redirect()->back()->withInput()->with('error', 'Data nilai CPMK tidak valid');
		}

		$this->db->transStart();

		// Save each CPMK score
		$total_weighted_score = 0;
		$total_bobot = 0;
		$valid_count = 0;

		foreach ($nilai_cpmk as $mk_id => $cpmk_scores) {
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
				continue; // Skip if no jadwal found
			}

			$jadwal_id = $jadwal['id'];

			// Get RPS for this mata kuliah to calculate CPMK weights
			$rps = $this->db->table('rps')
				->select('id')
				->where('mata_kuliah_id', $mk_id)
				->orderBy('created_at', 'DESC')
				->get()
				->getRowArray();

			foreach ($cpmk_scores as $cpmk_id => $nilai) {
				// Clean and validate nilai
				$nilai = str_replace(',', '.', trim($nilai));

				if ($nilai === '' || $nilai === null) {
					continue; // Skip empty values
				}

				$nilai_float = (float)$nilai;

				if ($nilai_float < 0 || $nilai_float > 100) {
					$this->db->transRollback();
					return redirect()->back()->withInput()->with('error', 'Nilai CPMK harus antara 0-100');
				}

				// Get actual CPMK bobot from rps_mingguan (sum of all weeks)
				$bobot = 0;
				if ($rps) {
					$bobot_result = $this->db->table('rps_mingguan')
						->selectSum('bobot')
						->where('rps_id', $rps['id'])
						->where('cpmk_id', $cpmk_id)
						->get()
						->getRowArray();

					$bobot = $bobot_result['bobot'] ?? 0;
				}

				// Calculate weighted score (input * bobot / 100)
				// Example: input 100, bobot 20% -> weighted score = 100 * 20 / 100 = 20
				$weighted_score = ($nilai_float * $bobot) / 100;

				if ($bobot > 0) {
					$total_weighted_score += $weighted_score * 100; // For final calculation
					$total_bobot += $bobot;
					$valid_count++;
				}

				// Save weighted CPMK score to nilai_cpmk_mahasiswa table
				$this->mbkmModel->saveNilaiCpmk($mahasiswa_id, $jadwal_id, $cpmk_id, $weighted_score);
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

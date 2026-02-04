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
			'status_kegiatan' => $this->request->getGet('status_kegiatan'),
			'jenis_kegiatan' => $this->request->getGet('jenis_kegiatan')
		];

		$kegiatan = $this->mbkmModel->getKegiatanLengkap($filters);

		// Group by status
		$kegiatan_by_status = [];
		foreach ($kegiatan as $k) {
			$status = $k['status_kegiatan'] ?? 'diajukan';
			$kegiatan_by_status[$status][] = $k;
		}

		// Get jenis kegiatan for filter
		$jenis_kegiatan = $this->db->table('mbkm_jenis_kegiatan')
			->where('status', 'aktif')
			->get()
			->getResultArray();

		$data = [
			'kegiatan_by_status' => $kegiatan_by_status,
			'jenis_kegiatan' => $jenis_kegiatan,
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

		// Get CPL and CPMK lists
		$cpl_list = $this->db->table('cpl')
			->orderBy('kode_cpl', 'ASC')
			->get()
			->getResultArray();

		$cpmk_list = $this->db->table('cpmk')
			->orderBy('kode_cpmk', 'ASC')
			->get()
			->getResultArray();

		$data = [
			'mahasiswa' => $mahasiswa,
			'dosen' => $dosen,
			'cpl_list' => $cpl_list,
			'cpmk_list' => $cpmk_list
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
			'mahasiswa_ids' => 'required',
			'jenis_kegiatan' => 'required|min_length[3]|max_length[100]',
			'judul_kegiatan' => 'required|min_length[5]|max_length[255]',
			'tempat_kegiatan' => 'required|max_length[255]',
			'tanggal_mulai' => 'required|valid_date',
			'tanggal_selesai' => 'required|valid_date',
			'tahun_akademik' => 'required|max_length[20]',
			'sks_dikonversi' => 'required|integer|greater_than[0]',
			'nilai_type' => 'required|in_list[cpmk,cpl]'
		];

		if (!$this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $validation->getErrors());
		}

		// Validate CPL/CPMK selection
		$nilai_type = $this->request->getPost('nilai_type');
		$cpmk_id = $this->request->getPost('cpmk_id');
		$cpl_id = $this->request->getPost('cpl_id');

		if ($nilai_type === 'cpmk' && empty($cpmk_id)) {
			return redirect()->back()->withInput()->with('error', 'Pilih CPMK yang akan dicapai');
		}

		if ($nilai_type === 'cpl' && empty($cpl_id)) {
			return redirect()->back()->withInput()->with('error', 'Pilih CPL yang akan dicapai');
		}

		// Calculate duration in weeks
		$tanggal_mulai = strtotime($this->request->getPost('tanggal_mulai'));
		$tanggal_selesai = strtotime($this->request->getPost('tanggal_selesai'));
		$durasi_minggu = ceil(($tanggal_selesai - $tanggal_mulai) / (60 * 60 * 24 * 7));

		// Get mahasiswa IDs array
		$mahasiswa_ids = $this->request->getPost('mahasiswa_ids');

		if (empty($mahasiswa_ids) || !is_array($mahasiswa_ids)) {
			return redirect()->back()->withInput()->with('error', 'Pilih minimal satu mahasiswa');
		}

		// Prepare kegiatan data
		$data = [
			'jenis_kegiatan' => $this->request->getPost('jenis_kegiatan'),
			'judul_kegiatan' => $this->request->getPost('judul_kegiatan'),
			'tempat_kegiatan' => $this->request->getPost('tempat_kegiatan'),
			'pembimbing_lapangan' => $this->request->getPost('pembimbing_lapangan'),
			'kontak_pembimbing' => $this->request->getPost('kontak_pembimbing'),
			'dosen_pembimbing_id' => $this->request->getPost('dosen_pembimbing_id') ?: null,
			'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
			'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
			'durasi_minggu' => $durasi_minggu,
			'sks_dikonversi' => $this->request->getPost('sks_dikonversi'),
			'deskripsi_kegiatan' => $this->request->getPost('deskripsi_kegiatan'),
			'status_kegiatan' => 'berlangsung',
			'tahun_akademik' => $this->request->getPost('tahun_akademik'),
			'nilai_type' => $nilai_type,
			'cpmk_id' => $nilai_type === 'cpmk' ? $cpmk_id : null,
			'cpl_id' => $nilai_type === 'cpl' ? $cpl_id : null
		];

		$this->db->transBegin();

		// Insert kegiatan
		if (!$this->mbkmModel->insert($data)) {
			$this->db->transRollback();
			return redirect()->back()->withInput()->with('error', 'Gagal menambahkan kegiatan MBKM');
		}

		$kegiatan_id = $this->mbkmModel->getInsertID();

		// Insert mahasiswa relationships
		foreach ($mahasiswa_ids as $mahasiswa_id) {
			$relasi_data = [
				'kegiatan_id' => $kegiatan_id,
				'mahasiswa_id' => $mahasiswa_id,
				'peran' => 'Peserta'
			];

			$this->db->table('mbkm_kegiatan_mahasiswa')->insert($relasi_data);
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

		// Get students associated with this activity
		$kegiatan_mahasiswa = $this->db->table('mbkm_kegiatan_mahasiswa km')
			->select('km.mahasiswa_id, m.nim, m.nama_lengkap, m.program_studi_kode')
			->join('mahasiswa m', 'm.id = km.mahasiswa_id')
			->where('km.kegiatan_id', $id)
			->get()
			->getResultArray();

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

		// Get CPL and CPMK lists
		$cpl_list = $this->db->table('cpl')
			->orderBy('kode_cpl', 'ASC')
			->get()
			->getResultArray();

		$cpmk_list = $this->db->table('cpmk')
			->orderBy('kode_cpmk', 'ASC')
			->get()
			->getResultArray();

		// Get nilai if exists
		$nilai = $this->db->table('mbkm_nilai_akhir')
			->where('kegiatan_id', $id)
			->get()
			->getRowArray();

		if ($nilai) {
			$kegiatan['nilai_angka'] = $nilai['nilai_angka'];
			$kegiatan['nilai_huruf'] = $nilai['nilai_huruf'];
			$kegiatan['status_kelulusan'] = $nilai['status_kelulusan'];
		} else {
			$kegiatan['nilai_angka'] = null;
			$kegiatan['nilai_huruf'] = null;
			$kegiatan['status_kelulusan'] = null;
		}

		$data = [
			'kegiatan' => $kegiatan,
			'kegiatan_mahasiswa' => $kegiatan_mahasiswa,
			'mahasiswa' => $mahasiswa,
			'dosen' => $dosen,
			'cpl_list' => $cpl_list,
			'cpmk_list' => $cpmk_list
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
			'mahasiswa_ids' => 'required',
			'jenis_kegiatan' => 'required|min_length[3]|max_length[100]',
			'judul_kegiatan' => 'required|min_length[5]|max_length[255]',
			'tempat_kegiatan' => 'required|max_length[255]',
			'tanggal_mulai' => 'required|valid_date',
			'tanggal_selesai' => 'required|valid_date',
			'tahun_akademik' => 'required|max_length[20]',
			'sks_dikonversi' => 'required|integer|greater_than[0]',
			'nilai_type' => 'required|in_list[cpmk,cpl]'
		];

		if (!$this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $validation->getErrors());
		}

		// Validate CPL/CPMK selection
		$nilai_type = $this->request->getPost('nilai_type');
		$cpmk_id = $this->request->getPost('cpmk_id');
		$cpl_id = $this->request->getPost('cpl_id');

		if ($nilai_type === 'cpmk' && empty($cpmk_id)) {
			return redirect()->back()->withInput()->with('error', 'Pilih CPMK yang akan dicapai');
		}

		if ($nilai_type === 'cpl' && empty($cpl_id)) {
			return redirect()->back()->withInput()->with('error', 'Pilih CPL yang akan dicapai');
		}

		// Calculate duration in weeks
		$tanggal_mulai = strtotime($this->request->getPost('tanggal_mulai'));
		$tanggal_selesai = strtotime($this->request->getPost('tanggal_selesai'));
		$durasi_minggu = ceil(($tanggal_selesai - $tanggal_mulai) / (60 * 60 * 24 * 7));

		// Get mahasiswa IDs array
		$mahasiswa_ids = $this->request->getPost('mahasiswa_ids');

		if (empty($mahasiswa_ids) || !is_array($mahasiswa_ids)) {
			return redirect()->back()->withInput()->with('error', 'Pilih minimal satu mahasiswa');
		}

		$this->db->transStart(); // Start transaction

		$data = [
			'jenis_kegiatan' => $this->request->getPost('jenis_kegiatan'),
			'judul_kegiatan' => $this->request->getPost('judul_kegiatan'),
			'tempat_kegiatan' => $this->request->getPost('tempat_kegiatan'),
			'pembimbing_lapangan' => $this->request->getPost('pembimbing_lapangan'),
			'kontak_pembimbing' => $this->request->getPost('kontak_pembimbing'),
			'dosen_pembimbing_id' => $this->request->getPost('dosen_pembimbing_id') ?: null,
			'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
			'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
			'durasi_minggu' => $durasi_minggu,
			'sks_dikonversi' => $this->request->getPost('sks_dikonversi'),
			'deskripsi_kegiatan' => $this->request->getPost('deskripsi_kegiatan'),
			'status_kegiatan' => $this->request->getPost('status_kegiatan'),
			'tahun_akademik' => $this->request->getPost('tahun_akademik'),
			'nilai_type' => $nilai_type,
			'cpmk_id' => $nilai_type === 'cpmk' ? $cpmk_id : null,
			'cpl_id' => $nilai_type === 'cpl' ? $cpl_id : null
		];

		// Update kegiatan
		if (!$this->mbkmModel->update($id, $data)) {
			$this->db->transRollback();
			return redirect()->back()->withInput()->with('error', 'Gagal memperbarui kegiatan MBKM');
		}

		// Delete existing mahasiswa relationships
		$this->db->table('mbkm_kegiatan_mahasiswa')
			->where('kegiatan_id', $id)
			->delete();

		// Insert new mahasiswa relationships
		foreach ($mahasiswa_ids as $mahasiswa_id) {
			$relasi_data = [
				'kegiatan_id' => $id,
				'mahasiswa_id' => $mahasiswa_id,
				'peran' => 'Peserta'
			];

			$this->db->table('mbkm_kegiatan_mahasiswa')->insert($relasi_data);
		}

		$this->db->transComplete(); // Complete transaction

		if ($this->db->transStatus() === false) {
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

		// Get kegiatan with full details including CPL/CPMK info
		$kegiatan = $this->db->table('mbkm_kegiatan k')
			->select('k.*, cpmk.kode_cpmk, cpmk.deskripsi as cpmk_deskripsi, cpl.kode_cpl, cpl.deskripsi as cpl_deskripsi')
			->join('cpmk', 'cpmk.id = k.cpmk_id', 'left')
			->join('cpl', 'cpl.id = k.cpl_id', 'left')
			->where('k.id', $kegiatan_id)
			->get()
			->getRowArray();

		if (!$kegiatan) {
			return redirect()->to('/admin/mbkm')->with('error', 'Kegiatan tidak ditemukan');
		}

		// Get mahasiswa for this kegiatan
		$mahasiswa = $this->db->table('mbkm_kegiatan_mahasiswa km')
			->select('m.id, m.nim, m.nama_lengkap, m.program_studi_kode')
			->join('mahasiswa m', 'm.id = km.mahasiswa_id')
			->where('km.kegiatan_id', $kegiatan_id)
			->get()
			->getResultArray();

		// Get existing nilai_akhir if exists
		$nilai_akhir = $this->db->table('mbkm_nilai_akhir')
			->where('kegiatan_id', $kegiatan_id)
			->get()
			->getRowArray();

		// Get grade configuration for dynamic grading
		$gradeConfigModel = new \App\Models\GradeConfigModel();
		$grade_config = $gradeConfigModel->getActiveGrades();

		$data = [
			'kegiatan' => $kegiatan,
			'mahasiswa' => $mahasiswa,
			'nilai_akhir' => $nilai_akhir,
			'grade_config' => $grade_config
		];

		return view('admin/mbkm/input_nilai', $data);
	}

	// Save nilai (Admin Only)
	public function saveNilai($kegiatan_id)
	{
		if (!$this->isAdmin()) {
			return $this->unauthorizedAccess();
		}

		// Get kegiatan to get CPL/CPMK info
		$kegiatan = $this->mbkmModel->find($kegiatan_id);
		if (!$kegiatan) {
			return redirect()->to('/admin/mbkm')->with('error', 'Kegiatan tidak ditemukan');
		}

		$nilai_angka = $this->request->getPost('nilai_angka');
		$catatan = $this->request->getPost('catatan');

		// Validate nilai_angka
		if ($nilai_angka === null || $nilai_angka === '' || $nilai_angka < 0 || $nilai_angka > 100) {
			return redirect()->back()->withInput()->with('error', 'Nilai harus antara 0-100');
		}

		$this->db->transStart();

		// Save final score using CPL/CPMK from kegiatan
		$this->mbkmModel->simpanNilaiAkhirWithCapaian(
			$kegiatan_id,
			$nilai_angka,
			$kegiatan['nilai_type'],
			$kegiatan['cpmk_id'],
			$kegiatan['cpl_id'],
			$catatan
		);

		// Update status kegiatan to selesai
		$this->mbkmModel->update($kegiatan_id, ['status_kegiatan' => 'selesai']);

		$this->db->transComplete();

		if ($this->db->transStatus() === false) {
			return redirect()->back()->with('error', 'Gagal menyimpan nilai');
		}

		return redirect()->to('/admin/mbkm')->with('success', 'Nilai berhasil disimpan');
	}

	// Detail nilai (AJAX) - Accessible to all authenticated users
	public function detailNilai($kegiatan_id)
	{
		if (!$this->request->isAJAX()) {
			return $this->response->setStatusCode(403)->setJSON(['error' => 'Invalid request']);
		}

		// Get kegiatan with full details including CPL/CPMK from kegiatan table
		$kegiatan = $this->db->table('mbkm_kegiatan k')
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

		// Get mahasiswa for this kegiatan
		$mahasiswa_list = $this->db->table('mbkm_kegiatan_mahasiswa km')
			->select('m.nim, m.nama_lengkap, m.program_studi_kode')
			->join('mahasiswa m', 'm.id = km.mahasiswa_id')
			->where('km.kegiatan_id', $kegiatan_id)
			->get()
			->getResultArray();

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
			'mahasiswa_list' => $mahasiswa_list
		]);
	}
}

<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TahunAkademikModel;

class TahunAkademik extends BaseController
{
	private function requireAdmin()
	{
		if (session()->get('role') !== 'admin') {
			return redirect()->to('/admin/tahun-akademik')->with('error', 'Anda tidak memiliki hak akses.');
		}
		return null;
	}

	public function index()
	{
		if (session()->get('role') !== 'admin') {
			return redirect()->to('/admin/dashboard')->with('error', 'Anda tidak memiliki hak akses ke menu ini.');
		}

		$model = new TahunAkademikModel();

		$data = [
			'title'          => 'Manajemen Tahun Akademik',
			'tahun_akademik' => $model->getAllForDisplay(),
		];

		return view('admin/tahun-akademik/index', $data);
	}

	public function create()
	{
		if ($redirect = $this->requireAdmin()) return $redirect;

		$data['title'] = 'Tambah Tahun Akademik';
		return view('admin/tahun-akademik/create', $data);
	}

	public function store()
	{
		if ($redirect = $this->requireAdmin()) return $redirect;

		$model = new TahunAkademikModel();

		$tahun    = trim($this->request->getPost('tahun'));
		$semester = $this->request->getPost('semester');

		// Validate tahun format YYYY/YYYY
		if (!preg_match('/^\d{4}$/', $tahun)) {
			return redirect()->back()->withInput()->with('error', 'Format tahun tidak valid. Gunakan format YYYY (contoh: 2025).');
		}

		// Validate semester
		if (!in_array($semester, ['Ganjil', 'Genap', 'Antara'])) {
			return redirect()->back()->withInput()->with('error', 'Semester harus Ganjil, Genap, atau Antara.');
		}

		// Check duplicate
		$existing = $model->where('tahun', $tahun)->where('semester', $semester)->first();
		if ($existing) {
			return redirect()->back()->withInput()->with('error', "Tahun akademik {$tahun} {$semester} sudah ada.");
		}

		$model->save([
			'tahun'     => $tahun,
			'semester'  => $semester,
			'is_active' => 1,
		]);

		return redirect()->to('/admin/tahun-akademik')->with('success', "Tahun akademik {$tahun} {$semester} berhasil ditambahkan.");
	}

	public function edit($id)
	{
		if ($redirect = $this->requireAdmin()) return $redirect;

		$model = new TahunAkademikModel();
		$row   = $model->find($id);

		if (empty($row)) {
			throw new \CodeIgniter\Exceptions\PageNotFoundException('Tahun akademik tidak ditemukan.');
		}

		$data = [
			'title'          => 'Edit Tahun Akademik',
			'tahun_akademik' => $row,
		];

		return view('admin/tahun-akademik/edit', $data);
	}

	public function update($id)
	{
		if ($redirect = $this->requireAdmin()) return $redirect;

		$model = new TahunAkademikModel();
		$row   = $model->find($id);

		if (empty($row)) {
			throw new \CodeIgniter\Exceptions\PageNotFoundException('Tahun akademik tidak ditemukan.');
		}

		$tahun    = trim($this->request->getPost('tahun'));
		$semester = $this->request->getPost('semester');

		if (!preg_match('/^\d{4}$/', $tahun)) {
			return redirect()->back()->withInput()->with('error', 'Format tahun tidak valid. Gunakan format YYYY (contoh: 2025).');
		}

		if (!in_array($semester, ['Ganjil', 'Genap', 'Antara'])) {
			return redirect()->back()->withInput()->with('error', 'Semester harus Ganjil, Genap, atau Antara.');
		}

		// Check duplicate (exclude current)
		$existing = $model->where('tahun', $tahun)->where('semester', $semester)->where('id !=', $id)->first();
		if ($existing) {
			return redirect()->back()->withInput()->with('error', "Tahun akademik {$tahun} {$semester} sudah ada.");
		}

	$model->update($id, [
			'tahun'     => $tahun,
			'semester'  => $semester,
			'is_active' => $this->request->getPost('is_active') ? 1 : 0,
		]);

		return redirect()->to('/admin/tahun-akademik')->with('success', "Tahun akademik {$tahun} {$semester} berhasil diperbarui.");
	}

	public function delete($id)
	{
		if ($redirect = $this->requireAdmin()) return $redirect;

		$model = new TahunAkademikModel();

		if ($model->delete($id)) {
			return redirect()->to('/admin/tahun-akademik')->with('success', 'Tahun akademik berhasil dihapus.');
		}

		return redirect()->to('/admin/tahun-akademik')->with('error', 'Gagal menghapus tahun akademik.');
	}

	public function toggle($id)
	{
		if ($redirect = $this->requireAdmin()) return $redirect;

		$model = new TahunAkademikModel();
		$row   = $model->find($id);

		if ($row) {
			$newStatus  = $row['is_active'] ? 0 : 1;
			$statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
			$model->update($id, ['is_active' => $newStatus]);
			return redirect()->to('/admin/tahun-akademik')->with('success', "Tahun akademik berhasil {$statusText}.");
		}

		return redirect()->to('/admin/tahun-akademik')->with('error', 'Tahun akademik tidak ditemukan.');
	}
}

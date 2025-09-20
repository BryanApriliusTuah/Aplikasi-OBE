<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MahasiswaModel;

class Mahasiswa extends BaseController
{
	protected $mahasiswaModel;

	public function __construct()
	{
		// Load the model in the constructor
		$this->mahasiswaModel = new MahasiswaModel();
	}

	/**
	 * Display a list of all students.
	 */
	public function index()
	{
		$data = [
			'title'     => 'Master Data Mahasiswa',
			'mahasiswa' => $this->mahasiswaModel->orderBy('nim', 'ASC')->findAll()
		];

		// The view path assumes your 'index.php' is in 'app/Views/admin/mahasiswa/'
		return view('admin/mahasiswa/index', $data);
	}

	/**
	 * Show the form for creating a new student.
	 */
	public function create()
	{
		$data = [
			'title' => 'Tambah Data Mahasiswa'
		];
		return view('admin/mahasiswa/create', $data);
	}

	/**
	 * Store a new student record using MANUAL VALIDATION.
	 */
	public function store()
	{
		$errors = [];
		$data = [
			'nim' => $this->request->getPost('nim'),
			'nama_lengkap' => $this->request->getPost('nama_lengkap'),
			'program_studi' => $this->request->getPost('program_studi'),
			'tahun_angkatan' => $this->request->getPost('tahun_angkatan'),
			'status_mahasiswa' => $this->request->getPost('status_mahasiswa'),
		];

		//--- Start Manual Validation ---
		if (empty($data['nim'])) {
			$errors['nim'] = 'NIM wajib diisi.';
		} else {
			$exists = $this->mahasiswaModel->where('nim', $data['nim'])->first();
			if ($exists) {
				$errors['nim'] = 'NIM sudah terdaftar. Silakan gunakan NIM lain.';
			}
		}

		if (empty($data['nama_lengkap'])) {
			$errors['nama_lengkap'] = 'Nama Lengkap wajib diisi.';
		}

		if (empty($data['program_studi'])) {
			$errors['program_studi'] = 'Program Studi wajib dipilih.';
		}

		if (empty($data['tahun_angkatan'])) {
			$errors['tahun_angkatan'] = 'Tahun Angkatan wajib diisi.';
		} elseif (!is_numeric($data['tahun_angkatan']) || strlen($data['tahun_angkatan']) != 4) {
			$errors['tahun_angkatan'] = 'Tahun Angkatan harus berupa 4 digit angka.';
		}
		//--- End Manual Validation ---

		// If there are any errors, redirect back with the errors
		if (!empty($errors)) {
			return redirect()->back()->withInput()->with('errors', $errors);
		}

		// If validation passes, save the data
		$this->mahasiswaModel->save($data);

		// Set a success flash message and redirect
		session()->setFlashdata('success', 'Data mahasiswa berhasil ditambahkan.');
		return redirect()->to('admin/mahasiswa');
	}

	/**
	 * Show the form for editing a student.
	 * @param int $id The student ID
	 */
	public function edit($id)
	{
		$mahasiswaData = $this->mahasiswaModel->find($id);

		if (empty($mahasiswaData)) {
			throw new \CodeIgniter\Exceptions\PageNotFoundException('Data mahasiswa dengan ID ' . $id . ' tidak ditemukan.');
		}

		$data = [
			'title'     => 'Edit Data Mahasiswa',
			'mahasiswa' => $mahasiswaData
		];
		return view('admin/mahasiswa/edit', $data);
	}

	/**
	 * Update an existing student record using MANUAL VALIDATION.
	 * @param int $id The student ID
	 */
	public function update($id)
	{
		$errors = [];
		$data = [
			'nim' => $this->request->getPost('nim'),
			'nama_lengkap' => $this->request->getPost('nama_lengkap'),
			'program_studi' => $this->request->getPost('program_studi'),
			'tahun_angkatan' => $this->request->getPost('tahun_angkatan'),
			'status_mahasiswa' => $this->request->getPost('status_mahasiswa'),
		];

		//--- Start Manual Validation ---
		$originalMahasiswa = $this->mahasiswaModel->find($id);

		if (empty($data['nim'])) {
			$errors['nim'] = 'NIM wajib diisi.';
		} elseif ($data['nim'] !== $originalMahasiswa['nim']) {
			// Check for uniqueness only if the NIM has changed
			$exists = $this->mahasiswaModel->where('nim', $data['nim'])->first();
			if ($exists) {
				$errors['nim'] = 'NIM sudah terdaftar. Silakan gunakan NIM lain.';
			}
		}

		if (empty($data['nama_lengkap'])) {
			$errors['nama_lengkap'] = 'Nama Lengkap wajib diisi.';
		}

		if (empty($data['program_studi'])) {
			$errors['program_studi'] = 'Program Studi wajib dipilih.';
		}

		if (empty($data['tahun_angkatan'])) {
			$errors['tahun_angkatan'] = 'Tahun Angkatan wajib diisi.';
		} elseif (!is_numeric($data['tahun_angkatan']) || strlen($data['tahun_angkatan']) != 4) {
			$errors['tahun_angkatan'] = 'Tahun Angkatan harus berupa 4 digit angka.';
		}
		//--- End Manual Validation ---

		// If there are any errors, redirect back with the errors
		if (!empty($errors)) {
			return redirect()->back()->withInput()->with('errors', $errors);
		}

		// If validation passes, update the data
		$this->mahasiswaModel->update($id, $data);

		// Set a success flash message and redirect
		session()->setFlashdata('success', 'Data mahasiswa berhasil diperbarui.');
		return redirect()->to('admin/mahasiswa');
	}

	/**
	 * Delete a student record.
	 * @param int $id The student ID
	 */
	public function delete($id)
	{
		$mahasiswaData = $this->mahasiswaModel->find($id);

		if (empty($mahasiswaData)) {
			// Set a flash message for the error
			session()->setFlashdata('error', 'Data mahasiswa tidak ditemukan.');
			return redirect()->to('admin/mahasiswa');
		}

		$this->mahasiswaModel->delete($id);

		// Set a flash message for the session
		session()->setFlashdata('success', 'Data mahasiswa berhasil dihapus.');

		return redirect()->to('admin/mahasiswa');
	}
}

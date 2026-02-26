<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MahasiswaModel;
use App\Models\ProgramStudiModel;

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
		$filters = [
			'status_mahasiswa' => $this->request->getGet('status_mahasiswa'),
			'tahun_angkatan'   => $this->request->getGet('tahun_angkatan'),
			'search'           => $this->request->getGet('search'),
		];

		$builder = $this->mahasiswaModel
			->select('mahasiswa.*, program_studi.nama_resmi as program_studi')
			->join('program_studi', 'program_studi.kode = mahasiswa.program_studi_kode', 'left')
			->orderBy('mahasiswa.nim', 'ASC');

		if (!empty($filters['status_mahasiswa'])) {
			$builder->where('mahasiswa.status_mahasiswa', $filters['status_mahasiswa']);
		}

		if (!empty($filters['tahun_angkatan'])) {
			$builder->where('mahasiswa.tahun_angkatan', $filters['tahun_angkatan']);
		}

		if (!empty($filters['search'])) {
			$search = $filters['search'];
			$builder->groupStart()
				->like('mahasiswa.nim', $search)
				->orLike('mahasiswa.nama_lengkap', $search)
				->orLike('mahasiswa.email', $search)
				->groupEnd();
		}

		$data = [
			'title'     => 'Master Data Mahasiswa',
			'mahasiswa' => $builder->findAll(),
			'filters'   => $filters,
		];

		return view('admin/mahasiswa/index', $data);
	}

	public function syncFromApi()
	{
		if (session()->get('role') !== 'admin') {
			return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat melakukan sinkronisasi.');
		}

		$apiUrl = 'https://api.siuber.upr.ac.id/api/siuber/mahasiswa?prodiKode=58';
		$apiKey = 'XT)+KVdVT]Z]1-p8<tIz/H0W5}_z%@KS';

		$client = \Config\Services::curlrequest();

		try {
			$model = new MahasiswaModel();
			$programStudiModel = new ProgramStudiModel();
			$inserted = 0;
			$updated = 0;
			$skipped = 0;

			// Pre-fetch all existing mahasiswa NIMs with their IDs in one query
			$existingRows = $model->select('id, nim')->findAll();
			$existingNims = [];
			foreach ($existingRows as $row) {
				$existingNims[$row['nim']] = $row['id'];
			}

			// Cache program studi lookup
			$prodiExists = (bool) $programStudiModel->find(58);

			$response = $client->request('GET', $apiUrl, [
				'headers' => [
					'x-api-key' => $apiKey,
					'Accept'    => 'application/json',
				],
				'timeout' => 120,
			]);

			$body = json_decode($response->getBody(), true);
			$items = $body['data'] ?? null;

			if (!is_array($items) || empty($items)) {
				return redirect()->back()->with('error', 'Tidak ada data mahasiswa yang diterima dari API.');
			}

			$insertBatch = [];
			$updateBatch = [];

			foreach ($items as $item) {
				$prodiKode = $item['prodiKode'] ?? null;

				if ($prodiKode && !$prodiExists) {
					$skipped++;
					continue;
				}

				$nim = $item['mhsNiu'] ?? null;
				$nama = $item['mhsNama'] ?? null;

				if (!$nim || !$nama) {
					continue;
				}

				$jenisKelamin = $item['mhsJenisKelamin'] ?? null;
				if ($jenisKelamin && !in_array($jenisKelamin, ['L', 'P'])) {
					$jenisKelamin = null;
				}

				$data = [
					'nim'                => $nim,
					'nama_lengkap'       => $nama,
					'jenis_kelamin'      => $jenisKelamin,
					'email'              => $item['mhsEmail'] ?? null,
					'no_hp'              => $item['mhsNoHp'] ?? null,
					'program_studi_kode' => $prodiKode,
					'tahun_angkatan'     => (string)($item['mhsAngkatan'] ?? ''),
					'status_mahasiswa'   => 'Aktif',
				];

				if (isset($existingNims[$nim])) {
					// Skip existing data to avoid timeout
					$data['id'] = $existingNims[$nim];
					$updateBatch[] = $data;
					// $skipped++;
					// continue;
				} else {
					$insertBatch[] = $data;
					// Track newly inserted NIMs to avoid duplicates in subsequent pages
					$existingNims[$nim] = true;
				}
			}

			if (!empty($insertBatch)) {
				$model->insertBatch($insertBatch);
				$inserted += count($insertBatch);
			}

			if (!empty($updateBatch)) {
				$model->updateBatch($updateBatch, 'id');
				$updated += count($updateBatch);
			}

			$message = "Sinkronisasi berhasil! $inserted data baru ditambahkan, $updated data diperbarui.";
			if ($skipped > 0) {
				$message .= " $skipped data dilewati.";
			}

			return redirect()->back()->with('success', $message);
		} catch (\Exception $e) {
			return redirect()->back()->with('error', 'Gagal mengambil data dari API: ' . $e->getMessage());
		}
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
			'jenis_kelamin' => $this->request->getPost('jenis_kelamin') ?: null,
			'email' => $this->request->getPost('email'),
			'no_hp' => $this->request->getPost('no_hp'),
			'program_studi_kode' => $this->request->getPost('program_studi_kode') ?: null,
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

		$programStudiModel = new ProgramStudiModel();

		$data = [
			'title'        => 'Edit Data Mahasiswa',
			'mahasiswa'    => $mahasiswaData,
			'programStudi' => $programStudiModel->orderBy('nama_resmi', 'ASC')->findAll(),
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
			'jenis_kelamin' => $this->request->getPost('jenis_kelamin') ?: null,
			'email' => $this->request->getPost('email'),
			'no_hp' => $this->request->getPost('no_hp'),
			'program_studi_kode' => $this->request->getPost('program_studi_kode') ?: null,
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

<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DosenModel;

class Dosen extends BaseController
{
	public function index()
	{
		$model = new DosenModel();

		$filters = [
			'status_keaktifan' => $this->request->getGet('status_keaktifan'),
			'search'           => $this->request->getGet('search'),
		];

		$builder = $model->orderBy('nama_lengkap', 'ASC');

		if (!empty($filters['status_keaktifan'])) {
			$builder->where('status_keaktifan', $filters['status_keaktifan']);
		}

		if (!empty($filters['search'])) {
			$search = $filters['search'];
			$builder->groupStart()
				->like('nip', $search)
				->orLike('nama_lengkap', $search)
				->orLike('email', $search)
				->groupEnd();
		}

		$data['dosen']   = $builder->findAll();
		$data['filters'] = $filters;

		return view('admin/dosen/index', $data);
	}

	public function create()
	{
		if (session()->get('role') !== 'admin') {
			return redirect()->to('/admin/dosen')->with('error', 'Anda tidak memiliki hak akses.');
		}

		return view('admin/dosen/create');
	}

	public function store()
	{
		if (session()->get('role') !== 'admin') {
			return redirect()->to('/admin/dosen')->with('error', 'Anda tidak memiliki hak akses.');
		}

		$model = new DosenModel();

		$jabatan = $this->request->getPost('jabatan_fungsional');
		$jabatan_string = is_array($jabatan) ? implode(', ', $jabatan) : '';

		$data = [
			'nip'                   => $this->request->getPost('nip'),
			'nama_lengkap'          => $this->request->getPost('nama_lengkap'),
			'gelar_depan'           => $this->request->getPost('gelar_depan'),
			'gelar_belakang'        => $this->request->getPost('gelar_belakang'),
			'email'                 => $this->request->getPost('email'),
			'no_hp'                 => $this->request->getPost('no_hp'),
			'jabatan_fungsional'    => $jabatan_string,
			'status_dosen'          => $this->request->getPost('status_dosen'),
			'status_keaktifan'      => $this->request->getPost('status_keaktifan'),
			'fakultas_kode'         => $this->request->getPost('fakultas_kode') ?: null,
			'program_studi_kode'    => $this->request->getPost('program_studi_kode') ?: null,
		];

		$model->save($data);
		return redirect()->to('/admin/dosen')->with('success', 'Data dosen berhasil ditambahkan.');
	}

	public function edit($id)
	{
		if (session()->get('role') !== 'admin') {
			return redirect()->to('/admin/dosen')->with('error', 'Anda tidak memiliki hak akses.');
		}

		$model = new DosenModel();
		$dosenData = $model->find($id);

		if (empty($dosenData)) {
			throw new \CodeIgniter\Exceptions\PageNotFoundException('Data dosen tidak ditemukan.');
		}

		$data['dosen'] = $dosenData;
		$data['jabatan_terpilih'] = explode(', ', $dosenData['jabatan_fungsional']);

		return view('admin/dosen/edit', $data);
	}

	public function update($id)
	{
		if (session()->get('role') !== 'admin') {
			return redirect()->to('/admin/dosen')->with('error', 'Anda tidak memiliki hak akses.');
		}

		$model = new DosenModel();

		$jabatan = $this->request->getPost('jabatan_fungsional');
		$jabatan_string = is_array($jabatan) ? implode(', ', $jabatan) : '';

		$data = [
			'nip'                   => $this->request->getPost('nip'),
			'nama_lengkap'          => $this->request->getPost('nama_lengkap'),
			'gelar_depan'           => $this->request->getPost('gelar_depan'),
			'gelar_belakang'        => $this->request->getPost('gelar_belakang'),
			'email'                 => $this->request->getPost('email'),
			'no_hp'                 => $this->request->getPost('no_hp'),
			'jabatan_fungsional'    => $jabatan_string,
			'status_dosen'          => $this->request->getPost('status_dosen'),
			'status_keaktifan'      => $this->request->getPost('status_keaktifan'),
			'fakultas_kode'         => $this->request->getPost('fakultas_kode') ?: null,
			'program_studi_kode'    => $this->request->getPost('program_studi_kode') ?: null,
		];

		$model->update($id, $data);
		return redirect()->to('/admin/dosen')->with('success', 'Data dosen berhasil diperbarui.');
	}

	public function delete($id)
	{
		if (session()->get('role') !== 'admin') {
			return redirect()->to('/admin/dosen')->with('error', 'Anda tidak memiliki hak akses.');
		}

		$model = new DosenModel();
		$model->delete($id);
		return redirect()->to('/admin/dosen')->with('success', 'Data dosen berhasil dihapus.');
	}

	public function syncFromApi()
	{
		if (session()->get('role') !== 'admin') {
			return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat melakukan sinkronisasi.');
		}

		$apiUrl = 'https://tik.upr.ac.id/api/siuber/dosen';
		$apiKey = 'XT)+KVdVT]Z]1-p8<tIz/H0W5}_z%@KS';

		$client = \Config\Services::curlrequest();

		try {
			$response = $client->request('GET', $apiUrl, [
				'headers' => [
					'x-api-key' => $apiKey,
					'Accept'    => 'application/json',
				],
				'timeout' => 30,
			]);

			$body = json_decode($response->getBody(), true);
			$body = $body['data'] ?? null;

			if (!is_array($body)) {
				return redirect()->back()->with('error', 'Format response API tidak valid.');
			}

			$model = new DosenModel();
			$inserted = 0;
			$updated = 0;

			foreach ($body as $item) {
				if ($item['dosen_program_studi_kode'] == 58) {
					$nip = $item['dosen_nip'] ?? null;
					$nama = $item['dosen_nama'] ?? null;

					if (!$nip || !$nama) {
						continue;
					}

					$existing = $model->where('nip', $nip)->first();

					$data = [
						'nip'                => $nip,
						'nama_lengkap'       => $nama,
						'gelar_depan'        => $item['dosen_gelar_depan'] ?? null,
						'gelar_belakang'     => $item['dosen_gelar_belakang'] ?? null,
						'email'              => $item['dosen_email'] ?? null,
						'no_hp'              => $item['dosen_no_hp'] ?? null,
						'status_dosen'       => $item['dosen_status_dosen'] ?? null,
						'status_keaktifan'   => ($item['dosen_status_aktif'] ?? 0) == 1 ? 'Aktif' : 'Tidak Aktif',
						'fakultas_kode'      => $item['dosen_fakultas_kode'] ?? null,
						'program_studi_kode' => $item['dosen_program_studi_kode'] ?? null,
					];

					if ($existing) {
						// $model->update($existing['id'], $data);
						// $updated++;
						continue;
					} else {
						$model->insert($data);
						$inserted++;
					}
				}
			}

			return redirect()->back()->with('success', "Sinkronisasi berhasil! $inserted data baru ditambahkan, $updated data diperbarui.");
		} catch (\Exception $e) {
			return redirect()->back()->with('error', 'Gagal mengambil data dari API: ' . $e->getMessage());
		}
	}
}

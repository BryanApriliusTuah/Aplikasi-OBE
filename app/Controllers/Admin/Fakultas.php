<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FakultasModel;

class Fakultas extends BaseController
{
	public function index()
	{
		$model = new FakultasModel();

		$search = $this->request->getGet('search') ?? '';

		$builder = $model;
		if ($search) {
			$builder = $builder->groupStart()
				->like('nama_singkat', $search)
				->orLike('nama_resmi', $search)
				->orLike('nama_dekan', $search)
				->groupEnd();
		}

		$perPage = $this->request->getGet('perPage') ?? 10;
		$page    = $this->request->getGet('page') ?? 1;

		$allData    = $builder->findAll();
		$total      = count($allData);
		$totalPages = max(ceil($total / $perPage), 1);
		$offset     = ($page - 1) * $perPage;
		$paginated  = array_slice($allData, $offset, $perPage);

		$data = [
			'fakultas'   => $paginated,
			'total'      => $total,
			'perPage'    => $perPage,
			'page'       => $page,
			'totalPages' => $totalPages,
			'filters'    => [
				'search' => $search,
			],
		];

		return view('admin/fakultas/index', $data);
	}

	public function syncFromApi()
	{
		if (session('role') !== 'admin') {
			return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat melakukan sinkronisasi.');
		}

		$apiUrl = 'https://tik.upr.ac.id/api/siuber/fakultas';
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
				return redirect()->back()->with('error', 'Format response API fakultas tidak valid.');
			}

			$model = new FakultasModel();
			$inserted = 0;
			$updated = 0;

			foreach ($body as $item) {
				$kode = $item['fakultas_kode'] ?? null;

				if (!$kode) {
					continue;
				}

				$data = [
					'kode'         => $kode,
					'nama_singkat' => $item['fakultas_nama_singkat'] ?? null,
					'nama_resmi'   => $item['fakultas_nama_resmi'] ?? null,
					'telepon'      => $item['fakultas_telepon'] ?? null,
					'email'        => $item['fakultas_email'] ?? null,
					'nip_dekan'    => $item['fakultas_nip_dekan'] ?? null,
					'nama_dekan'   => $item['fakultas_nama_dekan'] ?? null,
				];

				$existing = $model->find($kode);

				if ($existing) {
					$model->update($kode, $data);
					$updated++;
				} else {
					$model->insert($data);
					$inserted++;
				}
			}

			return redirect()->back()->with('success', "Sinkronisasi fakultas berhasil! $inserted data baru ditambahkan, $updated data diperbarui.");
		} catch (\Exception $e) {
			return redirect()->back()->with('error', 'Gagal mengambil data fakultas dari API: ' . $e->getMessage());
		}
	}
}

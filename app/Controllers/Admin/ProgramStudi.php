<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProgramStudiModel;
use App\Models\FakultasModel;

class ProgramStudi extends BaseController
{
	public function index()
	{
		$model = new ProgramStudiModel();
		$fakultasModel = new FakultasModel();

		$search = $this->request->getGet('search') ?? '';
		$fakultasKode = $this->request->getGet('fakultas_kode') ?? '';

		$builder = $model->select('program_studi.*, fakultas.nama_resmi as fakultas_nama')
			->join('fakultas', 'fakultas.kode = program_studi.fakultas_kode', 'left');

		if ($search) {
			$builder = $builder->groupStart()
				->like('program_studi.nama_singkat', $search)
				->orLike('program_studi.nama_resmi', $search)
				->orLike('program_studi.nama_kaprodi', $search)
				->groupEnd();
		}

		if ($fakultasKode) {
			$builder = $builder->where('program_studi.fakultas_kode', $fakultasKode);
		}

		// Build fakultas options for filter dropdown
		$fakultasList = $fakultasModel->findAll();
		$fakultasOptions = ['' => 'Semua Fakultas'];
		foreach ($fakultasList as $f) {
			$fakultasOptions[$f['kode']] = $f['nama_resmi'];
		}

		$perPage = $this->request->getGet('perPage') ?? 10;
		$page    = $this->request->getGet('page') ?? 1;

		$allData    = $builder->findAll();
		$total      = count($allData);
		$totalPages = max(ceil($total / $perPage), 1);
		$offset     = ($page - 1) * $perPage;
		$paginated  = array_slice($allData, $offset, $perPage);

		$data = [
			'program_studi'    => $paginated,
			'total'            => $total,
			'perPage'          => $perPage,
			'page'             => $page,
			'totalPages'       => $totalPages,
			'fakultas_options' => $fakultasOptions,
			'filters'          => [
				'search'        => $search,
				'fakultas_kode' => $fakultasKode,
			],
		];

		return view('admin/program_studi/index', $data);
	}

	public function syncFromApi()
	{
		if (session('role') !== 'admin') {
			return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat melakukan sinkronisasi.');
		}

		$apiUrl = 'https://api.siuber.upr.ac.id/api/siuber/programstudi';
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
				return redirect()->back()->with('error', 'Format response API program studi tidak valid.');
			}

			$model = new ProgramStudiModel();
			$fakultasModel = new FakultasModel();
			$inserted = 0;
			$updated = 0;
			$skipped = 0;

			foreach ($body as $item) {
				$kode = $item['programstudi_kode'] ?? null;
				$fakultasKode = $item['fakultas_kode'] ?? null;

				if (!$kode || !$fakultasKode) {
					continue;
				}

				// Ensure the referenced fakultas exists
				if (!$fakultasModel->find($fakultasKode)) {
					$skipped++;
					continue;
				}

				$data = [
					'kode'          => $kode,
					'nama_singkat'  => $item['programstudi_nama_singkat'] ?? null,
					'nama_resmi'    => $item['programstudi_nama_resmi'] ?? null,
					'telepon'       => $item['programstudi_telepon'] ?? null,
					'email'         => $item['programstudi_email'] ?? null,
					'website'       => $item['programstudi_website'] ?? null,
					'nip_kaprodi'   => $item['programstudi_nip_kaprodi'] ?? null,
					'nama_kaprodi'  => $item['programstudi_nama_kaprodi'] ?? null,
					'fakultas_kode' => $fakultasKode,
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

			$message = "Sinkronisasi program studi berhasil! $inserted data baru ditambahkan, $updated data diperbarui.";
			if ($skipped > 0) {
				$message .= " $skipped data dilewati (fakultas belum tersinkronisasi).";
			}

			return redirect()->back()->with('success', $message);
		} catch (\Exception $e) {
			return redirect()->back()->with('error', 'Gagal mengambil data program studi dari API: ' . $e->getMessage());
		}
	}
}

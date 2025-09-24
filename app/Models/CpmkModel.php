<?php

namespace App\Models;

use CodeIgniter\Model;

class CpmkModel extends Model
{
	protected $table       = 'cpmk';
	protected $primaryKey  = 'id';
	protected $returnType  = 'array';

	protected $allowedFields = ['kode_cpmk', 'deskripsi'];

	// timestamps
	protected $useTimestamps = true;
	protected $createdField  = 'created_at';
	protected $updatedField  = 'updated_at';


	protected $validationRules = [
		'kode_cpmk' => 'required|min_length[3]|max_length[50]|is_unique[cpmk.kode_cpmk,id,{id}]',
		'deskripsi' => 'required|string'
	];

	protected $validationMessages = [
		'kode_cpmk' => [
			'required'  => 'Kode CPMK wajib diisi.',
			'is_unique' => 'Kode CPMK sudah ada.',
		],
		'deskripsi' => [
			'required' => 'Deskripsi CPMK wajib diisi.',
		],
	];

	public function getCpmkByJadwal(int $jadwal_id): array
	{
		return $this->select('cpmk.id, cpmk.kode_cpmk, cpmk.deskripsi')
			->join('cpmk_mk', 'cpmk_mk.cpmk_id = cpmk.id')
			->join('jadwal_mengajar', 'jadwal_mengajar.mata_kuliah_id = cpmk_mk.mata_kuliah_id')
			->where('jadwal_mengajar.id', $jadwal_id)
			->orderBy('cpmk.kode_cpmk', 'ASC')
			->findAll();
	}
}

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

	public function getCpmkByJadwal($jadwal_id)
	{
		$db = \Config\Database::connect();

		// Get mata_kuliah_id from jadwal
		$jadwal = $db->table('jadwal')
			->select('mata_kuliah_id')
			->where('id', $jadwal_id)
			->get()
			->getRowArray();

		if (!$jadwal) {
			return [];
		}

		// Get RPS for this mata kuliah
		$rps = $db->table('rps')
			->select('id')
			->where('mata_kuliah_id', $jadwal['mata_kuliah_id'])
			->orderBy('created_at', 'DESC')
			->get()
			->getRowArray();

		if (!$rps) {
			// Fallback: get CPMK without weights from cpmk_mk
			return $db->table('cpmk')
				->select('cpmk.id, cpmk.kode_cpmk, cpmk.deskripsi, 0 as bobot_cpmk')
				->join('cpmk_mk', 'cpmk_mk.cpmk_id = cpmk.id')
				->where('cpmk_mk.mata_kuliah_id', $jadwal['mata_kuliah_id'])
				->get()
				->getResultArray();
		}

		// Get CPMK with summed weights from rps_mingguan
		$query = "
            SELECT 
                c.id, 
                c.kode_cpmk, 
                c.deskripsi,
                COALESCE(SUM(rm.bobot), 0) as bobot_cpmk
            FROM cpmk c
            INNER JOIN cpmk_mk cm ON cm.cpmk_id = c.id
            LEFT JOIN rps_mingguan rm ON rm.cpmk_id = c.id AND rm.rps_id = ?
            WHERE cm.mata_kuliah_id = ?
            GROUP BY c.id, c.kode_cpmk, c.deskripsi
            ORDER BY c.kode_cpmk
        ";

		return $db->query($query, [$rps['id'], $jadwal['mata_kuliah_id']])
			->getResultArray();
	}
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class MengajarModel extends Model
{
	protected $table            = 'jadwal_mengajar';
	protected $primaryKey       = 'id';
	protected $useAutoIncrement = true;
	protected $returnType       = 'array';
	protected $useSoftDeletes   = false;
	protected $protectFields    = true;

	// Fields from the 'jadwal_mengajar' table that are allowed to be mass-assigned.
	protected $allowedFields    = [
		'mata_kuliah_id',
		'program_studi',
		'tahun_akademik',
		'kelas',
		'ruang',
		'hari',
		'jam_mulai',
		'jam_selesai',
		'status',
		'is_nilai_validated',
		'validated_at',
		'validated_by'
	];

	// Dates
	protected $useTimestamps = true;
	protected $createdField  = 'created_at';
	protected $updatedField  = 'updated_at';

	/**
	 * Retrieves filtered and paginated teaching schedules with related data.
	 * This is useful for the main schedule index page.
	 *
	 * @param array $filters An array of filters (e.g., program_studi, tahun_akademik).
	 * @return array An array containing the paginated data.
	 */
	public function getFilteredJadwal($filters = [])
	{
		$builder = $this->db->table($this->table . ' jm');
		$builder->select('
            jm.id,
            jm.program_studi,
            jm.tahun_akademik,
            jm.kelas,
            jm.ruang,
            jm.hari,
            jm.jam_mulai,
            jm.jam_selesai,
            mk.nama_mk,
            mk.sks,
            GROUP_CONCAT(d.nama_lengkap SEPARATOR ", ") as dosen_pengampu
        ');
		$builder->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id');
		$builder->join('jadwal_dosen jd', 'jd.jadwal_mengajar_id = jm.id', 'left');
		$builder->join('dosen d', 'd.id = jd.dosen_id', 'left');

		// Apply filters
		if (!empty($filters['program_studi'])) {
			$builder->where('jm.program_studi', $filters['program_studi']);
		}
		if (!empty($filters['tahun_akademik'])) {
			$builder->like('jm.tahun_akademik', $filters['tahun_akademik']);
		}

		$builder->groupBy('jm.id');
		$builder->orderBy('jm.tahun_akademik', 'DESC')->orderBy('mk.nama_mk', 'ASC');

		return [
			'jadwal' => $this->paginate(15), // Paginate with 15 items per page
			'pager'  => $this->pager,
		];
	}

	public function getJadwalWithDetails(array $filters = [], bool $singleResult = false)
	{
		// We can use the view you already have in your database dump.
		$builder = $this->db->table('view_jadwal_lengkap');

		if (!empty($filters['id'])) {
			$builder->where('id', $filters['id']);
		}
		if (!empty($filters['program_studi'])) {
			$builder->where('program_studi', $filters['program_studi']);
		}
		if (!empty($filters['tahun_akademik'])) {
			$builder->like('tahun_akademik', $filters['tahun_akademik'], 'both');
		}

		if ($singleResult) {
			return $builder->get()->getRowArray();
		}

		return $builder->get()->getResultArray();
	}
}

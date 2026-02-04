<?php

namespace App\Models;

use CodeIgniter\Model;

class MengajarModel extends Model
{
	protected $table            = 'jadwal';
	protected $primaryKey       = 'id';
	protected $useAutoIncrement = true;
	protected $returnType       = 'array';
	protected $useSoftDeletes   = false;
	protected $protectFields    = true;

	// Fields from the 'jadwal' table that are allowed to be mass-assigned.
	protected $allowedFields    = [
		'mata_kuliah_id',
		'program_studi_kode',
		'tahun_akademik',
		'kelas',
		'ruang',
		'hari',
		'jam_mulai',
		'jam_selesai',
		'status',
		'is_nilai_validated',
		'validated_at',
		'validated_by',
		'rubrik_penilaian_file',
		'contoh_soal_file',
		'notulen_rapat_file',
		'kelas_id',
		'kelas_jenis',
		'kelas_semester',
		'kelas_status',
		'mk_kurikulum_kode',
		'total_mahasiswa'
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
            jm.program_studi_kode,
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
		$builder->join('jadwal_dosen jd', 'jd.jadwal_id = jm.id', 'left');
		$builder->join('dosen d', 'd.id = jd.dosen_id', 'left');

		// Apply filters
		if (!empty($filters['program_studi_kode'])) {
			$builder->where('jm.program_studi_kode', $filters['program_studi_kode']);
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
		$builder = $this->db->table('view_jadwal_lengkap vjl');
		$builder->select('vjl.*, ps.nama_resmi as program_studi');
		$builder->join('program_studi ps', 'ps.kode = vjl.program_studi_kode', 'left');

		if (!empty($filters['id'])) {
			$builder->where('vjl.id', $filters['id']);
		}
		if (!empty($filters['program_studi_kode'])) {
			$builder->where('vjl.program_studi_kode', $filters['program_studi_kode']);
		}
		if (!empty($filters['tahun_akademik'])) {
			$builder->like('vjl.tahun_akademik', $filters['tahun_akademik'], 'both');
		}

		if ($singleResult) {
			$result = $builder->get()->getRowArray();
			if ($result) {
				$result = $this->attachDosenTeam($result);
			}
			return $result;
		}

		$results = $builder->get()->getResultArray();

		// Attach dosen team information to each jadwal
		foreach ($results as &$jadwal) {
			$jadwal = $this->attachDosenTeam($jadwal);
		}

		return $results;
	}

	/**
	 * Attach dosen team information (coordinator and members) to a jadwal record
	 */
	private function attachDosenTeam($jadwal)
	{
		if (!isset($jadwal['id'])) {
			return $jadwal;
		}

		// Get all dosen for this jadwal with their roles
		$dosenBuilder = $this->db->table('jadwal_dosen jd');
		$dosenBuilder->select('d.nama_lengkap, jd.role');
		$dosenBuilder->join('dosen d', 'd.id = jd.dosen_id');
		$dosenBuilder->where('jd.jadwal_id', $jadwal['id']);
		$dosenBuilder->orderBy('jd.role', 'DESC'); // coordinator/ketua first
		$dosenList = $dosenBuilder->get()->getResultArray();

		// Separate coordinator from members
		$koordinator = null;
		$anggota = [];

		foreach ($dosenList as $dosen) {
			// Check if this is the coordinator (role could be 'koordinator', 'ketua', etc.)
			if (in_array(strtolower($dosen['role']), ['koordinator', 'ketua', 'coordinator'])) {
				$koordinator = $dosen['nama_lengkap'];
			} else {
				$anggota[] = $dosen['nama_lengkap'];
			}
		}

		// If no coordinator found but there are dosen, use the first one as coordinator
		if (!$koordinator && !empty($dosenList)) {
			$koordinator = $dosenList[0]['nama_lengkap'];
			array_shift($dosenList); // Remove first from list
			$anggota = array_column($dosenList, 'nama_lengkap');
		}

		// Add to jadwal array
		$jadwal['dosen_ketua'] = $koordinator;
		$jadwal['dosen_anggota'] = $anggota; // Array of team member names

		return $jadwal;
	}
}

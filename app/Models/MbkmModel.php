<?php

namespace App\Models;

use CodeIgniter\Model;

class MbkmModel extends Model
{
	protected $table = 'mbkm';
	protected $primaryKey = 'id';
	protected $useAutoIncrement = true;
	protected $returnType = 'array';
	protected $useSoftDeletes = false;
	protected $allowedFields = [
		'nim',
		'program',
		'sub_program',
		'tujuan',
		'status_kegiatan',
		'semester'
	];
	protected $useTimestamps = true;
	protected $createdField = 'created_at';
	protected $updatedField = 'updated_at';

	/**
	 * Get all MBKM activities with complete information
	 */
	public function getKegiatanLengkap($filters = [])
	{
		$builder = $this->db->table('mbkm k')
			->select('k.*');

		// Apply filters
		if (!empty($filters['status_kegiatan'])) {
			$builder->where('k.status_kegiatan', $filters['status_kegiatan']);
		}

		$result = $builder->orderBy('k.created_at', 'DESC')->get()->getResultArray();

		// Enrich each record with mahasiswa data from NIM
		foreach ($result as &$row) {
			$nim_list = [];
			$nama_list = [];
			$fakultas = '';
			$prodi = '';

			if (!empty($row['nim'])) {
				$nims = array_map('trim', explode(',', $row['nim']));
				foreach ($nims as $nim) {
					if (empty($nim)) continue;

					$mahasiswa = $this->db->table('mahasiswa m')
						->select('m.nim, m.nama_lengkap, ps.nama_resmi as program_studi, f.nama_resmi as fakultas')
						->join('program_studi ps', 'ps.kode = m.program_studi_kode', 'left')
						->join('fakultas f', 'f.kode = ps.fakultas_kode', 'left')
						->where('m.nim', $nim)
						->get()
						->getRowArray();

					if ($mahasiswa) {
						$nim_list[] = $mahasiswa['nim'];
						$nama_list[] = $mahasiswa['nama_lengkap'];
						if (empty($fakultas) && !empty($mahasiswa['fakultas'])) {
							$fakultas = $mahasiswa['fakultas'];
						}
						if (empty($prodi) && !empty($mahasiswa['program_studi'])) {
							$prodi = $mahasiswa['program_studi'];
						}
					} else {
						$nim_list[] = $nim;
						$nama_list[] = '-';
					}
				}
			}

			$row['nim_list'] = implode(', ', $nim_list) ?: '-';
			$row['nama_mahasiswa_list'] = implode(', ', $nama_list) ?: '-';
			$row['fakultas'] = $fakultas ?: '-';
			$row['program_studi'] = $prodi ?: '-';
		}

		return $result;
	}


	/**
	 * Get konversi MK by mahasiswa list
	 */
	public function getKonversiMkByMahasiswaList($nim)
	{
		if (empty($nim)) {
			return [];
		}

		$nims = array_map('trim', explode(',', $nim));

		// First, get all mata kuliah for MBKM students
		$mk_list = $this->db->table('mata_kuliah mk')
			->select('mk.id as mata_kuliah_id, mk.kode_mk, mk.nama_mk, mk.sks as bobot_mk, j.id as jadwal_id')
			->join('jadwal j', 'mk.id = j.mata_kuliah_id', 'inner')
			->join('jadwal_mahasiswa jm', 'j.id = jm.jadwal_id', 'inner')
			->where('j.kelas', 'KM')
			->whereIn('jm.nim', $nims)
			->groupBy('mk.id, mk.kode_mk, mk.nama_mk, mk.sks, j.id')
			->get()
			->getResultArray();

		$grouped = [];

		foreach ($mk_list as $mk) {
			$mk_key = $mk['kode_mk'];

			// Get RPS for this mata kuliah
			$rps = $this->db->table('rps')
				->select('id')
				->where('mata_kuliah_id', $mk['mata_kuliah_id'])
				->orderBy('created_at', 'DESC')
				->get()
				->getRowArray();

			// Get CPMK with summed weights from rps_mingguan (same as CpmkModel)
			if ($rps) {
				$query = "
					SELECT
						c.id as cpmk_id,
						c.kode_cpmk,
						c.deskripsi as deskripsi_cpmk,
						COALESCE(SUM(rm.bobot), 0) as bobot
					FROM cpmk c
					INNER JOIN cpmk_mk cm ON cm.cpmk_id = c.id
					LEFT JOIN rps_mingguan rm ON rm.cpmk_id = c.id AND rm.rps_id = ?
					WHERE cm.mata_kuliah_id = ?
					GROUP BY c.id, c.kode_cpmk, c.deskripsi
					ORDER BY c.kode_cpmk
				";

				$cpmk_list = $this->db->query($query, [$rps['id'], $mk['mata_kuliah_id']])
					->getResultArray();
			} else {
				// Fallback: get CPMK without weights from cpmk_mk
				$cpmk_list = $this->db->table('cpmk c')
					->select('c.id as cpmk_id, c.kode_cpmk, c.deskripsi as deskripsi_cpmk, 0 as bobot')
					->join('cpmk_mk cm', 'cm.cpmk_id = c.id')
					->where('cm.mata_kuliah_id', $mk['mata_kuliah_id'])
					->orderBy('c.kode_cpmk')
					->get()
					->getResultArray();
			}

			if (!isset($grouped[$mk_key])) {
				$grouped[$mk_key] = [
					'kode_mk' => $mk['kode_mk'],
					'nama_mk' => $mk['nama_mk'],
					'bobot_mk' => $mk['bobot_mk'],
					'mata_kuliah_id' => $mk['mata_kuliah_id'],
					'cpmk_list' => $cpmk_list
				];
			}
		}

		return array_values($grouped);
	}

	/**
	 * Get mahasiswa list by kegiatan ID
	 */
	public function getMahasiswaByKegiatan($kegiatan_id)
	{
		$kegiatan = $this->find($kegiatan_id);
		if (!$kegiatan || empty($kegiatan['nim'])) {
			return [];
		}

		$nims = array_map('trim', explode(',', $kegiatan['nim']));

		$mahasiswa_list = $this->db->table('mahasiswa')
			->select('id, nim, nama_lengkap, program_studi_kode')
			->whereIn('nim', $nims)
			->get()
			->getResultArray();

		return $mahasiswa_list;
	}

	/**
	 * Save nilai CPMK for MBKM using nilai_cpmk_mahasiswa table
	 */
	public function saveNilaiCpmk($mahasiswa_id, $jadwal_id, $cpmk_id, $nilai)
	{
		$data = [
			'mahasiswa_id' => $mahasiswa_id,
			'jadwal_id' => $jadwal_id,
			'cpmk_id' => $cpmk_id,
			'nilai_cpmk' => $nilai
		];

		// Check if exists
		$existing = $this->db->table('nilai_cpmk_mahasiswa')
			->where([
				'mahasiswa_id' => $mahasiswa_id,
				'jadwal_id' => $jadwal_id,
				'cpmk_id' => $cpmk_id
			])
			->get()
			->getRowArray();

		if ($existing) {
			$this->db->table('nilai_cpmk_mahasiswa')
				->where('id', $existing['id'])
				->update(['nilai_cpmk' => $nilai, 'updated_at' => date('Y-m-d H:i:s')]);
		} else {
			$this->db->table('nilai_cpmk_mahasiswa')->insert($data);
		}

		return true;
	}

	/**
	 * Get existing CPMK scores for MBKM
	 */
	public function getNilaiCpmk($nim)
	{
		if (empty($nim)) {
			return [];
		}

		// Get mahasiswa_id
		$mahasiswa = $this->db->table('mahasiswa')
			->select('id')
			->where('nim', trim($nim))
			->get()
			->getRowArray();

		if (!$mahasiswa) {
			return [];
		}

		$mahasiswa_id = $mahasiswa['id'];

		// Get all jadwal with kelas = 'KM' for this mahasiswa
		$jadwal_list = $this->db->table('jadwal_mahasiswa jm')
			->select('jm.jadwal_id, j.mata_kuliah_id')
			->join('jadwal j', 'j.id = jm.jadwal_id')
			->where('jm.nim', trim($nim))
			->where('j.kelas', 'KM')
			->get()
			->getResultArray();

		if (empty($jadwal_list)) {
			return [];
		}

		$jadwal_ids = array_column($jadwal_list, 'jadwal_id');

		// Get scores
		$results = $this->db->table('nilai_cpmk_mahasiswa ncm')
			->select('ncm.*, j.mata_kuliah_id')
			->join('jadwal j', 'j.id = ncm.jadwal_id')
			->where('ncm.mahasiswa_id', $mahasiswa_id)
			->whereIn('ncm.jadwal_id', $jadwal_ids)
			->get()
			->getResultArray();

		$scores = [];
		foreach ($results as $row) {
			$scores[$row['mata_kuliah_id']][$row['cpmk_id']] = $row['nilai_cpmk'];
		}

		return $scores;
	}

}

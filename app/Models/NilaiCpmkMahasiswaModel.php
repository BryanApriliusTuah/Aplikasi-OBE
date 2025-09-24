<?php

namespace App\Models;

use CodeIgniter\Model;

class NilaiCpmkMahasiswaModel extends Model
{
	protected $table            = 'nilai_cpmk_mahasiswa';
	protected $primaryKey       = 'id';
	protected $useAutoIncrement = true;
	protected $returnType       = 'array';
	protected $useSoftDeletes   = false;
	protected $protectFields    = true;

	// Note: 'bobot_cpmk' is no longer here.
	protected $allowedFields    = [
		'mahasiswa_id',
		'jadwal_mengajar_id',
		'cpmk_id',
		'nilai_cpmk'
	];

	// Dates
	protected $useTimestamps = true;
	protected $createdField  = 'created_at';
	protected $updatedField  = 'updated_at';

	/**
	 * Gets CPMK scores grouped by student for the "Detail Nilai" modal.
	 *
	 * @param int $jadwal_id
	 * @return array
	 */
	public function getScoresByJadwal(int $jadwal_id): array
	{
		$results = $this->select('m.id as mahasiswa_id, m.nim, m.nama_lengkap, nc.cpmk_id, nc.nilai_cpmk')
			->from('mahasiswa m')
			->join('nilai_cpmk_mahasiswa nc', 'm.id = nc.mahasiswa_id', 'left')
			->where('nc.jadwal_mengajar_id', $jadwal_id)
			->where('m.status_mahasiswa', 'Aktif')
			->orderBy('m.nim', 'ASC')
			->get()->getResultArray();

		// Re-structure the data for easier display in the view
		$groupedScores = [];
		foreach ($results as $row) {
			$mahasiswa_id = $row['mahasiswa_id'];
			if (!isset($groupedScores[$mahasiswa_id])) {
				$groupedScores[$mahasiswa_id] = [
					'id' => $mahasiswa_id,
					'nim' => $row['nim'],
					'nama_lengkap' => $row['nama_lengkap'],
					'scores' => [],
				];
			}
			$groupedScores[$mahasiswa_id]['scores'][$row['cpmk_id']] = $row['nilai_cpmk'];
		}
		return array_values($groupedScores);
	}

	/**
	 * Gets existing scores in a format perfect for the input form.
	 *
	 * @param int $jadwal_id
	 * @return array
	 */
	public function getScoresByJadwalForInput(int $jadwal_id): array
	{
		$results = $this->where('jadwal_mengajar_id', $jadwal_id)->findAll();
		$scores = [];
		foreach ($results as $row) {
			$scores[$row['mahasiswa_id']][$row['cpmk_id']] = $row['nilai_cpmk'];
		}
		return $scores;
	}

	/**
	 * Inserts a new record or updates it if it already exists.
	 *
	 * @param array $data
	 * @return bool
	 */
	public function saveOrUpdate(array $data)
	{
		$existing = $this->where([
			'mahasiswa_id' => $data['mahasiswa_id'],
			'jadwal_mengajar_id' => $data['jadwal_mengajar_id'],
			'cpmk_id' => $data['cpmk_id']
		])->first();

		if ($existing) {
			return $this->update($existing['id'], ['nilai_cpmk' => $data['nilai_cpmk']]);
		} else {
			return $this->insert($data);
		}
	}
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class NilaiMahasiswaModel extends Model
{
	protected $table            = 'nilai_mahasiswa';
	protected $primaryKey       = 'id';
	protected $useAutoIncrement = true;
	protected $returnType       = 'array';
	protected $useSoftDeletes   = false;
	protected $protectFields    = true;

	// These are the fields from the 'nilai_mahasiswa' table that are allowed to be saved.
	protected $allowedFields    = [
		'mahasiswa_id',
		'jadwal_mengajar_id',
		'nilai_akhir',
		'nilai_huruf',
		'status_kelulusan',
		'catatan'
	];

	// Dates
	protected $useTimestamps = true;
	protected $dateFormat    = 'datetime';
	protected $createdField  = 'created_at';
	protected $updatedField  = 'updated_at';

	/**
	 * Retrieves filtered and paginated final student scores with related data.
	 * This is used for the main 'Nilai Akhir' index page.
	 *
	 * @param array $filters An array of filters (tahun_akademik, mata_kuliah, kelas).
	 * @param int $perPage Number of items per page for pagination.
	 * @return array An array containing the paginated data.
	 */
	public function getFilteredNilai($filters = [], $perPage = 15)
	{
		// Start building the query
		$this->select('
            nilai_mahasiswa.id,
            m.nim,
            m.nama_lengkap as nama_mahasiswa,
            mk.nama_mk,
            jm.kelas,
            nilai_mahasiswa.nilai_akhir,
            nilai_mahasiswa.nilai_huruf,
            nilai_mahasiswa.status_kelulusan
        ')
			->join('mahasiswa m', 'm.id = nilai_mahasiswa.mahasiswa_id')
			->join('jadwal_mengajar jm', 'jm.id = nilai_mahasiswa.jadwal_mengajar_id')
			->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id');

		// Apply filters
		if (!empty($filters['tahun_akademik'])) {
			$this->like('jm.tahun_akademik', $filters['tahun_akademik']);
		}
		if (!empty($filters['mata_kuliah'])) {
			$this->where('mk.id', $filters['mata_kuliah']);
		}
		if (!empty($filters['kelas'])) {
			$this->where('jm.kelas', $filters['kelas']);
		}

		$this->orderBy('mk.nama_mk', 'ASC')->orderBy('m.nim', 'ASC');

		// Return paginated results
		return [
			'nilai' => $this->paginate($perPage),
			'pager' => $this->pager,
		];
	}

	/**
	 * Get final scores (DPNA) for a class.
	 *
	 * @param int $jadwal_id
	 * @return array
	 */
	public function getFinalScoresByJadwal(int $jadwal_id): array
	{
		return $this->select('nilai_mahasiswa.*, m.nim, m.nama_lengkap')
			->join('mahasiswa m', 'm.id = nilai_mahasiswa.mahasiswa_id')
			->where('nilai_mahasiswa.jadwal_mengajar_id', $jadwal_id)
			->orderBy('m.nim', 'ASC')
			->findAll();
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
			'jadwal_mengajar_id' => $data['jadwal_mengajar_id']
		])->first();

		if ($existing) {
			return $this->update($existing['id'], $data);
		} else {
			return $this->insert($data);
		}
	}

	/**
	 * Get nilai with mata kuliah details
	 */
	public function getNilaiWithDetails($mahasiswaId = null)
	{
		$builder = $this->select('nilai_mahasiswa.*, mata_kuliah.nama_mk, mata_kuliah.kode_mk, mata_kuliah.sks, mata_kuliah.semester, jadwal_mengajar.tahun_akademik, jadwal_mengajar.kelas')
			->join('jadwal_mengajar', 'nilai_mahasiswa.jadwal_mengajar_id = jadwal_mengajar.id')
			->join('mata_kuliah', 'jadwal_mengajar.mata_kuliah_id = mata_kuliah.id');

		if ($mahasiswaId) {
			$builder->where('nilai_mahasiswa.mahasiswa_id', $mahasiswaId);
		}

		return $builder->orderBy('jadwal_mengajar.tahun_akademik', 'DESC')
			->orderBy('mata_kuliah.semester', 'ASC')
			->findAll();
	}

	/**
     * Calculate IPK for a student
     */
    public function calculateIPK($mahasiswaId)
    {
        $nilai = $this->select('nilai_mahasiswa.nilai_akhir, mata_kuliah.sks')
            ->join('jadwal_mengajar', 'nilai_mahasiswa.jadwal_mengajar_id = jadwal_mengajar.id')
            ->join('mata_kuliah', 'jadwal_mengajar.mata_kuliah_id = mata_kuliah.id')
            ->where('nilai_mahasiswa.mahasiswa_id', $mahasiswaId)
            ->where('nilai_mahasiswa.status_kelulusan', 'Lulus')
            ->findAll();

        if (empty($nilai)) {
            return 0;
        }

        $totalNilai = 0;
        $totalSks = 0;

        foreach ($nilai as $n) {
            $totalNilai += $n['nilai_akhir'] * $n['sks'];
            $totalSks += $n['sks'];
        }

        return $totalSks > 0 ? $totalNilai / $totalSks : 0;
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class MbkmModel extends Model
{
	protected $table = 'mbkm_kegiatan';
	protected $primaryKey = 'id';
	protected $useAutoIncrement = true;
	protected $returnType = 'array';
	protected $useSoftDeletes = false;
	protected $allowedFields = [
		'jenis_kegiatan',
		'judul_kegiatan',
		'tempat_kegiatan',
		'pembimbing_lapangan',
		'kontak_pembimbing',
		'dosen_pembimbing_id',
		'tanggal_mulai',
		'tanggal_selesai',
		'durasi_minggu',
		'sks_dikonversi',
		'deskripsi_kegiatan',
		'dokumen_pendukung',
		'status_kegiatan',
		'tahun_akademik',
		'nilai_type',
		'cpmk_id',
		'cpl_id'
	];
	protected $useTimestamps = true;
	protected $createdField = 'created_at';
	protected $updatedField = 'updated_at';

	// Get all MBKM activities with related data
	public function getKegiatanLengkap($filters = [])
	{
		$builder = $this->db->table('view_mbkm_lengkap');

		if (!empty($filters['program_studi'])) {
			$builder->where('program_studi', $filters['program_studi']);
		}

		if (!empty($filters['tahun_akademik'])) {
			$builder->like('tahun_akademik', $filters['tahun_akademik']);
		}

		if (!empty($filters['status_kegiatan'])) {
			$builder->where('status_kegiatan', $filters['status_kegiatan']);
		}

		if (!empty($filters['jenis_kegiatan'])) {
			$builder->where('jenis_kegiatan', $filters['jenis_kegiatan']);
		}

		return $builder->get()->getResultArray();
	}

	// Get single activity with full details
	public function getKegiatanById($id)
	{
		return $this->db->table('mbkm_kegiatan k')
			->select('k.*, m.nim, m.nama_lengkap as nama_mahasiswa, m.program_studi_kode,
                     jk.nama_kegiatan, jk.kode_kegiatan, jk.sks_konversi as sks_default,
                     d.nama_lengkap as nama_dosen_pembimbing, d.nip,
                     na.nilai_angka, na.nilai_huruf, na.status_kelulusan, na.catatan_akhir')
			->join('mahasiswa m', 'm.id = k.mahasiswa_id')
			->join('mbkm_jenis_kegiatan jk', 'jk.id = k.jenis_kegiatan_id')
			->join('dosen d', 'd.id = k.dosen_pembimbing_id', 'left')
			->join('mbkm_nilai_akhir na', 'na.kegiatan_id = k.id', 'left')
			->where('k.id', $id)
			->get()
			->getRowArray();
	}

	// Get component scores for an activity
	public function getNilaiKomponen($kegiatan_id)
	{
		return $this->db->table('mbkm_nilai n')
			->select('n.*, k.nama_komponen, k.bobot')
			->join('mbkm_komponen_nilai k', 'k.id = n.komponen_id')
			->where('n.kegiatan_id', $kegiatan_id)
			->get()
			->getResultArray();
	}

	// Calculate final score
	public function hitungNilaiAkhir($kegiatan_id)
	{
		$komponen = $this->db->table('mbkm_nilai n')
			->select('n.nilai, k.bobot')
			->join('mbkm_komponen_nilai k', 'k.id = n.komponen_id')
			->where('n.kegiatan_id', $kegiatan_id)
			->get()
			->getResultArray();

		if (empty($komponen)) {
			return null;
		}

		$totalNilai = 0;
		$totalBobot = 0;

		foreach ($komponen as $k) {
			$totalNilai += ($k['nilai'] * $k['bobot']) / 100;
			$totalBobot += $k['bobot'];
		}

		if ($totalBobot < 100) {
			return null; // Belum semua komponen dinilai
		}

		return round($totalNilai, 2);
	}

	// Convert numeric score to letter grade using dynamic grade configuration
	public function konversiNilaiHuruf($nilai_angka)
	{
		$gradeConfigModel = new GradeConfigModel();
		return $gradeConfigModel->getGradeLetter((float)$nilai_angka);
	}

	// Save or update final score (legacy method for backward compatibility)
	public function simpanNilaiAkhir($kegiatan_id, $nilai_angka, $catatan = null)
	{
		return $this->simpanNilaiAkhirWithCapaian($kegiatan_id, $nilai_angka, null, null, null, $catatan);
	}

	// Save or update final score with CPL/CPMK reference
	public function simpanNilaiAkhirWithCapaian($kegiatan_id, $nilai_angka, $nilai_type = null, $cpmk_id = null, $cpl_id = null, $catatan = null)
	{
		$gradeConfigModel = new GradeConfigModel();
		$nilai_huruf = $gradeConfigModel->getGradeLetter((float)$nilai_angka);
		$status_kelulusan = $gradeConfigModel->isPassing((float)$nilai_angka) ? 'Lulus' : 'Tidak Lulus';

		$data = [
			'kegiatan_id' => $kegiatan_id,
			'nilai_angka' => $nilai_angka,
			'nilai_huruf' => $nilai_huruf,
			'status_kelulusan' => $status_kelulusan,
			'nilai_type' => $nilai_type,
			'cpmk_id' => $cpmk_id,
			'cpl_id' => $cpl_id,
			'catatan_akhir' => $catatan,
			'tanggal_penilaian' => date('Y-m-d')
		];

		$existing = $this->db->table('mbkm_nilai_akhir')
			->where('kegiatan_id', $kegiatan_id)
			->get()
			->getRowArray();

		if ($existing) {
			return $this->db->table('mbkm_nilai_akhir')
				->where('kegiatan_id', $kegiatan_id)
				->update($data);
		} else {
			return $this->db->table('mbkm_nilai_akhir')->insert($data);
		}
	}

	// Get statistics
	public function getStatistik($tahun_akademik = null)
	{
		$builder = $this->db->table('mbkm_kegiatan k')
			->select('k.status_kegiatan, COUNT(*) as jumlah')
			->groupBy('k.status_kegiatan');

		if ($tahun_akademik) {
			$builder->where('k.tahun_akademik', $tahun_akademik);
		}

		return $builder->get()->getResultArray();
	}
}

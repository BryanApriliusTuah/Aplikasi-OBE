<?php
namespace App\Models;
use CodeIgniter\Model;

class PenilaianCpmkModel extends Model
{
    protected $table      = 'penilaian_cpmk';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'cpl_id', 'mata_kuliah_id', 'cpmk_id',
        'partisipasi', 'observasi', 'unjuk_kerja', 'case_method',
        'tes_tulis_uts', 'tes_tulis_uas', 'tes_lisan', 'tahap_penilaian'
    ];

    // Join ke tabel untuk ambil nama CPL, MK, CPMK
    public function getFullPenilaian()
    {
        return $this->select('penilaian_cpmk.*, cpl.kode_cpl, cpl.deskripsi as deskripsi_cpl, mata_kuliah.nama_mk, mata_kuliah.kode_mk, cpmk.kode_cpmk, cpmk.deskripsi as deskripsi_cpmk')
            ->join('cpl', 'cpl.id = penilaian_cpmk.cpl_id')
            ->join('mata_kuliah', 'mata_kuliah.id = penilaian_cpmk.mata_kuliah_id')
            ->join('cpmk', 'cpmk.id = penilaian_cpmk.cpmk_id')
            ->orderBy('cpl.kode_cpl, mata_kuliah.nama_mk, cpmk.kode_cpmk')
            ->findAll();
    }
}

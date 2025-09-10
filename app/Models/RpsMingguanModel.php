<?php
namespace App\Models;
use CodeIgniter\Model;

class RpsMingguanModel extends Model
{
    protected $table = 'rps_mingguan';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'rps_id', 'minggu', 'cpl_id', 'cpmk_id', 'sub_cpmk_id',
        'indikator', 'kriteria_penilaian', 'teknik_penilaian', 'instrumen', 'tahap_penilaian',
        'materi_pembelajaran', 'metode', 'pustaka', 'bobot', 'created_at'
    ];
}

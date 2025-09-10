<?php
namespace App\Models;

use CodeIgniter\Model;

class RpsModel extends Model
{
    protected $table = 'rps';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'mata_kuliah_id', 'semester', 'tahun_ajaran', 'dosen_pengampu_id',
        'tgl_penyusunan', 'status', 'catatan', 'created_at', 'updated_at'
    ];
}

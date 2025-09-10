<?php
namespace App\Models;
use CodeIgniter\Model;

class RpsReferensiModel extends Model
{
    protected $table = 'rps_referensi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'rps_id', 'tipe', 'judul', 'penulis', 'tahun', 'penerbit', 'keterangan', 'created_at'
    ];
}

<?php
namespace App\Models;

use CodeIgniter\Model;

class MataKuliahModel extends Model
{
    protected $table = 'mata_kuliah';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'kode_mk', 'nama_mk', 'kategori', 'tipe', 'semester', 'sks', 'deskripsi_singkat'
    ];
}

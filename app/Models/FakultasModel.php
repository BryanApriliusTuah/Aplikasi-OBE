<?php

namespace App\Models;

use CodeIgniter\Model;

class FakultasModel extends Model
{
    protected $table            = 'fakultas';
    protected $primaryKey       = 'kode';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields    = [
        'kode',
        'nama_singkat',
        'nama_resmi',
        'telepon',
        'email',
        'nip_dekan',
        'nama_dekan'
    ];

    protected $useTimestamps = false;
}

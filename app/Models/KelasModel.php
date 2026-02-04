<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table            = 'kelas';
    protected $primaryKey       = 'kelas_id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields    = [
        'kelas_id',
        'kelas_sem_id',
        'kelas_nama',
        'matakuliah_kurikulum_id',
        'matakuliah_kode',
        'matakuliah_nama',
        'kurikulum_id',
        'kurikulum_status',
        'fakultas_kode',
        'fakultas_nama',
        'program_studi_kode',
        'program_studi_nama'
    ];

    protected $useTimestamps = false;
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class DosenModel extends Model
{
    protected $table            = 'dosen';
    protected $primaryKey       = 'id';
    protected $allowedFields    = [
        'user_id',
        'nip',
        'nama_lengkap',
        'gelar_depan',
        'gelar_belakang',
        'email',
        'no_hp',
        'jabatan_fungsional',
        'status_dosen',
        'status_keaktifan',
        'fakultas_kode',
        'program_studi_kode'
    ];
    protected $useTimestamps    = true;
}
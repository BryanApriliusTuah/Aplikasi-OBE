<?php

namespace App\Models;

use CodeIgniter\Model;

class DosenModel extends Model
{
    protected $table            = 'dosen';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['user_id', 'nip', 'nama_lengkap', 'jabatan_fungsional', 'status_keaktifan'];
    protected $useTimestamps    = true;
}
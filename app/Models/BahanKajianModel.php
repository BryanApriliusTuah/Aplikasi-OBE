<?php

namespace App\Models;

use CodeIgniter\Model;

class BahanKajianModel extends Model
{
    protected $table = 'bahan_kajian';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'kode_bk',
        'nama_bk',
    ];
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class BkMkModel extends Model
{
    protected $table = 'bk_mk';
    protected $primaryKey = 'id';
    protected $allowedFields = ['bahan_kajian_id', 'mata_kuliah_id'];
}

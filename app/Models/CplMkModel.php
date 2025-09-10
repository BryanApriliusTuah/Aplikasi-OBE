<?php

namespace App\Models;

use CodeIgniter\Model;

class CplMkModel extends Model
{
    protected $table = 'cpl_mk';
    protected $primaryKey = 'id';
    protected $allowedFields = ['cpl_id', 'mata_kuliah_id'];
}

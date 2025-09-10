<?php
namespace App\Models;

use CodeIgniter\Model;

class MkPrasyaratModel extends Model
{
    protected $table = 'mk_prasyarat';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'mata_kuliah_id',
        'prasyarat_mk_id'
    ];
}

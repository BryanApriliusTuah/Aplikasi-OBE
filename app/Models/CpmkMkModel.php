<?php
namespace App\Models;

use CodeIgniter\Model;

class CpmkMkModel extends Model
{
    protected $table         = 'cpmk_mk';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;
    protected $protectFields = true;
    protected $allowedFields = ['cpmk_id', 'mata_kuliah_id'];
}

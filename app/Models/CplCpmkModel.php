<?php
namespace App\Models;

use CodeIgniter\Model;

class CplCpmkModel extends Model
{
    protected $table = 'cpl_cpmk';
    protected $allowedFields = ['cpl_id', 'cpmk_id'];
    protected $useTimestamps = true;
}
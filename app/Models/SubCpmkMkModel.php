<?php
namespace App\Models;

use CodeIgniter\Model;

class SubCpmkMkModel extends Model
{
    protected $table      = 'sub_cpmk_mk';
    protected $primaryKey = 'id';
    protected $allowedFields = ['sub_cpmk_id', 'mata_kuliah_id'];
    public $timestamps = true;
}

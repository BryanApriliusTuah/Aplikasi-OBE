<?php
namespace App\Models;
use CodeIgniter\Model;

class SubCpmkModel extends Model
{
    protected $table = 'sub_cpmk';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kode_sub_cpmk', 'deskripsi', 'cpmk_id', 'created_at', 'updated_at'];
}

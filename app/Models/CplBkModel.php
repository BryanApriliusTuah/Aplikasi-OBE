<?php

namespace App\Models;

use CodeIgniter\Model;

class CplBkModel extends Model
{
    protected $table = 'cpl_bk';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'cpl_id',
        'bk_id'
    ];
}

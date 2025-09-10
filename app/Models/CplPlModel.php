<?php

namespace App\Models;

use CodeIgniter\Model;

class CplPlModel extends Model
{
    protected $table = 'cpl_pl';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'cpl_id',
        'pl_id'
    ];
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfilLulusanModel extends Model
{
    protected $table = 'profil_lulusan';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'kode_pl',
        'deskripsi',
    ];
}

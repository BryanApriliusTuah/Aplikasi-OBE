<?php
namespace App\Models;

use CodeIgniter\Model;

class ProfilProdiModel extends Model
{
    protected $table = 'profil_prodi';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'nama_universitas', 'nama_fakultas', 'nama_prodi', 'nama_ketua_prodi', 'nip_ketua_prodi',
        'nama_dekan', 'nip_dekan'
    ];
    protected $useTimestamps = true;
    protected $createdField  = false; 
}

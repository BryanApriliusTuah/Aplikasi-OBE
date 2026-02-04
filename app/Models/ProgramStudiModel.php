<?php

namespace App\Models;

use CodeIgniter\Model;

class ProgramStudiModel extends Model
{
    protected $table            = 'program_studi';
    protected $primaryKey       = 'kode';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields    = [
        'kode',
        'nama_singkat',
        'nama_resmi',
        'telepon',
        'email',
        'website',
        'nip_kaprodi',
        'nama_kaprodi',
        'fakultas_kode'
    ];

    protected $useTimestamps = false;

    public function getWithFakultas()
    {
        return $this->select('program_studi.*, fakultas.nama_resmi as fakultas_nama')
            ->join('fakultas', 'fakultas.kode = program_studi.fakultas_kode', 'left')
            ->findAll();
    }
}

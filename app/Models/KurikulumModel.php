<?php

namespace App\Models;

use CodeIgniter\Model;

class KurikulumModel extends Model
{
    protected $table            = 'kurikulum';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields    = [
        'id',
        'nama',
        'tahun',
        'revisi',
        'status',
        'fakultas_kode',
        'program_studi_kode'
    ];

    protected $useTimestamps = false;

    public function getWithRelations()
    {
        return $this->select('kurikulum.*, fakultas.nama_resmi as fakultas_nama, program_studi.nama_resmi as program_studi_nama')
            ->join('fakultas', 'fakultas.kode = kurikulum.fakultas_kode', 'left')
            ->join('program_studi', 'program_studi.kode = kurikulum.program_studi_kode', 'left')
            ->findAll();
    }
}

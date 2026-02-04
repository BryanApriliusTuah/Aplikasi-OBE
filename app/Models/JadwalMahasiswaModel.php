<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalMahasiswaModel extends Model
{
    protected $table            = 'jadwal_mahasiswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields    = [
        'jadwal_id',
        'nim'
    ];

    protected $useTimestamps = false;
    protected $createdField  = 'created_at';

    public function getByJadwal(int $jadwalId): array
    {
        return $this->select('jadwal_mahasiswa.*, mahasiswa.nama_lengkap, mahasiswa.id as mahasiswa_id')
            ->join('mahasiswa', 'mahasiswa.nim = jadwal_mahasiswa.nim')
            ->where('jadwal_mahasiswa.jadwal_id', $jadwalId)
            ->orderBy('mahasiswa.nim', 'ASC')
            ->findAll();
    }

    public function getByNim(string $nim): array
    {
        return $this->select('jadwal_mahasiswa.*, jadwal.tahun_akademik, jadwal.kelas, mata_kuliah.nama_mk')
            ->join('jadwal', 'jadwal.id = jadwal_mahasiswa.jadwal_id')
            ->join('mata_kuliah', 'mata_kuliah.id = jadwal.mata_kuliah_id')
            ->where('jadwal_mahasiswa.nim', $nim)
            ->findAll();
    }
}

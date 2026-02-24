<?php

namespace App\Models;

use CodeIgniter\Model;

class TahunAkademikModel extends Model
{
    protected $table      = 'tahun_akademik';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'tahun',
        'semester',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all tahun akademik for display, ordered newest first.
     */
    public function getAllForDisplay(): array
    {
        return $this->orderBy('tahun', 'DESC')
            ->orderBy('FIELD(semester, "Genap", "Ganjil")', '', false)
            ->findAll();
    }

    /**
     * Get only active tahun akademik for dropdowns.
     */
    public function getActive(): array
    {
        return $this->where('is_active', 1)
            ->orderBy('tahun', 'DESC')
            ->orderBy('FIELD(semester, "Genap", "Ganjil")', '', false)
            ->findAll();
    }

    /**
     * Return the full display name: "YYYY/YYYY Semester"
     */
    public static function displayName(array $row): string
    {
        return $row['tahun'] . ' ' . $row['semester'];
    }
}

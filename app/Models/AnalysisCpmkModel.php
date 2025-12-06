<?php

namespace App\Models;

use CodeIgniter\Model;

class AnalysisCpmkModel extends Model
{
    protected $table = 'analisis_cpmk';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'mata_kuliah_id',
        'tahun_akademik',
        'program_studi',
        'mode',
        'analisis_singkat'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'mata_kuliah_id' => 'required|integer',
        'tahun_akademik' => 'required|string|max_length[20]',
        'mode' => 'required|in_list[auto,manual]',
    ];

    protected $validationMessages = [
        'mata_kuliah_id' => [
            'required' => 'Mata kuliah harus dipilih',
            'integer' => 'ID mata kuliah tidak valid',
        ],
        'tahun_akademik' => [
            'required' => 'Tahun akademik harus diisi',
        ],
        'mode' => [
            'required' => 'Mode analisis harus dipilih',
            'in_list' => 'Mode analisis tidak valid',
        ],
    ];

    /**
     * Get or create analysis record for a specific course and academic year
     */
    public function getAnalysis($mataKuliahId, $tahunAkademik, $programStudi = null)
    {
        $builder = $this->where('mata_kuliah_id', $mataKuliahId)
                        ->where('tahun_akademik', $tahunAkademik);

        if ($programStudi) {
            $builder->where('program_studi', $programStudi);
        }

        return $builder->first();
    }

    /**
     * Save or update analysis
     */
    public function saveAnalysis($data)
    {
        $existing = $this->getAnalysis(
            $data['mata_kuliah_id'],
            $data['tahun_akademik'],
            $data['program_studi'] ?? null
        );

        if ($existing) {
            return $this->update($existing['id'], $data);
        }

        return $this->insert($data);
    }
}

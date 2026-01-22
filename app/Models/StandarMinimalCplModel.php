<?php

namespace App\Models;

use CodeIgniter\Model;

class StandarMinimalCplModel extends Model
{
    protected $table            = 'standar_minimal_cpl';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['persentase'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get the CPL passing threshold percentage
     *
     * @return float The threshold percentage, defaults to 75.0 if not set
     */
    public function getPersentase(): float
    {
        $result = $this->first();
        return $result ? (float)$result['persentase'] : 75.0;
    }

    /**
     * Update or insert the CPL passing threshold percentage
     *
     * @param float $persentase The threshold percentage to set
     * @return bool True on success, false on failure
     */
    public function updatePersentase(float $persentase): bool
    {
        $existing = $this->first();

        if ($existing) {
            return $this->update($existing['id'], ['persentase' => $persentase]);
        } else {
            return (bool)$this->insert(['persentase' => $persentase]);
        }
    }
}

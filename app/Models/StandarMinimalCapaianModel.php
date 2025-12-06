<?php

namespace App\Models;

use CodeIgniter\Model;

class StandarMinimalCapaianModel extends Model
{
    protected $table            = 'standar_minimal_capaian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields    = ['persentase'];

    /**
     * Get the CPMK passing threshold percentage
     *
     * @return float
     */
    public function getPersentase(): float
    {
        $result = $this->first();
        return $result ? (float)$result['persentase'] : 75.0;
    }

    /**
     * Update the CPMK passing threshold percentage
     *
     * @param float $persentase
     * @return bool
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

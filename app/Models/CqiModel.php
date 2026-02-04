<?php

namespace App\Models;

use CodeIgniter\Model;

class CqiModel extends Model
{
    protected $table = 'cqi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'type',
        'program_studi',
        'tahun_akademik',
        'angkatan',
        'jadwal_id',
        'kode_cpl',
        'kode_cpmk',
        'masalah',
        'rencana_perbaikan',
        'penanggung_jawab',
        'jadwal_pelaksanaan'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'type' => 'required|in_list[cpl,cpmk]',
    ];

    protected $validationMessages = [
        'type' => [
            'required' => 'Tipe CQI harus diisi',
            'in_list' => 'Tipe CQI tidak valid',
        ],
    ];

    /**
     * Get CQI records for CPL
     */
    public function getCqiCplList($programStudi, $tahunAkademik, $angkatan)
    {
        return $this->where('type', 'cpl')
                    ->where('program_studi', $programStudi)
                    ->where('tahun_akademik', $tahunAkademik)
                    ->where('angkatan', $angkatan)
                    ->findAll();
    }

    /**
     * Get CQI record for a specific CPL
     */
    public function getCqiByCpl($programStudi, $tahunAkademik, $angkatan, $kodeCpl)
    {
        return $this->where('type', 'cpl')
                    ->where('program_studi', $programStudi)
                    ->where('tahun_akademik', $tahunAkademik)
                    ->where('angkatan', $angkatan)
                    ->where('kode_cpl', $kodeCpl)
                    ->first();
    }

    /**
     * Get CQI records for CPMK
     */
    public function getCqiCpmkList($jadwalMengajarId)
    {
        return $this->where('type', 'cpmk')
                    ->where('jadwal_id', $jadwalMengajarId)
                    ->findAll();
    }

    /**
     * Get CQI record for a specific CPMK
     */
    public function getCqiByCpmk($jadwalMengajarId, $kodeCpmk)
    {
        return $this->where('type', 'cpmk')
                    ->where('jadwal_id', $jadwalMengajarId)
                    ->where('kode_cpmk', $kodeCpmk)
                    ->first();
    }

    /**
     * Save or update CQI data for CPL
     */
    public function saveCqiCpl($data)
    {
        $existing = $this->getCqiByCpl(
            $data['program_studi'],
            $data['tahun_akademik'],
            $data['angkatan'],
            $data['kode_cpl']
        );

        $data['type'] = 'cpl';

        if ($existing) {
            return $this->update($existing['id'], $data);
        }

        return $this->insert($data);
    }

    /**
     * Save or update CQI data for CPMK
     */
    public function saveCqiCpmk($data)
    {
        $existing = $this->getCqiByCpmk(
            $data['jadwal_id'],
            $data['kode_cpmk']
        );

        $data['type'] = 'cpmk';

        if ($existing) {
            return $this->update($existing['id'], $data);
        }

        return $this->insert($data);
    }

    /**
     * Delete CQI records for CPL
     */
    public function deleteCqiCpl($programStudi, $tahunAkademik, $angkatan)
    {
        return $this->where('type', 'cpl')
                    ->where('program_studi', $programStudi)
                    ->where('tahun_akademik', $tahunAkademik)
                    ->where('angkatan', $angkatan)
                    ->delete();
    }

    /**
     * Delete CQI records for CPMK
     */
    public function deleteCqiCpmk($jadwalMengajarId)
    {
        return $this->where('type', 'cpmk')
                    ->where('jadwal_id', $jadwalMengajarId)
                    ->delete();
    }
}

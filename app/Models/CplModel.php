<?php
namespace App\Models;
use CodeIgniter\Model;

class CplModel extends Model
{
    protected $table = 'cpl';
    protected $primaryKey = 'id';
    protected $allowedFields = ['kode_cpl', 'deskripsi', 'jenis_cpl', 'created_at', 'updated_at'];

    public function getByMk($mk_id)
    {
        return $this->db->table('cpl')
            ->join('cpl_mk', 'cpl.id = cpl_mk.cpl_id')
            ->where('cpl_mk.mata_kuliah_id', $mk_id)
            ->get()->getResultArray();
    }
}

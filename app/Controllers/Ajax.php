<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Ajax extends BaseController
{
  
    public function cpmkByCplMk($cpl_id, $mk_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('cpmk')
            ->select('cpmk.id, cpmk.kode_cpmk, cpmk.deskripsi')
            ->join('cpl_cpmk', 'cpmk.id = cpl_cpmk.cpmk_id') 
            ->join('cpmk_mk', 'cpmk.id = cpmk_mk.cpmk_id')
            ->where('cpl_cpmk.cpl_id', $cpl_id)
            ->where('cpmk_mk.mata_kuliah_id', $mk_id)
            ->orderBy('cpmk.kode_cpmk', 'asc')
            ->groupBy('cpmk.id');

        return $this->response->setJSON($builder->get()->getResultArray());
    }

   
    public function subcpmkByCpmkCplMk($cpmk_id, $cpl_id, $mk_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('sub_cpmk')
            ->select('sub_cpmk.id, sub_cpmk.kode_sub_cpmk, sub_cpmk.deskripsi')
            ->join('cpmk', 'sub_cpmk.cpmk_id = cpmk.id')
            ->join('cpl_cpmk', 'cpmk.id = cpl_cpmk.cpmk_id')
            ->join('cpmk_mk', 'cpmk.id = cpmk_mk.cpmk_id')
            ->where('sub_cpmk.cpmk_id', $cpmk_id)
            ->where('cpl_cpmk.cpl_id', $cpl_id)
            ->where('cpmk_mk.mata_kuliah_id', $mk_id)
            ->orderBy('sub_cpmk.kode_sub_cpmk', 'asc')
            ->groupBy('sub_cpmk.id');
            
        return $this->response->setJSON($builder->get()->getResultArray());
    }


    public function cpmkByCpl($cpl_id)
    {
        $model = new \App\Models\CpmkModel();
        $data = $model->where('cpl_id', $cpl_id)->findAll();
        return $this->response->setJSON($data);
    }

    public function subcpmkByCpmk($cpmk_id)
    {
        $model = new \App\Models\SubCpmkModel();
        $data = $model->where('cpmk_id', $cpmk_id)->findAll();
        return $this->response->setJSON($data);
    }

    public function mkByCpl($cpl_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('cpl_mk')
            ->select('mata_kuliah.id, mata_kuliah.kode_mk, mata_kuliah.nama_mk')
            ->join('mata_kuliah', 'cpl_mk.mata_kuliah_id = mata_kuliah.id')
            ->where('cpl_mk.cpl_id', $cpl_id)
            ->groupBy('mata_kuliah.id');

        return $this->response->setJSON($builder->get()->getResultArray());
    }

    public function mkByCpmk($cpmk_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('cpmk_mk')
            ->select('mata_kuliah.id, mata_kuliah.kode_mk, mata_kuliah.nama_mk')
            ->join('mata_kuliah', 'cpmk_mk.mata_kuliah_id = mata_kuliah.id')
            ->where('cpmk_mk.cpmk_id', $cpmk_id)
            ->groupBy('mata_kuliah.id');

        return $this->response->setJSON($builder->get()->getResultArray());
    }
}
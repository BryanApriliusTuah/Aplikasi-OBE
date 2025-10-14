<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class MbkmJenisController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    // List all activity types
    public function index()
    {
        $jenis = $this->db->table('mbkm_jenis_kegiatan')
            ->orderBy('kode_kegiatan', 'ASC')
            ->get()
            ->getResultArray();

        $data = ['jenis_kegiatan' => $jenis];
        return view('admin/mbkm_jenis/index', $data);
    }

    // Show create form
    public function create()
    {
        return view('admin/mbkm_jenis/create');
    }

    // Store new activity type
    public function store()
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'kode_kegiatan' => 'required|is_unique[mbkm_jenis_kegiatan.kode_kegiatan]|max_length[20]',
            'nama_kegiatan' => 'required|max_length[100]',
            'sks_konversi' => 'required|integer|greater_than[0]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'kode_kegiatan' => $this->request->getPost('kode_kegiatan'),
            'nama_kegiatan' => $this->request->getPost('nama_kegiatan'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'sks_konversi' => $this->request->getPost('sks_konversi'),
            'status' => 'aktif'
        ];

        if ($this->db->table('mbkm_jenis_kegiatan')->insert($data)) {
            return redirect()->to('/admin/mbkm-jenis')->with('success', 'Jenis kegiatan berhasil ditambahkan');
        }
        
        return redirect()->back()->withInput()->with('error', 'Gagal menambahkan jenis kegiatan');
    }

    // Show edit form
    public function edit($id)
    {
        $jenis = $this->db->table('mbkm_jenis_kegiatan')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$jenis) {
            return redirect()->to('/admin/mbkm-jenis')->with('error', 'Data tidak ditemukan');
        }

        $data = ['jenis' => $jenis];
        return view('admin/mbkm_jenis/edit', $data);
    }

    // Update activity type
    public function update($id)
    {
        $validation = \Config\Services::validation();
        
        $rules = [
            'kode_kegiatan' => "required|max_length[20]|is_unique[mbkm_jenis_kegiatan.kode_kegiatan,id,{$id}]",
            'nama_kegiatan' => 'required|max_length[100]',
            'sks_konversi' => 'required|integer|greater_than[0]',
            'status' => 'required|in_list[aktif,nonaktif]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'kode_kegiatan' => $this->request->getPost('kode_kegiatan'),
            'nama_kegiatan' => $this->request->getPost('nama_kegiatan'),
            'deskripsi' => $this->request->getPost('deskripsi'),
            'sks_konversi' => $this->request->getPost('sks_konversi'),
            'status' => $this->request->getPost('status')
        ];

        if ($this->db->table('mbkm_jenis_kegiatan')->where('id', $id)->update($data)) {
            return redirect()->to('/admin/mbkm-jenis')->with('success', 'Jenis kegiatan berhasil diperbarui');
        }
        
        return redirect()->back()->withInput()->with('error', 'Gagal memperbarui jenis kegiatan');
    }

    // Delete activity type
    public function delete($id)
    {
        // Check if used in any activity
        $used = $this->db->table('mbkm_kegiatan')
            ->where('jenis_kegiatan_id', $id)
            ->countAllResults();

        if ($used > 0) {
            return redirect()->to('/admin/mbkm-jenis')
                ->with('error', "Jenis kegiatan tidak dapat dihapus karena sedang digunakan oleh {$used} kegiatan");
        }

        if ($this->db->table('mbkm_jenis_kegiatan')->where('id', $id)->delete()) {
            return redirect()->to('/admin/mbkm-jenis')->with('success', 'Jenis kegiatan berhasil dihapus');
        }
        
        return redirect()->to('/admin/mbkm-jenis')->with('error', 'Gagal menghapus jenis kegiatan');
    }
}
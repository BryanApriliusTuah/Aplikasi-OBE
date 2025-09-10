<?php

namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\DosenModel;

class Dosen extends BaseController
{
    public function index()
    {
        $model = new DosenModel();
        $data['dosen'] = $model->orderBy('nama_lengkap', 'ASC')->findAll();
        
        return view('admin/dosen/index', $data);
    }

    public function create()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/admin/dosen')->with('error', 'Anda tidak memiliki hak akses.');
        }

        return view('admin/dosen/create');
    }

    public function store()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/admin/dosen')->with('error', 'Anda tidak memiliki hak akses.');
        }

        $model = new DosenModel();
        
        $jabatan = $this->request->getPost('jabatan_fungsional');
        $jabatan_string = is_array($jabatan) ? implode(', ', $jabatan) : '';

        $data = [
            'nip'                   => $this->request->getPost('nip'),
            'nama_lengkap'          => $this->request->getPost('nama_lengkap'),
            'jabatan_fungsional'    => $jabatan_string,
            'status_keaktifan'      => $this->request->getPost('status_keaktifan'),
        ];

        $model->save($data);
        return redirect()->to('/admin/dosen')->with('success', 'Data dosen berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/admin/dosen')->with('error', 'Anda tidak memiliki hak akses.');
        }

        $model = new DosenModel();
        $dosenData = $model->find($id);

        if (empty($dosenData)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Data dosen tidak ditemukan.');
        }

        $data['dosen'] = $dosenData;
        $data['jabatan_terpilih'] = explode(', ', $dosenData['jabatan_fungsional']);

        return view('admin/dosen/edit', $data);
    }

    public function update($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/admin/dosen')->with('error', 'Anda tidak memiliki hak akses.');
        }

        $model = new DosenModel();
        
        $jabatan = $this->request->getPost('jabatan_fungsional');
        $jabatan_string = is_array($jabatan) ? implode(', ', $jabatan) : '';

        $data = [
            'nip'                   => $this->request->getPost('nip'),
            'nama_lengkap'          => $this->request->getPost('nama_lengkap'),
            'jabatan_fungsional'    => $jabatan_string,
            'status_keaktifan'      => $this->request->getPost('status_keaktifan'),
        ];

        $model->update($id, $data);
        return redirect()->to('/admin/dosen')->with('success', 'Data dosen berhasil diperbarui.');
    }

    public function delete($id)
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->to('/admin/dosen')->with('error', 'Anda tidak memiliki hak akses.');
        }
        
        $model = new DosenModel();
        $model->delete($id);
        return redirect()->to('/admin/dosen')->with('success', 'Data dosen berhasil dihapus.');
    }
}
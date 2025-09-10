<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ProfilProdiModel;

class ProfilProdi extends BaseController
{
    protected $profilProdiModel;

    public function __construct()
    {
        $this->profilProdiModel = new ProfilProdiModel();
    }

    // FORM EDIT
    public function edit($id)
    {
        if (session('role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat mengedit data.');
        }
        $profil = $this->profilProdiModel->find($id);
        if (!$profil) return redirect()->to(base_url('admin'));
        return view('admin/profil_prodi/edit', ['profil' => $profil]);
    }

    // UPDATE DATA
    public function update($id)
    {
        if (session('role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat mengupdate data.');
        }
        $profil = $this->profilProdiModel->find($id);
        if (!$profil) return redirect()->to(base_url('admin'));

        $data = [
            'nama_universitas'   => $this->request->getPost('nama_universitas'),
            'nama_fakultas'      => $this->request->getPost('nama_fakultas'),
            'nama_prodi'         => $this->request->getPost('nama_prodi'),
            'nama_ketua_prodi'   => $this->request->getPost('nama_ketua_prodi'),
            'nip_ketua_prodi'    => $this->request->getPost('nip_ketua_prodi'),
            'nama_dekan'         => $this->request->getPost('nama_dekan'),
            'nip_dekan'          => $this->request->getPost('nip_dekan'),
            'updated_at'         => date('Y-m-d H:i:s')
        ];

        $this->profilProdiModel->update($id, $data);
        return redirect()->to(base_url('admin'))->with('success', 'Profil prodi berhasil diperbarui.');
    }

}

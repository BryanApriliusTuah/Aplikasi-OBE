<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\DosenModel;

class User extends BaseController
{
    protected $userModel;
    protected $dosenModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->dosenModel = new DosenModel();
    }

    public function index()
    {
        $data['users'] = $this->userModel
            ->select('users.id, users.username, users.role, dosen.nama_lengkap')
            ->join('dosen', 'dosen.user_id = users.id', 'left')
            ->findAll();
            
        return view('admin/user/index', $data);
    }

    public function create()
    {
        $data['dosen_list'] = $this->dosenModel->where('user_id', null)->findAll();
        return view('admin/user/create', $data);
    }

    public function store()
    {
        $userData = [
            'username' => $this->request->getPost('username'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => $this->request->getPost('role')
        ];
        $dosen_id = $this->request->getPost('dosen_id');

        if ($this->userModel->where('username', $userData['username'])->countAllResults() > 0) {
            return redirect()->back()->withInput()->with('error', 'Username sudah digunakan!');
        }

        $this->userModel->insert($userData);
        
        $new_user_id = $this->userModel->getInsertID();

        if ($userData['role'] === 'dosen' && !empty($dosen_id)) {
            $this->dosenModel->update($dosen_id, ['user_id' => $new_user_id]);
        }

        return redirect()->to(base_url('admin/user'))->with('success', 'User berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data['user'] = $this->userModel
            ->select('users.*, dosen.nama_lengkap')
            ->join('dosen', 'dosen.user_id = users.id', 'left')
            ->find($id);

        if (empty($data['user'])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('User tidak ditemukan.');
        }
            
        return view('admin/user/edit', $data);
    }

    public function update($id)
    {
        $data = $this->request->getPost();
        $userLama = $this->userModel->find($id);

        if (isset($data['username']) && $data['username'] !== $userLama['username']) {
            if ($this->userModel->where('username', $data['username'])->countAllResults() > 0) {
                return redirect()->back()->withInput()->with('error', 'Username sudah digunakan!');
            }
        }

        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }

        $this->userModel->update($id, $data);
        return redirect()->to(base_url('admin/user'))->with('success', 'User berhasil diupdate');
    }

    public function delete($id)
    {
        $user = $this->userModel->find($id);
        if ($user && $user['role'] === 'dosen') {
            $this->dosenModel->where('user_id', $id)->set(['user_id' => null])->update();
        }

        $this->userModel->delete($id);
        return redirect()->to(base_url('admin/user'))->with('success', 'User berhasil dihapus');
    }
}
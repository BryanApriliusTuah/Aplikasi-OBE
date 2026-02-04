<?php
namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\DosenModel;
use App\Models\MahasiswaModel;

class User extends BaseController
{
    protected $userModel;
    protected $dosenModel;
	protected $mahasiswaModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->dosenModel = new DosenModel();
		$this->mahasiswaModel = new MahasiswaModel();
    }

    public function index()
    {
        $filters = [
            'role'   => $this->request->getGet('role'),
            'search' => $this->request->getGet('search'),
        ];

        $builder = $this->userModel
            ->select('users.id, users.username, users.role, COALESCE(dosen.nama_lengkap, mahasiswa.nama_lengkap) as nama_lengkap')
            ->join('dosen', 'dosen.user_id = users.id', 'left')
            ->join('mahasiswa', 'mahasiswa.user_id = users.id', 'left')
            ->orderBy('users.id', 'ASC');

        if (!empty($filters['role'])) {
            $builder->where('users.role', $filters['role']);
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->like('users.username', $search)
                ->orLike('dosen.nama_lengkap', $search)
                ->orLike('mahasiswa.nama_lengkap', $search)
                ->groupEnd();
        }

        $data['users']   = $builder->findAll();
        $data['filters'] = $filters;

        return view('admin/user/index', $data);
    }

    public function create()
    {
        $data['dosen_list'] = $this->dosenModel->where('user_id', null)->findAll();
		$data['mahasiswa_list'] = $this->mahasiswaModel->where('user_id', null)->findAll();
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
		$mahasiswa_id = $this->request->getPost('mahasiswa_id');

        if ($this->userModel->where('username', $userData['username'])->countAllResults() > 0) {
            return redirect()->back()->withInput()->with('error', 'Username sudah digunakan!');
        }

        $this->userModel->insert($userData);
        
        $new_user_id = $this->userModel->getInsertID();

        if ($userData['role'] === 'dosen' && !empty($dosen_id)) {
            $this->dosenModel->update($dosen_id, ['user_id' => $new_user_id]);
        } elseif ($userData['role'] === 'mahasiswa' && !empty($mahasiswa_id)) {
			$this->mahasiswaModel->update($mahasiswa_id, ['user_id' => $new_user_id]);
		}

        return redirect()->to(base_url('admin/user'))->with('success', 'User berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data['user'] = $this->userModel
            ->select('users.*, COALESCE(dosen.nama_lengkap, mahasiswa.nama_lengkap) as nama_terhubung')
            ->join('dosen', 'dosen.user_id = users.id', 'left')
            ->join('mahasiswa', 'mahasiswa.user_id = users.id', 'left')
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
        } elseif ($user && $user['role'] === 'mahasiswa') {
			$this->mahasiswaModel->where('user_id', $id)->set(['user_id' => null])->update();
		}

        $this->userModel->delete($id);
        return redirect()->to(base_url('admin/user'))->with('success', 'User berhasil dihapus');
    }

    public function generateUsers()
    {
        set_time_limit(600);

        $usersCreated = 0;

        // Generate user accounts for dosen without user_id
        $unlinkedDosen = $this->dosenModel->where('user_id', null)->findAll();
        foreach ($unlinkedDosen as $dosen) {
            $username = $dosen['nip'];
            if (empty($username)) {
                continue;
            }

            if ($this->userModel->where('username', $username)->first()) {
                continue;
            }

            $this->userModel->insert([
                'username' => $username,
                'password' => password_hash($username, PASSWORD_DEFAULT),
                'role'     => 'dosen',
            ]);

            $newUserId = $this->userModel->getInsertID();
            $this->dosenModel->update($dosen['id'], ['user_id' => $newUserId]);
            $usersCreated++;
        }

        // Generate user accounts for mahasiswa without user_id
        $unlinkedMahasiswa = $this->mahasiswaModel->where('user_id', null)->findAll();
        foreach ($unlinkedMahasiswa as $mhs) {
            $username = $mhs['nim'];
            if (empty($username)) {
                continue;
            }

            if ($this->userModel->where('username', $username)->first()) {
                continue;
            }

            $this->userModel->insert([
                'username' => $username,
                'password' => password_hash($username, PASSWORD_DEFAULT),
                'role'     => 'mahasiswa',
            ]);

            $newUserId = $this->userModel->getInsertID();
            $this->mahasiswaModel->update($mhs['id'], ['user_id' => $newUserId]);
            $usersCreated++;
        }

        if ($usersCreated > 0) {
            return redirect()->to(base_url('admin/user'))->with('success', "$usersCreated akun user baru berhasil dibuat.");
        }

        return redirect()->to(base_url('admin/user'))->with('success', 'Tidak ada data dosen/mahasiswa yang perlu dibuatkan akun.');
    }
}
<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\DosenModel;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('logged_in')) {
            if (session()->get('role') === 'admin') {
                return redirect()->to('/admin');
            } else {
                return redirect()->to('/rps');
            }
        }
        return view('auth/login');
    }

    public function loginProcess()
    {
        $session = session();
        $userModel = new UserModel();
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $user = $userModel->where('username', $username)->first();

        if ($user && password_verify($password, $user['password'])) {
            
            $nama_session = $user['username'];

            if ($user['role'] === 'dosen') {
                $dosenModel = new DosenModel();
                $dosen = $dosenModel->where('user_id', $user['id'])->first();
                
                if ($dosen) {
                    $nama_session = $dosen['nama_lengkap'];
                }
            }

            $session_data = [
                'id'        => $user['id'],
                'username'  => $user['username'],
                'role'      => $user['role'],
                'nama'      => $nama_session,
                'logged_in' => true,
            ];
            $session->set($session_data);

            if ($user['role'] === 'admin') {
                return redirect()->to('/admin');
            } else {
                return redirect()->to('/rps');
            }
        } else {
            return redirect()->back()->with('error', 'Username atau password salah.');
        }
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/auth/login');
    }
}
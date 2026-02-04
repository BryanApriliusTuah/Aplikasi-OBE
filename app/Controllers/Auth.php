<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\DosenModel;
use App\Models\MahasiswaModel;

class Auth extends BaseController
{
	/**
	 * Display login page
	 */
	public function login()
	{
		// If already logged in, redirect based on role
		if (session()->get('logged_in')) {
			return $this->redirectBasedOnRole();
		}

		return view('auth/login');
	}

	/**
	 * Process login authentication
	 */
	public function loginProcess()
	{
		$session = session();
		$userModel = new UserModel();
		$username = $this->request->getPost('username');
		$password = $this->request->getPost('password');

		// Validate input
		if (empty($username) || empty($password)) {
			return redirect()->back()
				->with('error', 'Username dan password harus diisi.')
				->withInput();
		}

		// Find user by username
		$user = $userModel->where('username', $username)->first();

		// Check if user exists and password is correct
		if (!$user || !password_verify($password, $user['password'])) {
			return redirect()->back()
				->with('error', 'Username atau password salah.')
				->withInput();
		}

		// Prepare base session data
		$sessionData = [
			'user_id'   => $user['id'],
			'username'  => $user['username'],
			'role'      => $user['role'],
			'logged_in' => true,
		];

		// Get additional data based on role
		if ($user['role'] === 'dosen') {
			$dosenModel = new DosenModel();
			$dosen = $dosenModel->where('user_id', $user['id'])->first();

			if ($dosen) {
				$sessionData['dosen_id'] = $dosen['id'];
				$sessionData['nama_lengkap'] = $dosen['nama_lengkap'];
				$sessionData['nip'] = $dosen['nip'];
				$sessionData['nama'] = $dosen['nama_lengkap']; // For backward compatibility
			} else {
				$sessionData['nama'] = $user['username'];
				$sessionData['nama_lengkap'] = $user['username'];
			}
		} elseif ($user['role'] === 'mahasiswa') {
			$mahasiswaModel = new MahasiswaModel();
			$mahasiswa = $mahasiswaModel->where('user_id', $user['id'])->first();

			if ($mahasiswa) {
				$sessionData['mahasiswa_id'] = $mahasiswa['id'];
				$sessionData['nama_lengkap'] = $mahasiswa['nama_lengkap'];
				$sessionData['nim'] = $mahasiswa['nim'];
				$sessionData['program_studi_kode'] = $mahasiswa['program_studi_kode'];
				$sessionData['tahun_angkatan'] = $mahasiswa['tahun_angkatan'];
				$sessionData['nama'] = $mahasiswa['nama_lengkap']; // For backward compatibility
			} else {
				$sessionData['nama'] = $user['username'];
				$sessionData['nama_lengkap'] = $user['username'];
			}
		} elseif ($user['role'] === 'admin') {
			$sessionData['nama'] = 'Administrator';
			$sessionData['nama_lengkap'] = 'Administrator';
		}

		// Set session
		$session->set($sessionData);

		// Redirect based on role
		return $this->redirectBasedOnRole();
	}

	/**
	 * Logout and destroy session
	 */
	public function logout()
	{
		session()->destroy();
		return redirect()->to('/auth/login')->with('success', 'Anda telah berhasil logout.');
	}

	/**
	 * Redirect user based on their role
	 */
	private function redirectBasedOnRole()
	{
		$role = session()->get('role');

		switch ($role) {
			case 'admin':
				return redirect()->to('/admin');
			case 'dosen':
				return redirect()->to('/rps');
			case 'mahasiswa':
				return redirect()->to('/mahasiswa/dashboard');
			default:
				// If role is unknown, logout and redirect to login
				session()->destroy();
				return redirect()->to('/auth/login')->with('error', 'Role tidak valid.');
		}
	}
}

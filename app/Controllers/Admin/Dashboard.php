<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\MataKuliahModel;
use App\Models\RpsModel;
use App\Models\ProfilProdiModel;

class Dashboard extends BaseController
{
	public function index()
	{
		$userModel = new UserModel();
		$mkModel = new MataKuliahModel();
		$rpsModel = new RpsModel();
		$profilProdiModel = new ProfilProdiModel();

		$data = [
			'total_dosen'   => $userModel->where('role', 'dosen')->countAllResults(),
			'total_mk'      => $mkModel->countAllResults(),
			'total_rps'     => $rpsModel->countAllResults(),
			'profil_prodi'  => $profilProdiModel->first(),
		];

		return view('admin/dashboard', $data);
	}
}

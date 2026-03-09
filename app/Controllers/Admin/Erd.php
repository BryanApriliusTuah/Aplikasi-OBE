<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Erd extends BaseController
{
	public function index()
	{
		return view('admin/erd/index');
	}
}
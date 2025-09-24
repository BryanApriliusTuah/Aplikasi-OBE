<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/auth/login', 'Auth::login');
$routes->post('/auth/login', 'Auth::loginProcess');
$routes->get('/', 'Auth::login');
$routes->get('logout', 'Auth::logout');
$routes->get('admin', 'Admin\Dashboard::index');
$routes->get('admin/dashboard', 'Admin\Dashboard::index');

$routes->group('admin', ['namespace' => 'App\Controllers\Admin'], function ($routes) {

	// profil lulusan
	$routes->get('profil-lulusan', 'ProfilLulusan::index');
	$routes->get('profil-lulusan/create', 'ProfilLulusan::create');
	$routes->post('profil-lulusan/store', 'ProfilLulusan::store');
	$routes->get('profil-lulusan/edit/(:num)', 'ProfilLulusan::edit/$1');
	$routes->post('profil-lulusan/update/(:num)', 'ProfilLulusan::update/$1');
	$routes->get('profil-lulusan/delete/(:num)', 'ProfilLulusan::delete/$1');
	$routes->post('profil-lulusan/delete/(:num)', 'ProfilLulusan::delete/$1');

	//  CPL
	$routes->get('cpl', 'Cpl::index');
	$routes->get('cpl/create', 'Cpl::create');
	$routes->post('cpl/store', 'Cpl::store');
	$routes->get('cpl/edit/(:num)', 'Cpl::edit/$1');
	$routes->post('cpl/update/(:num)', 'Cpl::update/$1');
	$routes->get('cpl/delete/(:num)', 'Cpl::delete/$1');

	//Cpl-Pl
	$routes->get('cpl-pl', 'CplPl::index');
	$routes->get('cpl-pl/create', 'CplPl::create');
	$routes->post('cpl-pl/store', 'CplPl::store');
	$routes->get('cpl-pl/delete/(:num)', 'CplPl::delete/$1');
	$routes->post('cpl-pl/delete/(:num)', 'CplPl::delete/$1');

	//Bk
	$routes->get('bahan-kajian', 'BahanKajian::index');
	$routes->get('bahan-kajian/create', 'BahanKajian::create');
	$routes->post('bahan-kajian/store', 'BahanKajian::store');
	$routes->get('bahan-kajian/edit/(:num)', 'BahanKajian::edit/$1');
	$routes->post('bahan-kajian/update/(:num)', 'BahanKajian::update/$1');
	$routes->post('bahan-kajian/delete/(:num)', 'BahanKajian::delete/$1');

	// CPL-BK
	$routes->get('cpl-bk', 'CplBk::index');
	$routes->get('cpl-bk/create', 'CplBk::create');
	$routes->post('cpl-bk/store', 'CplBk::store');
	$routes->get('cpl-bk/delete/(:num)', 'CplBk::delete/$1');
	$routes->post('cpl-bk/delete/(:num)', 'CplBk::delete/$1');


	// Mata Kuliah
	$routes->get('mata-kuliah', 'MataKuliah::index');
	$routes->get('mata-kuliah/create', 'MataKuliah::create');
	$routes->post('mata-kuliah/store', 'MataKuliah::store');
	$routes->get('mata-kuliah/edit/(:num)', 'MataKuliah::edit/$1');
	$routes->post('mata-kuliah/update/(:num)', 'MataKuliah::update/$1');
	$routes->post('mata-kuliah/delete/(:num)', 'MataKuliah::delete/$1');



	//BK-Mk
	$routes->get('bkmk', 'BkMk::index');
	$routes->post('bkmk/store', 'BkMk::store');
	$routes->get('bkmk/delete/(:num)', 'BkMk::delete/$1');
	$routes->get('bkmk/create', 'BkMk::create');
	$routes->get('bkmk/edit/(:num)', 'BkMk::edit/$1');
	$routes->post('bkmk/update/(:num)', 'BkMk::update/$1');
	$routes->get('bkmk/matriks', 'BkMk::matriks');
	$routes->post('bkmk/delete/(:num)', 'BkMk::delete/$1');


	//CPL-Mk
	$routes->get('cpl-mk', 'CplMk::index');
	$routes->get('cpl-mk/create', 'CplMk::create');
	$routes->post('cpl-mk/store', 'CplMk::store');
	$routes->post('cpl-mk/delete/(:num)/(:num)', 'CplMk::delete/$1/$2');


	//cpl-bk-mk
	$routes->get('cpl-bk-mk', 'CplBkMkView::index');

	//organisasi mk
	$routes->get('organisasi-mk', 'OrganisasiMk::index');

	//peta pemenuhan CPL
	$routes->get('peta-cpl', 'PetaCPL::index');


	//  CPMK
	$routes->get('cpmk', 'Cpmk::index');
	$routes->get('cpmk/create', 'Cpmk::create');
	$routes->post('cpmk/store', 'Cpmk::store');
	$routes->post('cpmk/delete/(:num)', 'Cpmk::delete/$1');
	$routes->get('cpmk/edit/(:num)', 'Cpmk::edit/$1');
	$routes->post('cpmk/update/(:num)', 'Cpmk::update/$1');

	//pemetaan cpl-mk-cpmk
	$routes->get('pemetaan-cpl-mk-cpmk', 'PemetaanCplMkCpmk::index');
	$routes->get('pemetaan-cpl-mk-cpmk/create', 'PemetaanCplMkCpmk::create');
	$routes->post('pemetaan-cpl-mk-cpmk/store', 'PemetaanCplMkCpmk::store');
	$routes->get('pemetaan-cpl-mk-cpmk/edit/(:num)/(:num)', 'PemetaanCplMkCpmk::edit/$1/$2');
	$routes->post('pemetaan-cpl-mk-cpmk/update/(:num)/(:num)', 'PemetaanCplMkCpmk::update/$1/$2');
	$routes->get('pemetaan-cpl-mk-cpmk/delete/(:num)', 'PemetaanCplMkCpmk::delete/$1');
	$routes->post('pemetaan-cpl-mk-cpmk/deleteGroup/(:num)/(:num)', 'PemetaanCplMkCpmk::deleteGroup/$1/$2');
	$routes->get('pemetaan-cpl-mk-cpmk/get-mk/(:num)', 'PemetaanCplMkCpmk::getMataKuliahByCpl/$1');
	$routes->get('pemetaan-cpl-mk-cpmk/get-cpmk/(:any)', 'PemetaanCplMkCpmk::getCpmkByKodeCpl/$1');
	$routes->get('pemetaan-cpl-mk-cpmk/exportExcel', 'PemetaanCplMkCpmk::exportExcel');
	$routes->get('pemetaan-cpl-mk-cpmk/exportPdf', 'PemetaanCplMkCpmk::exportPdf');

	// Pemetaan MK-CPMK-SUBCPMK
	$routes->group('pemetaan-mk-cpmk-sub', static function ($routes) {
		$routes->get('/', 'PemetaanMkCpmkSub::index');
		$routes->get('create', 'PemetaanMkCpmkSub::create');
		$routes->post('store', 'PemetaanMkCpmkSub::store');
		$routes->get('edit/(:num)', 'PemetaanMkCpmkSub::edit/$1');
		$routes->post('update/(:num)', 'PemetaanMkCpmkSub::update/$1');
		$routes->post('delete/(:num)', 'PemetaanMkCpmkSub::delete/$1');
		$routes->get('export-excel', 'PemetaanMkCpmkSub::exportExcel');
		$routes->get('export-pdf', 'PemetaanMkCpmkSub::exportPdf');
		$routes->get('get-next-suffix/(:num)', 'PemetaanMkCpmkSub::getNextSuffix/$1');
	});

	// fetch MK by CPMK
	$routes->get('pemetaan-mk-cpmk-subcpmk/get-mk/(:num)', 'PemetaanMkCpmkSub::getMkByCpmk/$1');

	//Profil Lulusan
	$routes->get('profil-prodi', 'ProfilProdi::index');
	$routes->get('profil-prodi/create', 'ProfilProdi::create');
	$routes->post('profil-prodi/store', 'ProfilProdi::store');
	$routes->get('profil-prodi/edit/(:num)', 'ProfilProdi::edit/$1');
	$routes->post('profil-prodi/update/(:num)', 'ProfilProdi::update/$1');
	$routes->post('profil-prodi/delete/(:num)', 'ProfilProdi::delete/$1');

	// Rute untuk Master Data Dosen
	$routes->get('dosen', 'Dosen::index');
	$routes->get('dosen/create', 'Dosen::create');
	$routes->post('dosen/store', 'Dosen::store');
	$routes->get('dosen/delete/(:num)', 'Dosen::delete/$1');
	$routes->get('dosen/edit/(:num)', 'Dosen::edit/$1');
	$routes->post('dosen/update/(:num)', 'Dosen::update/$1');

	// Rute untuk Master Data Mahasiswa
	$routes->get('mahasiswa', 'Mahasiswa::index');
	$routes->get('mahasiswa/create', 'Mahasiswa::create');
	$routes->post('mahasiswa/store', 'Mahasiswa::store');
	$routes->get('mahasiswa/edit/(:num)', 'Mahasiswa::edit/$1');
	$routes->post('mahasiswa/update/(:num)', 'Mahasiswa::update/$1');
	$routes->get('mahasiswa/delete/(:num)', 'Mahasiswa::delete/$1');

	//matriks pemetaan cpl-cpmk-mk persemester
	$routes->get('cpl-cpmk-mk-per-semester', 'CplCpmkMkPerSemester::index');

	//matriks pemetaan MK-CPL-CPMK
	$routes->get('mk-cpl-cpmk', 'MkCplCpmk::index');

	//mengajar
	$routes->get('mengajar', 'Mengajar::index');
	$routes->get('mengajar/show/(:num)', 'Mengajar::show/$1');
	$routes->get('mengajar/create', 'Mengajar::create');
	$routes->post('mengajar/store', 'Mengajar::store');
	$routes->get('mengajar/edit/(:num)', 'Mengajar::edit/$1');
	$routes->post('mengajar/update/(:num)', 'Mengajar::update/$1');
	$routes->delete('mengajar/delete/(:num)', 'Mengajar::delete/$1');
	$routes->get('mengajar/exportExcel', 'Mengajar::exportExcel');
	$routes->get('mengajar/exportPdf', 'Mengajar::exportPdf');

	//nilai
	$routes->get('nilai', 'Nilai::index');
	$routes->get('nilai/input-nilai/(:num)', 'Nilai::inputNilai/$1');
	$routes->post('nilai/save-nilai/(:num)', 'Nilai::saveNilai/$1');
	$routes->get('nilai/detail-nilai/(:num)', 'Nilai::getDetailNilaiCpmk/$1');
	$routes->get('nilai/dpna/(:num)', 'Nilai::getDpna/$1');
	// $routes->get('nilai/cetak-dpna/(:num)', 'Nilai::cetakDpna/$1');
	// $routes->get('nilai/exportExcel/(:num)', 'Nilai::exportExcel/$1');
	// $routes->get('nilai/exportPdf/(:num)', 'Nilai::exportPdf/$1');
});

//teknik penilaian cpmk
$routes->get('teknik-penilaian-cpmk', 'TeknikPenilaianCpmk::index');



//mekanisme penilaian
$routes->get('tahap-mekanisme-penilaian', 'TahapMekanismePenilaian::index');

// Bobot Penilaian CPL
$routes->get('bobot-penilaian-cpl', 'BobotPenilaianCpl::index');


//bobot penilaian mk
$routes->get('bobot-penilaian-mk', 'BobotPenilaianMk::index');


// rumusan nilai alhir mk
$routes->get('rumusan-akhir-mk', 'RumusanAkhirMK::index');


//rumusan nilai akhir cpl
$routes->get('rumusan-akhir-cpl', 'RumusanAkhirCpl::index');


//referensi
$routes->get('rps/referensi/(:num)', 'Rps::referensi/$1');
$routes->get('rps/referensi-create/(:num)', 'Rps::referensi_create/$1');
$routes->post('rps/referensi-store/(:num)', 'Rps::referensi_store/$1');
$routes->get('rps/referensi-edit/(:num)', 'Rps::referensi_edit/$1');
$routes->post('rps/referensi-update/(:num)', 'Rps::referensi_update/$1');
$routes->post('rps/referensi-delete/(:num)', 'Rps::referensi_delete/$1');


//rencana mingguan
$routes->get('rps/mingguan/(:num)', 'Rps::mingguan/$1');
$routes->get('rps/mingguan-create/(:num)', 'Rps::mingguan_create/$1');
$routes->post('rps/mingguan-store/(:num)', 'Rps::mingguan_store/$1');
$routes->get('rps/mingguan-edit/(:num)', 'Rps::mingguan_edit/$1');
$routes->post('rps/mingguan-update/(:num)', 'Rps::mingguan_update/$1');
$routes->post('rps/mingguan-delete/(:num)', 'Rps::mingguan_delete/$1');

//ajax
$routes->get('rps/get-cpmk/(:num)/(:num)', 'Rps::get_cpmk/$1/$2');
$routes->get('rps/get-subcpmk/(:num)/(:num)', 'Rps::get_subcpmk/$1/$2');
$routes->get('ajax/cpmk-by-cpl/(:num)', 'Ajax::cpmkByCpl/$1');
$routes->get('ajax/subcpmk-by-cpmk/(:num)', 'Ajax::subcpmkByCpmk/$1');
$routes->get('ajax/mk-by-cpl/(:num)', 'Ajax::mkByCpl/$1');
$routes->get('admin/ajax/mk-by-cpmk/(:num)', 'Ajax::mkByCpmk/$1');
$routes->get('ajax/mk-by-cpmk/(:num)', 'Ajax::mkByCpmk/$1');
$routes->get('/ajax/cpmk/by-cpl-mk/(:num)/(:num)', 'Ajax::cpmkByCplMk/$1/$2');
$routes->get('/ajax/subcpmk/by-cpmk-cpl-mk/(:num)/(:num)/(:num)', 'Ajax::subcpmkByCpmkCplMk/$1/$2/$3');


//rps
$routes->get('rps', 'Rps::index');
$routes->get('rps/create', 'Rps::create');
$routes->post('rps/store', 'Rps::store');
$routes->get('rps/edit/(:num)', 'Rps::edit/$1');
$routes->post('rps/update/(:num)', 'Rps::update/$1');
$routes->post('rps/delete/(:num)', 'Rps::delete/$1');
$routes->get('rps/preview/(:num)', 'Rps::preview/$1');


// PROFIL PRODI
$routes->get('admin/profil-prodi/edit/(:num)', 'Admin\ProfilProdi::edit/$1');
$routes->post('admin/profil-prodi/update/(:num)', 'Admin\ProfilProdi::update/$1');

// PROFIL MANAJEMEN
$routes->group('admin', [
	'namespace' => 'App\Controllers\Admin',
	'filter'    => 'auth:admin'
], function ($routes) {
	$routes->get('user', 'User::index');
	$routes->get('user/create', 'User::create');
	$routes->post('user/store', 'User::store');
	$routes->get('user/edit/(:num)', 'User::edit/$1');
	$routes->post('user/update/(:num)', 'User::update/$1');
	$routes->post('user/delete/(:num)', 'User::delete/$1');
});

// //EXPORT
$routes->get('admin/bkmk/export/matriks', 'Admin\BkMk::exportExcel');
$routes->get('teknik-penilaian-cpmk/export/pdf', 'TeknikPenilaianCpmk::exportPdf');
$routes->get('teknik-penilaian-cpmk/export/excel', 'TeknikPenilaianCpmk::exportExcel');
$routes->get('tahap-mekanisme-penilaian/export/excel', 'TahapMekanismePenilaian::exportExcel');
$routes->get('tahap-mekanisme-penilaian/export/pdf', 'TahapMekanismePenilaian::exportPdf');
$routes->get('bobot-penilaian-cpl/export/excel', 'BobotPenilaianCpl::exportExcel');
$routes->get('bobot-penilaian-cpl/export/pdf', 'BobotPenilaianCpl::exportPdf');
$routes->get('bobot-mk/export/excel', 'BobotPenilaianMk::exportExcel');
$routes->get('bobot-mk/export/pdf', 'BobotPenilaianMk::exportPdf');
$routes->get('rumusan-akhir-mk/export/excel', 'RumusanAkhirMK::exportExcel');
$routes->get('rumusan-akhir-mk/export/pdf', 'RumusanAkhirMK::exportPdf');
$routes->get('rumusan-akhir-cpl/export/excel', 'RumusanAkhirCpl::exportExcel');
$routes->get('rumusan-akhir-cpl/export/pdf', 'RumusanAkhirCpl::exportPdf');

$routes->get('admin/profil-lulusan/exportExcel', 'Admin\ProfilLulusan::exportExcel');
$routes->get('admin/profil-lulusan/exportPdf', 'Admin\ProfilLulusan::exportPdf');

$routes->get('admin/bahan-kajian/exportExcel', 'Admin\BahanKajian::exportExcel');
$routes->get('admin/bahan-kajian/exportPdf', 'Admin\BahanKajian::exportPdf');

$routes->get('admin/cpl/exportExcel', 'Admin\Cpl::exportExcel');
$routes->get('admin/cpl/exportPdf', 'Admin\Cpl::exportPdf');

$routes->get('admin/mata-kuliah/exportExcel', 'Admin\MataKuliah::exportExcel');
$routes->get('admin/mata-kuliah/exportPdf', 'Admin\MataKuliah::exportPdf');

$routes->get('admin/cpmk/exportExcel', 'Admin\Cpmk::exportExcel');
$routes->get('admin/cpmk/exportPdf', 'Admin\Cpmk::exportPdf');

$routes->get('admin/cpl-pl/exportExcel', 'Admin\CplPl::exportExcel');
$routes->get('admin/cpl-pl/exportPdf', 'Admin\CplPl::exportPdf');

$routes->get('admin/cpl-bk/exportExcel', 'Admin\CplBk::exportExcel');
$routes->get('admin/cpl-bk/exportPdf', 'Admin\CplBk::exportPdf');

$routes->get('admin/cpl-mk/exportExcel', 'Admin\CplMk::exportExcel');
$routes->get('admin/cpl-mk/exportPdf', 'Admin\CplMk::exportPdf');

$routes->get('admin/peta-cpl/exportExcel', 'Admin\PetaCPL::exportExcel');
$routes->get('admin/peta-cpl/exportPdf', 'Admin\PetaCPL::exportPdf');

$routes->get('rps/export/pdf/(:num)', 'Rps::exportPdf/$1');
$routes->get('rps/export/doc/(:num)', 'Rps::exportDoc/$1');

$routes->get('admin/organisasi-mk/exportExcel', 'Admin\OrganisasiMk::exportExcel');
$routes->get('admin/organisasi-mk/exportPdf', 'Admin\OrganisasiMk::exportPdf');

$routes->get('admin/cpl-bk-mk/exportExcel', 'Admin\CplBkMkView::exportExcel');
$routes->get('admin/cpl-bk-mk/exportPdf', 'Admin\CplBkMkView::exportPdf');

$routes->get('admin/cpl-cpmk-mk-per-semester/exportExcel', 'Admin\CplCpmkMkPerSemester::exportExcel');
$routes->get('admin/cpl-cpmk-mk-per-semester/exportPdf', 'Admin\CplCpmkMkPerSemester::exportPdf');

$routes->get('admin/bkmk/exportExcel', 'Admin\BkMk::exportExcel');
$routes->get('admin/bkmk/exportPdf', 'Admin\BkMk::exportPdf');
$routes->get('admin/mk-cpl-cpmk/export/excel', 'Admin\MkCplCpmk::export_excel');

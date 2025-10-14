<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class MbkmKomponenController extends BaseController
{
	protected $db;

	public function __construct()
	{
		$this->db = \Config\Database::connect();
	}

	// List all components for a specific activity type
	public function index($jenis_kegiatan_id)
	{
		$jenis = $this->db->table('mbkm_jenis_kegiatan')
			->where('id', $jenis_kegiatan_id)
			->get()
			->getRowArray();

		if (!$jenis) {
			return redirect()->to('/admin/mbkm-jenis')->with('error', 'Jenis kegiatan tidak ditemukan');
		}

		$komponen = $this->db->table('mbkm_komponen_nilai')
			->where('jenis_kegiatan_id', $jenis_kegiatan_id)
			->orderBy('id', 'ASC')
			->get()
			->getResultArray();

		// Calculate total weight
		$totalBobot = array_sum(array_column($komponen, 'bobot'));

		$data = [
			'jenis' => $jenis,
			'komponen' => $komponen,
			'total_bobot' => $totalBobot
		];

		return view('admin/mbkm_komponen/index', $data);
	}

	// Show create form
	public function create($jenis_kegiatan_id)
	{
		$jenis = $this->db->table('mbkm_jenis_kegiatan')
			->where('id', $jenis_kegiatan_id)
			->get()
			->getRowArray();

		if (!$jenis) {
			return redirect()->to('/admin/mbkm-jenis')->with('error', 'Jenis kegiatan tidak ditemukan');
		}

		// Calculate remaining weight
		$usedWeight = $this->db->table('mbkm_komponen_nilai')
			->selectSum('bobot')
			->where('jenis_kegiatan_id', $jenis_kegiatan_id)
			->get()
			->getRow()
			->bobot ?? 0;

		$remainingWeight = 100 - $usedWeight;

		$data = [
			'jenis' => $jenis,
			'remaining_weight' => $remainingWeight
		];

		return view('admin/mbkm_komponen/create', $data);
	}

	// Store new component
	public function store()
	{
		$validation = \Config\Services::validation();

		$rules = [
			'jenis_kegiatan_id' => 'required|integer',
			'nama_komponen' => 'required|max_length[100]',
			'bobot' => 'required|decimal|greater_than[0]',
		];

		if (!$this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $validation->getErrors());
		}

		$jenisKegiatanId = $this->request->getPost('jenis_kegiatan_id');
		$bobot = $this->request->getPost('bobot');

		// Check total weight doesn't exceed 100
		$usedWeight = $this->db->table('mbkm_komponen_nilai')
			->selectSum('bobot')
			->where('jenis_kegiatan_id', $jenisKegiatanId)
			->get()
			->getRow()
			->bobot ?? 0;

		if (($usedWeight + $bobot) > 100) {
			return redirect()->back()->withInput()
				->with('error', 'Total bobot akan melebihi 100%. Sisa bobot: ' . (100 - $usedWeight) . '%');
		}

		$data = [
			'jenis_kegiatan_id' => $jenisKegiatanId,
			'nama_komponen' => $this->request->getPost('nama_komponen'),
			'bobot' => $bobot,
			'deskripsi' => $this->request->getPost('deskripsi')
		];

		if ($this->db->table('mbkm_komponen_nilai')->insert($data)) {
			return redirect()->to('/admin/mbkm-komponen/' . $jenisKegiatanId)
				->with('success', 'Komponen penilaian berhasil ditambahkan');
		}

		return redirect()->back()->withInput()->with('error', 'Gagal menambahkan komponen penilaian');
	}

	// Show edit form
	public function edit($id)
	{
		$komponen = $this->db->table('mbkm_komponen_nilai k')
			->select('k.*, j.nama_kegiatan')
			->join('mbkm_jenis_kegiatan j', 'j.id = k.jenis_kegiatan_id')
			->where('k.id', $id)
			->get()
			->getRowArray();

		if (!$komponen) {
			return redirect()->back()->with('error', 'Komponen tidak ditemukan');
		}

		// Calculate remaining weight (excluding current component)
		$usedWeight = $this->db->table('mbkm_komponen_nilai')
			->selectSum('bobot')
			->where('jenis_kegiatan_id', $komponen['jenis_kegiatan_id'])
			->where('id !=', $id)
			->get()
			->getRow()
			->bobot ?? 0;

		$remainingWeight = 100 - $usedWeight;

		$data = [
			'komponen' => $komponen,
			'remaining_weight' => $remainingWeight
		];

		return view('admin/mbkm_komponen/edit', $data);
	}

	// Update component
	public function update($id)
	{
		$validation = \Config\Services::validation();

		$rules = [
			'nama_komponen' => 'required|max_length[100]',
			'bobot' => 'required|decimal|greater_than[0]',
		];

		if (!$this->validate($rules)) {
			return redirect()->back()->withInput()->with('errors', $validation->getErrors());
		}

		$komponen = $this->db->table('mbkm_komponen_nilai')
			->where('id', $id)
			->get()
			->getRowArray();

		if (!$komponen) {
			return redirect()->back()->with('error', 'Komponen tidak ditemukan');
		}

		$bobot = $this->request->getPost('bobot');

		// Check total weight doesn't exceed 100
		$usedWeight = $this->db->table('mbkm_komponen_nilai')
			->selectSum('bobot')
			->where('jenis_kegiatan_id', $komponen['jenis_kegiatan_id'])
			->where('id !=', $id)
			->get()
			->getRow()
			->bobot ?? 0;

		if (($usedWeight + $bobot) > 100) {
			return redirect()->back()->withInput()
				->with('error', 'Total bobot akan melebihi 100%. Sisa bobot: ' . (100 - $usedWeight) . '%');
		}

		$data = [
			'nama_komponen' => $this->request->getPost('nama_komponen'),
			'bobot' => $bobot,
			'deskripsi' => $this->request->getPost('deskripsi')
		];

		if ($this->db->table('mbkm_komponen_nilai')->where('id', $id)->update($data)) {
			return redirect()->to('/admin/mbkm-komponen/' . $komponen['jenis_kegiatan_id'])
				->with('success', 'Komponen penilaian berhasil diperbarui');
		}

		return redirect()->back()->withInput()->with('error', 'Gagal memperbarui komponen penilaian');
	}

	// Delete component
	public function delete($id)
	{
		$komponen = $this->db->table('mbkm_komponen_nilai')
			->where('id', $id)
			->get()
			->getRowArray();

		if (!$komponen) {
			return redirect()->back()->with('error', 'Komponen tidak ditemukan');
		}

		// Check if used in any scoring
		$used = $this->db->table('mbkm_nilai')
			->where('komponen_id', $id)
			->countAllResults();

		if ($used > 0) {
			return redirect()->back()
				->with('error', "Komponen tidak dapat dihapus karena sudah digunakan dalam {$used} penilaian");
		}

		if ($this->db->table('mbkm_komponen_nilai')->where('id', $id)->delete()) {
			return redirect()->to('/admin/mbkm-komponen/' . $komponen['jenis_kegiatan_id'])
				->with('success', 'Komponen penilaian berhasil dihapus');
		}

		return redirect()->back()->with('error', 'Gagal menghapus komponen penilaian');
	}
}

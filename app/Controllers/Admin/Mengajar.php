<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

class Mengajar extends BaseController
{
	protected $db;

	public function __construct()
	{
		$this->db = \Config\Database::connect();
	}

	public function index()
	{
		// Get filters from query parameters
		$filters = [
			'program_studi' => $this->request->getGet('program_studi'),
			'tahun_akademik' => $this->request->getGet('tahun_akademik')
		];

		// Build query
		$builder = $this->db->table('jadwal_mengajar jm');
		$builder->select('jm.*, mk.kode_mk, mk.nama_mk, mk.semester, mk.sks');
		$builder->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id');

		// Apply filters
		if (!empty($filters['program_studi'])) {
			$builder->where('jm.program_studi', $filters['program_studi']);
		}
		if (!empty($filters['tahun_akademik'])) {
			// MODIFIED: Use 'like' for partial "starts with" matching
			$builder->like('jm.tahun_akademik', $filters['tahun_akademik'], 'after');
		}

		// Fetch ALL results, no pagination
		$jadwal_list = $builder->orderBy('jm.jam_mulai', 'ASC') // Order by time within each day
			->get()
			->getResultArray();

		// dd($jadwal_list);

		// Get dosen information for each jadwal
		foreach ($jadwal_list as &$jadwal) {
			$dosenBuilder = $this->db->table('jadwal_dosen jd');
			$dosenBuilder->select('d.id, d.nama_lengkap, jd.role');
			$dosenBuilder->join('dosen d', 'd.id = jd.dosen_id');
			$dosenBuilder->where('jd.jadwal_mengajar_id', $jadwal['id']);
			$dosenBuilder->orderBy('jd.role', 'DESC');
			$jadwal['dosen_list'] = $dosenBuilder->get()->getResultArray();
		}

		unset($jadwal); // This breaks the reference to the last element.

		// Group schedules by day for the Kanban view
		$jadwal_by_day = [
			'Senin' => [],
			'Selasa' => [],
			'Rabu' => [],
			'Kamis' => [],
			'Jumat' => [],
			'Sabtu' => [],
			'Belum Diatur' => []
		];

		foreach ($jadwal_list as $jadwal) {
			$day = $jadwal['hari'] ?: 'Belum Diatur';
			$jadwal_by_day[$day][] = $jadwal;
		}

		// Get distinct tahun akademik for filter dropdown
		$tahun_akademik_list = $this->db->table('jadwal_mengajar')
			->distinct()
			->select('tahun_akademik')
			->orderBy('tahun_akademik', 'DESC')
			->get()
			->getResultArray();

		// Get distinct program studi for filter dropdown
		$program_studi_list = $this->db->table('jadwal_mengajar')
			->distinct()
			->select('program_studi')
			->orderBy('program_studi', 'ASC')
			->get()
			->getResultArray();

		$data = [
			'title' => 'Jadwal Mengajar (Tampilan Papan)',
			'jadwal_by_day' => $jadwal_by_day,
			'filters' => $filters,
			'total_jadwal' => count($jadwal_list),
			'tahun_akademik_list' => array_column($tahun_akademik_list, 'tahun_akademik'),
			'program_studi_list' => array_column($program_studi_list, 'program_studi')
		];

		// Note: We are not sending a $pager object anymore
		return view('mengajar/index', $data); // The same view file will be modified
	}

	public function create()
	{
		// Get active dosen
		$dosen_list = $this->db->table('dosen')
			->where('status_keaktifan', 'Aktif')
			->orderBy('nama_lengkap', 'ASC')
			->get()
			->getResultArray();

		// Get all mata kuliah
		$mata_kuliah_list = $this->db->table('mata_kuliah')
			->orderBy('semester', 'ASC')
			->orderBy('kode_mk', 'ASC')
			->get()
			->getResultArray();

		// Get distinct tahun akademik for suggestions
		// CORRECTED QUERY:
		$tahun_akademik_list = $this->db->table('jadwal_mengajar')
			->distinct() // Use the distinct() method
			->select('tahun_akademik') // Select only the column name
			->orderBy('tahun_akademik', 'DESC')
			->get()
			->getResultArray();

		$data = [
			'title' => 'Tambah Jadwal Mengajar',
			'dosen_list' => $dosen_list,
			'mata_kuliah_list' => $mata_kuliah_list,
			'tahun_akademik_list' => array_column($tahun_akademik_list, 'tahun_akademik')
		];

		return view('mengajar/create', $data);
	}

	public function store()
	{
		$rules = [
			'mata_kuliah_id' => 'required|integer',
			'program_studi' => 'required|in_list[Teknik Informatika,Sistem Informasi,Teknik Komputer]',
			'tahun_akademik' => 'required',
			'kelas' => 'required|max_length[5]',
			'ruang' => 'permit_empty|max_length[20]',
			'hari' => 'permit_empty|in_list[Senin,Selasa,Rabu,Kamis,Jumat,Sabtu]',
			'jam_mulai' => 'permit_empty',
			'jam_selesai' => 'permit_empty',
			'dosen_leader' => 'required|integer',
			'dosen_members.*' => 'permit_empty|integer'
		];

		if (!$this->validate($rules)) {
			return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
		}

		$mata_kuliah_id = $this->request->getPost('mata_kuliah_id');
		$program_studi = $this->request->getPost('program_studi');
		$tahun_akademik = $this->request->getPost('tahun_akademik');
		$kelas = $this->request->getPost('kelas');
		$ruang = $this->request->getPost('ruang');
		$hari = $this->request->getPost('hari');
		$jam_mulai = $this->request->getPost('jam_mulai');
		$jam_selesai = $this->request->getPost('jam_selesai');
		$dosen_leader = $this->request->getPost('dosen_leader');
		$dosen_members = array_filter($this->request->getPost('dosen_members') ?? []);

		// Validation
		if (in_array($dosen_leader, $dosen_members)) {
			return redirect()->back()->withInput()->with('error', 'Dosen ketua tidak boleh sama dengan dosen anggota.');
		}

		if (count($dosen_members) !== count(array_unique($dosen_members))) {
			return redirect()->back()->withInput()->with('error', 'Dosen anggota tidak boleh ada yang sama.');
		}

		try {
			$this->db->transStart();

			// Check if jadwal exists
			$existing = $this->db->table('jadwal_mengajar')
				->where([
					'mata_kuliah_id' => $mata_kuliah_id,
					'tahun_akademik' => $tahun_akademik,
					'kelas' => $kelas
				])->countAllResults();

			if ($existing > 0) {
				return redirect()->back()->withInput()->with('error', 'Jadwal untuk mata kuliah ini sudah ada.');
			}

			// Insert jadwal
			$jadwalData = [
				'mata_kuliah_id' => $mata_kuliah_id,
				'program_studi' => $program_studi,
				'tahun_akademik' => $tahun_akademik,
				'kelas' => $kelas,
				'ruang' => $ruang ?: null,
				'hari' => $hari ?: null,
				'jam_mulai' => $jam_mulai ?: null,
				'jam_selesai' => $jam_selesai ?: null,
				'status' => 'active'
			];

			$this->db->table('jadwal_mengajar')->insert($jadwalData);
			$jadwal_id = $this->db->insertID();

			// Insert dosen leader
			$this->db->table('jadwal_dosen')->insert([
				'jadwal_mengajar_id' => $jadwal_id,
				'dosen_id' => $dosen_leader,
				'role' => 'leader'
			]);

			// Insert dosen members
			foreach ($dosen_members as $member_id) {
				if ($member_id) {
					$this->db->table('jadwal_dosen')->insert([
						'jadwal_mengajar_id' => $jadwal_id,
						'dosen_id' => $member_id,
						'role' => 'member'
					]);
				}
			}

			$this->db->transComplete();

			if ($this->db->transStatus() === false) {
				return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
			}

			return redirect()->to(base_url('admin/mengajar'))->with('success', 'Jadwal mengajar berhasil ditambahkan.');
		} catch (\Exception $e) {
			$this->db->transRollback();
			return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
		}
	}

	public function show($id)
	{
		// Get schedule data with course info
		$jadwalBuilder = $this->db->table('jadwal_mengajar jm');
		$jadwalBuilder->select('jm.*, mk.kode_mk, mk.nama_mk, mk.semester, mk.sks');
		$jadwalBuilder->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id');
		$jadwalBuilder->where('jm.id', $id);
		$jadwal = $jadwalBuilder->get()->getRowArray();

		if (!$jadwal) {
			// For AJAX requests, return a JSON error. Otherwise, redirect.
			if ($this->request->isAJAX()) {
				return $this->response->setStatusCode(404)->setJSON(['error' => 'Jadwal tidak ditemukan.']);
			}
			return redirect()->to(base_url('admin/mengajar'))->with('error', 'Jadwal tidak ditemukan.');
		}

		// Get assigned lecturers for this schedule
		$dosenBuilder = $this->db->table('jadwal_dosen jd');
		$dosenBuilder->select('d.nama_lengkap, jd.role');
		$dosenBuilder->join('dosen d', 'd.id = jd.dosen_id');
		$dosenBuilder->where('jd.jadwal_mengajar_id', $id);
		$dosenBuilder->orderBy('jd.role', 'DESC'); // Leader first
		$dosenBuilder->orderBy('d.nama_lengkap', 'ASC');
		$jadwal['dosen_list'] = $dosenBuilder->get()->getResultArray();

		$data = [
			'title' => 'Detail Jadwal Mengajar',
			'jadwal' => $jadwal
		];

		// Check if it's an AJAX request
		if ($this->request->isAJAX()) {
			// If yes, return the data as JSON
			return $this->response->setJSON($data);
		}

		// Otherwise, load the regular view (optional fallback)
		return view('mengajar/show', $data);
	}

	public function edit($id)
	{
		// Get jadwal data
		$jadwalBuilder = $this->db->table('jadwal_mengajar jm');
		$jadwalBuilder->select('jm.*, mk.kode_mk, mk.nama_mk, mk.semester, mk.sks');
		$jadwalBuilder->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id');
		$jadwalBuilder->where('jm.id', $id);
		$jadwal = $jadwalBuilder->get()->getRowArray();

		if (!$jadwal) {
			return redirect()->to(base_url('admin/mengajar'))->with('error', 'Jadwal tidak ditemukan.');
		}

		// Get dosen assignments
		$dosenBuilder = $this->db->table('jadwal_dosen jd');
		$dosenBuilder->select('jd.*, d.nama_lengkap');
		$dosenBuilder->join('dosen d', 'd.id = jd.dosen_id');
		$dosenBuilder->where('jd.jadwal_mengajar_id', $id);
		$dosenAssigned = $dosenBuilder->get()->getResultArray();

		$jadwal['dosen_leader'] = null;
		$jadwal['dosen_members'] = [];

		foreach ($dosenAssigned as $dosen) {
			if ($dosen['role'] === 'leader') {
				$jadwal['dosen_leader'] = $dosen['dosen_id'];
			} else {
				$jadwal['dosen_members'][] = $dosen['dosen_id'];
			}
		}

		// Get all active dosen
		$dosen_list = $this->db->table('dosen')
			->where('status_keaktifan', 'Aktif')
			->orderBy('nama_lengkap', 'ASC')
			->get()
			->getResultArray();

		// Get all mata kuliah
		$mata_kuliah_list = $this->db->table('mata_kuliah')
			->orderBy('semester', 'ASC')
			->orderBy('kode_mk', 'ASC')
			->get()
			->getResultArray();

		$data = [
			'title' => 'Edit Jadwal Mengajar',
			'jadwal' => $jadwal,
			'dosen_list' => $dosen_list,
			'mata_kuliah_list' => $mata_kuliah_list
		];

		return view('mengajar/edit', $data);
	}

	public function update($id)
	{
		$rules = [
			'mata_kuliah_id' => 'required|integer',
			'program_studi' => 'required|in_list[Teknik Informatika,Sistem Informasi,Teknik Komputer]',
			'tahun_akademik' => 'required',
			'kelas' => 'required|max_length[5]',
			'ruang' => 'permit_empty|max_length[20]',
			'hari' => 'permit_empty|in_list[Senin,Selasa,Rabu,Kamis,Jumat,Sabtu]',
			'jam_mulai' => 'permit_empty',
			'jam_selesai' => 'permit_empty',
			'dosen_leader' => 'required|integer',
			'dosen_members.*' => 'permit_empty|integer'
		];

		if (!$this->validate($rules)) {
			return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
		}

		$mata_kuliah_id = $this->request->getPost('mata_kuliah_id');
		$program_studi = $this->request->getPost('program_studi');
		$tahun_akademik = $this->request->getPost('tahun_akademik');
		$kelas = $this->request->getPost('kelas');
		$ruang = $this->request->getPost('ruang');
		$hari = $this->request->getPost('hari');
		$jam_mulai = $this->request->getPost('jam_mulai');
		$jam_selesai = $this->request->getPost('jam_selesai');
		$dosen_leader = $this->request->getPost('dosen_leader');
		$dosen_members = array_filter($this->request->getPost('dosen_members') ?? []);

		// Validation
		if (in_array($dosen_leader, $dosen_members)) {
			return redirect()->back()->withInput()->with('error', 'Dosen ketua tidak boleh sama dengan dosen anggota.');
		}

		if (count($dosen_members) !== count(array_unique($dosen_members))) {
			return redirect()->back()->withInput()->with('error', 'Dosen anggota tidak boleh ada yang sama.');
		}

		try {
			$this->db->transStart();

			// Check if jadwal exists (excluding current)
			$existing = $this->db->table('jadwal_mengajar')
				->where([
					'mata_kuliah_id' => $mata_kuliah_id,
					'tahun_akademik' => $tahun_akademik,
					'kelas' => $kelas
				])
				->where('id !=', $id)
				->countAllResults();

			if ($existing > 0) {
				return redirect()->back()->withInput()->with('error', 'Jadwal untuk mata kuliah ini sudah ada.');
			}

			// Update jadwal
			$jadwalData = [
				'mata_kuliah_id' => $mata_kuliah_id,
				'program_studi' => $program_studi,
				'tahun_akademik' => $tahun_akademik,
				'kelas' => $kelas,
				'ruang' => $ruang ?: null,
				'hari' => $hari ?: null,
				'jam_mulai' => $jam_mulai ?: null,
				'jam_selesai' => $jam_selesai ?: null
			];

			$this->db->table('jadwal_mengajar')->where('id', $id)->update($jadwalData);

			// Delete existing dosen assignments
			$this->db->table('jadwal_dosen')->where('jadwal_mengajar_id', $id)->delete();

			// Insert new dosen leader
			$this->db->table('jadwal_dosen')->insert([
				'jadwal_mengajar_id' => $id,
				'dosen_id' => $dosen_leader,
				'role' => 'leader'
			]);

			// Insert new dosen members
			foreach ($dosen_members as $member_id) {
				if ($member_id) {
					$this->db->table('jadwal_dosen')->insert([
						'jadwal_mengajar_id' => $id,
						'dosen_id' => $member_id,
						'role' => 'member'
					]);
				}
			}

			$this->db->transComplete();

			if ($this->db->transStatus() === false) {
				return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data.');
			}

			return redirect()->to(base_url('admin/mengajar'))->with('success', 'Jadwal mengajar berhasil diperbarui.');
		} catch (\Exception $e) {
			$this->db->transRollback();
			return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
		}
	}

	public function delete($id)
	{
		try {
			// Delete will cascade to jadwal_dosen
			$this->db->table('jadwal_mengajar')->where('id', $id)->delete();
			return redirect()->to(base_url('admin/mengajar'))->with('success', 'Jadwal mengajar berhasil dihapus.');
		} catch (\Exception $e) {
			return redirect()->to(base_url('admin/mengajar'))->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
		}
	}

	// Add this private method inside your Mengajar controller class
	private function _getFilteredJadwalData()
	{
		// Get filters from query parameters
		$filters = [
			'program_studi' => $this->request->getGet('program_studi'),
			'tahun_akademik' => $this->request->getGet('tahun_akademik')
		];

		// Build query
		$builder = $this->db->table('jadwal_mengajar jm');
		$builder->select('jm.*, mk.kode_mk, mk.nama_mk, mk.semester, mk.sks');
		$builder->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id');

		// Apply filters
		if (!empty($filters['program_studi'])) {
			$builder->where('jm.program_studi', $filters['program_studi']);
		}
		if (!empty($filters['tahun_akademik'])) {
			// MODIFIED: Use 'like' for partial "starts with" matching
			$builder->like('jm.tahun_akademik', $filters['tahun_akademik'], 'after');
		}

		$jadwal_list = $builder->orderBy('jm.tahun_akademik', 'DESC')
			->orderBy('mk.semester', 'ASC')
			->orderBy('mk.kode_mk', 'ASC')
			->get()
			->getResultArray();

		// Get dosen information for each jadwal
		foreach ($jadwal_list as &$jadwal) {
			$dosenBuilder = $this->db->table('jadwal_dosen jd');
			$dosenBuilder->select('d.nama_lengkap, jd.role');
			$dosenBuilder->join('dosen d', 'd.id = jd.dosen_id');
			$dosenBuilder->where('jd.jadwal_mengajar_id', $jadwal['id']);
			$dosenBuilder->orderBy('jd.role', 'DESC');
			$dosenBuilder->orderBy('d.nama_lengkap', 'ASC');
			$jadwal['dosen_list'] = $dosenBuilder->get()->getResultArray();
		}

		return $jadwal_list;
	}

	public function exportExcel()
	{
		$jadwal_list = $this->_getFilteredJadwalData();

		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		// Set Headers
		$sheet->setCellValue('A1', 'No');
		$sheet->setCellValue('B1', 'Program Studi');
		$sheet->setCellValue('C1', 'Tahun Akademik');
		$sheet->setCellValue('D1', 'Kode MK');
		$sheet->setCellValue('E1', 'Nama Mata Kuliah');
		$sheet->setCellValue('F1', 'SMT');
		$sheet->setCellValue('G1', 'SKS');
		$sheet->setCellValue('H1', 'Kelas');
		$sheet->setCellValue('I1', 'Hari');
		$sheet->setCellValue('J1', 'Waktu');
		$sheet->setCellValue('K1', 'Ruang');
		$sheet->setCellValue('L1', 'Dosen Pengampu');

		// Populate Data
		$row = 2;
		foreach ($jadwal_list as $key => $jadwal) {
			$dosen_pengampu = [];
			foreach ($jadwal['dosen_list'] as $dosen) {
				$dosen_pengampu[] = $dosen['nama_lengkap'] . ($dosen['role'] == 'leader' ? ' (Ketua)' : '');
			}

			$sheet->setCellValue('A' . $row, $key + 1);
			$sheet->setCellValue('B' . $row, $jadwal['program_studi']);
			$sheet->setCellValue('C' . $row, $jadwal['tahun_akademik']);
			$sheet->setCellValue('D' . $row, $jadwal['kode_mk']);
			$sheet->setCellValue('E' . $row, $jadwal['nama_mk']);
			$sheet->setCellValue('F' . $row, $jadwal['semester']);
			$sheet->setCellValue('G' . $row, $jadwal['sks']);
			$sheet->setCellValue('H' . $row, $jadwal['kelas']);
			$sheet->setCellValue('I' . $row, $jadwal['hari']);
			$sheet->setCellValue('J' . $row, (!empty($jadwal['jam_mulai']) ? date('H:i', strtotime($jadwal['jam_mulai'])) . '-' . date('H:i', strtotime($jadwal['jam_selesai'])) : ''));
			$sheet->setCellValue('K' . $row, $jadwal['ruang']);
			$sheet->setCellValue('L' . $row, implode(", ", $dosen_pengampu));
			$row++;
		}

		// Set Headers for download
		$filename = 'jadwal_mengajar_' . date('YmdHis') . '.xlsx';
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$writer = new Xlsx($spreadsheet);
		$writer->save('php://output');
		exit();
	}

	public function exportPdf()
	{
		$data['jadwal_list'] = $this->_getFilteredJadwalData();
		$filename = 'jadwal_mengajar_' . date('YmdHis');

		// instantiate and use the dompdf class
		$options = new Options();
		$options->set('isRemoteEnabled', TRUE);
		$dompdf = new Dompdf($options);

		// load HTML content
		$dompdf->loadHtml(view('mengajar/export_pdf', $data));

		// (Optional) Setup the paper size and orientation
		$dompdf->setPaper('A4', 'landscape');

		// Render the HTML as PDF
		$dompdf->render();

		// Output the generated PDF to Browser
		$dompdf->stream($filename, ['Attachment' => 1]); // 1 to force download, 0 to preview
		exit();
	}
}

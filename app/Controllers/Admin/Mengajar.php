<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\TahunAkademikModel;
use App\Models\KelasModel;
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
		$isDosen   = session()->get('role') === 'dosen';
		$dosenId   = session()->get('dosen_id');

		// Get filters from query parameters; default program_studi to Teknik Informatika (kode 58)
		$filters = [
			'program_studi_kode' => $this->request->getGet('program_studi_kode') ?: ($isDosen ? null : 58),
			'tahun'              => $this->request->getGet('tahun'),
			'semester'           => $this->request->getGet('semester'),
		];

		// Build query
		$builder = $this->db->table('jadwal jm');
		$builder->select('jm.*, mk.kode_mk, mk.nama_mk, mk.semester, mk.sks, ps.nama_resmi as program_studi_nama');
		$builder->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id');
		$builder->join('program_studi ps', 'ps.kode = jm.program_studi_kode', 'left');

		// Exclude MBKM (Merdeka) classes
		$builder->where('jm.kelas !=', 'KM');

		// If logged-in user is a dosen, only show their own jadwal
		if ($isDosen && $dosenId) {
			$builder->where("jm.id IN (SELECT jadwal_id FROM jadwal_dosen WHERE dosen_id = " . (int)$dosenId . ")", null, false);
		}

		// Apply filters
		if (!empty($filters['program_studi_kode'])) {
			$builder->where('jm.program_studi_kode', $filters['program_studi_kode']);
		}
		if (!empty($filters['tahun'])) {
			$builder->like('jm.tahun_akademik', $filters['tahun'], 'after');
		}
		if (!empty($filters['semester'])) {
			$builder->like('jm.tahun_akademik', $filters['semester'], 'before');
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
			$dosenBuilder->where('jd.jadwal_id', $jadwal['id']);
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

		// Get tahun and semester lists separately for filter dropdowns
		$tahunAkademikModel  = new TahunAkademikModel();
		$tahun_akademik_rows = $tahunAkademikModel->getAllForDisplay();
		$tahun_list          = array_values(array_unique(array_column($tahun_akademik_rows, 'tahun')));
		$semester_list       = ['Ganjil', 'Genap', 'Antara'];

		// Get program studi list for filter dropdown
		$program_studi_list = $this->db->table('program_studi')
			->orderBy('nama_resmi', 'ASC')
			->get()
			->getResultArray();

		$data = [
			'title'          => 'Jadwal Mengajar (Tampilan Papan)',
			'jadwal_by_day'  => $jadwal_by_day,
			'filters'        => $filters,
			'total_jadwal'   => count($jadwal_list),
			'tahun_list'     => $tahun_list,
			'semester_list'  => $semester_list,
			'program_studi_list' => $program_studi_list,
			'is_dosen'       => $isDosen,
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

		// Get tahun akademik from master table (active only)
		$tahunAkademikModel  = new TahunAkademikModel();
		$tahun_akademik_rows = $tahunAkademikModel->getActive();
		$tahun_akademik_list = array_map(
			fn($r) => $r['tahun'] . ' ' . $r['semester'],
			$tahun_akademik_rows
		);

		// Get program studi list
		$program_studi_list = $this->db->table('program_studi')
			->orderBy('nama_resmi', 'ASC')
			->get()
			->getResultArray();

		$data = [
			'title' => 'Tambah Jadwal Mengajar',
			'dosen_list' => $dosen_list,
			'mata_kuliah_list' => $mata_kuliah_list,
			'tahun_akademik_list' => $tahun_akademik_list,
			'program_studi_list' => $program_studi_list
		];

		return view('mengajar/create', $data);
	}

	public function store()
	{
		$rules = [
			'mata_kuliah_id' => 'required|integer',
			'program_studi_kode' => 'permit_empty|integer',
			'tahun_akademik' => 'required',
			'kelas' => 'required|max_length[5]',
			'ruang' => 'permit_empty|max_length[20]',
			'hari' => 'permit_empty|in_list[Senin,Selasa,Rabu,Kamis,Jumat,Sabtu]',
			'jam_mulai' => 'permit_empty',
			'jam_selesai' => 'permit_empty',
			'dosen_leader' => 'required|integer',
			'dosen_members.*' => 'permit_empty|integer',
			'kelas_id' => 'permit_empty|integer',
			'kelas_jenis' => 'permit_empty|max_length[50]',
			'kelas_semester' => 'permit_empty|integer',
			'mk_kurikulum_kode' => 'permit_empty|max_length[20]',
			'total_mahasiswa' => 'permit_empty|integer'
		];

		if (!$this->validate($rules)) {
			return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
		}

		$mata_kuliah_id = $this->request->getPost('mata_kuliah_id');
		$program_studi_kode = $this->request->getPost('program_studi_kode') ?: null;
		$tahun_akademik = $this->request->getPost('tahun_akademik');
		$kelas = $this->request->getPost('kelas');
		$ruang = $this->request->getPost('ruang');
		$hari = $this->request->getPost('hari');
		$jam_mulai = $this->request->getPost('jam_mulai');
		$jam_selesai = $this->request->getPost('jam_selesai');
		$dosen_leader = $this->request->getPost('dosen_leader');
		$dosen_members = array_filter($this->request->getPost('dosen_members') ?? []);
		$kelas_jenis = $this->request->getPost('kelas_jenis');
		$kelas_semester = $this->request->getPost('kelas_semester');
		$mk_kurikulum_kode = $this->request->getPost('mk_kurikulum_kode');

		// Validate that RPS exists for this mata kuliah
		$rps = $this->db->table('rps')
			->where('mata_kuliah_id', $mata_kuliah_id)
			->get()
			->getRowArray();

		if (!$rps) {
			return redirect()->back()->withInput()->with('error', 'RPS tidak ditemukan untuk mata kuliah ini. Harap buat RPS terlebih dahulu.');
		}

		try {
			$this->db->transStart();

			// Check if jadwal exists
			$existing = $this->db->table('jadwal')
				->where([
					'mata_kuliah_id' => $mata_kuliah_id,
					'program_studi_kode' => $program_studi_kode,
					'tahun_akademik' => $tahun_akademik,
					'kelas' => $kelas
				])->countAllResults();

			if ($existing > 0) {
				return redirect()->back()->withInput()->with('error', 'Jadwal untuk mata kuliah, program studi, tahun akademik, dan kelas ini sudah ada.');
			}

			// Insert jadwal
			$kelas_id = $this->request->getPost('kelas_id');
			$total_mahasiswa = $this->request->getPost('total_mahasiswa');

			$jadwalData = [
				'mata_kuliah_id' => $mata_kuliah_id,
				'program_studi_kode' => $program_studi_kode,
				'tahun_akademik' => $tahun_akademik,
				'kelas' => $kelas,
				'ruang' => $ruang ?: null,
				'hari' => $hari ?: null,
				'jam_mulai' => $jam_mulai ?: null,
				'jam_selesai' => $jam_selesai ?: null,
				'status' => 'active',
				'kelas_id' => $kelas_id ?: null,
				'kelas_jenis' => $kelas_jenis ?: null,
				'kelas_semester' => $kelas_semester ?: null,
				'mk_kurikulum_kode' => $mk_kurikulum_kode ?: null,
				'total_mahasiswa' => $total_mahasiswa ?: 0
			];

			$this->db->table('jadwal')->insert($jadwalData);
			$jadwal_id = $this->db->insertID();

			// Insert dosen leader
			$this->db->table('jadwal_dosen')->insert([
				'jadwal_id' => $jadwal_id,
				'dosen_id' => $dosen_leader,
				'role' => 'leader'
			]);

			// Insert dosen members
			foreach ($dosen_members as $member_id) {
				if ($member_id) {
					$this->db->table('jadwal_dosen')->insert([
						'jadwal_id' => $jadwal_id,
						'dosen_id' => $member_id,
						'role' => 'member'
					]);
				}
			}

			$this->db->transComplete();

			if ($this->db->transStatus() === false) {
				$error = $this->db->error();
				log_message('error', 'Failed to insert jadwal: ' . print_r($error, true));
				return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data. Error: ' . ($error['message'] ?? 'Unknown error'));
			}

			return redirect()->to(base_url('admin/mengajar'))->with('success', 'Jadwal mengajar berhasil ditambahkan.');
		} catch (\Exception $e) {
			$this->db->transRollback();
			log_message('error', 'Exception in store jadwal: ' . $e->getMessage());
			return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
		}
	}

	public function show($id)
	{
		// Get schedule data with course info
		$jadwalBuilder = $this->db->table('jadwal jm');
		$jadwalBuilder->select('jm.*, mk.kode_mk, mk.nama_mk, mk.semester, mk.sks, ps.nama_resmi as program_studi');
		$jadwalBuilder->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id');
		$jadwalBuilder->join('program_studi ps', 'ps.kode = jm.program_studi_kode', 'left');
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
		$dosenBuilder->where('jd.jadwal_id', $id);
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
		$jadwalBuilder = $this->db->table('jadwal jm');
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
		$dosenBuilder->where('jd.jadwal_id', $id);
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

		// Get program studi list
		$program_studi_list = $this->db->table('program_studi')
			->orderBy('nama_resmi', 'ASC')
			->get()
			->getResultArray();

		// Get tahun akademik from master table (active only)
		$tahunAkademikModel  = new TahunAkademikModel();
		$tahun_akademik_rows = $tahunAkademikModel->getActive();
		$tahun_akademik_list = array_map(
			fn($r) => $r['tahun'] . ' ' . $r['semester'],
			$tahun_akademik_rows
		);

		$data = [
			'title' => 'Edit Jadwal Mengajar',
			'jadwal' => $jadwal,
			'dosen_list' => $dosen_list,
			'mata_kuliah_list' => $mata_kuliah_list,
			'program_studi_list' => $program_studi_list,
			'tahun_akademik_list' => $tahun_akademik_list,
		];

		return view('mengajar/edit', $data);
	}

	public function update($id)
	{
		$rules = [
			'mata_kuliah_id' => 'required|integer',
			'program_studi_kode' => 'permit_empty|integer',
			'tahun_akademik' => 'required',
			'kelas' => 'required|max_length[5]',
			'ruang' => 'permit_empty|max_length[20]',
			'hari' => 'permit_empty|in_list[Senin,Selasa,Rabu,Kamis,Jumat,Sabtu]',
			'jam_mulai' => 'permit_empty',
			'jam_selesai' => 'permit_empty',
			'dosen_leader' => 'required|integer',
			'dosen_members.*' => 'permit_empty|integer',
			'kelas_id' => 'permit_empty|integer',
			'kelas_jenis' => 'permit_empty|max_length[50]',
			'total_mahasiswa' => 'permit_empty|integer',
			'kelas_semester' => 'permit_empty|integer',
			'mk_kurikulum_kode' => 'permit_empty|max_length[20]'
		];

		if (!$this->validate($rules)) {
			return redirect()->back()->withInput()->with('error', $this->validator->getErrors());
		}

		$mata_kuliah_id = $this->request->getPost('mata_kuliah_id');
		$program_studi_kode = $this->request->getPost('program_studi_kode') ?: null;
		$tahun_akademik = $this->request->getPost('tahun_akademik');
		$kelas = $this->request->getPost('kelas');
		$ruang = $this->request->getPost('ruang');
		$hari = $this->request->getPost('hari');
		$jam_mulai = $this->request->getPost('jam_mulai');
		$jam_selesai = $this->request->getPost('jam_selesai');
		$dosen_leader = $this->request->getPost('dosen_leader');
		$dosen_members = array_filter($this->request->getPost('dosen_members') ?? []);
		$kelas_jenis = $this->request->getPost('kelas_jenis');
		$kelas_semester = $this->request->getPost('kelas_semester');
		$mk_kurikulum_kode = $this->request->getPost('mk_kurikulum_kode');

		// Validation
		if (count($dosen_members) !== count(array_unique($dosen_members))) {
			return redirect()->back()->withInput()->with('error', 'Dosen anggota tidak boleh ada yang sama.');
		}

		try {
			$this->db->transStart();

			// Check if jadwal exists (excluding current)
			$existing = $this->db->table('jadwal')
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
			$kelas_id = $this->request->getPost('kelas_id');
			$total_mahasiswa = $this->request->getPost('total_mahasiswa');

			$jadwalData = [
				'mata_kuliah_id' => $mata_kuliah_id,
				'program_studi_kode' => $program_studi_kode,
				'tahun_akademik' => $tahun_akademik,
				'kelas' => $kelas,
				'ruang' => $ruang ?: null,
				'hari' => $hari ?: null,
				'jam_mulai' => $jam_mulai ?: null,
				'jam_selesai' => $jam_selesai ?: null,
				'kelas_id' => $kelas_id ?: null,
				'kelas_jenis' => $kelas_jenis ?: null,
				'kelas_semester' => $kelas_semester ?: null,
				'mk_kurikulum_kode' => $mk_kurikulum_kode ?: null,
				'total_mahasiswa' => $total_mahasiswa ?: 0
			];

			$this->db->table('jadwal')->where('id', $id)->update($jadwalData);

			// Delete existing dosen assignments
			$this->db->table('jadwal_dosen')->where('jadwal_id', $id)->delete();

			// Insert new dosen leader
			$this->db->table('jadwal_dosen')->insert([
				'jadwal_id' => $id,
				'dosen_id' => $dosen_leader,
				'role' => 'leader'
			]);

			// Insert new dosen members
			foreach ($dosen_members as $member_id) {
				if ($member_id) {
					$this->db->table('jadwal_dosen')->insert([
						'jadwal_id' => $id,
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
			$this->db->table('jadwal')->where('id', $id)->delete();
			return redirect()->to(base_url('admin/mengajar'))->with('success', 'Jadwal mengajar berhasil dihapus.');
		} catch (\Exception $e) {
			return redirect()->to(base_url('admin/mengajar'))->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
		}
	}

	/**
	 * Sync jadwal/kelas data from external API.
	 * Only creates jadwal for mata kuliah that already have RPS.
	 */
	public function syncFromApi()
    {
        if (session()->get('role') !== 'admin') {
            return redirect()->back()->with('error', 'Akses ditolak. Hanya admin yang dapat melakukan sinkronisasi.');
        }

        $semesterId = $this->request->getPost('semester_id');
        if (empty($semesterId) || !preg_match('/^\d{5}$/', $semesterId)) {
            return redirect()->back()->with('error', 'Semester ID tidak valid. Gunakan format 5 digit, misalnya 20251.');
        }

        $apiUrl = 'https://api.siuber.upr.ac.id/api/siuber/jadwal?klsSemester=' . $semesterId . '&prodiKode=58&fakKode=5&klsJenis=Reguler';
        $apiKey = 'XT)+KVdVT]Z]1-p8<tIz/H0W5}_z%@KS';
        $client = \Config\Services::curlrequest();

        try {
            // Ambil data jadwal dari API
			$response = $client->request('GET', $apiUrl, [
				'headers' => [
					'x-api-key' => $apiKey,
					'Accept'    => 'application/json',
				],
				'timeout' => 60,
			]);

            $body = json_decode($response->getBody(), true);
            $jadwalRaw = $body['jadwal'] ?? [];

            // Flatten array kelas
            $kelasList = [];
            foreach ($jadwalRaw as $item) {
                if (isset($item['kelas']) && is_array($item['kelas'])) {
                    foreach ($item['kelas'] as $k) {
                        $kelasList[] = $k;
                    }
                }
            }

            if (empty($kelasList)) {
                return redirect()->back()->with('error', 'Format response API tidak valid.');
            }

            // Pre-sync kelas dari API
            try {
                $kelasApiUrl = 'https://api.siuber.upr.ac.id/api/siuber/kelas?kelasSemId=' . $semesterId . '&prodiKode=58&fakKode=5';
                $kelasResponse = $client->request('GET', $kelasApiUrl, [
                    'headers' => [
                        'x-api-key' => $apiKey,
                        'Accept'    => 'application/json',
                    ],
                    'timeout' => 30,
                ]);
                $kelasBody = json_decode($kelasResponse->getBody(), true);
                $kelasApiList = $kelasBody['data'] ?? [];

                $kelasModel = new KelasModel();
                foreach ($kelasApiList as $kls) {
                    $klsId = $kls['kelas_id'] ?? null;
                    if (!$klsId) continue;

                    $existing = $kelasModel->find($klsId);
                    $kelasData = [
						'kelas_id'              => $kls['kelasId'] ?? null,
						'kelas_sem_id'          => $kls['kelasSemId'] ?? 0,
						'kelas_nama'            => $kls['kelasNama'] ?? '',
						'matakuliah_kurikulum_id' => $kls['matakuliahKurikulumId'] ?? 0,
						'matakuliah_kode'       => $kls['matakuliahKode'] ?? '',
						'matakuliah_nama'       => $kls['matakuliahNama'] ?? '',
						'kurikulum_id'          => $kls['kurikulumId'] ?? 0,
						'kurikulum_status'      => $kls['kurikulumNama'] ?? null, // sesuaikan jika mau pakai kurikulum_nama
						'fakultas_kode'         => $kls['fakKode'] ?? null,
						'fakultas_nama'         => $kls['fakNamaResmi'] ?? null,
						'program_studi_kode'    => $kls['prodiKode'] ?? null,
						'program_studi_nama'    => $kls['prodiNamaResmi'] ?? null,
					];

                    if ($existing) {
                        $kelasModel->update($klsId, $kelasData);
                    } else {
                        $kelasModel->insert($kelasData);
                    }
                }
            } catch (\Exception $e) {
                log_message('warning', 'Kelas pre-sync failed: ' . $e->getMessage());
            }

            // Build set kelas_id
            $validKelasIds = [];
            $allKelas = $this->db->table('kelas')->select('kelas_id')->get()->getResultArray();
            foreach ($allKelas as $k) {
                $validKelasIds[$k['kelas_id']] = true;
            }

            // Ambil mata kuliah yang sudah ada RPS
            $rpsData = $this->db->table('rps r')
                ->select('r.id as rps_id, r.mata_kuliah_id, mk.kode_mk, mk.nama_mk')
                ->join('mata_kuliah mk', 'mk.id = r.mata_kuliah_id')
                ->get()
                ->getResultArray();

            $mkWithRps = [];
            foreach ($rpsData as $rps) {
                $mkWithRps[$rps['kode_mk']] = $rps;
            }

            $inserted = $updated = $studentsInserted = $skipped = 0;

            foreach ($kelasList as $kelas) {
                $mkKode = $kelas['mata_kuliah']['kode'] ?? null;
                $kelasId = $kelas['kelas']['id'] ?? null;
                if ($kelasId && !isset($validKelasIds[$kelasId])) {
                    $kelasId = null;
                }
                $kelasNama = $kelas['kelas']['nama'] ?? 'A';
                $kelasJenis = $kelas['kelas']['jenis'] ?? null;
                $kelasSemester = $kelas['kelas']['semester'] ?? null;
                $kelasStatus = $kelas['kelas']['status'] ?? 'Aktif';
                $hari = $kelas['jadwal_perkuliahan'][0]['hari'] ?? null;

                $jamMulaiRaw = $kelas['jadwal_perkuliahan'][0]['jam']['mulai'] ?? null;
                $jamSelesaiRaw = $kelas['jadwal_perkuliahan'][0]['jam']['selesai'] ?? null;
                $jamMulai = $jamMulaiRaw ? substr($jamMulaiRaw, 11, 8) : null;
                $jamSelesai = $jamSelesaiRaw ? substr($jamSelesaiRaw, 11, 8) : null;

                $ruangKelas = $kelas['jadwal_perkuliahan'][0]['ruangan']['ruang'] ?? null;
                $gedung = $kelas['jadwal_perkuliahan'][0]['ruangan']['gedung'] ?? null;
                $mahasiswaData = $kelas['mahasiswa'] ?? [];
                $totalMahasiswa = $mahasiswaData['total'] ?? 0;
                $nimList = $mahasiswaData['nim'] ?? [];

				if (!$mkKode || !isset($mkWithRps[$mkKode])) {
					log_message('warning', "Kelas {$kelasNama} dilewati, MK {$mkKode} belum punya RPS");
					$skipped++;
					continue;
				}

                $matchedMk = $mkWithRps[$mkKode];
                $mataKuliahId = $matchedMk['mata_kuliah_id'];
                $tahunAkademik = $this->deriveTahunAkademik($kelasSemester);

                $mk = $this->db->table('mata_kuliah')
                    ->where('id', $mataKuliahId)
                    ->get()
                    ->getRowArray();

                $programStudiKode = $mk['program_studi_kode'] ?? null;

                $existing = $this->db->table('jadwal')
                    ->where('kelas_id', $kelasId)
                    ->get()
                    ->getRowArray();

                if ($existing) {
                    $this->db->table('jadwal')->where('id', $existing['id'])->update([
                        'kelas'             => $kelasNama,
                        'kelas_jenis'       => $kelasJenis,
                        'kelas_semester'    => $kelasSemester,
                        'kelas_status'      => $kelasStatus,
                        'mk_kurikulum_kode' => $mkKode,
                        'total_mahasiswa'   => $totalMahasiswa,
                        'ruang'             => $ruangKelas ? ($gedung ? "$gedung - $ruangKelas" : $ruangKelas) : null,
                        'hari'              => $hari,
                        'jam_mulai'         => $jamMulai,
                        'jam_selesai'       => $jamSelesai,
                    ]);
                    $jadwalId = $existing['id'];
                    $updated++;
                } else {
                    $this->db->table('jadwal')->insert([
                        'mata_kuliah_id'    => $mataKuliahId,
                        'program_studi_kode' => $programStudiKode,
                        'tahun_akademik'    => $tahunAkademik,
                        'kelas'             => $kelasNama,
                        'ruang'             => $ruangKelas ? ($gedung ? "$gedung - $ruangKelas" : $ruangKelas) : null,
                        'hari'              => $hari,
                        'jam_mulai'         => $jamMulai,
                        'jam_selesai'       => $jamSelesai,
                        'status'            => 'active',
                        'kelas_id'          => $kelasId,
                        'kelas_jenis'       => $kelasJenis,
                        'kelas_semester'    => $kelasSemester,
                        'kelas_status'      => $kelasStatus,
                        'mk_kurikulum_kode' => $mkKode,
                        'total_mahasiswa'   => $totalMahasiswa,
                    ]);
                    $jadwalId = $this->db->insertID();
                    $inserted++;

                    // Assign dosen
                    $rpsId = $matchedMk['rps_id'];
                    $rpsDosen = $this->db->table('rps_pengampu')->where('rps_id', $rpsId)->get()->getResultArray();
                    foreach ($rpsDosen as $rd) {
                        $role = ($rd['peran'] === 'koordinator') ? 'leader' : 'member';
                        $this->db->table('jadwal_dosen')->insert([
                            'jadwal_id' => $jadwalId,
                            'dosen_id'  => $rd['dosen_id'],
                            'role'      => $role,
                        ]);
                    }
                }

                // Sync mahasiswa
                if (!empty($nimList)) {
                    $this->db->table('jadwal_mahasiswa')->where('jadwal_id', $jadwalId)->delete();
                    foreach ($nimList as $nim) {
                        $mhsExists = $this->db->table('mahasiswa')->where('nim', $nim)->countAllResults();
                        if ($mhsExists > 0) {
                            $this->db->table('jadwal_mahasiswa')->insert([
                                'jadwal_id' => $jadwalId,
                                'nim'       => $nim,
                            ]);
                            $studentsInserted++;
                        }
                    }
                }
            }

            $message = "Sinkronisasi berhasil! $inserted jadwal baru ditambahkan, $updated diperbarui, $studentsInserted mahasiswa disinkronkan.";
            if ($skipped > 0) $message .= " $skipped kelas dilewati (MK belum memiliki RPS).";

            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            log_message('error', 'Jadwal sync error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal mengambil data dari API: ' . $e->getMessage());
        }
    }

	/**
	 * AJAX endpoint to get kelas data from API for a specific mata kuliah
	 */
	public function getApiKelas()
	{
		if (!$this->request->isAJAX()) {
			return $this->response->setStatusCode(403)->setJSON(['error' => 'Invalid request']);
		}

		$mkKode = $this->request->getGet('kode_mk');

		if (!$mkKode) {
			return $this->response->setJSON(['success' => false, 'message' => 'Kode MK diperlukan']);
		}

		$apiUrl = 'https://tik.upr.ac.id/api/siuber/jadwal?prodiKode=58&fakKode=5';
		$apiKey = 'XT)+KVdVT]Z]1-p8<tIz/H0W5}_z%@KS';

		$client = \Config\Services::curlrequest();

		try {
			$response = $client->request('GET', $apiUrl, [
				'headers' => [
					'x-api-key' => $apiKey,
					'Accept'    => 'application/json',
				],
				'timeout' => 30,
			]);

			$body = json_decode($response->getBody(), true);

			$jadwalRaw = $body['jadwal'][0] ?? [];

			// $jadwalRaw = { status, kelas: [...] } — iterate over the 'kelas' sub-array
			$kelasList = [];
			foreach ($jadwalRaw['kelas'] ?? [] as $item) {
				if (
					isset($item['mata_kuliah']['mkKode']) &&
					$item['mata_kuliah']['mkKode'] === $mkKode &&
					($item['kelas']['klsStatus'] ?? '') === 'Aktif'
				) {
					$kelasList[] = $item;
				}
			}

			return $this->response->setJSON([
				'success' => true,
				'data'    => $kelasList,
				'program_studi_kode' => 58,
			]);
		} catch (\Exception $e) {
			return $this->response->setStatusCode(500)->setJSON([
				'success' => false,
				'message' => 'Gagal mengambil data: ' . $e->getMessage(),
			]);
		}
	}

	/**
	 * Derive tahun akademik string from semester code.
	 * e.g. 20252 -> "2025/2026 Genap", 20251 -> "2024/2025 Ganjil"
	 */
	private function deriveTahunAkademik($semesterCode)
	{
		if (!$semesterCode) {
			return date('Y') . '/' . (date('Y') + 1);
		}

		$code = (string) $semesterCode;
		$year = (int) substr($code, 0, 4);
		$term = substr($code, 4, 1);

		if ($term === '1') {
			return $year . ' Ganjil';
		} else {
			return $year . ' Genap';
		}
	}

	/**
	 * AJAX endpoint to get RPS dosen data for a specific mata kuliah
	 */
	public function getRpsDosen($mata_kuliah_id)
	{
		if (!$this->request->isAJAX()) {
			return $this->response->setStatusCode(403)->setJSON(['error' => 'Invalid request']);
		}

		try {
			// Get RPS for this mata kuliah
			$rps = $this->db->table('rps')
				->where('mata_kuliah_id', $mata_kuliah_id)
				->get()
				->getRowArray();

			if (!$rps) {
				return $this->response->setJSON([
					'success' => false,
					'message' => 'RPS tidak ditemukan untuk mata kuliah ini'
				]);
			}

			// Get dosen koordinator
			$koordinator = $this->db->table('rps_pengampu rp')
				->select('d.id, d.nama_lengkap, rp.peran')
				->join('dosen d', 'd.id = rp.dosen_id')
				->where('rp.rps_id', $rps['id'])
				->where('rp.peran', 'koordinator')
				->get()
				->getRowArray();

			// Get dosen pengampu (members)
			$members = $this->db->table('rps_pengampu rp')
				->select('d.id, d.nama_lengkap, rp.peran')
				->join('dosen d', 'd.id = rp.dosen_id')
				->where('rp.rps_id', $rps['id'])
				->where('rp.peran', 'pengampu')
				->get()
				->getResultArray();

			return $this->response->setJSON([
				'success' => true,
				'data' => [
					'koordinator' => $koordinator,
					'members' => $members,
					'rps_id' => $rps['id']
				]
			]);
		} catch (\Exception $e) {
			return $this->response->setStatusCode(500)->setJSON([
				'success' => false,
				'message' => 'Terjadi kesalahan: ' . $e->getMessage()
			]);
		}
	}

	// ─── Mahasiswa Management ───────────────────────────────────────────────────

	public function mahasiswaPage($id)
	{
		$jadwal = $this->db->table('jadwal jm')
			->select('jm.*, mk.kode_mk, mk.nama_mk, mk.semester, mk.sks, ps.nama_resmi as program_studi_nama')
			->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id')
			->join('program_studi ps', 'ps.kode = jm.program_studi_kode', 'left')
			->where('jm.id', $id)
			->get()->getRowArray();

		if (!$jadwal) {
			return redirect()->to(base_url('admin/mengajar'))->with('error', 'Jadwal tidak ditemukan.');
		}

		$mahasiswaList = $this->db->table('jadwal_mahasiswa jmhs')
			->select('mhs.nim, mhs.nama_lengkap, mhs.tahun_angkatan, mhs.status_mahasiswa')
			->join('mahasiswa mhs', 'mhs.nim = jmhs.nim')
			->where('jmhs.jadwal_id', $id)
			->orderBy('mhs.nim', 'ASC')
			->get()->getResultArray();

		$data = [
			'title'          => 'Kelola Mahasiswa – ' . $jadwal['nama_mk'] . ' Kelas ' . $jadwal['kelas'],
			'jadwal'         => $jadwal,
			'mahasiswa_list' => $mahasiswaList,
		];

		return view('mengajar/mahasiswa', $data);
	}

	public function searchMahasiswa($id)
	{
		if (!$this->request->isAJAX()) {
			return $this->response->setStatusCode(403)->setJSON(['error' => 'Invalid request']);
		}

		$q = trim($this->request->getGet('q') ?? '');

		// NIMs already enrolled in this jadwal
		$enrolledNims = $this->db->table('jadwal_mahasiswa')
			->select('nim')
			->where('jadwal_id', $id)
			->get()->getResultArray();
		$enrolled = array_column($enrolledNims, 'nim');

		$builder = $this->db->table('mahasiswa')
			->select('nim, nama_lengkap, tahun_angkatan')
			->orderBy('nim', 'ASC')
			->limit(30);

		if (!empty($q)) {
			$builder->groupStart()
				->like('nim', $q)
				->orLike('nama_lengkap', $q)
				->groupEnd();
		}

		if (!empty($enrolled)) {
			$builder->whereNotIn('nim', $enrolled);
		}

		$rows = $builder->get()->getResultArray();

		$results = array_map(fn($r) => [
			'id'   => $r['nim'],
			'text' => $r['nim'] . ' – ' . $r['nama_lengkap'] . ' (' . $r['tahun_angkatan'] . ')',
		], $rows);

		return $this->response->setJSON(['results' => $results]);
	}

	public function addMahasiswa($id)
	{
		$jadwal = $this->db->table('jadwal')->where('id', $id)->get()->getRowArray();
		if (!$jadwal) {
			return redirect()->to(base_url('admin/mengajar'))->with('error', 'Jadwal tidak ditemukan.');
		}

		$nims = $this->request->getPost('nim') ?? [];
		if (!is_array($nims)) {
			$nims = [$nims];
		}
		$nims = array_filter($nims);

		if (empty($nims)) {
			return redirect()->back()->with('error', 'Pilih minimal satu mahasiswa.');
		}

		$added = 0;
		foreach ($nims as $nim) {
			$exists = $this->db->table('jadwal_mahasiswa')
				->where('jadwal_id', $id)->where('nim', $nim)
				->countAllResults();
			if (!$exists) {
				$mhsExists = $this->db->table('mahasiswa')->where('nim', $nim)->countAllResults();
				if ($mhsExists) {
					$this->db->table('jadwal_mahasiswa')->insert(['jadwal_id' => $id, 'nim' => $nim]);
					$added++;
				}
			}
		}

		return redirect()->to(base_url("admin/mengajar/$id/mahasiswa"))
			->with('success', "$added mahasiswa berhasil ditambahkan.");
	}

	public function removeMahasiswa($id)
	{
		$nim = $this->request->getPost('nim');
		if (!$nim) {
			return redirect()->back()->with('error', 'NIM tidak valid.');
		}

		$this->db->table('jadwal_mahasiswa')
			->where('jadwal_id', $id)->where('nim', $nim)->delete();

		return redirect()->to(base_url("admin/mengajar/$id/mahasiswa"))
			->with('success', 'Mahasiswa berhasil dihapus dari jadwal.');
	}

	// ────────────────────────────────────────────────────────────────────────────

	// Add this private method inside your Mengajar controller class
	private function _getFilteredJadwalData()
	{
		// Get filters from query parameters
		$filters = [
			'program_studi_kode' => $this->request->getGet('program_studi_kode'),
			'tahun_akademik' => $this->request->getGet('tahun_akademik')
		];

		// Build query
		$builder = $this->db->table('jadwal jm');
		$builder->select('jm.*, mk.kode_mk, mk.nama_mk, mk.semester, mk.sks, ps.nama_resmi as program_studi_nama');
		$builder->join('mata_kuliah mk', 'mk.id = jm.mata_kuliah_id');
		$builder->join('program_studi ps', 'ps.kode = jm.program_studi_kode', 'left');

		// Apply filters
		if (!empty($filters['program_studi_kode'])) {
			$builder->where('jm.program_studi_kode', $filters['program_studi_kode']);
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
			$dosenBuilder->where('jd.jadwal_id', $jadwal['id']);
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
			$sheet->setCellValue('B' . $row, $jadwal['program_studi_nama'] ?? $jadwal['program_studi_kode']);
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
		$filename = 'jadwal_' . date('YmdHis') . '.xlsx';
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
		$filename = 'jadwal_' . date('YmdHis');

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

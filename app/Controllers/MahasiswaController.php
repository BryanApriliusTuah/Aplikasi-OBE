<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\MahasiswaModel;
use App\Models\NilaiMahasiswaModel;
use App\Models\NilaiCpmkMahasiswaModel;
use App\Models\MataKuliahModel;
use App\Models\NilaiTeknikPenilaianModel;

class MahasiswaController extends BaseController
{
	protected $mahasiswaModel;
	protected $nilaiMahasiswaModel;
	protected $nilaiCpmkMahasiswaModel;
	protected $mataKuliahModel;
	protected $nilaiTeknikPenilaianModel;

	public function __construct()
	{
		$this->mahasiswaModel = new MahasiswaModel();
		$this->nilaiMahasiswaModel = new NilaiMahasiswaModel();
		$this->nilaiCpmkMahasiswaModel = new NilaiCpmkMahasiswaModel();
		$this->mataKuliahModel = new MataKuliahModel();
		$this->nilaiTeknikPenilaianModel = new NilaiTeknikPenilaianModel();
	}

	/**
	 * Mahasiswa Dashboard
	 */
	public function dashboard()
	{
		// Check if user is logged in as mahasiswa
		if (session('role') !== 'mahasiswa') {
			return redirect()->to('/login');
		}

		$mahasiswaId = session('mahasiswa_id');
		$mahasiswaData = $this->mahasiswaModel->find($mahasiswaId);

		// Get total nilai count
		$totalNilai = $this->nilaiMahasiswaModel
			->where('mahasiswa_id', $mahasiswaId)
			->countAllResults();

		// Get nilai with status Lulus
		$nilaiLulus = $this->nilaiMahasiswaModel
			->where('mahasiswa_id', $mahasiswaId)
			->where('status_kelulusan', 'Lulus')
			->countAllResults();

		// Calculate average nilai
		$avgNilai = $this->nilaiMahasiswaModel
			->selectAvg('nilai_akhir')
			->where('mahasiswa_id', $mahasiswaId)
			->where('status_kelulusan', 'Lulus')
			->get()
			->getRow()
			->nilai_akhir ?? 0;

		// Get recent nilai (last 5)
		$recentNilai = $this->nilaiMahasiswaModel
			->select('nilai_mahasiswa.*, mata_kuliah.nama_mk, mata_kuliah.kode_mk, jadwal_mengajar.tahun_akademik, jadwal_mengajar.kelas')
			->join('jadwal_mengajar', 'nilai_mahasiswa.jadwal_mengajar_id = jadwal_mengajar.id')
			->join('mata_kuliah', 'jadwal_mengajar.mata_kuliah_id = mata_kuliah.id')
			->where('nilai_mahasiswa.mahasiswa_id', $mahasiswaId)
			->orderBy('nilai_mahasiswa.updated_at', 'DESC')
			->limit(5)
			->findAll();

		$data = [
			'title' => 'Dashboard Mahasiswa',
			'mahasiswa' => $mahasiswaData,
			'totalNilai' => $totalNilai,
			'nilaiLulus' => $nilaiLulus,
			'avgNilai' => round($avgNilai, 2),
			'recentNilai' => $recentNilai
		];

		return view('mahasiswa/dashboard', $data);
	}

	/**
	 * Mahasiswa - View all scores
	 */
	public function nilai()
	{
		// Check if user is logged in as mahasiswa
		if (session('role') !== 'mahasiswa') {
			return redirect()->to('/login');
		}

		$mahasiswaId = session('mahasiswa_id');

		// Get all nilai for this mahasiswa
		$nilaiList = $this->nilaiMahasiswaModel
			->select('nilai_mahasiswa.*, mata_kuliah.nama_mk, mata_kuliah.kode_mk, mata_kuliah.sks, mata_kuliah.semester, jadwal_mengajar.tahun_akademik, jadwal_mengajar.kelas, jadwal_mengajar.id as jadwal_id')
			->join('jadwal_mengajar', 'nilai_mahasiswa.jadwal_mengajar_id = jadwal_mengajar.id')
			->join('mata_kuliah', 'jadwal_mengajar.mata_kuliah_id = mata_kuliah.id')
			->where('nilai_mahasiswa.mahasiswa_id', $mahasiswaId)
			->orderBy('jadwal_mengajar.tahun_akademik', 'DESC')
			->orderBy('mata_kuliah.semester', 'ASC')
			->findAll();

		$data = [
			'title' => 'Nilai Saya',
			'nilaiList' => $nilaiList
		];

		return view('mahasiswa/nilai/index', $data);
	}

	/**
	 * Mahasiswa - View detail scores including teknik penilaian
	 */
	public function nilaiDetail($jadwalId)
	{
		// Check if user is logged in as mahasiswa
		if (session('role') !== 'mahasiswa') {
			return redirect()->to('/login');
		}

		$mahasiswaId = session('mahasiswa_id');

		// Get nilai mahasiswa for this jadwal
		$nilai = $this->nilaiMahasiswaModel
			->select('nilai_mahasiswa.*, mata_kuliah.nama_mk, mata_kuliah.kode_mk, mata_kuliah.sks, mata_kuliah.semester, jadwal_mengajar.tahun_akademik, jadwal_mengajar.kelas')
			->join('jadwal_mengajar', 'nilai_mahasiswa.jadwal_mengajar_id = jadwal_mengajar.id')
			->join('mata_kuliah', 'jadwal_mengajar.mata_kuliah_id = mata_kuliah.id')
			->where('nilai_mahasiswa.mahasiswa_id', $mahasiswaId)
			->where('nilai_mahasiswa.jadwal_mengajar_id', $jadwalId)
			->first();

		if (!$nilai) {
			session()->setFlashdata('error', 'Data nilai tidak ditemukan.');
			return redirect()->to('mahasiswa/nilai');
		}

		// Get teknik penilaian structure grouped by tahap
		$teknikPenilaianData = $this->nilaiTeknikPenilaianModel->getCombinedTeknikPenilaianByJadwal($jadwalId);

		// Get student's scores for teknik penilaian
		$studentScores = $this->nilaiTeknikPenilaianModel->getCombinedScoresForInput($jadwalId);
		$mahasiswaScores = $studentScores[$mahasiswaId] ?? [];

		// Get CPMK scores (keep for reference)
		$nilaiCpmk = $this->nilaiCpmkMahasiswaModel
			->select('nilai_cpmk_mahasiswa.*, cpmk.kode_cpmk, cpmk.deskripsi')
			->join('cpmk', 'nilai_cpmk_mahasiswa.cpmk_id = cpmk.id')
			->where('nilai_cpmk_mahasiswa.mahasiswa_id', $mahasiswaId)
			->where('nilai_cpmk_mahasiswa.jadwal_mengajar_id', $jadwalId)
			->orderBy('cpmk.kode_cpmk', 'ASC')
			->findAll();

		$data = [
			'title' => 'Detail Nilai - ' . $nilai['nama_mk'],
			'nilai' => $nilai,
			'nilaiCpmk' => $nilaiCpmk,
			'teknikPenilaianByTahap' => $teknikPenilaianData['by_tahap'] ?? [],
			'mahasiswaScores' => $mahasiswaScores
		];

		return view('mahasiswa/nilai/detail', $data);
	}

	/**
	 * Mahasiswa - View schedule
	 */
	public function jadwal()
	{
		if (session('role') !== 'mahasiswa') {
			return redirect()->to('/login');
		}

		$mahasiswaId = session('mahasiswa_id');
		$db = \Config\Database::connect();

		// Get jadwal where student is enrolled (from nilai_mahasiswa table)
		$builder = $db->table('nilai_mahasiswa nm');
		$builder->select('jm.*, mk.kode_mk, mk.nama_mk, mk.sks, 
						  GROUP_CONCAT(DISTINCT d.nama_lengkap ORDER BY jd.role DESC SEPARATOR ", ") as dosen_pengampu');
		$builder->join('jadwal_mengajar jm', 'nm.jadwal_mengajar_id = jm.id');
		$builder->join('mata_kuliah mk', 'jm.mata_kuliah_id = mk.id');
		$builder->join('jadwal_dosen jd', 'jm.id = jd.jadwal_mengajar_id', 'left');
		$builder->join('dosen d', 'jd.dosen_id = d.id', 'left');
		$builder->where('nm.mahasiswa_id', $mahasiswaId);
		$builder->groupBy('jm.id');
		$jadwalList = $builder->get()->getResultArray();

		// Group by day
		$scheduleByDay = [
			'Senin' => [],
			'Selasa' => [],
			'Rabu' => [],
			'Kamis' => [],
			'Jumat' => [],
			'Sabtu' => []
		];

		foreach ($jadwalList as $jadwal) {
			if ($jadwal['hari']) {
				$scheduleByDay[$jadwal['hari']][] = $jadwal;
			}
		}

		// Calculate totals
		$totalSKS = 0;
		$totalMK = count($jadwalList);
		$activeDays = 0;

		foreach ($scheduleByDay as $day => $schedules) {
			if (!empty($schedules)) {
				$activeDays++;
				foreach ($schedules as $schedule) {
					$totalSKS += $schedule['sks'];
				}
			}
		}

		// Get unique lecturers count
		$uniqueLecturers = [];
		foreach ($jadwalList as $jadwal) {
			if ($jadwal['dosen_pengampu']) {
				$lecturers = explode(', ', $jadwal['dosen_pengampu']);
				foreach ($lecturers as $lecturer) {
					$uniqueLecturers[$lecturer] = true;
				}
			}
		}
		$totalDosen = count($uniqueLecturers);

		$data = [
			'title' => 'Jadwal Kuliah',
			'jadwalList' => $jadwalList,
			'scheduleByDay' => $scheduleByDay,
			'totalSKS' => $totalSKS,
			'totalMK' => $totalMK,
			'activeDays' => $activeDays,
			'totalDosen' => $totalDosen
		];

		return view('mahasiswa/jadwal', $data);
	}

	/**
	 * Mahasiswa - View CPL profile
	 */
	public function profilCpl()
	{
		if (session('role') !== 'mahasiswa') {
			return redirect()->to('/login');
		}

		$mahasiswaId = session('mahasiswa_id');
		$db = \Config\Database::connect();

		// Get all CPL
		$cplList = $db->table('cpl')
			->orderBy('kode_cpl', 'ASC')
			->get()
			->getResultArray();

		// Calculate CPL achievement for the student
		// Formula: Nilai CPL = Σ(CPMK scores), Capaian CPL (%) = (Nilai CPL / Total Bobot) * 100
		$calculatedCplList = [];

		foreach ($cplList as $cpl) {
			// Get all CPMK linked to this CPL
			$cpmkLinked = $db->table('cpl_cpmk')
				->select('cpmk_id')
				->where('cpl_id', $cpl['id'])
				->get()
				->getResultArray();

			if (empty($cpmkLinked)) {
				$cpl['nilai_cpl'] = 0;
				$cpl['capaian_cpl'] = 0;
				$calculatedCplList[] = $cpl;
				continue;
			}

			$cpmkIds = array_column($cpmkLinked, 'cpmk_id');

			// Get all nilai_cpmk for this student for these CPMK
			$nilaiList = $db->table('nilai_cpmk_mahasiswa ncm')
				->select('ncm.nilai_cpmk, ncm.cpmk_id, ncm.jadwal_mengajar_id, jm.mata_kuliah_id')
				->join('jadwal_mengajar jm', 'jm.id = ncm.jadwal_mengajar_id')
				->where('ncm.mahasiswa_id', $mahasiswaId)
				->whereIn('ncm.cpmk_id', $cpmkIds)
				->get()
				->getResultArray();

			// Calculate CPL using formula:
			// Nilai CPL = Σ(CPMK scores) - note: nilai_cpmk is already weighted (raw × bobot / 100)
			// Capaian CPL (%) = (Nilai CPL / Total Bobot) × 100
			$nilaiCpl = 0;
			$totalBobot = 0;

			foreach ($nilaiList as $nilai) {
				// Get bobot from rps_mingguan
				$rps = $db->table('rps')
					->select('id')
					->where('mata_kuliah_id', $nilai['mata_kuliah_id'])
					->orderBy('created_at', 'DESC')
					->get()
					->getRowArray();

				$bobot = 0;
				if ($rps) {
					// Sum bobot across all weeks for this CPMK
					$bobotResult = $db->table('rps_mingguan')
						->selectSum('bobot')
						->where('rps_id', $rps['id'])
						->where('cpmk_id', $nilai['cpmk_id'])
						->get()
						->getRowArray();

					$bobot = $bobotResult['bobot'] ?? 0;
				}

				// Sum CPMK scores and bobot
				if ($bobot > 0) {
					$nilaiCpl += $nilai['nilai_cpmk'];
					$totalBobot += $bobot;
				}
			}

			// Capaian CPL (%) = (Nilai CPL / Total Bobot) × 100
			$cpl['nilai_cpl'] = round($nilaiCpl, 2);
			$cpl['capaian_cpl'] = $totalBobot > 0 ? round(($nilaiCpl / $totalBobot) * 100, 2) : 0;
			$calculatedCplList[] = $cpl;
		}

		// Group by jenis_cpl
		$cplByType = [
			'P' => [],
			'KK' => [],
			'KU' => [],
			'S' => []
		];

		$totalNilai = 0;
		$countCPL = 0;
		$cplTercapai = 0;

		foreach ($calculatedCplList as $cpl) {
			$capaian = round($cpl['capaian_cpl'], 2);

			$cplByType[$cpl['jenis_cpl']][] = [
				'id' => $cpl['id'],
				'kode' => $cpl['kode_cpl'],
				'deskripsi' => $cpl['deskripsi'],
				'nilai' => $capaian
			];

			if ($capaian > 0) {
				$totalNilai += $capaian;
				$countCPL++;
				if ($capaian >= 70) {
					$cplTercapai++;
				}
			}
		}

		$avgCPL = $countCPL > 0 ? round($totalNilai / $countCPL, 2) : 0;
		$totalCPLCount = count($calculatedCplList);
		$percentComplete = $totalCPLCount > 0 ? round(($cplTercapai / $totalCPLCount) * 100) : 0;

		$data = [
			'title' => 'Profil CPL',
			'cplList' => $calculatedCplList,
			'cplByType' => $cplByType,
			'avgCPL' => $avgCPL,
			'cplTercapai' => $cplTercapai,
			'totalCPLCount' => $totalCPLCount,
			'percentComplete' => $percentComplete
		];

		return view('mahasiswa/profil_cpl', $data);
	}

	/**
	 * Mahasiswa - Get CPL Detail (CPMK breakdown)
	 */
	public function getCplDetail()
	{
		if (session('role') !== 'mahasiswa') {
			return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
		}

		$mahasiswaId = session('mahasiswa_id');
		$cplId = $this->request->getGet('cpl_id');

		if (!$cplId) {
			return $this->response->setJSON(['success' => false, 'message' => 'CPL ID required']);
		}

		$db = \Config\Database::connect();

		// Get CPL info
		$cpl = $db->table('cpl')->where('id', $cplId)->get()->getRowArray();
		if (!$cpl) {
			return $this->response->setJSON(['success' => false, 'message' => 'CPL tidak ditemukan']);
		}

		// Get all CPMK linked to this CPL
		$cpmkLinked = $db->table('cpl_cpmk')
			->select('cpmk.id, cpmk.kode_cpmk, cpmk.deskripsi')
			->join('cpmk', 'cpmk.id = cpl_cpmk.cpmk_id')
			->where('cpl_cpmk.cpl_id', $cplId)
			->orderBy('cpmk.kode_cpmk', 'ASC')
			->get()
			->getResultArray();

		if (empty($cpmkLinked)) {
			return $this->response->setJSON([
				'success' => true,
				'data' => [],
				'cpl' => $cpl,
				'message' => 'Tidak ada CPMK yang terkait dengan CPL ini'
			]);
		}

		// Get nilai for each CPMK
		$detailData = [];
		$totalNilaiCpl = 0;
		$totalBobotCpl = 0;

		foreach ($cpmkLinked as $cpmk) {
			// Get all nilai_cpmk for this CPMK and mahasiswa (grouped by jadwal)
			$nilaiCpmkList = $db->table('nilai_cpmk_mahasiswa')
				->select('nilai_cpmk_mahasiswa.nilai_cpmk, nilai_cpmk_mahasiswa.jadwal_mengajar_id, mata_kuliah.kode_mk, mata_kuliah.nama_mk, jadwal_mengajar.mata_kuliah_id')
				->join('jadwal_mengajar', 'jadwal_mengajar.id = nilai_cpmk_mahasiswa.jadwal_mengajar_id')
				->join('mata_kuliah', 'mata_kuliah.id = jadwal_mengajar.mata_kuliah_id')
				->where('nilai_cpmk_mahasiswa.mahasiswa_id', $mahasiswaId)
				->where('nilai_cpmk_mahasiswa.cpmk_id', $cpmk['id'])
				->get()
				->getResultArray();

			// For each jadwal, get the teknik penilaian breakdown
			$detailMk = [];
			foreach ($nilaiCpmkList as $nilaiCpmk) {
				$jadwalId = $nilaiCpmk['jadwal_mengajar_id'];

				// Get rps_mingguan for this CPMK
				$rps = $db->table('rps')
					->select('id')
					->where('mata_kuliah_id', $nilaiCpmk['mata_kuliah_id'])
					->orderBy('created_at', 'DESC')
					->get()
					->getRowArray();

				$teknikBreakdown = [];
				$totalWeighted = 0;
				$totalBobot = 0;

				if ($rps) {
					// Get rps_mingguan entries for this CPMK
					$rpsMingguanList = $db->table('rps_mingguan')
						->select('id, teknik_penilaian, bobot')
						->where('rps_id', $rps['id'])
						->where('cpmk_id', $cpmk['id'])
						->get()
						->getResultArray();

					$teknikLabels = [
						'partisipasi' => 'Partisipasi',
						'observasi' => 'Observasi',
						'unjuk_kerja' => 'Unjuk Kerja',
						'proyek' => 'Proyek',
						'tes_tulis_uts' => 'UTS',
						'tes_tulis_uas' => 'UAS',
						'tes_lisan' => 'Tes Lisan'
					];

					foreach ($rpsMingguanList as $rpsMingguan) {
						$teknikData = json_decode($rpsMingguan['teknik_penilaian'], true);

						if (is_array($teknikData)) {
							foreach ($teknikData as $teknikKey => $bobot) {
								if ($bobot > 0) {
									// Get the score for this teknik
									$nilaiTeknik = $db->table('nilai_teknik_penilaian')
										->select('nilai')
										->where('mahasiswa_id', $mahasiswaId)
										->where('jadwal_mengajar_id', $jadwalId)
										->where('rps_mingguan_id', $rpsMingguan['id'])
										->where('teknik_penilaian_key', $teknikKey)
										->get()
										->getRowArray();

									$score = $nilaiTeknik['nilai'] ?? 0;
									$weighted = ($score * $bobot) / 100;
									$totalWeighted += $weighted;
									$totalBobot += $bobot;

									$teknikBreakdown[] = [
										'teknik' => $teknikLabels[$teknikKey] ?? $teknikKey,
										'bobot' => $bobot,
										'nilai' => $score,
										'weighted' => round($weighted, 2)
									];
								}
							}
						}
					}
				}

				$detailMk[] = [
					'kode_mk' => $nilaiCpmk['kode_mk'],
					'nama_mk' => $nilaiCpmk['nama_mk'],
					'nilai_cpmk' => $nilaiCpmk['nilai_cpmk'],
					'total_bobot' => $totalBobot,
					'teknik_breakdown' => $teknikBreakdown
				];
			}

			// Calculate CPMK score and get bobot
			$nilaiCpmkTotal = 0;
			$bobotCpmk = 0;
			$countNilai = count($nilaiCpmkList);

			foreach ($nilaiCpmkList as $n) {
				$nilaiCpmkTotal += $n['nilai_cpmk'];

				// Get bobot for this CPMK from rps_mingguan
				$rps = $db->table('rps')
					->select('id')
					->where('mata_kuliah_id', $n['mata_kuliah_id'])
					->orderBy('created_at', 'DESC')
					->get()
					->getRowArray();

				if ($rps) {
					$bobotResult = $db->table('rps_mingguan')
						->selectSum('bobot')
						->where('rps_id', $rps['id'])
						->where('cpmk_id', $cpmk['id'])
						->get()
						->getRowArray();

					$bobotCpmk += $bobotResult['bobot'] ?? 0;
				}
			}

			// Add to CPL totals
			$totalNilaiCpl += $nilaiCpmkTotal;
			$totalBobotCpl += $bobotCpmk;

			$detailData[] = [
				'kode_cpmk' => $cpmk['kode_cpmk'],
				'deskripsi_cpmk' => $cpmk['deskripsi'],
				'nilai_cpmk' => round($nilaiCpmkTotal, 2),
				'bobot' => $bobotCpmk,
				'jumlah_nilai' => $countNilai,
				'detail_mk' => $detailMk
			];
		}

		// Calculate Capaian CPL (%)
		$capaianCpl = $totalBobotCpl > 0 ? round(($totalNilaiCpl / $totalBobotCpl) * 100, 2) : 0;

		return $this->response->setJSON([
			'success' => true,
			'data' => $detailData,
			'cpl' => $cpl,
			'summary' => [
				'nilai_cpl' => round($totalNilaiCpl, 2),
				'total_bobot' => $totalBobotCpl,
				'capaian_cpl' => $capaianCpl
			]
		]);
	}

	/**
	 * Mahasiswa - View MBKM activities
	 */
	public function mbkm()
	{
		if (session('role') !== 'mahasiswa') {
			return redirect()->to('/login');
		}

		$mahasiswaId = session('mahasiswa_id');
		$db = \Config\Database::connect();

		// Get MBKM activities for this student
		$builder = $db->table('mbkm_kegiatan_mahasiswa km');
		$builder->select('k.*, jk.nama_kegiatan, jk.kode_kegiatan, d.nama_lengkap as dosen_pembimbing, 
						  na.nilai_angka, na.nilai_huruf, na.status_kelulusan');
		$builder->join('mbkm_kegiatan k', 'km.kegiatan_id = k.id');
		$builder->join('mbkm_jenis_kegiatan jk', 'k.jenis_kegiatan_id = jk.id');
		$builder->join('dosen d', 'k.dosen_pembimbing_id = d.id', 'left');
		$builder->join('mbkm_nilai_akhir na', 'k.id = na.kegiatan_id', 'left');
		$builder->where('km.mahasiswa_id', $mahasiswaId);
		$builder->orderBy('k.tanggal_mulai', 'DESC');
		$kegiatanList = $builder->get()->getResultArray();

		// Calculate statistics
		$totalKegiatan = count($kegiatanList);
		$totalSKS = 0;
		$statusBadge = 'secondary';
		$statusText = 'Belum Ada';
		$nilaiHuruf = '-';

		if (!empty($kegiatanList)) {
			foreach ($kegiatanList as $kegiatan) {
				$totalSKS += $kegiatan['sks_dikonversi'];
			}

			$latestKegiatan = $kegiatanList[0];
			$statusText = $latestKegiatan['status_kegiatan'];

			switch ($statusText) {
				case 'selesai':
					$statusBadge = 'success';
					$statusText = 'Selesai';
					break;
				case 'berlangsung':
					$statusBadge = 'primary';
					$statusText = 'Berlangsung';
					break;
				case 'disetujui':
					$statusBadge = 'info';
					$statusText = 'Disetujui';
					break;
				case 'diajukan':
					$statusBadge = 'warning';
					$statusText = 'Diajukan';
					break;
				case 'ditolak':
					$statusBadge = 'danger';
					$statusText = 'Ditolak';
					break;
			}

			if ($latestKegiatan['nilai_huruf']) {
				$nilaiHuruf = $latestKegiatan['nilai_huruf'];
			}
		}

		// Get available MBKM program types
		$builderJenis = $db->table('mbkm_jenis_kegiatan');
		$builderJenis->where('status', 'aktif');
		$builderJenis->orderBy('kode_kegiatan', 'ASC');
		$jenisKegiatan = $builderJenis->get()->getResultArray();

		$data = [
			'title' => 'Kegiatan MBKM',
			'kegiatanList' => $kegiatanList,
			'totalKegiatan' => $totalKegiatan,
			'totalSKS' => $totalSKS,
			'statusBadge' => $statusBadge,
			'statusText' => $statusText,
			'nilaiHuruf' => $nilaiHuruf,
			'jenisKegiatan' => $jenisKegiatan
		];

		return view('mahasiswa/mbkm/mbkm', $data);
	}

	/**
	 * Mahasiswa - MBKM Registration Form
	 */
	public function mbkmDaftar()
	{
		if (session('role') !== 'mahasiswa') {
			return redirect()->to('/login');
		}

		$db = \Config\Database::connect();

		// Get available MBKM activity types
		$builderJenis = $db->table('mbkm_jenis_kegiatan');
		$builderJenis->where('status', 'aktif');
		$builderJenis->orderBy('kode_kegiatan', 'ASC');
		$jenisKegiatan = $builderJenis->get()->getResultArray();

		// Get list of dosen for pembimbing
		$builderDosen = $db->table('dosen');
		$builderDosen->where('status_keaktifan', 'Aktif');
		$builderDosen->orderBy('nama_lengkap', 'ASC');
		$dosenList = $builderDosen->get()->getResultArray();

		$data = [
			'title' => 'Daftar Kegiatan MBKM',
			'jenisKegiatan' => $jenisKegiatan,
			'dosenList' => $dosenList
		];

		return view('mahasiswa/mbkm/mbkm_daftar', $data);
	}

	/**
	 * Mahasiswa - Store MBKM Registration
	 */
	public function mbkmDaftarStore()
	{
		if (session('role') !== 'mahasiswa') {
			return redirect()->to('/login');
		}

		$mahasiswaId = session('mahasiswa_id');
		$db = \Config\Database::connect();

		// Validation
		$validation = \Config\Services::validation();
		$validation->setRules([
			'jenis_kegiatan_id' => 'required|integer',
			'judul_kegiatan' => 'required|min_length[10]|max_length[255]',
			'tempat_kegiatan' => 'required|max_length[255]',
			'tanggal_mulai' => 'required|valid_date',
			'tanggal_selesai' => 'required|valid_date',
			'pembimbing_lapangan' => 'permit_empty|max_length[150]',
			'kontak_pembimbing' => 'permit_empty|max_length[50]',
			'dosen_pembimbing_id' => 'permit_empty|integer',
			'deskripsi_kegiatan' => 'permit_empty',
			'dokumen_pendukung' => 'permit_empty|uploaded[dokumen_pendukung]|max_size[dokumen_pendukung,2048]|ext_in[dokumen_pendukung,pdf,doc,docx]'
		]);

		if (!$validation->withRequest($this->request)->run()) {
			session()->setFlashdata('errors', $validation->getErrors());
			return redirect()->back()->withInput();
		}

		// Calculate duration in weeks
		$tanggalMulai = new \DateTime($this->request->getPost('tanggal_mulai'));
		$tanggalSelesai = new \DateTime($this->request->getPost('tanggal_selesai'));
		$durasi = $tanggalMulai->diff($tanggalSelesai);
		$durasiMinggu = ceil($durasi->days / 7);

		// Handle file upload
		$dokumenName = null;
		$dokumen = $this->request->getFile('dokumen_pendukung');
		if ($dokumen && $dokumen->isValid() && !$dokumen->hasMoved()) {
			$dokumenName = $dokumen->getRandomName();
			$dokumen->move(WRITEPATH . '../public/uploads/mbkm/', $dokumenName);
		}

		// Get current academic year
		$tahunAkademik = $this->getCurrentAcademicYear();

		// Insert into mbkm_kegiatan
		$builderKegiatan = $db->table('mbkm_kegiatan');
		$kegiatanData = [
			'jenis_kegiatan_id' => $this->request->getPost('jenis_kegiatan_id'),
			'judul_kegiatan' => $this->request->getPost('judul_kegiatan'),
			'tempat_kegiatan' => $this->request->getPost('tempat_kegiatan'),
			'pembimbing_lapangan' => $this->request->getPost('pembimbing_lapangan'),
			'kontak_pembimbing' => $this->request->getPost('kontak_pembimbing'),
			'dosen_pembimbing_id' => $this->request->getPost('dosen_pembimbing_id') ?: null,
			'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
			'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
			'durasi_minggu' => $durasiMinggu,
			'sks_dikonversi' => 20, // Default 20 SKS
			'deskripsi_kegiatan' => $this->request->getPost('deskripsi_kegiatan'),
			'dokumen_pendukung' => $dokumenName,
			'status_kegiatan' => 'diajukan',
			'tahun_akademik' => $tahunAkademik
		];

		$builderKegiatan->insert($kegiatanData);
		$kegiatanId = $db->insertID();

		// Link mahasiswa to kegiatan
		$builderKegiatanMhs = $db->table('mbkm_kegiatan_mahasiswa');
		$builderKegiatanMhs->insert([
			'kegiatan_id' => $kegiatanId,
			'mahasiswa_id' => $mahasiswaId,
			'peran' => 'Peserta'
		]);

		session()->setFlashdata('success', 'Pendaftaran kegiatan MBKM berhasil diajukan. Menunggu persetujuan.');
		return redirect()->to('mahasiswa/mbkm');
	}

	/**
	 * Mahasiswa - View MBKM Detail
	 */
	public function mbkmDetail($kegiatanId)
	{
		if (session('role') !== 'mahasiswa') {
			return redirect()->to('/login');
		}

		$mahasiswaId = session('mahasiswa_id');
		$db = \Config\Database::connect();

		// Get kegiatan detail
		$builder = $db->table('mbkm_kegiatan_mahasiswa km');
		$builder->select('k.*, jk.nama_kegiatan, jk.kode_kegiatan, d.nama_lengkap as dosen_pembimbing, 
                      na.nilai_angka, na.nilai_huruf, na.status_kelulusan, na.catatan_akhir');
		$builder->join('mbkm_kegiatan k', 'km.kegiatan_id = k.id');
		$builder->join('mbkm_jenis_kegiatan jk', 'k.jenis_kegiatan_id = jk.id');
		$builder->join('dosen d', 'k.dosen_pembimbing_id = d.id', 'left');
		$builder->join('mbkm_nilai_akhir na', 'k.id = na.kegiatan_id', 'left');
		$builder->where('km.mahasiswa_id', $mahasiswaId);
		$builder->where('k.id', $kegiatanId);
		$kegiatan = $builder->get()->getRowArray();

		if (!$kegiatan) {
			session()->setFlashdata('error', 'Data kegiatan tidak ditemukan.');
			return redirect()->to('mahasiswa/mbkm/mbkm');
		}

		// Get nilai komponen if exists
		$builderNilai = $db->table('mbkm_nilai n');
		$builderNilai->select('n.*, k.nama_komponen, k.bobot');
		$builderNilai->join('mbkm_komponen_nilai k', 'n.komponen_id = k.id');
		$builderNilai->where('n.kegiatan_id', $kegiatanId);
		$nilaiKomponen = $builderNilai->get()->getResultArray();

		$data = [
			'title' => 'Detail Kegiatan MBKM',
			'kegiatan' => $kegiatan,
			'nilaiKomponen' => $nilaiKomponen
		];

		return view('mahasiswa/mbkm/mbkm_detail', $data);
	}

	/**
	 * Helper function to get current academic year
	 */
	private function getCurrentAcademicYear()
	{
		$currentMonth = date('n');
		$currentYear = date('Y');

		if ($currentMonth >= 8) {
			// August to December = Odd Semester
			return $currentYear . '/' . ($currentYear + 1) . ' Ganjil';
		} else {
			// January to July = Even Semester
			return ($currentYear - 1) . '/' . $currentYear . ' Genap';
		}
	}

	/**
	 * Mahasiswa - View/Edit Profile
	 */
	public function profil()
	{
		if (session('role') !== 'mahasiswa') {
			return redirect()->to('/login');
		}

		$mahasiswaId = session('mahasiswa_id');
		$mahasiswaData = $this->mahasiswaModel->find($mahasiswaId);

		// Get user data if user_id exists
		$userData = null;
		if ($mahasiswaData['user_id']) {
			$db = \Config\Database::connect();
			$builder = $db->table('users');
			$userData = $builder->where('id', $mahasiswaData['user_id'])->get()->getRowArray();
		}

		$data = [
			'title' => 'Profil Saya',
			'mahasiswa' => $mahasiswaData,
			'user' => $userData
		];

		return view('mahasiswa/profil', $data);
	}

	/**
	 * Mahasiswa - Change Password
	 */
	public function changePassword()
	{
		if (session('role') !== 'mahasiswa') {
			return redirect()->to('/login');
		}

		$mahasiswaId = session('mahasiswa_id');
		$mahasiswaData = $this->mahasiswaModel->find($mahasiswaId);

		// Get the user_id from mahasiswa
		$userId = $mahasiswaData['user_id'];

		if (!$userId) {
			session()->setFlashdata('error', 'User account not found.');
			return redirect()->to('mahasiswa/profil');
		}

		// Get user data
		$db = \Config\Database::connect();
		$builder = $db->table('users');
		$user = $builder->where('id', $userId)->get()->getRowArray();

		if (!$user) {
			session()->setFlashdata('error', 'User account not found.');
			return redirect()->to('mahasiswa/profil');
		}

		// Validate input
		$oldPassword = $this->request->getPost('old_password');
		$newPassword = $this->request->getPost('new_password');
		$confirmPassword = $this->request->getPost('confirm_password');

		// Manual validation
		$errors = [];

		if (empty($oldPassword)) {
			$errors['old_password'] = 'Password lama wajib diisi.';
		} else {
			// Verify old password
			if (!password_verify($oldPassword, $user['password'])) {
				$errors['old_password'] = 'Password lama tidak sesuai.';
			}
		}

		if (empty($newPassword)) {
			$errors['new_password'] = 'Password baru wajib diisi.';
		} elseif (strlen($newPassword) < 8) {
			$errors['new_password'] = 'Password baru minimal 8 karakter.';
		}

		if (empty($confirmPassword)) {
			$errors['confirm_password'] = 'Konfirmasi password wajib diisi.';
		} elseif ($newPassword !== $confirmPassword) {
			$errors['confirm_password'] = 'Konfirmasi password tidak cocok.';
		}

		if (!empty($errors)) {
			session()->setFlashdata('errors', $errors);
			return redirect()->to('mahasiswa/profil');
		}

		// Update password
		$hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
		$builder->where('id', $userId);
		$builder->update(['password' => $hashedPassword]);

		session()->setFlashdata('success', 'Password berhasil diubah.');
		return redirect()->to('mahasiswa/profil');
	}
}

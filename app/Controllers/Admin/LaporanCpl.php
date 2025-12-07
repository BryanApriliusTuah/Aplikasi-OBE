<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class LaporanCpl extends BaseController
{
	protected $db;
	protected $cplModel;
	protected $profilProdiModel;

	public function __construct()
	{
		$this->db = \Config\Database::connect();
		$this->cplModel = new \App\Models\CplModel();
		$this->profilProdiModel = new \App\Models\ProfilProdiModel();
	}

	public function index()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return redirect()->to('/')->with('error', 'Akses ditolak.');
		}

		$data = [
			'title' => 'Laporan CPL - Laporan Pemenuhan Capaian Pembelajaran Lulusan',
			'tahunAkademik' => $this->getTahunAkademik(),
			'programStudi' => $this->getProgramStudi()
		];

		return view('admin/laporan_cpl/index', $data);
	}

	public function generate()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return redirect()->to('/')->with('error', 'Akses ditolak.');
		}

		$tahunAkademik = $this->request->getGet('tahun_akademik');
		$programStudi = $this->request->getGet('program_studi');
		$angkatan = $this->request->getGet('angkatan');

		if (!$tahunAkademik || !$programStudi || !$angkatan) {
			return redirect()->to('admin/laporan-cpl')->with('error', 'Tahun akademik, program studi, dan angkatan harus dipilih.');
		}

		// Get CPL report data
		$reportData = $this->getReportData($tahunAkademik, $programStudi, $angkatan);

		if (!$reportData) {
			return redirect()->to('admin/laporan-cpl')->with('error', 'Data tidak ditemukan untuk filter yang dipilih.');
		}

		$data = [
			'title' => 'Laporan Pemenuhan CPL',
			'report' => $reportData
		];

		return view('admin/laporan_cpl/portfolio', $data);
	}

	public function generatePdf()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return redirect()->to('/')->with('error', 'Akses ditolak.');
		}

		$tahunAkademik = $this->request->getGet('tahun_akademik');
		$programStudi = $this->request->getGet('program_studi');
		$angkatan = $this->request->getGet('angkatan');

		if (!$tahunAkademik || !$programStudi || !$angkatan) {
			return redirect()->to('admin/laporan-cpl')->with('error', 'Tahun akademik, program studi, dan angkatan harus dipilih.');
		}

		// Get CPL report data
		$reportData = $this->getReportData($tahunAkademik, $programStudi, $angkatan);

		if (!$reportData) {
			return redirect()->to('admin/laporan-cpl')->with('error', 'Data tidak ditemukan untuk filter yang dipilih.');
		}

		$data = [
			'report' => $reportData
		];

		// Return PDF-optimized view
		return view('admin/laporan_cpl/portfolio_pdf', $data);
	}

	private function getReportData($tahunAkademik, $programStudi, $angkatan)
	{
		// 1. Get Program Studi Identity from database
		$profilProdi = $this->profilProdiModel->where('nama_prodi', $programStudi)->first();

		// If profil prodi not found in database, use default values
		if (!$profilProdi) {
			$identitas = [
				'nama_program_studi' => $programStudi,
				'fakultas' => 'Fakultas Teknik',
				'perguruan_tinggi' => 'Universitas Palangka Raya',
				'tahun_akademik' => $tahunAkademik,
				'angkatan' => $angkatan,
				'ketua_prodi' => '-'
			];
		} else {
			$identitas = [
				'nama_program_studi' => $profilProdi['nama_prodi'],
				'fakultas' => $profilProdi['nama_fakultas'],
				'perguruan_tinggi' => $profilProdi['nama_universitas'],
				'tahun_akademik' => $tahunAkademik,
				'angkatan' => $angkatan,
				'ketua_prodi' => $profilProdi['nama_ketua_prodi']
			];
		}

		// 2. Get CPL List
		$cplList = $this->getCplList();

		if (empty($cplList)) {
			return null;
		}

		// 3. Get CPMK-CPL Matrix
		$cpmkCplMatrix = $this->getCpmkCplMatrix($programStudi);

		// 4. Get CPL Achievement Data for the cohort
		$cplAchievementData = $this->getCplAchievementData($programStudi, $angkatan, $tahunAkademik);

		// 5. Get Analysis
		$analysis = $this->getAnalysisData($cplAchievementData);

		return [
			'identitas' => $identitas,
			'cpl_list' => $cplList,
			'cpmk_cpl_matrix' => $cpmkCplMatrix,
			'cpl_achievement' => $cplAchievementData,
			'analysis' => $analysis
		];
	}

	private function getCplList()
	{
		// Get all CPL (CPL is shared across all program studi)
		$query = "
            SELECT
                cpl.id,
                cpl.kode_cpl,
                cpl.deskripsi,
                cpl.jenis_cpl,
                GROUP_CONCAT(DISTINCT pl.kode_pl ORDER BY pl.kode_pl SEPARATOR ', ') as sumber_turunan
            FROM cpl
            LEFT JOIN cpl_pl ON cpl.id = cpl_pl.cpl_id
            LEFT JOIN profil_lulusan pl ON cpl_pl.pl_id = pl.id
            GROUP BY cpl.id, cpl.kode_cpl, cpl.deskripsi, cpl.jenis_cpl
            ORDER BY cpl.kode_cpl
        ";

		return $this->db->query($query)->getResultArray();
	}

	private function getCpmkCplMatrix($programStudi)
	{
		// Get all mata kuliah for the program through jadwal_mengajar
		// Calculate bobot_cpmk from rps_mingguan
		$query = "
            SELECT DISTINCT
                mk.id as mata_kuliah_id,
                mk.kode_mk,
                mk.nama_mk,
                c.id as cpmk_id,
                c.kode_cpmk,
                COALESCE(
                    (SELECT SUM(rm.bobot)
                     FROM rps_mingguan rm
                     INNER JOIN rps r ON rm.rps_id = r.id
                     WHERE rm.cpmk_id = c.id AND r.mata_kuliah_id = mk.id
                    ), 0
                ) as bobot_cpmk,
                cc.cpl_id,
                cpl.kode_cpl
            FROM mata_kuliah mk
            INNER JOIN jadwal_mengajar jm ON mk.id = jm.mata_kuliah_id
            INNER JOIN cpmk_mk cm ON mk.id = cm.mata_kuliah_id
            INNER JOIN cpmk c ON cm.cpmk_id = c.id
            LEFT JOIN cpl_cpmk cc ON c.id = cc.cpmk_id
            LEFT JOIN cpl ON cc.cpl_id = cpl.id
            WHERE jm.program_studi = ?
            ORDER BY mk.nama_mk, c.kode_cpmk, cpl.kode_cpl
        ";

		$results = $this->db->query($query, [$programStudi])->getResultArray();

		// Group by mata kuliah
		$matrix = [];
		foreach ($results as $row) {
			$mkKey = $row['mata_kuliah_id'];

			if (!isset($matrix[$mkKey])) {
				$matrix[$mkKey] = [
					'nama_mk' => $row['nama_mk'],
					'kode_mk' => $row['kode_mk'],
					'cpmk_list' => []
				];
			}

			$cpmkKey = $row['cpmk_id'];
			if (!isset($matrix[$mkKey]['cpmk_list'][$cpmkKey])) {
				$matrix[$mkKey]['cpmk_list'][$cpmkKey] = [
					'kode_cpmk' => $row['kode_cpmk'],
					'bobot_cpmk' => $row['bobot_cpmk'],
					'cpl_terkait' => []
				];
			}

			if ($row['kode_cpl']) {
				$matrix[$mkKey]['cpmk_list'][$cpmkKey]['cpl_terkait'][] = $row['kode_cpl'];
			}
		}

		// Convert to indexed array
		return array_values($matrix);
	}

	private function getCplAchievementData($programStudi, $angkatan, $tahunAkademik)
	{
		$cplList = $this->getCplList($programStudi);
		$achievementData = [];

		// Get all students in the cohort
		$students = $this->db->table('mahasiswa')
			->where('program_studi', $programStudi)
			->where('tahun_angkatan', $angkatan)
			->where('status_mahasiswa', 'Aktif')
			->get()
			->getResultArray();

		if (empty($students)) {
			return [];
		}

		$studentIds = array_column($students, 'id');

		foreach ($cplList as $cpl) {
			// Get all CPMK linked to this CPL
			$cpmkLinked = $this->db->table('cpl_cpmk')
				->select('cpmk_id')
				->where('cpl_id', $cpl['id'])
				->get()
				->getResultArray();

			if (empty($cpmkLinked)) {
				$achievementData[] = [
					'kode_cpl' => $cpl['kode_cpl'],
					'cpmk_kontributor' => [],
					'capaian_rata_rata_cpmk' => 0,
					'capaian_cpl' => 0,
					'capaian_cpl_persen' => 0
				];
				continue;
			}

			$cpmkIds = array_column($cpmkLinked, 'cpmk_id');

			// Get CPMK details with ALL mata kuliah info
			$cpmkDetails = [];
			$totalBobot = 0;
			foreach ($cpmkIds as $cpmkId) {
				// Get ALL mata kuliah that have this CPMK from jadwal_mengajar (specific to tahun_akademik and program_studi)
				$cpmkMataKuliahList = $this->db->table('cpmk c')
					->select('c.id, c.kode_cpmk, cm.mata_kuliah_id, mk.nama_mk')
					->join('cpmk_mk cm', 'cm.cpmk_id = c.id')
					->join('mata_kuliah mk', 'mk.id = cm.mata_kuliah_id')
					->join('jadwal_mengajar jm', 'jm.mata_kuliah_id = mk.id')
					->where('c.id', $cpmkId)
					->where('jm.tahun_akademik', $tahunAkademik)
					->where('jm.program_studi', $programStudi)
					->groupBy('cm.mata_kuliah_id, c.id, c.kode_cpmk, mk.nama_mk')
					->get()
					->getResultArray(); // Get ALL mata kuliah for this CPMK in this tahun_akademik and program_studi

				if (!empty($cpmkMataKuliahList)) {
					// Collect all mata kuliah IDs and names for this CPMK
					$mataKuliahIds = [];
					$mataKuliahNames = [];
					$totalBobotCpmk = 0;

					foreach ($cpmkMataKuliahList as $cpmkMk) {
						$mataKuliahIds[] = $cpmkMk['mata_kuliah_id'];
						$mataKuliahNames[] = $cpmkMk['nama_mk'];

						// Calculate bobot from rps_mingguan for each mata kuliah
						$bobotQuery = "
							SELECT COALESCE(SUM(rm.bobot), 0) as total_bobot
							FROM rps_mingguan rm
							INNER JOIN rps r ON rm.rps_id = r.id
							WHERE rm.cpmk_id = ? AND r.mata_kuliah_id = ?
						";
						$bobotResult = $this->db->query($bobotQuery, [$cpmkId, $cpmkMk['mata_kuliah_id']])->getRowArray();
						$totalBobotCpmk += $bobotResult['total_bobot'] ?? 0;
					}

					$cpmkDetails[] = [
						'cpmk_id' => $cpmkId,
						'kode_cpmk' => $cpmkMataKuliahList[0]['kode_cpmk'],
						'mata_kuliah_ids' => $mataKuliahIds,
						'mata_kuliah_names' => $mataKuliahNames,
						'bobot_cpmk' => $totalBobotCpmk
					];

					$totalBobot += $totalBobotCpmk;
				}
			}

			// Get all nilai_cpmk for all students for these CPMK
			$nilaiList = $this->db->table('nilai_cpmk_mahasiswa ncm')
				->select('ncm.nilai_cpmk, ncm.cpmk_id, ncm.mahasiswa_id, jm.mata_kuliah_id')
				->join('jadwal_mengajar jm', 'jm.id = ncm.jadwal_mengajar_id')
				->whereIn('ncm.mahasiswa_id', $studentIds)
				->whereIn('ncm.cpmk_id', $cpmkIds)
				->get()
				->getResultArray();

			// Calculate CPL for each student using formula:
			// Nilai CPL = Σ(CPMK scores), Capaian CPL (%) = (Nilai CPL / Total Bobot) × 100
			$studentScores = [];
			$nilaiCpl = 0;
			
			foreach ($nilaiList as $nilai) {
				// Get bobot from rps_mingguan
				$rps = $this->db->table('rps')
					->select('id')
					->where('mata_kuliah_id', $nilai['mata_kuliah_id'])
					->orderBy('created_at', 'DESC')
					->get()
					->getRowArray();
				
				$bobot = 0;
				if ($rps) {
					// Sum bobot across all weeks for this CPMK
					$bobotResult = $this->db->table('rps_mingguan')
						->selectSum('bobot')
						->where('rps_id', $rps['id'])
						->where('cpmk_id', $nilai['cpmk_id'])
						->get()
						->getRowArray();

					$bobot = $bobotResult['bobot'] ?? 0;
				}

				$mhsId = $nilai['mahasiswa_id'];
				if (!isset($studentScores[$mhsId])) {
					$studentScores[$mhsId] = [
						'nilaiCpl' => 0,
						'totalBobot' => 0
					];
				}

				// Sum CPMK scores and bobot
				if ($bobot > 0) {
					$nilaiCpl += $nilai['nilai_cpmk'];
					$studentScores[$mhsId]['nilaiCpl'] += $nilai['nilai_cpmk'];
					$studentScores[$mhsId]['totalBobot'] += $bobot;
				}
			}

			// Calculate average Capaian CPL (%) across all students
			$totalCplScore = 0;
			$studentCount = 0;
			
			foreach ($studentScores as $mhsId => $scores) {
				if ($scores['totalBobot'] > 0) {
					$cplScore = $scores['nilaiCpl'];
					$totalCplScore += ($cplScore / $scores['totalBobot']) * 100;
					$studentCount++;
				}
			}

			$average = $studentCount > 0 ? round($totalCplScore / $studentCount, 2) : 0;
			
			// Calculate CPMK contributors for this CPL
			$cpmkContributors = [];

			foreach ($cpmkDetails as $cpmkDetail) {
				// Get average CPMK score for this CPMK from nilai_cpmk_mahasiswa
				$cpmkScores = array_filter($nilaiList, function($item) use ($cpmkDetail) {
					return $item['cpmk_id'] == $cpmkDetail['cpmk_id'];
				});

				if (empty($cpmkScores)) {
					continue;
				}

				// Calculate average score across all students for this CPMK
				$totalScore = array_sum(array_column($cpmkScores, 'nilai_cpmk'));
				$countStudents = count(array_unique(array_column($cpmkScores, 'mahasiswa_id'))); // Unique students count
				$avgScore = $countStudents > 0 ? $totalScore / $countStudents : 0;

				// Use bobot from cpmkDetails (already calculated from rps_mingguan)
				$bobot = $cpmkDetail['bobot_cpmk'];

				if ($bobot > 0) {
					$cpmkContributors[] = [
						'kode_cpmk' => $cpmkDetail['kode_cpmk'],
						'mata_kuliah_names' => $cpmkDetail['mata_kuliah_names'],
						'capaian_rata_rata' => round($avgScore, 2),
						'bobot' => $bobot
					];
				}
			}

			$achievementData[] = [
				'kode_cpl' => $cpl['kode_cpl'],
				'cpmk_kontributor' => $cpmkContributors,
				'capaian_cpl' => round($nilaiCpl, 2),
				'total_bobot' => $totalBobot,
				'total_capaian_cpl_persen' => round($totalCplScore, 2),
				'capaian_cpl_persen' => round($average, 2)
			];
		
	}
		// DD($achievementData);
		return $achievementData;
	}

	private function getAnalysisData($cplAchievementData)
	{
		$passingThreshold = 75; // Standard 75% for CPL
		$cplTercapai = [];
		$cplTidakTercapai = [];

		foreach ($cplAchievementData as $cpl) {
			if ($cpl['capaian_cpl_persen'] >= $passingThreshold) {
				$cplTercapai[] = $cpl['kode_cpl'];
			} else {
				$cplTidakTercapai[] = [
					'kode_cpl' => $cpl['kode_cpl'],
					'capaian' => $cpl['capaian_cpl_persen']
				];
			}
		}

		// Generate analysis summary
		$analysisSummary = $this->generateAnalysisSummary($cplTercapai, $cplTidakTercapai);

		return [
			'cpl_tercapai' => $cplTercapai,
			'cpl_tidak_tercapai' => $cplTidakTercapai,
			'passing_threshold' => $passingThreshold,
			'analisis_summary' => $analysisSummary,
			'total_cpl' => count($cplAchievementData),
			'total_tercapai' => count($cplTercapai),
			'total_tidak_tercapai' => count($cplTidakTercapai)
		];
	}

	private function generateAnalysisSummary($cplTercapai, $cplTidakTercapai)
	{
		if (empty($cplTidakTercapai)) {
			return 'Semua CPL tercapai dengan baik. Mahasiswa menunjukkan kompetensi yang memadai sesuai dengan profil lulusan yang diharapkan.';
		}

		$jumlahTidakTercapai = count($cplTidakTercapai);
		$cplList = array_column($cplTidakTercapai, 'kode_cpl');
		$cplListStr = implode(', ', $cplList);

		return "Terdapat $jumlahTidakTercapai CPL yang belum tercapai ($cplListStr). Diperlukan evaluasi lebih lanjut terhadap mata kuliah kontributor dan metode pembelajaran untuk meningkatkan capaian CPL tersebut.";
	}

	private function getTahunAkademik()
	{
		return $this->db->table('jadwal_mengajar')
			->select('tahun_akademik')
			->groupBy('tahun_akademik')
			->orderBy('tahun_akademik', 'DESC')
			->get()
			->getResultArray();
	}

	private function getProgramStudi()
	{
		return $this->db->table('jadwal_mengajar')
			->select('program_studi')
			->groupBy('program_studi')
			->orderBy('program_studi', 'ASC')
			->get()
			->getResultArray();
	}

	private function getAngkatan()
	{
		return $this->db->table('mahasiswa')
			->select('tahun_angkatan')
			->groupBy('tahun_angkatan')
			->orderBy('tahun_angkatan', 'DESC')
			->get()
			->getResultArray();
	}

	public function getAngkatanByFilter()
	{
		$programStudi = $this->request->getGet('program_studi');

		$builder = $this->db->table('mahasiswa')
			->select('tahun_angkatan')
			->where('status_mahasiswa', 'Aktif')
			->groupBy('tahun_angkatan')
			->orderBy('tahun_angkatan', 'DESC');

		if ($programStudi) {
			$builder->where('program_studi', $programStudi);
		}

		$angkatan = $builder->get()->getResultArray();

		return $this->response->setJSON($angkatan);
	}
}

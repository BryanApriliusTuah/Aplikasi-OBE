<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class LaporanCpl extends BaseController
{
	protected $db;
	protected $cplModel;
	protected $profilProdiModel;
	protected $programStudiModel;
	protected $fakultasModel;
	protected $analysisCplModel;
	protected $cqiModel;
	protected $standarMinimalCapaianModel;
	protected $analysisTemplateCplModel;

	public function __construct()
	{
		$this->db = \Config\Database::connect();
		$this->cplModel = new \App\Models\CplModel();
		$this->profilProdiModel = new \App\Models\ProfilProdiModel();
		$this->programStudiModel = new \App\Models\ProgramStudiModel();
		$this->fakultasModel = new \App\Models\FakultasModel();
		$this->analysisCplModel = new \App\Models\AnalysisCplModel();
		$this->cqiModel = new \App\Models\CqiModel();
		$this->standarMinimalCapaianModel = new \App\Models\StandarMinimalCplModel();
		$this->analysisTemplateCplModel = new \App\Models\AnalysisTemplateCplModel();
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
		$programStudiKode = $this->request->getGet('program_studi');
		$angkatan = $this->request->getGet('angkatan');

		if (!$tahunAkademik || !$programStudiKode || !$angkatan) {
			return redirect()->to('admin/laporan-cpl')->with('error', 'Tahun akademik, program studi, dan angkatan harus dipilih.');
		}

		// Get CPL report data
		$reportData = $this->getReportData($tahunAkademik, $programStudiKode, $angkatan);

		if (!$reportData) {
			return redirect()->to('admin/laporan-cpl')->with('error', 'Data tidak ditemukan untuk filter yang dipilih.');
		}

		$data = [
			'report' => $reportData
		];

		// Return PDF-optimized view
		return view('admin/laporan_cpl/portfolio_pdf', $data);
	}

	private function getReportData($tahunAkademik, $programStudiKode, $angkatan)
	{
		// 1. Get Program Studi Identity from database
		$programStudi = $this->programStudiModel->where('kode', $programStudiKode)->first();

		// If profil prodi not found in database, use default values
		if (!$programStudi) {
			$identitas = [
				'nama_program_studi' => $programStudiKode,
				'fakultas' => 'Fakultas Teknik',
				'perguruan_tinggi' => 'Universitas Palangka Raya',
				'tahun_akademik' => $tahunAkademik,
				'angkatan' => $angkatan,
				'ketua_prodi' => '-'
			];
		} else {
			$fakultas = $this->fakultasModel->where('kode', $programStudi['fakultas_kode'])->first();
			$identitas = [
				'nama_program_studi' => $programStudi['nama_resmi'],
				'fakultas' => $fakultas['nama_resmi'],
				'perguruan_tinggi' => 'Universitas Palangka Raya',
				'tahun_akademik' => $tahunAkademik,
				'angkatan' => $angkatan,
				'ketua_prodi' => $programStudi['nama_kaprodi']
			];
		}

		// 2. Get CPL List
		$cplList = $this->getCplList();

		if (empty($cplList)) {
			return null;
		}

		// 3. Get CPMK-CPL Matrix
		$cpmkCplMatrix = $this->getCpmkCplMatrix($programStudiKode);

		// 4. Get CPL Achievement Data for the cohort
		$cplAchievementData = $this->getCplAchievementData($programStudiKode, $angkatan, $tahunAkademik);

		// 5. Get Analysis
		$analysis = $this->getAnalysisData($cplAchievementData, $programStudiKode, $tahunAkademik, $angkatan);

		// 6. Get Jadwal and RPS data for Lampiran section
		$lampiranData = $this->getLampiranData($programStudiKode, $tahunAkademik, $cpmkCplMatrix);

		// 7. Get CQI data
		$cqiData = $this->getCqiData($programStudiKode, $tahunAkademik, $angkatan);

		// 8. Get document files from analysis record
		$analysisRecord = $this->analysisCplModel->getAnalysis($programStudiKode, $tahunAkademik, $angkatan);
		$buktiDokumentasiFile = $analysisRecord['bukti_dokumentasi_file'] ?? null;
		$notulensiRapatFile = $analysisRecord['notulensi_rapat_file'] ?? null;

		return [
			'identitas' => $identitas,
			'cpl_list' => $cplList,
			'cpmk_cpl_matrix' => $cpmkCplMatrix,
			'cpl_achievement' => $cplAchievementData,
			'analysis' => $analysis,
			'lampiran' => $lampiranData,
			'cqi_data' => $cqiData,
			'bukti_dokumentasi_file' => $buktiDokumentasiFile,
			'notulensi_rapat_file' => $notulensiRapatFile
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
		// Get all mata kuliah for the program through jadwal
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
            INNER JOIN jadwal jm ON mk.id = jm.mata_kuliah_id
            INNER JOIN cpmk_mk cm ON mk.id = cm.mata_kuliah_id
            INNER JOIN cpmk c ON cm.cpmk_id = c.id
            LEFT JOIN cpl_cpmk cc ON c.id = cc.cpmk_id
            LEFT JOIN cpl ON cc.cpl_id = cpl.id
            WHERE jm.program_studi_kode = ?
            ORDER BY mk.nama_mk, c.kode_cpmk, cpl.kode_cpl
        ";

		$results = $this->db->query($query, [$programStudi])->getResultArray();

		// Group by mata kuliah
		$matrix = [];
		foreach ($results as $row) {
			$mkKey = $row['mata_kuliah_id'];

			if (!isset($matrix[$mkKey])) {
				$matrix[$mkKey] = [
					'mata_kuliah_id' => $row['mata_kuliah_id'],
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
			->where('program_studi_kode', $programStudi)
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
				// Get ALL mata kuliah that have this CPMK from jadwal (specific to tahun_akademik and program_studi)
				$cpmkMataKuliahList = $this->db->table('cpmk c')
					->select('c.id, c.kode_cpmk, cm.mata_kuliah_id, mk.nama_mk')
					->join('cpmk_mk cm', 'cm.cpmk_id = c.id')
					->join('mata_kuliah mk', 'mk.id = cm.mata_kuliah_id')
					->join('jadwal jm', 'jm.mata_kuliah_id = mk.id')
					->where('c.id', $cpmkId)
					->like('jm.tahun_akademik', $tahunAkademik, 'both')
					->where('jm.program_studi_kode', $programStudi)
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
				->join('jadwal jm', 'jm.id = ncm.jadwal_id')
				->whereIn('ncm.mahasiswa_id', $studentIds)
				->whereIn('ncm.cpmk_id', $cpmkIds)
				->like('jm.tahun_akademik', $tahunAkademik, 'both')
				->where('jm.program_studi_kode', $programStudi)
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
				$cpmkScores = array_filter($nilaiList, function ($item) use ($cpmkDetail) {
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

	private function getAnalysisData($cplAchievementData, $programStudi = null, $tahunAkademik = null, $angkatan = null)
	{
		// Get passing threshold from database
		$passingThreshold = $this->standarMinimalCapaianModel->getPersentase();
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

		// Check if there's saved analysis
		$savedAnalysis = null;
		if ($programStudi && $tahunAkademik && $angkatan) {
			$savedAnalysis = $this->analysisCplModel->getAnalysis($programStudi, $tahunAkademik, $angkatan);
		}

		// Determine mode and analysis text
		$mode = $savedAnalysis['mode'] ?? 'auto';
		$analysisSummary = '';

		// Get auto_options
		$autoOptions = [];
		if (!empty($savedAnalysis['auto_options'])) {
			$autoOptions = json_decode($savedAnalysis['auto_options'], true);
			if (!is_array($autoOptions)) {
				$autoOptions = [];
			}
		}

		// Get templates - initialize defaults if empty
		$templates = $this->analysisTemplateCplModel->getTemplatesAsArray();
		if (empty($templates)) {
			$this->initializeDefaultTemplates();
			$templates = $this->analysisTemplateCplModel->getTemplatesAsArray();
		}

		if ($mode === 'manual' && !empty($savedAnalysis['analisis_summary'])) {
			// Use manual analysis
			$analysisSummary = $savedAnalysis['analisis_summary'];
		} else {
			// Use auto-generated analysis
			$analysisSummary = $this->generateAnalysisSummary($cplTercapai, $cplTidakTercapai, $passingThreshold, $autoOptions, $templates);
		}

		return [
			'cpl_tercapai' => $cplTercapai,
			'cpl_tidak_tercapai' => $cplTidakTercapai,
			'passing_threshold' => $passingThreshold,
			'analisis_summary' => $analysisSummary,
			'total_cpl' => count($cplAchievementData),
			'total_tercapai' => count($cplTercapai),
			'total_tidak_tercapai' => count($cplTidakTercapai),
			'mode' => $mode,
			'auto_options' => $autoOptions,
			'templates' => $templates
		];
	}

	private function generateAnalysisSummary($cplTercapai, $cplTidakTercapai, $passingThreshold, $autoOptions = [], $templates = [])
	{
		// Get selected template if auto_options is provided
		$selectedTemplateKey = !empty($autoOptions) && isset($autoOptions[0]) ? $autoOptions[0] : 'default';
		$template = $templates[$selectedTemplateKey] ?? null;

		// Calculate statistics
		$totalCpl = count($cplTercapai) + count($cplTidakTercapai);
		$jumlahTercapai = count($cplTercapai);
		$jumlahTidakTercapai = count($cplTidakTercapai);
		$persentaseTercapai = $totalCpl > 0 ? round(($jumlahTercapai / $totalCpl) * 100, 2) : 0;
		$cplTercapaiList = implode(', ', $cplTercapai);
		$cplTidakTercapaiList = implode(', ', array_column($cplTidakTercapai, 'kode_cpl'));

		// Use template if available
		if ($template) {
			if (empty($cplTidakTercapai)) {
				$analysisSummary = $template['template_tercapai'] ?? '';
			} else {
				$analysisSummary = $template['template_tidak_tercapai'] ?? '';
			}

			// Replace placeholders
			$analysisSummary = str_replace(
				['{total_cpl}', '{jumlah_tercapai}', '{jumlah_tidak_tercapai}', '{persentase_tercapai}', '{cpl_tercapai_list}', '{cpl_tidak_tercapai_list}', '{standar_minimal}'],
				[$totalCpl, $jumlahTercapai, $jumlahTidakTercapai, $persentaseTercapai, $cplTercapaiList, $cplTidakTercapaiList, $passingThreshold],
				$analysisSummary
			);

			return $analysisSummary;
		}

		// Default analysis if no template
		if (empty($cplTidakTercapai)) {
			return 'Semua CPL tercapai dengan baik. Mahasiswa menunjukkan kompetensi yang memadai sesuai dengan profil lulusan yang diharapkan.';
		}

		return "Terdapat $jumlahTidakTercapai CPL yang belum tercapai ($cplTidakTercapaiList). Diperlukan evaluasi lebih lanjut terhadap mata kuliah kontributor dan metode pembelajaran untuk meningkatkan capaian CPL tersebut.";
	}

	private function getTahunAkademik()
	{
		return $this->db->table('jadwal')
			->select('TRIM(REPLACE(REPLACE(tahun_akademik, "Genap", ""), "Ganjil", "")) as tahun_akademik')
			->groupBy('TRIM(REPLACE(REPLACE(tahun_akademik, "Genap", ""), "Ganjil", ""))')
			->orderBy('tahun_akademik', 'DESC')
			->get()
			->getResultArray();
	}

	private function getProgramStudi()
	{
		$builder = $this->db->table('program_studi');
		$result = $builder
			->select('kode, nama_resmi')
			->distinct()
			->orderBy('nama_resmi', 'ASC')
			->get()
			->getResultArray();

		return array_column($result, 'nama_resmi', 'kode');
	}

	public function getAngkatanByFilter()
	{
		$programStudi = $this->request->getGet('program_studi_kode');

		$builder = $this->db->table('mahasiswa')
			->select('tahun_angkatan')
			->where('status_mahasiswa', 'Aktif')
			->groupBy('tahun_angkatan')
			->orderBy('tahun_angkatan', 'DESC');

		if ($programStudi) {
			$builder->where('program_studi_kode', $programStudi);
		}

		$angkatan = $builder->get()->getResultArray();

		return $this->response->setJSON($angkatan);
	}

	private function getLampiranData($programStudi, $tahunAkademik, $cpmkCplMatrix)
	{
		$lampiranData = [
			'rekap_cpmk' => [],
			'rps_list' => []
		];

		foreach ($cpmkCplMatrix as $mk) {
			$mataKuliahId = $mk['mata_kuliah_id'] ?? 0;

			// Get jadwal for this mata kuliah
			$jadwalList = $this->db->table('jadwal')
				->select('id, kelas')
				->where('mata_kuliah_id', $mataKuliahId)
				->like('tahun_akademik', $tahunAkademik, 'both')
				->where('program_studi_kode', $programStudi)
				->get()
				->getResultArray();

			// Get RPS ID for this mata kuliah
			$rpsData = $this->db->table('rps')
				->select('id')
				->where('mata_kuliah_id', $mataKuliahId)
				->orderBy('created_at', 'DESC')
				->get()
				->getRowArray();

			$rpsId = $rpsData['id'] ?? null;

			// Add to rekap_cpmk list
			if (!empty($jadwalList)) {
				foreach ($jadwalList as $jadwal) {
					$lampiranData['rekap_cpmk'][] = [
						'nama_mk' => $mk['nama_mk'],
						'jadwal_id' => $jadwal['id'],
						'kelas' => $jadwal['kelas']
					];
				}
			} else {
				$lampiranData['rekap_cpmk'][] = [
					'nama_mk' => $mk['nama_mk'],
					'jadwal_id' => null,
					'kelas' => null
				];
			}

			// Add to RPS list
			$lampiranData['rps_list'][] = [
				'nama_mk' => $mk['nama_mk'],
				'rps_id' => $rpsId
			];
		}

		return $lampiranData;
	}

	public function exportZip()
	{
		try {
			// Check user role
			if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
				return redirect()->to('/')->with('error', 'Akses ditolak.');
			}

			$tahunAkademik = $this->request->getGet('tahun_akademik');
			$programStudi = $this->request->getGet('program_studi');
			$angkatan = $this->request->getGet('angkatan');
			$documents = $this->request->getGet('documents');

			if (!$tahunAkademik || !$programStudi || !$angkatan) {
				return redirect()->to('admin/laporan-cpl')->with('error', 'Parameter tidak lengkap.');
			}

			// Get CPL report data
			$reportData = $this->getReportData($tahunAkademik, $programStudi, $angkatan);

			if (!$reportData) {
				return redirect()->to('admin/laporan-cpl')->with('error', 'Data tidak ditemukan.');
			}

			// Parse selected documents
			$selectedDocuments = [];
			if (!empty($documents)) {
				$selectedDocuments = explode(',', $documents);
			}

			// Create ZIP file
			$zip = new \ZipArchive();
			$zipFilename = 'Laporan_CPL_' . str_replace(' ', '_', $programStudi) . '_' . $angkatan . '_' . time() . '.zip';
			$zipPath = WRITEPATH . 'uploads/' . $zipFilename;

			// Ensure uploads directory exists
			if (!is_dir(WRITEPATH . 'uploads')) {
				mkdir(WRITEPATH . 'uploads', 0755, true);
			}

			if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
				return redirect()->to('admin/laporan-cpl')->with('error', 'Gagal membuat file ZIP.');
			}

			$tempFiles = [];

			// 1. Generate and add Portfolio DOC
			$portfolioHtml = view('admin/laporan_cpl/portfolio_pdf', ['report' => $reportData]);

			// Wrap in Word-compatible HTML structure
			$wordHtml = '
			<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
			<head>
				<meta charset="UTF-8">
				<style>
					body { font-family: Arial, sans-serif; font-size: 11pt; padding: 30px; }
					h2 { font-size: 18pt; font-weight: bold; margin-bottom: 10px; text-align: center; }
					h5 { font-size: 14pt; font-weight: bold; margin-bottom: 10px; }
					p, li { font-size: 11pt; line-height: 1.4; }
					table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
					table th, table td { padding: 8px; border: 1px solid #000; word-wrap: break-word; vertical-align: top; }
					table thead th { background-color: #f8f9fa; font-weight: bold; }
					.section { margin-bottom: 25px; page-break-inside: avoid; }
					.text-center { text-align: center; }
					.fw-bold { font-weight: bold; }
					.text-success { color: #198754; }
					.text-danger { color: #dc3545; }
					.text-muted { color: #6c757d; }
					.bg-light { background-color: #f8f9fa; padding: 10px; }
					ul { margin: 0; padding-left: 20px; }
					.list-unstyled { list-style: none; padding-left: 0; }
					@page { margin: 2cm; }
				</style>
			</head>
			<body>' . $portfolioHtml . '</body></html>';

			// Save as .doc file
			$docFilename = 'Laporan_Pemenuhan_CPL.doc';
			$tempDocPath = WRITEPATH . 'uploads/temp_portfolio_cpl_' . time() . '.doc';
			file_put_contents($tempDocPath, $wordHtml);
			$zip->addFile($tempDocPath, $docFilename);
			$tempFiles[] = $tempDocPath;

			// 2. Add supporting documents based on selection
			foreach ($selectedDocuments as $docType) {
				$this->addDocumentToZip($zip, $docType, $reportData, $tempFiles);
			}

			$zip->close();

			// Download the ZIP file
			$data = file_get_contents($zipPath);

			// Clean up temporary files
			foreach ($tempFiles as $tempFile) {
				if (file_exists($tempFile)) {
					@unlink($tempFile);
				}
			}

			// Return the download response
			$response = $this->response->download($zipFilename, $data);

			// Clean up the ZIP file after sending
			@unlink($zipPath);

			return $response;
		} catch (\Exception $e) {
			log_message('error', 'Error in exportZip: ' . $e->getMessage());
			return redirect()->to('admin/laporan-cpl')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
		}
	}

	private function addDocumentToZip(&$zip, $docType, $reportData, &$tempFiles)
	{
		try {
			switch ($docType) {
				case 'rekap_cpmk':
					// Add all CPMK Excel files
					if (!empty($reportData['lampiran']['rekap_cpmk'])) {
						foreach ($reportData['lampiran']['rekap_cpmk'] as $item) {
							if ($item['jadwal_id']) {
								$this->generateAndAddCpmkExcel($zip, $item['jadwal_id'], $item['nama_mk'], $item['kelas'], $tempFiles);
							}
						}
					}
					return true;

				case 'matriks_cpl_cpmk':
					// Add matrix Excel
					return $this->generateAndAddMatrixExcel($zip, $tempFiles);

				case 'rps_mk_kontributor':
					// Add all RPS DOC files
					if (!empty($reportData['lampiran']['rps_list'])) {
						foreach ($reportData['lampiran']['rps_list'] as $item) {
							if ($item['rps_id']) {
								$this->generateAndAddRpsDoc($zip, $item['rps_id'], $item['nama_mk'], $tempFiles);
							}
						}
					}
					return true;

				case 'bukti_dokumentasi':
					// Add bukti dokumentasi asesmen file
					if (!empty($reportData['bukti_dokumentasi_file'])) {
						$buktiPath = FCPATH . 'uploads/bukti_dokumentasi/' . $reportData['bukti_dokumentasi_file'];
						if (file_exists($buktiPath)) {
							$extension = pathinfo($buktiPath, PATHINFO_EXTENSION);
							$zip->addFile($buktiPath, 'Bukti_Dokumentasi_Asesmen.' . $extension);
							return true;
						}
					}
					return false;

				case 'notulensi_rapat':
					// Add notulensi rapat evaluasi CPL file
					if (!empty($reportData['notulensi_rapat_file'])) {
						$notulensiPath = FCPATH . 'uploads/notulensi_cpl/' . $reportData['notulensi_rapat_file'];
						if (file_exists($notulensiPath)) {
							$extension = pathinfo($notulensiPath, PATHINFO_EXTENSION);
							$zip->addFile($notulensiPath, 'Notulensi_Rapat_Evaluasi_CPL.' . $extension);
							return true;
						}
					}
					return false;
			}
		} catch (\Exception $e) {
			log_message('error', 'Error adding document to ZIP: ' . $e->getMessage());
			return false;
		}

		return false;
	}

	private function generateAndAddCpmkExcel(&$zip, $jadwalId, $namaMk, $kelas, &$tempFiles)
	{
		try {
			// Import required models
			$jadwalModel = new \App\Models\MengajarModel();
			$mahasiswaModel = new \App\Models\MahasiswaModel();
			$cpmkModel = new \App\Models\CpmkModel();
			$nilaiCpmkModel = new \App\Models\NilaiCpmkMahasiswaModel();

			// Get jadwal details
			$jadwal = $jadwalModel->getJadwalWithDetails(['id' => $jadwalId], true);
			if (!$jadwal) {
				log_message('error', 'Jadwal not found for ID: ' . $jadwalId);
				return false;
			}

			// Get students for this class
			$students = $mahasiswaModel->getStudentsForScoring($jadwal['program_studi'], $jadwal['semester']);

			// Get CPMK list for this jadwal
			$cpmk_list = $cpmkModel->getCpmkByJadwal($jadwalId);

			// Get all CPMK scores for all students
			$existing_scores = $nilaiCpmkModel->getScoresByJadwalForInput($jadwalId);

			// Create Excel file using PhpSpreadsheet
			$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();

			// Set document properties
			$spreadsheet->getProperties()
				->setCreator('OBE System')
				->setTitle('Nilai CPMK - ' . $jadwal['nama_mk'])
				->setSubject('Nilai CPMK');

			// Basic table header
			$row = 1;
			$col = 1;

			// Course information
			$sheet->setCellValue('A' . $row, 'MATA KULIAH');
			$sheet->setCellValue('B' . $row, strtoupper($jadwal['nama_mk']));
			$row++;
			$sheet->setCellValue('A' . $row, 'KELAS/PROGRAM STUDI');
			$sheet->setCellValue('B' . $row, strtoupper($jadwal['kelas']) . " / " . strtoupper($jadwal['program_studi']));
			$row++;
			$sheet->setCellValue('A' . $row, 'DOSEN PENGAMPU');
			$sheet->setCellValue('B' . $row, strtoupper($jadwal['dosen_ketua']));
			$row += 2;

			// Table header
			$headerRow = $row;
			$col = 1;

			// Basic columns
			$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, 'No');
			$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, 'NIM');
			$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, 'Nama');

			// CPMK columns
			foreach ($cpmk_list as $cpmk) {
				$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, $cpmk['kode_cpmk']);
			}

			// Nilai Akhir column
			$lastCol = $col;
			$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row, 'Nilai Akhir MK');

			// Style header
			$lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastCol);
			$headerStyle = $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $headerRow);
			$headerStyle->getFont()->setBold(true);
			$headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
				->getStartColor()->setARGB('FF4472C4');
			$headerStyle->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
			$headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

			// Data rows
			$row++;
			$no = 1;
			foreach ($students as $student) {
				$col = 1;
				$mahasiswa_id = $student['id'];

				// Basic info
				$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, $no++);
				$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, $student['nim']);
				$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, $student['nama_lengkap']);

				// CPMK scores
				$student_scores = [];
				foreach ($cpmk_list as $cpmk) {
					$score = $existing_scores[$mahasiswa_id][$cpmk['id']] ?? null;
					if ($score !== null && $score !== '') {
						$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, $score);
						$student_scores[] = (float)$score;
					} else {
						$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, '-');
					}
				}

				// Nilai Akhir
				if (count($student_scores) > 0) {
					$total = array_sum($student_scores);
					$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row, number_format($total, 2));
				} else {
					$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row, '-');
				}

				$row++;
			}

			// Add borders
			$lastRow = $row - 1;
			$sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $lastRow)->applyFromArray([
				'borders' => [
					'allBorders' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						'color' => ['argb' => 'FF000000'],
					],
				],
			]);

			// Auto-size columns
			for ($i = 1; $i <= $lastCol; $i++) {
				$columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
				$sheet->getColumnDimension($columnLetter)->setAutoSize(true);
			}

			// Save to file
			$tempPath = WRITEPATH . 'uploads/temp_cpmk_' . $jadwalId . '_' . time() . '_' . mt_rand() . '.xlsx';
			$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
			$writer->save($tempPath);

			// Add to ZIP
			$filename = 'Rekap_CPMK_' . str_replace(' ', '_', $namaMk) . '_Kelas_' . $kelas . '.xlsx';
			$zip->addFile($tempPath, $filename);
			$tempFiles[] = $tempPath;

			log_message('info', 'Successfully added CPMK Excel to ZIP: ' . $filename);
			return true;
		} catch (\Exception $e) {
			log_message('error', 'Error generating CPMK Excel: ' . $e->getMessage());
			return false;
		}
	}

	private function generateAndAddRpsDoc(&$zip, $rpsId, $namaMk, &$tempFiles)
	{
		try {
			// Generate RPS DOC using preview data
			$rpsService = new \App\Services\RpsPreviewService();
			$rpsData = $rpsService->getData($rpsId);

			// Get the view content
			$fullHtml = view('rps/preview', $rpsData);

			// Extract only the content within #rps-content div
			libxml_use_internal_errors(true);
			$dom = new \DOMDocument();
			$dom->loadHTML('<?xml encoding="UTF-8">' . $fullHtml);
			libxml_clear_errors();

			$xpath = new \DOMXPath($dom);
			$contentNode = $xpath->query("//*[@id='rps-content']")->item(0);

			if ($contentNode) {
				$cleanHtml = '';
				foreach ($contentNode->childNodes as $child) {
					$cleanHtml .= $dom->saveHTML($child);
				}

				// Wrap in Word-compatible HTML structure
				$rpsHtml = '
				<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
				<head>
					<meta charset="UTF-8">
					<style>
						body { font-family: Arial, sans-serif; font-size: 10pt; padding: 20px; }
						table { border-collapse: collapse; width: 100%; margin-bottom: 15px; }
						table th, table td { padding: 6px; border: 1px solid #000; word-wrap: break-word; vertical-align: top; }
						.fw-bold { font-weight: bold; }
						.text-center { text-align: center; }
						@page { margin: 2cm; }
					</style>
				</head>
				<body>' . $cleanHtml . '</body></html>';

				// Save as .doc file
				$filename = 'RPS_' . str_replace(' ', '_', $namaMk) . '.doc';
				$tempRpsPath = WRITEPATH . 'uploads/temp_rps_' . $rpsId . '_' . time() . '.doc';
				file_put_contents($tempRpsPath, $rpsHtml);
				$zip->addFile($tempRpsPath, $filename);
				$tempFiles[] = $tempRpsPath;

				return true;
			}

			return false;
		} catch (\Exception $e) {
			log_message('error', 'Error generating RPS DOC: ' . $e->getMessage());
			return false;
		}
	}

	private function generateAndAddMatrixExcel(&$zip, &$tempFiles)
	{
		try {
			// Get matrix data
			$matrixData = $this->db->table('cpl_cpmk')
				->select(
					'cpl.id AS cpl_id, cpmk.id AS cpmk_id, cpl.kode_cpl, cpmk.kode_cpmk, GROUP_CONCAT(DISTINCT mk.nama_mk ORDER BY mk.nama_mk SEPARATOR ", ") AS mk_list'
				)
				->join('cpl', 'cpl.id = cpl_cpmk.cpl_id')
				->join('cpmk', 'cpmk.id = cpl_cpmk.cpmk_id')
				->join('cpmk_mk', 'cpmk_mk.cpmk_id = cpmk.id', 'left')
				->join('mata_kuliah mk', 'mk.id = cpmk_mk.mata_kuliah_id', 'left')
				->groupBy(['cpl.id', 'cpmk.id'])
				->orderBy('cpl.kode_cpl', 'asc')
				->orderBy('cpmk.kode_cpmk', 'asc')
				->get()->getResultArray();

			// Create Excel file using PhpSpreadsheet
			$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();

			// Set document properties
			$spreadsheet->getProperties()
				->setCreator('OBE System')
				->setTitle('Pemetaan CPL - CPMK - MK')
				->setSubject('Pemetaan CPL - CPMK - MK');

			// Set headers
			$sheet->setCellValue('A1', 'Kode CPL');
			$sheet->setCellValue('B1', 'Kode CPMK');
			$sheet->setCellValue('C1', 'Mata Kuliah');

			// Style header
			$headerStyle = $sheet->getStyle('A1:C1');
			$headerStyle->getFont()->setBold(true);
			$headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
				->getStartColor()->setARGB('FF4472C4');
			$headerStyle->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
			$headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

			// Fill data
			$rowNumber = 2;
			foreach ($matrixData as $row) {
				$sheet->setCellValue('A' . $rowNumber, $row['kode_cpl']);
				$sheet->setCellValue('B' . $rowNumber, $row['kode_cpmk']);
				$sheet->setCellValue('C' . $rowNumber, $row['mk_list']);
				$rowNumber++;
			}

			// Add borders
			$sheet->getStyle('A1:C' . ($rowNumber - 1))->applyFromArray([
				'borders' => [
					'allBorders' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						'color' => ['argb' => 'FF000000'],
					],
				],
			]);

			// Auto-size columns
			foreach (range('A', 'C') as $col) {
				$sheet->getColumnDimension($col)->setAutoSize(true);
			}

			// Save to file
			$tempPath = WRITEPATH . 'uploads/temp_matrix_' . time() . '_' . mt_rand() . '.xlsx';
			$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
			$writer->save($tempPath);

			// Add to ZIP
			$filename = 'Pemetaan_CPL_CPMK_MK.xlsx';
			$zip->addFile($tempPath, $filename);
			$tempFiles[] = $tempPath;

			log_message('info', 'Successfully added Matrix Excel to ZIP: ' . $filename);
			return true;
		} catch (\Exception $e) {
			log_message('error', 'Error generating Matrix Excel: ' . $e->getMessage());
			return false;
		}
	}

	private function getCqiData($programStudi, $tahunAkademik, $angkatan)
	{
		$cqiList = $this->cqiModel->getCqiCplList($programStudi, $tahunAkademik, $angkatan);

		// Convert to associative array with kode_cpl as key
		$cqiByKodeCpl = [];
		foreach ($cqiList as $cqi) {
			$cqiByKodeCpl[$cqi['kode_cpl']] = $cqi;
		}

		return $cqiByKodeCpl;
	}

	private function initializeDefaultTemplates()
	{
		// Check if templates already exist
		$existingTemplates = $this->analysisTemplateCplModel->findAll();
		if (!empty($existingTemplates)) {
			return; // Templates already exist
		}

		// Create default templates
		$defaultTemplates = [
			[
				'option_key' => 'default',
				'option_label' => 'Template 1',
				'template_tercapai' => 'Semua CPL tercapai dengan baik. Dari total {total_cpl} CPL yang diukur, seluruhnya ({jumlah_tercapai} CPL) telah mencapai standar minimal {standar_minimal}%. Mahasiswa menunjukkan kompetensi yang memadai sesuai dengan profil lulusan yang diharapkan.',
				'template_tidak_tercapai' => 'Dari {total_cpl} CPL yang diukur, terdapat {jumlah_tidak_tercapai} CPL yang belum mencapai standar minimal {standar_minimal}%, yaitu: {cpl_tidak_tercapai_list}. Sementara itu, {jumlah_tercapai} CPL lainnya ({cpl_tercapai_list}) telah tercapai dengan baik. Diperlukan evaluasi lebih lanjut terhadap mata kuliah kontributor dan metode pembelajaran untuk meningkatkan capaian CPL yang belum tercapai.',
				'is_active' => 1
			],
			[
				'option_key' => 'formal',
				'option_label' => 'Template 2',
				'template_tercapai' => 'Berdasarkan hasil evaluasi capaian pembelajaran lulusan untuk periode ini, dapat dilaporkan bahwa seluruh CPL (total {total_cpl} CPL) telah mencapai standar minimal yang ditetapkan yaitu {standar_minimal}%. Pencapaian ini menunjukkan bahwa proses pembelajaran telah berjalan efektif dan mahasiswa telah menguasai kompetensi yang diharapkan sesuai dengan standar KKNI.',
				'template_tidak_tercapai' => 'Berdasarkan hasil evaluasi capaian pembelajaran lulusan, dari {total_cpl} CPL yang diukur, sebanyak {jumlah_tercapai} CPL ({persentase_tercapai}%) telah mencapai standar minimal {standar_minimal}%, yaitu: {cpl_tercapai_list}. Namun demikian, masih terdapat {jumlah_tidak_tercapai} CPL yang belum mencapai standar, yaitu: {cpl_tidak_tercapai_list}. Untuk CPL yang belum tercapai, diperlukan tindakan perbaikan berkelanjutan (Continuous Quality Improvement) yang mencakup evaluasi mata kuliah kontributor, perbaikan metode pembelajaran, serta penyesuaian strategi asesmen.',
				'is_active' => 1
			],
			[
				'option_key' => 'singkat',
				'option_label' => 'Template 3',
				'template_tercapai' => 'Seluruh CPL tercapai (100%).',
				'template_tidak_tercapai' => '{jumlah_tercapai} dari {total_cpl} CPL tercapai ({persentase_tercapai}%). CPL yang belum tercapai: {cpl_tidak_tercapai_list}.',
				'is_active' => 1
			],
			[
				'option_key' => 'template_4',
				'option_label' => 'Template 4',
				'template_tercapai' => 'Analisis capaian pembelajaran menunjukkan bahwa semua CPL telah tercapai dengan persentase keberhasilan 100%. Hasil ini mencerminkan efektivitas strategi pembelajaran yang diterapkan.',
				'template_tidak_tercapai' => 'Hasil analisis menunjukkan bahwa dari {total_cpl} CPL, sebanyak {jumlah_tercapai} CPL telah tercapai ({persentase_tercapai}%), namun {jumlah_tidak_tercapai} CPL masih perlu perbaikan: {cpl_tidak_tercapai_list}. Rekomendasi tindak lanjut perlu segera dilakukan.',
				'is_active' => 1
			],
			[
				'option_key' => 'template_5',
				'option_label' => 'Template 5',
				'template_tercapai' => 'Evaluasi terhadap {total_cpl} CPL menunjukkan pencapaian yang sangat baik dengan seluruh indikator terpenuhi. Mahasiswa telah mendemonstrasikan kompetensi sesuai dengan standar yang ditetapkan sebesar {standar_minimal}%.',
				'template_tidak_tercapai' => 'Pencapaian CPL periode ini menunjukkan {persentase_tercapai}% CPL telah memenuhi standar ({cpl_tercapai_list}). Adapun CPL yang masih di bawah standar minimal {standar_minimal}% adalah: {cpl_tidak_tercapai_list}. Diperlukan intervensi pada mata kuliah pendukung CPL tersebut.',
				'is_active' => 1
			],
			[
				'option_key' => 'template_6',
				'option_label' => 'Template 6',
				'template_tercapai' => 'Capaian pembelajaran lulusan untuk periode ini sangat memuaskan dengan tingkat pencapaian 100% untuk semua CPL.',
				'template_tidak_tercapai' => 'CPL tercapai: {cpl_tercapai_list} ({persentase_tercapai}%). CPL perlu perbaikan: {cpl_tidak_tercapai_list}. Evaluasi dan perbaikan pembelajaran diperlukan untuk CPL yang belum tercapai.',
				'is_active' => 1
			]
		];

		foreach ($defaultTemplates as $template) {
			$this->analysisTemplateCplModel->insert($template);
		}
	}

	public function saveAnalysis()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Akses ditolak.'
			])->setStatusCode(403);
		}

		$programStudiKode = $this->request->getPost('program_studi_kode');
		$tahunAkademik = $this->request->getPost('tahun_akademik');
		$angkatan = $this->request->getPost('angkatan');
		$mode = $this->request->getPost('mode');
		$analysisSummary = $this->request->getPost('analisis_summary');
		$autoOptionsJson = $this->request->getPost('auto_options');
		$templatesJson = $this->request->getPost('templates');

		if (!$programStudiKode || !$tahunAkademik || !$angkatan || !$mode) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Data tidak lengkap.'
			])->setStatusCode(400);
		}

		// Decode auto_options if it's in JSON format
		$autoOptions = null;
		if ($mode === 'auto' && $autoOptionsJson) {
			$autoOptions = $autoOptionsJson; // Already JSON string from frontend
		}

		$data = [
			'program_studi' => $programStudiKode, // Kode program studi
			'tahun_akademik' => $tahunAkademik,
			'angkatan' => $angkatan,
			'mode' => $mode,
			'analisis_summary' => $mode === 'manual' ? $analysisSummary : null,
			'auto_options' => $autoOptions
		];

		try {
			// Save analysis settings
			$this->analysisCplModel->saveAnalysis($data);

			// Save templates if provided
			if ($templatesJson) {
				$templates = json_decode($templatesJson, true);
				if (is_array($templates)) {
					foreach ($templates as $optionKey => $templateData) {
						$this->analysisTemplateCplModel->updateByKey($optionKey, [
							'template_tercapai' => $templateData['template_tercapai'] ?? '',
							'template_tidak_tercapai' => $templateData['template_tidak_tercapai'] ?? ''
						]);
					}
				}
			}

			return $this->response->setJSON([
				'success' => true,
				'message' => 'Analisis dan template berhasil disimpan.'
			]);
		} catch (\Exception $e) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Gagal menyimpan analisis: ' . $e->getMessage()
			])->setStatusCode(500);
		}
	}

	public function saveCqi()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Akses ditolak.'
			])->setStatusCode(403);
		}

		$programStudi = $this->request->getPost('program_studi');
		$tahunAkademik = $this->request->getPost('tahun_akademik');
		$angkatan = $this->request->getPost('angkatan');
		$cqiDataJson = $this->request->getPost('cqi_data');

		if (!$programStudi || !$tahunAkademik || !$angkatan || !$cqiDataJson) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Data tidak lengkap.'
			])->setStatusCode(400);
		}

		try {
			// Decode JSON string to array
			$cqiData = json_decode($cqiDataJson, true);

			if (!is_array($cqiData)) {
				throw new \Exception('Format data CQI tidak valid.');
			}

			// Save each CQI record
			foreach ($cqiData as $cqi) {
				if (!empty($cqi['kode_cpl'])) {
					$data = [
						'program_studi' => $programStudi,
						'tahun_akademik' => $tahunAkademik,
						'angkatan' => $angkatan,
						'kode_cpl' => $cqi['kode_cpl'],
						'masalah' => $cqi['masalah'] ?? null,
						'rencana_perbaikan' => $cqi['rencana_perbaikan'] ?? null,
						'penanggung_jawab' => $cqi['penanggung_jawab'] ?? null,
						'jadwal_pelaksanaan' => $cqi['jadwal_pelaksanaan'] ?? null
					];

					$this->cqiModel->saveCqiCpl($data);
				}
			}

			return $this->response->setJSON([
				'success' => true,
				'message' => 'Data CQI berhasil disimpan.'
			]);
		} catch (\Exception $e) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Gagal menyimpan data CQI: ' . $e->getMessage()
			])->setStatusCode(500);
		}
	}

	/**
	 * Upload Bukti Dokumentasi Asesmen
	 */
	public function uploadBuktiDokumentasi()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Akses ditolak.'
			])->setStatusCode(403);
		}

		$programStudi = $this->request->getPost('program_studi');
		$tahunAkademik = $this->request->getPost('tahun_akademik');
		$angkatan = $this->request->getPost('angkatan');

		if (!$programStudi || !$tahunAkademik || !$angkatan) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Data tidak lengkap.'
			])->setStatusCode(400);
		}

		$file = $this->request->getFile('bukti_dokumentasi_file');

		if (!$file || !$file->isValid()) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'File tidak valid.'
			])->setStatusCode(400);
		}

		// Validate file type
		$allowedMimes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
		if (!in_array($file->getMimeType(), $allowedMimes)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Tipe file tidak didukung. Hanya PDF, DOC, dan DOCX yang diizinkan.'
			])->setStatusCode(400);
		}

		// Validate file size (max 5MB)
		if ($file->getSize() > 5 * 1024 * 1024) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Ukuran file maksimal 5MB.'
			])->setStatusCode(400);
		}

		try {
			// Create upload directory if not exists
			$uploadPath = FCPATH . 'uploads/bukti_dokumentasi/';
			if (!is_dir($uploadPath)) {
				mkdir($uploadPath, 0755, true);
			}

			// Get or create analysis record
			$existingAnalysis = $this->analysisCplModel->getAnalysis($programStudi, $tahunAkademik, $angkatan);

			// Delete old file if exists
			if ($existingAnalysis && !empty($existingAnalysis['bukti_dokumentasi_file'])) {
				$oldFile = $uploadPath . $existingAnalysis['bukti_dokumentasi_file'];
				if (file_exists($oldFile)) {
					@unlink($oldFile);
				}
			}

			// Generate new filename
			$extension = $file->getExtension();
			$filename = 'bukti_dok_' . str_replace(' ', '_', $programStudi) . '_' . $angkatan . '_' . time() . '.' . $extension;

			// Move file
			$file->move($uploadPath, $filename);

			// Update or create analysis record
			$data = [
				'program_studi' => $programStudi,
				'tahun_akademik' => $tahunAkademik,
				'angkatan' => $angkatan,
				'bukti_dokumentasi_file' => $filename
			];

			if ($existingAnalysis) {
				$this->analysisCplModel->update($existingAnalysis['id'], ['bukti_dokumentasi_file' => $filename]);
			} else {
				$data['mode'] = 'auto';
				$this->analysisCplModel->insert($data);
			}

			return $this->response->setJSON([
				'success' => true,
				'message' => 'File berhasil diunggah.',
				'filename' => $filename
			]);
		} catch (\Exception $e) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Gagal mengunggah file: ' . $e->getMessage()
			])->setStatusCode(500);
		}
	}

	/**
	 * Delete Bukti Dokumentasi Asesmen
	 */
	public function deleteBuktiDokumentasi()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Akses ditolak.'
			])->setStatusCode(403);
		}

		$programStudi = $this->request->getPost('program_studi');
		$tahunAkademik = $this->request->getPost('tahun_akademik');
		$angkatan = $this->request->getPost('angkatan');

		if (!$programStudi || !$tahunAkademik || !$angkatan) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Data tidak lengkap.'
			])->setStatusCode(400);
		}

		try {
			$existingAnalysis = $this->analysisCplModel->getAnalysis($programStudi, $tahunAkademik, $angkatan);

			if (!$existingAnalysis || empty($existingAnalysis['bukti_dokumentasi_file'])) {
				return $this->response->setJSON([
					'success' => false,
					'message' => 'File tidak ditemukan.'
				])->setStatusCode(404);
			}

			// Delete file from storage
			$uploadPath = FCPATH . 'uploads/bukti_dokumentasi/';
			$filePath = $uploadPath . $existingAnalysis['bukti_dokumentasi_file'];
			if (file_exists($filePath)) {
				@unlink($filePath);
			}

			// Update database
			$this->analysisCplModel->update($existingAnalysis['id'], ['bukti_dokumentasi_file' => null]);

			return $this->response->setJSON([
				'success' => true,
				'message' => 'File berhasil dihapus.'
			]);
		} catch (\Exception $e) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Gagal menghapus file: ' . $e->getMessage()
			])->setStatusCode(500);
		}
	}

	/**
	 * Upload Notulensi Rapat Evaluasi CPL
	 */
	public function uploadNotulensiRapat()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Akses ditolak.'
			])->setStatusCode(403);
		}

		$programStudi = $this->request->getPost('program_studi');
		$tahunAkademik = $this->request->getPost('tahun_akademik');
		$angkatan = $this->request->getPost('angkatan');

		if (!$programStudi || !$tahunAkademik || !$angkatan) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Data tidak lengkap.'
			])->setStatusCode(400);
		}

		$file = $this->request->getFile('notulensi_rapat_file');

		if (!$file || !$file->isValid()) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'File tidak valid.'
			])->setStatusCode(400);
		}

		// Validate file type
		$allowedMimes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
		if (!in_array($file->getMimeType(), $allowedMimes)) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Tipe file tidak didukung. Hanya PDF, DOC, dan DOCX yang diizinkan.'
			])->setStatusCode(400);
		}

		// Validate file size (max 5MB)
		if ($file->getSize() > 5 * 1024 * 1024) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Ukuran file maksimal 5MB.'
			])->setStatusCode(400);
		}

		try {
			// Create upload directory if not exists
			$uploadPath = FCPATH . 'uploads/notulensi_cpl/';
			if (!is_dir($uploadPath)) {
				mkdir($uploadPath, 0755, true);
			}

			// Get or create analysis record
			$existingAnalysis = $this->analysisCplModel->getAnalysis($programStudi, $tahunAkademik, $angkatan);

			// Delete old file if exists
			if ($existingAnalysis && !empty($existingAnalysis['notulensi_rapat_file'])) {
				$oldFile = $uploadPath . $existingAnalysis['notulensi_rapat_file'];
				if (file_exists($oldFile)) {
					@unlink($oldFile);
				}
			}

			// Generate new filename
			$extension = $file->getExtension();
			$filename = 'notulensi_cpl_' . str_replace(' ', '_', $programStudi) . '_' . $angkatan . '_' . time() . '.' . $extension;

			// Move file
			$file->move($uploadPath, $filename);

			// Update or create analysis record
			$data = [
				'program_studi' => $programStudi,
				'tahun_akademik' => $tahunAkademik,
				'angkatan' => $angkatan,
				'notulensi_rapat_file' => $filename
			];

			if ($existingAnalysis) {
				$this->analysisCplModel->update($existingAnalysis['id'], ['notulensi_rapat_file' => $filename]);
			} else {
				$data['mode'] = 'auto';
				$this->analysisCplModel->insert($data);
			}

			return $this->response->setJSON([
				'success' => true,
				'message' => 'File berhasil diunggah.',
				'filename' => $filename
			]);
		} catch (\Exception $e) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Gagal mengunggah file: ' . $e->getMessage()
			])->setStatusCode(500);
		}
	}

	/**
	 * Delete Notulensi Rapat Evaluasi CPL
	 */
	public function deleteNotulensiRapat()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Akses ditolak.'
			])->setStatusCode(403);
		}

		$programStudi = $this->request->getPost('program_studi');
		$tahunAkademik = $this->request->getPost('tahun_akademik');
		$angkatan = $this->request->getPost('angkatan');

		if (!$programStudi || !$tahunAkademik || !$angkatan) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Data tidak lengkap.'
			])->setStatusCode(400);
		}

		try {
			$existingAnalysis = $this->analysisCplModel->getAnalysis($programStudi, $tahunAkademik, $angkatan);

			if (!$existingAnalysis || empty($existingAnalysis['notulensi_rapat_file'])) {
				return $this->response->setJSON([
					'success' => false,
					'message' => 'File tidak ditemukan.'
				])->setStatusCode(404);
			}

			// Delete file from storage
			$uploadPath = FCPATH . 'uploads/notulensi_cpl/';
			$filePath = $uploadPath . $existingAnalysis['notulensi_rapat_file'];
			if (file_exists($filePath)) {
				@unlink($filePath);
			}

			// Update database
			$this->analysisCplModel->update($existingAnalysis['id'], ['notulensi_rapat_file' => null]);

			return $this->response->setJSON([
				'success' => true,
				'message' => 'File berhasil dihapus.'
			]);
		} catch (\Exception $e) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Gagal menghapus file: ' . $e->getMessage()
			])->setStatusCode(500);
		}
	}
}

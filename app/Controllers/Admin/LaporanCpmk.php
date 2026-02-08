<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class LaporanCpmk extends BaseController
{
	protected $db;
	protected $mataKuliahModel;
	protected $cpmkModel;
	protected $jadwalMengajarModel;
	protected $nilaiCpmkMahasiswaModel;
	protected $standarMinimalCapaianModel;
	protected $analysisCpmkModel;
	protected $cqiModel;
	protected $analysisTemplateModel;

	public function __construct()
	{
		$this->db = \Config\Database::connect();
		$this->mataKuliahModel = new \App\Models\MataKuliahModel();
		$this->cpmkModel = new \App\Models\CpmkModel();
		$this->jadwalMengajarModel = new \App\Models\MengajarModel();
		$this->nilaiCpmkMahasiswaModel = new \App\Models\NilaiCpmkMahasiswaModel();
		$this->standarMinimalCapaianModel = new \App\Models\StandarMinimalCpmkModel();
		$this->analysisCpmkModel = new \App\Models\AnalysisCpmkModel();
		$this->cqiModel = new \App\Models\CqiModel();
		$this->analysisTemplateModel = new \App\Models\AnalysisTemplateModel();
	}

	public function index()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return redirect()->to('/')->with('error', 'Akses ditolak.');
		}

		$data = [
			'title' => 'Laporan CPMK - Portofolio Mata Kuliah',
			'mataKuliah' => $this->mataKuliahModel->findAll(),
			'tahunAkademik' => $this->getTahunAkademik(),
			'programStudi' => $this->getProgramStudi()
		];

		return view('admin/laporan_cpmk/index', $data);
	}

	public function generate()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return redirect()->to('/')->with('error', 'Akses ditolak.');
		}

		$mataKuliahId = $this->request->getGet('mata_kuliah_id');
		$tahunAkademik = $this->request->getGet('tahun_akademik');
		$programStudi = $this->request->getGet('program_studi');

		if (!$mataKuliahId || !$tahunAkademik) {
			return redirect()->to('admin/laporan-cpmk')->with('error', 'Pilih mata kuliah dan tahun akademik terlebih dahulu.');
		}

		// Get course portfolio data
		$portfolioData = $this->getPortfolioData($mataKuliahId, $tahunAkademik, $programStudi);

		if (!$portfolioData) {
			return redirect()->to('admin/laporan-cpmk')->with('error', 'Data tidak ditemukan untuk mata kuliah dan tahun akademik yang dipilih.');
		}

		$data = [
			'title' => 'Portofolio Mata Kuliah',
			'portfolio' => $portfolioData
		];

		return view('admin/laporan_cpmk/portfolio', $data);
	}

	public function generatePdf()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return redirect()->to('/')->with('error', 'Akses ditolak.');
		}

		$mataKuliahId = $this->request->getGet('mata_kuliah_id');
		$tahunAkademik = $this->request->getGet('tahun_akademik');
		$programStudi = $this->request->getGet('program_studi_kode');
		$documents = $this->request->getGet('documents');

		if (!$mataKuliahId || !$tahunAkademik) {
			return redirect()->to('admin/laporan-cpmk')->with('error', 'Pilih mata kuliah dan tahun akademik terlebih dahulu.');
		}

		// Get course portfolio data
		$portfolioData = $this->getPortfolioData($mataKuliahId, $tahunAkademik, $programStudi);

		if (!$portfolioData) {
			return redirect()->to('admin/laporan-cpmk')->with('error', 'Data tidak ditemukan untuk mata kuliah dan tahun akademik yang dipilih.');
		}

		// Parse selected documents
		$selectedDocuments = [];
		if (!empty($documents)) {
			$selectedDocuments = explode(',', $documents);
		}

		$data = [
			'portfolio' => $portfolioData,
			'selectedDocuments' => $selectedDocuments
		];

		// Return PDF-optimized view
		return view('admin/laporan_cpmk/portfolio_pdf', $data);
	}

	private function getPortfolioData($mataKuliahId, $tahunAkademik, $programStudi = null)
	{
		// 1. Get Course Identity (Identitas Mata Kuliah)
		$mataKuliah = $this->mataKuliahModel->find($mataKuliahId);
		if (!$mataKuliah) {
			return null;
		}

		// Get teaching schedule
		$builder = $this->db->table('jadwal');
		$builder->where('mata_kuliah_id', $mataKuliahId);
		$builder->like('tahun_akademik', $tahunAkademik, 'both');
		if ($programStudi) {
			$builder->where('program_studi_kode', $programStudi);
		}
		$jadwalMengajar = $builder->get()->getResultArray();

		if (empty($jadwalMengajar)) {
			return null;
		}

		// Get lecturers (dosen pengampu)
		$jadwalIds = array_column($jadwalMengajar, 'id');
		$dosenPengampu = $this->db->table('jadwal_dosen jd')
			->select('d.nama_lengkap, d.nip, MIN(jd.role) as role')
			->join('dosen d', 'd.id = jd.dosen_id')
			->whereIn('jd.jadwal_id', $jadwalIds)
			->groupBy('d.nip')
			->get()
			->getResultArray();

		// 2. Get CPMK data with CPL relations
		$cpmkData = $this->getCpmkData($mataKuliahId);

		// 3. Get Assessment Data (Rencana dan Realisasi Penilaian)
		$assessmentData = $this->getAssessmentData($mataKuliahId, $jadwalIds);

		// 4. Get Analysis Data (Analisis Pencapaian)
		$passingThreshold = $this->standarMinimalCapaianModel->getPersentase();
		$analysis = $this->getAnalysisData($assessmentData, $passingThreshold, $mataKuliahId, $tahunAkademik, $programStudi);

		// 5. Get CQI data
		$jadwalMengajarId = $jadwalMengajar[0]['id'] ?? null;
		$cqiData = $jadwalMengajarId ? $this->getCqiData($jadwalMengajarId) : [];

		// 6. Get RPS ID for document download
		$rpsId = $this->db->table('rps')
			->where('mata_kuliah_id', $mataKuliahId)
			->orderBy('id', 'DESC')
			->limit(1)
			->get()
			->getRow('id');

		// Get templates for inline editing
		$templates = $this->analysisTemplateModel->getTemplatesAsArray();

		// Get document files
		$rubrikFile = $jadwalMengajar[0]['rubrik_penilaian_file'] ?? null;
		$contohSoalFile = $jadwalMengajar[0]['contoh_soal_file'] ?? null;
		$notulenFile = $jadwalMengajar[0]['notulen_rapat_file'] ?? null;

		$programStudiNamaResmi = $this->db->table('program_studi')
			->select('nama_resmi')
			->where('kode', $jadwalMengajar[0]['program_studi_kode'] ?? '')
			->get()
			->getRow('nama_resmi');

		return [
			'identitas' => [
				'nama_mata_kuliah' => $mataKuliah['nama_mk'],
				'kode_mata_kuliah' => $mataKuliah['kode_mk'],
				'program_studi_nama_resmi' => $programStudiNamaResmi ?? '-',
				'program_studi_kode' => $jadwalMengajar[0]['program_studi_kode'] ?? '-',
				'semester' => $mataKuliah['semester'],
				'jumlah_sks' => $mataKuliah['sks'],
				'tahun_akademik' => $tahunAkademik,
				'dosen_pengampu' => $dosenPengampu
			],
			'cpmk' => $cpmkData,
			'assessment' => $assessmentData,
			'analysis' => $analysis,
			'passing_threshold' => $passingThreshold,
			'mata_kuliah_id' => $mataKuliahId,
			'jadwal_id' => $jadwalMengajarId,
			'rps_id' => $rpsId,
			'cqi_data' => $cqiData,
			'templates' => $templates,
			'rubrik_penilaian_file' => $rubrikFile,
			'contoh_soal_file' => $contohSoalFile,
			'notulen_rapat_file' => $notulenFile
		];
	}

	private function getCpmkData($mataKuliahId)
	{
		$query = "
            SELECT
                c.id,
                c.kode_cpmk,
                c.deskripsi,
                GROUP_CONCAT(DISTINCT cpl.kode_cpl ORDER BY cpl.kode_cpl SEPARATOR ', ') as keterkaitan_cpl
            FROM cpmk c
            INNER JOIN cpmk_mk cm ON c.id = cm.cpmk_id
            LEFT JOIN cpl_cpmk cc ON c.id = cc.cpmk_id
            LEFT JOIN cpl ON cc.cpl_id = cpl.id
            WHERE cm.mata_kuliah_id = ?
            GROUP BY c.id, c.kode_cpmk, c.deskripsi
            ORDER BY c.kode_cpmk
        ";

		$result = $this->db->query($query, [$mataKuliahId])->getResultArray();

		// Get teaching and assessment methods from RPS
		foreach ($result as &$cpmk) {
			$rpsData = $this->getRpsMethodsByCpmk($mataKuliahId, $cpmk['id']);
			$cpmk['metode_pembelajaran'] = $rpsData['metode_pembelajaran'] ?? 'Ceramah, Diskusi';
			$cpmk['metode_asesmen'] = $rpsData['metode_asesmen'] ?? 'Tugas, Ujian';
		}

		return $result;
	}

	private function getRpsMethodsByCpmk($mataKuliahId, $cpmkId)
	{
		// Get teaching and assessment methods from rps_mingguan
		$query = "
            SELECT
                rm.metode,
                rm.teknik_penilaian
            FROM rps_mingguan rm
            INNER JOIN rps r ON rm.rps_id = r.id
            WHERE r.mata_kuliah_id = ?
            AND rm.cpmk_id = ?
            LIMIT 1
        ";

		$result = $this->db->query($query, [$mataKuliahId, $cpmkId])->getRowArray();

		if ($result) {
			// Parse metode if it's JSON, otherwise use as is
			$metodePembelajaran = 'Ceramah, Diskusi';
			if (!empty($result['metode'])) {
				$metode = $result['metode'];
				// Check if it's JSON
				$metodeDecoded = json_decode($metode, true);
				if (json_last_error() === JSON_ERROR_NONE && is_array($metodeDecoded)) {
					$metodePembelajaran = implode(', ', $metodeDecoded);
				} else {
					$metodePembelajaran = $metode;
				}
			}

			if ($result && !empty($result['teknik_penilaian'])) {
				$teknik = json_decode($result['teknik_penilaian'], true);

				$metodeAssesment = [];
				if (json_last_error() === JSON_ERROR_NONE && is_array($teknik) && !empty($teknik)) {

					if (array_keys($teknik) !== range(0, count($teknik) - 1)) {
						foreach ($teknik as $teknikKey => $bobot) {
							$metodeAssesment[] = $this->MetodeAssesment($teknikKey);
						}
					} else {
						foreach ($teknik as $t) {
							if (is_array($t) && isset($t['teknik'])) {
								$metodeAssesment[] = $this->MetodeAssesment($t['teknik']);
							} elseif (is_string($t)) {
								$metodeAssesment[] = $this->MetodeAssesment($t);
							}
						}
					}
				}
			}

			return [
				'metode_pembelajaran' => $metodePembelajaran,
				'metode_asesmen' => implode(', ', $metodeAssesment)
			];
		}
	}

	private function formatTeknikPenilaian($teknik)
	{
		$mapping = [
			'partisipasi' => 'Partisipasi',
			'observasi' => 'Observasi',
			'unjuk_kerja' => 'Unjuk Kerja',
			'proyek' => 'Proyek',
			'tes_tulis_uts' => 'Ujian Tengah Semester',
			'tes_tulis_uas' => 'Ujian Akhir Semester',
			'tes_lisan' => 'Tes Lisan'
		];

		return $mapping[$teknik] ?? ucfirst(str_replace('_', ' ', $teknik));
	}

	private function MetodeAssesment($teknik)
	{
		$mapping = [
			'partisipasi' => 'Kehadiran/Quiz',
			'observasi' => 'Praktek/Tugas',
			'unjuk_kerja' => 'Presentasi',
			'proyek' => 'Case Method/Project Based',
			'tes_tulis_uts' => 'UTS',
			'tes_tulis_uas' => 'UAS',
			'tes_lisan' => 'Tugas Kelompok'
		];

		return $mapping[$teknik] ?? ucfirst(str_replace('_', ' ', $teknik));
	}

	private function getAssessmentData($mataKuliahId, $jadwalIds)
	{
		$assessmentData = [];

		// Get all CPMK for this course
		$cpmkList = $this->db->table('cpmk_mk cm')
			->select('cm.cpmk_id, c.kode_cpmk')
			->join('cpmk c', 'c.id = cm.cpmk_id')
			->where('cm.mata_kuliah_id', $mataKuliahId)
			->orderBy('c.kode_cpmk')
			->get()
			->getResultArray();

		// Get bobot from rps_mingguan (shared across all jadwals for same course)
		$rps = $this->db->table('rps')
			->select('id')
			->where('mata_kuliah_id', $mataKuliahId)
			->orderBy('created_at', 'DESC')
			->get()
			->getRowArray();

		foreach ($cpmkList as $cpmk) {
			// Get bobot for this CPMK from rps_mingguan
			$bobot = 0;
			if ($rps) {
				$bobotResult = $this->db->table('rps_mingguan')
					->selectSum('bobot')
					->where('rps_id', $rps['id'])
					->where('cpmk_id', $cpmk['cpmk_id'])
					->get()
					->getRowArray();
				$bobot = floatval($bobotResult['bobot'] ?? 0);
			}

			// Get all nilai_cpmk for students in these jadwals for this CPMK
			$nilaiList = $this->db->table('nilai_cpmk_mahasiswa ncm')
				->select('ncm.nilai_cpmk, ncm.mahasiswa_id')
				->whereIn('ncm.jadwal_id', $jadwalIds)
				->where('ncm.cpmk_id', $cpmk['cpmk_id'])
				->get()
				->getResultArray();

			// Aggregate per student (in case of multiple jadwals)
			$studentScores = [];
			foreach ($nilaiList as $nilai) {
				$mhsId = $nilai['mahasiswa_id'];
				if (!isset($studentScores[$mhsId])) {
					$studentScores[$mhsId] = 0;
				}
				$studentScores[$mhsId] += floatval($nilai['nilai_cpmk']);
			}

			$jumlahMahasiswa = count($studentScores);
			$totalScore = array_sum($studentScores);

			// nilai_rata_rata = average CPMK score across students
			$nilaiRataRata = $jumlahMahasiswa > 0 ? round($totalScore / $jumlahMahasiswa, 2) : 0;

			// persentase_capaian = (avg score / bobot) × 100
			$persentaseCapaian = ($bobot > 0 && $jumlahMahasiswa > 0)
				? round(($nilaiRataRata / $bobot) * 100, 2)
				: 0;

			// Get teknik penilaian
			$teknikPenilaian = $this->getTeknikPenilaianByCpmk($mataKuliahId, $cpmk['cpmk_id']);

			$assessmentData[] = [
				'kode_cpmk' => $cpmk['kode_cpmk'],
				'cpmk_id' => $cpmk['cpmk_id'],
				'bobot' => $bobot,
				'teknik_penilaian' => $teknikPenilaian,
				'indikator_penilaian' => 'Kesesuaian hasil kerja dengan rubrik',
				'nilai_rata_rata' => $nilaiRataRata,
				'jumlah_mahasiswa' => $jumlahMahasiswa,
				'persentase_capaian' => $persentaseCapaian
			];
		}

		return $assessmentData;
	}

	private function getTeknikPenilaianByCpmk($mataKuliahId, $cpmkId)
	{
		$query = "
            SELECT rm.teknik_penilaian
            FROM rps_mingguan rm
            INNER JOIN rps r ON rm.rps_id = r.id
            WHERE r.mata_kuliah_id = ?
            AND rm.cpmk_id = ?
            LIMIT 1
        ";

		$result = $this->db->query($query, [$mataKuliahId, $cpmkId])->getRowArray();

		if ($result && !empty($result['teknik_penilaian'])) {
			$teknik = json_decode($result['teknik_penilaian'], true);

			// Check for JSON decode errors
			if (json_last_error() === JSON_ERROR_NONE && is_array($teknik) && !empty($teknik)) {
				$teknikNames = [];

				if (array_keys($teknik) !== range(0, count($teknik) - 1)) {
					foreach ($teknik as $teknikKey => $bobot) {
						$teknikNames[] = $this->formatTeknikPenilaian($teknikKey);
					}
				} else {
					foreach ($teknik as $t) {
						if (is_array($t) && isset($t['teknik'])) {
							$teknikNames[] = $this->formatTeknikPenilaian($t['teknik']);
						} elseif (is_string($t)) {
							$teknikNames[] = $this->formatTeknikPenilaian($t);
						}
					}
				}

				if (!empty($teknikNames)) {
					return implode(', ', array_unique($teknikNames));
				}
			}
		}
	}

	private function getAnalysisData($assessmentData, $passingThreshold, $mataKuliahId, $tahunAkademik, $programStudi = null)
	{
		$cpmkTercapai = [];
		$cpmkTidakTercapai = [];

		foreach ($assessmentData as $assessment) {
			$persentaseCapaian = $assessment['persentase_capaian'];

			if ($persentaseCapaian >= $passingThreshold) {
				$cpmkTercapai[] = $assessment['kode_cpmk'];
			} else {
				$cpmkTidakTercapai[] = $assessment['kode_cpmk'];
			}
		}

		// Check if there's saved analysis
		$savedAnalysis = $this->analysisCpmkModel->getAnalysis($mataKuliahId, $tahunAkademik, $programStudi);

		// Determine mode and analysis text
		$mode = $savedAnalysis['mode'] ?? 'auto';
		$autoOptions = [];
		$analysisSingkat = '';

		if ($mode === 'manual' && !empty($savedAnalysis['analisis_singkat'])) {
			// Use manual analysis
			$analysisSingkat = $savedAnalysis['analisis_singkat'];
		} else {
			// Decode auto_options from saved analysis
			if (!empty($savedAnalysis['auto_options'])) {
				$autoOptions = json_decode($savedAnalysis['auto_options'], true);
				if (!is_array($autoOptions)) {
					$autoOptions = [];
				}
			}

			// Use auto-generated analysis with saved options
			$analysisSingkat = $this->generateAnalysisSingkat($cpmkTercapai, $cpmkTidakTercapai, $passingThreshold, $autoOptions);
		}

		return [
			'standar_minimal' => $passingThreshold,
			'cpmk_tercapai' => $cpmkTercapai,
			'cpmk_tidak_tercapai' => $cpmkTidakTercapai,
			'analisis_singkat' => $analysisSingkat,
			'mode' => $mode,
			'auto_options' => $autoOptions
		];
	}

	private function generateAnalysisSingkat($cpmkTercapai, $cpmkTidakTercapai, $passingThreshold, $autoOptions = [])
	{
		// If no options specified, use the main default template
		if (empty($autoOptions)) {
			$autoOptions = ['default'];
		}

		// Get templates from database
		$templates = $this->analysisTemplateModel->getTemplatesAsArray();

		// Prepare placeholder values
		$totalCpmk = count($cpmkTercapai) + count($cpmkTidakTercapai);
		$jumlahTercapai = count($cpmkTercapai);
		$jumlahTidakTercapai = count($cpmkTidakTercapai);
		$persentaseTercapai = $totalCpmk > 0 ? round(($jumlahTercapai / $totalCpmk) * 100, 2) : 0;
		$cpmkTercapaiList = implode(', ', $cpmkTercapai);
		$cpmkTidakTercapaiList = implode(', ', $cpmkTidakTercapai);

		$placeholders = [
			'{total_cpmk}' => $totalCpmk,
			'{jumlah_tercapai}' => $jumlahTercapai,
			'{jumlah_tidak_tercapai}' => $jumlahTidakTercapai,
			'{persentase_tercapai}' => $persentaseTercapai,
			'{cpmk_tercapai_list}' => $cpmkTercapaiList,
			'{cpmk_tidak_tercapai_list}' => $cpmkTidakTercapaiList,
			'{standar_minimal}' => $passingThreshold,
		];

		$analysisParts = [];

		// Process each selected option
		foreach ($autoOptions as $optionKey) {
			if (!isset($templates[$optionKey])) {
				continue;
			}

			$template = $templates[$optionKey];

			// Determine which template to use based on whether all CPMKs are achieved
			$templateText = empty($cpmkTidakTercapai)
				? $template['template_tercapai']
				: $template['template_tidak_tercapai'];

			// Skip if template is empty
			if (empty(trim($templateText))) {
				continue;
			}

			// Replace placeholders with actual values
			$analysisText = str_replace(
				array_keys($placeholders),
				array_values($placeholders),
				$templateText
			);

			$analysisParts[] = $analysisText;
		}

		return implode(' ', $analysisParts);
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

	public function exportZip()
	{
		try {
			// Check user role
			if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
				return redirect()->to('/')->with('error', 'Akses ditolak.');
			}

			$mataKuliahId = $this->request->getGet('mata_kuliah_id');
			$tahunAkademik = $this->request->getGet('tahun_akademik');
			$programStudi = $this->request->getGet('program_studi_kode');
			$documents = $this->request->getGet('documents');

			// Log the request parameters
			log_message('info', 'Export ZIP requested - MK: ' . $mataKuliahId . ', TA: ' . $tahunAkademik . ', Docs: ' . $documents);

			if (!$mataKuliahId || !$tahunAkademik) {
				return redirect()->to('admin/laporan-cpmk')->with('error', 'Pilih mata kuliah dan tahun akademik terlebih dahulu.');
			}

			// Get course portfolio data
			$portfolioData = $this->getPortfolioData($mataKuliahId, $tahunAkademik, $programStudi);

			if (!$portfolioData) {
				log_message('error', 'Portfolio data not found for MK: ' . $mataKuliahId . ', TA: ' . $tahunAkademik);
				return redirect()->to('admin/laporan-cpmk')->with('error', 'Data tidak ditemukan untuk mata kuliah dan tahun akademik yang dipilih.');
			}

			// Parse selected documents
			$selectedDocuments = [];
			if (!empty($documents)) {
				$selectedDocuments = explode(',', $documents);
			}

			// Get jadwal ID for nilai exports
			$builder = $this->db->table('jadwal');
			$builder->where('mata_kuliah_id', $mataKuliahId);
			$builder->like('tahun_akademik', $tahunAkademik, 'both');
			if ($programStudi) {
				$builder->where('program_studi_kode', $programStudi);
			}
			$jadwalMengajar = $builder->get()->getFirstRow();
			$jadwalMengajarId = $jadwalMengajar ? $jadwalMengajar->id : null;

			// Get RPS ID
			$rpsBuilder = $this->db->table('rps');
			$rpsBuilder->where('mata_kuliah_id', $mataKuliahId);
			$rpsBuilder->orderBy('id', 'DESC');
			$rpsData = $rpsBuilder->get()->getFirstRow();
			$rpsId = $rpsData ? $rpsData->id : null;

			// Create ZIP file
			$zip = new \ZipArchive();
			$zipFilename = 'Portofolio_' . str_replace(' ', '_', $portfolioData['identitas']['kode_mata_kuliah']) . '_' . time() . '.zip';
			$zipPath = WRITEPATH . 'uploads/' . $zipFilename;

			// Ensure uploads directory exists
			if (!is_dir(WRITEPATH . 'uploads')) {
				mkdir(WRITEPATH . 'uploads', 0755, true);
			}

			if ($zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== TRUE) {
				log_message('error', 'Failed to create ZIP file at: ' . $zipPath);
				return redirect()->to('admin/laporan-cpmk')->with('error', 'Gagal membuat file ZIP.');
			}

			$tempFiles = [];

			// 1. Generate and add Portfolio DOC
			$docData = [
				'portfolio' => $portfolioData,
				'selectedDocuments' => $selectedDocuments
			];
			$portfolioHtml = view('admin/laporan_cpmk/portfolio_pdf', $docData);

			// Wrap in Word-compatible HTML structure
			$wordHtml = '
			<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
			<head>
				<meta charset="UTF-8">
				<xml>
					<w:WordDocument>
						<w:View>Print</w:View>
						<w:Zoom>100</w:Zoom>
					</w:WordDocument>
				</xml>
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
			<body>';

			// Extract body content from the portfolio HTML
			if (preg_match('/<body>(.*?)<\/body>/s', $portfolioHtml, $matches)) {
				$wordHtml .= $matches[1];
			} else {
				$wordHtml .= $portfolioHtml;
			}

			$wordHtml .= '</body></html>';

			// Save as .doc file
			$docFilename = 'Portofolio_Mata_Kuliah.doc';
			$tempDocPath = WRITEPATH . 'uploads/temp_portfolio_' . time() . '.doc';
			file_put_contents($tempDocPath, $wordHtml);
			$zip->addFile($tempDocPath, $docFilename);
			$tempFiles[] = $tempDocPath;

			// 2. Add supporting documents based on selection
			foreach ($selectedDocuments as $docType) {
				$fileAdded = $this->addDocumentToZip($zip, $docType, $rpsId, $jadwalMengajarId, $tempFiles);
				if (!$fileAdded) {
					log_message('warning', 'Failed to add document type: ' . $docType);
				}
			}

			$zip->close();

			// Download the ZIP file using CodeIgniter's download response
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
			log_message('error', 'Error in exportZip: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
			return redirect()->to('admin/laporan-cpmk')->with('error', 'Terjadi kesalahan saat membuat file ZIP: ' . $e->getMessage());
		}
	}

	private function addDocumentToZip(&$zip, $docType, $rpsId, $jadwalMengajarId, &$tempFiles)
	{
		try {
			log_message('info', 'Adding document type: ' . $docType . ' (RPS ID: ' . $rpsId . ', Jadwal ID: ' . $jadwalMengajarId . ')');

			switch ($docType) {
				case 'rps':
					if (!$rpsId) {
						log_message('warning', 'RPS ID is null, skipping RPS document');
						return false;
					}

					log_message('info', 'Generating RPS DOC for RPS ID: ' . $rpsId);

					// Generate RPS DOC using preview data
					$rpsService = new \App\Services\RpsPreviewService();
					$rpsData = $rpsService->getData($rpsId);

					// Render just the content section without the layout
					$rpsData['for_doc'] = true; // Flag to indicate DOC generation

					// Get the view content and extract only the #rps-content div
					$fullHtml = view('rps/preview', $rpsData);

					// Extract only the content within #rps-content div using DOMDocument
					libxml_use_internal_errors(true);
					$dom = new \DOMDocument();
					$dom->loadHTML('<?xml encoding="UTF-8">' . $fullHtml);
					libxml_clear_errors();

					$xpath = new \DOMXPath($dom);
					$contentNode = $xpath->query("//*[@id='rps-content']")->item(0);

					if ($contentNode) {
						// Get the inner HTML of rps-content
						$cleanHtml = '';
						foreach ($contentNode->childNodes as $child) {
							$cleanHtml .= $dom->saveHTML($child);
						}

						// Wrap in a Word-compatible HTML structure
						$rpsHtml = '
						<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
						<head>
							<meta charset="UTF-8">
							<xml>
								<w:WordDocument>
									<w:View>Print</w:View>
									<w:Zoom>100</w:Zoom>
								</w:WordDocument>
							</xml>
							<style>
								body { font-family: Arial, sans-serif; font-size: 11pt; }
								table { border-collapse: collapse; width: 100%; margin-bottom: 10px; }
								table, th, td { border: 1px solid #222; }
								th, td { padding: 8px; }
								.text-center { text-align: center; }
								@page { margin: 2cm; }
							</style>
						</head>
						<body>' . $cleanHtml . '</body>
						</html>';
					} else {
						// Fallback: use full HTML
						$rpsHtml = $fullHtml;
					}

					// Save as .doc file (HTML format that Word can open)
					$tempRpsPath = WRITEPATH . 'uploads/temp_rps_' . time() . '_' . mt_rand() . '.doc';
					file_put_contents($tempRpsPath, $rpsHtml);

					if (file_exists($tempRpsPath) && filesize($tempRpsPath) > 0) {
						$zip->addFile($tempRpsPath, 'RPS.doc');
						$tempFiles[] = $tempRpsPath;
						log_message('info', 'Successfully added RPS DOC to ZIP');
						return true;
					} else {
						log_message('error', 'RPS DOC file not created or empty: ' . $tempRpsPath);
						return false;
					}

				case 'nilai':
					if (!$jadwalMengajarId) {
						log_message('warning', 'Jadwal Mengajar ID is null, skipping DPNA document');
						return false;
					}

					log_message('info', 'Generating DPNA Excel for Jadwal ID: ' . $jadwalMengajarId);
					// Generate DPNA Excel file directly
					$tempNilaiPath = WRITEPATH . 'uploads/temp_dpna_' . time() . '_' . mt_rand() . '.xlsx';

					// Use the Nilai controller's logic to generate Excel
					$result = $this->generateDpnaExcel($jadwalMengajarId, $tempNilaiPath);

					if ($result && file_exists($tempNilaiPath) && filesize($tempNilaiPath) > 0) {
						$zip->addFile($tempNilaiPath, 'Daftar_Nilai_Mahasiswa.xlsx');
						$tempFiles[] = $tempNilaiPath;
						log_message('info', 'Successfully added DPNA Excel to ZIP');
						return true;
					} else {
						log_message('error', 'DPNA Excel file not created or empty: ' . $tempNilaiPath);
						return false;
					}

				case 'rekapitulasi':
					if (!$jadwalMengajarId) {
						log_message('warning', 'Jadwal Mengajar ID is null, skipping CPMK Rekapitulasi document');
						return false;
					}

					log_message('info', 'Generating CPMK Excel for Jadwal ID: ' . $jadwalMengajarId);
					// Generate CPMK Excel file directly
					$tempRekapPath = WRITEPATH . 'uploads/temp_cpmk_' . time() . '_' . mt_rand() . '.xlsx';

					// Use the Nilai controller's logic to generate Excel
					$result = $this->generateCpmkExcel($jadwalMengajarId, $tempRekapPath);

					if ($result && file_exists($tempRekapPath) && filesize($tempRekapPath) > 0) {
						$zip->addFile($tempRekapPath, 'Rekapitulasi_Nilai_CPMK.xlsx');
						$tempFiles[] = $tempRekapPath;
						log_message('info', 'Successfully added CPMK Excel to ZIP');
						return true;
					} else {
						log_message('error', 'CPMK Excel file not created or empty: ' . $tempRekapPath);
						return false;
					}

				case 'rubrik':
					if (!$jadwalMengajarId) {
						log_message('warning', 'Jadwal Mengajar ID is null, skipping Rubrik Penilaian document');
						return false;
					}

					log_message('info', 'Adding Rubrik Penilaian for Jadwal ID: ' . $jadwalMengajarId);

					// Get jadwal mengajar data
					$jadwal = $this->jadwalMengajarModel->find($jadwalMengajarId);
					if (!$jadwal || empty($jadwal['rubrik_penilaian_file'])) {
						log_message('warning', 'Rubrik Penilaian file not found for Jadwal ID: ' . $jadwalMengajarId);
						return false;
					}

					// Get file path
					$rubrikPath = FCPATH . 'uploads/rubrik/' . $jadwal['rubrik_penilaian_file'];

					if (!file_exists($rubrikPath)) {
						log_message('error', 'Rubrik Penilaian file does not exist: ' . $rubrikPath);
						return false;
					}

					// Get file extension to determine the filename in ZIP
					$extension = pathinfo($jadwal['rubrik_penilaian_file'], PATHINFO_EXTENSION);
					$zipFilename = 'Rubrik_Penilaian.' . $extension;

					// Add file to ZIP
					$zip->addFile($rubrikPath, $zipFilename);
					log_message('info', 'Successfully added Rubrik Penilaian to ZIP');
					return true;

				case 'contoh_soal':
					if (!$jadwalMengajarId) {
						log_message('warning', 'Jadwal Mengajar ID is null, skipping Contoh Soal document');
						return false;
					}

					log_message('info', 'Adding Contoh Soal for Jadwal ID: ' . $jadwalMengajarId);

					// Get jadwal mengajar data
					$jadwal = $this->jadwalMengajarModel->find($jadwalMengajarId);
					if (!$jadwal || empty($jadwal['contoh_soal_file'])) {
						log_message('warning', 'Contoh Soal file not found for Jadwal ID: ' . $jadwalMengajarId);
						return false;
					}

					// Get file path
					$contohSoalPath = FCPATH . 'uploads/contoh_soal/' . $jadwal['contoh_soal_file'];

					if (!file_exists($contohSoalPath)) {
						log_message('error', 'Contoh Soal file does not exist: ' . $contohSoalPath);
						return false;
					}

					// Get file extension to determine the filename in ZIP
					$extension = pathinfo($jadwal['contoh_soal_file'], PATHINFO_EXTENSION);
					$zipFilename = 'Contoh_Soal_dan_Jawaban.' . $extension;

					// Add file to ZIP
					$zip->addFile($contohSoalPath, $zipFilename);
					log_message('info', 'Successfully added Contoh Soal to ZIP');
					return true;

				case 'notulen':
					if (!$jadwalMengajarId) {
						log_message('warning', 'Jadwal Mengajar ID is null, skipping Notulen document');
						return false;
					}

					log_message('info', 'Adding Notulen for Jadwal ID: ' . $jadwalMengajarId);

					// Get jadwal mengajar data
					$jadwal = $this->jadwalMengajarModel->find($jadwalMengajarId);
					if (!$jadwal || empty($jadwal['notulen_rapat_file'])) {
						log_message('warning', 'Notulen file not found for Jadwal ID: ' . $jadwalMengajarId);
						return false;
					}

					// Get file path
					$notulenPath = FCPATH . 'uploads/notulen/' . $jadwal['notulen_rapat_file'];

					if (!file_exists($notulenPath)) {
						log_message('error', 'Notulen file does not exist: ' . $notulenPath);
						return false;
					}

					// Get file extension to determine the filename in ZIP
					$extension = pathinfo($jadwal['notulen_rapat_file'], PATHINFO_EXTENSION);
					$zipFilename = 'Notulen_Rapat_Evaluasi.' . $extension;

					// Add file to ZIP
					$zip->addFile($notulenPath, $zipFilename);
					log_message('info', 'Successfully added Notulen to ZIP');
					return true;
			}
		} catch (\Exception $e) {
			log_message('error', 'Error adding document to ZIP: ' . $e->getMessage());
			return false;
		}

		return false;
	}

	private function generateDpnaExcel($jadwalId, $outputPath)
	{
		try {
			// Import required models
			$jadwalModel = new \App\Models\MengajarModel();
			$mahasiswaModel = new \App\Models\MahasiswaModel();
			$nilaiTeknikModel = new \App\Models\NilaiTeknikPenilaianModel();
			$nilaiMahasiswaModel = new \App\Models\NilaiMahasiswaModel();

			// Get jadwal details
			$jadwal = $jadwalModel->getJadwalWithDetails(['id' => $jadwalId], true);
			if (!$jadwal) {
				log_message('error', 'Jadwal not found for ID: ' . $jadwalId);
				return false;
			}

			// Get students for this class
			$students = $mahasiswaModel->getStudentsForScoring($jadwal['program_studi_kode'], $jadwal['semester']);

			// Get SEPARATED teknik_penilaian list (NOT grouped/combined by type)
			$teknik_list = $nilaiTeknikModel->getTeknikPenilaianByJadwal($jadwalId);

			// Group teknik_list by tahap for organized display
			$teknik_by_tahap = [];
			foreach ($teknik_list as $item) {
				$tahap = $item['tahap_penilaian'] ?? 'Perkuliahan';
				if (!isset($teknik_by_tahap[$tahap])) {
					$teknik_by_tahap[$tahap] = [];
				}
				$teknik_by_tahap[$tahap][] = $item;
			}

			// Get existing scores to pre-fill the form (individual per week)
			$existing_scores = $nilaiTeknikModel->getScoresByJadwalForInput($jadwalId);

			// Get final scores
			$final_scores = $nilaiMahasiswaModel->getFinalScoresByJadwal($jadwalId);
			$final_scores_map = [];
			foreach ($final_scores as $score) {
				$final_scores_map[$score['mahasiswa_id']] = $score;
			}

			// Helper function to calculate keterangan based on grade
			$getKeterangan = function ($grade) {
				$failingGrades = ['B', 'BC', 'C', 'D', 'E'];
				if (in_array(strtoupper($grade), $failingGrades)) {
					return 'TM'; // Tidak Memenuhi
				}
				return 'Lulus';
			};

			// Prepare DPNA data
			$dpna_data = [];
			$no = 1;
			foreach ($students as $student) {
				$mahasiswa_id = $student['id'];

				// Initialize row with basic data
				$row_data = [
					'no' => $no++,
					'nim' => $student['nim'],
					'nama' => $student['nama_lengkap']
				];

				// Add scores for each teknik penilaian (separated by week/rps_mingguan_id)
				foreach ($teknik_list as $item) {
					$rps_mingguan_id = $item['rps_mingguan_id'];
					$teknik_key = $item['teknik_key'];

					$score = 0;
					if (isset($existing_scores[$mahasiswa_id][$rps_mingguan_id][$teknik_key])) {
						$score = $existing_scores[$mahasiswa_id][$rps_mingguan_id][$teknik_key];
					}
					$row_data['teknik_' . $rps_mingguan_id . '_' . $teknik_key] = $score;
				}

				// Get nilai akhir and nilai huruf
				$nilai_akhir = $final_scores_map[$mahasiswa_id]['nilai_akhir'] ?? 0;
				$nilai_huruf = $final_scores_map[$mahasiswa_id]['nilai_huruf'] ?? '-';
				$keterangan = $getKeterangan($nilai_huruf);

				$row_data['nilai_akhir'] = $nilai_akhir;
				$row_data['nilai_huruf'] = $nilai_huruf;
				$row_data['keterangan'] = $keterangan;

				$dpna_data[] = $row_data;
			}

			// Create Excel file using PhpSpreadsheet
			$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();

			// Set document properties
			$spreadsheet->getProperties()
				->setCreator('OBE System')
				->setTitle('DPNA - ' . $jadwal['nama_mk'])
				->setSubject('Daftar Penilaian Nilai Akhir');

			// Set row height for header
			$sheet->getRowDimension(1)->setRowHeight(50);
			$sheet->getRowDimension(2)->setRowHeight(20);

			// Add logo if exists
			$logoPath = FCPATH . 'img/Logo UPR.png';
			if (file_exists($logoPath)) {
				$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
				$drawing->setName('Logo');
				$drawing->setDescription('Logo');
				$drawing->setPath($logoPath);
				$drawing->setCoordinates('A1');
				$drawing->setHeight(50);
				$drawing->setOffsetX(10);
				$drawing->setOffsetY(5);
				$drawing->setWorksheet($sheet);
			}

			// Determine semester type (Genap/Ganjil) based on semester number
			$semester_type = '';
			if (isset($jadwal['semester'])) {
				$semester_type = ($jadwal['semester'] % 2 == 0) ? 'Genap' : 'Ganjil';
			}

			// Extract year from tahun_akademik (e.g., "2023/2024 Ganjil" -> "2023/2024")
			$tahun = isset($jadwal['tahun_akademik']) ? trim(preg_replace('/(Ganjil|Genap)/', '', $jadwal['tahun_akademik'])) : '';

			// Helper function to convert column index to Excel column letter
			$getColumnLetter = function ($index) {
				$letter = '';
				while ($index >= 0) {
					$letter = chr($index % 26 + 65) . $letter;
					$index = floor($index / 26) - 1;
				}
				return $letter;
			};

			// Calculate the last column for dynamic layout
			// Columns: No, NIM, Nama, [teknik_list], Nilai Akhir (Angka), Nilai Akhir (Huruf), Keterangan
			$totalColumns = 3 + count($teknik_list) + 3; // 3 basic + teknik + 3 final columns
			$keteranganColIndex = $totalColumns - 1;
			$lastColLetter = $getColumnLetter($keteranganColIndex);
			$beforeLastColLetter = $getColumnLetter($keteranganColIndex - 1);

			// Set header - Ministry text (in same row as logo)
			$header_text = "KEMENTERIAN PENDIDIKAN TINGGI, SAINS, \nDAN TEKNOLOGI";
			$sheet->setCellValue('B1', $header_text);
			$sheet->mergeCells('B1:' . $beforeLastColLetter . '1');
			$sheet->getStyle('B1')->getFont()->setBold(true)->setSize(15);
			$sheet->getStyle('B1')->getAlignment()
				->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
				->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
				->setWrapText(true);

			// Set DPNA and Semester info on the right side (last column)
			$dpna_text = "DPNA\nSemester " . $semester_type . " " . $tahun;
			$sheet->setCellValue($lastColLetter . '1', $dpna_text);
			$sheet->getStyle($lastColLetter . '1')->getFont()->setBold(true)->setSize(15);
			$sheet->getStyle($lastColLetter . '1')->getAlignment()
				->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
				->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
				->setWrapText(true);

			// University text (row 2)
			$sheet->setCellValue('B2', 'UNIVERSITAS PALANGKA RAYA');
			$sheet->mergeCells('B2:' . $beforeLastColLetter . '2');
			$sheet->getStyle('B2')->getFont()->setBold(true)->setSize(15);
			$sheet->getStyle('B2')->getAlignment()
				->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
				->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

			// Course information
			$row = 4;
			$sheet->setCellValue('B' . $row, 'MATA KULIAH');
			$sheet->setCellValue('C' . $row, strtoupper($jadwal['nama_mk']));
			$row++;
			$sheet->setCellValue('B' . $row, 'KELAS/PROGRAM STUDI');
			$sheet->setCellValue('C' . $row, strtoupper($jadwal['kelas']) . " / " . strtoupper($jadwal['program_studi_kode']));
			$row++;
			$sheet->setCellValue('B' . $row, 'DOSEN PENGAMPU');
			$sheet->setCellValue('C' . $row, strtoupper($jadwal['dosen_ketua']));

			// Style course information (bold and bigger)
			$sheet->getStyle('B4:C' . $row)->getFont()->setBold(true)->setSize(12);

			// Table header - First row
			$row += 2;
			$headerRow = $row;

			$col = 0;
			$sheet->setCellValue($getColumnLetter($col++) . $row, 'No');
			$sheet->setCellValue($getColumnLetter($col++) . $row, 'NIM');
			$sheet->setCellValue($getColumnLetter($col++) . $row, 'Nama');

			// Merge cells for basic columns (No, NIM, Nama)
			$sheet->mergeCells('A' . $row . ':A' . ($row + 1));
			$sheet->mergeCells('B' . $row . ':B' . ($row + 1));
			$sheet->mergeCells('C' . $row . ':C' . ($row + 1));

			// Add dynamic teknik penilaian columns (separated by week)
			// First row: Tahap headers
			$tahapStartCols = [];
			foreach ($teknik_by_tahap as $tahap => $tahap_items) {
				$tahapStartCol = $col;
				$tahapEndCol = $col + count($tahap_items) - 1;
				$sheet->setCellValue($getColumnLetter($col) . $row, $tahap);
				if ($tahapEndCol > $tahapStartCol) {
					$sheet->mergeCells($getColumnLetter($tahapStartCol) . $row . ':' . $getColumnLetter($tahapEndCol) . $row);
				}
				$col = $tahapEndCol + 1;
			}

			// Second row: Individual teknik with minggu and CPMK
			$col = 3; // Reset to after Nama column
			$row++;
			foreach ($teknik_list as $item) {
				$colLetter = $getColumnLetter($col);
				$cpmk_display = $item['kode_cpmk'] ?? $item['cpmk_code'] ?? 'N/A';
				$teknik_text = $item['teknik_label'] . "\nMinggu: " . $item['minggu'] . "\n" . $cpmk_display . " (" . number_format($item['bobot'], 1) . '%)';
				$sheet->setCellValue($colLetter . $row, $teknik_text);
				$col++;
			}

			// Nilai Akhir columns (merged header)
			$nilaiAkhirStartCol = $col;
			$row--; // Go back to first header row
			$sheet->setCellValue($getColumnLetter($col) . $row, 'Nilai Akhir');
			$sheet->mergeCells($getColumnLetter($col) . $row . ':' . $getColumnLetter($col + 1) . $row);

			// Keterangan column (using pre-calculated index)
			$sheet->setCellValue($lastColLetter . $row, 'Keterangan');
			$sheet->mergeCells($lastColLetter . $row . ':' . $lastColLetter . ($row + 1));

			// Second header row - Sub-headers for Nilai Akhir
			$row++;
			$sheet->setCellValue($getColumnLetter($nilaiAkhirStartCol) . $row, 'Angka');
			$sheet->setCellValue($getColumnLetter($nilaiAkhirStartCol + 1) . $row, 'Huruf');

			// Style both header rows
			$headerStyle = $sheet->getStyle('A' . $headerRow . ':' . $lastColLetter . $row);
			$headerStyle->getFont()->setBold(true);
			$headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
				->getStartColor()->setARGB('FF4472C4');
			$headerStyle->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
			$headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$headerStyle->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
			$headerStyle->getAlignment()->setWrapText(true);

			// Data rows
			$row++;
			foreach ($dpna_data as $data) {
				$col = 0;
				$sheet->setCellValue($getColumnLetter($col++) . $row, $data['no']);
				$sheet->setCellValue($getColumnLetter($col++) . $row, $data['nim']);
				$sheet->setCellValue($getColumnLetter($col++) . $row, $data['nama']);

				// Add teknik penilaian scores (separated by week)
				$teknikColStart = $col;
				foreach ($teknik_list as $item) {
					$rps_mingguan_id = $item['rps_mingguan_id'];
					$teknik_key = $item['teknik_key'];
					$sheet->setCellValue($getColumnLetter($col++) . $row, $data['teknik_' . $rps_mingguan_id . '_' . $teknik_key]);
				}
				$teknikColEnd = $col - 1;

				// Add background color to teknik penilaian cells
				$sheet->getStyle($getColumnLetter($teknikColStart) . $row . ':' . $getColumnLetter($teknikColEnd) . $row)->getFill()
					->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
					->getStartColor()->setARGB('FFFFFF00'); // Light yellow

				// Add nilai akhir
				$sheet->setCellValue($getColumnLetter($col++) . $row, $data['nilai_akhir']);
				$sheet->setCellValue($getColumnLetter($col++) . $row, $data['nilai_huruf']);
				$sheet->setCellValue($getColumnLetter($col++) . $row, $data['keterangan']);

				// Center align for numeric columns
				$sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				$sheet->getStyle($getColumnLetter($teknikColStart) . $row . ':' . $lastColLetter . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

				$row++;
			}

			// Add borders to table
			$lastRow = $row - 1;
			$sheet->getStyle('A' . $headerRow . ':' . $lastColLetter . $lastRow)->applyFromArray([
				'borders' => [
					'allBorders' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						'color' => ['argb' => 'FF000000'],
					],
				],
			]);

			// Auto-size columns
			for ($i = 0; $i <= $keteranganColIndex; $i++) {
				$sheet->getColumnDimension($getColumnLetter($i))->setAutoSize(true);
			}

			// Get NIP of dosen ketua
			$db = \Config\Database::connect();
			$dosenKetuaNip = $db->table('jadwal_dosen jd')
				->select('d.nip')
				->join('dosen d', 'd.id = jd.dosen_id')
				->where('jd.jadwal_id', $jadwalId)
				->where('jd.role', 'leader')
				->get()
				->getRowArray();
			$nip = $dosenKetuaNip['nip'] ?? '';

			// Add signature section
			$row = $lastRow + 3; // Add some space after the table

			// Date and location on the right side
			$sheet->setCellValue($lastColLetter . $row, 'Palangka Raya, ' . date('d F Y'));
			$sheet->mergeCells($lastColLetter . $row . ':' . $lastColLetter . $row);
			$sheet->getStyle($lastColLetter . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

			$row++;

			// Mengetahui
			$sheet->setCellValue($lastColLetter . $row, 'Mengetahui');
			$sheet->mergeCells($lastColLetter . $row . ':' . $lastColLetter . $row);
			$sheet->getStyle($lastColLetter . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

			$row++;

			// Title
			$sheet->setCellValue($lastColLetter . $row, 'Dosen Koordinator Mata Kuliah');
			$sheet->mergeCells($lastColLetter . $row . ':' . $lastColLetter . $row);
			$sheet->getStyle($lastColLetter . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

			// Add empty rows for signature space
			$row += 4;

			// Dosen name
			$sheet->setCellValue($lastColLetter . $row, $jadwal['dosen_ketua']);
			$sheet->mergeCells($lastColLetter . $row . ':' . $lastColLetter . $row);
			$sheet->getStyle($lastColLetter . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
			$sheet->getStyle($lastColLetter . $row)->getFont()->setBold(true);

			$row++;

			// NIP line
			$sheet->setCellValue($lastColLetter . $row, 'NIP. ' . $nip);
			$sheet->mergeCells($lastColLetter . $row . ':' . $lastColLetter . $row);
			$sheet->getStyle($lastColLetter . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

			// Save to file
			$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
			$writer->save($outputPath);

			log_message('info', 'DPNA Excel generated successfully: ' . $outputPath);
			return true;
		} catch (\Exception $e) {
			log_message('error', 'Error generating DPNA Excel: ' . $e->getMessage());
			return false;
		}
	}

	private function generateCpmkExcel($jadwalId, $outputPath)
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
			$students = $mahasiswaModel->getStudentsForScoring($jadwal['program_studi_kode'], $jadwal['semester']);

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

			// Set row height for header
			$sheet->getRowDimension(1)->setRowHeight(50);
			$sheet->getRowDimension(2)->setRowHeight(20);

			// Add logo if exists
			$logoPath = FCPATH . 'img/Logo UPR.png';
			if (file_exists($logoPath)) {
				$drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
				$drawing->setName('Logo');
				$drawing->setDescription('Logo');
				$drawing->setPath($logoPath);
				$drawing->setCoordinates('A1');
				$drawing->setHeight(50);
				$drawing->setOffsetX(10);
				$drawing->setOffsetY(5);
				$drawing->setWorksheet($sheet);
			}

			// Determine semester type (Genap/Ganjil) based on semester number
			$semester_type = '';
			if (isset($jadwal['semester'])) {
				$semester_type = ($jadwal['semester'] % 2 == 0) ? 'Genap' : 'Ganjil';
			}

			// Extract year from tahun_akademik (e.g., "2023/2024 Ganjil" -> "2023/2024")
			$tahun = isset($jadwal['tahun_akademik']) ? trim(preg_replace('/(Ganjil|Genap)/', '', $jadwal['tahun_akademik'])) : '';

			// Calculate total columns for proper header width
			$totalColumns = 3 + (count($cpmk_list) * 2) + 1; // No, NIM, Nama + (CPMK Score + Capaian) * count + Nilai Akhir
			$lastColumn = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns);

			// Set header - Ministry text (in same row as logo)
			$header_text = "KEMENTERIAN PENDIDIKAN TINGGI, SAINS, \nDAN TEKNOLOGI";
			$sheet->setCellValue('B1', $header_text);
			$header_end_col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns - 1);
			$sheet->mergeCells('B1:' . $header_end_col . '1');
			$sheet->getStyle('B1')->getFont()->setBold(true)->setSize(15);
			$sheet->getStyle('B1')->getAlignment()
				->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
				->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
				->setWrapText(true);

			// Set CPMK and Semester info on the right side (last column)
			$cpmk_text = "NILAI CPMK\nSemester " . $semester_type . " " . $tahun;
			$sheet->setCellValue($lastColumn . '1', $cpmk_text);
			$sheet->getStyle($lastColumn . '1')->getFont()->setBold(true)->setSize(15);
			$sheet->getStyle($lastColumn . '1')->getAlignment()
				->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
				->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
				->setWrapText(true);

			// University text (row 2)
			$sheet->setCellValue('B2', 'UNIVERSITAS PALANGKA RAYA');
			$sheet->mergeCells('B2:' . $header_end_col . '2');
			$sheet->getStyle('B2')->getFont()->setBold(true)->setSize(15);
			$sheet->getStyle('B2')->getAlignment()
				->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
				->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

			// Course information
			$row = 4;
			$sheet->setCellValue('B' . $row, 'MATA KULIAH');
			$sheet->setCellValue('C' . $row, strtoupper($jadwal['nama_mk']));
			$row++;
			$sheet->setCellValue('B' . $row, 'KELAS/PROGRAM STUDI');
			$sheet->setCellValue('C' . $row, strtoupper($jadwal['kelas']) . " / " . strtoupper($jadwal['program_studi_kode']));
			$row++;
			$sheet->setCellValue('B' . $row, 'DOSEN PENGAMPU');
			$sheet->setCellValue('C' . $row, strtoupper($jadwal['dosen_ketua']));

			// Style course information (bold and bigger)
			$sheet->getStyle('B4:C' . $row)->getFont()->setBold(true)->setSize(12);

			// Table header - First row
			$row += 2;
			$headerRow = $row;
			$col = 1;

			// Basic columns - First row
			$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, 'No');
			$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, 'NIM');
			$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, 'Nama');

			// CPMK columns - merged headers with sub-cells
			foreach ($cpmk_list as $cpmk) {
				$startCol = $col;
				$endCol = $col + 1;
				$startColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol);
				$endColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endCol);

				// Merge cells for CPMK header
				$sheet->setCellValue($startColLetter . $row, $cpmk['kode_cpmk']);
				$sheet->mergeCells($startColLetter . $row . ':' . $endColLetter . $row);

				$col += 2;
			}

			// Nilai Akhir MK column
			$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row, 'Nilai Akhir MK');

			// Merge cells vertically for columns that span both header rows
			$sheet->mergeCells('A' . $row . ':A' . ($row + 1));
			$sheet->mergeCells('B' . $row . ':B' . ($row + 1));
			$sheet->mergeCells('C' . $row . ':C' . ($row + 1));
			$nilaiAkhirCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
			$sheet->mergeCells($nilaiAkhirCol . $row . ':' . $nilaiAkhirCol . ($row + 1));

			// Second header row - Sub-headers for CPMK
			$row++;
			$col = 4; // Start after No, NIM, Nama
			foreach ($cpmk_list as $cpmk) {
				$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, 'Skor');
				$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, 'Capaian (%)');
			}

			// Style both header rows
			$headerStyle = $sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $row);
			$headerStyle->getFont()->setBold(true);
			$headerStyle->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
				->getStartColor()->setARGB('FF4472C4');
			$headerStyle->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);
			$headerStyle->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$headerStyle->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
			$headerStyle->getAlignment()->setWrapText(true);

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

				// CPMK scores and capaian
				$student_scores = [];
				foreach ($cpmk_list as $cpmk) {
					$score = $existing_scores[$mahasiswa_id][$cpmk['id']] ?? null;

					// Score column
					if ($score !== null && $score !== '') {
						$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, $score);
						$student_scores[] = (float)$score;

						// Capaian column
						$capaian = ($score / $cpmk['bobot_cpmk']) * 100;
						$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, number_format($capaian, 2));
					} else {
						$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, '-');
						$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, '-');
					}
				}

				// Nilai Akhir MK
				if (count($student_scores) > 0) {
					$total = array_sum($student_scores);
					$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row, number_format($total, 2));
				} else {
					$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col) . $row, '-');
				}

				// Center align for all data columns
				$sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
				$sheet->getStyle('D' . $row . ':' . $lastColumn . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

				$row++;
			}

			// Add borders to table
			$lastRow = $row - 1;
			$sheet->getStyle('A' . $headerRow . ':' . $lastColumn . $lastRow)->applyFromArray([
				'borders' => [
					'allBorders' => [
						'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
						'color' => ['argb' => 'FF000000'],
					],
				],
			]);

			// Auto-size all columns
			foreach (range(1, $totalColumns) as $col) {
				$columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col);
				$sheet->getColumnDimension($columnLetter)->setAutoSize(true);
			}

			// Get NIP of dosen ketua
			$db = \Config\Database::connect();
			$dosenKetuaNip = $db->table('jadwal_dosen jd')
				->select('d.nip')
				->join('dosen d', 'd.id = jd.dosen_id')
				->where('jd.jadwal_id', $jadwalId)
				->where('jd.role', 'leader')
				->get()
				->getRowArray();
			$nip = $dosenKetuaNip['nip'] ?? '';

			// Add signature section
			$row = $lastRow + 3; // Add some space after the table

			// Date and location on the right side
			$signatureStartCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns);
			$signatureEndCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalColumns);
			$sheet->setCellValue($signatureStartCol . $row, 'Palangka Raya, ' . date('d F Y'));
			$sheet->mergeCells($signatureStartCol . $row . ':' . $signatureEndCol . $row);
			$sheet->getStyle($signatureStartCol . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

			$row++;

			// Mengetahui
			$sheet->setCellValue($signatureStartCol . $row, 'Mengetahui');
			$sheet->mergeCells($signatureStartCol . $row . ':' . $signatureEndCol . $row);
			$sheet->getStyle($signatureStartCol . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

			$row++;

			// Title
			$sheet->setCellValue($signatureStartCol . $row, 'Dosen Koordinator Mata Kuliah');
			$sheet->mergeCells($signatureStartCol . $row . ':' . $signatureEndCol . $row);
			$sheet->getStyle($signatureStartCol . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

			// Add empty rows for signature space
			$row += 4;

			// Dosen name
			$sheet->setCellValue($signatureStartCol . $row, $jadwal['dosen_ketua']);
			$sheet->mergeCells($signatureStartCol . $row . ':' . $signatureEndCol . $row);
			$sheet->getStyle($signatureStartCol . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
			$sheet->getStyle($signatureStartCol . $row)->getFont()->setBold(true);

			$row++;

			// NIP line
			$sheet->setCellValue($signatureStartCol . $row, 'NIP. ' . $nip);
			$sheet->mergeCells($signatureStartCol . $row . ':' . $signatureEndCol . $row);
			$sheet->getStyle($signatureStartCol . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

			// Save to file
			$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
			$writer->save($outputPath);

			log_message('info', 'CPMK Excel generated successfully: ' . $outputPath);
			return true;
		} catch (\Exception $e) {
			log_message('error', 'Error generating CPMK Excel: ' . $e->getMessage());
			return false;
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

		$mataKuliahId = $this->request->getPost('mata_kuliah_id');
		$tahunAkademik = $this->request->getPost('tahun_akademik');
		$programStudi = $this->request->getPost('program_studi_kode');
		$mode = $this->request->getPost('mode');
		$analysisSingkat = $this->request->getPost('analisis_singkat');
		$autoOptionsJson = $this->request->getPost('auto_options');
		$templatesJson = $this->request->getPost('templates');

		if (!$mataKuliahId || !$tahunAkademik || !$mode) {
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
			'mata_kuliah_id' => $mataKuliahId,
			'tahun_akademik' => $tahunAkademik,
			'program_studi_kode' => $programStudi,
			'mode' => $mode,
			'analisis_singkat' => $mode === 'manual' ? $analysisSingkat : null,
			'auto_options' => $autoOptions
		];

		try {
			// Save analysis settings
			$this->analysisCpmkModel->saveAnalysis($data);

			// Save templates if provided
			if ($templatesJson) {
				$templates = json_decode($templatesJson, true);
				if (is_array($templates)) {
					foreach ($templates as $optionKey => $templateData) {
						$this->analysisTemplateModel->updateByKey($optionKey, [
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

	private function getCqiData($jadwalMengajarId)
	{
		$cqiList = $this->cqiModel->getCqiCpmkList($jadwalMengajarId);

		// Convert to associative array with kode_cpmk as key
		$cqiByKodeCpmk = [];
		foreach ($cqiList as $cqi) {
			$cqiByKodeCpmk[$cqi['kode_cpmk']] = $cqi;
		}

		return $cqiByKodeCpmk;
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

		$jadwalMengajarId = $this->request->getPost('jadwal_id');
		$cqiDataJson = $this->request->getPost('cqi_data');

		if (!$jadwalMengajarId || !$cqiDataJson) {
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
				if (!empty($cqi['kode_cpmk'])) {
					$data = [
						'jadwal_id' => $jadwalMengajarId,
						'kode_cpmk' => $cqi['kode_cpmk'],
						'masalah' => $cqi['masalah'] ?? null,
						'rencana_perbaikan' => $cqi['rencana_perbaikan'] ?? null,
						'penanggung_jawab' => $cqi['penanggung_jawab'] ?? null,
						'jadwal_pelaksanaan' => $cqi['jadwal_pelaksanaan'] ?? null
					];

					$this->cqiModel->saveCqiCpmk($data);
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

	public function templates()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return redirect()->to('/')->with('error', 'Akses ditolak.');
		}

		$data = [
			'title' => 'Template Analisis CPMK',
			'templates' => $this->analysisTemplateModel->getActiveTemplates(),
			'placeholders' => $this->analysisTemplateModel->getAvailablePlaceholders()
		];

		return view('admin/laporan_cpmk/templates', $data);
	}

	public function saveTemplate()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Akses ditolak.'
			])->setStatusCode(403);
		}

		$optionKey = $this->request->getPost('option_key');
		$templateTercapai = $this->request->getPost('template_tercapai');
		$templateTidakTercapai = $this->request->getPost('template_tidak_tercapai');

		if (!$optionKey) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Option key tidak valid.'
			])->setStatusCode(400);
		}

		$data = [
			'template_tercapai' => $templateTercapai,
			'template_tidak_tercapai' => $templateTidakTercapai
		];

		try {
			$result = $this->analysisTemplateModel->updateByKey($optionKey, $data);

			if ($result) {
				return $this->response->setJSON([
					'success' => true,
					'message' => 'Template berhasil disimpan.'
				]);
			} else {
				return $this->response->setJSON([
					'success' => false,
					'message' => 'Template tidak ditemukan.'
				])->setStatusCode(404);
			}
		} catch (\Exception $e) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Gagal menyimpan template: ' . $e->getMessage()
			])->setStatusCode(500);
		}
	}

	public function uploadRubrik()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Akses ditolak.'
			])->setStatusCode(403);
		}

		$jadwalMengajarId = $this->request->getPost('jadwal_id');
		$file = $this->request->getFile('rubrik_file');

		if (!$jadwalMengajarId || !$file) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Data tidak lengkap.'
			])->setStatusCode(400);
		}

		try {
			// Validate file
			if (!$file->isValid()) {
				throw new \Exception('File tidak valid.');
			}

			// Validate file type
			$allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
			if (!in_array($file->getMimeType(), $allowedTypes)) {
				throw new \Exception('File harus berformat PDF, DOC, atau DOCX.');
			}

			// Validate file size (max 5MB)
			if ($file->getSize() > 5 * 1024 * 1024) {
				throw new \Exception('Ukuran file maksimal 5MB.');
			}

			// Get jadwal mengajar data
			$jadwal = $this->jadwalMengajarModel->find($jadwalMengajarId);
			if (!$jadwal) {
				throw new \Exception('Jadwal mengajar tidak ditemukan.');
			}

			// Create upload directory if not exists
			$uploadPath = FCPATH . 'uploads/rubrik/';
			if (!is_dir($uploadPath)) {
				mkdir($uploadPath, 0755, true);
			}

			// Delete old file if exists
			if (!empty($jadwal['rubrik_penilaian_file'])) {
				$oldFilePath = $uploadPath . $jadwal['rubrik_penilaian_file'];
				if (file_exists($oldFilePath)) {
					@unlink($oldFilePath);
				}
			}

			// Generate unique filename
			$extension = $file->getExtension();
			$filename = 'rubrik_' . $jadwalMengajarId . '_' . time() . '.' . $extension;

			// Move file to upload directory
			$file->move($uploadPath, $filename);

			// Update database
			$this->jadwalMengajarModel->update($jadwalMengajarId, [
				'rubrik_penilaian_file' => $filename
			]);

			return $this->response->setJSON([
				'success' => true,
				'message' => 'Rubrik penilaian berhasil diunggah.',
				'filename' => $filename
			]);
		} catch (\Exception $e) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Gagal mengunggah file: ' . $e->getMessage()
			])->setStatusCode(500);
		}
	}

	public function deleteRubrik()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Akses ditolak.'
			])->setStatusCode(403);
		}

		$jadwalMengajarId = $this->request->getPost('jadwal_id');

		if (!$jadwalMengajarId) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Data tidak lengkap.'
			])->setStatusCode(400);
		}

		try {
			// Get jadwal mengajar data
			$jadwal = $this->jadwalMengajarModel->find($jadwalMengajarId);
			if (!$jadwal) {
				throw new \Exception('Jadwal mengajar tidak ditemukan.');
			}

			// Delete file if exists
			if (!empty($jadwal['rubrik_penilaian_file'])) {
				$uploadPath = FCPATH . 'uploads/rubrik/';
				$filePath = $uploadPath . $jadwal['rubrik_penilaian_file'];
				if (file_exists($filePath)) {
					@unlink($filePath);
				}
			}

			// Update database
			$this->jadwalMengajarModel->update($jadwalMengajarId, [
				'rubrik_penilaian_file' => null
			]);

			return $this->response->setJSON([
				'success' => true,
				'message' => 'Rubrik penilaian berhasil dihapus.'
			]);
		} catch (\Exception $e) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Gagal menghapus file: ' . $e->getMessage()
			])->setStatusCode(500);
		}
	}

	public function uploadContohSoal()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Akses ditolak.'
			])->setStatusCode(403);
		}

		$jadwalMengajarId = $this->request->getPost('jadwal_id');
		$file = $this->request->getFile('contoh_soal_file');

		if (!$jadwalMengajarId || !$file) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Data tidak lengkap.'
			])->setStatusCode(400);
		}

		try {
			// Validate file
			if (!$file->isValid()) {
				throw new \Exception('File tidak valid.');
			}

			// Validate file type
			$allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
			if (!in_array($file->getMimeType(), $allowedTypes)) {
				throw new \Exception('File harus berformat PDF, DOC, atau DOCX.');
			}

			// Validate file size (max 5MB)
			if ($file->getSize() > 5 * 1024 * 1024) {
				throw new \Exception('Ukuran file maksimal 5MB.');
			}

			// Get jadwal mengajar data
			$jadwal = $this->jadwalMengajarModel->find($jadwalMengajarId);
			if (!$jadwal) {
				throw new \Exception('Jadwal mengajar tidak ditemukan.');
			}

			// Create upload directory if not exists
			$uploadPath = FCPATH . 'uploads/contoh_soal/';
			if (!is_dir($uploadPath)) {
				mkdir($uploadPath, 0755, true);
			}

			// Delete old file if exists
			if (!empty($jadwal['contoh_soal_file'])) {
				$oldFilePath = $uploadPath . $jadwal['contoh_soal_file'];
				if (file_exists($oldFilePath)) {
					@unlink($oldFilePath);
				}
			}

			// Generate unique filename
			$extension = $file->getExtension();
			$filename = 'contoh_soal_' . $jadwalMengajarId . '_' . time() . '.' . $extension;

			// Move file to upload directory
			$file->move($uploadPath, $filename);

			// Update database
			$this->jadwalMengajarModel->update($jadwalMengajarId, [
				'contoh_soal_file' => $filename
			]);

			return $this->response->setJSON([
				'success' => true,
				'message' => 'Contoh soal berhasil diunggah.',
				'filename' => $filename
			]);
		} catch (\Exception $e) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Gagal mengunggah file: ' . $e->getMessage()
			])->setStatusCode(500);
		}
	}

	public function deleteContohSoal()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Akses ditolak.'
			])->setStatusCode(403);
		}

		$jadwalMengajarId = $this->request->getPost('jadwal_id');

		if (!$jadwalMengajarId) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Data tidak lengkap.'
			])->setStatusCode(400);
		}

		try {
			// Get jadwal mengajar data
			$jadwal = $this->jadwalMengajarModel->find($jadwalMengajarId);
			if (!$jadwal) {
				throw new \Exception('Jadwal mengajar tidak ditemukan.');
			}

			// Delete file if exists
			if (!empty($jadwal['contoh_soal_file'])) {
				$uploadPath = FCPATH . 'uploads/contoh_soal/';
				$filePath = $uploadPath . $jadwal['contoh_soal_file'];
				if (file_exists($filePath)) {
					@unlink($filePath);
				}
			}

			// Update database
			$this->jadwalMengajarModel->update($jadwalMengajarId, [
				'contoh_soal_file' => null
			]);

			return $this->response->setJSON([
				'success' => true,
				'message' => 'Contoh soal berhasil dihapus.'
			]);
		} catch (\Exception $e) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Gagal menghapus file: ' . $e->getMessage()
			])->setStatusCode(500);
		}
	}

	public function uploadNotulen()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Akses ditolak.'
			])->setStatusCode(403);
		}

		$jadwalMengajarId = $this->request->getPost('jadwal_id');
		$file = $this->request->getFile('notulen_file');

		if (!$jadwalMengajarId || !$file) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Data tidak lengkap.'
			])->setStatusCode(400);
		}

		try {
			// Validate file
			if (!$file->isValid()) {
				throw new \Exception('File tidak valid.');
			}

			// Validate file type
			$allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
			if (!in_array($file->getMimeType(), $allowedTypes)) {
				throw new \Exception('File harus berformat PDF, DOC, atau DOCX.');
			}

			// Validate file size (max 5MB)
			if ($file->getSize() > 5 * 1024 * 1024) {
				throw new \Exception('Ukuran file maksimal 5MB.');
			}

			// Get jadwal mengajar data
			$jadwal = $this->jadwalMengajarModel->find($jadwalMengajarId);
			if (!$jadwal) {
				throw new \Exception('Jadwal mengajar tidak ditemukan.');
			}

			// Create upload directory if not exists
			$uploadPath = FCPATH . 'uploads/notulen/';
			if (!is_dir($uploadPath)) {
				mkdir($uploadPath, 0755, true);
			}

			// Delete old file if exists
			if (!empty($jadwal['notulen_rapat_file'])) {
				$oldFilePath = $uploadPath . $jadwal['notulen_rapat_file'];
				if (file_exists($oldFilePath)) {
					@unlink($oldFilePath);
				}
			}

			// Generate unique filename
			$extension = $file->getExtension();
			$filename = 'notulen_' . $jadwalMengajarId . '_' . time() . '.' . $extension;

			// Move file to upload directory
			$file->move($uploadPath, $filename);

			// Update database
			$this->jadwalMengajarModel->update($jadwalMengajarId, [
				'notulen_rapat_file' => $filename
			]);

			return $this->response->setJSON([
				'success' => true,
				'message' => 'Notulen rapat berhasil diunggah.',
				'filename' => $filename
			]);
		} catch (\Exception $e) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Gagal mengunggah file: ' . $e->getMessage()
			])->setStatusCode(500);
		}
	}

	public function deleteNotulen()
	{
		// Check user role
		if (!in_array(session()->get('role'), ['admin', 'dosen'])) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Akses ditolak.'
			])->setStatusCode(403);
		}

		$jadwalMengajarId = $this->request->getPost('jadwal_id');

		if (!$jadwalMengajarId) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Data tidak lengkap.'
			])->setStatusCode(400);
		}

		try {
			// Get jadwal mengajar data
			$jadwal = $this->jadwalMengajarModel->find($jadwalMengajarId);
			if (!$jadwal) {
				throw new \Exception('Jadwal mengajar tidak ditemukan.');
			}

			// Delete file if exists
			if (!empty($jadwal['notulen_rapat_file'])) {
				$uploadPath = FCPATH . 'uploads/notulen/';
				$filePath = $uploadPath . $jadwal['notulen_rapat_file'];
				if (file_exists($filePath)) {
					@unlink($filePath);
				}
			}

			// Update database
			$this->jadwalMengajarModel->update($jadwalMengajarId, [
				'notulen_rapat_file' => null
			]);

			return $this->response->setJSON([
				'success' => true,
				'message' => 'Notulen rapat berhasil dihapus.'
			]);
		} catch (\Exception $e) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Gagal menghapus file: ' . $e->getMessage()
			])->setStatusCode(500);
		}
	}
}

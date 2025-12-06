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

	public function __construct()
	{
		$this->db = \Config\Database::connect();
		$this->mataKuliahModel = new \App\Models\MataKuliahModel();
		$this->cpmkModel = new \App\Models\CpmkModel();
		$this->jadwalMengajarModel = new \App\Models\MengajarModel();
		$this->nilaiCpmkMahasiswaModel = new \App\Models\NilaiCpmkMahasiswaModel();
		$this->standarMinimalCapaianModel = new \App\Models\StandarMinimalCapaianModel();
		$this->analysisCpmkModel = new \App\Models\AnalysisCpmkModel();
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

	private function getPortfolioData($mataKuliahId, $tahunAkademik, $programStudi = null)
	{
		// 1. Get Course Identity (Identitas Mata Kuliah)
		$mataKuliah = $this->mataKuliahModel->find($mataKuliahId);
		if (!$mataKuliah) {
			return null;
		}

		// Get teaching schedule
		$builder = $this->db->table('jadwal_mengajar');
		$builder->where('mata_kuliah_id', $mataKuliahId);
		$builder->where('tahun_akademik', $tahunAkademik);
		if ($programStudi) {
			$builder->where('program_studi', $programStudi);
		}
		$jadwalMengajar = $builder->get()->getResultArray();

		if (empty($jadwalMengajar)) {
			return null;
		}

		// Get lecturers (dosen pengampu)
		$jadwalIds = array_column($jadwalMengajar, 'id');
		$dosenPengampu = $this->db->table('jadwal_dosen jd')
			->select('d.nama_lengkap, d.nip, jd.role')
			->join('dosen d', 'd.id = jd.dosen_id')
			->whereIn('jd.jadwal_mengajar_id', $jadwalIds)
			->get()
			->getResultArray();

		// 2. Get CPMK data with CPL relations
		$cpmkData = $this->getCpmkData($mataKuliahId);

		// 3. Get Assessment Data (Rencana dan Realisasi Penilaian)
		$assessmentData = $this->getAssessmentData($mataKuliahId, $jadwalIds);

		// 4. Get Analysis Data (Analisis Pencapaian)
		$passingThreshold = $this->standarMinimalCapaianModel->getPersentase();
		$analysis = $this->getAnalysisData($assessmentData, $passingThreshold, $mataKuliahId, $tahunAkademik, $programStudi);

		return [
			'identitas' => [
				'nama_mata_kuliah' => $mataKuliah['nama_mk'],
				'kode_mata_kuliah' => $mataKuliah['kode_mk'],
				'program_studi' => $jadwalMengajar[0]['program_studi'] ?? '-',
				'semester' => $mataKuliah['semester'],
				'jumlah_sks' => $mataKuliah['sks'],
				'tahun_akademik' => $tahunAkademik,
				'dosen_pengampu' => $dosenPengampu
			],
			'cpmk' => $cpmkData,
			'assessment' => $assessmentData,
			'analysis' => $analysis,
			'passing_threshold' => $passingThreshold,
			'mata_kuliah_id' => $mataKuliahId
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

		foreach ($cpmkList as $cpmk) {
			// Get all rps_mingguan entries for this CPMK
			$rpsMingguan = $this->db->table('rps_mingguan rm')
				->select('rm.id, rm.bobot, rm.teknik_penilaian')
				->join('rps r', 'r.id = rm.rps_id')
				->where('r.mata_kuliah_id', $mataKuliahId)
				->where('rm.cpmk_id', $cpmk['cpmk_id'])
				->get()
				->getResultArray();

			$totalScore = 0;
			$totalCount = 0;
			$jumlahMahasiswa = 0;

			// Get all rps_mingguan IDs for this CPMK
			$rpsMingguan_ids = array_column($rpsMingguan, 'id');

			if (!empty($rpsMingguan_ids)) {
				// Get sum and count of all input scores from teknik penilaian for this CPMK
				$query = "
                    SELECT
                        SUM(ntp.nilai) as total_nilai,
                        COUNT(ntp.nilai) as jumlah_nilai,
                        COUNT(DISTINCT ntp.mahasiswa_id) as jml_mhs
                    FROM nilai_teknik_penilaian ntp
                    WHERE ntp.rps_mingguan_id IN (" . implode(',', array_fill(0, count($rpsMingguan_ids), '?')) . ")
                    AND ntp.jadwal_mengajar_id IN (" . implode(',', array_fill(0, count($jadwalIds), '?')) . ")
                    AND ntp.nilai IS NOT NULL
                ";

				$params = array_merge($rpsMingguan_ids, $jadwalIds);
				$result = $this->db->query($query, $params)->getRowArray();

				if ($result && $result['total_nilai'] !== null) {
					$totalScore = (float)$result['total_nilai'];
					$totalCount = (int)$result['jumlah_nilai'];
					$jumlahMahasiswa = (int)$result['jml_mhs'];
				}
			}

			// Formula: nilai_rata_rata = total of input score / total count of input score
			$nilaiRataRata = $totalCount > 0 ? round($totalScore / $totalCount, 2) : 0;

			// Build bobot mapping for each rps_mingguan
			$bobotMapping = [];
			foreach ($rpsMingguan as $rps) {
				$bobot = 0;

				// First try to get bobot from the direct column
				if (!empty($rps['bobot'])) {
					$bobot = (int)$rps['bobot'];
				}
				// Otherwise try from teknik_penilaian JSON
				elseif (!empty($rps['teknik_penilaian'])) {
					$teknik = json_decode($rps['teknik_penilaian'], true);
					if (is_array($teknik)) {
						foreach ($teknik as $t) {
							if (isset($t['bobot'])) {
								$bobot += (int)$t['bobot'];
							}
						}
					}
				}

				// Default to 1 if no bobot found
				$bobotMapping[$rps['id']] = $bobot > 0 ? $bobot : 1;
			}

			// Get total bobot (total weight) for this CPMK
			$totalBobot = array_sum($bobotMapping);

			// Formula: persentase_capaian = ∑(input score * weight) / total weight * 100
			$persentaseCapaian = 0;
			if (!empty($rpsMingguan_ids)) {
				// Get all scores with their rps_mingguan_id to apply weights
				$queryScores = "
                    SELECT
                        ntp.nilai,
                        ntp.rps_mingguan_id
                    FROM nilai_teknik_penilaian ntp
                    WHERE ntp.rps_mingguan_id IN (" . implode(',', array_fill(0, count($rpsMingguan_ids), '?')) . ")
                    AND ntp.jadwal_mengajar_id IN (" . implode(',', array_fill(0, count($jadwalIds), '?')) . ")
                    AND ntp.nilai IS NOT NULL
                ";

				$params = array_merge($rpsMingguan_ids, $jadwalIds);
				$scores = $this->db->query($queryScores, $params)->getResultArray();

				// Calculate weighted sum and total weight
				$weightedSum = 0;
				$totalWeight = 0;

				foreach ($scores as $score) {
					$nilai = (float)$score['nilai'];
					$weight = $bobotMapping[$score['rps_mingguan_id']] ?? 1;

					$weightedSum += $nilai * $weight;
					$totalWeight += $weight;
				}

				// Calculate persentase capaian
				if ($totalWeight > 0) {
					$persentaseCapaian = round(($weightedSum / $totalWeight), 2);
				}
			}

			// Use the already calculated totalBobot
			$bobot = $totalBobot;

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


	private function getCpmkBobot($mataKuliahId, $cpmkId)
	{
		// Try to get bobot from RPS mingguan
		$query = "
            SELECT rm.bobot, rm.teknik_penilaian
            FROM rps_mingguan rm
            INNER JOIN rps r ON rm.rps_id = r.id
            WHERE r.mata_kuliah_id = ?
            AND rm.cpmk_id = ?
        ";

		$result = $this->db->query($query, [$mataKuliahId, $cpmkId])->getResultArray();

		// Calculate total weight from all weeks
		$totalBobot = 0;
		foreach ($result as $row) {
			// First try to get bobot from the direct column
			if (!empty($row['bobot'])) {
				$totalBobot += (int)$row['bobot'];
			}
			// Otherwise try from teknik_penilaian JSON
			elseif (!empty($row['teknik_penilaian'])) {
				$teknik = json_decode($row['teknik_penilaian'], true);
				if (is_array($teknik)) {
					foreach ($teknik as $t) {
						if (isset($t['bobot'])) {
							$totalBobot += (int)$t['bobot'];
						}
					}
				}
			}
		}

		// If no bobot found, return default equal weight
		if ($totalBobot == 0) {
			$cpmkCount = $this->db->table('cpmk_mk')
				->where('mata_kuliah_id', $mataKuliahId)
				->countAllResults();

			return $cpmkCount > 0 ? round(100 / $cpmkCount) : 0;
		}

		return $totalBobot;
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
			$persentaseCapaian = $assessment['nilai_rata_rata'];

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
		$analysisSingkat = '';

		if ($mode === 'manual' && !empty($savedAnalysis['analisis_singkat'])) {
			// Use manual analysis
			$analysisSingkat = $savedAnalysis['analisis_singkat'];
		} else {
			// Use auto-generated analysis
			$analysisSingkat = $this->generateAnalysisSingkat($cpmkTidakTercapai);
		}

		return [
			'standar_minimal' => $passingThreshold,
			'cpmk_tercapai' => $cpmkTercapai,
			'cpmk_tidak_tercapai' => $cpmkTidakTercapai,
			'analisis_singkat' => $analysisSingkat,
			'mode' => $mode
		];
	}

	private function generateAnalysisSingkat($cpmkTidakTercapai)
	{
		if (empty($cpmkTidakTercapai)) {
			return 'Semua CPMK tercapai dengan baik. Mahasiswa menunjukkan pemahaman yang memadai terhadap materi pembelajaran.';
		}

		$jumlahTidakTercapai = count($cpmkTidakTercapai);
		$cpmkList = implode(', ', $cpmkTidakTercapai);

		return "Terdapat $jumlahTidakTercapai CPMK yang belum tercapai ($cpmkList). Mahasiswa mengalami kesulitan dalam memahami dan menerapkan konsep-konsep tersebut. Diperlukan evaluasi lebih lanjut terhadap metode pengajaran dan materi pembelajaran.";
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
		return [
			['program_studi' => 'Teknik Informatika'],
			['program_studi' => 'Sistem Informasi'],
			['program_studi' => 'Teknik Komputer']
		];
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
		$programStudi = $this->request->getPost('program_studi');
		$mode = $this->request->getPost('mode');
		$analysisSingkat = $this->request->getPost('analisis_singkat');

		if (!$mataKuliahId || !$tahunAkademik || !$mode) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Data tidak lengkap.'
			])->setStatusCode(400);
		}

		$data = [
			'mata_kuliah_id' => $mataKuliahId,
			'tahun_akademik' => $tahunAkademik,
			'program_studi' => $programStudi,
			'mode' => $mode,
			'analisis_singkat' => $mode === 'manual' ? $analysisSingkat : null
		];

		try {
			$this->analysisCpmkModel->saveAnalysis($data);

			return $this->response->setJSON([
				'success' => true,
				'message' => 'Analisis berhasil disimpan.'
			]);
		} catch (\Exception $e) {
			return $this->response->setJSON([
				'success' => false,
				'message' => 'Gagal menyimpan analisis: ' . $e->getMessage()
			])->setStatusCode(500);
		}
	}
}

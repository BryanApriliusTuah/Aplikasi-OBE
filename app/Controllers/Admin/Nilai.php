<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MengajarModel;
use App\Models\MahasiswaModel;
use App\Models\CpmkModel;
use App\Models\NilaiMahasiswaModel;
use App\Models\NilaiCpmkMahasiswaModel;
use App\Models\DosenModel;

class Nilai extends BaseController
{
	public function index()
	{
		$jadwalModel = new MengajarModel();
		$filters = [
			'program_studi' => $this->request->getGet('program_studi'),
			'tahun_akademik' => $this->request->getGet('tahun_akademik'),
		];

		// Fetch schedules with related data
		$schedules = $jadwalModel->getJadwalWithDetails($filters);

		// Group schedules by day
		$jadwal_by_day = [
			'Senin' => [],
			'Selasa' => [],
			'Rabu' => [],
			'Kamis' => [],
			'Jumat' => [],
			'Sabtu' => []
		];

		// Get current user's dosen_id if they are a lecturer
		$currentDosenId = null;
		if (session()->get('role') === 'dosen') {
			$dosenModel = new DosenModel();
			$currentDosen = $dosenModel->where('user_id', session()->get('user_id'))->first();
			$currentDosenId = $currentDosen ? $currentDosen['id'] : null;
		}

		foreach ($schedules as $schedule) {
			if (isset($jadwal_by_day[$schedule['hari']])) {
				// Check if current user can input grades for this schedule
				$canInputGrades = $this->canInputGrades($schedule['id'], $currentDosenId);
				$schedule['can_input_grades'] = $canInputGrades;

				$jadwal_by_day[$schedule['hari']][] = $schedule;
			}
		}

		$data = [
			'title' => 'Penilaian Jadwal Ajar',
			'jadwal_by_day' => $jadwal_by_day,
			'filters' => $filters,
			'current_dosen_id' => $currentDosenId,
		];

		// dd($data);

		return view('admin/nilai/index', $data);
	}

	/**
	 * Check if the current user can input grades for a specific schedule
	 */
	private function canInputGrades($jadwal_id, $current_dosen_id)
	{
		// Admin can always input grades
		if (session()->get('role') === 'admin') {
			return true;
		}

		// If not a lecturer or no dosen_id, cannot input grades
		if (session()->get('role') !== 'dosen' || !$current_dosen_id) {
			return false;
		}

		// Check if the current lecturer is assigned to this schedule
		$db = \Config\Database::connect();
		$builder = $db->table('jadwal_dosen');
		$result = $builder->where([
			'jadwal_mengajar_id' => $jadwal_id,
			'dosen_id' => $current_dosen_id
		])->get()->getRowArray();

		return !empty($result);
	}

	/**
	 * AJAX endpoint to get student scores per CPMK for a specific class.
	 */
	public function getDetailNilaiCpmk($jadwal_id)
	{
		if (!$this->request->isAJAX()) {
			return $this->response->setStatusCode(403);
		}

		$nilaiCpmkModel = new NilaiCpmkMahasiswaModel();

		$data = [
			'jadwal' => (new MengajarModel())->find($jadwal_id),
			'cpmk_list' => (new CpmkModel())->getCpmkByJadwal($jadwal_id),
			'scores' => $nilaiCpmkModel->getScoresByJadwal($jadwal_id),
		];

		return $this->response->setJSON($data);
	}

	/**
	 * AJAX endpoint to get the final student scores (DPNA).
	 */
	public function getDpna($jadwal_id)
	{
		if (!$this->request->isAJAX()) {
			return $this->response->setStatusCode(403);
		}

		$nilaiModel = new NilaiMahasiswaModel();

		$data = [
			'jadwal' => (new MengajarModel())->getJadwalWithDetails(['id' => $jadwal_id], true),
			'nilai_mahasiswa' => $nilaiModel->getFinalScoresByJadwal($jadwal_id)
		];

		return $this->response->setJSON($data);
	}

	/**
	 * Display the form to input scores for all students in a class.
	 */
	public function inputNilai($jadwal_id)
	{
		// Check access permissions first
		$currentDosenId = null;
		if (session()->get('role') === 'dosen') {
			$dosenModel = new DosenModel();
			$currentDosen = $dosenModel->where('user_id', session()->get('user_id'))->first();
			$currentDosenId = $currentDosen ? $currentDosen['id'] : null;
		}

		if (!$this->canInputGrades($jadwal_id, $currentDosenId)) {
			return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menginput nilai pada jadwal ini. Hanya dosen pengampu yang dapat menginput nilai.');
		}

		$jadwalModel = new MengajarModel();
		$mahasiswaModel = new MahasiswaModel();
		$cpmkModel = new CpmkModel();
		$nilaiCpmkModel = new NilaiCpmkMahasiswaModel();

		$jadwal = $jadwalModel->getJadwalWithDetails(['id' => $jadwal_id], true);
		if (!$jadwal) {
			return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
		}

		// Get students for this class
		$students = $mahasiswaModel->getStudentsForScoring($jadwal['program_studi'], $jadwal['semester']);

		// Get CPMK list with weights from rps_mingguan
		$cpmk_list = $cpmkModel->getCpmkByJadwal($jadwal_id);

		// Get existing scores to pre-fill the form
		$existing_scores = $nilaiCpmkModel->getScoresByJadwalForInput($jadwal_id);

		// Calculate total weight
		$total_weight = 0;
		foreach ($cpmk_list as $cpmk) {
			$total_weight += (float) $cpmk['bobot_cpmk'];
		}

		$data = [
			'title' => 'Input Nilai',
			'jadwal' => $jadwal,
			'mahasiswa_list' => $students,
			'cpmk_list' => $cpmk_list,
			'existing_scores' => $existing_scores,
			'total_weight' => $total_weight,
		];

		return view('admin/nilai/input_nilai', $data);
	}

	/**
	 * Process and save the bulk score submission.
	 */
	public function saveNilai($jadwal_id)
	{
		// Check access permissions first
		$currentDosenId = null;
		if (session()->get('role') === 'dosen') {
			$dosenModel = new DosenModel();
			$currentDosen = $dosenModel->where('user_id', session()->get('user_id'))->first();
			$currentDosenId = $currentDosen ? $currentDosen['id'] : null;
		}

		if (!$this->canInputGrades($jadwal_id, $currentDosenId)) {
			return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menyimpan nilai pada jadwal ini.');
		}

		$nilai_data = $this->request->getPost('nilai');

		if (empty($nilai_data)) {
			return redirect()->back()->with('error', 'Tidak ada data nilai yang dikirim.');
		}

		$nilaiMahasiswaModel = new NilaiMahasiswaModel();
		$nilaiCpmkMahasiswaModel = new NilaiCpmkMahasiswaModel();
		$cpmkModel = new CpmkModel();

		$db = \Config\Database::connect();

		// Get CPMK weights from rps_mingguan for this jadwal
		$cpmk_list = $cpmkModel->getCpmkByJadwal($jadwal_id);
		$cpmk_weights = [];
		$total_weight = 0;

		foreach ($cpmk_list as $cpmk) {
			$cpmk_weights[$cpmk['id']] = (float) $cpmk['bobot_cpmk'];
			$total_weight += (float) $cpmk['bobot_cpmk'];
		}

		// Log warning if weights don't sum to 100
		if (abs($total_weight - 100) > 0.01 && $total_weight > 0) {
			log_message('warning', "Total CPMK weight for jadwal {$jadwal_id} is {$total_weight}, not 100. Using weighted average.");
		}

		// If no weights defined, fall back to simple average
		$use_weighted = ($total_weight > 0);

		foreach ($nilai_data as $mahasiswa_id => $cpmk_scores) {
			$db->transStart();

			// 1. Save individual CPMK scores
			foreach ($cpmk_scores as $cpmk_id => $score) {
				$cpmkData = [
					'mahasiswa_id' => $mahasiswa_id,
					'jadwal_mengajar_id' => $jadwal_id,
					'cpmk_id' => $cpmk_id,
					'nilai_cpmk' => empty($score) ? null : $score,
				];
				$nilaiCpmkMahasiswaModel->saveOrUpdate($cpmkData);
			}

			// 2. Calculate final score
			$nilai_akhir = 0;

			if ($use_weighted) {
				// Calculate weighted average based on rps_mingguan bobot
				$weighted_sum = 0;
				$used_weight = 0;

				foreach ($cpmk_scores as $cpmk_id => $score) {
					if (is_numeric($score) && isset($cpmk_weights[$cpmk_id]) && $cpmk_weights[$cpmk_id] > 0) {
						$weight = $cpmk_weights[$cpmk_id];
						$weighted_sum += ($score * $weight);
						$used_weight += $weight;
					}
				}

				// Calculate final score
				if ($used_weight > 0) {
					$nilai_akhir = $weighted_sum / $used_weight;
				}
			} else {
				// Fallback: simple average if no weights defined
				$valid_scores = array_filter($cpmk_scores, function ($val) {
					return is_numeric($val);
				});

				if (!empty($valid_scores)) {
					$nilai_akhir = array_sum($valid_scores) / count($valid_scores);
				}
			}

			$finalData = [
				'mahasiswa_id' => $mahasiswa_id,
				'jadwal_mengajar_id' => $jadwal_id,
				'nilai_akhir' => round($nilai_akhir, 2),
				'nilai_huruf' => $this->calculateGrade($nilai_akhir),
				'status_kelulusan' => $nilai_akhir > 50 ? 'Lulus' : 'Tidak Lulus',
			];
			$nilaiMahasiswaModel->saveOrUpdate($finalData);

			$db->transComplete();
		}

		if ($db->transStatus() === false) {
			return redirect()->to('admin/nilai')->with('error', 'Gagal menyimpan nilai.');
		}

		return redirect()->to('admin/nilai')->with('success', 'Nilai mahasiswa berhasil disimpan.');
	}

	private function calculateGrade($score)
	{
		// Based on OBE/KKNI/SKKNI APTIKOM Guideline Rubric
		if ($score > 80) return 'A';      // Istimewa
		if ($score > 70) return 'AB';     // Baik Sekali
		if ($score > 65) return 'B';      // Baik
		if ($score > 60) return 'BC';     // Cukup Baik
		if ($score > 50) return 'C';      // Cukup
		if ($score > 40) return 'D';      // Kurang
		return 'E';                       // Sangat Kurang
	}
}

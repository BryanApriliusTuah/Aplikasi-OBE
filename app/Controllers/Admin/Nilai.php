<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MengajarModel;
use App\Models\MahasiswaModel;
use App\Models\CpmkModel;
use App\Models\NilaiMahasiswaModel;
use App\Models\NilaiCpmkMahasiswaModel;
use App\Models\DosenModel;
use App\Models\NilaiTeknikPenilaianModel;

class Nilai extends BaseController
{
	public function index()
	{
		$jadwalModel = new MengajarModel();
		$filters = [
			'program_studi' => $this->request->getGet('program_studi') ?? 'Teknik Informatika',
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

		// Get validation status and score completion for all schedules at once
		$jadwal_ids = array_column($schedules, 'id');
		$validation_status = [];
		$score_completion = [];

		if (!empty($jadwal_ids)) {
			$db = \Config\Database::connect();
			$validation_results = $db->table('jadwal_mengajar')
				->select('id, is_nilai_validated')
				->whereIn('id', $jadwal_ids)
				->get()
				->getResultArray();

			foreach ($validation_results as $result) {
				$validation_status[$result['id']] = $result['is_nilai_validated'];
			}

			// Calculate score completion for each jadwal
			foreach ($jadwal_ids as $jadwal_id) {
				// Get total students for this jadwal
				$jadwal_info = array_values(array_filter($schedules, function ($s) use ($jadwal_id) {
					return $s['id'] == $jadwal_id;
				}))[0] ?? null;

				if (!$jadwal_info) continue;

				$mahasiswaModel = new MahasiswaModel();
				$students = $mahasiswaModel->getStudentsForScoring($jadwal_info['program_studi'], $jadwal_info['semester']);
				$total_students = count($students);

				if ($total_students == 0) {
					$score_completion[$jadwal_id] = ['completed' => 0, 'total' => 0];
					continue;
				}

				// Get combined teknik penilaian for this jadwal
				$nilaiTeknikModel = new NilaiTeknikPenilaianModel();
				$combined_data = $nilaiTeknikModel->getCombinedTeknikPenilaianByJadwal($jadwal_id);

				if (empty($combined_data['combined_list'])) {
					$score_completion[$jadwal_id] = ['completed' => 0, 'total' => $total_students];
					continue;
				}

				$teknik_keys = array_column($combined_data['combined_list'], 'teknik_key');
				$total_teknik = count($teknik_keys);

				// Count students with complete scores
				$completed_students = 0;
				foreach ($students as $student) {
					// Check if this student has all teknik scores
					$student_scores = $db->table('nilai_teknik_penilaian')
						->select('teknik_penilaian_key, nilai')
						->where('mahasiswa_id', $student['id'])
						->where('jadwal_mengajar_id', $jadwal_id)
						->whereIn('teknik_penilaian_key', $teknik_keys)
						->where('nilai IS NOT NULL')
						->get()
						->getResultArray();

					// Student is complete if they have scores for all teknik
					if (count($student_scores) >= $total_teknik) {
						$completed_students++;
					}
				}

				$score_completion[$jadwal_id] = ['completed' => $completed_students, 'total' => $total_students];
			}
		}

		foreach ($schedules as $schedule) {
			if (isset($jadwal_by_day[$schedule['hari']])) {
				// Check if current user can input grades for this schedule
				$canInputGrades = $this->canInputGrades($schedule['id'], $currentDosenId);
				$schedule['can_input_grades'] = $canInputGrades;

				// Add validation status
				$schedule['is_nilai_validated'] = $validation_status[$schedule['id']] ?? 0;

				// Add score completion
				$schedule['score_completion'] = $score_completion[$schedule['id']] ?? ['completed' => 0, 'total' => 0];

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
				'status_kelulusan' => $this->calculatePassingStatus($nilai_akhir),
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
		// Use dynamic grade configuration from database
		$gradeConfigModel = new \App\Models\GradeConfigModel();
		$gradeLetter = $gradeConfigModel->getGradeLetter($score);

		// Fallback to hardcoded values if no configuration found in database
		if (!$gradeLetter) {
			// Based on OBE/KKNI/SKKNI APTIKOM Guideline Rubric
			if ($score > 80) return 'A';      // Istimewa
			if ($score > 70) return 'AB';     // Baik Sekali
			if ($score > 65) return 'B';      // Baik
			if ($score > 60) return 'BC';     // Cukup Baik
			if ($score > 50) return 'C';      // Cukup
			if ($score > 40) return 'D';      // Kurang
			return 'E';                       // Sangat Kurang
		}

		return $gradeLetter;
	}

	private function calculatePassingStatus($score)
	{
		// Use dynamic grade configuration from database
		$gradeConfigModel = new \App\Models\GradeConfigModel();
		$isPassing = $gradeConfigModel->isPassing($score);

		// Fallback to hardcoded value if no configuration found in database
		if ($isPassing === null) {
			// Default passing score is > 50 (grade C and above)
			$isPassing = $score > 50;
		}

		return $isPassing ? 'Lulus' : 'Tidak Lulus';
	}

	/**
	 * Display the form to input scores by teknik_penilaian (new method)
	 */
	public function inputNilaiByTeknikPenilaian($jadwal_id)
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
		$nilaiTeknikModel = new NilaiTeknikPenilaianModel();

		$jadwal = $jadwalModel->getJadwalWithDetails(['id' => $jadwal_id], true);
		if (!$jadwal) {
			return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
		}

		// Get validation status directly from jadwal_mengajar table (not from view)
		$jadwalValidation = $jadwalModel->find($jadwal_id);
		if ($jadwalValidation) {
			$jadwal['is_nilai_validated'] = $jadwalValidation['is_nilai_validated'];
			$jadwal['validated_at'] = $jadwalValidation['validated_at'];
			$jadwal['validated_by'] = $jadwalValidation['validated_by'];

			// Get validator user information
			if ($jadwalValidation['validated_by']) {
				$db = \Config\Database::connect();
				$validator = $db->table('users')
					->select('users.username, users.role')
					->where('users.id', $jadwalValidation['validated_by'])
					->get()
					->getRowArray();

				if ($validator) {
					// Get full name based on role
					if ($validator['role'] === 'dosen') {
						$dosen = $db->table('dosen')
							->select('nama_lengkap')
							->where('user_id', $jadwalValidation['validated_by'])
							->get()
							->getRowArray();
						$jadwal['validated_by_name'] = $dosen ? $dosen['nama_lengkap'] : $validator['username'];
					} else {
						// For admin or other roles, just use username
						$jadwal['validated_by_name'] = $validator['username'];
					}
					$jadwal['validator_role'] = $validator['role'];
				}
			}
		}

		// Get students for this class
		$students = $mahasiswaModel->getStudentsForScoring($jadwal['program_studi'], $jadwal['semester']);

		// Get COMBINED teknik_penilaian list (grouped by type, not by week)
		$combined_data = $nilaiTeknikModel->getCombinedTeknikPenilaianByJadwal($jadwal_id);

		if (empty($combined_data['combined_list'])) {
			return redirect()->back()->with('error', 'Tidak ada teknik penilaian yang terdefinisi pada RPS untuk mata kuliah ini. Silakan lengkapi RPS terlebih dahulu.');
		}

		// Get existing scores to pre-fill the form (combined view)
		$existing_scores = $nilaiTeknikModel->getCombinedScoresForInput($jadwal_id);

		// Get dynamic grade configuration from database
		$gradeConfigModel = new \App\Models\GradeConfigModel();
		$grades = $gradeConfigModel->getActiveGrades();

		$data = [
			'title' => 'Input Nilai Berdasarkan Teknik Penilaian',
			'jadwal' => $jadwal,
			'mahasiswa_list' => $students,
			'combined_list' => $combined_data['combined_list'],
			'teknik_by_tahap' => $combined_data['by_tahap'],
			'existing_scores' => $existing_scores,
			'grade_config' => $grades,
		];

		return view('admin/nilai/input_nilai_teknik', $data);
	}

	/**
	 * Save scores by teknik_penilaian (combined mode) and auto-calculate CPMK scores
	 */
	public function saveNilaiByTeknikPenilaian($jadwal_id)
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

		// Check if nilai is already validated (only dosen is blocked, admin can still edit)
		$jadwalModel = new MengajarModel();
		$jadwal = $jadwalModel->find($jadwal_id);

		if ($jadwal && $jadwal['is_nilai_validated'] == 1 && session()->get('role') === 'dosen') {
			return redirect()->back()->with('error', 'Nilai sudah divalidasi. Anda tidak dapat lagi mengedit nilai. Hubungi admin jika perlu mengubah nilai.');
		}

		$nilai_data = $this->request->getPost('nilai');

		if (empty($nilai_data)) {
			return redirect()->back()->with('error', 'Tidak ada data nilai yang dikirim.');
		}

		$nilaiTeknikModel = new NilaiTeknikPenilaianModel();
		$nilaiCpmkModel = new NilaiCpmkMahasiswaModel();
		$nilaiMahasiswaModel = new NilaiMahasiswaModel();
		$cpmkModel = new CpmkModel();

		$db = \Config\Database::connect();
		$db->transStart();

		// Get combined data to map teknik_key to all rps_mingguan_ids
		$combined_data = $nilaiTeknikModel->getCombinedTeknikPenilaianByJadwal($jadwal_id);
		$teknik_mapping = [];

		// Build mapping: teknik_key => [rps_mingguan_id, ...]
		foreach ($combined_data['combined_list'] as $item) {
			$teknik_mapping[$item['teknik_key']] = $item['rps_mingguan_ids'];
		}

		// 1. Save teknik_penilaian scores (distribute to all matching rps_mingguan_ids)
		foreach ($nilai_data as $mahasiswa_id => $teknik_scores) {
			foreach ($teknik_scores as $teknik_key => $score) {
				// Get all rps_mingguan_ids that use this teknik_key
				if (isset($teknik_mapping[$teknik_key])) {
					foreach ($teknik_mapping[$teknik_key] as $rps_info) {
						$scoreData = [
							'mahasiswa_id' => $mahasiswa_id,
							'jadwal_mengajar_id' => $jadwal_id,
							'rps_mingguan_id' => $rps_info['rps_mingguan_id'],
							'teknik_penilaian_key' => $teknik_key,
							'nilai' => empty($score) ? null : $score,
						];
						$nilaiTeknikModel->saveOrUpdate($scoreData);
					}
				}
			}
		}

		// 2. Calculate CPMK scores from teknik_penilaian scores
		$cpmk_scores = $nilaiTeknikModel->calculateCpmkScores($jadwal_id);

		// 3. Save calculated CPMK scores
		foreach ($cpmk_scores as $mahasiswa_id => $cpmk_data) {
			foreach ($cpmk_data as $cpmk_id => $cpmk_score) {
				$cpmkData = [
					'mahasiswa_id' => $mahasiswa_id,
					'jadwal_mengajar_id' => $jadwal_id,
					'cpmk_id' => $cpmk_id,
					'nilai_cpmk' => $cpmk_score,
				];
				$nilaiCpmkModel->saveOrUpdate($cpmkData);
			}
		}

		// 4. Calculate final scores based on CPMK weights
		foreach ($cpmk_scores as $mahasiswa_id => $cpmk_data) {
			$nilai_akhir = 0;

			// Calculate sum of all CPMK scores
			if (!empty($cpmk_data)) {
				$nilai_akhir = array_sum($cpmk_data);
			}

			$finalData = [
				'mahasiswa_id' => $mahasiswa_id,
				'jadwal_mengajar_id' => $jadwal_id,
				'nilai_akhir' => round($nilai_akhir, 2),
				'nilai_huruf' => $this->calculateGrade($nilai_akhir),
				'status_kelulusan' => $this->calculatePassingStatus($nilai_akhir),
			];
			$nilaiMahasiswaModel->saveOrUpdate($finalData);
		}

		$db->transComplete();

		if ($db->transStatus() === false) {
			return redirect()->to('admin/nilai')->with('error', 'Gagal menyimpan nilai.');
		}

		return redirect()->to('admin/nilai')->with('success', 'Nilai berhasil disimpan. CPMK scores dihitung otomatis berdasarkan teknik penilaian.');
	}

	/**
	 * AJAX endpoint to get detail nilai by teknik_penilaian
	 */
	public function getDetailNilaiTeknikPenilaian($jadwal_id)
	{
		if (!$this->request->isAJAX()) {
			return $this->response->setStatusCode(403);
		}

		$nilaiTeknikModel = new NilaiTeknikPenilaianModel();
		$mahasiswaModel = new MahasiswaModel();

		$jadwalModel = new MengajarModel();
		$jadwal = $jadwalModel->getJadwalWithDetails(['id' => $jadwal_id], true);

		$students = $mahasiswaModel->getStudentsForScoring($jadwal['program_studi'], $jadwal['semester']);
		$teknik_list = $nilaiTeknikModel->getTeknikPenilaianByJadwal($jadwal_id);
		$scores = $nilaiTeknikModel->getScoresByJadwalForInput($jadwal_id);

		// Group by CPMK for display
		$teknik_by_cpmk = [];
		foreach ($teknik_list as $item) {
			$cpmk_id = $item['cpmk_id'];
			if (!isset($teknik_by_cpmk[$cpmk_id])) {
				$teknik_by_cpmk[$cpmk_id] = [
					'kode_cpmk' => $item['kode_cpmk'],
					'cpmk_deskripsi' => $item['cpmk_deskripsi'],
					'items' => []
				];
			}
			$teknik_by_cpmk[$cpmk_id]['items'][] = $item;
		}

		$data = [
			'jadwal' => $jadwal,
			'students' => $students,
			'teknik_by_cpmk' => $teknik_by_cpmk,
			'scores' => $scores,
		];

		return $this->response->setJSON($data);
	}

	/**
	 * Validate the nilai for a specific jadwal (Admin and Dosen can validate)
	 * After validation, dosen cannot edit the scores anymore
	 */
	public function validateNilai($jadwal_id)
	{
		// Check if user has permission (both admin and dosen can validate)
		$currentDosenId = null;
		if (session()->get('role') === 'dosen') {
			$dosenModel = new DosenModel();
			$currentDosen = $dosenModel->where('user_id', session()->get('user_id'))->first();
			$currentDosenId = $currentDosen ? $currentDosen['id'] : null;
		}

		// Check if user can access this jadwal
		if (session()->get('role') !== 'admin' && !$this->canInputGrades($jadwal_id, $currentDosenId)) {
			return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk memvalidasi nilai pada jadwal ini.');
		}

		$jadwalModel = new MengajarModel();
		$jadwal = $jadwalModel->find($jadwal_id);

		if (!$jadwal) {
			return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
		}

		// Check if already validated
		if ($jadwal['is_nilai_validated'] == 1) {
			return redirect()->back()->with('warning', 'Nilai untuk jadwal ini sudah divalidasi sebelumnya.');
		}

		// Update validation status
		$updateData = [
			'is_nilai_validated' => 1,
			'validated_at' => date('Y-m-d H:i:s'),
			'validated_by' => session()->get('user_id')
		];

		if ($jadwalModel->update($jadwal_id, $updateData)) {
			$message = session()->get('role') === 'admin'
				? 'Nilai berhasil divalidasi. Dosen tidak dapat lagi mengedit nilai.'
				: 'Nilai berhasil divalidasi. Anda tidak dapat lagi mengedit nilai.';
			return redirect()->back()->with('success', $message);
		} else {
			return redirect()->back()->with('error', 'Gagal memvalidasi nilai.');
		}
	}

	/**
	 * Unvalidate the nilai for a specific jadwal (Admin only)
	 * After unvalidation, dosen can edit the scores again
	 */
	public function unvalidateNilai($jadwal_id)
	{
		// Only admin can unvalidate
		if (session()->get('role') !== 'admin') {
			return redirect()->back()->with('error', 'Hanya admin yang dapat membatalkan validasi nilai.');
		}

		$jadwalModel = new MengajarModel();
		$jadwal = $jadwalModel->find($jadwal_id);

		if (!$jadwal) {
			return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
		}

		// Check if not validated
		if ($jadwal['is_nilai_validated'] == 0) {
			return redirect()->back()->with('warning', 'Nilai untuk jadwal ini belum divalidasi.');
		}

		// Update validation status
		$updateData = [
			'is_nilai_validated' => 0,
			'validated_at' => null,
			'validated_by' => null
		];

		if ($jadwalModel->update($jadwal_id, $updateData)) {
			return redirect()->back()->with('success', 'Validasi nilai berhasil dibatalkan. Dosen dapat mengedit nilai kembali.');
		} else {
			return redirect()->back()->with('error', 'Gagal membatalkan validasi nilai.');
		}
	}

	/**
	 * Display CPMK scores for all students in a teaching schedule
	 * Anyone can view this (no permission check required)
	 */
	public function lihatCpmk($jadwal_id)
	{
		$jadwalModel = new MengajarModel();
		$mahasiswaModel = new MahasiswaModel();
		$cpmkModel = new CpmkModel();
		$nilaiCpmkModel = new NilaiCpmkMahasiswaModel();

		$jadwal = $jadwalModel->getJadwalWithDetails(['id' => $jadwal_id], true);
		if (!$jadwal) {
			return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
		}

		// Get validation status directly from jadwal_mengajar table
		$jadwalValidation = $jadwalModel->find($jadwal_id);
		if ($jadwalValidation) {
			$jadwal['is_nilai_validated'] = $jadwalValidation['is_nilai_validated'];
			$jadwal['validated_at'] = $jadwalValidation['validated_at'];
			$jadwal['validated_by'] = $jadwalValidation['validated_by'];
		}

		// Get students for this class
		$students = $mahasiswaModel->getStudentsForScoring($jadwal['program_studi'], $jadwal['semester']);

		// Get CPMK list for this jadwal
		$cpmk_list = $cpmkModel->getCpmkByJadwal($jadwal_id);

		// Get all CPMK scores for all students
		$existing_scores = $nilaiCpmkModel->getScoresByJadwalForInput($jadwal_id);

		// Calculate statistics for each CPMK
		$cpmk_stats = [];
		foreach ($cpmk_list as $cpmk) {
			$scores = [];
			foreach ($students as $student) {
				$score = $existing_scores[$student['id']][$cpmk['id']] ?? null;
				if ($score !== null && $score !== '') {
					$scores[] = (float)$score;
				}
			}

			$cpmk_stats[$cpmk['id']] = [
				'count' => count($scores),
				'avg' => count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : 0,
				'min' => count($scores) > 0 ? min($scores) : 0,
				'max' => count($scores) > 0 ? max($scores) : 0,
			];
		}

		// Get dynamic passing threshold from grade configuration
		$gradeConfigModel = new \App\Models\GradeConfigModel();
		$passingThreshold = $gradeConfigModel->getPassingThreshold();

		$data = [
			'title' => 'Lihat Nilai CPMK',
			'jadwal' => $jadwal,
			'mahasiswa_list' => $students,
			'cpmk_list' => $cpmk_list,
			'existing_scores' => $existing_scores,
			'cpmk_stats' => $cpmk_stats,
			'passing_threshold' => $passingThreshold,
		];

		return view('admin/nilai/lihat_cpmk', $data);
	}

	/**
	 * Display CPL scores for all students in a teaching schedule
	 * Anyone can view this (no permission check required)
	 */
	public function lihatCpl($jadwal_id)
	{
		$jadwalModel = new MengajarModel();
		$mahasiswaModel = new MahasiswaModel();
		$cpmkModel = new CpmkModel();
		$nilaiCpmkModel = new NilaiCpmkMahasiswaModel();

		$jadwal = $jadwalModel->getJadwalWithDetails(['id' => $jadwal_id], true);
		if (!$jadwal) {
			return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
		}

		// Get validation status directly from jadwal_mengajar table
		$jadwalValidation = $jadwalModel->find($jadwal_id);
		if ($jadwalValidation) {
			$jadwal['is_nilai_validated'] = $jadwalValidation['is_nilai_validated'];
			$jadwal['validated_at'] = $jadwalValidation['validated_at'];
			$jadwal['validated_by'] = $jadwalValidation['validated_by'];
		}

		// Get students for this class
		$students = $mahasiswaModel->getStudentsForScoring($jadwal['program_studi'], $jadwal['semester']);

		// Get CPMK list for this jadwal
		$cpmk_list = $cpmkModel->getCpmkByJadwal($jadwal_id);

		// Get all CPMK scores for all students
		$existing_scores = $nilaiCpmkModel->getScoresByJadwalForInput($jadwal_id);

		// Calculate statistics for each CPMK
		$cpmk_stats = [];
		foreach ($cpmk_list as $cpmk) {
			$scores = [];
			foreach ($students as $student) {
				$score = $existing_scores[$student['id']][$cpmk['id']] ?? null;
				if ($score !== null && $score !== '') {
					$scores[] = (float)$score;
				}
			}

			$cpmk_stats[$cpmk['id']] = [
				'count' => count($scores),
				'avg' => count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : 0,
				'min' => count($scores) > 0 ? min($scores) : 0,
				'max' => count($scores) > 0 ? max($scores) : 0,
			];
		}

		// Calculate CPL data
		$db = \Config\Database::connect();

		// Get mata_kuliah_id from jadwal_mengajar table
		$jadwal_data = $db->table('jadwal_mengajar')
			->select('mata_kuliah_id')
			->where('id', $jadwal_id)
			->get()
			->getRowArray();

		if (!$jadwal_data) {
			// If no mata_kuliah found, initialize empty arrays
			$cpl_list = [];
			$cpl_stats = [];
			$cpl_mahasiswa_scores = [];
		} else {
			$mata_kuliah_id = $jadwal_data['mata_kuliah_id'];

			// Get all CPLs for this mata kuliah
			$cpl_list = $db->table('cpl')
				->select('cpl.id, cpl.kode_cpl, cpl.deskripsi')
				->join('cpl_mk', 'cpl.id = cpl_mk.cpl_id')
				->where('cpl_mk.mata_kuliah_id', $mata_kuliah_id)
				->orderBy('cpl.kode_cpl', 'ASC')
				->get()
				->getResultArray();

			// Calculate CPL achievements (aggregate)
			$cpl_stats = [];
			foreach ($cpl_list as $cpl) {
				// Get all CPMKs mapped to this CPL
				$cpmk_for_cpl = $db->table('cpl_cpmk')
					->select('cpmk_id')
					->where('cpl_id', $cpl['id'])
					->get()
					->getResultArray();

				$cpmk_ids = array_column($cpmk_for_cpl, 'cpmk_id');

				// Calculate total score and total weight for this CPL
				$total_cpmk_score = 0;
				$total_cpmk_weight = 0;
				$cpmk_count = 0;
				$cpmk_codes = [];

				foreach ($cpmk_list as $cpmk) {
					if (in_array($cpmk['id'], $cpmk_ids)) {
						$cpmk_count++;
						$cpmk_codes[] = $cpmk['kode_cpmk'];
						// Get average score for this CPMK
						$avg_score = $cpmk_stats[$cpmk['id']]['avg'] ?? 0;
						$weight = (float)$cpmk['bobot_cpmk'];

						$total_cpmk_score += $avg_score;
						$total_cpmk_weight += $weight;
					}
				}

				// Calculate CPL achievement: Σ(CPMK score) / Σ(CPMK weight)
				$cpl_achievement = 0;
				if ($total_cpmk_weight > 0) {
					$cpl_achievement = ($total_cpmk_score / $total_cpmk_weight) * 100;
				}

				$cpl_stats[$cpl['id']] = [
					'kode_cpl' => $cpl['kode_cpl'],
					'deskripsi' => $cpl['deskripsi'],
					'total_score' => round($total_cpmk_score, 2),
					'total_weight' => round($total_cpmk_weight, 2),
					'achievement' => round($cpl_achievement, 2),
					'cpmk_count' => $cpmk_count,
					'cpmk_codes' => $cpmk_codes
				];
			}

			// Calculate CPL scores per student
			$cpl_mahasiswa_scores = [];
			foreach ($students as $student) {
				$mahasiswa_id = $student['id'];
				$cpl_mahasiswa_scores[$mahasiswa_id] = [];

				foreach ($cpl_list as $cpl) {
					// Get all CPMKs mapped to this CPL
					$cpmk_for_cpl = $db->table('cpl_cpmk')
						->select('cpmk_id')
						->where('cpl_id', $cpl['id'])
						->get()
						->getResultArray();

					$cpmk_ids = array_column($cpmk_for_cpl, 'cpmk_id');

					// Calculate total score and total weight for this student and CPL
					$student_total_score = 0;
					$student_total_weight = 0;

					foreach ($cpmk_list as $cpmk) {
						if (in_array($cpmk['id'], $cpmk_ids)) {
							// Get this student's score for this CPMK
							$student_cpmk_score = $existing_scores[$mahasiswa_id][$cpmk['id']] ?? null;

							if ($student_cpmk_score !== null && $student_cpmk_score !== '') {
								$weight = (float)$cpmk['bobot_cpmk'];
								$student_total_score += (float)$student_cpmk_score;
								$student_total_weight += $weight;
							}
						}
					}

					// Calculate CPL percentage
					$cpl_percentage = null;
					if ($student_total_weight > 0) {
						$cpl_percentage = ($student_total_score / $student_total_weight) * 100;
					}

					// Store both raw score and percentage
					$cpl_mahasiswa_scores[$mahasiswa_id][$cpl['id']] = [
						'score' => $student_total_score > 0 ? $student_total_score : null,
						'percentage' => $cpl_percentage
					];
				}
			}
		}

		// Get dynamic passing threshold from grade configuration
		$gradeConfigModel = new \App\Models\GradeConfigModel();
		$passingThreshold = $gradeConfigModel->getPassingThreshold();

		$data = [
			'title' => 'Lihat Nilai CPL',
			'jadwal' => $jadwal,
			'mahasiswa_list' => $students,
			'cpl_list' => $cpl_list,
			'cpl_stats' => $cpl_stats,
			'cpl_mahasiswa_scores' => $cpl_mahasiswa_scores ?? [],
			'passing_threshold' => $passingThreshold,
		];

		return view('admin/nilai/lihat_cpl', $data);
	}

	/**
	 * Export CPMK scores to Excel with separate columns for score and capaian
	 */
	public function exportCpmkExcel($jadwal_id)
	{
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

		// Get CPMK list for this jadwal
		$cpmk_list = $cpmkModel->getCpmkByJadwal($jadwal_id);

		// Get all CPMK scores for all students
		$existing_scores = $nilaiCpmkModel->getScoresByJadwalForInput($jadwal_id);

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
			$drawing->setHeight(50); // Set logo height
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
		$sheet->setCellValue('C' . $row, strtoupper($jadwal['kelas']) . " / " . strtoupper($jadwal['program_studi']));
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
			->where('jd.jadwal_mengajar_id', $jadwal_id)
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

		// Set filename
		$filename = 'Nilai_CPMK_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $jadwal['nama_mk']) . '_' . $jadwal['kelas'] . '_' . date('YmdHis') . '.xlsx';

		// Output file
		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		exit;
	}

	/**
	 * Export CPL scores to Excel
	 */
	public function exportCplExcel($jadwal_id)
	{
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

		// Get CPMK list for this jadwal
		$cpmk_list = $cpmkModel->getCpmkByJadwal($jadwal_id);

		// Get all CPMK scores for all students
		$existing_scores = $nilaiCpmkModel->getScoresByJadwalForInput($jadwal_id);

		// Calculate statistics for each CPMK
		$cpmk_stats = [];
		foreach ($cpmk_list as $cpmk) {
			$scores = [];
			foreach ($students as $student) {
				$score = $existing_scores[$student['id']][$cpmk['id']] ?? null;
				if ($score !== null && $score !== '') {
					$scores[] = (float)$score;
				}
			}

			$cpmk_stats[$cpmk['id']] = [
				'count' => count($scores),
				'avg' => count($scores) > 0 ? round(array_sum($scores) / count($scores), 2) : 0,
				'min' => count($scores) > 0 ? min($scores) : 0,
				'max' => count($scores) > 0 ? max($scores) : 0,
			];
		}

		// Calculate CPL data
		$db = \Config\Database::connect();

		// Get mata_kuliah_id from jadwal_mengajar table
		$jadwal_data = $db->table('jadwal_mengajar')
			->select('mata_kuliah_id')
			->where('id', $jadwal_id)
			->get()
			->getRowArray();

		if (!$jadwal_data) {
			$cpl_list = [];
			$cpl_stats = [];
			$cpl_mahasiswa_scores = [];
		} else {
			$mata_kuliah_id = $jadwal_data['mata_kuliah_id'];

			// Get all CPLs for this mata kuliah
			$cpl_list = $db->table('cpl')
				->select('cpl.id, cpl.kode_cpl, cpl.deskripsi')
				->join('cpl_mk', 'cpl.id = cpl_mk.cpl_id')
				->where('cpl_mk.mata_kuliah_id', $mata_kuliah_id)
				->orderBy('cpl.kode_cpl', 'ASC')
				->get()
				->getResultArray();

			// Calculate CPL achievements (aggregate)
			$cpl_stats = [];
			foreach ($cpl_list as $cpl) {
				// Get all CPMKs mapped to this CPL
				$cpmk_for_cpl = $db->table('cpl_cpmk')
					->select('cpmk_id')
					->where('cpl_id', $cpl['id'])
					->get()
					->getResultArray();

				$cpmk_ids = array_column($cpmk_for_cpl, 'cpmk_id');

				// Calculate total score and total weight for this CPL
				$total_cpmk_score = 0;
				$total_cpmk_weight = 0;
				$cpmk_count = 0;
				$cpmk_codes = [];

				foreach ($cpmk_list as $cpmk) {
					if (in_array($cpmk['id'], $cpmk_ids)) {
						$cpmk_count++;
						$cpmk_codes[] = $cpmk['kode_cpmk'];
						// Get average score for this CPMK
						$avg_score = $cpmk_stats[$cpmk['id']]['avg'] ?? 0;
						$weight = (float)$cpmk['bobot_cpmk'];

						$total_cpmk_score += $avg_score;
						$total_cpmk_weight += $weight;
					}
				}

				// Calculate CPL achievement
				$cpl_achievement = 0;
				if ($total_cpmk_weight > 0) {
					$cpl_achievement = ($total_cpmk_score / $total_cpmk_weight) * 100;
				}

				$cpl_stats[$cpl['id']] = [
					'kode_cpl' => $cpl['kode_cpl'],
					'deskripsi' => $cpl['deskripsi'],
					'total_score' => round($total_cpmk_score, 2),
					'total_weight' => round($total_cpmk_weight, 2),
					'achievement' => round($cpl_achievement, 2),
					'cpmk_count' => $cpmk_count,
					'cpmk_codes' => $cpmk_codes
				];
			}

			// Calculate CPL scores per student
			$cpl_mahasiswa_scores = [];
			foreach ($students as $student) {
				$mahasiswa_id = $student['id'];
				$cpl_mahasiswa_scores[$mahasiswa_id] = [];

				foreach ($cpl_list as $cpl) {
					// Get all CPMKs mapped to this CPL
					$cpmk_for_cpl = $db->table('cpl_cpmk')
						->select('cpmk_id')
						->where('cpl_id', $cpl['id'])
						->get()
						->getResultArray();

					$cpmk_ids = array_column($cpmk_for_cpl, 'cpmk_id');

					// Calculate total score and total weight for this student and CPL
					$student_total_score = 0;
					$student_total_weight = 0;

					foreach ($cpmk_list as $cpmk) {
						if (in_array($cpmk['id'], $cpmk_ids)) {
							// Get this student's score for this CPMK
							$student_cpmk_score = $existing_scores[$mahasiswa_id][$cpmk['id']] ?? null;

							if ($student_cpmk_score !== null && $student_cpmk_score !== '') {
								$weight = (float)$cpmk['bobot_cpmk'];
								$student_total_score += (float)$student_cpmk_score;
								$student_total_weight += $weight;
							}
						}
					}

					// Calculate CPL score for this student
					$cpl_score = null;
					if ($student_total_weight > 0) {
						$cpl_score = ($student_total_score / $student_total_weight) * 100;
					}

					$cpl_mahasiswa_scores[$mahasiswa_id][$cpl['id']] = $cpl_score;
				}
			}
		}

		// Create Excel file using PhpSpreadsheet
		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();

		// Set document properties
		$spreadsheet->getProperties()
			->setCreator('OBE System')
			->setTitle('Nilai CPL - ' . $jadwal['nama_mk'])
			->setSubject('Nilai CPL');

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
			$drawing->setHeight(50); // Set logo height
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
		$totalColumns = 3 + (count($cpl_list) * 2); // No, NIM, Nama + (CPL score + percentage) * count
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

		// Set CPL and Semester info on the right side (last column)
		$cpl_text = "NILAI CPL\nSemester " . $semester_type . " " . $tahun;
		$sheet->setCellValue($lastColumn . '1', $cpl_text);
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
		$sheet->setCellValue('C' . $row, strtoupper($jadwal['kelas']) . " / " . strtoupper($jadwal['program_studi']));
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

		// CPL columns - merged headers with sub-cells
		foreach ($cpl_list as $cpl) {
			$startCol = $col;
			$endCol = $col + 1;
			$startColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($startCol);
			$endColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($endCol);

			// Merge cells for CPL header
			$sheet->setCellValue($startColLetter . $row, $cpl['kode_cpl']);
			$sheet->mergeCells($startColLetter . $row . ':' . $endColLetter . $row);

			$col += 2;
		}

		// Merge cells vertically for columns that span both header rows
		$sheet->mergeCells('A' . $row . ':A' . ($row + 1));
		$sheet->mergeCells('B' . $row . ':B' . ($row + 1));
		$sheet->mergeCells('C' . $row . ':C' . ($row + 1));

		// Second header row - Sub-headers for CPL
		$row++;
		$col = 4; // Start after No, NIM, Nama
		foreach ($cpl_list as $cpl) {
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

			// CPL scores
			foreach ($cpl_list as $cpl) {
				// Recalculate CPL score and percentage for this student
				$cpmk_for_cpl = $db->table('cpl_cpmk')
					->select('cpmk_id')
					->where('cpl_id', $cpl['id'])
					->get()
					->getResultArray();

				$cpmk_ids = array_column($cpmk_for_cpl, 'cpmk_id');

				$student_total_score = 0;
				$student_total_weight = 0;

				foreach ($cpmk_list as $cpmk) {
					if (in_array($cpmk['id'], $cpmk_ids)) {
						$student_cpmk_score = $existing_scores[$mahasiswa_id][$cpmk['id']] ?? null;

						if ($student_cpmk_score !== null && $student_cpmk_score !== '') {
							$weight = (float)$cpmk['bobot_cpmk'];
							$student_total_score += (float)$student_cpmk_score;
							$student_total_weight += $weight;
						}
					}
				}

				// Calculate CPL percentage
				$cpl_percentage = null;
				if ($student_total_weight > 0) {
					$cpl_percentage = ($student_total_score / $student_total_weight) * 100;
				}

				// Display score and percentage in separate columns
				if ($cpl_percentage !== null) {
					// Score column
					$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, number_format($student_total_score, 2));
					// Percentage column
					$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, number_format($cpl_percentage, 2));
				} else {
					$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, '-');
					$sheet->setCellValue(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++) . $row, '-');
				}
			}

			// Center align for all data columns
			$sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle('D' . $row . ':' . $lastColumn . $row)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

			$row++;
		}

		// Add borders to student table
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
		for ($i = 1; $i <= $totalColumns; $i++) {
			$columnLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
			$sheet->getColumnDimension($columnLetter)->setAutoSize(true);
		}

		// Set wider width for description column
		$sheet->getColumnDimension('C')->setWidth(30);

		// Get NIP of dosen ketua
		$dosenKetuaNip = $db->table('jadwal_dosen jd')
			->select('d.nip')
			->join('dosen d', 'd.id = jd.dosen_id')
			->where('jd.jadwal_mengajar_id', $jadwal_id)
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

		// Set filename
		$filename = 'Nilai_CPL_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $jadwal['nama_mk']) . '_' . $jadwal['kelas'] . '_' . date('YmdHis') . '.xlsx';

		// Output file
		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		exit;
	}

	/**
	 * Display read-only view of scores by teknik_penilaian
	 * Anyone can view this (no permission check required)
	 */
	public function lihatNilai($jadwal_id)
	{
		$jadwalModel = new MengajarModel();
		$mahasiswaModel = new MahasiswaModel();
		$nilaiTeknikModel = new NilaiTeknikPenilaianModel();
		$nilaiMahasiswaModel = new NilaiMahasiswaModel();

		$jadwal = $jadwalModel->getJadwalWithDetails(['id' => $jadwal_id], true);
		if (!$jadwal) {
			return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
		}

		// Get validation status directly from jadwal_mengajar table
		$jadwalValidation = $jadwalModel->find($jadwal_id);
		if ($jadwalValidation) {
			$jadwal['is_nilai_validated'] = $jadwalValidation['is_nilai_validated'];
			$jadwal['validated_at'] = $jadwalValidation['validated_at'];
			$jadwal['validated_by'] = $jadwalValidation['validated_by'];

			// Get validator user information
			if ($jadwalValidation['validated_by']) {
				$db = \Config\Database::connect();
				$validator = $db->table('users')
					->select('users.username, users.role')
					->where('users.id', $jadwalValidation['validated_by'])
					->get()
					->getRowArray();

				if ($validator) {
					// Get full name based on role
					if ($validator['role'] === 'dosen') {
						$dosen = $db->table('dosen')
							->select('nama_lengkap')
							->where('user_id', $jadwalValidation['validated_by'])
							->get()
							->getRowArray();
						$jadwal['validated_by_name'] = $dosen ? $dosen['nama_lengkap'] : $validator['username'];
					} else {
						// For admin or other roles, just use username
						$jadwal['validated_by_name'] = $validator['username'];
					}
					$jadwal['validator_role'] = $validator['role'];
				}
			}
		}

		// Get students for this class
		$students = $mahasiswaModel->getStudentsForScoring($jadwal['program_studi'], $jadwal['semester']);

		// Get COMBINED teknik_penilaian list
		$combined_data = $nilaiTeknikModel->getCombinedTeknikPenilaianByJadwal($jadwal_id);

		if (empty($combined_data['combined_list'])) {
			return redirect()->back()->with('error', 'Tidak ada teknik penilaian yang terdefinisi pada RPS untuk mata kuliah ini.');
		}

		// Get existing scores to display
		$existing_scores = $nilaiTeknikModel->getCombinedScoresForInput($jadwal_id);

		// Get final scores (nilai akhir and nilai huruf)
		$final_scores = $nilaiMahasiswaModel->getFinalScoresByJadwal($jadwal_id);
		$final_scores_map = [];
		foreach ($final_scores as $score) {
			$final_scores_map[$score['mahasiswa_id']] = $score;
		}

		$data = [
			'title' => 'Lihat Nilai',
			'jadwal' => $jadwal,
			'mahasiswa_list' => $students,
			'combined_list' => $combined_data['combined_list'],
			'teknik_by_tahap' => $combined_data['by_tahap'],
			'existing_scores' => $existing_scores,
			'final_scores_map' => $final_scores_map,
			'readonly' => true, // Flag to indicate read-only mode
		];

		return view('admin/nilai/lihat_nilai', $data);
	}

	/**
	 * Download DPNA (Daftar Penilaian Nilai Akhir) as HTML table
	 * Shows: No, NIM, Nama, Tugas, UTS, UAS, Nilai Akhir, Nilai Huruf
	 */
	public function unduhDpna($jadwal_id)
	{
		$jadwalModel = new MengajarModel();
		$mahasiswaModel = new MahasiswaModel();
		$nilaiTeknikModel = new NilaiTeknikPenilaianModel();
		$nilaiMahasiswaModel = new NilaiMahasiswaModel();

		// Get jadwal details
		$jadwal = $jadwalModel->getJadwalWithDetails(['id' => $jadwal_id], true);
		if (!$jadwal) {
			return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
		}

		// Get students for this class
		$students = $mahasiswaModel->getStudentsForScoring($jadwal['program_studi'], $jadwal['semester']);

		// Get existing scores
		$existing_scores = $nilaiTeknikModel->getCombinedScoresForInput($jadwal_id);

		// Get combined teknik data to get all available techniques and weights
		$combined_data = $nilaiTeknikModel->getCombinedTeknikPenilaianByJadwal($jadwal_id);

		// Build a map of all available techniques with their weights and labels
		$teknik_list = [];
		$teknik_weight_map = [];

		foreach ($combined_data['combined_list'] as $item) {
			$teknik_key = $item['teknik_key'];
			$teknik_list[$teknik_key] = [
				'label' => $item['teknik_label'],
				'weight' => $item['total_bobot']
			];
			$teknik_weight_map[$teknik_key] = $item['total_bobot'];
		}

		// Get final scores (nilai akhir and nilai huruf)
		$final_scores = $nilaiMahasiswaModel->getFinalScoresByJadwal($jadwal_id);
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
			$row = [
				'no' => $no++,
				'nim' => $student['nim'],
				'nama' => $student['nama_lengkap']
			];

			// Add scores for each teknik penilaian
			foreach ($teknik_list as $teknik_key => $teknik_info) {
				$score = 0;
				if (isset($existing_scores[$mahasiswa_id][$teknik_key])) {
					$score = $existing_scores[$mahasiswa_id][$teknik_key];
				}
				$row['teknik_' . $teknik_key] = $score;
			}

			// Get nilai akhir and nilai huruf
			$nilai_akhir = $final_scores_map[$mahasiswa_id]['nilai_akhir'] ?? 0;
			$nilai_huruf = $final_scores_map[$mahasiswa_id]['nilai_huruf'] ?? '-';
			$keterangan = $getKeterangan($nilai_huruf);

			$row['nilai_akhir'] = $nilai_akhir;
			$row['nilai_huruf'] = $nilai_huruf;
			$row['keterangan'] = $keterangan;

			$dpna_data[] = $row;
		}

		// Get NIP of dosen ketua
		$db = \Config\Database::connect();
		$dosenKetuaNip = $db->table('jadwal_dosen jd')
			->select('d.nip')
			->join('dosen d', 'd.id = jd.dosen_id')
			->where('jd.jadwal_mengajar_id', $jadwal_id)
			->where('jd.role', 'leader')
			->get()
			->getRowArray();
		$nip = $dosenKetuaNip['nip'] ?? '';

		$data = [
			'title' => 'DPNA - ' . $jadwal['nama_mk'],
			'jadwal' => $jadwal,
			'dpna_data' => $dpna_data,
			'teknik_list' => $teknik_list,
			'nip' => $nip
		];

		return view('admin/nilai/dpna', $data);
	}

	/**
	 * Export DPNA to Excel
	 */
	public function exportDpnaExcel($jadwal_id)
	{
		$jadwalModel = new MengajarModel();
		$mahasiswaModel = new MahasiswaModel();
		$nilaiTeknikModel = new NilaiTeknikPenilaianModel();
		$nilaiMahasiswaModel = new NilaiMahasiswaModel();

		// Get jadwal details
		$jadwal = $jadwalModel->getJadwalWithDetails(['id' => $jadwal_id], true);
		if (!$jadwal) {
			return redirect()->back()->with('error', 'Jadwal tidak ditemukan.');
		}

		// Get students for this class
		$students = $mahasiswaModel->getStudentsForScoring($jadwal['program_studi'], $jadwal['semester']);

		// Get existing scores
		$existing_scores = $nilaiTeknikModel->getCombinedScoresForInput($jadwal_id);

		// Get combined teknik data to get all available techniques and weights
		$combined_data = $nilaiTeknikModel->getCombinedTeknikPenilaianByJadwal($jadwal_id);

		// Build a map of all available techniques with their weights and labels
		$teknik_list = [];
		$teknik_weight_map = [];

		foreach ($combined_data['combined_list'] as $item) {
			$teknik_key = $item['teknik_key'];
			$teknik_list[$teknik_key] = [
				'label' => $item['teknik_label'],
				'weight' => $item['total_bobot']
			];
			$teknik_weight_map[$teknik_key] = $item['total_bobot'];
		}

		// Get final scores
		$final_scores = $nilaiMahasiswaModel->getFinalScoresByJadwal($jadwal_id);
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
			$row = [
				'no' => $no++,
				'nim' => $student['nim'],
				'nama' => $student['nama_lengkap']
			];

			// Add scores for each teknik penilaian
			foreach ($teknik_list as $teknik_key => $teknik_info) {
				$score = 0;
				if (isset($existing_scores[$mahasiswa_id][$teknik_key])) {
					$score = $existing_scores[$mahasiswa_id][$teknik_key];
				}
				$row['teknik_' . $teknik_key] = $score;
			}

			// Get nilai akhir and nilai huruf
			$nilai_akhir = $final_scores_map[$mahasiswa_id]['nilai_akhir'] ?? 0;
			$nilai_huruf = $final_scores_map[$mahasiswa_id]['nilai_huruf'] ?? '-';
			$keterangan = $getKeterangan($nilai_huruf);

			$row['nilai_akhir'] = $nilai_akhir;
			$row['nilai_huruf'] = $nilai_huruf;
			$row['keterangan'] = $keterangan;

			$dpna_data[] = $row;
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
			$drawing->setHeight(50); // Set logo height
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
		$getColumnLetter = function($index) {
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
		$sheet->setCellValue('C' . $row, strtoupper($jadwal['kelas']) . " / " . strtoupper($jadwal['program_studi']));
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

		// Add dynamic teknik penilaian columns
		foreach ($teknik_list as $teknik_key => $teknik_info) {
			$colLetter = $getColumnLetter($col);
			$teknik_text = $teknik_info['label'] . "\n(" . number_format($teknik_info['weight'], 1) . '%)';
			$sheet->setCellValue($colLetter . $row, $teknik_text);
			$sheet->mergeCells($colLetter . $row . ':' . $colLetter . ($row + 1));
			$col++;
		}

		// Nilai Akhir columns (merged header)
		$nilaiAkhirStartCol = $col;
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

			// Add teknik penilaian scores
			$teknikColStart = $col;
			foreach ($teknik_list as $teknik_key => $teknik_info) {
				$sheet->setCellValue($getColumnLetter($col++) . $row, $data['teknik_' . $teknik_key]);
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
			->where('jd.jadwal_mengajar_id', $jadwal_id)
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

		// Set filename
		$filename = 'DPNA_' . preg_replace('/[^A-Za-z0-9_-]/', '_', $jadwal['nama_mk']) . '_' . $jadwal['kelas'] . '_' . date('YmdHis') . '.xlsx';

		// Output file
		$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		$writer->save('php://output');
		exit;
	}

	/**
	 * Import scores from Excel file (DPNA format)
	 * Accepts Excel file with columns: No, NIM, Nama, [Individual Teknik Penilaian columns], Nilai Akhir, etc.
	 * Each teknik penilaian (e.g., Partisipasi, Observasi, UTS, UAS, etc.) is imported separately
	 * Maps to individual teknik_penilaian scores in the database
	 */
	public function importNilaiExcel($jadwal_id)
	{
		// Check if AJAX request
		if (!$this->request->isAJAX()) {
			return $this->response->setStatusCode(403)->setJSON([
				'status' => 'error',
				'message' => 'Invalid request method.'
			]);
		}

		// Check access permissions
		$currentDosenId = null;
		if (session()->get('role') === 'dosen') {
			$dosenModel = new DosenModel();
			$currentDosen = $dosenModel->where('user_id', session()->get('user_id'))->first();
			$currentDosenId = $currentDosen ? $currentDosen['id'] : null;
		}

		if (!$this->canInputGrades($jadwal_id, $currentDosenId)) {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'Anda tidak memiliki akses untuk mengimport nilai pada jadwal ini.'
			]);
		}

		// Check if nilai is already validated (only dosen is blocked)
		$jadwalModel = new MengajarModel();
		$jadwal = $jadwalModel->find($jadwal_id);

		if ($jadwal && $jadwal['is_nilai_validated'] == 1 && session()->get('role') === 'dosen') {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'Nilai sudah divalidasi. Anda tidak dapat lagi mengedit nilai.'
			]);
		}

		// Validate uploaded file
		$file = $this->request->getFile('file_nilai');

		if (!$file || !$file->isValid()) {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'File tidak valid atau tidak ditemukan.'
			]);
		}

		// Check file extension
		$allowedExtensions = ['xlsx', 'xls'];
		if (!in_array($file->getExtension(), $allowedExtensions)) {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'Format file tidak didukung. Gunakan file Excel (.xlsx atau .xls).'
			]);
		}

		// Check file size (max 5MB)
		if ($file->getSize() > 5 * 1024 * 1024) {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'Ukuran file terlalu besar. Maksimal 5MB.'
			]);
		}

		try {
			// Load Excel file
			$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getTempName());
			$sheet = $spreadsheet->getActiveSheet();

			// Find the header row (should contain "NIM")
			$headerRow = null;
			$highestRow = $sheet->getHighestRow();

			for ($row = 1; $row <= min(20, $highestRow); $row++) {
				$cellValue = strtoupper(trim($sheet->getCell('B' . $row)->getValue()));
				if ($cellValue === 'NIM') {
					$headerRow = $row;
					break;
				}
			}

			if (!$headerRow) {
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'Format file tidak sesuai. Tidak dapat menemukan header "NIM".'
				]);
			}

			// Get combined teknik data to identify available techniques
			$nilaiTeknikModel = new NilaiTeknikPenilaianModel();
			$combined_data = $nilaiTeknikModel->getCombinedTeknikPenilaianByJadwal($jadwal_id);

			// Build mapping of available techniques with their labels
			$teknik_list = [];
			$teknik_mapping = [];
			foreach ($combined_data['combined_list'] as $item) {
				$teknik_key = $item['teknik_key'];
				$teknik_label = strtoupper(trim($item['teknik_label']));
				$teknik_list[$teknik_key] = [
					'label' => $teknik_label,
					'rps_mingguan_ids' => $item['rps_mingguan_ids']
				];
				$teknik_mapping[$teknik_label] = $teknik_key;
			}

			// Helper function to convert column index to Excel column letter
			$getColumnLetter = function($index) {
				$letter = '';
				$index++; // Excel columns are 1-based
				while ($index > 0) {
					$index--;
					$letter = chr($index % 26 + 65) . $letter;
					$index = floor($index / 26);
				}
				return $letter;
			};

			// Scan header row to find NIM and all teknik penilaian columns
			$columns = [];
			$highestColumn = $sheet->getHighestColumn();
			$highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

			for ($col = 1; $col <= $highestColumnIndex; $col++) {
				$colLetter = $getColumnLetter($col - 1);
				$headerValue = strtoupper(trim($sheet->getCell($colLetter . $headerRow)->getValue()));

				// Remove only the weight percentage from header (e.g., "TES TULIS (UTS) (20%)" -> "TES TULIS (UTS)")
				// Only removes patterns like (20%), (15.5%), etc., not descriptive text like (UTS) or (PRAKTEK / TUGAS)
				$headerValue = preg_replace('/\s*\(\s*\d+\.?\d*\s*%\s*\)\s*$/', '', $headerValue);
				$headerValue = trim($headerValue);

				if ($headerValue === 'NIM') {
					$columns['nim'] = $colLetter;
				} elseif (isset($teknik_mapping[$headerValue])) {
					// Map column to teknik_key
					$teknik_key = $teknik_mapping[$headerValue];
					$columns['teknik_' . $teknik_key] = $colLetter;
				}
			}

			// Validate that NIM column exists
			if (!isset($columns['nim'])) {
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'Format file tidak sesuai. Kolom NIM tidak ditemukan.'
				]);
			}

			// Check if at least one teknik penilaian column is found
			$found_teknik_count = 0;
			foreach (array_keys($columns) as $key) {
				if (strpos($key, 'teknik_') === 0) {
					$found_teknik_count++;
				}
			}

			if ($found_teknik_count === 0) {
				$available_labels = [];
				foreach ($teknik_list as $info) {
					$available_labels[] = $info['label'];
				}
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'Format file tidak sesuai. Tidak ada kolom teknik penilaian yang ditemukan. Kolom yang tersedia: ' . implode(', ', $available_labels) . '.'
				]);
			}

			// Get mahasiswa data for this jadwal
			$mahasiswaModel = new MahasiswaModel();
			$jadwalDetails = $jadwalModel->getJadwalWithDetails(['id' => $jadwal_id], true);
			$students = $mahasiswaModel->getStudentsForScoring($jadwalDetails['program_studi'], $jadwalDetails['semester']);

			// Create NIM to mahasiswa_id mapping
			$nimToId = [];
			foreach ($students as $student) {
				$nimToId[$student['nim']] = $student['id'];
			}

			// Parse data rows
			$imported_count = 0;
			$errors = [];
			$db = \Config\Database::connect();

			$db->transStart();

			for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
				$nim = trim($sheet->getCell($columns['nim'] . $row)->getValue());

				// Skip empty rows
				if (empty($nim)) {
					continue;
				}

				// Check if student exists
				if (!isset($nimToId[$nim])) {
					$errors[] = "Baris $row: NIM $nim tidak ditemukan dalam daftar mahasiswa.";
					continue;
				}

				$mahasiswa_id = $nimToId[$nim];
				$hasValidScore = false;

				// Read and validate scores for each teknik penilaian
				$teknik_scores = [];
				foreach ($columns as $col_key => $col_letter) {
					// Skip non-teknik columns
					if (strpos($col_key, 'teknik_') !== 0) {
						continue;
					}

					// Extract teknik_key from col_key (e.g., "teknik_tes_tulis_uts" -> "tes_tulis_uts")
					$teknik_key = substr($col_key, 7);

					// Get the score value
					$score_value = $sheet->getCell($col_letter . $row)->getValue();
					$score = is_numeric($score_value) ? floatval($score_value) : null;

					// Validate score range
					if ($score !== null) {
						if ($score < 0 || $score > 100) {
							$teknik_label = $teknik_list[$teknik_key]['label'] ?? $teknik_key;
							$errors[] = "Baris $row (NIM $nim): Nilai $teknik_label tidak valid ($score). Harus antara 0-100.";
							continue 2; // Skip this student entirely
						}
						$hasValidScore = true;
					}

					$teknik_scores[$teknik_key] = $score;
				}

				// Skip if no valid scores found
				if (!$hasValidScore) {
					continue;
				}

				// Save scores for each teknik penilaian
				foreach ($teknik_scores as $teknik_key => $score) {
					// Skip if score is null (not provided)
					if ($score === null) {
						continue;
					}

					// Get rps_mingguan_ids for this teknik
					$rps_ids = $teknik_list[$teknik_key]['rps_mingguan_ids'] ?? [];

					// Save to each rps_mingguan entry
					foreach ($rps_ids as $rps_info) {
						$scoreData = [
							'mahasiswa_id' => $mahasiswa_id,
							'jadwal_mengajar_id' => $jadwal_id,
							'rps_mingguan_id' => $rps_info['rps_mingguan_id'],
							'teknik_penilaian_key' => $teknik_key,
							'nilai' => $score,
						];
						$nilaiTeknikModel->saveOrUpdate($scoreData);
					}
				}

				$imported_count++;
			}

			// Calculate CPMK scores from imported teknik_penilaian scores
			$cpmk_scores = $nilaiTeknikModel->calculateCpmkScores($jadwal_id);

			// Save calculated CPMK scores
			$nilaiCpmkModel = new NilaiCpmkMahasiswaModel();
			foreach ($cpmk_scores as $mahasiswa_id => $cpmk_data) {
				foreach ($cpmk_data as $cpmk_id => $cpmk_score) {
					$cpmkData = [
						'mahasiswa_id' => $mahasiswa_id,
						'jadwal_mengajar_id' => $jadwal_id,
						'cpmk_id' => $cpmk_id,
						'nilai_cpmk' => $cpmk_score,
					];
					$nilaiCpmkModel->saveOrUpdate($cpmkData);
				}
			}

			// Calculate final scores
			$nilaiMahasiswaModel = new NilaiMahasiswaModel();
			foreach ($cpmk_scores as $mahasiswa_id => $cpmk_data) {
				$nilai_akhir = 0;
				if (!empty($cpmk_data)) {
					$nilai_akhir = array_sum($cpmk_data);
				}

				$finalData = [
					'mahasiswa_id' => $mahasiswa_id,
					'jadwal_mengajar_id' => $jadwal_id,
					'nilai_akhir' => round($nilai_akhir, 2),
					'nilai_huruf' => $this->calculateGrade($nilai_akhir),
					'status_kelulusan' => $this->calculatePassingStatus($nilai_akhir),
				];
				$nilaiMahasiswaModel->saveOrUpdate($finalData);
			}

			$db->transComplete();

			if ($db->transStatus() === false) {
				return $this->response->setJSON([
					'status' => 'error',
					'message' => 'Gagal menyimpan data ke database.'
				]);
			}

			return $this->response->setJSON([
				'status' => 'success',
				'message' => 'Data nilai berhasil diimport dan CPMK scores dihitung otomatis.',
				'imported_count' => $imported_count,
				'errors' => $errors
			]);
		} catch (\Exception $e) {
			return $this->response->setJSON([
				'status' => 'error',
				'message' => 'Terjadi kesalahan saat memproses file: ' . $e->getMessage()
			]);
		}
	}
}

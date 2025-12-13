<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4" style="overflow-x: hidden;">
	<div class="row mb-4">
		<div class="col-12">
			<nav aria-label="breadcrumb" class="mb-3">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">
						<a href="<?= base_url('admin/nilai') ?>" class="text-decoration-none">
							<i class="bi bi-house-door me-1"></i>Penilaian
						</a>
					</li>
					<li class="breadcrumb-item active">Lihat Nilai</li>
				</ol>
			</nav>

			<div class="d-flex justify-content-between align-items-center">
				<div>
					<h2 class="fw-bold mb-1">Lihat Nilai</h2>
					<p class="text-muted mb-0">Menampilkan nilai untuk setiap teknik penilaian (Kehadiran, Tugas, UTS, UAS, dll)</p>
					<?php if (isset($jadwal['is_nilai_validated']) && $jadwal['is_nilai_validated'] == 1): ?>
						<div class="alert alert-success alert-sm mt-2 mb-0 py-2">
							<i class="bi bi-check-circle-fill me-1"></i>
							<strong>Nilai telah divalidasi</strong>
							<?php if (isset($jadwal['validated_by_name'])): ?>
								oleh <strong><?= esc($jadwal['validated_by_name']) ?></strong>
							<?php endif; ?>
							pada <?= date('d/m/Y H:i', strtotime($jadwal['validated_at'])) ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="d-flex gap-2">
					<a href="<?= base_url('admin/nilai/unduh-dpna/' . $jadwal['id']) ?>"
						class="btn btn-success"
						target="_blank"
						title="Unduh Daftar Penilaian Nilai Akhir">
						<i class="bi bi-download me-2"></i>Unduh DPNA
					</a>
					<a href="<?= base_url('admin/nilai') ?>" class="btn btn-outline-secondary">
						<i class="bi bi-arrow-left me-2"></i>Kembali
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="card border-0 shadow-sm mb-4">
		<div class="card-body bg-light">
			<div class="row g-3">
				<div class="col-md-4">
					<div class="d-flex align-items-center">
						<div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
							<i class="bi bi-book text-primary fs-5"></i>
						</div>
						<div>
							<small class="text-muted">Mata Kuliah</small>
							<h6 class="mb-0 fw-semibold"><?= esc($jadwal['nama_mk']) ?></h6>
						</div>
					</div>
				</div>
				<div class="col-md-2">
					<div class="d-flex align-items-center">
						<div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
							<i class="bi bi-people text-success fs-5"></i>
						</div>
						<div>
							<small class="text-muted">Kelas</small>
							<h6 class="mb-0 fw-semibold"><?= esc($jadwal['kelas']) ?></h6>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="d-flex align-items-center">
						<div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
							<i class="bi bi-calendar text-info fs-5"></i>
						</div>
						<div>
							<small class="text-muted">Tahun Akademik</small>
							<h6 class="mb-0 fw-semibold"><?= esc($jadwal['tahun_akademik']) ?></h6>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="d-flex align-items-center">
						<div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
							<i class="bi bi-person-badge text-warning fs-5"></i>
						</div>
						<div>
							<small class="text-muted">Dosen Pengampu</small>
							<h6 class="mb-0 fw-semibold"><?= esc($jadwal['dosen_ketua'] ?? 'N/A') ?></h6>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Quick Info & RPS Link -->
	<?php if (!empty($teknik_by_tahap)): ?>
		<div class="card border-0 shadow-sm mb-4">
			<div class="card-body">
				<div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
					<div class="d-flex align-items-center flex-grow-1">
						<div class="bg-info bg-opacity-10 rounded-circle p-2 me-3 flex-shrink-0">
							<i class="bi bi-lightbulb-fill text-info fs-5"></i>
						</div>
						<div>
							<h6 class="fw-bold mb-1">Informasi Mata Kuliah</h6>
							<small class="text-muted">
								Berikut adalah informasi singkat mengenai RPS dan teknik penilaian mingguan yang digunakan pada mata kuliah ini.
							</small>
						</div>
					</div>
					<div class="d-flex gap-2 flex-shrink-0">
						<?php
						// Get RPS ID from the first teknik item
						$db = \Config\Database::connect();
						$first_rps_mingguan_id = $teknik_list[0]['rps_mingguan_id'] ?? null;
						$rps_id = null;
						if ($first_rps_mingguan_id) {
							$first_rps_mingguan = $db->table('rps_mingguan')
								->select('rps_id')
								->where('id', $first_rps_mingguan_id)
								->get()
								->getRowArray();
							$rps_id = $first_rps_mingguan['rps_id'] ?? null;
						}
						?>
						<?php if ($rps_id): ?>
							<a href="<?= base_url('rps/preview/' . $rps_id) ?>"
								class="btn btn-sm btn-outline-primary"
								target="_blank"
								title="Lihat RPS">
								<i class="bi bi-file-text"></i>
								<span class="d-none d-lg-inline ms-1">RPS</span>
							</a>
							<a href="<?= base_url('rps/mingguan/' . $rps_id) ?>"
								class="btn btn-sm btn-outline-secondary"
								target="_blank"
								title="Kelola RPS Mingguan">
								<i class="bi bi-calendar-week"></i>
								<span class="d-none d-lg-inline ms-1">Mingguan</span>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<!-- Grade Distribution Chart -->
	<?php if (!empty($mahasiswa_list) && !empty($teknik_list)): ?>
		<div class="card border-0 shadow-sm mb-4">
			<div class="card-body">
				<canvas id="gradeDistributionChart" style="max-height: 400px;"></canvas>
			</div>
		</div>
	<?php endif; ?>

	<div class="card border-0 shadow-sm">
		<div class="card-header bg-primary text-white py-3">
			<div class="d-flex justify-content-between align-items-center">
				<div>
					<h5 class="mb-0"><i class="bi bi-eye me-2"></i>Tampilan Nilai Teknik Penilaian</h5>
					<small class="opacity-75">Nilai ini hanya dapat dilihat, tidak dapat diubah</small>
				</div>
				<?php if (!empty($mahasiswa_list) && !empty($teknik_list)): ?>
					<div class="text-end">
						<small class="opacity-75">
							<?= count($mahasiswa_list) ?> Mahasiswa | <?= count($teknik_list) ?> Teknik Penilaian
						</small>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="card-body p-0">
			<?php if (empty($mahasiswa_list) || empty($teknik_list)): ?>
				<div class="text-center py-5">
					<div class="mb-4">
						<i class="bi bi-exclamation-triangle display-1 text-warning opacity-25"></i>
					</div>
					<h5 class="text-muted">Data Tidak Tersedia</h5>
					<p class="text-muted mb-4">
						Tidak ditemukan data mahasiswa atau teknik penilaian untuk mata kuliah ini.<br>
						Pastikan RPS Mingguan sudah dilengkapi dengan teknik penilaian dan bobotnya.
					</p>
					<a href="<?= base_url('admin/nilai') ?>" class="btn btn-primary">
						<i class="bi bi-arrow-left me-2"></i>Kembali ke Jadwal
					</a>
				</div>
			<?php else: ?>
				<div class="table-responsive" style="max-height: 70vh; overflow-x: auto; overflow-y: auto;">
					<table class="table table-hover table-bordered mb-0 table-sm" id="nilaiTable">
						<thead class="table-dark sticky-top">
							<tr>
								<th class="text-center align-middle" style="width: 60px; min-width: 60px;" rowspan="2">No</th>
								<th class="align-middle" style="width: 130px; min-width: 130px;" rowspan="2">
									<div class="d-flex align-items-center">
										<i class="bi bi-hash me-2"></i>NIM
									</div>
								</th>
								<th class="align-middle" style="min-width: 200px;" rowspan="2">
									<div class="d-flex align-items-center">
										<i class="bi bi-person me-2"></i>Nama Mahasiswa
									</div>
								</th>
								<?php
								$tahap_count = count($teknik_by_tahap);
								$tahap_index = 0;
								?>
								<?php foreach ($teknik_by_tahap as $tahap => $tahap_items): ?>
									<?php
									$tahap_index++;
									$is_last_tahap = ($tahap_index === $tahap_count);
									?>
									<th class="text-center align-middle bg-primary bg-opacity-25 <?= $is_last_tahap ? '' : 'tahap-border-right' ?>" colspan="<?= count($tahap_items) ?>">
										<?= esc($tahap) ?>
									</th>
								<?php endforeach; ?>
								<th class="text-center align-middle bg-success bg-opacity-10" style="width: 120px; min-width: 120px;" rowspan="2">
									<div class="d-flex flex-column align-items-center">
										<span class="fw-bold">Nilai Huruf</span>
									</div>
								</th>
								<th class="text-center align-middle bg-warning bg-opacity-10" style="width: 150px; min-width: 150px;" rowspan="2">
									<div class="d-flex flex-column align-items-center">
										<span class="fw-bold">Keterangan</span>
									</div>
								</th>
							</tr>
							<tr>
								<?php
								$tahap_keys = array_keys($teknik_by_tahap);
								$last_tahap_key = end($tahap_keys);
								?>
								<?php foreach ($teknik_by_tahap as $tahap => $tahap_items): ?>
									<?php
									$item_count = count($tahap_items);
									$item_index = 0;
									$is_last_tahap_group = ($tahap === $last_tahap_key);
									?>
									<?php foreach ($tahap_items as $item): ?>
										<?php
										$item_index++;
										$is_last_in_group = ($item_index === $item_count);
										$show_border = $is_last_in_group && !$is_last_tahap_group;
										// Build tooltip with week and bobot
										$cpmk_display = $item['kode_cpmk'] ?? $item['cpmk_code'] ?? 'N/A';
										$tooltip = esc($item['teknik_label']) . " - Minggu " . $item['minggu'] . " - CPMK: " . esc($cpmk_display) . " (" . number_format($item['bobot'], 1) . "%)";
										?>
										<th class="text-center align-middle <?= $show_border ? 'tahap-border-right' : '' ?>" style="width: 110px; min-width: 110px;"
											title="<?= $tooltip ?>"
											data-bs-toggle="tooltip">
											<div class="d-flex flex-column align-items-center">
												<small class="fw-bold" style="font-size: 0.75rem; line-height: 1.2;">
													<?php
													// Abbreviate long names
													$label = $item['teknik_label'];
													if (strlen($label) > 20) {
														$label = substr($label, 0, 17) . '...';
													}
													echo esc($label);
													?>
												</small>
												<small class="opacity-75" style="font-size: 0.65rem;">
													Minggu: <?= $item['minggu'] ?><br />
													<?= esc($item['kode_cpmk'] ?? $item['cpmk_code'] ?? '') ?>
												</small>
												<span class="badge bg-success" style="font-size: 0.65rem;">
													<?= number_format($item['bobot'], 1) ?>%
												</span>
											</div>
										</th>
									<?php endforeach; ?>
								<?php endforeach; ?>
							</tr>
						</thead>
						<tbody>
							<?php
							// Helper function to calculate keterangan based on grade
							$getKeterangan = function ($grade) {
								$failingGrades = ['B', 'BC', 'C', 'D', 'E'];
								if (in_array(strtoupper($grade), $failingGrades)) {
									return 'TM'; // Tidak Memenuhi
								}
								return 'Lulus';
							};
							?>
							<?php foreach ($mahasiswa_list as $index => $mahasiswa) : ?>
								<?php
								// Get final scores for this student
								$nilai_akhir = $final_scores_map[$mahasiswa['id']]['nilai_akhir'] ?? 0;
								$nilai_huruf = $final_scores_map[$mahasiswa['id']]['nilai_huruf'] ?? '-';
								$keterangan = $getKeterangan($nilai_huruf);
								?>
								<tr class="<?= $index % 2 === 0 ? 'bg-light bg-opacity-50' : '' ?>">
									<td class="text-center align-middle fw-bold text-muted">
										<?= $index + 1 ?>
									</td>
									<td class="align-middle">
										<span class="fw-semibold text-primary"><?= esc($mahasiswa['nim']) ?></span>
									</td>
									<td class="align-middle">
										<div class="d-flex align-items-center">
											<span><?= esc($mahasiswa['nama_lengkap']) ?></span>
										</div>
									</td>
									<?php
									// Build a map to know which columns are the last in their tahap group (but not the very last group)
									$last_in_group = [];
									$tahap_keys = array_keys($teknik_by_tahap);
									$last_tahap_key = end($tahap_keys);
									foreach ($teknik_by_tahap as $tahap => $tahap_items) {
										if (!empty($tahap_items) && $tahap !== $last_tahap_key) {
											$last_item = end($tahap_items);
											// Use rps_mingguan_id + teknik_key as unique identifier
											$last_in_group[$last_item['rps_mingguan_id'] . '_' . $last_item['teknik_key']] = true;
										}
									}
									?>
									<?php foreach ($teknik_list as $item) : ?>
										<?php
										$unique_key = $item['rps_mingguan_id'] . '_' . $item['teknik_key'];
										$is_last = isset($last_in_group[$unique_key]);
										?>
										<td class="align-middle text-center <?= $is_last ? 'tahap-border-right' : '' ?>">
											<?php
											$score = $existing_scores[$mahasiswa['id']][$item['rps_mingguan_id']][$item['teknik_key']] ?? '';
											echo $score !== '' ? number_format($score, 2) : '-';
											?>
										</td>
									<?php endforeach; ?>
									<td class="align-middle text-center">
										<span class="fw-bold" style="font-size: 1rem; min-width: 50px;">
											<?= esc($nilai_huruf) ?>
										</span>
									</td>
									<td class="align-middle text-center">
										<span class="fw-bold <?= $keterangan == 'Lulus' ? '' : 'text-danger' ?>" style="font-size: 1rem;">
											<?= esc($keterangan) ?>
										</span>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<div class="card-footer bg-light border-0 py-3">
					<div class="row align-items-center">
						<div class="col-12">
							<div class="d-flex align-items-center gap-3 justify-content-center">
								<small class="text-muted">
									<i class="bi bi-info-circle me-1"></i>
									Total: <?= count($mahasiswa_list) ?> mahasiswa dengan <?= count($teknik_list) ?> teknik penilaian
								</small>
								<small class="text-muted">
									<i class="bi bi-lock me-1"></i>
									Mode tampilan saja
								</small>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
	.sticky-top {
		position: sticky;
		top: 0;
		z-index: 1020;
	}

	.table-responsive {
		border: 1px solid #dee2e6;
		border-radius: 0;
		overflow-x: auto !important;
		overflow-y: auto !important;
		-webkit-overflow-scrolling: touch;
		width: 100%;
		max-width: 100%;
	}

	/* Prevent table from expanding container */
	#nilaiTable {
		width: max-content;
		max-width: none;
	}

	/* Sticky first columns for better UX */
	#nilaiTable thead th:nth-child(1),
	#nilaiTable thead th:nth-child(2),
	#nilaiTable thead th:nth-child(3),
	#nilaiTable tbody td:nth-child(1),
	#nilaiTable tbody td:nth-child(2),
	#nilaiTable tbody td:nth-child(3) {
		position: sticky;
		background-color: white;
		z-index: 10;
	}

	#nilaiTable thead th:nth-child(1),
	#nilaiTable tbody td:nth-child(1) {
		left: 0;
	}

	#nilaiTable thead th:nth-child(2),
	#nilaiTable tbody td:nth-child(2) {
		left: 60px;
	}

	#nilaiTable thead th:nth-child(3),
	#nilaiTable tbody td:nth-child(3) {
		left: 190px;
	}

	#nilaiTable thead th {
		z-index: 20;
		background-color: #212529;
	}

	#nilaiTable tbody tr:nth-of-type(even) td:nth-child(-n+3) {
		background-color: rgba(0, 0, 0, 0.05);
	}

	.card {
		transition: all 0.3s ease;
	}

	/* Border to separate tahap penilaian groups */
	#nilaiTable th.tahap-border-right,
	#nilaiTable td.tahap-border-right {
		border-right: 4px solid #ffc107 !important;
	}

	/* Sticky columns for Nilai Huruf and Keterangan (last two columns) */
	#nilaiTable thead th:nth-last-child(1),
	#nilaiTable thead th:nth-last-child(2),
	#nilaiTable tbody td:nth-last-child(1),
	#nilaiTable tbody td:nth-last-child(2) {
		position: sticky;
		background-color: white;
		z-index: 10;
	}

	#nilaiTable thead th:nth-last-child(1),
	#nilaiTable tbody td:nth-last-child(1) {
		right: 0;
	}

	#nilaiTable thead th:nth-last-child(2),
	#nilaiTable tbody td:nth-last-child(2) {
		right: 150px;
	}

	#nilaiTable thead th:nth-last-child(1),
	#nilaiTable thead th:nth-last-child(2) {
		z-index: 20;
	}

	#nilaiTable tbody tr:nth-of-type(even) td:nth-last-child(1),
	#nilaiTable tbody tr:nth-of-type(even) td:nth-last-child(2) {
		background-color: rgba(0, 0, 0, 0.05);
	}

	/* Ensure body doesn't scroll horizontally */
	body {
		overflow-x: hidden;
	}

	@media (max-width: 768px) {
		.table-responsive {
			max-height: 60vh;
		}
	}

	/* Grade color coding - consistent with DPNA */
	.grade-a {
		background-color: #d4edda !important;
		font-weight: bold;
	}

	.grade-ab {
		background-color: #d1ecf1 !important;
	}

	.grade-b {
		background-color: #fff3cd !important;
	}

	.grade-bc {
		background-color: #ffe0b2 !important;
	}

	.grade-c {
		background-color: #f8d7da !important;
	}

	.grade-d {
		background-color: #f5c6cb !important;
		font-weight: bold;
	}

	.grade-e {
		background-color: #f8d7da !important;
		font-weight: bold;
		color: #721c24;
	}

	/* Ensure body doesn't scroll horizontally */
	body {
		overflow-x: hidden;
	}

	@media (max-width: 768px) {
		.table-responsive {
			max-height: 60vh;
		}
	}

	@media print {
		.table-responsive {
			overflow: visible !important;
			max-height: none !important;
			box-shadow: none;
			border: 1px solid #ddd;
		}

		#nilaiTable thead th {
			position: static;
		}

		canvas {
			display: none !important;
		}

		.btn {
			display: none !important;
		}
	}
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

<script>
	// Prepare data for Grade Distribution Chart
	<?php
	// Calculate grade distribution from the existing data
	$grade_distribution = [];
	if (!empty($mahasiswa_list) && !empty($final_scores_map)) {
		foreach ($mahasiswa_list as $mahasiswa) {
			$nilai_huruf = $final_scores_map[$mahasiswa['id']]['nilai_huruf'] ?? null;
			if ($nilai_huruf && $nilai_huruf !== '-') {
				if (!isset($grade_distribution[$nilai_huruf])) {
					$grade_distribution[$nilai_huruf] = 0;
				}
				$grade_distribution[$nilai_huruf]++;
			}
		}
	}

	// Get grade order from database
	$gradeConfigModel = new \App\Models\GradeConfigModel();
	$all_grades = $gradeConfigModel->orderBy('min_score', 'DESC')->findAll();
	$grade_order = array_column($all_grades, 'grade_letter');

	$sorted_grades = [];
	foreach ($grade_order as $grade) {
		if (isset($grade_distribution[$grade])) {
			$sorted_grades[$grade] = $grade_distribution[$grade];
		}
	}
	// Add any other grades not in the database configuration
	foreach ($grade_distribution as $grade => $count) {
		if (!in_array($grade, $grade_order)) {
			$sorted_grades[$grade] = $count;
		}
	}

	// Generate dynamic colors based on grade configuration
	// Create a color map from grade data (green for high scores, red for low scores)
	$grade_colors = [];
	$total_grades = count($all_grades);
	foreach ($all_grades as $index => $grade_config) {
		// Calculate color position (0 = best grade, 1 = worst grade)
		$position = $total_grades > 1 ? $index / ($total_grades - 1) : 0;

		// Interpolate between green (good) and red (bad)
		// Green: rgb(40, 167, 69), Yellow: rgb(255, 193, 7), Red: rgb(220, 53, 69)
		if ($position < 0.5) {
			// Green to Yellow
			$factor = $position * 2;
			$r = (int)(40 + ($factor * (255 - 40)));
			$g = (int)(167 + ($factor * (193 - 167)));
			$b = (int)(69 + ($factor * (7 - 69)));
		} else {
			// Yellow to Red
			$factor = ($position - 0.5) * 2;
			$r = (int)(255 + ($factor * (220 - 255)));
			$g = (int)(193 + ($factor * (53 - 193)));
			$b = (int)(7 + ($factor * (69 - 7)));
		}

		$grade_colors[$grade_config['grade_letter']] = "rgba($r, $g, $b, 0.8)";
	}
	?>

	const gradeLabels = <?= json_encode(array_keys($sorted_grades)) ?>;
	const gradeCounts = <?= json_encode(array_values($sorted_grades)) ?>;
	const gradeColors = <?= json_encode($grade_colors) ?>;

	// Grade Distribution Chart
	const ctxGrade = document.getElementById('gradeDistributionChart');
	if (ctxGrade && gradeLabels.length > 0) {
		const backgroundColor = gradeLabels.map(grade => gradeColors[grade] || 'rgba(102, 126, 234, 0.8)');
		const borderColor = gradeLabels.map(grade => {
			const bgColor = gradeColors[grade] || 'rgba(102, 126, 234, 0.8)';
			return bgColor.replace('0.8', '1');
		});

		new Chart(ctxGrade, {
			type: 'bar',
			data: {
				labels: gradeLabels,
				datasets: [{
					label: 'Jumlah Mahasiswa',
					data: gradeCounts,
					backgroundColor: backgroundColor,
					borderColor: borderColor,
					borderWidth: 2,
					borderRadius: 8
				}]
			},
			plugins: [ChartDataLabels],
			options: {
				responsive: true,
				maintainAspectRatio: true,
				plugins: {
					legend: {
						display: false
					},
					title: {
						display: true,
						text: 'Distribusi Nilai Huruf Mahasiswa',
						font: {
							size: 16,
							weight: 'bold'
						}
					},
					datalabels: {
						anchor: 'end',
						align: 'top',
						formatter: function(value, context) {
							return value;
						},
						font: {
							weight: 'bold',
							size: 12
						},
						color: '#333'
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						ticks: {
							stepSize: 1,
							callback: function(value) {
								if (Number.isInteger(value)) {
									return value;
								}
							}
						},
						title: {
							display: true,
							text: 'Jumlah Mahasiswa',
							font: {
								size: 14,
								weight: 'bold'
							}
						}
					},
					x: {
						title: {
							display: true,
							text: 'Nilai Huruf',
							font: {
								size: 14,
								weight: 'bold'
							}
						}
					}
				}
			}
		});
	}
</script>
<?= $this->endSection() ?>
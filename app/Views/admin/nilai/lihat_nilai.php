<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="container-fluid px-4" style="overflow-x: hidden;">
	<div class="row mb-4">
		<div class="col-12">
			<div class="d-flex justify-content-between align-items-center">
				<div>
					<h2 class="fw-bold mb-1">Lihat Nilai</h2>
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
				<div class="col-md-3">
					<div class="d-flex align-items-center">
						<div>
							<small class="text-muted">Mata Kuliah</small>
							<h6 class="mb-0 fw-semibold"><?= esc($jadwal['nama_mk']) ?></h6>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="d-flex align-items-center">
						<div>
							<small class="text-muted">Kelas</small>
							<h6 class="mb-0 fw-semibold"><?= esc($jadwal['kelas']) ?></h6>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="d-flex align-items-center">
						<div>
							<small class="text-muted">Tahun Akademik</small>
							<h6 class="mb-0 fw-semibold"><?= esc($jadwal['tahun_akademik']) ?></h6>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="d-flex align-items-center">
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
				<div class="modern-table-wrapper" style="max-height: 70vh; overflow-y: auto;">
					<div class="scroll-indicator"></div>
					<table class="modern-table" id="nilaiTable">
						<thead>
							<tr>
								<th class="sticky-col text-center align-middle" rowspan="2">No</th>
								<th class="sticky-col align-middle" rowspan="2">NIM</th>
								<th class="sticky-col align-middle" rowspan="2">Nama Mahasiswa</th>
								<?php
								$tahap_count = count($teknik_by_tahap);
								$tahap_index = 0;
								?>
								<?php foreach ($teknik_by_tahap as $tahap => $tahap_items): ?>
									<?php
									$tahap_index++;
									$is_last_tahap = ($tahap_index === $tahap_count);
									?>
									<th class="text-center align-middle bg-secondary bg-opacity-10 <?= $is_last_tahap ? '' : 'tahap-border-right' ?>" colspan="<?= count($tahap_items) ?>">
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
							// Build a map of grade => is_passing from the database configuration
							$grade_passing_map = [];
							if (isset($grade_config) && is_array($grade_config)) {
								foreach ($grade_config as $grade_item) {
									$grade_passing_map[$grade_item['grade_letter']] = ($grade_item['is_passing'] == 1);
								}
							}

							// Helper function to calculate keterangan based on grade using dynamic config
							$getKeterangan = function ($grade) use ($grade_passing_map) {
								// Check if grade exists in the database configuration
								if (isset($grade_passing_map[$grade])) {
									return $grade_passing_map[$grade] ? 'Lulus' : 'Tidak Lulus';
								}
								// Fallback for unknown grades
								return '-';
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
									<td class="sticky-col text-center align-middle fw-bold text-muted">
										<?= $index + 1 ?>
									</td>
									<td class="sticky-col align-middle">
										<span class="fw-semibold"><?= esc($mahasiswa['nim']) ?></span>
									</td>
									<td class="sticky-col align-middle">
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
										<span class="fw-bold <?= $keterangan == 'Lulus' ? 'text-success' : 'text-danger' ?>" style="font-size: 1rem;">
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
	#nilaiTable th.tahap-border-right,
	#nilaiTable td.tahap-border-right {
		border-right: 4px solid #ffc107 !important;
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
		.modern-table-wrapper {
			max-height: 60vh;
		}
	}

	@media print {
		.modern-table-wrapper {
			overflow: visible !important;
			max-height: none !important;
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

	// Use grade configuration from controller
	$grade_order = isset($grade_config) ? array_column($grade_config, 'grade_letter') : [];

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
	if (isset($grade_config) && is_array($grade_config)) {
		$total_grades = count($grade_config);
		foreach ($grade_config as $index => $grade_item) {
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

			$grade_colors[$grade_item['grade_letter']] = "rgba($r, $g, $b, 0.8)";
		}
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

	// Handle scroll indicator for modern table
	document.addEventListener('DOMContentLoaded', function() {
		const tableWrapper = document.querySelector('.modern-table-wrapper');
		if (tableWrapper) {
			function checkScroll() {
				const hasHorizontalScroll = tableWrapper.scrollWidth > tableWrapper.clientWidth;
				const isScrolledToEnd = tableWrapper.scrollLeft >= (tableWrapper.scrollWidth - tableWrapper.clientWidth - 10);

				if (hasHorizontalScroll && !isScrolledToEnd) {
					tableWrapper.classList.add('has-scroll');
				} else {
					tableWrapper.classList.remove('has-scroll');
				}
			}

			// Check on load and resize
			checkScroll();
			window.addEventListener('resize', checkScroll);
			tableWrapper.addEventListener('scroll', checkScroll);
		}

		// Dynamic sticky column positioning
		const table = document.getElementById('nilaiTable');
		if (table) {
			function updateStickyPositions() {
				// Get all sticky columns from the first row (header)
				const headerRow = table.querySelector('thead tr');
				if (!headerRow) return;

				const stickyColumns = headerRow.querySelectorAll('.sticky-col');
				let cumulativeLeft = 0;

				stickyColumns.forEach((col, index) => {
					// Set the left position for this column
					const varName = `--sticky-col-${index + 1}-left`;
					table.style.setProperty(varName, `${cumulativeLeft}px`);

					// Add this column's width to the cumulative total for the next column
					cumulativeLeft += col.offsetWidth;
				});
			}

			// Update positions on load
			updateStickyPositions();

			// Update on window resize with debouncing for performance
			let resizeTimeout;
			window.addEventListener('resize', function() {
				clearTimeout(resizeTimeout);
				resizeTimeout = setTimeout(updateStickyPositions, 100);
			});

			// Update after fonts load (can affect column widths)
			if (document.fonts && document.fonts.ready) {
				document.fonts.ready.then(updateStickyPositions);
			}
		}
	});
</script>
<?= $this->endSection() ?>
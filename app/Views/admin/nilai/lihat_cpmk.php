<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<style>
	/* Page-specific color variables */
	:root {
		--color-good: #f0fdf4;
		--color-good-text: #15803d;
		--color-good-border: #86efac;
		--color-medium: #fef3c7;
		--color-medium-text: #b45309;
		--color-medium-border: #fcd34d;
		--color-low: #fef2f2;
		--color-low-text: #dc2626;
		--color-low-border: #fca5a5;
		--color-primary: #3b82f6;
		--color-primary-light: #dbeafe;
		--shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
		--shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
	}

	/* CPMK-specific styles */
	.cpmk-header {
		color: white;
		padding: 2rem;
		border-radius: 0.75rem;
		margin-bottom: 2rem;
	}

	.stats-card {
		border-left: 4px solid #667eea;
		transition: all 0.3s ease;
	}

	.stats-card:hover {
		transform: translateY(-2px);
		box-shadow: var(--shadow-md);
	}

	/* Score background styles */
	.score-good,
	.score-medium,
	.score-low {
		background-color: transparent;
	}

	/* Modern Badges */
	.badge-score {
		background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
		color: white;
		font-weight: 600;
		padding: 0.375rem 0.75rem;
		border-radius: 0.5rem;
		font-size: 0.875rem;
		display: inline-block;
		box-shadow: 0 2px 4px rgba(59, 130, 246, 0.3);
		letter-spacing: 0.025em;
	}

	.badge-capaian {
		font-weight: 600;
		padding: 0.25rem 0.625rem;
		border-radius: 0.375rem;
		font-size: 0.75rem;
		display: inline-block;
		letter-spacing: 0.025em;
	}

	.badge-capaian-low {
		background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
		color: white;
		box-shadow: 0 2px 4px rgba(239, 68, 68, 0.3);
	}

	.badge-capaian-medium {
		background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
		color: #78350f;
		box-shadow: 0 2px 4px rgba(251, 191, 36, 0.3);
	}

	/* Fix missing border between Huruf and Keterangan */
	.modern-table thead tr:nth-child(2) th:last-child {
		border-right: 1px solid var(--modern-table-border) !important;
	}
</style>

<div class="container-fluid px-4">
	<div class="cpmk-header bg-primary shadow-sm mb-4">
		<div class="d-flex justify-content-between align-items-center">
			<div>
				<p class="mb-0 display-6 fw-bold">
					<?= esc($jadwal['nama_mk']) ?> - Kelas <?= esc($jadwal['kelas']) ?>
				</p>
				<small><?= esc($jadwal['tahun_akademik']) ?></small>
			</div>
			<div class="text-end">
				<a href="<?= base_url('admin/nilai/export-cpmk-excel/' . $jadwal['id']) ?>" class="btn btn-success me-2">
					<i class="bi bi-file-earmark-excel"></i> Export to Excel
				</a>
				<a href="<?= base_url('admin/nilai') ?>" class="btn btn-light">
					<i class="bi bi-arrow-left"></i> Kembali
				</a>
			</div>
		</div>
	</div>

	<!-- Validation Status Banner -->
	<?php if (isset($jadwal['is_nilai_validated']) && $jadwal['is_nilai_validated'] == 1): ?>
		<div class="alert alert-success mb-4" role="alert">
			<i class="bi bi-check-circle-fill me-2"></i>
			<strong>Nilai Tervalidasi</strong> - Data ini telah divalidasi dan terkunci
			<?php if (isset($jadwal['validated_at'])): ?>
				pada <?= date('d/m/Y H:i', strtotime($jadwal['validated_at'])) ?>
			<?php endif; ?>
		</div>
	<?php else: ?>
		<div class="alert alert-warning mb-4" role="alert">
			<i class="bi bi-exclamation-triangle-fill me-2"></i>
			<strong>Belum Tervalidasi</strong> - Nilai masih dapat diubah
		</div>
	<?php endif; ?>

	<!-- CPMK Achievement Charts -->
	<div class="row mb-4">
		<div class="col-12">
			<div id="cpmkChartContainer"></div>
		</div>
	</div>

	<!-- CPMK Scores Table -->
	<div class="shadow-sm">
		<div class="p-0">
			<?php if (empty($mahasiswa_list)): ?>
				<div class="text-center text-muted py-5">
					<i class="bi bi-inbox fs-1"></i>
					<p class="mt-3">Tidak ada data mahasiswa</p>
				</div>
			<?php elseif (empty($cpmk_list)): ?>
				<div class="text-center text-muted py-5">
					<i class="bi bi-exclamation-circle fs-1"></i>
					<p class="mt-3">Tidak ada CPMK yang terdefinisi untuk mata kuliah ini</p>
					<p class="small">Silakan lengkapi RPS terlebih dahulu</p>
				</div>
			<?php else: ?>
				<div class="modern-table-wrapper">
					<div class="scroll-indicator"></div>
					<table class="modern-table" id="cpmkTable">
						<thead>
							<tr>
								<th class="text-center sticky-col" rowspan="2">No</th>
								<th class="sticky-col" rowspan="2">NIM</th>
								<th class="sticky-col" rowspan="2">Nama Mahasiswa</th>
								<th class="text-center" colspan="<?= count($cpmk_list) ?>">Nilai CPMK</th>
								<th class="text-center" colspan="2" style="width: 150px;">Nilai Akhir</th>
								<th class="text-center" rowspan="2" style="width: 100px;">Keterangan</th>
							</tr>
							<tr>
								<?php foreach ($cpmk_list as $cpmk): ?>
									<th class="text-center score-cell" title="<?= esc($cpmk['deskripsi']) ?>">
										<?php
										// Split CPMK code into prefix and number
										$cpmk_code = $cpmk['kode_cpmk'];
										if (preg_match('/^(CPMK)(.+)$/i', $cpmk_code, $matches)) {
											$cpmk_prefix = $matches[1];
											$cpmk_number = $matches[2];
										} else {
											$cpmk_prefix = $cpmk_code;
											$cpmk_number = '';
										}
										?>
										<div class="fw-bold"><?= esc($cpmk_prefix) ?></div>
										<?php if ($cpmk_number): ?>
											<div class="fw-bold"><?= esc($cpmk_number) ?></div>
										<?php endif; ?>
										<small class="text-muted">(<?= esc($cpmk['bobot_cpmk']) ?>%)</small>
									</th>
								<?php endforeach; ?>
								<th class="text-center" style="width: 80px;">Angka</th>
								<th class="text-center" style="width: 70px;">Huruf</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($mahasiswa_list as $index => $mahasiswa): ?>
								<tr>
									<td class="text-center sticky-col"><?= $index + 1 ?></td>
									<td class="sticky-col"><small><?= esc($mahasiswa['nim']) ?></small></td>
									<td class="sticky-col"><small><?= esc($mahasiswa['nama_lengkap']) ?></small></td>

									<?php
									$student_scores = [];
									foreach ($cpmk_list as $cpmk):
										$score = $existing_scores[$mahasiswa['id']][$cpmk['id']] ?? null;
										if ($score !== null && $score !== '') {
											$student_scores[] = (float)$score;
										}

										// Determine cell color based on score
										$cell_class = '';
										if ($score !== null && $score !== '') {
											if ($score >= (($passing_threshold ?? 65) + 10)) {
												$cell_class = 'score-good';
											} elseif ($score >= ($passing_threshold ?? 65)) {
												$cell_class = 'score-medium';
											} else {
												$cell_class = 'score-low';
											}
										}
									?>
										<td class="score-cell <?= $cell_class ?>">
											<?php if ($score !== null && $score !== ''): ?>
												<div class="d-flex flex-column align-items-center gap-1">
													<span class="badge-score">
														<?= number_format($score, 2) ?>
													</span>
													<?php
													$capaian = ($score / $cpmk['bobot_cpmk']) * 100;
													$capaian_class = $capaian < 60 ? 'badge-capaian-low' : 'badge-capaian-medium';
													?>
													<span class="badge-capaian <?= $capaian_class ?>">
														<?= number_format($capaian, 2) ?>%
													</span>
												</div>
											<?php else: ?>
												<span class="text-muted">-</span>
											<?php endif; ?>
										</td>
									<?php endforeach; ?>

									<!-- Calculate sum and grade -->
									<?php
									if (count($student_scores) > 0) {
										$total = array_sum($student_scores);

										// Use dynamic grade configuration from database
										$gradeConfigModel = new \App\Models\GradeConfigModel();
										$grade_data = $gradeConfigModel->getGradeByScore($total);

										$nilai_huruf = $grade_data ? $grade_data['grade_letter'] : 'E';
										$is_passing = $grade_data ? (bool)$grade_data['is_passing'] : false;
										$keterangan = $is_passing ? 'Lulus' : 'Tidak Lulus';
									}
									?>
									<!-- Nilai Akhir Angka -->
									<td class="text-center">
										<?php if (count($student_scores) > 0): ?>
											<strong><?= number_format($total, 2) ?></strong>
										<?php else: ?>
											<span class="text-muted">-</span>
										<?php endif; ?>
									</td>
									<!-- Nilai Akhir Huruf -->
									<td class="text-center">
										<?php if (count($student_scores) > 0): ?>
											<strong><?= esc($nilai_huruf) ?></strong>
										<?php else: ?>
											<span class="text-muted">-</span>
										<?php endif; ?>
									</td>
									<!-- Keterangan -->
									<td class="text-center">
										<?php if (count($student_scores) > 0): ?>
											<?= esc($keterangan) ?>
										<?php else: ?>
											<span class="text-muted">-</span>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<!-- Badge Legend -->
	<div class="card shadow-sm mb-3 mt-4">
		<div class="card-body">
			<div class="row align-items-center">
				<div class="col-md-3">
					<h6 class="mb-0"><i class="bi bi-info-circle"></i> Keterangan:</h6>
				</div>
				<div class="col-md-9">
					<div class="d-flex flex-wrap gap-4 align-items-center">
						<div>
							<span class="badge-score text-primary">.</span>
							<small class="text-muted ms-2">= Skor CPMK</small>
						</div>
						<div>
							<span class="badge-capaian text-danger badge-capaian-low">.</span>
							<small class="text-muted ms-2">= Capaian CPMK &lt; 60%</small>
						</div>
						<div>
							<span class="badge-capaian text-warning badge-capaian-medium">.</span>
							<small class="text-muted ms-2">= Capaian CPMK ≥ 60%</small>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Action Buttons -->
	<div class="d-flex gap-2 justify-content-end mt-4 mb-4">
		<a href="<?= base_url('admin/nilai/export-cpmk-excel/' . $jadwal['id']) ?>" class="btn btn-success">
			<i class="bi bi-file-earmark-excel"></i> Export to Excel
		</a>
		<a href="<?= base_url('admin/nilai') ?>" class="btn btn-secondary">
			<i class="bi bi-arrow-left"></i> Kembali ke Daftar
		</a>
	</div>
</div>

<style media="print">
	.btn,
	.alert,
	.cpmk-header .btn {
		display: none !important;
	}

	.cpmk-header {
		background: #667eea !important;
		-webkit-print-color-adjust: exact;
		print-color-adjust: exact;
	}

	.card {
		border: 1px solid #dee2e6 !important;
		page-break-inside: avoid;
	}

	canvas {
		display: none !important;
	}

	.badge-score,
	.badge-capaian,
	.badge-capaian-low,
	.badge-capaian-medium {
		-webkit-print-color-adjust: exact;
		print-color-adjust: exact;
	}
</style>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<!-- Modern Chart Component -->
<script src="<?= base_url('js/modern-chart-component.js') ?>"></script>

<script>
	// Prepare data for CPMK Average Capaian Chart
	<?php
	$cpmk_data = [];
	foreach ($cpmk_list as $cpmk) {
		$cpmk_data[] = [
			'label' => $cpmk['kode_cpmk'],
			'avgScore' => $cpmk_stats[$cpmk['id']]['avg'],
			'bobot' => $cpmk['bobot_cpmk'],
			'capaian' => ($cpmk_stats[$cpmk['id']]['avg'] / $cpmk['bobot_cpmk']) * 100
		];
	}
	?>
	const cpmkLabels = <?= json_encode(array_map(function ($cpmk) {
							return $cpmk['kode_cpmk'];
						}, $cpmk_list)) ?>;
	const cpmkCapaianData = <?= json_encode($cpmk_data) ?>;

	// Initialize Modern Chart Component when DOM is ready
	let cpmkChart;

	function initializeCpmkChart() {
		cpmkChart = new ModernChartComponent({
			containerId: 'cpmkChartContainer',
			chartData: {
				labels: cpmkLabels,
				data: cpmkCapaianData.map(d => d.capaian)
			},
			config: {
				title: 'Grafik Capaian CPMK',
				subtitle: 'Rata-rata capaian CPMK (Jumlah Persentase Capaian CPMK / Jumlah Mahasiswa)',
				type: 'bar',
				passingThreshold: 60,
				showExportButton: true,
				showSubtitle: true,
				height: 80,
				animationDuration: 1500,
				exportFilename: 'capaian-cpmk-<?= esc($jadwal['nama_mk']) ?>-<?= esc($jadwal['kelas']) ?>.png',
				labels: {
					yAxis: 'Capaian (%)',
					xAxis: 'Kode CPMK'
				}
			}
		});

		// Render the chart
		cpmkChart.render();
	}

	// Handle scroll indicator for modern table
	document.addEventListener('DOMContentLoaded', function() {
		// Initialize the chart
		initializeCpmkChart();

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
		const table = document.getElementById('cpmkTable');
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

<?= $this->section('css') ?>
<?= $this->endSection() ?>
<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<style>
	.cpmk-header {
		background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
		color: white;
		padding: 2rem;
		border-radius: 0.5rem;
		margin-bottom: 2rem;
	}

	.stats-card {
		border-left: 4px solid #667eea;
		transition: transform 0.2s;
	}

	.stats-card:hover {
		transform: translateY(-2px);
		box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
	}

	.score-cell {
		min-width: 80px;
		text-align: center;
	}

	.score-good {
		background-color: #d1e7dd;
		color: #0f5132;
	}

	.score-medium {
		background-color: #fff3cd;
		color: #856404;
	}

	.score-low {
		background-color: #f8d7da;
		color: #842029;
	}

	.sticky-col {
		position: sticky;
		left: 0;
		background-color: white;
		z-index: 10;
	}

	.sticky-header {
		position: sticky;
		top: 0;
		z-index: 20;
		background-color: #f8f9fa;
	}

	.badge-score {
		background-color: rgba(0, 140, 254, 0.75);
		color: white;
		font-weight: 600;
		padding: 0.4rem 0.8rem;
		border-radius: 0.375rem;
		font-size: 0.875rem;
		display: inline-block;
		margin-bottom: 0.25rem;
	}

	.badge-capaian {
		font-weight: 600;
		padding: 0.3rem 0.6rem;
		border-radius: 0.25rem;
		font-size: 0.75rem;
		display: inline-block;
	}

	.badge-capaian-low {
		background-color: rgba(220, 53, 69, 0.85);
		color: white;
	}

	.badge-capaian-medium {
		background-color: rgba(255, 193, 7, 0.85);
		color: #000;
	}
</style>

<div class="container-fluid px-4">
	<div class="cpmk-header">
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

	<!-- CPMK Statistics -->
	<div class="card shadow-sm mb-4">
		<div class="card-header bg-light">
			<h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Statistik CPMK</h5>
		</div>
		<div class="card-body p-0">
			<div class="table-responsive">
				<table class="table table-bordered table-hover mb-0 align-middle">
					<thead class="table-light">
						<tr>
							<th class="text-center" style="width: 50px;">No</th>
							<th style="min-width: 120px;">Kode CPMK</th>
							<th style="min-width: 250px;">Deskripsi</th>
							<th class="text-center" style="width: 100px;">Bobot</th>
							<th class="text-center" style="width: 120px;">Rata-rata</th>
							<th class="text-center" style="width: 120px;">Capaian</th>
							<th class="text-center" style="width: 100px;">Terisi</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($cpmk_list as $index => $cpmk): ?>
							<?php
							$avg_capaian = ($cpmk_stats[$cpmk['id']]['avg'] / $cpmk['bobot_cpmk']) * 100;
							?>
							<tr>
								<td class="text-center"><?= $index + 1 ?></td>
								<td class="text-center"><strong class="text-primary"><?= esc($cpmk['kode_cpmk']) ?></strong></td>
								<td><small class="text-dark"><?= esc($cpmk['deskripsi']) ?></small></td>
								<td class="text-center">
									<span class="badge bg-info text-dark me-1 mb-1"><?= esc($cpmk['bobot_cpmk']) ?>%</span>
								</td>
								<td class="text-center">
									<strong><?= $cpmk_stats[$cpmk['id']]['avg'] ?></strong>
								</td>
								<td class="text-center">
									<strong><?= number_format($avg_capaian, 2) ?>%</strong>
								</td>
								<td class="text-center">
									<strong><?= $cpmk_stats[$cpmk['id']]['count'] ?></strong> / <?= count($mahasiswa_list) ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<!-- CPMK Achievement Charts -->
	<div class="row mb-4">
		<div class="col-12">
			<div class="card shadow-sm">
				<div class="card-header bg-light">
					<h5 class="mb-0"><i class="bi bi-bar-chart-line"></i> Grafik Capaian CPMK</h5>
				</div>
				<div class="card-body">
					<canvas id="cpmkCapaianChart" style="max-height: 400px;"></canvas>
				</div>
			</div>
		</div>
	</div>

	<!-- CPMK Scores Table -->
	<div class="card shadow-sm">
		<div class="card-header bg-light">
			<h5 class="mb-0"><i class="bi bi-table"></i> Tabel Nilai CPMK</h5>
		</div>
		<div class="card-body p-0">
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
				<div class="table-responsive">
					<table class="table table-bordered table-hover mb-0 align-middle">
						<thead class="table-light sticky-header">
							<tr>
								<th class="text-center sticky-col" rowspan="2" style="width: 50px;">No</th>
								<th class="sticky-col" rowspan="2" style="min-width: 150px;">NIM</th>
								<th class="sticky-col" rowspan="2" style="min-width: 200px;">Nama Mahasiswa</th>
								<th class="text-center" colspan="<?= count($cpmk_list) ?>">Nilai CPMK</th>
								<th class="text-center" rowspan="2" style="width: 100px;">Nilai Akhir MK</th>
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
											if ($score >= 75) {
												$cell_class = 'score-good';
											} elseif ($score >= 60) {
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

									<!-- Calculate sum -->
									<td class="text-center">
										<?php if (count($student_scores) > 0): ?>
											<?php $total = array_sum($student_scores); ?>
											<strong>
												<?= number_format($total, 2) ?>
											</strong>
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
							<span class="badge-score">.</span>
							<small class="text-muted ms-2">= Skor CPMK</small>
						</div>
						<div>
							<span class="badge-capaian badge-capaian-low">.</span>
							<small class="text-muted ms-2">= Capaian CPMK &lt; 60%</small>
						</div>
						<div>
							<span class="badge-capaian badge-capaian-medium">.</span>
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

	// CPMK Average Capaian Chart
	const ctxCpmk = document.getElementById('cpmkCapaianChart');
	if (ctxCpmk) {
		new Chart(ctxCpmk, {
			type: 'bar',
			data: {
				labels: cpmkLabels,
				datasets: [{
					label: 'Capaian CPMK (%)',
					data: cpmkCapaianData.map(d => d.capaian),
					backgroundColor: 'rgba(102, 126, 234, 0.8)',
					borderColor: 'rgba(102, 126, 234, 1)',
					borderWidth: 2,
					borderRadius: 5
				}]
			},
			plugins: [ChartDataLabels],
			options: {
				responsive: true,
				maintainAspectRatio: true,
				plugins: {
					legend: {
						display: true,
						position: 'top'
					},
					title: {
						display: true,
						text: 'Rata-rata Capaian CPMK (Nilai / Bobot × 100%)',
						font: {
							size: 16,
							weight: 'bold'
						}
					},
					tooltip: {
						callbacks: {
							label: function(context) {
								const data = cpmkCapaianData[context.dataIndex];
								return [
									`Capaian: ${data.capaian.toFixed(2)}%`,
									`Rata-rata Nilai: ${data.avgScore}`,
									`Bobot: ${data.bobot}%`
								];
							}
						}
					},
					datalabels: {
						anchor: 'end',
						align: 'top',
						formatter: function(value) {
							return value.toFixed(2) + '%';
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
						max: 120,
						ticks: {
							callback: function(value) {
								return value + '%';
							}
						},
						title: {
							display: true,
							text: 'Capaian (%)'
						}
					},
					x: {
						title: {
							display: true,
							text: 'CPMK'
						}
					}
				}
			}
		});
	}
</script>

<?= $this->endSection() ?>
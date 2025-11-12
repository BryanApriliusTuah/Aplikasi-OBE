<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<style>
	.cpl-header {
		background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
		color: white;
		padding: 2rem;
		border-radius: 0.5rem;
		margin-bottom: 2rem;
	}

	.stats-card {
		border-left: 4px solid #764ba2;
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
	<div class="cpl-header">
		<div class="d-flex justify-content-between align-items-center">
			<div>
				<p class="mb-0 display-6 fw-bold">
					<?= esc($jadwal['nama_mk']) ?> - Kelas <?= esc($jadwal['kelas']) ?>
				</p>
				<small><?= esc($jadwal['tahun_akademik']) ?></small>
			</div>
			<div class="text-end">
				<a href="<?= base_url('admin/nilai/export-cpl-excel/' . $jadwal['id']) ?>" class="btn btn-success me-2">
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

	<!-- CPL Statistics -->
	<?php if (!empty($cpl_list)): ?>
		<div class="card shadow-sm mb-4">
			<div class="card-header bg-light">
				<h5 class="mb-0"><i class="bi bi-trophy-fill"></i> Statistik CPL (Capaian Pembelajaran Lulusan)</h5>
			</div>
			<div class="card-body p-0">
				<div class="table-responsive">
					<table class="table table-bordered table-hover mb-0 align-middle">
						<thead class="table-light">
							<tr>
								<th class="text-center" style="width: 50px;">No</th>
								<th style="min-width: 120px;">Kode CPL</th>
								<th style="min-width: 250px;">Deskripsi</th>
								<th class="text-center" style="width: 150px;">CPMK Terkait</th>
								<th class="text-center" style="width: 120px;">Total Skor</th>
								<th class="text-center" style="width: 120px;">Total Bobot</th>
								<th class="text-center" style="width: 120px;">Capaian</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($cpl_list as $index => $cpl): ?>
								<?php $stats = $cpl_stats[$cpl['id']] ?? []; ?>
								<tr>
									<td class="text-center"><?= $index + 1 ?></td>
									<td class="text-center"><strong class="text-primary"><?= esc($cpl['kode_cpl']) ?></strong></td>
									<td><small class="text-dark"><?= esc($cpl['deskripsi']) ?></small></td>
									<td class="text-center">
										<?php if (!empty($stats['cpmk_codes'])): ?>
											<?php foreach ($stats['cpmk_codes'] as $cpmk_code): ?>
												<span class="badge bg-info text-dark me-1 mb-1"><?= esc($cpmk_code) ?></span>
											<?php endforeach; ?>
										<?php else: ?>
											<span class="text-muted">-</span>
										<?php endif; ?>
									</td>
									<td class="text-center">
										<strong><?= $stats['total_score'] ?? 0 ?></strong>
									</td>
									<td class="text-center">
										<strong><?= $stats['total_weight'] ?? 0 ?>%</strong>
									</td>
									<td class="text-center">
										<strong><?= number_format($stats['achievement'] ?? 0, 2) ?>%</strong>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<!-- CPL Achievement Chart -->
		<div class="row mb-4">
			<div class="col-12">
				<div class="card shadow-sm">
					<div class="card-header bg-light">
						<h5 class="mb-0"><i class="bi bi-bar-chart-line-fill"></i> Grafik Capaian CPL</h5>
					</div>
					<div class="card-body">
						<canvas id="cplCapaianChart" style="max-height: 400px;"></canvas>
					</div>
				</div>
			</div>
		</div>

		<!-- CPL Scores Table per Student -->
		<div class="card shadow-sm mt-4">
			<div class="card-header bg-light">
				<h5 class="mb-0"><i class="bi bi-trophy"></i> Tabel Nilai CPL per Mahasiswa</h5>
			</div>
			<div class="card-body p-0">
				<?php if (empty($mahasiswa_list)): ?>
					<div class="text-center text-muted py-5">
						<i class="bi bi-inbox fs-1"></i>
						<p class="mt-3">Tidak ada data mahasiswa</p>
					</div>
				<?php else: ?>
					<div class="table-responsive">
						<table class="table table-bordered table-hover mb-0 align-middle">
							<thead class="table-light sticky-header">
								<tr>
									<th class="text-center sticky-col" rowspan="2" style="width: 50px;">No</th>
									<th class="sticky-col" rowspan="2" style="min-width: 150px;">NIM</th>
									<th class="sticky-col" rowspan="2" style="min-width: 200px;">Nama Mahasiswa</th>
									<th class="text-center" colspan="<?= count($cpl_list) ?>">CPL</th>
								</tr>
								<tr>
									<?php foreach ($cpl_list as $cpl): ?>
										<th class="text-center score-cell" title="<?= esc($cpl['deskripsi']) ?>">
											<div class="fw-bold"><?= esc($cpl['kode_cpl']) ?></div>
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

										<?php foreach ($cpl_list as $cpl): ?>
											<?php
											$cpl_data = $cpl_mahasiswa_scores[$mahasiswa['id']][$cpl['id']] ?? null;
											$cpl_score = $cpl_data['score'] ?? null;
											$cpl_percentage = $cpl_data['percentage'] ?? null;

											// Determine cell color based on CPL percentage
											$cell_class = '';
											if ($cpl_percentage !== null) {
												if ($cpl_percentage >= 75) {
													$cell_class = 'score-good';
												} elseif ($cpl_percentage >= 60) {
													$cell_class = 'score-medium';
												} else {
													$cell_class = 'score-low';
												}
											}
											?>
											<td class="score-cell <?= $cell_class ?>">
												<?php if ($cpl_percentage !== null): ?>
													<div class="d-flex flex-column align-items-center gap-1">
														<span class="badge-score">
															<?= number_format($cpl_score, 2) ?>
														</span>
														<?php
														$capaian_class = $cpl_percentage < 60 ? 'badge-capaian-low' : 'badge-capaian-medium';
														?>
														<span class="badge-capaian <?= $capaian_class ?>">
															<?= number_format($cpl_percentage, 2) ?>%
														</span>
													</div>
												<?php else: ?>
													<span class="text-muted">-</span>
												<?php endif; ?>
											</td>
										<?php endforeach; ?>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php else: ?>
		<div class="card shadow-sm">
			<div class="card-body text-center py-5">
				<i class="bi bi-exclamation-circle fs-1 text-muted"></i>
				<p class="mt-3 text-muted">Tidak ada data CPL untuk mata kuliah ini</p>
			</div>
		</div>
	<?php endif; ?>

	<!-- Badge Legend -->
	<?php if (!empty($cpl_list) && !empty($mahasiswa_list)): ?>
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
								<small class="text-muted ms-2">= Skor CPL</small>
							</div>
							<div>
								<span class="badge-capaian badge-capaian-low">.</span>
								<small class="text-muted ms-2">= Capaian CPL &lt; 60%</small>
							</div>
							<div>
								<span class="badge-capaian badge-capaian-medium">.</span>
								<small class="text-muted ms-2">= Capaian CPL ≥ 60%</small>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<!-- Action Buttons -->
	<div class="d-flex gap-2 justify-content-end mt-4 mb-4">
		<a href="<?= base_url('admin/nilai/export-cpl-excel/' . $jadwal['id']) ?>" class="btn btn-success">
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
	.cpl-header .btn {
		display: none !important;
	}

	.cpl-header {
		background: #764ba2 !important;
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
	// Prepare data for CPL Achievement Chart
	<?php
	$cpl_data = [];
	if (!empty($cpl_list)) {
		foreach ($cpl_list as $cpl) {
			$stats = $cpl_stats[$cpl['id']] ?? [];
			$cpl_data[] = [
				'label' => $cpl['kode_cpl'],
				'totalScore' => $stats['total_score'] ?? 0,
				'totalWeight' => $stats['total_weight'] ?? 0,
				'achievement' => $stats['achievement'] ?? 0,
				'cpmkCount' => $stats['cpmk_count'] ?? 0
			];
		}
	}
	?>
	const cplLabels = <?= json_encode(array_map(function ($cpl) {
							return $cpl['kode_cpl'];
						}, $cpl_list ?? [])) ?>;
	const cplCapaianData = <?= json_encode($cpl_data) ?>;

	// CPL Achievement Chart
	const ctxCpl = document.getElementById('cplCapaianChart');
	if (ctxCpl && cplCapaianData.length > 0) {
		new Chart(ctxCpl, {
			type: 'bar',
			data: {
				labels: cplLabels,
				datasets: [{
					label: 'Capaian CPL (%)',
					data: cplCapaianData.map(d => d.achievement),
					backgroundColor: 'rgba(118, 75, 162, 0.8)',
					borderColor: 'rgba(118, 75, 162, 1)',
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
						text: 'Capaian CPL = Σ(Skor CPMK pada setiap MK dari CPL) / Σ(Total Bobot semua CPMK dari CPL)',
						font: {
							size: 14,
							weight: 'bold'
						}
					},
					tooltip: {
						callbacks: {
							label: function(context) {
								const data = cplCapaianData[context.dataIndex];
								return [
									`Capaian: ${data.achievement.toFixed(2)}%`,
									`Total Skor CPMK: ${data.totalScore}`,
									`Total Bobot CPMK: ${data.totalWeight}%`,
									`Jumlah CPMK: ${data.cpmkCount}`
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
							text: 'CPL'
						}
					}
				}
			}
		});
	}
</script>

<?= $this->endSection() ?>
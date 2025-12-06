<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<div class="mb-4">
	<h2 class="mb-1">Profil CPL</h2>
	<p class="text-muted">Capaian Pembelajaran Lulusan</p>
</div>

<?php if (empty($cplList)): ?>
	<div class="card">
		<div class="card-body text-center py-5 text-muted">
			<p class="mb-1">Belum ada data CPL</p>
			<small>Data CPL akan muncul setelah Anda memiliki nilai</small>
		</div>
	</div>
<?php else: ?>
	<!-- Chart Section -->
	<div class="card mb-4">
		<div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
			<h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Grafik Capaian CPL</h5>
			<button class="btn btn-light btn-sm" id="exportChartBtn">
				<i class="bi bi-download"></i> Export PNG
			</button>
		</div>
		<div class="card-body">
			<canvas id="cplChart" height="80"></canvas>
		</div>
	</div>

	<!-- Detailed Calculation Table -->
	<div class="card">
		<div class="card-header bg-secondary text-white">
			<h5 class="mb-0"><i class="bi bi-table"></i> Detail Perhitungan CPL</h5>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table id="cplDetailTable" class="table table-bordered table-hover">
					<thead class="table-light">
						<tr>
							<th width="5%" class="text-center">No</th>
							<th width="12%">Kode CPL</th>
							<th width="40%">Deskripsi</th>
							<th width="15%" class="text-center">Jenis CPL</th>
							<th width="15%" class="text-center">Capaian (%)</th>
							<th width="13%" class="text-center">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$cplCategories = [
							'P' => 'Pengetahuan',
							'KK' => 'Keterampilan Khusus',
							'KU' => 'Keterampilan Umum',
							'S' => 'Sikap',
						];

						$no = 1;
						foreach ($cplCategories as $key => $categoryName):
							if (!empty($cplByType[$key])):
								foreach ($cplByType[$key] as $cpl):
						?>
									<tr>
										<td class="text-center"><?= $no++ ?></td>
										<td><strong><?= esc($cpl['kode']) ?></strong></td>
										<td><?= esc($cpl['deskripsi']) ?></td>
										<td class="text-center"><span><?= $categoryName ?></span></td>
										<td class="text-center"><strong><?= $cpl['nilai'] ?>%</strong></td>
										<td class="text-center">
											<button type="button" class="btn btn-sm btn-primary" onclick="showDetail(<?= $cpl['id'] ?>, '<?= esc($cpl['kode']) ?>')">
												<i class="bi bi-eye"></i>
											</button>
										</td>
									</tr>
						<?php
								endforeach;
							endif;
						endforeach;
						?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<?php endif; ?>

<!-- Modal Detail CPL -->
<div class="modal fade" id="detailModal" tabindex="-1">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title" id="detailCplModalTitle">Detail Perhitungan CPL</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div id="detailCplModalContent">
					<div class="text-center py-4">
						<div class="spinner-border text-primary" role="status">
							<span class="visually-hidden">Loading...</span>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
	// Dynamic passing threshold from grade configuration
	const passingThreshold = <?= json_encode($passing_threshold ?? 65) ?>;

	let cplChart = null;

	$(document).ready(function() {
		// Initialize DataTable
		if ($('#cplDetailTable').length) {
			$('#cplDetailTable').DataTable({
				pageLength: 10,
				language: {
					url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
				},
				columnDefs: [{
					orderable: false,
					targets: -1
				}]
			});
		}

		// Initialize Chart
		initializeCplChart();
	});

	function initializeCplChart() {
		const chartData = {
			labels: [],
			data: []
		};

		<?php
		// Iterate through CPL by type structure
		$cplCategories = [
			'P' => 'Pengetahuan',
			'KK' => 'Keterampilan Khusus',
			'KU' => 'Keterampilan Umum',
			'S' => 'Sikap',
		];

		foreach ($cplCategories as $key => $categoryName):
			if (!empty($cplByType[$key])):
				foreach ($cplByType[$key] as $cpl):
		?>
					chartData.labels.push('<?= esc($cpl['kode']) ?>');
					chartData.data.push(<?= $cpl['nilai'] ?>);
		<?php
				endforeach;
			endif;
		endforeach;
		?>

		if (chartData.labels.length === 0) {
			return;
		}

		// Create conditional colors based on passing threshold
		const backgroundColors = chartData.data.map(value =>
			value < passingThreshold ? 'rgba(220, 53, 69, 0.8)' : 'rgba(13, 110, 253, 0.8)'
		);
		const borderColors = chartData.data.map(value =>
			value < passingThreshold ? 'rgba(220, 53, 69, 1)' : 'rgba(13, 110, 253, 1)'
		);

		const ctx = document.getElementById('cplChart').getContext('2d');
		if (cplChart) cplChart.destroy();

		cplChart = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: chartData.labels,
				datasets: [{
					label: 'Capaian CPL',
					data: chartData.data,
					backgroundColor: backgroundColors,
					borderColor: borderColors,
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
						position: 'top',
						labels: {
							generateLabels: function(chart) {
								const data = chart.data.datasets[0].data;
								const labels = [];

								// Check if there are values >= threshold (blue bars)
								const hasAboveThreshold = data.some(value => value >= passingThreshold);
								if (hasAboveThreshold) {
									labels.push({
										text: `Capaian ≥ ${passingThreshold}%`,
										fillStyle: 'rgba(13, 110, 253, 0.8)',
										strokeStyle: 'rgba(13, 110, 253, 1)',
										lineWidth: 2,
										hidden: false,
										index: 0
									});
								}

								// Check if there are values < threshold (red bars)
								const hasBelowThreshold = data.some(value => value < passingThreshold);
								if (hasBelowThreshold) {
									labels.push({
										text: `Capaian < ${passingThreshold}%`,
										fillStyle: 'rgba(220, 53, 69, 0.8)',
										strokeStyle: 'rgba(220, 53, 69, 1)',
										lineWidth: 2,
										hidden: false,
										index: 1
									});
								}

								return labels;
							}
						}
					},
					title: {
						display: true,
						text: 'Capaian Pembelajaran Lulusan (CPL)',
						font: {
							size: 16,
							weight: 'bold'
						}
					},
					tooltip: {
						backgroundColor: 'rgba(0, 0, 0, 0.8)',
						padding: 12,
						callbacks: {
							label: function(context) {
								return 'Capaian CPL: ' + context.parsed.y.toFixed(2) + '%';
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
						max: 100,
						title: {
							display: true,
							text: 'Capaian CPL (%)',
							font: {
								size: 14,
								weight: 'bold'
							}
						},
						ticks: {
							callback: function(value) {
								return value + '%';
							}
						},
						grid: {
							display: true,
							color: 'rgba(0, 0, 0, 0.05)'
						}
					},
					x: {
						title: {
							display: true,
							text: 'Kode CPL',
							font: {
								size: 14,
								weight: 'bold'
							}
						},
						grid: {
							display: false
						}
					}
				}
			}
		});
	}

	// Export Chart
	$('#exportChartBtn').on('click', function() {
		if (cplChart) {
			const link = document.createElement('a');
			link.download = 'capaian-cpl-mahasiswa.png';
			link.href = cplChart.toBase64Image();
			link.click();
		}
	});

	function showDetail(cplId, cplKode) {
		document.getElementById('detailCplModalTitle').textContent = `Detail Perhitungan ${cplKode}`;
		document.getElementById('detailCplModalContent').innerHTML = `
		<div class="text-center py-4">
			<div class="spinner-border text-primary" role="status">
				<span class="visually-hidden">Loading...</span>
			</div>
			<p class="mt-3 text-muted">Memuat detail perhitungan...</p>
		</div>
	`;

		const modal = new bootstrap.Modal(document.getElementById('detailModal'));
		modal.show();

		fetch(`<?= base_url('mahasiswa/profil-cpl/detail') ?>?cpl_id=${cplId}`)
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					if (data.data.length === 0) {
						document.getElementById('detailCplModalContent').innerHTML = `
						<div class="text-center py-4 text-muted">
							<i class="bi bi-inbox" style="font-size: 3rem;"></i>
							<p class="mt-3">Tidak ada CPMK yang terkait dengan CPL ini</p>
						</div>
					`;
						return;
					}

					let html = '';

					// Show CPL Summary with calculation formula
					if (data.summary) {
						html += `
						<div class="card mb-4">
							<div class="card-header bg-light">
								<h6 class="mb-0"><i class="bi bi-calculator"></i> Ringkasan Perhitungan</h6>
							</div>
							<div class="card-body">
								<div class="row text-center mb-3">
									<div class="col-4">
										<small class="text-muted d-block">Total Nilai CPL</small>
										<h5 class="mb-0">${data.summary.nilai_cpl}</h5>
									</div>
									<div class="col-4">
										<small class="text-muted d-block">Total Bobot</small>
										<h5 class="mb-0">${data.summary.total_bobot}%</h5>
									</div>
									<div class="col-4">
										<small class="text-muted d-block">Capaian CPL</small>
										<h5 class="mb-0 text-primary">${data.summary.capaian_cpl}%</h5>
									</div>
								</div>
								<div class="alert alert-info mb-0">
									<strong><i class="bi bi-info-circle"></i> Formula Perhitungan:</strong><br>
									Capaian CPL (%) = (Total Nilai CPL / Total Bobot) × 100<br>
									= (${data.summary.nilai_cpl} / ${data.summary.total_bobot}) × 100 = <strong>${data.summary.capaian_cpl}%</strong>
								</div>
							</div>
						</div>
					`;
					}

					// Show CPMK Details
					html += '<h6 class="mb-3"><i class="bi bi-list-check"></i> Detail CPMK yang Berkontribusi</h6>';

					data.data.forEach(cpmk => {
						html += `
						<div class="card mb-3">
							<div class="card-header bg-primary text-white">
								<h6 class="mb-0">${cpmk.kode_cpmk} - ${cpmk.deskripsi_cpmk}</h6>
							</div>
							<div class="card-body">
								<div class="row mb-3">
									<div class="col-6">
										<strong>Nilai CPMK:</strong> ${cpmk.nilai_cpmk}
									</div>
									<div class="col-6">
										<strong>Bobot:</strong> ${cpmk.bobot}%
									</div>
								</div>
					`;

						if (cpmk.detail_mk.length > 0) {
							cpmk.detail_mk.forEach(mk => {
								html += `
								<div class="mb-3">
									<div class="d-flex align-items-center mb-2">
										<span class="badge bg-secondary me-2">${mk.kode_mk}</span>
										<small>${mk.nama_mk}</small>
									</div>
									<div class="table-responsive">
										<table class="table table-sm table-bordered mb-0">
											<thead class="table-light">
												<tr>
													<th>Teknik Penilaian</th>
													<th class="text-center" width="15%">Nilai</th>
													<th class="text-center" width="15%">Bobot (%)</th>
													<th class="text-center" width="15%">Nilai CPMK</th>
												</tr>
											</thead>
											<tbody>
							`;

								if (mk.teknik_breakdown && mk.teknik_breakdown.length > 0) {
									mk.teknik_breakdown.forEach(t => {
										html += `
										<tr>
											<td>${t.teknik}</td>
											<td class="text-center">${t.nilai}</td>
											<td class="text-center">${t.bobot}%</td>
											<td class="text-center">${t.weighted}</td>
										</tr>
									`;
									});
									html += `
									<tr class="table-success">
										<td colspan="3" class="text-end"><strong>Total CPMK</strong></td>
										<td class="text-center"><strong>${mk.nilai_cpmk}</strong></td>
									</tr>
								`;
								} else {
									html += `<tr><td colspan="4" class="text-center text-muted">Tidak ada data teknik penilaian</td></tr>`;
								}

								html += `
											</tbody>
										</table>
									</div>
								</div>
							`;
							});
						} else {
							html += '<p class="text-muted mb-0"><i class="bi bi-exclamation-circle"></i> Belum ada nilai untuk CPMK ini</p>';
						}

						html += `
							</div>
						</div>
					`;
					});

					document.getElementById('detailCplModalContent').innerHTML = html;
				} else {
					document.getElementById('detailCplModalContent').innerHTML = `
					<div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> ${data.message}</div>
				`;
				}
			})
			.catch(error => {
				document.getElementById('detailCplModalContent').innerHTML = `
				<div class="alert alert-danger"><i class="bi bi-exclamation-triangle"></i> Terjadi kesalahan saat memuat data</div>
			`;
			});
	}
</script>

<?= $this->endSection() ?>
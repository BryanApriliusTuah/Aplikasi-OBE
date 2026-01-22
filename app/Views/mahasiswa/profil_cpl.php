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
	<div id="cplChartContainer" class="mb-4"></div>

	<!-- Detailed Calculation Table -->
	<div class="card shadow-sm border-0">
		<div class="card-body p-0">
			<div class="modern-table-wrapper" style="position: relative;">
				<div class="scroll-indicator"></div>
				<table id="cplDetailTable" class="modern-table">
					<thead>
						<tr>
							<th width="5%" class="text-center">No</th>
							<th width="10%" class="text-center">Kode CPL</th>
							<th width="50%" class="text-center">Deskripsi</th>
							<th width="15%" class="text-center">Jenis CPL</th>
							<th width="10%" class="text-center">Capaian (%)</th>
							<th width="10%" class="text-center">Aksi</th>
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
										<td class="text-center">
											<span style="font-size: 0.85rem; font-weight: 600;">
												<?= esc($cpl['kode']) ?>
											</span>
										</td>
										<td><?= esc($cpl['deskripsi']) ?></td>
										<td class="text-center">
											<span style="font-size: 0.8rem; padding: 0.4rem 0.8rem;">
												<?= $categoryName ?>
											</span>
										</td>
										<td class="text-center">
											<span class="fw-bold">
												<?= $cpl['nilai'] ?>%
											</span>
										</td>
										<td class="text-center">
											<button class="btn btn-sm btn-outline-primary" onclick="showDetail(<?= $cpl['id'] ?>, '<?= esc($cpl['kode']) ?>')" data-bs-toggle="tooltip" title="Lihat detail nilai CPL">
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
<script src="<?= base_url('js/modern-chart-component.js') ?>"></script>

<!-- Modern Table CSS -->
<link href="<?= base_url('css/modern-table.css') ?>" rel="stylesheet" />

<style>
	/* Sticky column positions */
	.modern-table .sticky-col:nth-child(1) {
		left: 0;
		min-width: 60px;
	}

	.modern-table .sticky-col:nth-child(2) {
		left: 60px;
		min-width: 120px;
	}

	/* Button hover effect */
	.btn-primary.rounded-pill:hover {
		transform: translateY(-2px);
		box-shadow: 0 4px 8px rgba(102, 126, 234, 0.3);
	}

	/* Remove default card body padding for seamless table integration */
	.card-body.p-0 {
		overflow: hidden;
	}
</style>

<script>
	// Dynamic passing threshold from grade configuration
	const passingThreshold = <?= json_encode($passing_threshold ?? 65) ?>;

	let cplChartComponent = null;

	$(document).ready(function() {
		// Initialize scroll detection for modern table
		initScrollDetection();

		// Initialize Chart
		initializeCplChart();
	});

	// Function to detect and handle horizontal scroll
	function initScrollDetection() {
		const wrapper = document.querySelector('.modern-table-wrapper');
		if (!wrapper) return;

		function checkScroll() {
			const hasScroll = wrapper.scrollWidth > wrapper.clientWidth;
			const isScrolledToEnd = wrapper.scrollLeft >= (wrapper.scrollWidth - wrapper.clientWidth - 10);

			if (hasScroll && !isScrolledToEnd) {
				wrapper.classList.add('has-scroll');
			} else {
				wrapper.classList.remove('has-scroll');
			}
		}

		// Check on load and scroll
		checkScroll();
		wrapper.addEventListener('scroll', checkScroll);
		window.addEventListener('resize', checkScroll);
	}

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

		// Destroy existing chart if any
		if (cplChartComponent) {
			cplChartComponent.destroy();
		}

		// Create and render modern chart
		cplChartComponent = new ModernChartComponent({
			containerId: 'cplChartContainer',
			chartData: chartData,
			config: {
				title: 'Grafik Capaian CPL',
				type: 'bar',
				passingThreshold: passingThreshold,
				showExportButton: true,
				showSubtitle: true,
				subtitle: 'Visualisasi Capaian Pembelajaran Lulusan (CPL)',
				exportFilename: 'capaian-cpl-mahasiswa.png',
				height: 80,
				labels: {
					yAxis: 'Capaian CPL (%)',
					xAxis: 'Kode CPL'
				}
			}
		});

		cplChartComponent.render();
	}

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

					let html = `
						<div class="modern-table-wrapper">
							<table class="modern-table mb-0">
								<thead>
									<tr>
										<th width="5%" class="text-center">No</th>
										<th width="15%">Kode CPMK</th>
										<th width="30%">Deskripsi CPMK</th>
										<th width="20%">Mata Kuliah</th>
										<th width="10%" class="text-center">Nilai CPMK</th>
										<th width="10%" class="text-center">Bobot (%)</th>
										<th width="10%" class="text-center">Kontribusi</th>
									</tr>
								</thead>
								<tbody>
					`;

					// Flatten the data structure to show all MK for each CPMK
					let rowNum = 1;
					let totalNilaiCpl = 0;
					let totalBobot = 0;

					data.data.forEach(cpmk => {
						if (cpmk.detail_mk && cpmk.detail_mk.length > 0) {
							cpmk.detail_mk.forEach((mk, mkIndex) => {
								const kontribusi = (parseFloat(cpmk.nilai_cpmk) * parseFloat(cpmk.bobot) / 100).toFixed(2);

								html += `
									<tr>
										<td class="text-center">${rowNum++}</td>
										<td><strong>${cpmk.kode_cpmk}</strong></td>
										<td><small>${cpmk.deskripsi_cpmk}</small></td>
										<td><small>${mk.kode_mk} - ${mk.nama_mk}</small></td>
										<td class="text-center">${parseFloat(cpmk.nilai_cpmk).toFixed(2)}</td>
										<td class="text-center">${parseFloat(cpmk.bobot).toFixed(0)}%</td>
										<td class="text-center">${kontribusi}</td>
									</tr>
								`;

								// Only count once per CPMK
								if (mkIndex === 0) {
									totalNilaiCpl += parseFloat(kontribusi);
									totalBobot += parseFloat(cpmk.bobot);
								}
							});
						} else {
							const kontribusi = (parseFloat(cpmk.nilai_cpmk) * parseFloat(cpmk.bobot) / 100).toFixed(2);

							html += `
								<tr>
									<td class="text-center">${rowNum++}</td>
									<td><strong>${cpmk.kode_cpmk}</strong></td>
									<td><small>${cpmk.deskripsi_cpmk}</small></td>
									<td class="text-center text-muted"><small>-</small></td>
									<td class="text-center">${parseFloat(cpmk.nilai_cpmk).toFixed(2)}</td>
									<td class="text-center">${parseFloat(cpmk.bobot).toFixed(0)}%</td>
									<td class="text-center">${kontribusi}</td>
								</tr>
							`;

							totalNilaiCpl += parseFloat(kontribusi);
							totalBobot += parseFloat(cpmk.bobot);
						}
					});

					// Calculate final CPL achievement
					const capaianCpl = data.summary ? parseFloat(data.summary.capaian_cpl).toFixed(2) :
						(totalBobot > 0 ? (totalNilaiCpl / totalBobot * 100).toFixed(2) : '0.00');

					html += `
								</tbody>
								<tfoot>
									<tr>
										<td colspan="6" class="text-end"><strong>TOTAL KONTRIBUSI:</strong></td>
										<td class="text-center"><strong>${totalNilaiCpl.toFixed(2)}</strong></td>
									</tr>
									<tr style="background-color: #d1e7dd;">
										<td colspan="6" class="text-end">
											<strong>Capaian CPL (%) = (Total Kontribusi / Total Bobot) × 100</strong><br>
											<small class="text-muted">= (${totalNilaiCpl.toFixed(2)} / ${totalBobot.toFixed(0)}) × 100</small>
										</td>
										<td class="text-center"><h6 class="mb-0"><strong>${capaianCpl}%</strong></h6></td>
									</tr>
								</tfoot>
							</table>
						</div>
					`;

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
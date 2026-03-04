<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<!-- Modern Table CSS -->
<link href="<?= base_url('css/modern-table.css') ?>" rel="stylesheet" />

<div class="mb-4">
	<h2 class="mb-1">Profil CPL</h2>
	<p class="text-muted">Capaian Pembelajaran Lulusan</p>
</div>

<?php if (empty($cplList)): ?>
	<div class="text-center py-5">
		<i class="bi bi-bar-chart" style="font-size: 4rem; color: #ccc;"></i>
		<p class="text-muted mt-3">Belum ada data CPL</p>
	</div>
<?php else: ?>
	<!-- Chart Section -->
	<div id="cplChartContainer" class="mb-4"></div>

	<!-- Detailed Calculation Table -->
	<div class="modern-table-wrapper" style="position: relative;">
		<div class="scroll-indicator"></div>
		<table id="cplDetailTable" class="modern-table">
					<thead>
						<tr>
							<th width="5%" class="text-center">No</th>
							<th width="10%" class="text-center">Kode CPL</th>
							<th width="38%" class="text-center">Deskripsi CPL</th>
							<th width="10%" class="text-center">Jumlah CPMK</th>
							<th width="10%" class="text-center">Jumlah MK</th>
							<th width="12%" class="text-center">Capaian (%)</th>
							<th width="10%" class="text-center">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php $no = 1; foreach ($cplItems as $cpl): ?>
							<tr>
								<td class="text-center"><?= $no++ ?></td>
								<td class="text-center">
									<strong class="text-primary"><?= esc($cpl['kode']) ?></strong>
								</td>
								<td><small><?= esc($cpl['deskripsi']) ?></small></td>
								<td class="text-center"><?= $cpl['jumlah_cpmk'] ?></td>
								<td class="text-center"><?= $cpl['jumlah_mk'] ?></td>
								<td class="text-center">
									<strong><?= $cpl['nilai'] ?>%</strong>
								</td>
								<td class="text-center">
									<button class="btn btn-sm btn-outline-primary" onclick="showDetail(<?= $cpl['id'] ?>, '<?= esc($cpl['kode']) ?>')" data-bs-toggle="tooltip" title="Lihat detail nilai CPL">
										<i class="bi bi-eye"></i>
									</button>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
		</table>
	</div>
<?php endif; ?>

<!-- Modal Detail CPL -->
<div class="modal fade" id="detailModal" tabindex="-1">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
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

		<?php foreach ($cplItems as $cpl): ?>
				chartData.labels.push('<?= esc($cpl['kode']) ?>');
				chartData.data.push(<?= $cpl['nilai'] ?>);
		<?php endforeach; ?>

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
										<th width="12%">Kode CPMK</th>
										<th width="22%">Mata Kuliah</th>
										<th width="12%" class="text-center">Tahun Akademik</th>
										<th width="10%" class="text-center">Kelas</th>
										<th width="10%" class="text-center">Nilai CPMK</th>
										<th width="10%" class="text-center">Bobot</th>
										<th width="10%" class="text-center">Capaian (%)</th>
									</tr>
								</thead>
								<tbody>
					`;

					// Flatten the data structure to show all MK for each CPMK
					let rowNum = 1;
					let totalNilai = 0;
					let totalBobot = 0;

					data.data.forEach(cpmk => {
						const nilaiCpmk = parseFloat(cpmk.nilai_cpmk);
						const bobot = parseFloat(cpmk.bobot);
						const capaian = bobot > 0 ? (nilaiCpmk / bobot * 100).toFixed(2) : '0.00';

						if (cpmk.detail_mk && cpmk.detail_mk.length > 0) {
							cpmk.detail_mk.forEach((mk, mkIndex) => {
								const kelasDisplay = mk.kelas === 'KM' ? '<span class="badge bg-primary">MBKM</span>' : mk.kelas;
								html += `
									<tr>
										<td class="text-center">${rowNum++}</td>
										<td><strong>${cpmk.kode_cpmk}</strong></td>
										<td><small>${mk.kode_mk} - ${mk.nama_mk}</small></td>
										<td class="text-center">${mk.tahun_akademik}</td>
										<td class="text-center">${kelasDisplay}</td>
										<td class="text-center">${nilaiCpmk.toFixed(2)}</td>
										<td class="text-center">${bobot.toFixed(2)}</td>
										<td class="text-center">${capaian}%</td>
									</tr>
								`;

								// Only accumulate totals once per CPMK
								if (mkIndex === 0) {
									totalNilai += nilaiCpmk;
									totalBobot += bobot;
								}
							});
						} else {
							html += `
								<tr>
									<td class="text-center">${rowNum++}</td>
									<td><strong>${cpmk.kode_cpmk}</strong></td>
									<td class="text-center text-muted"><small>-</small></td>
									<td class="text-center">-</td>
									<td class="text-center">-</td>
									<td class="text-center">${nilaiCpmk.toFixed(2)}</td>
									<td class="text-center">${bobot.toFixed(2)}</td>
									<td class="text-center">${capaian}%</td>
								</tr>
							`;

							totalNilai += nilaiCpmk;
							totalBobot += bobot;
						}
					});

					// Calculate final CPL achievement
					const capaianCpl = data.summary ? parseFloat(data.summary.capaian_cpl).toFixed(2) :
						(totalBobot > 0 ? (totalNilai / totalBobot * 100).toFixed(2) : '0.00');

					html += `
								</tbody>
								<tfoot>
									<tr>
										<td colspan="5" class="text-end"><strong>TOTAL:</strong></td>
										<td class="text-center"><strong>${totalNilai.toFixed(2)}</strong></td>
										<td class="text-center"><strong>${totalBobot.toFixed(2)}</strong></td>
										<td></td>
									</tr>
									<tr style="background-color: #d1e7dd;">
										<td colspan="7" class="text-end"><strong>Capaian CPL (%) = (${totalNilai.toFixed(2)} / ${totalBobot.toFixed(2)}) &times; 100</strong></td>
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
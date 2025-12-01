<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="card">
	<div class="card-body">
		<h2 class="mb-4">Capaian CPMK</h2>

		<!-- Tab Navigation -->
		<ul class="nav nav-tabs mb-4" id="cpmkTabs" role="tablist">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" id="individual-tab" data-bs-toggle="tab" data-bs-target="#individual" type="button" role="tab">
					<i class="bi bi-person"></i> Mahasiswa
				</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="comparative-tab" data-bs-toggle="tab" data-bs-target="#comparative" type="button" role="tab">
					<i class="bi bi-people"></i> Angkatan
				</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="keseluruhan-tab" data-bs-toggle="tab" data-bs-target="#keseluruhan" type="button" role="tab">
					<i class="bi bi-bar-chart-line"></i> Keseluruhan
				</button>
			</li>
		</ul>

		<!-- Tab Content -->
		<div class="tab-content" id="cpmkTabContent">
			<!-- Individual Tab (Mahasiswa) -->
			<div class="tab-pane fade show active" id="individual" role="tabpanel">
				<!-- Filter Section Individual -->
				<div class="card mb-4">
					<div class="card-header bg-light">
						<h5 class="mb-0"><i class="bi bi-funnel"></i> Filter Mahasiswa</h5>
					</div>
					<div class="card-body">
						<form id="filterIndividualForm">
							<div class="row g-3">
								<div class="col-md-4">
									<label for="programStudiSelect" class="form-label">Program Studi</label>
									<select class="form-select" id="programStudiSelect" name="program_studi">
										<option value="">-- Semua Program Studi --</option>
										<?php foreach ($programStudi as $prodi): ?>
											<option value="<?= esc($prodi) ?>" <?= $prodi === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-md-3">
									<label for="tahunAngkatanSelect" class="form-label">Tahun Angkatan</label>
									<select class="form-select" id="tahunAngkatanSelect" name="tahun_angkatan">
										<option value="">-- Semua Tahun --</option>
										<?php foreach ($tahunAngkatan as $tahun): ?>
											<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-md-4">
									<label for="mahasiswaSelect" class="form-label">Mahasiswa <span class="text-danger">*</span></label>
									<select class="form-select" id="mahasiswaSelect" name="mahasiswa_id">
										<option value="">-- Pilih Mahasiswa --</option>
									</select>
								</div>
								<div class="col-md-1 d-flex align-items-end">
									<button type="submit" class="btn btn-primary w-100">
										<i class="bi bi-search"></i>
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>

				<!-- Info Section Individual -->
				<div id="infoSectionIndividual" class="alert alert-info d-none">
					<h6 class="mb-2"><strong>Informasi Mahasiswa:</strong></h6>
					<div id="infoContentIndividual"></div>
				</div>

				<!-- Chart Section Individual -->
				<div id="chartSectionIndividual" class="d-none"></div>

				<!-- Detailed Calculation Table Individual -->
				<div id="detailCalculationIndividual" class="d-none">
					<div class="card mt-4">
						<div class="card-header bg-secondary text-white">
							<h5 class="mb-0"><i class="bi bi-table"></i> Detail Perhitungan CPMK</h5>
						</div>
						<div class="card-body">
							<div id="detailCalculationContent"></div>
						</div>
					</div>
				</div>

				<!-- Empty State Individual -->
				<div id="emptyStateIndividual" class="text-center py-5">
					<i class="bi bi-bar-chart" style="font-size: 4rem; color: #ccc;"></i>
					<p class="text-muted mt-3">Pilih mahasiswa dan klik tombol search untuk melihat grafik capaian CPMK</p>
				</div>
			</div>

			<!-- Comparative Tab (Angkatan) -->
			<div class="tab-pane fade" id="comparative" role="tabpanel">
				<!-- Filter Section Comparative -->
				<div class="card mb-4">
					<div class="card-header bg-light">
						<h5 class="mb-0"><i class="bi bi-funnel"></i> Filter Angkatan</h5>
					</div>
					<div class="card-body">
						<form id="filterComparativeForm">
							<div class="row g-3">
								<div class="col-md-5">
									<label for="programStudiComparativeSelect" class="form-label">Program Studi <span class="text-danger">*</span></label>
									<select class="form-select" id="programStudiComparativeSelect" name="program_studi" required>
										<option value="">-- Pilih Program Studi --</option>
										<?php foreach ($programStudi as $prodi): ?>
											<option value="<?= esc($prodi) ?>" <?= $prodi === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-md-5">
									<label for="tahunAngkatanComparativeSelect" class="form-label">Tahun Angkatan <span class="text-danger">*</span></label>
									<select class="form-select" id="tahunAngkatanComparativeSelect" name="tahun_angkatan" required>
										<option value="">-- Pilih Tahun Angkatan --</option>
										<?php foreach ($tahunAngkatan as $tahun): ?>
											<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-md-2 d-flex align-items-end">
									<button type="submit" class="btn btn-primary w-100">
										<i class="bi bi-search"></i> Tampilkan
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>

				<!-- Info Section Comparative -->
				<div id="infoSectionComparative" class="alert alert-info d-none">
					<h6 class="mb-2"><strong>Informasi:</strong></h6>
					<div id="infoContentComparative"></div>
				</div>

				<!-- Chart Section Comparative -->
				<div id="chartSectionComparative" class="d-none"></div>

				<!-- Detailed Calculation Table Comparative -->
				<div id="detailCalculationComparative" class="d-none">
					<div class="card mt-4">
						<div class="card-header bg-secondary text-white">
							<h5 class="mb-0"><i class="bi bi-table"></i> Detail Perhitungan CPMK per Angkatan</h5>
						</div>
						<div class="card-body">
							<div id="detailCalculationComparativeContent"></div>
						</div>
					</div>
				</div>

				<!-- Empty State Comparative -->
				<div id="emptyStateComparative" class="text-center py-5">
					<i class="bi bi-people" style="font-size: 4rem; color: #ccc;"></i>
					<p class="text-muted mt-3">Pilih program studi dan tahun angkatan untuk melihat grafik rata-rata capaian CPMK</p>
				</div>
			</div>

			<!-- Keseluruhan Tab -->
			<div class="tab-pane fade" id="keseluruhan" role="tabpanel">
				<!-- Filter Section Keseluruhan -->
				<div class="card mb-4">
					<div class="card-header bg-light">
						<h5 class="mb-0"><i class="bi bi-funnel"></i> Filter</h5>
					</div>
					<div class="card-body">
						<form id="filterKeseluruhanForm">
							<div class="row g-3">
								<div class="col-md-10">
									<label for="programStudiKeseluruhanSelect" class="form-label">Program Studi <span class="text-danger">*</span></label>
									<select class="form-select" id="programStudiKeseluruhanSelect" name="program_studi" required>
										<option value="">-- Pilih Program Studi --</option>
										<?php foreach ($programStudi as $prodi): ?>
											<option value="<?= esc($prodi) ?>" <?= $prodi === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
										<?php endforeach; ?>
									</select>
									<small class="text-muted">Menampilkan rata-rata CPMK dari semua mahasiswa aktif di semua angkatan</small>
								</div>
								<div class="col-md-2 d-flex align-items-end">
									<button type="submit" class="btn btn-primary w-100">
										<i class="bi bi-search"></i> Tampilkan
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>

				<!-- Info Section Keseluruhan -->
				<div id="infoSectionKeseluruhan" class="alert alert-info d-none">
					<h6 class="mb-2"><strong>Informasi:</strong></h6>
					<div id="infoContentKeseluruhan"></div>
				</div>

				<!-- Chart Section Keseluruhan -->
				<div id="chartSectionKeseluruhan" class="d-none"></div>

				<!-- Detailed Calculation Table Keseluruhan -->
				<div id="detailCalculationKeseluruhan" class="d-none">
					<div class="card mt-4">
						<div class="card-header bg-secondary text-white">
							<h5 class="mb-0"><i class="bi bi-table"></i> Detail Perhitungan CPMK Keseluruhan</h5>
						</div>
						<div class="card-body">
							<div id="detailCalculationKeseluruhanContent"></div>
						</div>
					</div>
				</div>

				<!-- Empty State Keseluruhan -->
				<div id="emptyStateKeseluruhan" class="text-center py-5">
					<i class="bi bi-bar-chart-line" style="font-size: 4rem; color: #ccc;"></i>
					<p class="text-muted mt-3">Pilih program studi untuk melihat grafik rata-rata capaian CPMK dari semua angkatan</p>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Detail CPMK -->
<div class="modal fade" id="detailCpmkModal" tabindex="-1">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title" id="detailCpmkModalTitle">Detail Capaian CPMK</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div id="detailCpmkModalContent">
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
	// Dynamic passing threshold from grade configuration
	const passingThreshold = <?= json_encode($passing_threshold ?? 65) ?>;

	let cpmkChartIndividual = null;
	let cpmkChartComparative = null;
	let cpmkChartKeseluruhan = null;
	let currentIndividualData = null;
	let currentComparativeData = null;
	let currentKeseluruhanData = null;

	$(document).ready(function() {
		// Initialize Select2 on Program Studi dropdown
		$('#programStudiSelect').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Semua Program Studi --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Program studi tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				}
			}
		});

		// Initialize Select2 on Tahun Angkatan dropdown
		$('#tahunAngkatanSelect').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Semua Tahun --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Tahun angkatan tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				}
			}
		});

		// Initialize Select2 on mahasiswa dropdown
		$('#mahasiswaSelect').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Mahasiswa --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Mahasiswa tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				},
				inputTooShort: function() {
					return "Ketik untuk mencari...";
				}
			}
		});

		// Load initial data
		loadMahasiswa();

		// Individual Tab Events
		$('#programStudiSelect, #tahunAngkatanSelect').on('change', function() {
			loadMahasiswa();
		});

		$('#filterIndividualForm').on('submit', function(e) {
			e.preventDefault();
			loadIndividualChartData();
		});

		// Comparative Tab Events
		$('#filterComparativeForm').on('submit', function(e) {
			e.preventDefault();
			loadComparativeChartData();
		});

		// Keseluruhan Tab Events
		$('#filterKeseluruhanForm').on('submit', function(e) {
			e.preventDefault();
			loadKeseluruhanChartData();
		});

		// Auto-load data when Keseluruhan tab is shown
		$('#keseluruhan-tab').on('shown.bs.tab', function() {
			const programStudi = $('#programStudiKeseluruhanSelect').val();
			// If a program studi is already selected (default is Teknik Informatika), load the data automatically
			if (programStudi) {
				loadKeseluruhanChartData();
			}
		});
	});

	function loadMahasiswa() {
		const programStudi = $('#programStudiSelect').val();
		const tahunAngkatan = $('#tahunAngkatanSelect').val();

		console.log('Loading mahasiswa with filters:', {
			programStudi,
			tahunAngkatan
		});

		$.ajax({
			url: '<?= base_url("admin/capaian-cpmk/mahasiswa") ?>',
			method: 'GET',
			data: {
				program_studi: programStudi,
				tahun_angkatan: tahunAngkatan
			},
			success: function(response) {
				console.log('Mahasiswa response:', response);
				const mahasiswaSelect = $('#mahasiswaSelect');

				// Clear existing options
				mahasiswaSelect.html('<option value="">-- Pilih Mahasiswa --</option>');

				if (response && response.length > 0) {
					response.forEach(function(mhs) {
						mahasiswaSelect.append(`<option value="${mhs.id}">${mhs.nim} - ${mhs.nama_lengkap}</option>`);
					});
					mahasiswaSelect.prop('disabled', false);
					console.log('Loaded ' + response.length + ' mahasiswa');
				} else {
					mahasiswaSelect.prop('disabled', true);
					console.log('No mahasiswa found');
				}

				// Refresh Select2 after updating options
				mahasiswaSelect.trigger('change.select2');
			},
			error: function(xhr, status, error) {
				console.error('Error loading mahasiswa:', {
					xhr,
					status,
					error
				});
				console.error('Response text:', xhr.responseText);
				alert('Error loading mahasiswa: ' + error);
			}
		});
	}

	function loadIndividualChartData() {
		const mahasiswaId = $('#mahasiswaSelect').val();

		if (!mahasiswaId) {
			alert('Silakan pilih mahasiswa terlebih dahulu');
			return;
		}

		$('#emptyStateIndividual').addClass('d-none');
		$('#detailCalculationIndividual').addClass('d-none');
		$('#chartSectionIndividual').removeClass('d-none').html(getLoadingHTML());

		$.ajax({
			url: '<?= base_url("admin/capaian-cpmk/chartDataIndividual") ?>',
			method: 'GET',
			data: {
				mahasiswa_id: mahasiswaId,
				program_studi: $('#programStudiSelect').val(),
				tahun_angkatan: $('#tahunAngkatanSelect').val()
			},
			success: function(response) {
				if (response.success) {
					currentIndividualData = response;
					displayIndividualChart(response);
					displayIndividualInfo(response);
				} else {
					showError('emptyStateIndividual', 'chartSectionIndividual', response.message);
				}
			},
			error: function() {
				showError('emptyStateIndividual', 'chartSectionIndividual', 'Terjadi kesalahan saat memuat data');
			}
		});
	}

	function loadComparativeChartData() {
		const programStudi = $('#programStudiComparativeSelect').val();
		const tahunAngkatan = $('#tahunAngkatanComparativeSelect').val();

		if (!programStudi || !tahunAngkatan) {
			alert('Silakan pilih program studi dan tahun angkatan');
			return;
		}

		$('#emptyStateComparative').addClass('d-none');
		$('#detailCalculationComparative').addClass('d-none');
		$('#chartSectionComparative').removeClass('d-none').html(getLoadingHTML('success'));

		$.ajax({
			url: '<?= base_url("admin/capaian-cpmk/comparativeData") ?>',
			method: 'GET',
			data: {
				program_studi: programStudi,
				tahun_angkatan: tahunAngkatan
			},
			success: function(response) {
				if (response.success) {
					currentComparativeData = response;
					displayComparativeChart(response);
					displayComparativeInfo(response);
				} else {
					showError('emptyStateComparative', 'chartSectionComparative', response.message);
				}
			},
			error: function() {
				showError('emptyStateComparative', 'chartSectionComparative', 'Terjadi kesalahan saat memuat data');
			}
		});
	}

	function loadKeseluruhanChartData() {
		const programStudi = $('#programStudiKeseluruhanSelect').val();

		if (!programStudi) {
			alert('Silakan pilih program studi');
			return;
		}

		$('#emptyStateKeseluruhan').addClass('d-none');
		$('#detailCalculationKeseluruhan').addClass('d-none');
		$('#chartSectionKeseluruhan').removeClass('d-none').html(getLoadingHTML('info'));

		$.ajax({
			url: '<?= base_url("admin/capaian-cpmk/keseluruhanData") ?>',
			method: 'GET',
			data: {
				program_studi: programStudi
			},
			success: function(response) {
				if (response.success) {
					currentKeseluruhanData = response;
					displayKeseluruhanChart(response);
					displayKeseluruhanInfo(response);
				} else {
					showError('emptyStateKeseluruhan', 'chartSectionKeseluruhan', response.message);
				}
			},
			error: function() {
				showError('emptyStateKeseluruhan', 'chartSectionKeseluruhan', 'Terjadi kesalahan saat memuat data');
			}
		});
	}

	function displayIndividualInfo(response) {
		const info = `
        <div class="row">
            <div class="col-md-3">
                <strong>NIM:</strong> ${response.mahasiswa.nim}
            </div>
            <div class="col-md-4">
                <strong>Nama:</strong> ${response.mahasiswa.nama_lengkap}
            </div>
            <div class="col-md-3">
                <strong>Program Studi:</strong> ${response.mahasiswa.program_studi}
            </div>
            <div class="col-md-2">
                <strong>Angkatan:</strong> ${response.mahasiswa.tahun_angkatan}
            </div>
        </div>
    `;
		$('#infoContentIndividual').html(info);
		$('#infoSectionIndividual').removeClass('d-none');
	}

	function displayComparativeInfo(response) {
		const info = `
        <div class="row">
            <div class="col-md-4">
                <strong>Program Studi:</strong> ${response.programStudi}
            </div>
            <div class="col-md-4">
                <strong>Tahun Angkatan:</strong> ${response.tahunAngkatan}
            </div>
            <div class="col-md-4">
                <strong>Total Mahasiswa:</strong> ${response.totalMahasiswa} orang
            </div>
        </div>
    `;
		$('#infoContentComparative').html(info);
		$('#infoSectionComparative').removeClass('d-none');
	}

	function displayKeseluruhanInfo(response) {
		const angkatanStr = response.angkatanList.join(', ');
		const info = `
        <div class="row">
            <div class="col-md-4">
                <strong>Program Studi:</strong> ${response.programStudi}
            </div>
            <div class="col-md-4">
                <strong>Total Mahasiswa:</strong> ${response.totalMahasiswa} orang
            </div>
            <div class="col-md-4">
                <strong>Angkatan:</strong> ${angkatanStr}
            </div>
        </div>
    `;
		$('#infoContentKeseluruhan').html(info);
		$('#infoSectionKeseluruhan').removeClass('d-none');
	}

	function displayIndividualChart(response) {
		const chartHTML = createChartHTML('Individual', 'primary', 'cpmkChartIndividual');
		$('#chartSectionIndividual').html(chartHTML);

		bindExportButton('#exportChartIndividualBtn', cpmkChartIndividual, 'capaian-cpmk-individual.png');

		const ctx = document.getElementById('cpmkChartIndividual').getContext('2d');
		if (cpmkChartIndividual) cpmkChartIndividual.destroy();

		cpmkChartIndividual = createBarChart(ctx, response.chartData, 'Capaian CPMK Mahasiswa', 'rgba(13, 110, 253, 0.8)', 'rgba(13, 110, 253, 1)');

		// Display detailed calculation breakdown
		displayDetailedCalculation(response);
	}

	function displayDetailedCalculation(response) {
		const mahasiswaId = $('#mahasiswaSelect').val();

		if (!response.chartData || !response.chartData.details) {
			$('#detailCalculationIndividual').addClass('d-none');
			return;
		}

		// Group details by CPMK code
		const groupedByCpmk = {};
		response.chartData.details.forEach(item => {
			if (!groupedByCpmk[item.kode_cpmk]) {
				groupedByCpmk[item.kode_cpmk] = {
					kode_cpmk: item.kode_cpmk,
					cpmk_id: item.cpmk_id,
					deskripsi: item.deskripsi,
					courses: []
				};
			}
			groupedByCpmk[item.kode_cpmk].courses.push(item);
		});

		// Get aggregated values from chartData
		const cpmkValues = {};
		response.chartData.labels.forEach((label, index) => {
			cpmkValues[label] = response.chartData.data[index];
		});

		let html = '<div class="table-responsive"><table class="table table-bordered table-hover">';
		html += '<thead class="table-light">';
		html += '<tr>';
		html += '<th width="5%" class="text-center">No</th>';
		html += '<th width="15%">Kode CPMK</th>';
		html += '<th width="35%">Deskripsi CPMK</th>';
		html += '<th width="12%" class="text-center">Jumlah MK</th>';
		html += '<th width="13%" class="text-center">Capaian (%)</th>';
		html += '<th width="10%" class="text-center">Aksi</th>';
		html += '</tr>';
		html += '</thead>';
		html += '<tbody>';

		let rowIndex = 0;
		Object.keys(groupedByCpmk).forEach(kodeCpmk => {
			const cpmk = groupedByCpmk[kodeCpmk];
			const nilai = cpmkValues[kodeCpmk] || 0;
			const statusBadge = getStatusBadge(nilai);

			html += `
				<tr>
					<td class="text-center">${rowIndex + 1}</td>
					<td><strong>${cpmk.kode_cpmk}</strong></td>
					<td><small>${cpmk.deskripsi}</small></td>
					<td class="text-center">${cpmk.courses.length}</td>
					<td class="text-center"><strong>${nilai.toFixed(2)}%</strong></td>
					<td class="text-center">
						<button class="btn btn-sm btn-primary" onclick="loadIndividualCpmkDetail('${cpmk.kode_cpmk}', ${rowIndex})">
							<i class="bi bi-calculator"></i> Detail
						</button>
					</td>
				</tr>
				<tr id="individualCpmkDetail_${rowIndex}" style="display: none;">
					<td colspan="7" class="bg-light">
						<div class="p-3" id="individualCpmkDetailContent_${rowIndex}">
							<div class="text-center py-3">
								<div class="spinner-border spinner-border-sm" role="status"></div>
								<span class="ms-2">Memuat detail perhitungan...</span>
							</div>
						</div>
					</td>
				</tr>
			`;
			rowIndex++;
		});

		html += '</tbody></table></div>';

		$('#detailCalculationContent').html(html);
		$('#detailCalculationIndividual').removeClass('d-none');
	}

	function loadIndividualCpmkDetail(kodeCpmk, index) {
		const detailRow = $(`#individualCpmkDetail_${index}`);
		const contentDiv = $(`#individualCpmkDetailContent_${index}`);
		const button = detailRow.prev().find('button');

		// If already visible, just toggle
		if (detailRow.is(':visible')) {
			detailRow.hide();
			button.html('<i class="bi bi-calculator"></i> Detail');
			return;
		}

		// Show loading state
		detailRow.show();
		button.html('<i class="bi bi-x-circle"></i> Tutup');

		const mahasiswaId = $('#mahasiswaSelect').val();

		$.ajax({
			url: '<?= base_url("admin/capaian-cpmk/individualCpmkDetailCalculation") ?>',
			method: 'GET',
			data: {
				mahasiswa_id: mahasiswaId,
				kode_cpmk: kodeCpmk
			},
			success: function(response) {
				if (response.success) {
					displayIndividualCpmkCalculationDetail(index, response);
				} else {
					contentDiv.html(`
						<div class="alert alert-warning mb-0">
							<i class="bi bi-exclamation-triangle"></i> ${response.message || 'Gagal memuat detail perhitungan'}
						</div>
					`);
				}
			},
			error: function(xhr, status, error) {
				console.error('Error loading detail:', error);
				contentDiv.html(`
					<div class="alert alert-danger mb-0">
						<i class="bi bi-x-circle"></i> Terjadi kesalahan saat memuat detail perhitungan
					</div>
				`);
			}
		});
	}

	function displayIndividualCpmkCalculationDetail(index, data) {
		const contentDiv = $(`#individualCpmkDetailContent_${index}`);

		let html = ``;

		let totalCpmk = 0;
		let totalBobot = 0;

		// Display each course with its assessment breakdown
		data.courses.forEach((course, idx) => {
			html += `
				<div class="mb-4">
					<h6><strong>${course.kode_mk}</strong> - ${course.nama_mk}</h6>
					<p class="mb-2"><small class="text-muted">${course.tahun_akademik} / ${course.kelas}</small></p>
			`;

			if (course.assessments && course.assessments.length > 0) {
				html += `
					<div class="table-responsive">
						<table class="table table-sm table-bordered">
							<thead class="table-light">
								<tr>
									<th>Teknik Penilaian</th>
									<th class="text-center">Nilai</th>
									<th class="text-center">Bobot (%)</th>
									<th class="text-center">CPMK</th>
								</tr>
							</thead>
							<tbody>
				`;

				course.assessments.forEach(assessment => {
					html += `
						<tr>
							<td>${assessment.teknik}</td>
							<td class="text-center">${assessment.nilai}</td>
							<td class="text-center">${assessment.bobot}%</td>
							<td class="text-center">${assessment.weighted.toFixed(2)}</td>
						</tr>
					`;
				});

				const nilaiCpmk = course.total_weighted;

				html += `
							</tbody>
							<tfoot class="table-light">
								<tr>
									<td colspan="3" class="text-end"><strong>Total</strong></td>
									<td class="text-center"><strong>${nilaiCpmk.toFixed(2)}</strong></td>
								</tr>
							</tfoot>
						</table>
					</div>
				`;
			} else {
				html += `<p class="text-muted">Belum ada nilai</p>`;
			}

			html += `</div>`;
		});

		// Display formula and final capaian
		html += `
			<div class="alert alert-info mb-0">
				<h6 class="mb-2"><i class="bi bi-calculator"></i> Capaian ${data.kode_cpmk}:</h6>
				<p class="mb-1"><strong>Capaian CPMK</strong> = ${data.summary.grand_total_weighted} / ${data.summary.grand_total_bobot} × 100 = ${data.summary.capaian}%</p>

			</div>
		`;

		contentDiv.html(html);
	}

	function displayComparativeChart(response) {
		const chartHTML = createChartHTML('Comparative', 'primary', 'cpmkChartComparative');
		$('#chartSectionComparative').html(chartHTML);

		bindExportButton('#exportChartComparativeBtn', cpmkChartComparative, 'capaian-cpmk-comparative.png');

		const ctx = document.getElementById('cpmkChartComparative').getContext('2d');
		if (cpmkChartComparative) cpmkChartComparative.destroy();

		cpmkChartComparative = createBarChart(ctx, response.chartData, 'Rata-rata Capaian CPMK Angkatan', 'rgba(13, 110, 253, 0.8)', 'rgba(13, 110, 253, 1)');

		// Display detailed calculation
		displayComparativeDetailedCalculation(response);
	}

	function displayKeseluruhanChart(response) {
		const chartHTML = createChartHTML('Keseluruhan', 'primary', 'cpmkChartKeseluruhan');
		$('#chartSectionKeseluruhan').html(chartHTML);

		bindExportButton('#exportChartKeseluruhanBtn', cpmkChartKeseluruhan, 'capaian-cpmk-keseluruhan.png');

		const ctx = document.getElementById('cpmkChartKeseluruhan').getContext('2d');
		if (cpmkChartKeseluruhan) cpmkChartKeseluruhan.destroy();

		cpmkChartKeseluruhan = createBarChart(ctx, response.chartData, 'Rata-rata Capaian CPMK Keseluruhan', 'rgba(13, 110, 253, 0.8)', 'rgba(13, 110, 253, 1)');

		// Display detailed calculation
		displayKeseluruhanDetailedCalculation(response);
	}

	function displayComparativeDetailedCalculation(response) {
		if (!response.chartData || !response.chartData.details) {
			$('#detailCalculationComparative').addClass('d-none');
			return;
		}

		let html = '<div class="table-responsive"><table class="table table-bordered table-hover">';
		html += '<thead class="table-light">';
		html += '<tr>';
		html += '<th width="5%" class="text-center">No</th>';
		html += '<th width="15%">Kode CPMK</th>';
		html += '<th width="50%">Deskripsi CPMK</th>';
		html += '<th width="10%" class="text-center">Jumlah Mahasiswa</th>';
		html += '<th width="10%" class="text-center">Rata-rata (%)</th>';
		html += '<th width="10%" class="text-center">Aksi</th>';
		html += '</tr>';
		html += '</thead>';
		html += '<tbody>';

		response.chartData.details.forEach((cpmk, index) => {
			const statusBadge = getStatusBadge(cpmk.rata_rata);
			html += `
				<tr>
					<td class="text-center">${index + 1}</td>
					<td><strong>${cpmk.kode_cpmk}</strong></td>
					<td><small>${cpmk.deskripsi}</small></td>
					<td class="text-center">${cpmk.jumlah_mahasiswa}</td>
					<td class="text-center"><strong>${cpmk.rata_rata.toFixed(2)}%</strong></td>
					<td class="text-center">
						<button class="btn btn-sm btn-primary" onclick="loadComparativeCpmkDetail(${cpmk.cpmk_id}, '${cpmk.kode_cpmk}', ${index})">
							<i class="bi bi-eye"></i> Detail
						</button>
					</td>
				</tr>
				<tr id="cpmkComparativeDetail_${index}" style="display: none;">
					<td colspan="6" class="bg-light">
						<div id="cpmkComparativeDetailContent_${index}"></div>
					</td>
				</tr>
			`;
		});

		html += '</tbody></table></div>';

		// Add explanation
		html += '<div class="alert alert-info mt-3">';
		html += '<h6 class="mb-2"><i class="bi bi-info-circle"></i> Penjelasan Perhitungan:</h6>';
		html += '<p class="mb-1">Setiap Capaian CPMK dihitung dengan rumus:</p>';
		html += '<p class="mb-1"><strong>Rata-rata Capaian CPMK = (Σ Capaian CPMK semua mahasiswa) / Jumlah mahasiswa</strong></p>';
		html += `<p class="mb-0">Data ini menunjukkan rata-rata capaian untuk setiap CPMK dari <strong>${response.totalMahasiswa} mahasiswa</strong> pada angkatan <strong>${response.tahunAngkatan}</strong> program studi <strong>${response.programStudi}</strong>.</p>`;
		html += '</div>';

		$('#detailCalculationComparativeContent').html(html);
		$('#detailCalculationComparative').removeClass('d-none');
	}

	function displayKeseluruhanDetailedCalculation(response) {
		if (!response.chartData || !response.chartData.details) {
			$('#detailCalculationKeseluruhan').addClass('d-none');
			return;
		}

		let html = '<div class="table-responsive"><table class="table table-bordered table-hover">';
		html += '<thead class="table-light">';
		html += '<tr>';
		html += '<th width="5%" class="text-center">No</th>';
		html += '<th width="15%">Kode CPMK</th>';
		html += '<th width="50%">Deskripsi CPMK</th>';
		html += '<th width="10%" class="text-center">Jumlah Mahasiswa</th>';
		html += '<th width="10%" class="text-center">Rata-rata (%)</th>';
		html += '<th width="10%" class="text-center">Aksi</th>';
		html += '</tr>';
		html += '</thead>';
		html += '<tbody>';

		response.chartData.details.forEach((cpmk, index) => {
			const statusBadge = getStatusBadge(cpmk.rata_rata);
			html += `
				<tr>
					<td class="text-center">${index + 1}</td>
					<td><strong>${cpmk.kode_cpmk}</strong></td>
					<td><small>${cpmk.deskripsi}</small></td>
					<td class="text-center">${cpmk.jumlah_mahasiswa}</td>
					<td class="text-center"><strong>${cpmk.rata_rata.toFixed(2)}%</strong></td>
					<td class="text-center">
						<button class="btn btn-sm btn-primary" onclick="loadKeseluruhanCpmkDetail(${cpmk.cpmk_id}, '${cpmk.kode_cpmk}', ${index})">
							<i class="bi bi-eye"></i> Detail
						</button>
					</td>
				</tr>
				<tr id="cpmkKeseluruhanDetail_${index}" style="display: none;">
					<td colspan="6" class="bg-light">
						<div id="cpmkKeseluruhanDetailContent_${index}"></div>
					</td>
				</tr>
			`;
		});

		html += '</tbody></table></div>';

		// Add explanation
		html += '<div class="alert alert-info mt-3">';
		html += '<h6 class="mb-2"><i class="bi bi-info-circle"></i> Penjelasan Perhitungan:</h6>';
		html += '<p class="mb-1">Setiap Capaian CPMK dihitung dengan rumus:</p>';
		html += '<p class="mb-1"><strong>Rata-rata Capaian CPMK = (Σ Capaian CPMK setiap mahasiswa) / Jumlah mahasiswa</strong></p>';
		html += `<p class="mb-0">Data ini menunjukkan rata-rata capaian untuk setiap CPMK dari <strong>${response.totalMahasiswa} mahasiswa</strong> di program studi <strong>${response.programStudi}</strong> dari semua angkatan (${response.angkatanList.join(', ')}).</p>`;
		html += '</div>';

		$('#detailCalculationKeseluruhanContent').html(html);
		$('#detailCalculationKeseluruhan').removeClass('d-none');
	}

	function loadComparativeCpmkDetail(cpmkId, kodeCpmk, index) {
		const targetDiv = $(`#cpmkComparativeDetail_${index}`);
		const contentDiv = $(`#cpmkComparativeDetailContent_${index}`);
		const button = targetDiv.prev().find('button');

		// Toggle visibility
		if (targetDiv.is(':visible')) {
			targetDiv.hide();
			button.html('<i class="bi bi-eye"></i> Detail');
			return;
		}

		const programStudi = $('#programStudiComparativeSelect').val();
		const tahunAngkatan = $('#tahunAngkatanComparativeSelect').val();

		targetDiv.show();
		button.html('<i class="bi bi-eye-slash"></i> Tutup');
		contentDiv.html('<div class="text-center py-3"><div class="spinner-border spinner-border-sm" role="status"></div> Memuat detail perhitungan...</div>');

		$.ajax({
			url: '<?= base_url("admin/capaian-cpmk/comparativeDetailCalculation") ?>',
			method: 'GET',
			data: {
				cpmk_id: cpmkId,
				program_studi: programStudi,
				tahun_angkatan: tahunAngkatan
			},
			success: function(response) {
				if (response.success) {
					displayComparativeCpmkCalculationDetail(index, kodeCpmk, response.data, response.summary);
				} else {
					contentDiv.html('<div class="alert alert-warning mb-0">' + (response.message || 'Tidak ada data') + '</div>');
				}
			},
			error: function() {
				contentDiv.html('<div class="alert alert-danger mb-0">Terjadi kesalahan saat memuat data</div>');
			}
		});
	}

	function displayComparativeCpmkCalculationDetail(index, kodeCpmk, data, summary) {
		let html = `
			<div class="card mb-0 border-0">
				<div class="card-header bg-info text-white">
					<h6 class="mb-0"><i class="bi bi-calculator"></i> Detail Perhitungan ${kodeCpmk}</h6>
				</div>
				<div class="card-body">
		`;

		if (data.length === 0) {
			html += '<div class="alert alert-info mb-0">Belum ada mahasiswa yang memiliki nilai untuk CPMK ini</div>';
		} else {
			html += `
				<div class="table-responsive">
					<table class="table table-bordered table-sm table-hover mb-0">
						<thead class="table-primary">
							<tr>
								<th width="10%" class="text-center">No</th>
								<th width="20%">NIM</th>
								<th width="50%">Nama Mahasiswa</th>
								<th width="20%" class="text-center">Capaian CPMK (%)</th>
							</tr>
						</thead>
						<tbody>
			`;

			data.forEach((mhs, idx) => {
				html += `
					<tr>
						<td class="text-center">${idx + 1}</td>
						<td>${mhs.nim}</td>
						<td>${mhs.nama_lengkap}</td>
						<td class="text-center"><strong>${mhs.nilai_cpmk.toFixed(2)}%</strong></td>
					</tr>
				`;
			});

			html += `
						</tbody>
						<tfoot class="table-light">
							<tr>
								<td colspan="3" class="text-end"><strong>Total dari ${summary.jumlah_mahasiswa} mahasiswa:</strong></td>
								<td class="text-center"><strong>${summary.total_nilai.toFixed(2)}%</strong></td>
							</tr>
							<tr class="table-success">
								<td colspan="3" class="text-end"><strong>Rata - rata  = ${summary.total_nilai.toFixed(2)} / ${summary.jumlah_mahasiswa} mahasiswa</strong></td>
								<td class="text-center"><h6 class="mb-0"><strong>${summary.rata_rata.toFixed(2)}%</strong></h6></td>
							</tr>
						</tfoot>
					</table>
				</div>
			`;
		}

		html += `
				</div>
			</div>
		`;

		$(`#cpmkComparativeDetailContent_${index}`).html(html);
	}

	function loadKeseluruhanCpmkDetail(cpmkId, kodeCpmk, index) {
		const targetDiv = $('#cpmkKeseluruhanDetail_' + index);
		const contentDiv = $('#cpmkKeseluruhanDetailContent_' + index);
		const button = targetDiv.prev().find('button');

		// Toggle visibility
		if (targetDiv.is(':visible')) {
			targetDiv.hide();
			button.html('<i class="bi bi-eye"></i> Detail');
			return;
		}

		const programStudi = $('#programStudiKeseluruhanSelect').val();

		targetDiv.show();
		button.html('<i class="bi bi-eye-slash"></i> Tutup');
		contentDiv.html('<div class="text-center py-3"><div class="spinner-border spinner-border-sm" role="status"></div> Memuat detail perhitungan...</div>');

		$.ajax({
			url: '<?= base_url("admin/capaian-cpmk/keseluruhanDetailCalculation") ?>',
			method: 'GET',
			data: {
				cpmk_id: cpmkId,
				program_studi: programStudi
			},
			success: function(response) {
				if (response.success) {
					displayKeseluruhanCpmkCalculationDetail(index, kodeCpmk, response.data, response.summary);
				} else {
					contentDiv.html('<div class="alert alert-warning mb-0">' + (response.message || 'Tidak ada data') + '</div>');
				}
			},
			error: function() {
				contentDiv.html('<div class="alert alert-danger mb-0">Terjadi kesalahan saat memuat data</div>');
			}
		});
	}

	function displayKeseluruhanCpmkCalculationDetail(index, kodeCpmk, data, summary) {
		let html = `
			<div class="card mb-0 border-0">
				<div class="card-header bg-info text-white">
					<h6 class="mb-0"><i class="bi bi-calculator"></i> Detail Perhitungan ${kodeCpmk} - Per Mahasiswa (Semua Angkatan)</h6>
				</div>
				<div class="card-body">
		`;

		if (data.length === 0) {
			html += '<div class="alert alert-info mb-0">Belum ada mahasiswa yang memiliki nilai untuk CPMK ini</div>';
		} else {
			html += `
				<div class="table-responsive">
					<table class="table table-bordered table-sm table-hover mb-0">
						<thead class="table-primary">
							<tr>
								<th width="8%" class="text-center">No</th>
								<th width="15%">NIM</th>
								<th width="40%">Nama Mahasiswa</th>
								<th width="12%" class="text-center">Angkatan</th>
								<th width="25%" class="text-center">Capaian CPMK</th>
							</tr>
						</thead>
						<tbody>
			`;

			data.forEach((mhs, idx) => {
				html += `
					<tr>
						<td class="text-center">${idx + 1}</td>
						<td>${mhs.nim}</td>
						<td>${mhs.nama_lengkap}</td>
						<td class="text-center"><span class="badge bg-secondary">${mhs.tahun_angkatan}</span></td>
						<td class="text-center"><strong>${mhs.nilai_cpmk.toFixed(2)}%</strong></td>
					</tr>
				`;
			});

			html += `
						</tbody>
						<tfoot class="table-light">
							<tr>
								<td colspan="4" class="text-end"><strong>Total dari ${summary.jumlah_mahasiswa} mahasiswa (semua angkatan):</strong></td>
								<td class="text-center"><strong>${summary.total_nilai.toFixed(2)}%</strong></td>
							</tr>
							<tr class="table-success">
								<td colspan="4" class="text-end"><strong>Rata - rata = ${summary.total_nilai.toFixed(2)}% / ${summary.jumlah_mahasiswa} mahasiswa</strong></td>
								<td class="text-center"><h6 class="mb-0"><strong>${summary.rata_rata.toFixed(2)}%</strong></h6></td>
							</tr>
						</tfoot>
					</table>
				</div>
			`;
		}

		html += `
				</div>
			</div>
		`;

		$('#cpmkKeseluruhanDetailContent_' + index).html(html);
	}

	// Helper Functions
	function createBarChart(ctx, chartData, title, backgroundColor, borderColor) {
		// Create conditional colors based on passing threshold
		const backgroundColors = chartData.data.map(value =>
			value < passingThreshold ? 'rgba(220, 53, 69, 0.8)' : backgroundColor
		);
		const borderColors = chartData.data.map(value =>
			value < passingThreshold ? 'rgba(220, 53, 69, 1)' : borderColor
		);

		return new Chart(ctx, {
			type: 'bar',
			data: {
				labels: chartData.labels,
				datasets: [{
					label: 'Capaian CPMK',
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
						text: title,
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
								return 'Capaian CPMK: ' + context.parsed.y.toFixed(2);
							}
						}
					},
					datalabels: {
						anchor: 'end',
						align: 'top',
						formatter: function(value) {
							return value.toFixed(2);
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
							text: 'Nilai CPMK',
							font: {
								size: 14,
								weight: 'bold'
							}
						},
						ticks: {
							callback: function(value) {
								return value;
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
							text: 'Kode CPMK',
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

	function createChartHTML(type, color, canvasId, title = 'Grafik Capaian CPMK') {
		return `
        <div class="card">
            <div class="card-header bg-${color} text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> ${title}</h5>
                <button class="btn btn-light btn-sm" id="exportChart${type}Btn">
                    <i class="bi bi-download"></i> Export PNG
                </button>
            </div>
            <div class="card-body">
                <canvas id="${canvasId}" height="80"></canvas>
            </div>
        </div>
    `;
	}

	function getStatusBadge(nilai) {
		// Use dynamic threshold: passing+10 for "good", passing for "fair"
		const goodThreshold = passingThreshold + 10;
		if (nilai >= goodThreshold) return '<span class="badge bg-success">Baik</span>';
		if (nilai >= passingThreshold) return '<span class="badge bg-warning">Cukup</span>';
		return '<span class="badge bg-danger">Kurang</span>';
	}

	function getLoadingHTML(color = 'primary') {
		return `
        <div class="text-center py-5">
            <div class="spinner-border text-${color}" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-3">Memuat data...</p>
        </div>
    `;
	}

	function showError(emptyStateId, chartSectionId, message) {
		$(`#${chartSectionId}`).addClass('d-none');
		$('#detailCalculationIndividual').addClass('d-none');
		$('#detailCalculationComparative').addClass('d-none');
		$('#detailCalculationKeseluruhan').addClass('d-none');
		$(`#${emptyStateId}`).removeClass('d-none').html(`
        <i class="bi bi-exclamation-circle" style="font-size: 4rem; color: #dc3545;"></i>
        <p class="text-danger mt-3">${message}</p>
    `);
	}

	function bindExportButton(selector, chart, filename) {
		$(document).off('click', selector);
		$(document).on('click', selector, function() {
			if (chart) {
				const link = document.createElement('a');
				link.download = filename;
				link.href = chart.toBase64Image();
				link.click();
			}
		});
	}

	function exportChart(chart, filename) {
		if (chart) {
			const link = document.createElement('a');
			link.download = filename;
			link.href = chart.toBase64Image();
			link.click();
		}
	}
</script>

<?= $this->endSection() ?>
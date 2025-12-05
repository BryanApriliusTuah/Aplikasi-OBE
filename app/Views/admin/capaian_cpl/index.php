<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="card">
	<div class="card-body">
		<h2 class="mb-4">Capaian CPL</h2>

		<!-- Tab Navigation -->
		<ul class="nav nav-tabs mb-4" id="cplTabs" role="tablist">
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
			<!-- <li class="nav-item" role="presentation">
				<button class="nav-link" id="all-subjects-tab" data-bs-toggle="tab" data-bs-target="#allSubjects" type="button" role="tab">
					<i class="bi bi-grid-3x3"></i> Seluruh Mata Kuliah
				</button>
			</li> -->
		</ul>

		<!-- Tab Content -->
		<div class="tab-content" id="cplTabContent">
			<!-- Individual Tab -->
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
									<select class="form-select" id="mahasiswaSelect" name="mahasiswa_id" required disabled>
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

				<!-- Chart Section Individual -->
				<div id="chartSectionIndividual" class="d-none"></div>

				<!-- Detailed Calculation Table Individual -->
				<div id="detailCalculationIndividual" class="d-none">
					<div class="card mt-4">
						<div class="card-header bg-secondary text-white">
							<h5 class="mb-0"><i class="bi bi-table"></i> Detail Perhitungan CPL per Mahasiswa</h5>
						</div>
						<div class="card-body">
							<div id="detailCalculationContent"></div>
						</div>
					</div>
				</div>

				<!-- Empty State Individual -->
				<div id="emptyStateIndividual" class="text-center py-5">
					<i class="bi bi-bar-chart" style="font-size: 4rem; color: #ccc;"></i>
					<p class="text-muted mt-3">Pilih mahasiswa dan klik tombol search untuk melihat grafik capaian CPL</p>
				</div>
			</div>

			<!-- Comparative Tab -->
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

				<!-- Chart Section Comparative -->
				<div id="chartSectionComparative" class="d-none"></div>

				<!-- Detailed Calculation Table Comparative -->
				<div id="detailCalculationComparative" class="d-none">
					<div class="card mt-4">
						<div class="card-header bg-secondary text-white">
							<h5 class="mb-0"><i class="bi bi-table"></i> Detail Perhitungan CPL per Angkatan</h5>
						</div>
						<div class="card-body">
							<div id="detailCalculationComparativeContent"></div>
						</div>
					</div>
				</div>

				<!-- Empty State Comparative -->
				<div id="emptyStateComparative" class="text-center py-5">
					<i class="bi bi-people" style="font-size: 4rem; color: #ccc;"></i>
					<p class="text-muted mt-3">Pilih program studi dan tahun angkatan untuk melihat grafik rata-rata capaian CPL</p>
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
									<small class="text-muted">Menampilkan rata-rata CPL dari semua mahasiswa aktif di semua angkatan</small>
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

				<!-- Chart Section Keseluruhan -->
				<div id="chartSectionKeseluruhan" class="d-none"></div>

				<!-- Detailed Calculation Table Keseluruhan -->
				<div id="detailCalculationKeseluruhan" class="d-none">
					<div class="card mt-4">
						<div class="card-header bg-secondary text-white">
							<h5 class="mb-0"><i class="bi bi-table"></i> Detail Perhitungan CPL Keseluruhan</h5>
						</div>
						<div class="card-body">
							<div id="detailCalculationKeseluruhanContent"></div>
						</div>
					</div>
				</div>

				<!-- Empty State Keseluruhan -->
				<div id="emptyStateKeseluruhan" class="text-center py-5">
					<i class="bi bi-bar-chart-line" style="font-size: 4rem; color: #ccc;"></i>
					<p class="text-muted mt-3">Pilih program studi untuk melihat grafik rata-rata capaian CPL dari semua angkatan</p>
				</div>
			</div>

			<!-- All Subjects Tab (NEW) -->
			<div class="tab-pane fade" id="allSubjects" role="tabpanel">
				<!-- Filter Section All Subjects -->
				<div class="card mb-4">
					<div class="card-header bg-light">
						<h5 class="mb-0"><i class="bi bi-funnel"></i> Filter</h5>
					</div>
					<div class="card-body">
						<form id="filterAllSubjectsForm">
							<div class="row g-3">
								<div class="col-md-10">
									<label for="programStudiAllSelect" class="form-label">Program Studi <span class="text-danger">*</span></label>
									<select class="form-select" id="programStudiAllSelect" name="program_studi" required>
										<option value="">-- Pilih Program Studi --</option>
										<?php foreach ($programStudi as $prodi): ?>
											<option value="<?= esc($prodi) ?>"><?= esc($prodi) ?></option>
										<?php endforeach; ?>
									</select>
									<small class="text-muted">Menampilkan semua mata kuliah aktif dari semua tahun akademik</small>
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

				<!-- Chart Section All Subjects -->
				<div id="chartSectionAllSubjects" class="d-none"></div>

				<!-- Empty State All Subjects -->
				<div id="emptyStateAllSubjects" class="text-center py-5">
					<i class="bi bi-grid-3x3" style="font-size: 4rem; color: #ccc;"></i>
					<p class="text-muted mt-3">Pilih program studi untuk melihat capaian CPL di semua mata kuliah (semua tahun akademik)</p>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Detail CPL -->
<div class="modal fade" id="detailCplModal" tabindex="-1">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title" id="detailCplModalTitle">Detail Capaian CPL</h5>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
	// Dynamic passing threshold from grade configuration
	const passingThreshold = <?= json_encode($passing_threshold ?? 65) ?>;

	let cplChartIndividual = null;
	let cplChartComparative = null;
	let cplChartKeseluruhan = null;
	let cplChartAllSubjects = null;
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

		// Initialize Select2 on Program Studi dropdown (Comparative Tab)
		$('#programStudiComparativeSelect').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Program Studi --',
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

		// Initialize Select2 on Tahun Angkatan dropdown (Comparative Tab)
		$('#tahunAngkatanComparativeSelect').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Tahun Angkatan --',
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

		// Initialize Select2 on Program Studi dropdown (Keseluruhan Tab)
		$('#programStudiKeseluruhanSelect').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Program Studi --',
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

		// All Subjects Tab Events
		$('#filterAllSubjectsForm').on('submit', function(e) {
			e.preventDefault();
			loadAllSubjectsChartData();
		});

		// Export buttons
		$('#exportChartIndividualBtn').on('click', function() {
			exportChart(cplChartIndividual, 'capaian-cpl-individual.png');
		});

		$('#exportChartComparativeBtn').on('click', function() {
			exportChart(cplChartComparative, 'capaian-cpl-comparative.png');
		});
	});


	function loadMahasiswa() {
		const programStudi = $('#programStudiSelect').val();
		const tahunAngkatan = $('#tahunAngkatanSelect').val();

		$.ajax({
			url: '<?= base_url("admin/capaian-cpl/mahasiswa") ?>',
			method: 'GET',
			data: {
				program_studi: programStudi,
				tahun_angkatan: tahunAngkatan
			},
			success: function(response) {
				const mahasiswaSelect = $('#mahasiswaSelect');

				// Clear existing options
				mahasiswaSelect.html('<option value="">-- Pilih Mahasiswa --</option>');

				if (response.length > 0) {
					response.forEach(function(mhs) {
						mahasiswaSelect.append(`<option value="${mhs.id}">${mhs.nim} - ${mhs.nama_lengkap}</option>`);
					});
					mahasiswaSelect.prop('disabled', false);
				} else {
					mahasiswaSelect.prop('disabled', true);
				}

				// Refresh Select2 after updating options
				mahasiswaSelect.trigger('change.select2');
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
		$('#calculationExplanationIndividual').addClass('d-none');
		$('#detailCalculationIndividual').addClass('d-none');
		$('#chartSectionIndividual').removeClass('d-none').html(getLoadingHTML());

		$.ajax({
			url: '<?= base_url("admin/capaian-cpl/chart-data") ?>',
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
			url: '<?= base_url("admin/capaian-cpl/comparative-data") ?>',
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
			url: '<?= base_url("admin/capaian-cpl/keseluruhan-data") ?>',
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

	function loadAllSubjectsChartData() {
		const programStudi = $('#programStudiAllSelect').val();

		if (!programStudi) {
			alert('Silakan pilih program studi');
			return;
		}

		$('#emptyStateAllSubjects').addClass('d-none');
		$('#chartSectionAllSubjects').removeClass('d-none').html(getLoadingHTML('secondary'));

		$.ajax({
			url: '<?= base_url("admin/capaian-cpl/all-subjects-data") ?>',
			method: 'GET',
			data: {
				program_studi: programStudi
			},
			success: function(response) {
				if (response.success) {
					displayAllSubjectsChart(response);
					displayAllSubjectsInfo(response);
				} else {
					showError('emptyStateAllSubjects', 'chartSectionAllSubjects', response.message);
				}
			},
			error: function() {
				showError('emptyStateAllSubjects', 'chartSectionAllSubjects', 'Terjadi kesalahan saat memuat data');
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

	function displayAllSubjectsInfo(response) {
		const info = `
        <div class="row">
            <div class="col-md-6">
                <strong>Program Studi:</strong> ${response.programStudi}
            </div>
            <div class="col-md-6">
                <strong>Total Mata Kuliah (Semua Tahun):</strong> ${response.totalMataKuliah} mata kuliah aktif
            </div>
        </div>
    `;
		$('#infoContentAllSubjects').html(info);
		$('#infoSectionAllSubjects').removeClass('d-none');
	}

	function displayIndividualChart(response) {
		const chartHTML = createChartHTML('Individual', 'primary', 'cplChartIndividual');
		$('#chartSectionIndividual').html(chartHTML);

		bindExportButton('#exportChartIndividualBtn', cplChartIndividual, 'capaian-cpl-individual.png');

		const ctx = document.getElementById('cplChartIndividual').getContext('2d');
		if (cplChartIndividual) cplChartIndividual.destroy();

		cplChartIndividual = createBarChart(ctx, response.chartData, 'Capaian CPL Mahasiswa', 'rgba(13, 110, 253, 0.8)', 'rgba(13, 110, 253, 1)');

		// Show calculation explanation
		$('#calculationExplanationIndividual').removeClass('d-none');

		// Display detailed calculation breakdown
		displayDetailedCalculation(response);
	}

	function displayDetailedCalculation(response) {
		const mahasiswaId = $('#mahasiswaSelect').val();

		if (!response.chartData || !response.chartData.details) {
			$('#detailCalculationIndividual').addClass('d-none');
			return;
		}

		let html = '<div class="table-responsive"><table id="individualDetailTable" class="table table-bordered table-hover">';
		html += '<thead class="table-light">';
		html += '<tr>';
		html += '<th width="5%" class="text-center">No</th>';
		html += '<th width="12%">Kode CPL</th>';
		html += '<th width="35%">Deskripsi CPL</th>';
		html += '<th width="12%" class="text-center">Jenis CPL</th>';
		html += '<th width="10%" class="text-center">Jumlah CPMK</th>';
		html += '<th width="10%" class="text-center">Jumlah MK</th>';
		html += '<th width="10%" class="text-center">Capaian (%)</th>';
		html += '<th width="6%" class="text-center">Aksi</th>';
		html += '</tr>';
		html += '</thead>';
		html += '<tbody>';

		response.chartData.details.forEach((cpl, index) => {
			html += `
				<tr>
					<td class="text-center">${index + 1}</td>
					<td><strong>${cpl.kode_cpl}</strong></td>
					<td>${cpl.deskripsi}</td>
					<td class="text-center"><span class="badge bg-primary">${cpl.jenis_cpl}</span></td>
					<td class="text-center">${cpl.jumlah_cpmk}</td>
					<td class="text-center">${cpl.jumlah_mk}</td>
					<td class="text-center"><strong>${cpl.rata_rata.toFixed(2)}%</strong></td>
					<td class="text-center">
						<button class="btn btn-sm btn-primary" onclick="loadCplCalculationDetail(${cpl.cpl_id}, '${cpl.kode_cpl}')">
							<i class="bi bi-eye"></i>
						</button>
					</td>
				</tr>
			`;
		});

		html += '</tbody></table></div>';

		$('#detailCalculationContent').html(html);
		$('#detailCalculationIndividual').removeClass('d-none');

		// Destroy existing DataTable if it exists
		if ($.fn.DataTable.isDataTable('#individualDetailTable')) {
			$('#individualDetailTable').DataTable().destroy();
		}

		// Initialize DataTable with pagination
		$('#individualDetailTable').DataTable({
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

	function loadCplCalculationDetail(cplId, kodeCpl) {
		const mahasiswaId = $('#mahasiswaSelect').val();

		// Set modal title
		$('#detailCplModalTitle').text(`Detail Perhitungan ${kodeCpl}`);

		// Show loading state in modal
		$('#detailCplModalContent').html(`
			<div class="text-center py-4">
				<div class="spinner-border text-primary" role="status">
					<span class="visually-hidden">Loading...</span>
				</div>
				<p class="mt-3 text-muted">Memuat detail perhitungan...</p>
			</div>
		`);

		// Show modal
		const modal = new bootstrap.Modal(document.getElementById('detailCplModal'));
		modal.show();

		$.ajax({
			url: '<?= base_url("admin/capaian-cpl/detail-calculation") ?>',
			method: 'GET',
			data: {
				mahasiswa_id: mahasiswaId,
				cpl_id: cplId
			},
			success: function(response) {
				if (response.success) {
					displayCplCalculationDetail(kodeCpl, response.data, response.summary);
				} else {
					$('#detailCplModalContent').html('<div class="alert alert-warning mb-0">' + (response.message || 'Tidak ada data') + '</div>');
				}
			},
			error: function() {
				$('#detailCplModalContent').html('<div class="alert alert-danger mb-0">Terjadi kesalahan saat memuat data</div>');
			}
		});
	}

	function displayCplCalculationDetail(kodeCpl, data, summary) {
		let html = `
			<div class="table-responsive">
				<table class="table table-bordered table-sm mb-0">
					<thead class="table-primary">
						<tr>
							<th width="5%" class="text-center">No</th>
							<th width="15%">Kode CPMK</th>
							<th width="25%">Mata Kuliah</th>
							<th width="12%" class="text-center">Tahun Akademik</th>
							<th width="10%" class="text-center">Kelas</th>
							<th width="10%" class="text-center">Nilai CPMK</th>
							<th width="10%" class="text-center">Bobot (%)</th>
						</tr>
					</thead>
					<tbody>
		`;

		if (data.length === 0) {
			html += `
				<tr>
					<td colspan="7" class="text-center text-muted">Belum ada data nilai untuk CPL ini</td>
				</tr>
			`;
		} else {
			data.forEach((item, index) => {
				html += `
					<tr>
						<td class="text-center">${index + 1}</td>
						<td><strong>${item.kode_cpmk}</strong></td>
						<td><small>${item.kode_mk} - ${item.nama_mk}</small></td>
						<td class="text-center">${item.tahun_akademik}</td>
						<td class="text-center">${item.kelas}</td>
						<td class="text-center">${parseFloat(item.nilai_cpmk).toFixed(2)}</td>
						<td class="text-center">${parseFloat(item.bobot).toFixed(0)}%</td>
					</tr>
				`;
			});
		}

		// Summary row
		html += `
					</tbody>
					<tfoot class="table-light">
						<tr>
							<td colspan="5" class="text-end"><strong>TOTAL:</strong></td>
							<td class="text-center"><strong>${summary.nilai_cpl.toFixed(2)}</strong></td>
							<td class="text-center"><strong>${summary.total_bobot.toFixed(0)}%</strong></td>
						</tr>
						<tr class="table-success">
							<td colspan="6" class="text-end"><strong>Capaian CPL (%) = (${summary.nilai_cpl.toFixed(2)} / ${summary.total_bobot.toFixed(0)}) × 100</strong></td>
							<td class="text-center"><h6 class="mb-0"><strong>${summary.capaian_cpl.toFixed(2)}%</strong></h6></td>
						</tr>
					</tfoot>
				</table>
			</div>
		`;

		$('#detailCplModalContent').html(html);
	}

	function displayComparativeChart(response) {
		const chartHTML = createChartHTML('Comparative', 'primary', 'cplChartComparative');
		$('#chartSectionComparative').html(chartHTML);

		bindExportButton('#exportChartComparativeBtn', cplChartComparative, 'capaian-cpl-comparative.png');

		const ctx = document.getElementById('cplChartComparative').getContext('2d');
		if (cplChartComparative) cplChartComparative.destroy();

		cplChartComparative = createBarChart(ctx, response.chartData, 'Rata-rata Capaian CPL (Angkatan)', 'rgba(13, 110, 253, 0.8)', 'rgba(13, 110, 253, 1)');

		// Display detailed calculation
		displayComparativeDetailedCalculation(response);
	}

	function displayKeseluruhanChart(response) {
		const chartHTML = createChartHTML('Keseluruhan', 'primary', 'cplChartKeseluruhan');
		$('#chartSectionKeseluruhan').html(chartHTML);

		bindExportButton('#exportChartKeseluruhanBtn', cplChartKeseluruhan, 'capaian-cpl-keseluruhan.png');

		const ctx = document.getElementById('cplChartKeseluruhan').getContext('2d');
		if (cplChartKeseluruhan) cplChartKeseluruhan.destroy();

		cplChartKeseluruhan = createBarChart(ctx, response.chartData, 'Rata-rata Capaian CPL Keseluruhan', 'rgba(13, 110, 253, 0.8)', 'rgba(13, 110, 253, 1)');

		// Display detailed calculation
		displayKeseluruhanDetailedCalculation(response);
	}

	function displayKeseluruhanDetailedCalculation(response) {
		if (!response.chartData || !response.chartData.details) {
			$('#detailCalculationKeseluruhan').addClass('d-none');
			return;
		}

		let html = '<div class="table-responsive"><table id="keseluruhanDetailTable" class="table table-bordered table-hover">';
		html += '<thead class="table-light">';
		html += '<tr>';
		html += '<th width="5%" class="text-center">No</th>';
		html += '<th width="12%">Kode CPL</th>';
		html += '<th width="43%">Deskripsi CPL</th>';
		html += '<th width="12%" class="text-center">Jenis CPL</th>';
		html += '<th width="10%" class="text-center">Jumlah Mahasiswa</th>';
		html += '<th width="11%" class="text-center">Rata-rata (%)</th>';
		html += '<th width="7%" class="text-center">Aksi</th>';
		html += '</tr>';
		html += '</thead>';
		html += '<tbody>';

		response.chartData.details.forEach((cpl, index) => {
			html += `
				<tr>
					<td class="text-center">${index + 1}</td>
					<td><strong>${cpl.kode_cpl}</strong></td>
					<td>${cpl.deskripsi}</td>
					<td class="text-center"><span class="badge bg-primary">${cpl.jenis_cpl}</span></td>
					<td class="text-center">${cpl.jumlah_mahasiswa}</td>
					<td class="text-center"><strong>${cpl.rata_rata.toFixed(2)}%</strong></td>
					<td class="text-center">
						<button class="btn btn-sm btn-primary" onclick="loadKeseluruhanCplDetail(${cpl.cpl_id}, '${cpl.kode_cpl}', ${index})">
							<i class="bi bi-eye"></i>
						</button>
					</td>
				</tr>
			`;
		});

		html += '</tbody></table></div>';

		$('#detailCalculationKeseluruhanContent').html(html);
		$('#detailCalculationKeseluruhan').removeClass('d-none');

		// Destroy existing DataTable if it exists
		if ($.fn.DataTable.isDataTable('#keseluruhanDetailTable')) {
			$('#keseluruhanDetailTable').DataTable().destroy();
		}

		// Initialize DataTable with pagination
		$('#keseluruhanDetailTable').DataTable({
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

	function loadKeseluruhanCplDetail(cplId, kodeCpl, index) {
		const programStudi = $('#programStudiKeseluruhanSelect').val();

		// Set modal title
		$('#detailCplModalTitle').text(`Detail Perhitungan ${kodeCpl} - Semua Angkatan`);

		// Show loading state in modal
		$('#detailCplModalContent').html(`
			<div class="text-center py-4">
				<div class="spinner-border text-primary" role="status">
					<span class="visually-hidden">Loading...</span>
				</div>
				<p class="mt-3 text-muted">Memuat detail perhitungan...</p>
			</div>
		`);

		// Show modal
		const modal = new bootstrap.Modal(document.getElementById('detailCplModal'));
		modal.show();

		$.ajax({
			url: '<?= base_url("admin/capaian-cpl/keseluruhan-detail-calculation") ?>',
			method: 'GET',
			data: {
				cpl_id: cplId,
				program_studi: programStudi
			},
			success: function(response) {
				if (response.success) {
					displayKeseluruhanCplCalculationDetail(index, kodeCpl, response.data, response.summary);
				} else {
					$('#detailCplModalContent').html('<div class="alert alert-warning mb-0">' + (response.message || 'Tidak ada data') + '</div>');
				}
			},
			error: function() {
				$('#detailCplModalContent').html('<div class="alert alert-danger mb-0">Terjadi kesalahan saat memuat data</div>');
			}
		});
	}

	function displayKeseluruhanCplCalculationDetail(index, kodeCpl, data, summary) {
		let html = ``;

		if (data.length === 0) {
			html += '<div class="alert alert-info mb-0">Belum ada mahasiswa yang memiliki nilai untuk CPL ini</div>';
		} else {
			html += `
				<div class="table-responsive">
					<table id="keseluruhanCplDetailTable_${index}" class="table table-bordered table-sm table-hover mb-0">
						<thead class="table-primary">
							<tr>
								<th width="8%" class="text-center">No</th>
								<th width="15%">NIM</th>
								<th width="40%">Nama Mahasiswa</th>
								<th width="12%" class="text-center">Angkatan</th>
								<th width="25%" class="text-center">Capaian CPL (%)</th>
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
						<td class="text-center"><strong>${mhs.capaian_cpl.toFixed(2)}%</strong></td>
					</tr>
				`;
			});

			html += `
						</tbody>
						<tfoot class="table-light">
							<tr>
								<td colspan="4" class="text-end"><strong>Total dari ${summary.jumlah_mahasiswa} mahasiswa (semua angkatan):</strong></td>
								<td class="text-center"><strong>${summary.total_cpl.toFixed(2)}%</strong></td>
							</tr>
							<tr class="table-success">
								<td colspan="4" class="text-end"><strong>Rata-rata CPL = ${summary.total_cpl.toFixed(2)}% / ${summary.jumlah_mahasiswa} mahasiswa</strong></td>
								<td class="text-center"><h6 class="mb-0"><strong>${summary.rata_rata.toFixed(2)}%</strong></h6></td>
							</tr>
						</tfoot>
					</table>
				</div>
			`;
		}

		$('#detailCplModalContent').html(html);

		// Initialize DataTable with pagination
		if (data.length > 0) {
			// Destroy existing DataTable if it exists
			if ($.fn.DataTable.isDataTable(`#keseluruhanCplDetailTable_${index}`)) {
				$(`#keseluruhanCplDetailTable_${index}`).DataTable().destroy();
			}

			$(`#keseluruhanCplDetailTable_${index}`).DataTable({
				pageLength: 10,
				language: {
					url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
				}
			});
		}
	}

	function displayComparativeDetailedCalculation(response) {
		if (!response.chartData || !response.chartData.details) {
			$('#detailCalculationComparative').addClass('d-none');
			return;
		}

		let html = '<div class="table-responsive"><table id="comparativeDetailTable" class="table table-bordered table-hover">';
		html += '<thead class="table-light">';
		html += '<tr>';
		html += '<th width="5%" class="text-center">No</th>';
		html += '<th width="12%">Kode CPL</th>';
		html += '<th width="38%">Deskripsi CPL</th>';
		html += '<th width="12%" class="text-center">Jenis CPL</th>';
		html += '<th width="10%" class="text-center">Jumlah Mahasiswa</th>';
		html += '<th width="13%" class="text-center">Rata-rata (%)</th>';
		html += '<th width="10%" class="text-center">Aksi</th>';
		html += '</tr>';
		html += '</thead>';
		html += '<tbody>';

		response.chartData.details.forEach((cpl, index) => {
			html += `
				<tr>
					<td class="text-center">${index + 1}</td>
					<td><strong>${cpl.kode_cpl}</strong></td>
					<td>${cpl.deskripsi}</td>
					<td class="text-center"><span class="badge bg-primary">${cpl.jenis_cpl}</span></td>
					<td class="text-center">${cpl.jumlah_mahasiswa}</td>
					<td class="text-center"><strong>${cpl.rata_rata.toFixed(2)}%</strong></td>
					<td class="text-center">
						<button class="btn btn-sm btn-primary" onclick="loadComparativeCplDetail(${cpl.cpl_id}, '${cpl.kode_cpl}', ${index})">
							<i class="bi bi-eye"></i>
						</button>
					</td>
				</tr>
			`;
		});

		html += '</tbody></table></div>';

		$('#detailCalculationComparativeContent').html(html);
		$('#detailCalculationComparative').removeClass('d-none');

		// Destroy existing DataTable if it exists
		if ($.fn.DataTable.isDataTable('#comparativeDetailTable')) {
			$('#comparativeDetailTable').DataTable().destroy();
		}

		// Initialize DataTable with pagination
		$('#comparativeDetailTable').DataTable({
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

	function loadComparativeCplDetail(cplId, kodeCpl, index) {
		const programStudi = $('#programStudiComparativeSelect').val();
		const tahunAngkatan = $('#tahunAngkatanComparativeSelect').val();

		// Set modal title
		$('#detailCplModalTitle').text(`Detail Perhitungan ${kodeCpl} - Angkatan`);

		// Show loading state in modal
		$('#detailCplModalContent').html(`
			<div class="text-center py-4">
				<div class="spinner-border text-primary" role="status">
					<span class="visually-hidden">Loading...</span>
				</div>
				<p class="mt-3 text-muted">Memuat detail perhitungan...</p>
			</div>
		`);

		// Show modal
		const modal = new bootstrap.Modal(document.getElementById('detailCplModal'));
		modal.show();

		$.ajax({
			url: '<?= base_url("admin/capaian-cpl/comparative-detail-calculation") ?>',
			method: 'GET',
			data: {
				cpl_id: cplId,
				program_studi: programStudi,
				tahun_angkatan: tahunAngkatan
			},
			success: function(response) {
				if (response.success) {
					displayComparativeCplCalculationDetail(index, kodeCpl, response.data, response.summary);
				} else {
					$('#detailCplModalContent').html('<div class="alert alert-warning mb-0">' + (response.message || 'Tidak ada data') + '</div>');
				}
			},
			error: function() {
				$('#detailCplModalContent').html('<div class="alert alert-danger mb-0">Terjadi kesalahan saat memuat data</div>');
			}
		});
	}

	function displayComparativeCplCalculationDetail(index, kodeCpl, data, summary) {
		let html = ``;

		if (data.length === 0) {
			html += '<div class="alert alert-info mb-0">Belum ada mahasiswa yang memiliki nilai untuk CPL ini</div>';
		} else {
			html += `
				<div class="table-responsive">
					<table id="comparativeCplDetailTable_${index}" class="table table-bordered table-sm table-hover mb-0">
						<thead class="table-primary">
							<tr>
								<th width="10%" class="text-center">No</th>
								<th width="20%">NIM</th>
								<th width="50%">Nama Mahasiswa</th>
								<th width="20%" class="text-center">Capaian CPL (%)</th>
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
						<td class="text-center"><strong>${mhs.capaian_cpl.toFixed(2)}%</strong></td>
					</tr>
				`;
			});

			html += `
						</tbody>
						<tfoot class="table-light">
							<tr>
								<td colspan="3" class="text-end"><strong>Total dari ${summary.jumlah_mahasiswa} mahasiswa:</strong></td>
								<td class="text-center"><strong>${summary.total_cpl.toFixed(2)}%</strong></td>
							</tr>
							<tr class="table-success">
								<td colspan="3" class="text-end"><strong>Rata-rata CPL = ${summary.total_cpl.toFixed(2)}% / ${summary.jumlah_mahasiswa} mahasiswa</strong></td>
								<td class="text-center"><h6 class="mb-0"><strong>${summary.rata_rata.toFixed(2)}%</strong></h6></td>
							</tr>
						</tfoot>
					</table>
				</div>
			`;
		}

		$('#detailCplModalContent').html(html);

		// Initialize DataTable with pagination
		if (data.length > 0) {
			// Destroy existing DataTable if it exists
			if ($.fn.DataTable.isDataTable(`#comparativeCplDetailTable_${index}`)) {
				$(`#comparativeCplDetailTable_${index}`).DataTable().destroy();
			}

			$(`#comparativeCplDetailTable_${index}`).DataTable({
				pageLength: 10,
				language: {
					url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
				}
			});
		}
	}


	function displayAllSubjectsChart(response) {
		const chartHTML = `
        <div class="card">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Capaian CPL di Semua Mata Kuliah (Semua Tahun)</h5>
                <button class="btn btn-light btn-sm" id="exportChartAllSubjectsBtn">
                    <i class="bi bi-download"></i> Export PNG
                </button>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> <strong>Info:</strong> Grafik menampilkan ${response.chartData.datasets.length} mata kuliah dari semua tahun akademik.
                </div>
                <canvas id="cplChartAllSubjects" height="120"></canvas>
            </div>
        </div>
    `;
		$('#chartSectionAllSubjects').html(chartHTML);

		bindExportButton('#exportChartAllSubjectsBtn', cplChartAllSubjects, 'capaian-cpl-all-subjects.png');

		const ctx = document.getElementById('cplChartAllSubjects').getContext('2d');
		if (cplChartAllSubjects) cplChartAllSubjects.destroy();

		cplChartAllSubjects = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: response.chartData.labels,
				datasets: response.chartData.datasets
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
							boxWidth: 12,
							font: {
								size: 10
							},
							padding: 8
						}
					},
					title: {
						display: true,
						text: 'Capaian CPL di Semua Mata Kuliah (Semua Tahun Akademik)',
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
								return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + '%';
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
							size: 10
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

	function displayAllSubjectsSummaryTable(summaryData) {
		let html = `
        <div class="card mt-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-table"></i> Ringkasan Capaian per Mata Kuliah</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Kode MK</th>
                                <th width="28%">Nama Mata Kuliah</th>
                                <th width="8%" class="text-center">Kelas</th>
                                <th width="8%" class="text-center">Semester</th>
                                <th width="12%" class="text-center">Tahun Akademik</th>
                                <th width="10%" class="text-center">Jumlah Mahasiswa</th>
                                <th width="10%" class="text-center">Rata-rata CPL (%)</th>
                                <th width="9%" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
    `;

		summaryData.forEach((item, index) => {
			const statusBadge = getStatusBadge(item.rata_rata_keseluruhan);
			html += `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${item.kode_mk}</strong></td>
                <td>${item.nama_mk}</td>
                <td class="text-center">${item.kelas}</td>
                <td class="text-center">${item.semester}</td>
                <td class="text-center"><span class="badge bg-primary">${item.tahun_akademik}</span></td>
                <td class="text-center">${item.jumlah_mahasiswa}</td>
                <td class="text-center"><strong>${item.rata_rata_keseluruhan.toFixed(2)}%</strong></td>
                <td class="text-center">${statusBadge}</td>
            </tr>
        `;
		});

		html += `
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;

		$('#chartSectionAllSubjects').append(html);
	}

	function displayIndividualDetailTable(details) {
		let html = '';
		details.forEach((item, index) => {
			const statusBadge = getStatusBadge(item.rata_rata);
			html += `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${item.kode_cpl}</strong></td>
                <td>${item.deskripsi}</td>
                <td><span class="badge bg-info">${item.jenis_cpl}</span></td>
                <td class="text-center">${item.jumlah_cpmk}</td>
                <td class="text-center"><strong>${item.rata_rata.toFixed(2)}%</strong></td>
                <td class="text-center">${statusBadge}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-info" onclick="showCplDetail(${item.cpl_id}, '${item.kode_cpl}')">
                        <i class="bi bi-eye"></i>
                    </button>
                </td>
            </tr>
        `;
		});
		$('#detailTableBodyIndividual').html(html);
	}

	function displayComparativeDetailTable(details) {
		let html = '';
		details.forEach((item, index) => {
			const statusBadge = getStatusBadge(item.rata_rata);
			html += `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${item.kode_cpl}</strong></td>
                <td>${item.deskripsi}</td>
                <td><span class="badge bg-info">${item.jenis_cpl}</span></td>
                <td class="text-center">${item.jumlah_mahasiswa}</td>
                <td class="text-center"><strong>${item.rata_rata.toFixed(2)}%</strong></td>
                <td class="text-center">${statusBadge}</td>
            </tr>
        `;
		});
		$('#detailTableBodyComparative').html(html);
	}

	function showCplDetail(cplId, kodeCpl) {
		const mahasiswaId = $('#mahasiswaSelect').val();

		$('#detailCplModalTitle').text(`Detail Capaian ${kodeCpl}`);
		$('#detailCplModal').modal('show');
		$('#detailCplModalContent').html(getLoadingHTML());

		$.ajax({
			url: '<?= base_url("admin/capaian-cpl/detail-data") ?>',
			method: 'GET',
			data: {
				mahasiswa_id: mahasiswaId,
				cpl_id: cplId
			},
			success: function(response) {
				if (response.success) {
					displayCplDetailModal(response.data, response.cpl);
				} else {
					$('#detailCplModalContent').html(`<div class="alert alert-warning">${response.message || 'Tidak ada data'}</div>`);
				}
			},
			error: function() {
				$('#detailCplModalContent').html(`<div class="alert alert-danger">Terjadi kesalahan saat memuat data</div>`);
			}
		});
	}

	function displayCplDetailModal(data, cpl) {
		let html = `
        <div class="mb-4">
            <h6><strong>CPL:</strong> ${cpl.kode_cpl}</h6>
            <p class="text-muted">${cpl.deskripsi}</p>
            <span class="badge bg-info">Jenis: ${getJenisCplLabel(cpl.jenis_cpl)}</span>
        </div>
    `;

		if (data.length === 0) {
			html += `<div class="alert alert-info"><i class="bi bi-info-circle"></i> Belum ada data CPMK yang terkait dengan CPL ini atau mahasiswa belum memiliki nilai.</div>`;
		} else {
			html += `
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Kode CPMK</th>
                            <th width="38%">Deskripsi CPMK</th>
                            <th width="10%" class="text-center">Jumlah MK</th>
                            <th width="10%" class="text-center">Rata-rata</th>
                            <th width="25%">Detail Mata Kuliah</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

			data.forEach((item, index) => {
				// Use dynamic threshold: passing+10 for "good", passing for "fair"
				const goodThreshold = passingThreshold + 10;
				const badgeClass = item.rata_rata >= goodThreshold ? 'success' : (item.rata_rata >= passingThreshold ? 'warning' : 'danger');

				let detailMk = '<ul class="mb-0" style="font-size: 0.85rem;">';
				if (item.detail_mk.length === 0) {
					detailMk += '<li class="text-muted">Belum ada nilai</li>';
				} else {
					item.detail_mk.forEach(mk => {
						detailMk += `<li>${mk.kode_mk} (${mk.tahun_akademik} - ${mk.kelas}): <strong>${parseFloat(mk.nilai_cpmk).toFixed(2)}</strong></li>`;
					});
				}
				detailMk += '</ul>';

				html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${item.kode_cpmk}</strong></td>
                    <td><small>${item.deskripsi_cpmk}</small></td>
                    <td class="text-center">${item.jumlah_nilai}</td>
                    <td class="text-center"><span class="badge bg-${badgeClass}">${item.rata_rata.toFixed(2)}</span></td>
                    <td>${detailMk}</td>
                </tr>
            `;
			});

			html += `</tbody></table></div>`;
		}

		$('#detailCplModalContent').html(html);
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

	function createChartHTML(type, color, canvasId, title = 'Grafik Capaian CPL') {
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

	function createTableHTML(type, showAction) {
		const actionHeader = showAction ? '<th width="5%" class="text-center">Aksi</th>' : '';

		return `
        <div class="card mt-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-table"></i> Detail Capaian CPL</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Kode CPL</th>
                                <th width="35%">Deskripsi CPL</th>
                                <th width="15%">Jenis CPL</th>
                                <th width="10%" class="text-center">${type === 'Individual' ? 'Jumlah CPMK' : 'Jumlah Mhs'}</th>
                                ${type === 'Subject' ? '<th width="10%" class="text-center">Jumlah CPMK</th>' : ''}
                                <th width="10%" class="text-center">Capaian (%)</th>
                                <th width="10%" class="text-center">Status</th>
                                ${actionHeader}
                            </tr>
                        </thead>
                        <tbody id="detailTableBody${type}">
                        </tbody>
                    </table>
                </div>
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

	function getJenisCplLabel(jenis) {
		const labels = {
			'P': 'Pengetahuan',
			'KK': 'Keterampilan Khusus',
			'S': 'Sikap',
			'KU': 'Keterampilan Umum'
		};
		return labels[jenis] || jenis;
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
		$('#calculationExplanationIndividual').addClass('d-none');
		$('#detailCalculationIndividual').addClass('d-none');
		$('#detailCalculationComparative').addClass('d-none');
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
<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="card">
	<div class="card-body">
		<h2 class="mb-4">Capaian CPL Mahasiswa</h2>

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

				<!-- Info Section Comparative -->
				<div id="infoSectionComparative" class="alert alert-info d-none">
					<h6 class="mb-2"><strong>Informasi:</strong></h6>
					<div id="infoContentComparative"></div>
				</div>

				<!-- Chart Section Comparative -->
				<div id="chartSectionComparative" class="d-none"></div>

				<!-- Empty State Comparative -->
				<div id="emptyStateComparative" class="text-center py-5">
					<i class="bi bi-people" style="font-size: 4rem; color: #ccc;"></i>
					<p class="text-muted mt-3">Pilih program studi dan tahun angkatan untuk melihat grafik rata-rata capaian CPL</p>
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

				<!-- Info Section All Subjects -->
				<div id="infoSectionAllSubjects" class="alert alert-info d-none">
					<h6 class="mb-2"><strong>Informasi:</strong></h6>
					<div id="infoContentAllSubjects"></div>
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

<script>
	// Dynamic passing threshold from grade configuration
	const passingThreshold = <?= json_encode($passing_threshold ?? 65) ?>;

	let cplChartIndividual = null;
	let cplChartComparative = null;
	let cplChartAllSubjects = null;
	let currentIndividualData = null;
	let currentComparativeData = null;

	$(document).ready(function() {
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
				mahasiswaSelect.html('<option value="">-- Pilih Mahasiswa --</option>');

				if (response.length > 0) {
					response.forEach(function(mhs) {
						mahasiswaSelect.append(`<option value="${mhs.id}">${mhs.nim} - ${mhs.nama_lengkap}</option>`);
					});
					mahasiswaSelect.prop('disabled', false);
				} else {
					mahasiswaSelect.prop('disabled', true);
				}
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

		let html = '';
		response.chartData.details.forEach((cpl, index) => {
			html += `
				<div class="card mb-3 ${index > 0 ? 'mt-3' : ''}">
					<div class="card-header bg-light">
						<h6 class="mb-0">
							<strong>${cpl.kode_cpl}</strong> - ${cpl.deskripsi}
							<span class="badge bg-info float-end">${cpl.jenis_cpl}</span>
						</h6>
					</div>
					<div class="card-body">
						<button class="btn btn-sm btn-primary mb-3" onclick="loadCplCalculationDetail(${cpl.cpl_id}, '${cpl.kode_cpl}')">
							<i class="bi bi-eye"></i> Lihat Detail Perhitungan
						</button>
						<div id="cplCalcDetail_${cpl.cpl_id}"></div>

						<div class="row mt-3">
							<div class="col-md-12">
								<table class="table table-sm table-bordered">
									<tr class="table-light">
										<td width="40%"><strong>Jumlah CPMK Terkait</strong></td>
										<td width="60%">${cpl.jumlah_cpmk} CPMK dari ${cpl.jumlah_mk} Mata Kuliah</td>
									</tr>
									<tr class="table-success">
										<td><strong>Capaian CPL (%)</strong></td>
										<td><h5 class="mb-0">${cpl.rata_rata.toFixed(2)}%</h5></td>
									</tr>
								</table>
							</div>
						</div>
					</div>
				</div>
			`;
		});

		$('#detailCalculationContent').html(html);
		$('#detailCalculationIndividual').removeClass('d-none');
	}

	function loadCplCalculationDetail(cplId, kodeCpl) {
		const mahasiswaId = $('#mahasiswaSelect').val();
		const targetDiv = $(`#cplCalcDetail_${cplId}`);

		// Check if already loaded
		if (targetDiv.html().trim() !== '') {
			targetDiv.html(''); // Toggle hide
			return;
		}

		targetDiv.html('<div class="text-center py-3"><div class="spinner-border spinner-border-sm" role="status"></div> Memuat detail...</div>');

		$.ajax({
			url: '<?= base_url("admin/capaian-cpl/detail-calculation") ?>',
			method: 'GET',
			data: {
				mahasiswa_id: mahasiswaId,
				cpl_id: cplId
			},
			success: function(response) {
				if (response.success) {
					displayCplCalculationDetail(cplId, response.data, response.summary);
				} else {
					targetDiv.html('<div class="alert alert-warning mb-0">' + (response.message || 'Tidak ada data') + '</div>');
				}
			},
			error: function() {
				targetDiv.html('<div class="alert alert-danger mb-0">Terjadi kesalahan saat memuat data</div>');
			}
		});
	}

	function displayCplCalculationDetail(cplId, data, summary) {
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
							<th width="13%" class="text-center">Kontribusi</th>
						</tr>
					</thead>
					<tbody>
		`;

		if (data.length === 0) {
			html += `
				<tr>
					<td colspan="8" class="text-center text-muted">Belum ada data nilai untuk CPL ini</td>
				</tr>
			`;
		} else {
			data.forEach((item, index) => {
				const kontribusi = item.bobot > 0 ? (item.nilai_cpmk * item.bobot / 100) : 0;
				html += `
					<tr>
						<td class="text-center">${index + 1}</td>
						<td><strong>${item.kode_cpmk}</strong></td>
						<td><small>${item.kode_mk} - ${item.nama_mk}</small></td>
						<td class="text-center">${item.tahun_akademik}</td>
						<td class="text-center">${item.kelas}</td>
						<td class="text-center"><span class="badge bg-info">${parseFloat(item.nilai_cpmk).toFixed(2)}</span></td>
						<td class="text-center">${parseFloat(item.bobot).toFixed(0)}%</td>
						<td class="text-center"><strong>${kontribusi.toFixed(2)}</strong></td>
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
							<td class="text-center"><strong>Σ Nilai CPMK</strong></td>
							<td class="text-center"><strong>${summary.total_bobot.toFixed(0)}%</strong></td>
							<td class="text-center"><strong>${summary.nilai_cpl.toFixed(2)}</strong></td>
						</tr>
						<tr class="table-success">
							<td colspan="7" class="text-end"><strong>Capaian CPL (%) = (${summary.nilai_cpl.toFixed(2)} / ${summary.total_bobot.toFixed(0)}) × 100</strong></td>
							<td class="text-center"><h6 class="mb-0"><strong>${summary.capaian_cpl.toFixed(2)}%</strong></h6></td>
						</tr>
					</tfoot>
				</table>
			</div>
		`;

		$(`#cplCalcDetail_${cplId}`).html(html);
	}

	function displayComparativeChart(response) {
		const chartHTML = createChartHTML('Comparative', 'success', 'cplChartComparative', 'Rata-rata Capaian CPL (Angkatan)');
		$('#chartSectionComparative').html(chartHTML);

		bindExportButton('#exportChartComparativeBtn', cplChartComparative, 'capaian-cpl-comparative.png');

		const ctx = document.getElementById('cplChartComparative').getContext('2d');
		if (cplChartComparative) cplChartComparative.destroy();

		cplChartComparative = createBarChart(ctx, response.chartData, 'Rata-rata Capaian CPL (Angkatan)', 'rgba(25, 135, 84, 0.8)', 'rgba(25, 135, 84, 1)');
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
		const gradient = ctx.createLinearGradient(0, 0, 0, 400);
		gradient.addColorStop(0, backgroundColor);
		gradient.addColorStop(1, backgroundColor.replace('0.8', '0.2'));

		return new Chart(ctx, {
			type: 'bar',
			data: {
				labels: chartData.labels,
				datasets: [{
					label: 'Capaian CPL (%)',
					data: chartData.data,
					backgroundColor: gradient,
					borderColor: borderColor,
					borderWidth: 2,
					borderRadius: 5,
					barThickness: 40
				}]
			},
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
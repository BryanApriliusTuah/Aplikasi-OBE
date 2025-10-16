<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="card">
	<div class="card-body">
		<h2 class="mb-4">Capaian CPMK Mahasiswa</h2>

		<!-- Tab Navigation -->
		<ul class="nav nav-tabs mb-4" id="cpmkTabs" role="tablist">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" id="single-subject-tab" data-bs-toggle="tab" data-bs-target="#singleSubject" type="button" role="tab">
					<i class="bi bi-book"></i> Per Mata Kuliah
				</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="multi-subject-tab" data-bs-toggle="tab" data-bs-target="#multiSubject" type="button" role="tab">
					<i class="bi bi-books"></i> Komparasi Mata Kuliah
				</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="all-subjects-tab" data-bs-toggle="tab" data-bs-target="#allSubjects" type="button" role="tab">
					<i class="bi bi-grid-3x3"></i> Semua Mata Kuliah
				</button>
			</li>
		</ul>

		<!-- Tab Content -->
		<div class="tab-content" id="cpmkTabContent">
			<!-- Single Subject Tab -->
			<div class="tab-pane fade show active" id="singleSubject" role="tabpanel">
				<!-- Filter Section -->
				<div class="card mb-4">
					<div class="card-header bg-light">
						<h5 class="mb-0"><i class="bi bi-funnel"></i> Filter Data</h5>
					</div>
					<div class="card-body">
						<form id="filterForm">
							<div class="row g-3">
								<div class="col-md-4">
									<label for="mataKuliahSelect" class="form-label">Mata Kuliah <span class="text-danger">*</span></label>
									<select class="form-select" id="mataKuliahSelect" name="mata_kuliah_id" required>
										<option value="">-- Pilih Mata Kuliah --</option>
										<?php foreach ($mataKuliah as $mk): ?>
											<option value="<?= $mk['id'] ?>"><?= esc($mk['kode_mk']) ?> - <?= esc($mk['nama_mk']) ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-md-3">
									<label for="tahunAkademikSelect" class="form-label">Tahun Akademik</label>
									<select class="form-select" id="tahunAkademikSelect" name="tahun_akademik">
										<option value="">-- Semua Tahun --</option>
										<?php foreach ($tahunAkademik as $ta): ?>
											<option value="<?= esc($ta) ?>"><?= esc($ta) ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-md-3">
									<label for="kelasSelect" class="form-label">Kelas</label>
									<select class="form-select" id="kelasSelect" name="kelas" disabled>
										<option value="">-- Semua Kelas --</option>
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

				<!-- Info Section -->
				<div id="infoSection" class="alert alert-info d-none">
					<h6 class="mb-2"><strong>Informasi:</strong></h6>
					<div id="infoContent"></div>
				</div>

				<!-- Chart Section -->
				<div id="chartSection" class="d-none"></div>

				<!-- Empty State -->
				<div id="emptyState" class="text-center py-5">
					<i class="bi bi-bar-chart" style="font-size: 4rem; color: #ccc;"></i>
					<p class="text-muted mt-3">Pilih mata kuliah dan klik "Tampilkan" untuk melihat grafik capaian CPMK</p>
				</div>
			</div>

			<!-- Multi Subject Tab -->
			<div class="tab-pane fade" id="multiSubject" role="tabpanel">
				<!-- Filter Section Multi Subject -->
				<div class="card mb-4">
					<div class="card-header bg-light">
						<h5 class="mb-0"><i class="bi bi-funnel"></i> Filter Mata Kuliah (Pilih Multiple)</h5>
					</div>
					<div class="card-body">
						<form id="filterMultiSubjectForm">
							<div class="row g-3">
								<div class="col-md-5">
									<label for="mataKuliahMultiSelect" class="form-label">Mata Kuliah (Pilih beberapa) <span class="text-danger">*</span></label>
									<select class="form-select" id="mataKuliahMultiSelect" name="mata_kuliah_ids[]" multiple required style="height: 150px;">
										<?php foreach ($mataKuliah as $mk): ?>
											<option value="<?= $mk['id'] ?>"><?= esc($mk['kode_mk']) ?> - <?= esc($mk['nama_mk']) ?></option>
										<?php endforeach; ?>
									</select>
									<small class="text-muted">Tahan Ctrl (Windows) atau Cmd (Mac) untuk pilih beberapa. Maksimal 5 mata kuliah.</small>
								</div>
								<div class="col-md-3">
									<label for="tahunAkademikMultiSelect" class="form-label">Tahun Akademik</label>
									<select class="form-select" id="tahunAkademikMultiSelect" name="tahun_akademik">
										<option value="">-- Semua Tahun --</option>
										<?php foreach ($tahunAkademik as $ta): ?>
											<option value="<?= esc($ta) ?>"><?= esc($ta) ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-md-2">
									<label for="kelasMultiSelect" class="form-label">Kelas</label>
									<select class="form-select" id="kelasMultiSelect" name="kelas">
										<option value="">-- Semua Kelas --</option>
										<option value="A">A</option>
										<option value="B">B</option>
										<option value="C">C</option>
										<option value="D">D</option>
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

				<!-- Chart Section Multi Subject -->
				<div id="chartSectionMulti" class="d-none"></div>

				<!-- Empty State Multi Subject -->
				<div id="emptyStateMulti" class="text-center py-5">
					<i class="bi bi-books" style="font-size: 4rem; color: #ccc;"></i>
					<p class="text-muted mt-3">Pilih beberapa mata kuliah untuk membandingkan capaian CPMK antar mata kuliah</p>
				</div>
			</div>

			<!-- All Subjects Tab -->
			<div class="tab-pane fade" id="allSubjects" role="tabpanel">
				<!-- Filter Section All Subjects -->
				<div class="card mb-4">
					<div class="card-header bg-light">
						<h5 class="mb-0"><i class="bi bi-funnel"></i> Filter</h5>
					</div>
					<div class="card-body">
						<form id="filterAllSubjectsForm">
							<div class="row g-3">
								<div class="col-md-4">
									<label for="tahunAkademikAllSelect" class="form-label">Tahun Akademik</label>
									<select class="form-select" id="tahunAkademikAllSelect" name="tahun_akademik">
										<option value="">-- Semua Tahun --</option>
										<?php foreach ($tahunAkademik as $ta): ?>
											<option value="<?= esc($ta) ?>"><?= esc($ta) ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-md-6">
									<label class="form-label d-block">&nbsp;</label>
									<small class="text-muted">Menampilkan semua mata kuliah yang memiliki data CPMK</small>
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
				<div id="chartSectionAll" class="d-none"></div>

				<!-- Empty State All Subjects -->
				<div id="emptyStateAll" class="text-center py-5">
					<i class="bi bi-grid-3x3" style="font-size: 4rem; color: #ccc;"></i>
					<p class="text-muted mt-3">Klik "Tampilkan" untuk melihat capaian CPMK di semua mata kuliah</p>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Detail Mahasiswa -->
<div class="modal fade" id="detailModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title" id="detailModalTitle">Detail Nilai Mahasiswa</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div id="detailModalContent">
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
	let cpmkChart = null;
	let cpmkChartMulti = null;
	let cpmkChartAll = null;
	let currentChartData = null;

	$(document).ready(function() {
		// Single Subject Tab Events
		$('#mataKuliahSelect, #tahunAkademikSelect').on('change', function() {
			const mataKuliahId = $('#mataKuliahSelect').val();
			const tahunAkademik = $('#tahunAkademikSelect').val();

			if (mataKuliahId) {
				loadKelas(mataKuliahId, tahunAkademik);
			} else {
				$('#kelasSelect').prop('disabled', true).html('<option value="">-- Semua Kelas --</option>');
			}
		});

		$('#filterForm').on('submit', function(e) {
			e.preventDefault();
			loadChartData();
		});

		// Multi Subject Tab Events
		$('#filterMultiSubjectForm').on('submit', function(e) {
			e.preventDefault();
			loadMultiSubjectChartData();
		});

		// All Subjects Tab Events
		$('#filterAllSubjectsForm').on('submit', function(e) {
			e.preventDefault();
			loadAllSubjectsChartData();
		});
	});

	function loadKelas(mataKuliahId, tahunAkademik) {
		$.ajax({
			url: '<?= base_url("admin/capaian-cpmk/get-kelas") ?>',
			method: 'GET',
			data: {
				mata_kuliah_id: mataKuliahId,
				tahun_akademik: tahunAkademik
			},
			success: function(response) {
				const kelasSelect = $('#kelasSelect');
				kelasSelect.html('<option value="">-- Semua Kelas --</option>');

				if (response.length > 0) {
					response.forEach(function(kelas) {
						kelasSelect.append(`<option value="${kelas}">${kelas}</option>`);
					});
					kelasSelect.prop('disabled', false);
				} else {
					kelasSelect.prop('disabled', true);
				}
			}
		});
	}

	function loadChartData() {
		const formData = {
			mata_kuliah_id: $('#mataKuliahSelect').val(),
			tahun_akademik: $('#tahunAkademikSelect').val(),
			kelas: $('#kelasSelect').val()
		};

		if (!formData.mata_kuliah_id) {
			alert('Silakan pilih mata kuliah terlebih dahulu');
			return;
		}

		$('#emptyState').addClass('d-none');
		$('#chartSection').removeClass('d-none').html(getLoadingHTML());

		$.ajax({
			url: '<?= base_url("admin/capaian-cpmk/chart-data") ?>',
			method: 'GET',
			data: formData,
			success: function(response) {
				if (response.success) {
					currentChartData = response;
					displayChart(response);
					displayInfo(response);
					displayDetailTable(response.chartData.details);
				} else {
					showError('emptyState', 'chartSection', response.message);
				}
			},
			error: function() {
				showError('emptyState', 'chartSection', 'Terjadi kesalahan saat memuat data');
			}
		});
	}

	function loadMultiSubjectChartData() {
		const selectedOptions = $('#mataKuliahMultiSelect').val();

		if (!selectedOptions || selectedOptions.length === 0) {
			alert('Silakan pilih minimal satu mata kuliah');
			return;
		}

		if (selectedOptions.length > 5) {
			alert('Maksimal 5 mata kuliah yang dapat dibandingkan');
			return;
		}

		const formData = {
			mata_kuliah_ids: selectedOptions.join(','),
			tahun_akademik: $('#tahunAkademikMultiSelect').val(),
			kelas: $('#kelasMultiSelect').val()
		};

		$('#emptyStateMulti').addClass('d-none');
		$('#chartSectionMulti').removeClass('d-none').html(getLoadingHTML('info'));

		$.ajax({
			url: '<?= base_url("admin/capaian-cpmk/comparative-subjects") ?>',
			method: 'GET',
			data: formData,
			success: function(response) {
				if (response.success) {
					displayMultiSubjectChart(response);
				} else {
					showError('emptyStateMulti', 'chartSectionMulti', response.message);
				}
			},
			error: function() {
				showError('emptyStateMulti', 'chartSectionMulti', 'Terjadi kesalahan saat memuat data');
			}
		});
	}

	function loadAllSubjectsChartData() {
		const tahunAkademik = $('#tahunAkademikAllSelect').val();

		$('#emptyStateAll').addClass('d-none');
		$('#chartSectionAll').removeClass('d-none').html(getLoadingHTML('secondary'));

		$.ajax({
			url: '<?= base_url("admin/capaian-cpmk/all-subjects-data") ?>',
			method: 'GET',
			data: {
				tahun_akademik: tahunAkademik
			},
			success: function(response) {
				if (response.success) {
					displayAllSubjectsChart(response);
					displayAllSubjectsSummaryTable(response.summaryData);
				} else {
					showError('emptyStateAll', 'chartSectionAll', response.message);
				}
			},
			error: function() {
				showError('emptyStateAll', 'chartSectionAll', 'Terjadi kesalahan saat memuat data');
			}
		});
	}

	function displayInfo(response) {
		const info = `
        <div class="row">
            <div class="col-md-6">
                <strong>Mata Kuliah:</strong> ${response.mataKuliah.kode_mk} - ${response.mataKuliah.nama_mk}
            </div>
            <div class="col-md-3">
                <strong>Tahun Akademik:</strong> ${response.jadwal.tahun_akademik}
            </div>
            <div class="col-md-3">
                <strong>Kelas:</strong> ${response.jadwal.kelas}
            </div>
        </div>
    `;
		$('#infoContent').html(info);
		$('#infoSection').removeClass('d-none');
	}

	function displayChart(response) {
		$('#chartSection').html(`
        <div class="card">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Grafik Rata-rata Capaian CPMK</h5>
                <button class="btn btn-light btn-sm" id="exportChartBtn">
                    <i class="bi bi-download"></i> Export PNG
                </button>
            </div>
            <div class="card-body">
                <canvas id="cpmkChart" height="80"></canvas>
            </div>
        </div>
        <div class="card mt-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-table"></i> Detail Capaian CPMK</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="detailTable">
                        <thead class="table-light">
                            <tr>
                                <th>No</th>
                                <th>Kode CPMK</th>
                                <th>Deskripsi CPMK</th>
                                <th class="text-center">Jumlah Mahasiswa</th>
                                <th class="text-center">Rata-rata Nilai</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="detailTableBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `);

		$('#exportChartBtn').on('click', function() {
			exportChart(cpmkChart, 'capaian-cpmk.png');
		});

		const ctx = document.getElementById('cpmkChart').getContext('2d');
		if (cpmkChart) cpmkChart.destroy();

		const gradient = ctx.createLinearGradient(0, 0, 0, 400);
		gradient.addColorStop(0, 'rgba(54, 162, 235, 0.8)');
		gradient.addColorStop(1, 'rgba(54, 162, 235, 0.2)');

		cpmkChart = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: response.chartData.labels,
				datasets: [{
					label: 'Rata-rata Nilai CPMK',
					data: response.chartData.data,
					backgroundColor: gradient,
					borderColor: 'rgba(54, 162, 235, 1)',
					borderWidth: 2,
					borderRadius: 5,
					barThickness: 50
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
						text: 'Rata-rata Capaian CPMK',
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
								return 'Rata-rata: ' + context.parsed.y.toFixed(2);
							}
						}
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						max: 100,
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
						grid: {
							display: false
						}
					}
				}
			}
		});
	}

	function displayMultiSubjectChart(response) {
		const chartHTML = `
        <div class="card">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Perbandingan Capaian CPMK Antar Mata Kuliah</h5>
                <button class="btn btn-light btn-sm" id="exportChartMultiBtn">
                    <i class="bi bi-download"></i> Export PNG
                </button>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle"></i> <strong>Info:</strong> Membandingkan ${response.chartData.datasets.length} mata kuliah
                </div>
                <canvas id="cpmkChartMulti" height="100"></canvas>
            </div>
        </div>
    `;
		$('#chartSectionMulti').html(chartHTML);

		$('#exportChartMultiBtn').on('click', function() {
			exportChart(cpmkChartMulti, 'capaian-cpmk-multi.png');
		});

		const ctx = document.getElementById('cpmkChartMulti').getContext('2d');
		if (cpmkChartMulti) cpmkChartMulti.destroy();

		cpmkChartMulti = new Chart(ctx, {
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
						position: 'top'
					},
					title: {
						display: true,
						text: 'Perbandingan Capaian CPMK Antar Mata Kuliah',
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
								return context.dataset.label + ': ' + context.parsed.y.toFixed(2);
							}
						}
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						max: 100,
						grid: {
							display: true,
							color: 'rgba(0, 0, 0, 0.05)'
						}
					},
					x: {
						grid: {
							display: false
						}
					}
				}
			}
		});
	}

	function displayAllSubjectsChart(response) {
		const chartHTML = `
        <div class="card">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Capaian CPMK di Semua Mata Kuliah</h5>
                <button class="btn btn-light btn-sm" id="exportChartAllBtn">
                    <i class="bi bi-download"></i> Export PNG
                </button>
            </div>
            <div class="card-body">
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle"></i> <strong>Info:</strong> Menampilkan ${response.chartData.datasets.length} mata kuliah yang memiliki data CPMK
                </div>
                <canvas id="cpmkChartAll" height="120"></canvas>
            </div>
        </div>
    `;
		$('#chartSectionAll').html(chartHTML);

		$('#exportChartAllBtn').on('click', function() {
			exportChart(cpmkChartAll, 'capaian-cpmk-all.png');
		});

		const ctx = document.getElementById('cpmkChartAll').getContext('2d');
		if (cpmkChartAll) cpmkChartAll.destroy();

		cpmkChartAll = new Chart(ctx, {
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
						text: 'Capaian CPMK di Semua Mata Kuliah',
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
								return context.dataset.label + ': ' + context.parsed.y.toFixed(2);
							}
						}
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						max: 100,
						grid: {
							display: true,
							color: 'rgba(0, 0, 0, 0.05)'
						}
					},
					x: {
						grid: {
							display: false
						}
					}
				}
			}
		});
	}

	function displayDetailTable(details) {
		let html = '';
		details.forEach((item, index) => {
			const statusClass = item.rata_rata >= 75 ? 'success' : (item.rata_rata >= 60 ? 'warning' : 'danger');
			const statusText = item.rata_rata >= 75 ? 'Baik' : (item.rata_rata >= 60 ? 'Cukup' : 'Kurang');

			html += `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${item.kode_cpmk}</strong></td>
                <td>${item.deskripsi}</td>
                <td class="text-center">${item.jumlah_mahasiswa}</td>
                <td class="text-center"><strong>${item.rata_rata.toFixed(2)}</strong></td>
                <td class="text-center"><span class="badge bg-${statusClass}">${statusText}</span></td>
                <td class="text-center">
                    <button class="btn btn-sm btn-info" onclick="showDetail(${item.cpmk_id}, '${item.kode_cpmk}')">
                        <i class="bi bi-eye"></i> Lihat Detail
                    </button>
                </td>
            </tr>
        `;
		});
		$('#detailTableBody').html(html);
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
                                <th width="30%">Nama Mata Kuliah</th>
                                <th width="10%" class="text-center">Kelas</th>
                                <th width="12%" class="text-center">Tahun Akademik</th>
                                <th width="10%" class="text-center">Jumlah CPMK</th>
                                <th width="10%" class="text-center">Jumlah Mahasiswa</th>
                                <th width="10%" class="text-center">Rata-rata</th>
                                <th width="8%" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
    `;

		summaryData.forEach((item, index) => {
			const statusClass = item.rata_rata >= 75 ? 'success' : (item.rata_rata >= 60 ? 'warning' : 'danger');
			const statusText = item.rata_rata >= 75 ? 'Baik' : (item.rata_rata >= 60 ? 'Cukup' : 'Kurang');

			html += `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${item.kode_mk}</strong></td>
                <td>${item.nama_mk}</td>
                <td class="text-center">${item.kelas}</td>
                <td class="text-center"><span class="badge bg-primary">${item.tahun_akademik}</span></td>
                <td class="text-center">${item.jumlah_cpmk}</td>
                <td class="text-center">${item.jumlah_mahasiswa}</td>
                <td class="text-center"><strong>${item.rata_rata.toFixed(2)}</strong></td>
                <td class="text-center"><span class="badge bg-${statusClass}">${statusText}</span></td>
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

		$('#chartSectionAll').append(html);
	}

	function showDetail(cpmkId, kodeCpmk) {
		$('#detailModalTitle').text(`Detail Nilai ${kodeCpmk}`);
		$('#detailModal').modal('show');

		$('#detailModalContent').html(getLoadingHTML());

		$.ajax({
			url: '<?= base_url("admin/capaian-cpmk/detail-data") ?>',
			method: 'GET',
			data: {
				mata_kuliah_id: $('#mataKuliahSelect').val(),
				tahun_akademik: $('#tahunAkademikSelect').val(),
				kelas: $('#kelasSelect').val(),
				cpmk_id: cpmkId
			},
			success: function(response) {
				if (response.success) {
					displayDetailModal(response.data, response.cpmk);
				} else {
					$('#detailModalContent').html(`<div class="alert alert-danger">${response.message}</div>`);
				}
			},
			error: function() {
				$('#detailModalContent').html(`<div class="alert alert-danger">Terjadi kesalahan saat memuat data</div>`);
			}
		});
	}

	function displayDetailModal(data, cpmk) {
		let html = `
        <div class="mb-3">
            <h6><strong>CPMK:</strong> ${cpmk.kode_cpmk}</h6>
            <p class="text-muted">${cpmk.deskripsi}</p>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-sm">
                <thead class="table-primary">
                    <tr>
                        <th width="10%">No</th>
                        <th width="20%">NIM</th>
                        <th width="50%">Nama Mahasiswa</th>
                        <th width="20%" class="text-center">Nilai</th>
                    </tr>
                </thead>
                <tbody>
    `;

		if (data.length === 0) {
			html += `
            <tr>
                <td colspan="4" class="text-center text-muted">Belum ada data nilai mahasiswa</td>
            </tr>
        `;
		} else {
			data.forEach((item, index) => {
				const badgeClass = item.nilai_cpmk >= 75 ? 'success' : (item.nilai_cpmk >= 60 ? 'warning' : 'danger');
				html += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${item.nim}</td>
                    <td>${item.nama_lengkap}</td>
                    <td class="text-center">
                        <span class="badge bg-${badgeClass}">${parseFloat(item.nilai_cpmk).toFixed(2)}</span>
                    </td>
                </tr>
            `;
			});
		}

		html += `
                </tbody>
            </table>
        </div>
    `;

		$('#detailModalContent').html(html);
	}

	// Helper Functions
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
		$(`#${emptyStateId}`).removeClass('d-none').html(`
        <i class="bi bi-exclamation-circle" style="font-size: 4rem; color: #dc3545;"></i>
        <p class="text-danger mt-3">${message}</p>
    `);
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
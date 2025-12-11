<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<style>
	.filter-card {
		border: 2px solid #e0e0e0;
		background: #ffffff;
	}

	.filter-card:hover {
		transform: translateY(-5px);
		border-color: #0d6efd !important;
	}

	.filter-card.border-primary {
		background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
	}

	.filter-card i {
		transition: all 0.3s ease;
	}

	.filter-card:hover i {
		transform: scale(1.1);
	}

	.filter-card .card-title {
		transition: all 0.3s ease;
		font-size: 1.1rem;
	}

	.filter-card .card-text {
		line-height: 1.4;
		min-height: 40px;
	}
</style>

<div class="mb-4">
	<h2 class="mb-1">Laporan CPMK</h2>
	<p class="text-muted">Laporan Capaian Pembelajaran Mata Kuliah</p>
</div>

<!-- Sub-tabs for different filter types -->
<div class="row g-3 mb-4">
	<div class="col-md-6">
		<button type="button" class="btn p-0 w-100 text-start border-0" data-bs-toggle="tab" data-bs-target="#semester-filter" id="semester-card-btn">
			<div class="card filter-card h-100 border-primary shadow-sm" id="semester-card" style="cursor: pointer; transition: all 0.3s;">
				<div class="card-body text-center p-3">
					<div class="mb-2">
						<i class="bi bi-calendar-week text-primary" style="font-size: 1.8rem;"></i>
					</div>
					<h6 class="card-title mb-2 text-primary fw-bold">CPMK Per Semester</h6>
					<p class="card-text text-muted small mb-0">Pilih semester tertentu untuk melihat laporan CPMK mahasiswa pada semester tersebut.</p>
				</div>
			</div>
		</button>
	</div>
	<div class="col-md-6">
		<button type="button" class="btn p-0 w-100 text-start border-0" data-bs-toggle="tab" data-bs-target="#tahun-filter" id="tahun-card-btn">
			<div class="card filter-card h-100 shadow-sm" id="tahun-card" style="cursor: pointer; transition: all 0.3s;">
				<div class="card-body text-center p-3">
					<div class="mb-2">
						<i class="bi bi-calendar-range text-secondary" style="font-size: 1.8rem;"></i>
					</div>
					<h6 class="card-title mb-2">CPMK Per Tahun Akademik</h6>
					<p class="card-text text-muted small mb-0">Pilih tahun akademik untuk melihat laporan CPMK mahasiswa dalam 1 tahun akademik.</p>
				</div>
			</div>
		</button>
	</div>
</div>

<!-- Tab Content -->
<div class="tab-content" id="filterTabsContent">
	<!-- Semester Filter Tab -->
	<div class="tab-pane fade show active" id="semester-filter" role="tabpanel">
		<div class="card mb-4">
			<div class="card-body">
				<form id="filterFormSemester">
					<div class="row g-3">
						<div class="col-md-10">
							<label for="semester" class="form-label">Semester <span class="text-danger">*</span></label>
							<select class="form-select" id="semester" name="semester" required>
								<option value="">-- Pilih Semester --</option>
								<?php foreach ($semesterList as $sem): ?>
									<option value="<?= esc($sem) ?>"><?= esc($sem) ?></option>
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
	</div>

	<!-- Tahun Akademik Filter Tab -->
	<div class="tab-pane fade" id="tahun-filter" role="tabpanel">
		<div class="card mb-4">
			<div class="card-body">
				<form id="filterFormTahun">
					<div class="row g-3">
						<div class="col-md-10">
							<label for="tahun_akademik" class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
							<select class="form-select" id="tahun_akademik" name="tahun_akademik" required>
								<option value="">-- Pilih Tahun Akademik --</option>
								<?php foreach ($tahunAkademikList as $tahun): ?>
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
	</div>
</div>

<!-- Loading Spinner -->
<div id="loadingSpinner" class="text-center py-5" style="display: none;">
	<div class="spinner-border text-primary" role="status">
		<span class="visually-hidden">Loading...</span>
	</div>
	<p class="mt-3 text-muted">Memuat data...</p>
</div>

<!-- Empty State -->
<div id="emptyState" class="card" style="display: none;">
	<div class="card-body text-center py-5 text-muted">
		<i class="bi bi-inbox" style="font-size: 3rem;"></i>
		<p class="mb-1 mt-3">Belum ada data yang ditampilkan</p>
		<small>Pilih filter berdasarkan semester atau tahun akademik untuk melihat laporan CPMK</small>
	</div>
</div>

<!-- Data Table -->
<div id="dataTable" style="display: none;">
	<div class="card">
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-bordered table-hover">
					<thead class="table-light">
						<tr>
							<th width="5%" class="text-center">No</th>
							<th width="15%">Kode MK</th>
							<th width="40%">Mata Kuliah</th>
							<th width="20%" class="text-center">Rata-rata CPMK (%)</th>
							<th width="20%" class="text-center">Detail</th>
						</tr>
					</thead>
					<tbody id="tableBody">
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- Modal Detail CPMK -->
<div class="modal fade" id="detailModal" tabindex="-1">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title" id="detailModalTitle">Detail Perhitungan CPMK</h5>
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
<script>
	$(document).ready(function() {
		// Show empty state initially
		$('#emptyState').show();

		// Handle filter card clicks - update styling and switch tabs
		$('#semester-card-btn, #semester-card').on('click', function(e) {
			e.preventDefault();

			// Remove active styling from all cards
			$('.filter-card').removeClass('border-primary');
			$('.filter-card .card-title').removeClass('text-primary fw-bold');
			$('.filter-card i').removeClass('text-primary').addClass('text-secondary');

			// Add active styling to semester card
			$('#semester-card').addClass('border-primary');
			$('#semester-card').find('.card-title').addClass('text-primary fw-bold');
			$('#semester-card').find('i').removeClass('text-secondary').addClass('text-primary');

			// Switch to semester tab
			$('.tab-pane').removeClass('show active');
			$('#semester-filter').addClass('show active');
		});

		$('#tahun-card-btn, #tahun-card').on('click', function(e) {
			e.preventDefault();

			// Remove active styling from all cards
			$('.filter-card').removeClass('border-primary');
			$('.filter-card .card-title').removeClass('text-primary fw-bold');
			$('.filter-card i').removeClass('text-primary').addClass('text-secondary');

			// Add active styling to tahun card
			$('#tahun-card').addClass('border-primary');
			$('#tahun-card').find('.card-title').addClass('text-primary fw-bold');
			$('#tahun-card').find('i').removeClass('text-secondary').addClass('text-primary');

			// Switch to tahun akademik tab
			$('.tab-pane').removeClass('show active');
			$('#tahun-filter').addClass('show active');
		});

		// Handle semester filter form submission
		$('#filterFormSemester').on('submit', function(e) {
			e.preventDefault();

			const semester = $('#semester').val();

			if (!semester) {
				alert('Pilih semester terlebih dahulu');
				return;
			}

			loadData(semester, '');
		});

		// Handle tahun akademik filter form submission
		$('#filterFormTahun').on('submit', function(e) {
			e.preventDefault();

			const tahunAkademik = $('#tahun_akademik').val();

			if (!tahunAkademik) {
				alert('Pilih tahun akademik terlebih dahulu');
				return;
			}

			loadData('', tahunAkademik);
		});

		function loadData(semester, tahunAkademik) {
			// Show loading, hide others
			$('#loadingSpinner').show();
			$('#emptyState').hide();
			$('#dataTable').hide();

			$.ajax({
				url: '<?= base_url('mahasiswa/get-laporan-cpmk-data') ?>',
				type: 'GET',
				data: {
					semester: semester,
					tahun_akademik: tahunAkademik
				},
				dataType: 'json',
				success: function(response) {
					$('#loadingSpinner').hide();

					if (response.success && response.data.length > 0) {
						renderTable(response.data);
						$('#dataTable').show();
					} else {
						alert(response.message || 'Tidak ada data untuk filter yang dipilih');
						$('#emptyState').show();
					}
				},
				error: function(xhr) {
					$('#loadingSpinner').hide();
					$('#emptyState').show();
					alert('Terjadi kesalahan saat memuat data');
					console.error(xhr);
				}
			});
		}

		function renderTable(data) {
			const tbody = $('#tableBody');
			tbody.empty();

			data.forEach((item, index) => {
				const row = `
					<tr>
						<td class="text-center">${index + 1}</td>
						<td><strong>${escapeHtml(item.kode_mk)}</strong></td>
						<td>${escapeHtml(item.nama_mk)}</td>
						<td class="text-center">
							<span class="badge bg-primary" style="font-size: 1rem;">
								${item.avg_cpmk}%
							</span>
						</td>
						<td class="text-center">
							<button class="btn btn-sm btn-info" onclick="showDetail('${escapeHtml(item.kode_mk)}')">
								<i class="bi bi-eye"></i> Lihat Detail
							</button>
						</td>
					</tr>
				`;
				tbody.append(row);
			});
		}

		window.showDetail = function(kodeMk) {
			// Get filter values based on active tab
			let semester = '';
			let tahunAkademik = '';

			if ($('#semester-filter').hasClass('show active')) {
				semester = $('#semester').val();
			} else if ($('#tahun-filter').hasClass('show active')) {
				tahunAkademik = $('#tahun_akademik').val();
			}

			$('#detailModal').modal('show');
			$('#detailModalContent').html(`
				<div class="text-center py-4">
					<div class="spinner-border text-primary" role="status">
						<span class="visually-hidden">Loading...</span>
					</div>
				</div>
			`);

			$.ajax({
				url: '<?= base_url('mahasiswa/get-cpmk-detail-calculation') ?>',
				type: 'GET',
				data: {
					kode_mk: kodeMk,
					semester: semester,
					tahun_akademik: tahunAkademik
				},
				dataType: 'json',
				success: function(response) {
					if (response.success) {
						renderDetailModal(response.data, response.summary);
					} else {
						$('#detailModalContent').html(`
							<div class="alert alert-warning">
								${response.message || 'Tidak ada data'}
							</div>
						`);
					}
				},
				error: function(xhr) {
					$('#detailModalContent').html(`
						<div class="alert alert-danger">
							Terjadi kesalahan saat memuat detail
						</div>
					`);
					console.error(xhr);
				}
			});
		};

		function renderDetailModal(data, summary) {
			let html = `
				<div class="mb-3">
					<h6><strong>Mata Kuliah:</strong> ${escapeHtml(summary.kode_mk)} - ${escapeHtml(summary.nama_mk)}</h6>
				</div>
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead class="table-light">
							<tr>
								<th width="5%" class="text-center">No</th>
								<th width="20%">Kode CPMK</th>
								<th width="55%">Deskripsi</th>
								<th width="20%" class="text-center">Capaian (%)</th>
							</tr>
						</thead>
						<tbody>
			`;

			data.forEach((item, index) => {
				html += `
					<tr>
						<td class="text-center">${index + 1}</td>
						<td><strong>${escapeHtml(item.kode_cpmk)}</strong></td>
						<td>${escapeHtml(item.deskripsi)}</td>
						<td class="text-center">${item.capaian}%</td>
					</tr>
				`;
			});

			html += `
						</tbody>
						<tfoot class="table-secondary">
							<tr>
								<td colspan="3" class="text-end"><strong>Rata-rata CPMK:</strong></td>
								<td class="text-center"><strong>${summary.avg_cpmk}%</strong></td>
							</tr>
						</tfoot>
					</table>
				</div>
			`;

			$('#detailModalContent').html(html);
		}

		function escapeHtml(text) {
			const map = {
				'&': '&amp;',
				'<': '&lt;',
				'>': '&gt;',
				'"': '&quot;',
				"'": '&#039;'
			};
			return text.replace(/[&<>"']/g, m => map[m]);
		}
	});
</script>
<?= $this->endSection() ?>
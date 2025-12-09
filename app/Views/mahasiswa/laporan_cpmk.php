<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<div class="mb-4">
	<h2 class="mb-1">Laporan CPMK</h2>
	<p class="text-muted">Laporan Capaian Pembelajaran Mata Kuliah</p>
</div>

<!-- Filter Section -->
<div class="card mb-4">
	<div class="card-header bg-primary text-white">
		<h5 class="mb-0"><i class="bi bi-funnel"></i> Filter Data</h5>
	</div>
	<div class="card-body">
		<form id="filterForm">
			<div class="row g-3">
				<div class="col-md-5">
					<label for="semester" class="form-label">Semester</label>
					<select class="form-select" id="semester" name="semester">
						<option value="">-- Pilih Semester --</option>
						<?php foreach ($semesterList as $sem): ?>
							<option value="<?= esc($sem) ?>"><?= esc($sem) ?></option>
						<?php endforeach; ?>
					</select>
					<small class="text-muted">Pilih semester tertentu untuk melihat data</small>
				</div>
				<div class="col-md-5">
					<label for="tahun_akademik" class="form-label">Tahun Akademik</label>
					<select class="form-select" id="tahun_akademik" name="tahun_akademik">
						<option value="">-- Pilih Tahun Akademik --</option>
						<?php foreach ($tahunAkademikList as $tahun): ?>
							<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
						<?php endforeach; ?>
					</select>
					<small class="text-muted">Atau pilih tahun akademik saja</small>
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
		<small>Pilih filter semester dan/atau tahun akademik untuk melihat laporan CPMK</small>
	</div>
</div>

<!-- Data Table -->
<div id="dataTable" style="display: none;">
	<div class="card">
		<div class="card-header bg-secondary text-white">
			<h5 class="mb-0"><i class="bi bi-table"></i> Laporan Rata-rata CPMK per Mata Kuliah</h5>
		</div>
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

		// Handle filter form submission
		$('#filterForm').on('submit', function(e) {
			e.preventDefault();

			const semester = $('#semester').val();
			const tahunAkademik = $('#tahun_akademik').val();

			if (!semester && !tahunAkademik) {
				alert('Pilih minimal satu filter (Semester atau Tahun Akademik)');
				return;
			}

			loadData(semester, tahunAkademik);
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
			const semester = $('#semester').val();
			const tahunAkademik = $('#tahun_akademik').val();

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

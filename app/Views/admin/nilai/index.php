<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<style>
.course-name {
	font-size: 0.95rem;
	line-height: 1.3;
	color: #2c3e50;
}
.course-info {
	font-size: 0.8rem;
	color: #6c757d;
}
.progress-cell {
	min-width: 140px;
}
.progress-info {
	font-size: 0.75rem;
}
.action-buttons .btn {
	padding: 0.25rem 0.5rem;
}
.table-hover tbody tr:hover {
	background-color: #f8f9fa;
	cursor: pointer;
}
.badge-status {
	font-size: 0.75rem;
	padding: 0.35rem 0.6rem;
}
#jadwalTable_wrapper .dataTables_filter input {
	border-radius: 0.25rem;
	border: 1px solid #ced4da;
	padding: 0.375rem 0.75rem;
}
#jadwalTable_wrapper .dataTables_length select {
	border-radius: 0.25rem;
	border: 1px solid #ced4da;
	padding: 0.375rem 0.75rem;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
	background: #0d6efd !important;
	color: white !important;
	border: 1px solid #0d6efd !important;
	border-radius: 0.25rem;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
	background: #e9ecef !important;
	color: #0d6efd !important;
	border: 1px solid #dee2e6 !important;
}
</style>

<div class="container-fluid px-4">
	<h2 class="fw-bold my-4 text-center">Penilaian Jadwal Mengajar</h2>

	<?php if (session()->getFlashdata('success')): ?>
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			<?= esc(session()->getFlashdata('success')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>
	<?php if (session()->getFlashdata('error')): ?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<?= esc(session()->getFlashdata('error')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>

	<div class="card shadow-sm mb-4">
		<div class="card-header bg-light p-3">
			<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
				<div class="d-flex align-items-center gap-2">
					<i class="bi bi-funnel-fill fs-5 text-primary"></i>
					<h5 class="mb-0">Filter Jadwal</h5>
				</div>
			</div>
		</div>
		<div class="card-body">
			<form method="GET" action="<?= current_url() ?>">
				<div class="row g-3 align-items-end">
					<div class="col-md-4">
						<label for="filter_program_studi" class="form-label">Program Studi</label>
						<select class="form-select" id="filter_program_studi" name="program_studi">
							<option value="">Semua Program Studi</option>
							<option value="Teknik Informatika" <?= ($filters['program_studi'] ?? 'Teknik Informatika') == 'Teknik Informatika' ? 'selected' : '' ?>>Teknik Informatika</option>
							<option value="Sistem Informasi" <?= ($filters['program_studi'] ?? 'Teknik Informatika') == 'Sistem Informasi' ? 'selected' : '' ?>>Sistem Informasi</option>
							<option value="Teknik Komputer" <?= ($filters['program_studi'] ?? 'Teknik Informatika') == 'Teknik Komputer' ? 'selected' : '' ?>>Teknik Komputer</option>
						</select>
					</div>
					<div class="col-md-4">
						<label for="filter_tahun_akademik" class="form-label">Tahun Akademik</label>
						<input type="text" class="form-control" name="tahun_akademik" value="<?= esc($filters['tahun_akademik'] ?? '') ?>" placeholder="e.g. 2025/2026 Ganjil">
					</div>
					<div class="col-md-4 d-flex gap-2">
						<button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Terapkan</button>
						<a href="<?= current_url() ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
					</div>
				</div>
			</form>
		</div>
	</div>

	<?php
	// Flatten the jadwal array from day-based grouping
	$all_schedules = [];
	foreach ($jadwal_by_day as $day => $schedules) {
		foreach ($schedules as $jadwal) {
			$jadwal['hari'] = $day;
			$all_schedules[] = $jadwal;
		}
	}
	?>

	<div class="card shadow-sm">
		<div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
			<div class="d-flex justify-content-between align-items-center">
				<h5 class="mb-0"><i class="bi bi-calendar3"></i> Daftar Jadwal Mengajar</h5>
				<span class="badge bg-white text-primary rounded-pill"><?= count($all_schedules) ?> Jadwal</span>
			</div>
		</div>
		<div class="card-body p-0">
			<?php if (empty($all_schedules)): ?>
				<div class="text-center text-muted py-5">
					<i class="bi bi-calendar-x fs-1"></i>
					<p class="mt-3 fw-semibold">Tidak ada jadwal ditemukan</p>
					<p class="small">Silakan sesuaikan filter untuk melihat jadwal</p>
				</div>
			<?php else: ?>
				<div class="table-responsive p-3">
					<table id="jadwalTable" class="table table-hover align-middle mb-0" style="width:100%">
						<thead class="table-light">
							<tr>
								<th class="text-center" style="width: 50px;">No</th>
								<th style="min-width: 250px;">Mata Kuliah</th>
								<th style="min-width: 180px;">Dosen</th>
								<th style="min-width: 150px;">Program Studi</th>
								<th style="width: 180px;">Progress Penilaian</th>
								<th style="width: 150px;">Status</th>
								<th class="text-center" style="width: 160px;">Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($all_schedules as $index => $jadwal): ?>
								<tr>
									<td class="text-center fw-semibold text-muted"><?= $index + 1 ?></td>
									<td>
										<div class="course-name fw-bold"><?= esc($jadwal['nama_mk']) ?></div>
										<div class="course-info">
											<?php if (isset($jadwal['kode_mk'])): ?>
												<i class="bi bi-code-square"></i> <?= esc($jadwal['kode_mk']) ?>
											<?php endif; ?>
											<?php if (isset($jadwal['sks'])): ?>
												<?= isset($jadwal['kode_mk']) ? ' • ' : '' ?><i class="bi bi-book"></i> <?= esc($jadwal['sks']) ?> SKS
											<?php endif; ?>
											<?php if (isset($jadwal['kelas'])): ?>
												<?= (isset($jadwal['kode_mk']) || isset($jadwal['sks'])) ? ' • ' : '' ?><i class="bi bi-people"></i> Kelas <?= esc($jadwal['kelas']) ?>
											<?php endif; ?>
										</div>
									</td>
									<td>
										<?php if (isset($jadwal['dosen_ketua']) && !empty($jadwal['dosen_ketua'])): ?>
											<div class="mb-1">
												<span class="badge bg-primary me-1" style="font-size: 0.7rem;">
													<i class="bi bi-star-fill"></i> Koordinator
												</span>
												<div class="mt-1">
													<small class="fw-semibold"><?= esc($jadwal['dosen_ketua']) ?></small>
												</div>
											</div>
											<?php if (isset($jadwal['dosen_anggota']) && !empty($jadwal['dosen_anggota'])): ?>
												<?php
												$anggota_list = is_array($jadwal['dosen_anggota']) ? $jadwal['dosen_anggota'] : explode(',', $jadwal['dosen_anggota']);
												foreach ($anggota_list as $anggota):
													$anggota = trim($anggota);
													if (!empty($anggota)):
												?>
													<div class="mt-1">
														<i class="bi bi-person text-muted" style="font-size: 0.8rem;"></i>
														<small class="text-muted"><?= esc($anggota) ?></small>
													</div>
												<?php
													endif;
												endforeach;
												?>
											<?php endif; ?>
										<?php else: ?>
											<small class="text-muted fst-italic">Belum ditentukan</small>
										<?php endif; ?>
									</td>
									<td><small class="text-muted"><?= esc($jadwal['program_studi']) ?></small></td>
									<td>
										<?php if (isset($jadwal['score_completion'])): ?>
											<?php
											$completed = $jadwal['score_completion']['completed'];
											$total = $jadwal['score_completion']['total'];
											$percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
											$progress_color = $percentage == 100 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger');
											?>
											<div class="progress-cell">
												<div class="d-flex justify-content-between align-items-center mb-1 progress-info">
													<span class="text-muted"><i class="bi bi-clipboard-data"></i> <?= $completed ?>/<?= $total ?></span>
													<span class="fw-bold text-<?= $progress_color ?>"><?= $percentage ?>%</span>
												</div>
												<div class="progress" style="height: 6px;">
													<div class="progress-bar bg-<?= $progress_color ?>" role="progressbar" style="width: <?= $percentage ?>%"></div>
												</div>
											</div>
										<?php else: ?>
											<small class="text-muted fst-italic">Tidak ada data</small>
										<?php endif; ?>
									</td>
									<td>
										<?php if (isset($jadwal['is_nilai_validated']) && $jadwal['is_nilai_validated'] == 1): ?>
											<span class="badge bg-success badge-status">
												<i class="bi bi-check-circle-fill"></i> Tervalidasi
											</span>
										<?php else: ?>
											<span class="badge bg-warning text-dark badge-status">
												<i class="bi bi-clock-history"></i> Belum Validasi
											</span>
										<?php endif; ?>
									</td>
									<td>
										<div class="d-flex gap-1 justify-content-center flex-nowrap action-buttons">
											<a href="<?= base_url('admin/nilai/lihat-nilai/' . $jadwal['id']) ?>"
											   class="btn btn-sm btn-outline-info"
											   data-bs-toggle="tooltip"
											   title="Lihat Nilai Teknik">
												<i class="bi bi-eye"></i>
											</a>

											<a href="<?= base_url('admin/nilai/lihat-cpmk/' . $jadwal['id']) ?>"
											   class="btn btn-sm btn-outline-success"
											   data-bs-toggle="tooltip"
											   title="Lihat Nilai CPMK">
												<i class="bi bi-graph-up"></i>
											</a>

											<?php if (isset($jadwal['can_input_grades']) && $jadwal['can_input_grades']): ?>
												<a href="<?= base_url('admin/nilai/input-nilai-teknik/' . $jadwal['id']) ?>"
												   class="btn btn-sm btn-primary"
												   data-bs-toggle="tooltip"
												   title="Input Nilai">
													<i class="bi bi-pencil-square"></i>
												</a>
											<?php else: ?>
												<button class="btn btn-sm btn-secondary"
														disabled
														data-bs-toggle="tooltip"
														title="Hanya dosen pengampu yang dapat menginput nilai">
													<i class="bi bi-lock"></i>
												</button>
											<?php endif; ?>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">

<script>
$(document).ready(function() {
	// Initialize DataTable
	$('#jadwalTable').DataTable({
		"language": {
			"lengthMenu": "Tampilkan _MENU_ data per halaman",
			"zeroRecords": "Data tidak ditemukan",
			"info": "Menampilkan halaman _PAGE_ dari _PAGES_",
			"infoEmpty": "Tidak ada data tersedia",
			"infoFiltered": "(difilter dari _MAX_ total data)",
			"search": "Cari:",
			"paginate": {
				"first": "Pertama",
				"last": "Terakhir",
				"next": "Selanjutnya",
				"previous": "Sebelumnya"
			}
		},
		"pageLength": 10,
		"order": [[1, 'asc']], // Sort by Mata Kuliah by default
		"columnDefs": [
			{ "orderable": false, "targets": [6] }, // Disable sorting on Action column
			{ "searchable": false, "targets": [0, 6] } // Disable search on No and Action columns
		],
		"responsive": true,
		"autoWidth": false,
		"dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip'
	});

	// Initialize Bootstrap tooltips
	var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
	var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
		return new bootstrap.Tooltip(tooltipTriggerEl);
	});
});
</script>

<?= $this->endSection() ?>
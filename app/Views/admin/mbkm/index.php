<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
	<h2 class="fw-bold my-4 text-center">Manajemen Kegiatan MBKM (Merdeka Belajar Kampus Merdeka)</h2>

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
					<h5 class="mb-0">Filter Kegiatan</h5>
				</div>
				<?php if (session()->get('role') === 'admin'): ?>
					<div class="d-flex gap-2">
						<a href="<?= base_url('admin/mbkm/create') ?>" class="btn btn-primary">
							<i class="bi bi-plus-circle"></i> Tambah Kegiatan
						</a>

						<a href="<?= base_url('admin/mbkm/generate-api') ?>"
						class="btn btn-success"
						onclick="return confirm('Generate data MBKM dari API sekarang?')">
							<i class="bi bi-cloud-download"></i> Generate API
						</a>
					</div>
				<?php endif; ?>
			</div>
			
		</div>
		<div class="card-body">
			<form method="GET" action="<?= current_url() ?>">
				<div class="row g-3 align-items-end">
					<div class="col-md-3">
						<label for="filter_program_studi" class="form-label">Program Studi</label>
						<select class="form-select" id="filter_program_studi" name="program_studi">
							<option value="">Semua Program Studi</option>
							<option value="Teknik Informatika" <?= ($filters['program_studi'] ?? '') == 'Teknik Informatika' ? 'selected' : '' ?>>Teknik Informatika</option>
							<option value="Sistem Informasi" <?= ($filters['program_studi'] ?? '') == 'Sistem Informasi' ? 'selected' : '' ?>>Sistem Informasi</option>
							<option value="Teknik Komputer" <?= ($filters['program_studi'] ?? '') == 'Teknik Komputer' ? 'selected' : '' ?>>Teknik Komputer</option>
						</select>
					</div>
					<div class="col-md-3">
						<label for="filter_status" class="form-label">Status</label>
						<select class="form-select" id="filter_status" name="status_kegiatan">
							<option value="">Semua Status</option>
							<option value="diajukan" <?= ($filters['status_kegiatan'] ?? '') == 'diajukan' ? 'selected' : '' ?>>Diajukan</option>
							<option value="disetujui" <?= ($filters['status_kegiatan'] ?? '') == 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
							<option value="ditolak" <?= ($filters['status_kegiatan'] ?? '') == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
							<option value="berlangsung" <?= ($filters['status_kegiatan'] ?? '') == 'berlangsung' ? 'selected' : '' ?>>Berlangsung</option>
							<option value="selesai" <?= ($filters['status_kegiatan'] ?? '') == 'selesai' ? 'selected' : '' ?>>Selesai</option>
						</select>
					</div>
					<div class="col-md-2">
						<label for="filter_tahun_akademik" class="form-label">Tahun Akademik</label>
						<input type="text" class="form-control" name="tahun_akademik" value="<?= esc($filters['tahun_akademik'] ?? '') ?>" placeholder="2025/2026">
					</div>
					<div class="col-md-2 d-flex gap-2">
						<button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Terapkan</button>
						<a href="<?= current_url() ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
					</div>
				</div>
			</form>
			<a href="<?= base_url('admin/mbkm/create') ?>" class="btn btn-primary">
				<i class="bi bi-plus-circle"></i> Tambah Kegiatan
			</a>
		</div>
	</div>

	<?php
	$tahunOptions = ['' => 'Semua Tahun'];
	foreach ($tahun_list as $t) {
		$tahunOptions[$t] = $t;
	}
	$semesterOptions = ['' => 'Semua Semester'];
	foreach ($semester_list as $s) {
		$semesterOptions[$s] = $s;
	}
	?>
	<?= view('components/modern_filter', [
		'title'   => 'Filter Kegiatan',
		'action'  => current_url(),
		'filters' => [
			[
				'type'    => 'readonly',
				'name'    => 'program_studi',
				'label'   => 'Program Studi',
				'icon'    => 'bi-mortarboard-fill',
				'col'     => 'col-md-4',
				'value'   => 'Teknik Informatika',
				'display' => 'Teknik Informatika',
			],
			[
				'type'     => 'select',
				'name'     => 'tahun',
				'label'    => 'Tahun Akademik',
				'icon'     => 'bi-calendar-event',
				'col'      => 'col-md-3',
				'options'  => $tahunOptions,
				'selected' => $filters['tahun'] ?? '',
			],
			[
				'type'     => 'select',
				'name'     => 'semester',
				'label'    => 'Semester',
				'icon'     => 'bi-layers',
				'col'      => 'col-md-3',
				'options'  => $semesterOptions,
				'selected' => $filters['semester'] ?? '',
			],
		],
		'buttonCol'  => 'col-md-2',
		'buttonText' => 'Terapkan',
		'showReset'  => true,
	]) ?>

	<?php
	$status_labels = [
		'diajukan' => ['label' => 'Diajukan', 'icon' => 'bi-hourglass-split', 'color' => 'warning'],
		'disetujui' => ['label' => 'Disetujui', 'icon' => 'bi-check-circle', 'color' => 'info'],
		'ditolak' => ['label' => 'Ditolak', 'icon' => 'bi-x-circle', 'color' => 'danger'],
		'berlangsung' => ['label' => 'Berlangsung', 'icon' => 'bi-play-circle', 'color' => 'primary'],
		'selesai' => ['label' => 'Selesai', 'icon' => 'bi-check-circle-fill', 'color' => 'success']
	];

	// Combine all kegiatan into one array with status info
	$all_kegiatan = [];
	foreach ($status_labels as $status_key => $status_info) {
		if (!empty($kegiatan_by_status[$status_key])) {
			foreach ($kegiatan_by_status[$status_key] as $kegiatan) {
				$kegiatan['status_info'] = $status_info;
				$kegiatan['status_key'] = $status_key;
				$all_kegiatan[] = $kegiatan;
			}
		}
	}
	?>

	<div class="modern-filter-wrapper mb-4">
		<div class="modern-filter-header">
			<div class="d-flex justify-content-between align-items-center">
				<div class="modern-filter-title">
					<i class="bi bi-list-check"></i> Daftar Kegiatan MBKM
				</div>
				<span class="badge bg-primary rounded-pill">
					Total: <?= count($all_kegiatan) ?> Kegiatan
				</span>
			</div>
		</div>

		<div class="modern-table-wrapper">
			<?php if (empty($all_kegiatan)): ?>
				<div class="text-center text-muted py-5">
					<i class="bi bi-inbox fs-1"></i>
					<p class="mt-2 small">Tidak ada kegiatan</p>
				</div>
			<?php else: ?>
				<table class="modern-table" id="mbkmTable">
					<thead>
						<tr>
							<th style="min-width: 120px;" class="text-center">NIM</th>
							<th style="min-width: 150px;" class="text-center">Nama Mahasiswa</th>
							<th style="min-width: 200px;" class="text-center">Fakultas/Prodi Asal</th>
							<th style="min-width: 120px;" class="text-center">Program</th>
							<th style="min-width: 150px;" class="text-center">Sub Program</th>
							<th style="min-width: 180px;" class="text-center">Tujuan</th>
							<th style="min-width: 100px;" class="text-center">Status</th>
							<th style="min-width: 250px;" class="text-center">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($all_kegiatan as $kegiatan): ?>
							<tr>
								<td>
									<?php
									$nim_list = $kegiatan['nim_list'] ?? '-';
									if ($nim_list !== '-') {
										$nim_array = explode(',', $nim_list);
										foreach ($nim_array as $index => $nim) {
											echo esc(trim($nim));
											if ($index < count($nim_array) - 1) {
												echo '<br>';
											}
										}
									} else {
										echo '-';
									}
									?>
								</td>
								<td>
									<?php
									$mahasiswa_list = $kegiatan['nama_mahasiswa_list'] ?? '-';
									if ($mahasiswa_list !== '-') {
										$mahasiswa_array = explode(',', $mahasiswa_list);
										foreach ($mahasiswa_array as $index => $mhs) {
											echo esc(trim($mhs));
											if ($index < count($mahasiswa_array) - 1) {
												echo '<br>';
											}
										}
									} else {
										echo '-';
									}
									?>
								</td>
								<td>
									<?php
									$fakultas = $kegiatan['fakultas'] ?? '';
									$prodi = $kegiatan['program_studi'] ?? '';
									if ($fakultas && $prodi) {
										echo esc($fakultas) . ' / ' . esc($prodi);
									} elseif ($prodi) {
										echo esc($prodi);
									} elseif ($fakultas) {
										echo esc($fakultas);
									} else {
										echo '-';
									}
									?>
								</td>
								<td><?= esc($kegiatan['program'] ?? '-') ?></td>
								<td><?= esc($kegiatan['sub_program'] ?? '-') ?></td>
								<td><?= esc($kegiatan['tujuan'] ?? '-') ?></td>
								<td class="text-center">
									<span class="badge bg-<?= $kegiatan['status_info']['color'] ?>">
										<i class="bi <?= $kegiatan['status_info']['icon'] ?>"></i>
										<?= esc($kegiatan['status_info']['label']) ?>
									</span>
								</td>
								<td>
									<div class="d-flex gap-2 justify-content-center flex-wrap">
										<?php if (session()->get('role') === 'admin'): ?>
											<?php if ($kegiatan['status_key'] == 'disetujui' || $kegiatan['status_key'] == 'berlangsung' || $kegiatan['status_key'] == 'selesai'): ?>
												<a href="<?= base_url('admin/mbkm/input-nilai/' . $kegiatan['id']) ?>" class="btn btn-sm btn-outline-success">
													<i class="bi bi-pencil-square"></i> Input Nilai
												</a>
											<?php endif; ?>

											<a href="<?= base_url('admin/mbkm/edit/' . $kegiatan['id']) ?>" class="btn btn-sm btn-outline-warning">
												<i class="bi bi-pencil"></i> Edit
											</a>

											<button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?= $kegiatan['id'] ?>)">
												<i class="bi bi-trash"></i> Hapus
											</button>
										<?php endif; ?>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>

		<!-- Pagination Container -->
		<div id="mbkmTable_pagination" class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3"></div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		function initPagination(tableId, rowsPerPage = 10) {
			const table = document.getElementById(tableId);
			if (!table) return;

			const tbody = table.querySelector('tbody');
			const rows = Array.from(tbody.querySelectorAll('tr'));
			const totalRows = rows.length;
			const totalPages = Math.ceil(totalRows / rowsPerPage);
			let currentPage = 1;

			const paginationId = `${tableId}_pagination`;
			let paginationContainer = document.getElementById(paginationId);

			if (!paginationContainer) {
				paginationContainer = document.createElement('div');
				paginationContainer.id = paginationId;
				paginationContainer.className = 'd-flex justify-content-between align-items-center mt-3 px-3 pb-3';
				table.parentElement.parentElement.appendChild(paginationContainer);
			}

			function showPage(page) {
				currentPage = page;
				const start = (page - 1) * rowsPerPage;
				const end = start + rowsPerPage;

				rows.forEach((row, index) => {
					row.style.display = (index >= start && index < end) ? '' : 'none';
				});

				renderPagination();

				const wrapper = table.closest('.modern-table-wrapper');
				if (wrapper) wrapper.scrollTop = 0;
			}

			function renderPagination() {
				if (totalRows <= rowsPerPage) {
					paginationContainer.innerHTML = '';
					return;
				}

				const startEntry = totalRows === 0 ? 0 : ((currentPage - 1) * rowsPerPage) + 1;
				const endEntry = Math.min(currentPage * rowsPerPage, totalRows);

				let html = `
					<div class="text-muted small">
						Menampilkan ${startEntry} sampai ${endEntry} dari ${totalRows} data
					</div>
					<nav>
						<ul class="pagination pagination-sm mb-0">
				`;

				html += `
					<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
						<a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
					</li>
				`;

				const maxVisiblePages = 5;
				let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
				let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

				if (endPage - startPage + 1 < maxVisiblePages) {
					startPage = Math.max(1, endPage - maxVisiblePages + 1);
				}

				if (startPage > 1) {
					html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
					if (startPage > 2) {
						html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
					}
				}

				for (let i = startPage; i <= endPage; i++) {
					html += `
						<li class="page-item ${i === currentPage ? 'active' : ''}">
							<a class="page-link" href="#" data-page="${i}">${i}</a>
						</li>
					`;
				}

				if (endPage < totalPages) {
					if (endPage < totalPages - 1) {
						html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
					}
					html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
				}

				html += `
					<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
						<a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
					</li>
				`;

				html += `
						</ul>
					</nav>
				`;

				paginationContainer.innerHTML = html;

				paginationContainer.querySelectorAll('a.page-link').forEach(link => {
					link.addEventListener('click', function(e) {
						e.preventDefault();
						const page = parseInt(this.getAttribute('data-page'));
						if (page >= 1 && page <= totalPages) {
							showPage(page);
						}
					});
				});
			}

			showPage(1);
		}

		initPagination('mbkmTable', 10);
	});

	function confirmDelete(id) {
		if (confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')) {
			window.location.href = `<?= base_url('admin/mbkm/delete/') ?>${id}`;
		}
	}
</script>
<?= $this->endSection() ?>
<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="d-flex justify-content-between align-items-center mb-3">
	<h2 class="mb-0 fw-bold">Daftar Mata Kuliah</h2>
	<?php if (session('role') === 'admin'): ?>
		<div class="d-flex gap-2">
			<div class="btn-group">
				<button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
					<i class="bi bi-download"></i> Download
				</button>
				<ul class="dropdown-menu dropdown-menu-end">
					<li>
						<a class="dropdown-item" href="<?= base_url('admin/mata-kuliah/exportPdf') ?>">
							<i class="bi bi-file-earmark-pdf text-danger"></i> PDF
						</a>
					</li>
					<li>
						<a class="dropdown-item" href="<?= base_url('admin/mata-kuliah/exportExcel') ?>">
							<i class="bi bi-file-earmark-excel text-success"></i> Excel
						</a>
					</li>
				</ul>
			</div>
			<form action="<?= base_url('admin/mata-kuliah/sync') ?>" method="post" class="d-inline" onsubmit="return confirm('Sinkronisasi data mata kuliah dari API? Data yang sudah ada akan diperbarui.');">
				<button type="submit" class="btn btn-warning">
					<i class="bi bi-arrow-repeat"></i> Sinkronisasi
				</button>
			</form>
			<!-- <a href="<?= base_url('admin/mata-kuliah/create') ?>" class="btn btn-primary">
				<i class="bi bi-plus-circle"></i> Tambah Mata Kuliah
			</a> -->
		</div>
	<?php endif; ?>
</div>

<?php if (session()->getFlashdata('success')): ?>
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<?= session()->getFlashdata('success') ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		<?= session()->getFlashdata('error') ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
<?php endif; ?>

<?= view('components/modern_filter', [
	'title' => 'Filter Mata Kuliah',
	'action' => current_url(),
	'filters' => [
		[
			'type' => 'select',
			'name' => 'semester',
			'label' => 'Semester',
			'icon' => 'bi-calendar3',
			'col' => 'col-md-3',
			'options' => [
				'' => 'Semua Semester',
				'0' => 'Semester 0',
				'1' => 'Semester 1',
				'2' => 'Semester 2',
				'3' => 'Semester 3',
				'4' => 'Semester 4',
				'5' => 'Semester 5',
				'6' => 'Semester 6',
				'7' => 'Semester 7',
				'8' => 'Semester 8',
			],
			'selected' => $filters['semester'] ?? ''
		],
		[
			'type' => 'select',
			'name' => 'tipe',
			'label' => 'Tipe',
			'icon' => 'bi-tag-fill',
			'col' => 'col-md-3',
			'options' => [
				'' => 'Semua Tipe',
				'Wajib' => 'Wajib',
				'Pilihan' => 'Pilihan',
			],
			'selected' => $filters['tipe'] ?? ''
		],
		[
			'type' => 'text',
			'name' => 'search',
			'label' => 'Cari',
			'icon' => 'bi-search',
			'col' => 'col-md-4',
			'placeholder' => 'Kode MK atau Nama MK...',
			'value' => $filters['search'] ?? ''
		]
	],
	'buttonCol' => 'col-md-2',
	'buttonText' => 'Terapkan',
	'showReset' => true
]) ?>

<div class="shadow-sm border-0">
	<div class="p-0">
		<?php if (empty($matakuliah)): ?>
			<div class="text-center text-muted py-5">
				<i class="bi bi-journal-x fs-1"></i>
				<p class="mt-3 fw-semibold">Belum ada data mata kuliah</p>
				<p class="small">Silakan tambah data atau sesuaikan filter</p>
			</div>
		<?php else: ?>
			<div class="modern-table-wrapper">
				<div class="scroll-indicator"></div>
				<table class="modern-table" id="mataKuliahTable">
					<thead>
						<tr>
							<th class="text-center align-middle" style="width: 50px;" rowspan="2">No</th>
							<th class="text-center align-middle" rowspan="2">Kode MK</th>
							<th class="text-center align-middle" style="min-width: 200px;" rowspan="2">Nama Mata Kuliah</th>
							<th class="text-center align-middle" rowspan="2">Tipe</th>
							<th class="text-center" colspan="9">Semester</th>
							<th class="text-center align-middle" style="min-width: 200px;" rowspan="2">Deskripsi Singkat</th>
							<th class="text-center align-middle" style="width: 100px;" rowspan="2">Aksi</th>
						</tr>
						<tr>
							<?php for ($i = 0; $i <= 8; $i++): ?>
								<th class="text-center" style="width: 40px;"><?= $i ?></th>
							<?php endfor ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($matakuliah as $index => $mk): ?>
							<tr>
								<td class="text-center fw-semibold text-muted"><?= $index + 1 ?></td>
								<td class="text-center text-nowrap"><?= esc($mk['kode_mk']) ?></td>
								<td style="white-space: normal;"><?= esc($mk['nama_mk']) ?></td>
								<td class="text-center text-nowrap"><?= esc($mk['tipe']) ?></td>
								<?php for ($i = 0; $i <= 8; $i++): ?>
									<td class="text-center"><?= ((int)$mk['semester'] === $i) ? esc($mk['semester']) : '' ?></td>
								<?php endfor ?>
								<td style="white-space: normal;"><?= esc($mk['deskripsi_singkat']) ?></td>
								<td class="text-center">
									<?php if (session('role') === 'admin'): ?>
										<div class="d-flex gap-1 justify-content-center">
											<a href="<?= base_url('admin/mata-kuliah/edit/' . $mk['id']) ?>"
												class="btn btn-outline-primary btn-sm"
												data-bs-toggle="tooltip"
												title="Edit">
												<i class="bi bi-pencil-square"></i>
											</a>
											<form action="<?= base_url('admin/mata-kuliah/delete/' . $mk['id']) ?>" method="post" onsubmit="return confirm('Yakin hapus mata kuliah ini?');">
												<button class="btn btn-outline-danger btn-sm" type="submit" title="Hapus">
													<i class="bi bi-trash"></i>
												</button>
											</form>
										</div>
									<?php else: ?>
										<span class="text-muted" style="font-size:14px;">-</span>
									<?php endif; ?>
								</td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div>

			<!-- Pagination Container -->
			<div id="mataKuliahTable_pagination" class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3"></div>

			<div class="card-footer bg-light border-0 py-3">
				<div class="d-flex align-items-center gap-3 justify-content-center">
					<small class="text-muted">
						<i class="bi bi-info-circle me-1"></i>
						Total: <?= count($matakuliah) ?> mata kuliah
					</small>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Scroll indicator
		const tableWrapper = document.querySelector('.modern-table-wrapper');
		if (tableWrapper) {
			function checkScroll() {
				const hasHorizontalScroll = tableWrapper.scrollWidth > tableWrapper.clientWidth;
				const isScrolledToEnd = tableWrapper.scrollLeft >= (tableWrapper.scrollWidth - tableWrapper.clientWidth - 10);

				if (hasHorizontalScroll && !isScrolledToEnd) {
					tableWrapper.classList.add('has-scroll');
				} else {
					tableWrapper.classList.remove('has-scroll');
				}
			}

			checkScroll();
			window.addEventListener('resize', checkScroll);
			tableWrapper.addEventListener('scroll', checkScroll);
		}

		// Pagination
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

				// Update row numbers for visible rows
				let visibleNum = start + 1;
				rows.forEach((row, index) => {
					if (index >= start && index < end) {
						const firstCell = row.querySelector('td:first-child');
						if (firstCell) firstCell.textContent = visibleNum++;
					}
				});

				renderPagination();

				// Scroll table wrapper back to top on page change
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

				// Previous button
				html += `
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
                </li>
            `;

				// Page numbers
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

				// Next button
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

				// Add event listeners
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

			// Initialize first page
			showPage(1);
		}

		// Init pagination for mata kuliah table
		initPagination('mataKuliahTable', 10);

		// Initialize Bootstrap tooltips
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
		tooltipTriggerList.map(function(el) {
			return new bootstrap.Tooltip(el);
		});
	});
</script>

<?= $this->endSection() ?>
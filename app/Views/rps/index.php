<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="d-flex justify-content-between align-items-center mb-3">
	<h2 class="mb-0 fw-bold">Daftar RPS</h2>
	<a href="<?= base_url('rps/create') ?>" class="btn btn-primary">
		<i class="bi bi-plus-lg"></i> Tambah RPS
	</a>
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
	'title' => 'Filter RPS',
	'action' => current_url(),
	'filters' => [
		[
			'type' => 'select',
			'name' => 'status',
			'label' => 'Status',
			'icon' => 'bi-check-circle',
			'col' => 'col-md-3',
			'options' => [
				'' => 'Semua Status',
				'draft' => 'Draft',
				'final' => 'Final',
			],
			'selected' => $filters['status'] ?? ''
		],
		[
			'type' => 'text',
			'name' => 'search',
			'label' => 'Cari',
			'icon' => 'bi-search',
			'col' => 'col-md-5',
			'placeholder' => 'Mata Kuliah, Dosen, atau Tahun Ajaran...',
			'value' => $filters['search'] ?? ''
		]
	],
	'buttonCol' => 'col-md-2',
	'buttonText' => 'Terapkan',
	'showReset' => true
]) ?>

<div class="shadow-sm border-0">
	<div class="p-0">
		<?php if (empty($rps)): ?>
			<div class="text-center text-muted py-5">
				<i class="bi bi-journal-x fs-1"></i>
				<p class="mt-3 fw-semibold">Belum ada data RPS</p>
				<p class="small">Silakan tambah data atau sesuaikan filter</p>
			</div>
		<?php else: ?>
			<div class="modern-table-wrapper">
				<div class="scroll-indicator"></div>
				<table class="modern-table" id="rpsTable">
					<thead>
						<tr>
							<th class="text-center" style="width: 50px;">No</th>
							<th class="text-center" style="min-width: 180px;">Mata Kuliah</th>
							<th class="text-center" style="min-width: 180px;">Dosen Pengampu</th>
							<th class="text-center" style="min-width: 140px;">Koordinator MK</th>
							<th class="text-center" style="width: 80px;">Semester</th>
							<th class="text-center" style="width: 120px;">Tahun Ajaran</th>
							<th class="text-center" style="width: 90px;">Status</th>
							<th class="text-center" style="width: 160px;">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($rps as $index => $r): ?>
							<tr>
								<td class="text-center fw-semibold text-muted"><?= $index + 1 ?></td>
								<td style="white-space: normal;"><?= esc($r['nama_mk']) ?></td>
								<td style="white-space: normal;">
									<?php if (!empty($r['pengampu_list'])): ?>
										<?= implode(', ', array_map('esc', $r['pengampu_list'])) ?>
									<?php else: ?>
										<span class="text-danger">Belum ada</span>
									<?php endif ?>
								</td>
								<td style="white-space: normal;"><?= esc($r['koordinator_nama']) ?: '<span class="text-danger">Belum ada</span>' ?></td>
								<td class="text-center"><?= esc($r['semester']) ?></td>
								<td class="text-center"><?= esc($r['tahun_ajaran']) ?></td>
								<td class="text-center">
									<?php if ($r['status'] === 'final'): ?>
										<span class="badge bg-success">Final</span>
									<?php else: ?>
										<span class="badge bg-warning text-dark">Draft</span>
									<?php endif; ?>
								</td>
								<td class="text-center">
									<div class="btn-group" role="group">
										<a href="<?= base_url('rps/edit/' . $r['id']) ?>" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit">
											<i class="bi bi-pencil-square"></i>
										</a>
										<?php if ($r['status'] != 'final'): ?>
											<a href="<?= base_url('rps/referensi/' . $r['id']) ?>" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Referensi">
												<i class="bi bi-book"></i>
											</a>
											<a href="<?= base_url('rps/mingguan/' . $r['id']) ?>" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Kelola Mingguan">
												<i class="bi bi-calendar-week"></i>
											</a>
										<?php endif; ?>
										<a href="<?= base_url('rps/preview/' . $r['id']) ?>" class="btn btn-sm btn-primary" data-bs-toggle="tooltip" title="Preview RPS" target="_blank">
											<i class="bi bi-eye"></i>
										</a>
										<?php if ($r['status'] != 'final'): ?>
											<form action="<?= base_url('rps/delete/' . $r['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Yakin hapus?');">
												<button class="btn btn-sm btn-danger" type="submit" data-bs-toggle="tooltip" title="Hapus" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
													<i class="bi bi-trash"></i>
												</button>
											</form>
										<?php endif; ?>
									</div>
								</td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div>

			<!-- Pagination Container -->
			<div id="rpsTable_pagination" class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3"></div>

			<div class="card-footer bg-light border-0 py-3">
				<div class="d-flex align-items-center gap-3 justify-content-center">
					<small class="text-muted">
						<i class="bi bi-info-circle me-1"></i>
						Total: <?= count($rps) ?> RPS
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

		// Init pagination for RPS table
		initPagination('rpsTable', 10);

		// Initialize Bootstrap tooltips
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
		tooltipTriggerList.map(function(el) {
			return new bootstrap.Tooltip(el);
		});
	});
</script>

<?= $this->endSection() ?>
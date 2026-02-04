<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="d-flex justify-content-between align-items-center mb-3">
	<h2 class="mb-0 fw-bold">Pemetaan CPL ke Mata Kuliah</h2>
	<?php if (session('role') === 'admin'): ?>
		<div class="d-flex gap-2">
			<div class="btn-group">
				<button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
					<i class="bi bi-download"></i> Download
				</button>
				<ul class="dropdown-menu dropdown-menu-end">
					<li>
						<a class="dropdown-item" href="<?= base_url('admin/cpl-mk/exportPdf') ?>">
							<i class="bi bi-file-earmark-pdf text-danger"></i> PDF
						</a>
					</li>
					<li>
						<a class="dropdown-item" href="<?= base_url('admin/cpl-mk/exportExcel') ?>">
							<i class="bi bi-file-earmark-excel text-success"></i> Excel
						</a>
					</li>
				</ul>
			</div>
			<a href="<?= base_url('admin/cpl-mk/create') ?>" class="btn btn-primary">
				<i class="bi bi-plus-circle"></i> Tambah Pemetaan
			</a>
		</div>
	<?php endif; ?>
</div>

<?php if (session()->getFlashdata('success')): ?>
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<?= session()->getFlashdata('success'); ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
<?php endif ?>
<?php if (session()->getFlashdata('error')): ?>
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		<?= session()->getFlashdata('error'); ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
<?php endif ?>

<?= view('components/modern_filter', [
	'title' => 'Filter Pemetaan CPL-MK',
	'action' => current_url(),
	'filters' => [
		[
			'type' => 'text',
			'name' => 'search',
			'label' => 'Cari',
			'icon' => 'bi-search',
			'col' => 'col-md-5',
			'placeholder' => 'Kode MK atau Nama Mata Kuliah...',
			'value' => $filters['search'] ?? ''
		]
	],
	'buttonCol' => 'col-md-2',
	'buttonText' => 'Terapkan',
	'showReset' => true
]) ?>

<div class="shadow-sm border-0">
	<div class="p-0">
		<?php if (empty($mataKuliah)): ?>
			<div class="text-center text-muted py-5">
				<i class="bi bi-grid-3x3 fs-1"></i>
				<p class="mt-3 fw-semibold">Belum ada data mata kuliah</p>
				<p class="small">Silakan tambah data atau sesuaikan filter</p>
			</div>
		<?php else: ?>
			<div class="modern-table-wrapper">
				<div class="scroll-indicator"></div>
				<table class="modern-table" id="cplMkTable">
					<thead>
						<tr>
							<th class="text-center" style="width: 50px;">No</th>
							<th class="text-center" style="width: 130px;">Kode MK</th>
							<th class="text-center" style="min-width: 200px;">Nama Mata Kuliah</th>
							<?php foreach ($cpl as $c): ?>
								<th class="text-center" style="width: 80px;"><?= esc($c['kode_cpl']) ?></th>
							<?php endforeach ?>
						</tr>
					</thead>
					<tbody>
						<?php $no = 1;
						foreach ($mataKuliah as $mk): ?>
							<tr>
								<td class="text-center fw-semibold text-muted"><?= $no++ ?></td>
								<td class="text-center text-nowrap"><?= esc($mk['kode_mk']) ?></td>
								<td style="white-space: normal;"><?= esc($mk['nama_mk']) ?></td>
								<?php foreach ($cpl as $c): ?>
									<td class="text-center">
										<?php if (isset($pemetaan[$mk['id']][$c['id']])): ?>
											<?php if (session('role') === 'admin'): ?>
												<form action="<?= base_url('admin/cpl-mk/delete/' . $mk['id'] . '/' . $c['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus pemetaan MK <?= esc($mk['kode_mk']) ?> ke CPL <?= esc($c['kode_cpl']) ?>?')">
													<button type="submit" class="btn btn-outline-success btn-sm p-0" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;" title="Hapus Pemetaan">
														<i class="bi bi-check2-square" style="font-size: 20px;"></i>
													</button>
												</form>
											<?php else: ?>
												<i class="bi bi-check2-square text-success" style="font-size: 1.5em; line-height: 1;"></i>
											<?php endif; ?>
										<?php endif; ?>
									</td>
								<?php endforeach ?>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div>

			<!-- Pagination Container -->
			<div id="cplMkTable_pagination" class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3"></div>

			<div class="card-footer bg-light border-0 py-3">
				<div class="d-flex align-items-center gap-3 justify-content-center">
					<small class="text-muted">
						<i class="bi bi-info-circle me-1"></i>
						Total: <?= count($mataKuliah) ?> mata kuliah
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

		// Init pagination for CPL-MK table
		initPagination('cplMkTable', 10);

		// Initialize Bootstrap tooltips
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
		tooltipTriggerList.map(function(el) {
			return new bootstrap.Tooltip(el);
		});
	});
</script>

<?= $this->endSection() ?>
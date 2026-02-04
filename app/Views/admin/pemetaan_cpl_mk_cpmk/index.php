<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="d-flex justify-content-between align-items-center mb-3">
	<h2 class="mb-0 fw-bold"><?= esc($title ?? 'Pemetaan CPL - CPMK - MK') ?></h2>
	<div class="d-flex gap-2">
		<div class="btn-group">
			<button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
				<i class="bi bi-download"></i> Download
			</button>
			<ul class="dropdown-menu dropdown-menu-end">
				<li>
					<a class="dropdown-item" href="<?= base_url('admin/pemetaan-cpl-mk-cpmk/exportExcel') ?>">
						<i class="bi bi-file-earmark-excel text-success"></i> Excel
					</a>
				</li>
				<li>
					<a class="dropdown-item" href="<?= base_url('admin/pemetaan-cpl-mk-cpmk/exportPdf') ?>">
						<i class="bi bi-file-earmark-pdf text-danger"></i> PDF
					</a>
				</li>
			</ul>
		</div>
		<a href="<?= base_url('admin/pemetaan-cpl-mk-cpmk/create') ?>" class="btn btn-primary">
			<i class="bi bi-plus-lg"></i> Tambah Pemetaan
		</a>
	</div>
</div>

<?php if (session()->getFlashdata('success')): ?>
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<?= esc(session()->getFlashdata('success')) ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		<?php $e = session()->getFlashdata('error');
		echo is_array($e) ? implode('<br>', array_map('esc', $e)) : esc($e); ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
<?php endif; ?>

<?= view('components/modern_filter', [
	'title' => 'Filter Pemetaan',
	'action' => current_url(),
	'filters' => [
		[
			'type' => 'text',
			'name' => 'search',
			'label' => 'Cari',
			'icon' => 'bi-search',
			'col' => 'col-md-5',
			'placeholder' => 'Kode CPL, Kode CPMK, atau Mata Kuliah...',
			'value' => $filters['search'] ?? ''
		]
	],
	'buttonCol' => 'col-md-2',
	'buttonText' => 'Terapkan',
	'showReset' => true
]) ?>

<div class="shadow-sm border-0">
	<div class="p-0">
		<?php if (empty($rows)): ?>
			<div class="text-center text-muted py-5">
				<i class="bi bi-diagram-3 fs-1"></i>
				<p class="mt-3 fw-semibold">Belum ada pemetaan</p>
				<p class="small">Silakan tambah data atau sesuaikan filter</p>
			</div>
		<?php else: ?>
			<div class="modern-table-wrapper">
				<div class="scroll-indicator"></div>
				<table class="modern-table" id="pemetaanTable">
					<thead>
						<tr>
							<th class="text-center" style="width: 110px;">Kode CPL</th>
							<th class="text-center" style="width: 130px;">Kode CPMK</th>
							<th class="text-center" style="min-width: 300px;">Mata Kuliah</th>
							<th class="text-center" style="width: 120px;">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$lastCpl = null;
						$cplRowspan = array_count_values(array_column($rows, 'kode_cpl'));
						?>
						<?php foreach ($rows as $r): ?>
							<tr data-group="<?= esc($r['kode_cpl']) ?>">
								<?php if ($lastCpl != $r['kode_cpl']): ?>
									<td rowspan="<?= $cplRowspan[$r['kode_cpl']] ?>" class="text-center fw-semibold" style="vertical-align: middle;">
										<?= esc($r['kode_cpl']) ?>
									</td>
								<?php endif; ?>
								<td class="text-nowrap"><?= esc($r['kode_cpmk']) ?></td>
								<td style="white-space: normal; word-break: break-word;"><?= esc($r['mk_list']) ?></td>
								<td class="text-center">
									<div class="d-flex gap-1 justify-content-center">
										<a href="<?= base_url('admin/pemetaan-cpl-mk-cpmk/edit/' . (int) $r['cpl_id'] . '/' . (int) $r['cpmk_id']) ?>"
											class="btn btn-sm btn-outline-primary"
											data-bs-toggle="tooltip"
											title="Edit">
											<i class="bi bi-pencil-square"></i>
										</a>
										<button type="button" class="btn btn-sm btn-outline-danger"
											data-bs-toggle="modal"
											data-bs-target="#deleteModal"
											data-url="<?= base_url('admin/pemetaan-cpl-mk-cpmk/deleteGroup/' . (int) $r['cpl_id'] . '/' . (int) $r['cpmk_id']) ?>"
											title="Hapus">
											<i class="bi bi-trash3"></i>
										</button>
									</div>
								</td>
							</tr>
							<?php $lastCpl = $r['kode_cpl']; ?>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<!-- Pagination Container -->
			<div id="pemetaanTable_pagination" class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3"></div>

			<div class="card-footer bg-light border-0 py-3">
				<div class="d-flex align-items-center gap-3 justify-content-center">
					<small class="text-muted">
						<i class="bi bi-info-circle me-1"></i>
						Total: <?= count($rows) ?> pemetaan
					</small>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Konfirmasi Penghapusan</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<p>Hapus semua pemetaan MK untuk kombinasi CPL & CPMK ini?</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
				<form id="deleteForm" action="" method="post" class="d-inline">
					<button type="submit" class="btn btn-danger">Ya, Hapus</button>
				</form>
			</div>
		</div>
	</div>
</div>

<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Delete modal handler
		const deleteModal = document.getElementById('deleteModal');
		if (deleteModal) {
			deleteModal.addEventListener('show.bs.modal', function(event) {
				const button = event.relatedTarget;
				const url = button.getAttribute('data-url');
				const form = deleteModal.querySelector('#deleteForm');
				form.action = url;
			});
		}

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

		// Group-aware pagination (paginates by CPL groups to preserve rowspan)
		function initGroupPagination(tableId, groupsPerPage = 5) {
			const table = document.getElementById(tableId);
			if (!table) return;

			const tbody = table.querySelector('tbody');
			const allRows = Array.from(tbody.querySelectorAll('tr'));

			// Build groups based on data-group attribute
			const groups = [];
			let currentGroup = null;
			allRows.forEach(row => {
				const groupKey = row.getAttribute('data-group');
				if (!currentGroup || currentGroup.key !== groupKey) {
					currentGroup = {
						key: groupKey,
						rows: []
					};
					groups.push(currentGroup);
				}
				currentGroup.rows.push(row);
			});

			const totalGroups = groups.length;
			const totalRows = allRows.length;
			const totalPages = Math.ceil(totalGroups / groupsPerPage);
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
				const startGroup = (page - 1) * groupsPerPage;
				const endGroup = Math.min(startGroup + groupsPerPage, totalGroups);

				// Determine which groups are visible
				const visibleGroups = new Set();
				for (let i = startGroup; i < endGroup; i++) {
					visibleGroups.add(groups[i].key);
				}

				// Show/hide rows and rebuild rowspan cells
				allRows.forEach(row => {
					const groupKey = row.getAttribute('data-group');
					row.style.display = visibleGroups.has(groupKey) ? '' : 'none';
				});

				renderPagination();

				const wrapper = table.closest('.modern-table-wrapper');
				if (wrapper) wrapper.scrollTop = 0;
			}

			function renderPagination() {
				if (totalGroups <= groupsPerPage) {
					paginationContainer.innerHTML = '';
					return;
				}

				const startGroup = (currentPage - 1) * groupsPerPage;
				const endGroup = Math.min(currentPage * groupsPerPage, totalGroups);

				// Count visible rows for display info
				let visibleRowCount = 0;
				let rowsBefore = 0;
				for (let i = 0; i < totalGroups; i++) {
					if (i < startGroup) rowsBefore += groups[i].rows.length;
					if (i >= startGroup && i < endGroup) visibleRowCount += groups[i].rows.length;
				}
				const startEntry = rowsBefore + 1;
				const endEntry = rowsBefore + visibleRowCount;

				let html = `
					<div class="text-muted small">
						Menampilkan ${startEntry} sampai ${endEntry} dari ${totalRows} data (${totalGroups} CPL)
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

		// Init group pagination (5 CPL groups per page)
		initGroupPagination('pemetaanTable', 5);

		// Initialize Bootstrap tooltips
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
		tooltipTriggerList.map(function(el) {
			return new bootstrap.Tooltip(el);
		});
	});
</script>

<?= $this->endSection() ?>
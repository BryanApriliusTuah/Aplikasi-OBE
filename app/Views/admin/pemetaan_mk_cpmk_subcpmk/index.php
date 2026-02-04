<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="d-flex justify-content-between align-items-center mb-3">
	<h2 class="mb-0 fw-bold">Pemetaan MK – CPMK – SubCPMK</h2>
	<div class="d-flex gap-2">
		<div class="btn-group">
			<button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
				<i class="bi bi-download"></i> Download
			</button>
			<ul class="dropdown-menu dropdown-menu-end">
				<li>
					<a class="dropdown-item" href="<?= base_url('admin/pemetaan-mk-cpmk-sub/export-pdf') ?>">
						<i class="bi bi-file-earmark-pdf text-danger"></i> PDF
					</a>
				</li>
				<li>
					<a class="dropdown-item" href="<?= base_url('admin/pemetaan-mk-cpmk-sub/export-excel') ?>">
						<i class="bi bi-file-earmark-excel text-success"></i> Excel
					</a>
				</li>
			</ul>
		</div>
		<a href="<?= base_url('admin/pemetaan-mk-cpmk-sub/create') ?>" class="btn btn-primary">
			<i class="bi bi-plus-circle"></i> Tambah
		</a>
	</div>
</div>

<?php if (session()->getFlashdata('success')): ?>
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<?= esc(session()->getFlashdata('success')) ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
<?php endif ?>
<?php if (session()->getFlashdata('error')): ?>
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		<?= esc(session()->getFlashdata('error')) ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
<?php endif ?>

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
			'placeholder' => 'Kode CPL, CPMK, SubCPMK, atau Mata Kuliah...',
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
				<p class="mt-3 fw-semibold">Belum ada data pemetaan</p>
				<p class="small">Silakan tambah data atau sesuaikan filter</p>
			</div>
		<?php else: ?>
			<div class="modern-table-wrapper">
				<div class="scroll-indicator"></div>
				<table class="modern-table" id="pemetaanSubTable">
					<thead>
						<tr>
							<th class="text-center" style="width: 50px;">No</th>
							<th class="text-center">Kode CPL</th>
							<th class="text-center">Kode CPMK</th>
							<th class="text-center" style="min-width: 200px;">Nama Mata Kuliah</th>
							<th class="text-center">Kode SubCPMK</th>
							<th class="text-center" style="min-width: 250px;">Deskripsi</th>
							<th class="text-center" style="width: 100px;">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$no = 1;
						$lastCpl = null;
						$lastCpmk = null;
						$lastSubCpmk = null;
						$rowspanData = [];

						foreach ($rows as $row) {
							$rowspanData[$row['kode_cpl']]['count'] = ($rowspanData[$row['kode_cpl']]['count'] ?? 0) + 1;
							$rowspanData[$row['kode_cpl']][$row['kode_cpmk']]['count'] = ($rowspanData[$row['kode_cpl']][$row['kode_cpmk']]['count'] ?? 0) + 1;
							$rowspanData[$row['kode_cpl']][$row['kode_cpmk']][$row['id']]['count'] = ($rowspanData[$row['kode_cpl']][$row['kode_cpmk']][$row['id']]['count'] ?? 0) + 1;
						}
						?>
						<?php foreach ($rows as $row): ?>
							<tr data-group="<?= esc($row['kode_cpl']) ?>">
								<?php if ($lastSubCpmk != $row['id']): ?>
									<td rowspan="<?= $rowspanData[$row['kode_cpl']][$row['kode_cpmk']][$row['id']]['count'] ?>" class="text-center fw-semibold text-muted" style="vertical-align: middle;"><?= $no++ ?></td>
								<?php endif; ?>

								<?php if ($lastCpl != $row['kode_cpl']): ?>
									<td rowspan="<?= $rowspanData[$row['kode_cpl']]['count'] ?>" class="text-center fw-semibold" style="vertical-align: middle;"><?= esc($row['kode_cpl']) ?></td>
								<?php endif; ?>

								<?php if ($lastCpmk != $row['kode_cpmk']): ?>
									<td rowspan="<?= $rowspanData[$row['kode_cpl']][$row['kode_cpmk']]['count'] ?>" class="text-center" style="vertical-align: middle;"><?= esc($row['kode_cpmk']) ?></td>
								<?php endif; ?>

								<td style="white-space: normal;"><?= esc($row['mata_kuliah']) ?></td>

								<?php if ($lastSubCpmk != $row['id']): ?>
									<td rowspan="<?= $rowspanData[$row['kode_cpl']][$row['kode_cpmk']][$row['id']]['count'] ?>" style="white-space: normal; vertical-align: middle;"><?= esc($row['kode_sub_cpmk']) ?></td>
									<td rowspan="<?= $rowspanData[$row['kode_cpl']][$row['kode_cpmk']][$row['id']]['count'] ?>" style="white-space: normal; vertical-align: middle;"><?= esc($row['deskripsi']) ?></td>
									<td rowspan="<?= $rowspanData[$row['kode_cpl']][$row['kode_cpmk']][$row['id']]['count'] ?>" class="text-center" style="vertical-align: middle;">
										<div class="d-flex justify-content-center gap-1">
											<a href="<?= base_url('admin/pemetaan-mk-cpmk-sub/edit/' . $row['id']) ?>"
												class="btn btn-sm btn-outline-primary"
												data-bs-toggle="tooltip"
												title="Edit">
												<i class="bi bi-pencil-square"></i>
											</a>
											<button type="button" class="btn btn-sm btn-outline-danger"
												data-bs-toggle="modal"
												data-bs-target="#deleteModal"
												data-id="<?= $row['id'] ?>"
												title="Hapus">
												<i class="bi bi-trash3"></i>
											</button>
										</div>
									</td>
								<?php endif; ?>
							</tr>
							<?php
							$lastCpl = $row['kode_cpl'];
							$lastCpmk = $row['kode_cpmk'];
							$lastSubCpmk = $row['id'];
							?>
						<?php endforeach ?>
					</tbody>
				</table>
			</div>

			<!-- Pagination Container -->
			<div id="pemetaanSubTable_pagination" class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3"></div>

			<div class="card-footer bg-light border-0 py-3">
				<div class="d-flex align-items-center gap-3 justify-content-center">
					<small class="text-muted">
						<i class="bi bi-info-circle me-1"></i>
						Total: <?= count($rows) ?> data pemetaan
					</small>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="deleteModalLabel">Konfirmasi Penghapusan</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<p>Anda yakin akan menghapus SubCPMK ini?</p>
				<p class="text-danger fw-bold">RPS mingguan yang memuat SubCPMK ini juga akan terhapus dan tidak bisa dipulihkan.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
				<form id="deleteForm" action="" method="post">
					<?= csrf_field() ?>
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
				const id = button.getAttribute('data-id');
				const form = deleteModal.querySelector('#deleteForm');
				const baseUrl = '<?= rtrim(base_url(), '/') ?>';
				form.action = `${baseUrl}/admin/pemetaan-mk-cpmk-sub/delete/${id}`;
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

				const visibleGroups = new Set();
				for (let i = startGroup; i < endGroup; i++) {
					visibleGroups.add(groups[i].key);
				}

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
		initGroupPagination('pemetaanSubTable', 5);

		// Initialize Bootstrap tooltips
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
		tooltipTriggerList.map(function(el) {
			return new bootstrap.Tooltip(el);
		});
	});
</script>

<?= $this->endSection() ?>
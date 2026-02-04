<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<h2 class="mb-0 fw-bold">Rencana Mingguan RPS</h2>

<div class="d-flex justify-content-between align-items-center mb-3 mt-3">
	<a href="<?= base_url('rps') ?>" class="btn btn-secondary btn-lg">
		<i class="bi bi-arrow-left"></i> Kembali ke Daftar RPS
	</a>
	<a href="<?= base_url('rps/mingguan-create/' . $rps_id) ?>" class="btn btn-primary btn-lg">
		<i class="bi bi-plus-circle"></i> Tambah Mingguan
	</a>
</div>

<?php if (session()->getFlashdata('success')): ?>
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<?= session()->getFlashdata('success') ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
	</div>
<?php endif ?>

<?php
if ($totalBobot == 100) {
	$notifClass = 'alert-success';
	$notifText = 'Total bobot sudah PAS 100%.';
} elseif ($totalBobot < 100) {
	$notifClass = 'alert-danger';
	$notifText = 'Total bobot masih kurang (<100%).';
} else {
	$notifClass = 'alert-warning';
	$notifText = 'Total bobot LEBIH dari 100%.';
}
?>
<div class="alert <?= $notifClass ?> d-flex align-items-center" role="alert" style="font-size:1.1em;">
	<i class="bi bi-exclamation-circle me-2"></i>
	<div>
		<strong>Total seluruh bobot dari semua minggu:</strong>
		<span style="font-weight:bold;"><?= $totalBobot ?>%</span>
		&mdash; <?= $notifText ?>
	</div>
</div>

<?= view('components/modern_filter', [
	'title' => 'Filter Rencana Mingguan',
	'action' => current_url(),
	'filters' => [
		[
			'type' => 'text',
			'name' => 'search',
			'label' => 'Cari',
			'icon' => 'bi-search',
			'col' => 'col-md-5',
			'placeholder' => 'Minggu, CPL, CPMK, SubCPMK...',
			'value' => ''
		]
	],
	'buttonCol' => 'col-md-2',
	'buttonText' => 'Cari',
	'showReset' => true
]) ?>

<div class="shadow-sm border-0">
	<div class="p-0">
		<?php if (empty($mingguan)): ?>
			<div class="text-center text-muted py-5">
				<i class="bi bi-calendar-x fs-1"></i>
				<p class="mt-3 fw-semibold">Data belum ada</p>
				<p class="small">Silakan tambah rencana mingguan atau sesuaikan filter</p>
			</div>
		<?php else: ?>
			<div class="modern-table-wrapper">
				<div class="scroll-indicator"></div>
				<table class="modern-table" id="mingguanTable">
					<thead>
						<tr>
							<th class="text-center">Minggu</th>
							<th class="text-center">CPL</th>
							<th class="text-center">CPMK</th>
							<th class="text-center">SubCPMK</th>
							<th class="text-center" style="min-width: 200px;">Teknik Penilaian</th>
							<th class="text-center">Bobot (%)</th>
							<th class="text-center" style="width: 100px;">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php $no = 1;
						foreach ($mingguan as $m): ?>
							<tr>
								<td class="text-center"><?= esc($m['minggu']) ?></td>
								<td class="text-center"><?= esc($m['kode_cpl'] ?? '-') ?></td>
								<td class="text-center"><?= esc($m['kode_cpmk'] ?? '-') ?></td>
								<td class="text-center"><?= esc($m['kode_sub_cpmk'] ?? '-') ?></td>
								<td>
									<?php
									$teknik = json_decode($m['teknik_penilaian'], true);
									$label_teknik = [
										'partisipasi'   => 'Partisipasi',
										'observasi'     => 'Observasi',
										'unjuk_kerja'   => 'Unjuk Kerja',
										'proyek'        => 'Proyek',
										'tes_tulis_uts' => 'Tes Tulis (UTS)',
										'tes_tulis_uas' => 'Tes Tulis (UAS)',
										'tes_lisan'     => 'Tes Lisan'
									];
									$hasil = [];
									if (is_array($teknik)) {
										foreach ($teknik as $k => $bobot) {
											$nm = $label_teknik[$k] ?? ucfirst($k);
											if ($bobot > 0) {
												$hasil[] = $nm . ' (' . $bobot . ')';
											}
										}
									}
									echo !empty($hasil) ? implode(', ', $hasil) : '-';
									?>
								</td>
								<td class="text-center"><?= esc($m['bobot']) ?></td>
								<td class="text-center">
									<div class="d-flex gap-1 justify-content-center">
										<a href="<?= base_url('rps/mingguan-edit/' . $m['id']) ?>"
											class="btn btn-outline-primary btn-sm"
											data-bs-toggle="tooltip"
											title="Edit">
											<i class="bi bi-pencil-square"></i>
										</a>
										<form action="<?= base_url('rps/mingguan-delete/' . $m['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Yakin hapus?');">
											<button class="btn btn-outline-danger btn-sm" type="submit"
												data-bs-toggle="tooltip"
												title="Hapus">
												<i class="bi bi-trash"></i>
											</button>
										</form>
									</div>
								</td>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div>

			<!-- Pagination Container -->
			<div id="mingguanTable_pagination" class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3"></div>

			<div class="card-footer bg-light border-0 py-3">
				<div class="d-flex align-items-center gap-3 justify-content-center">
					<small class="text-muted">
						<i class="bi bi-info-circle me-1"></i>
						Total: <?= count($mingguan) ?> rencana mingguan
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

		// Client-side search filter
		const filterForm = document.querySelector('.modern-filter-wrapper form');
		if (filterForm) {
			filterForm.addEventListener('submit', function(e) {
				e.preventDefault();
				const searchInput = this.querySelector('input[name="search"]');
				const searchTerm = searchInput ? searchInput.value.toLowerCase().trim() : '';
				const table = document.getElementById('mingguanTable');
				if (!table) return;

				const rows = Array.from(table.querySelector('tbody').querySelectorAll('tr'));
				let visibleNum = 1;

				rows.forEach(row => {
					const text = row.textContent.toLowerCase();
					if (searchTerm === '' || text.includes(searchTerm)) {
						row.style.display = '';
						row.setAttribute('data-filtered', 'visible');
						const firstCell = row.querySelector('td:first-child');
						if (firstCell) firstCell.textContent = visibleNum++;
					} else {
						row.style.display = 'none';
						row.setAttribute('data-filtered', 'hidden');
					}
				});

				// Re-init pagination after filter
				initPagination('mingguanTable', 10);
			});
		}

		// Pagination
		function initPagination(tableId, rowsPerPage = 10) {
			const table = document.getElementById(tableId);
			if (!table) return;

			const tbody = table.querySelector('tbody');
			const rows = Array.from(tbody.querySelectorAll('tr')).filter(row => {
				return row.getAttribute('data-filtered') !== 'hidden';
			});
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

		// Init pagination for mingguan table
		initPagination('mingguanTable', 10);

		// Initialize Bootstrap tooltips
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
		tooltipTriggerList.map(function(el) {
			return new bootstrap.Tooltip(el);
		});
	});
</script>

<?= $this->endSection() ?>
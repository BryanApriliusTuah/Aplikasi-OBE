<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="d-flex justify-content-between align-items-center mb-3">
	<h2 class="mb-0 fw-bold">Manajemen User</h2>
	<div class="d-flex gap-2">
		<form action="<?= base_url('admin/user/generate') ?>" method="post" class="d-inline" onsubmit="return confirm('Generate akun user untuk semua dosen & mahasiswa yang belum memiliki akun?');">
			<?= csrf_field() ?>
			<button type="submit" class="btn btn-success">
				<i class="bi bi-people-fill"></i> Generate User
			</button>
		</form>
		<a href="<?= base_url('admin/user/create') ?>" class="btn btn-primary">
			<i class="bi bi-plus-lg"></i> Tambah User
		</a>
	</div>
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
	'title' => 'Filter User',
	'action' => current_url(),
	'filters' => [
		[
			'type' => 'select',
			'name' => 'role',
			'label' => 'Role',
			'icon' => 'bi-shield-lock-fill',
			'col' => 'col-md-3',
			'options' => [
				'' => 'Semua Role',
				'admin' => 'Admin',
				'dosen' => 'Dosen',
				'mahasiswa' => 'Mahasiswa',
			],
			'selected' => $filters['role'] ?? ''
		],
		[
			'type' => 'text',
			'name' => 'search',
			'label' => 'Cari',
			'icon' => 'bi-search',
			'col' => 'col-md-5',
			'placeholder' => 'Username atau Nama Lengkap...',
			'value' => $filters['search'] ?? ''
		]
	],
	'buttonCol' => 'col-md-2',
	'buttonText' => 'Terapkan',
	'showReset' => true
]) ?>

<div class="shadow-sm border-0">
	<div class="p-0">
		<?php if (empty($users)): ?>
			<div class="text-center text-muted py-5">
				<i class="bi bi-person-x fs-1"></i>
				<p class="mt-3 fw-semibold">Belum ada data user</p>
				<p class="small">Silakan tambah data atau sesuaikan filter</p>
			</div>
		<?php else: ?>
			<div class="modern-table-wrapper">
				<div class="scroll-indicator"></div>
				<table class="modern-table" id="userTable">
					<thead>
						<tr>
							<th class="text-center" style="width: 50px;">No</th>
							<th class="text-center">Username</th>
							<th class="text-center" style="min-width: 200px;">Nama Lengkap</th>
							<th class="text-center">Role</th>
							<th class="text-center" style="width: 120px;">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($users as $index => $user):
							$badge_class = 'bg-secondary';
							if ($user['role'] == 'admin') {
								$badge_class = 'bg-primary';
							} elseif ($user['role'] == 'dosen') {
								$badge_class = 'bg-info';
							} elseif ($user['role'] == 'mahasiswa') {
								$badge_class = 'bg-success';
							}
						?>
						<tr>
							<td class="text-center fw-semibold text-muted"><?= $index + 1 ?></td>
							<td><?= esc($user['username']) ?></td>
							<td><?= esc($user['nama_lengkap'] ?? '-') ?></td>
							<td class="text-center">
								<span class="badge <?= $badge_class ?>">
									<?= ucfirst(esc($user['role'])) ?>
								</span>
							</td>
							<td class="text-center">
								<div class="d-flex gap-1 justify-content-center">
									<a href="<?= base_url('admin/user/edit/' . $user['id']) ?>"
										class="btn btn-outline-primary btn-sm"
										data-bs-toggle="tooltip"
										title="Edit">
										<i class="bi bi-pencil-square"></i>
									</a>
									<button type="button" class="btn btn-outline-danger btn-sm"
										data-bs-toggle="modal"
										data-bs-target="#deleteModal"
										data-delete-url="<?= base_url('admin/user/delete/' . $user['id']) ?>"
										title="Hapus">
										<i class="bi bi-trash3"></i>
									</button>
								</div>
							</td>
						</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<!-- Pagination Container -->
			<div id="userTable_pagination" class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3"></div>

			<div class="card-footer bg-light border-0 py-3">
				<div class="d-flex align-items-center gap-3 justify-content-center">
					<small class="text-muted">
						<i class="bi bi-info-circle me-1"></i>
						Total: <?= count($users) ?> user
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
				<h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				Apakah Anda yakin ingin menghapus user ini? Proses ini tidak dapat dibatalkan.
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
				<form id="deleteForm" action="" method="post" class="d-inline">
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
				const deleteUrl = button.getAttribute('data-delete-url');
				document.getElementById('deleteForm').setAttribute('action', deleteUrl);
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

		// Init pagination for user table
		initPagination('userTable', 10);

		// Initialize Bootstrap tooltips
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
		tooltipTriggerList.map(function(el) {
			return new bootstrap.Tooltip(el);
		});
	});
</script>

<?= $this->endSection() ?>

<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="container-fluid px-4">

	<!-- Page Header -->
	<div class="d-flex justify-content-between align-items-center my-4">
		<div>
			<nav aria-label="breadcrumb">
				<ol class="breadcrumb mb-1" style="font-size: 0.8125rem;">
					<li class="breadcrumb-item">
						<a href="<?= base_url('admin/mengajar') ?>" class="text-decoration-none" style="color: var(--modern-table-header-text);">
							<i class="bi bi-calendar3 me-1"></i>Jadwal Mengajar
						</a>
					</li>
					<li class="breadcrumb-item active" style="color: var(--modern-table-text);">
						Kelola Mahasiswa
					</li>
				</ol>
			</nav>
			<h2 class="fw-bold mb-0" style="color: var(--modern-table-text); font-size: 1.5rem;">
				Kelola Mahasiswa
			</h2>
		</div>
		<a href="<?= base_url('admin/mengajar') ?>" class="btn btn-outline-secondary btn-sm">
			<i class="bi bi-arrow-left me-1"></i> Kembali
		</a>
	</div>

	<!-- Flash Messages -->
	<?php if (session()->getFlashdata('success')): ?>
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			<i class="bi bi-check-circle me-1"></i>
			<?= esc(session()->getFlashdata('success')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
		</div>
	<?php endif; ?>
	<?php if (session()->getFlashdata('error')): ?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<i class="bi bi-exclamation-triangle me-1"></i>
			<?= esc(session()->getFlashdata('error')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
		</div>
	<?php endif; ?>

	<!-- Jadwal Info Card -->
	<div class="card border-0 mb-4" style="border: 1px solid var(--modern-table-border) !important; box-shadow: var(--modern-table-shadow-sm);">
		<div class="card-header fw-semibold"
			style="background: linear-gradient(to bottom, var(--modern-table-header-bg-start), var(--modern-table-header-bg-end));
				   border-bottom: 2px solid var(--modern-table-border-header);
				   color: var(--modern-table-header-text);
				   font-size: 0.875rem;
				   text-transform: uppercase;
				   letter-spacing: 0.025em;
				   padding: 0.875rem 1.25rem;">
			<i class="bi bi-info-circle me-2"></i>Informasi Jadwal
		</div>
		<div class="card-body py-3">
			<div class="row g-3">
				<div class="col-md-5">
					<div class="d-flex flex-column gap-1" style="font-size: 0.875rem;">
						<div class="d-flex gap-2">
							<span style="color: var(--modern-table-header-text); min-width: 120px;"><i class="bi bi-book me-1"></i>Mata Kuliah</span>
							<span class="fw-semibold" style="color: var(--modern-table-text);">
								<?= esc($jadwal['kode_mk']) ?> – <?= esc($jadwal['nama_mk']) ?>
							</span>
						</div>
						<div class="d-flex gap-2">
							<span style="color: var(--modern-table-header-text); min-width: 120px;"><i class="bi bi-mortarboard me-1"></i>Program Studi</span>
							<span style="color: var(--modern-table-text);"><?= esc($jadwal['program_studi_nama'] ?? '–') ?></span>
						</div>
					</div>
				</div>
				<div class="col-md-4">
					<div class="d-flex flex-column gap-1" style="font-size: 0.875rem;">
						<div class="d-flex gap-2">
							<span style="color: var(--modern-table-header-text); min-width: 120px;"><i class="bi bi-calendar me-1"></i>Tahun Akademik</span>
							<span style="color: var(--modern-table-text);"><?= esc($jadwal['tahun_akademik']) ?></span>
						</div>
						<div class="d-flex gap-2">
							<span style="color: var(--modern-table-header-text); min-width: 120px;"><i class="bi bi-door-open me-1"></i>Kelas / Ruang</span>
							<span style="color: var(--modern-table-text);">
								<?= esc($jadwal['kelas']) ?>
								<?php if (!empty($jadwal['ruang'])): ?>
									<span class="text-muted">/ <?= esc($jadwal['ruang']) ?></span>
								<?php endif; ?>
							</span>
						</div>
					</div>
				</div>
				<div class="col-md-3 d-flex align-items-center justify-content-md-end">
					<div class="text-center px-4 py-2 rounded-3"
						style="background: var(--modern-table-border-light); border: 1px solid var(--modern-table-border);">
						<div class="fw-bold" style="font-size: 1.75rem; color: var(--modern-table-text); line-height: 1;">
							<?= count($mahasiswa_list) ?>
						</div>
						<div style="font-size: 0.75rem; color: var(--modern-table-header-text); text-transform: uppercase; letter-spacing: 0.05em;">
							Mahasiswa Terdaftar
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Add Mahasiswa Section -->
	<div class="modern-filter-wrapper mb-4">
		<div class="modern-filter-header">
			<div class="d-flex align-items-center gap-2">
				<i class="bi bi-person-plus-fill text-primary"></i>
				<span class="modern-filter-title">Tambah Mahasiswa</span>
			</div>
		</div>
		<div class="modern-filter-body">
			<form action="<?= base_url('admin/mengajar/' . (int)$jadwal['id'] . '/mahasiswa/add') ?>" method="post">
				<?= csrf_field() ?>
				<div class="row g-3 align-items-end">
					<div class="col-md-10">
						<label class="modern-filter-label">
							<i class="bi bi-search me-1"></i>
							Cari berdasarkan NIM atau Nama (ketik minimal 2 karakter)
						</label>
						<select class="form-select modern-filter-input" name="nim[]" id="mahasiswa-search" multiple="multiple" style="width: 100%;"></select>
					</div>
					<div class="col-md-2 d-flex">
						<button type="submit" class="btn btn-primary modern-filter-btn flex-fill">
							<i class="bi bi-plus-lg me-1"></i> Tambah
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- Enrolled Mahasiswa Table -->
	<div class="shadow-sm border-0">
		<div class="p-0">
			<!-- Table Header Bar -->
			<div class="d-flex justify-content-between align-items-center px-0 pb-3">
				<h6 class="fw-semibold mb-0" style="color: var(--modern-table-text);">
					<i class="bi bi-people-fill me-1"></i>
					Daftar Mahasiswa Terdaftar
				</h6>
				<span class="badge rounded-pill"
					style="background: var(--modern-table-header-bg-end); color: var(--modern-table-header-text); border: 1px solid var(--modern-table-border); font-size: 0.8125rem; font-weight: 600; padding: 0.35em 0.75em;">
					<?= count($mahasiswa_list) ?> mahasiswa
				</span>
			</div>

			<?php if (empty($mahasiswa_list)): ?>
				<div class="text-center text-muted py-5"
					style="border: 1px solid var(--modern-table-border); border-radius: 0.375rem; background: white;">
					<i class="bi bi-person-slash fs-1" style="opacity: 0.3;"></i>
					<p class="mt-3 fw-semibold mb-1" style="color: var(--modern-table-header-text);">Belum ada mahasiswa terdaftar</p>
					<p class="small mb-0" style="color: var(--modern-table-header-text); opacity: 0.7;">Gunakan form di atas untuk menambahkan mahasiswa.</p>
				</div>
			<?php else: ?>
				<div class="modern-table-wrapper">
					<div class="scroll-indicator"></div>
					<table class="modern-table" id="mahasiswaTable">
						<thead>
							<tr>
								<th class="text-center" style="width: 50px;">No</th>
								<th>NIM</th>
								<th style="min-width: 220px;">Nama Lengkap</th>
								<th class="text-center">Angkatan</th>
								<th class="text-center">Status</th>
								<th class="text-center" style="width: 80px;">Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($mahasiswa_list as $i => $mhs): ?>
								<tr>
									<td class="text-center fw-semibold" style="color: var(--modern-table-header-text);"><?= $i + 1 ?></td>
									<td class="font-monospace text-nowrap fw-semibold" style="color: var(--modern-table-text);">
										<?= esc($mhs['nim']) ?>
									</td>
									<td style="color: var(--modern-table-text);"><?= esc($mhs['nama_lengkap']) ?></td>
									<td class="text-center" style="color: var(--modern-table-header-text);">
										<?= esc($mhs['tahun_angkatan'] ?? '–') ?>
									</td>
									<td class="text-center">
										<?php $st = $mhs['status_mahasiswa'] ?? 'Aktif'; ?>
										<span class="badge <?= $st === 'Aktif' ? 'bg-success' : 'bg-secondary' ?>"
											style="font-size: 0.75rem;">
											<?= esc($st) ?>
										</span>
									</td>
									<td class="text-center">
										<form action="<?= base_url('admin/mengajar/' . (int)$jadwal['id'] . '/mahasiswa/remove') ?>" method="post"
											onsubmit="return confirm('Hapus <?= esc(addslashes($mhs['nama_lengkap'])) ?> dari jadwal ini?');">
											<?= csrf_field() ?>
											<input type="hidden" name="nim" value="<?= esc($mhs['nim']) ?>">
											<button type="submit" class="btn btn-outline-danger btn-sm"
												data-bs-toggle="tooltip" title="Hapus dari jadwal">
												<i class="bi bi-trash3"></i>
											</button>
										</form>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<!-- Pagination container -->
				<div id="mahasiswaTable_pagination" class="d-flex justify-content-between align-items-center mt-3 px-1 pb-2"></div>

				<div class="py-3 mt-1" style="border-top: 1px solid var(--modern-table-border-light);">
					<div class="d-flex align-items-center gap-2 justify-content-center">
						<small style="color: var(--modern-table-header-text);">
							<i class="bi bi-info-circle me-1"></i>
							Total: <strong><?= count($mahasiswa_list) ?></strong> mahasiswa terdaftar di jadwal ini
						</small>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>

</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	$(document).ready(function () {

		// ── Select2 AJAX search ──────────────────────────────────────────────────
		$('#mahasiswa-search').select2({
			theme: 'bootstrap-5',
			placeholder: 'Ketik NIM atau nama mahasiswa...',
			allowClear: true,
			minimumInputLength: 2,
			width: '100%',
			ajax: {
				url: '<?= base_url('admin/mengajar/' . (int)$jadwal['id'] . '/mahasiswa/search') ?>',
				dataType: 'json',
				delay: 300,
				headers: { 'X-Requested-With': 'XMLHttpRequest' },
				data: function (params) {
					return { q: params.term };
				},
				processResults: function (data) {
					return { results: data.results };
				},
				cache: true
			}
		});

		// ── Scroll indicator ─────────────────────────────────────────────────────
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

		// ── Client-side pagination ───────────────────────────────────────────────
		function initPagination(tableId, rowsPerPage = 15) {
			const table = document.getElementById(tableId);
			if (!table) return;

			const tbody = table.querySelector('tbody');
			const rows = Array.from(tbody.querySelectorAll('tr'));
			const totalRows = rows.length;
			const totalPages = Math.ceil(totalRows / rowsPerPage);
			let currentPage = 1;

			const paginationContainer = document.getElementById(tableId + '_pagination');
			if (!paginationContainer) return;

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
						Menampilkan ${startEntry}–${endEntry} dari ${totalRows} mahasiswa
					</div>
					<nav><ul class="pagination pagination-sm mb-0">
				`;

				html += `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
					<a class="page-link" href="#" data-page="${currentPage - 1}">
						<i class="bi bi-chevron-left" style="font-size:0.7rem;"></i>
					</a></li>`;

				const maxVisible = 5;
				let startPage = Math.max(1, currentPage - Math.floor(maxVisible / 2));
				let endPage = Math.min(totalPages, startPage + maxVisible - 1);
				if (endPage - startPage + 1 < maxVisible) startPage = Math.max(1, endPage - maxVisible + 1);

				if (startPage > 1) {
					html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
					if (startPage > 2) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
				}

				for (let i = startPage; i <= endPage; i++) {
					html += `<li class="page-item ${i === currentPage ? 'active' : ''}">
						<a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
				}

				if (endPage < totalPages) {
					if (endPage < totalPages - 1) html += `<li class="page-item disabled"><span class="page-link">…</span></li>`;
					html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
				}

				html += `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
					<a class="page-link" href="#" data-page="${currentPage + 1}">
						<i class="bi bi-chevron-right" style="font-size:0.7rem;"></i>
					</a></li>`;

				html += `</ul></nav>`;
				paginationContainer.innerHTML = html;

				paginationContainer.querySelectorAll('a.page-link').forEach(link => {
					link.addEventListener('click', function (e) {
						e.preventDefault();
						const page = parseInt(this.getAttribute('data-page'));
						if (page >= 1 && page <= totalPages) showPage(page);
					});
				});
			}

			showPage(1);
		}

		initPagination('mahasiswaTable', 15);

		// ── Bootstrap tooltips ───────────────────────────────────────────────────
		document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
			new bootstrap.Tooltip(el);
		});
	});
</script>
<?= $this->endSection() ?>

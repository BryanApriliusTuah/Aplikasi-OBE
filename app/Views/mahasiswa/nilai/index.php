<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<style>
	.grade-badge {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 2rem;
		height: 2rem;
		border-radius: 0.375rem;
		font-size: 0.8rem;
		font-weight: 700;
	}

	.course-name {
		font-size: 0.95rem;
		line-height: 1.3;
		color: #2c3e50;
	}

	.course-info {
		display: flex;
		flex-wrap: wrap;
		gap: 0.3rem;
		margin-top: 0.35rem;
	}

	.course-badge {
		display: inline-flex;
		align-items: center;
		gap: 0.25rem;
		font-size: 0.7rem;
		font-weight: 500;
		padding: 0.2rem 0.5rem;
		border-radius: 0.375rem;
		line-height: 1.4;
	}

	.course-badge-code  { background: #f0f4ff; color: #3b5bdb; border: 1px solid #c5d0fa; }
	.course-badge-kelas { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }
	.course-badge-sem   { background: #fdf4ff; color: #7e22ce; border: 1px solid #e9d5ff; }
	.course-badge-sks   { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }

	.badge-status {
		font-size: 0.75rem;
		padding: 0.35rem 0.6rem;
	}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
	<div>
		<h2 class="mb-1 fw-bold">Daftar Nilai</h2>
		<p class="text-muted mb-0">Riwayat nilai seluruh mata kuliah yang telah diambil</p>
	</div>
</div>

<?php if (session()->getFlashdata('success')): ?>
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<?= session()->getFlashdata('success') ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		<?= session()->getFlashdata('error') ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
<?php endif; ?>

<?php if (empty($nilaiList)): ?>
	<div class="text-center py-5 text-muted">
		<i class="bi bi-inbox" style="font-size: 4rem;"></i>
		<p class="mt-3 mb-0 fw-semibold">Belum ada data nilai tersedia</p>
		<small>Nilai akan muncul setelah dosen menginput nilai Anda</small>
	</div>
<?php else: ?>

	<!-- Filter -->
	<div class="modern-filter-wrapper mb-4">
		<div class="modern-filter-header">
			<div class="d-flex align-items-center gap-2">
				<i class="bi bi-funnel-fill text-primary"></i>
				<span class="modern-filter-title">Filter Nilai</span>
			</div>
		</div>
		<div class="modern-filter-body">
			<div class="row g-3 align-items-end">
				<div class="col-md-4">
					<label class="modern-filter-label">
						<i class="bi bi-search me-1"></i>
						Cari Mata Kuliah
					</label>
					<input type="text" id="searchInput" class="form-control modern-filter-input" placeholder="Cari mata kuliah...">
				</div>
				<div class="col-md-3">
					<label class="modern-filter-label">
						<i class="bi bi-calendar-event me-1"></i>
						Semester
					</label>
					<select id="filterTahun" class="form-select modern-filter-input">
						<option value="">Semua Semester</option>
						<?php
						$tahunList = array_unique(array_column($nilaiList, 'tahun_akademik'));
						rsort($tahunList);
						foreach ($tahunList as $tahun):
						?>
							<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-3">
					<label class="modern-filter-label">
						<i class="bi bi-check2-circle me-1"></i>
						Status
					</label>
					<select id="filterStatus" class="form-select modern-filter-input">
						<option value="">Semua Status</option>
						<option value="Lulus">Lulus</option>
						<option value="Tidak Lulus">Tidak Lulus</option>
						<option value="Diproses">Diproses</option>
					</select>
				</div>
				<div class="col-md-2">
					<button type="button" id="resetFilter" class="btn btn-outline-secondary modern-filter-btn-reset w-100">
						<i class="bi bi-arrow-clockwise"></i> Reset
					</button>
				</div>
			</div>
		</div>
	</div>

	<div class="shadow-sm border-0">
		<div class="p-0">
			<div class="modern-table-wrapper">
				<div class="scroll-indicator"></div>
				<table class="modern-table" id="nilaiTable">
					<thead>
						<tr>
							<th class="text-center" style="width: 50px;">No</th>
							<th class="text-center" style="min-width: 260px;">Mata Kuliah</th>
							<th class="text-center" style="min-width: 120px;">Semester</th>
							<th class="text-center" style="width: 80px;">Nilai Akhir</th>
							<th class="text-center" style="width: 80px;">Grade</th>
							<th class="text-center" style="width: 130px;">Status</th>
							<th class="text-center" style="width: 90px;">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$no = 1;
						foreach ($nilaiList as $nilai):
						?>
							<tr data-tahun="<?= esc($nilai['tahun_akademik']) ?>"
								data-status="<?= esc($nilai['status_kelulusan']) ?>"
								data-search="<?= strtolower(esc($nilai['nama_mk']) . ' ' . esc($nilai['kode_mk'])) ?>">
								<td class="text-center fw-semibold text-muted"><?= $no++ ?></td>
								<td class="align-middle">
									<div class="course-name fw-bold"><?= esc($nilai['nama_mk']) ?></div>
									<div class="course-info">
										<span class="course-badge course-badge-code">
											<i class="bi bi-code-square"></i> <?= esc($nilai['kode_mk']) ?>
										</span>
										<span class="course-badge course-badge-kelas">
											<i class="bi bi-people"></i> Kelas <?= esc($nilai['kelas']) ?>
										</span>
										<span class="course-badge course-badge-sem">
											<i class="bi bi-layers"></i> Sem <?= esc($nilai['semester']) ?>
										</span>
										<span class="course-badge course-badge-sks">
											<i class="bi bi-journal-bookmark"></i> <?= esc($nilai['sks']) ?> SKS
										</span>
									</div>
								</td>
								<td class="text-center align-middle">
									<small class="text-muted"><?= esc($nilai['tahun_akademik']) ?></small>
								</td>
								<td class="text-center align-middle fw-bold">
									<?= $nilai['nilai_akhir'] ? number_format($nilai['nilai_akhir'], 2) : '<span class="text-muted">-</span>' ?>
								</td>
								<td class="text-center align-middle">
									<?php if ($nilai['nilai_huruf']): ?>
										<?php
										$gradeClass = 'bg-primary text-white';
										if (in_array($nilai['nilai_huruf'], ['A', 'A-'])) $gradeClass = 'bg-success text-white';
										elseif (in_array($nilai['nilai_huruf'], ['B+', 'B', 'B-'])) $gradeClass = 'bg-info text-white';
										elseif (in_array($nilai['nilai_huruf'], ['C+', 'C'])) $gradeClass = 'bg-warning text-dark';
										elseif (in_array($nilai['nilai_huruf'], ['D', 'E'])) $gradeClass = 'bg-danger text-white';
										?>
										<span class="grade-badge <?= $gradeClass ?>"><?= esc($nilai['nilai_huruf']) ?></span>
									<?php else: ?>
										<span class="text-muted">-</span>
									<?php endif; ?>
								</td>
								<td class="text-center align-middle">
									<?php if ($nilai['status_kelulusan'] == 'Lulus'): ?>
										<span class="badge bg-success badge-status">
											<i class="bi bi-check-circle-fill"></i> Lulus
										</span>
									<?php elseif ($nilai['status_kelulusan'] == 'Tidak Lulus'): ?>
										<span class="badge bg-danger badge-status">
											<i class="bi bi-x-circle-fill"></i> Tidak Lulus
										</span>
									<?php else: ?>
										<span class="badge bg-warning text-dark badge-status">
											<i class="bi bi-hourglass-split"></i> Diproses
										</span>
									<?php endif; ?>
								</td>
								<td class="text-center align-middle">
									<a href="<?= base_url('mahasiswa/nilai/detail/' . $nilai['jadwal_id']) ?>"
										class="btn btn-sm btn-outline-primary"
										data-bs-toggle="tooltip"
										title="Lihat detail nilai CPMK">
										<i class="bi bi-eye"></i> Detail
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<div id="nilaiTable_pagination" class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3"></div>

			<div class="card-footer bg-light border-0 py-3">
				<div class="d-flex align-items-center gap-3 justify-content-center">
					<small class="text-muted" id="tableInfo">
						<i class="bi bi-info-circle me-1"></i>
						Total: <?= count($nilaiList) ?> mata kuliah
					</small>
				</div>
			</div>
		</div>
	</div>

<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
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

		// Filter logic
		const searchInput = document.getElementById('searchInput');
		const filterTahun = document.getElementById('filterTahun');
		const filterStatus = document.getElementById('filterStatus');
		const resetBtn = document.getElementById('resetFilter');
		const tableInfo = document.getElementById('tableInfo');

		function filterTable() {
			const search = searchInput ? searchInput.value.toLowerCase() : '';
			const tahun = filterTahun ? filterTahun.value : '';
			const status = filterStatus ? filterStatus.value : '';

			const rows = document.querySelectorAll('#nilaiTable tbody tr');
			let visibleCount = 0;

			rows.forEach(row => {
				const rowSearch = row.getAttribute('data-search') || '';
				const rowTahun = row.getAttribute('data-tahun') || '';
				const rowStatus = row.getAttribute('data-status') || '';

				const matchSearch = !search || rowSearch.includes(search);
				const matchTahun = !tahun || rowTahun === tahun;
				const matchStatus = !status || rowStatus === status;

				const visible = matchSearch && matchTahun && matchStatus;
				row.style.display = visible ? '' : 'none';
				if (visible) visibleCount++;
			});

			if (tableInfo) {
				tableInfo.innerHTML = `<i class="bi bi-info-circle me-1"></i>Menampilkan ${visibleCount} dari <?= count($nilaiList) ?> mata kuliah`;
			}
		}

		if (searchInput) searchInput.addEventListener('keyup', filterTable);
		if (filterTahun) filterTahun.addEventListener('change', filterTable);
		if (filterStatus) filterStatus.addEventListener('change', filterTable);

		if (resetBtn) {
			resetBtn.addEventListener('click', function() {
				if (searchInput) searchInput.value = '';
				if (filterTahun) filterTahun.value = '';
				if (filterStatus) filterStatus.value = '';
				filterTable();
			});
		}

		// Initialize Bootstrap tooltips
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
		tooltipTriggerList.map(function(el) {
			return new bootstrap.Tooltip(el);
		});
	});
</script>
<?= $this->endSection() ?>

<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<style>
	.dash-stat-card {
		background: #fff;
		border: 1px solid #e5e7eb;
		border-radius: 0.875rem;
		padding: 1.5rem;
		display: flex;
		align-items: center;
		gap: 1.25rem;
		box-shadow: 0 1px 3px rgba(0,0,0,0.05);
		transition: box-shadow 0.2s ease, transform 0.2s ease;
		height: 100%;
	}

	.dash-stat-card:hover {
		box-shadow: 0 4px 12px rgba(0,0,0,0.08);
		transform: translateY(-2px);
	}

	.dash-stat-icon {
		width: 3.25rem;
		height: 3.25rem;
		border-radius: 0.75rem;
		display: flex;
		align-items: center;
		justify-content: center;
		font-size: 1.5rem;
		flex-shrink: 0;
	}

	.dash-stat-icon.blue   { background: #eff6ff; color: #3b82f6; }
	.dash-stat-icon.green  { background: #f0fdf4; color: #22c55e; }
	.dash-stat-icon.purple { background: #faf5ff; color: #a855f7; }

	.dash-stat-label {
		font-size: 0.72rem;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.05em;
		color: #94a3b8;
		margin-bottom: 0.2rem;
	}

	.dash-stat-value {
		font-size: 1.75rem;
		font-weight: 700;
		color: #1e293b;
		line-height: 1;
	}

	.section-card {
		background: #fff;
		border: 1px solid #e5e7eb;
		border-radius: 0.875rem;
		box-shadow: 0 1px 3px rgba(0,0,0,0.05);
		overflow: hidden;
	}

	.section-card-header {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 1rem 1.5rem;
		border-bottom: 1px solid #f1f5f9;
		background: #f8fafc;
	}

	.section-card-title {
		display: flex;
		align-items: center;
		gap: 0.5rem;
		font-size: 0.85rem;
		font-weight: 700;
		text-transform: uppercase;
		letter-spacing: 0.05em;
		color: #475569;
	}

	.course-name {
		font-size: 0.9rem;
		font-weight: 600;
		color: #2c3e50;
		line-height: 1.3;
	}

	.course-info {
		display: flex;
		flex-wrap: wrap;
		gap: 0.25rem;
		margin-top: 0.3rem;
	}

	.course-badge {
		display: inline-flex;
		align-items: center;
		gap: 0.2rem;
		font-size: 0.68rem;
		font-weight: 500;
		padding: 0.15rem 0.45rem;
		border-radius: 0.3rem;
		line-height: 1.4;
	}

	.course-badge-code  { background: #f0f4ff; color: #3b5bdb; border: 1px solid #c5d0fa; }
	.course-badge-kelas { background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa; }

	.grade-pill {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 2rem;
		height: 2rem;
		border-radius: 0.375rem;
		font-size: 0.78rem;
		font-weight: 700;
	}

	.badge-status {
		font-size: 0.72rem;
		padding: 0.3rem 0.55rem;
	}
</style>

<!-- Page Header -->
<div class="mb-4">
	<h2 class="mb-1 fw-bold">Selamat Datang, <?= esc($mahasiswa['nama_lengkap']) ?>!</h2>
	<p class="text-muted mb-0">
		<i class="bi bi-mortarboard-fill me-1"></i><?= esc(ucwords(strtolower($programStudi))) ?>
		<span class="mx-2 text-muted">·</span>
		<i class="bi bi-calendar3 me-1"></i>Angkatan <?= esc($mahasiswa['tahun_angkatan']) ?>
	</p>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
	<div class="col-md-4">
		<div class="dash-stat-card">
			<div class="dash-stat-icon blue">
				<i class="bi bi-book-fill"></i>
			</div>
			<div>
				<div class="dash-stat-label">Total Mata Kuliah</div>
				<div class="dash-stat-value"><?= $totalNilai ?></div>
			</div>
		</div>
	</div>

	<div class="col-md-4">
		<div class="dash-stat-card">
			<div class="dash-stat-icon green">
				<i class="bi bi-check-circle-fill"></i>
			</div>
			<div>
				<div class="dash-stat-label">Mata Kuliah Lulus</div>
				<div class="dash-stat-value"><?= $nilaiLulus ?></div>
			</div>
		</div>
	</div>

	<div class="col-md-4">
		<div class="dash-stat-card">
			<div class="dash-stat-icon purple">
				<i class="bi bi-person-badge-fill"></i>
			</div>
			<div>
				<div class="dash-stat-label">Status Mahasiswa</div>
				<div class="mt-1">
					<?php if ($mahasiswa['status_mahasiswa'] == 'Aktif'): ?>
						<span class="badge bg-success" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">
							<i class="bi bi-check-circle-fill me-1"></i>Aktif
						</span>
					<?php else: ?>
						<span class="badge bg-secondary" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">
							<?= esc($mahasiswa['status_mahasiswa']) ?>
						</span>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Recent Grades -->
<div class="section-card">
	<div class="section-card-header">
		<div class="section-card-title">
			<i class="bi bi-clock-history text-primary"></i>
			Nilai Terbaru
		</div>
		<a href="<?= base_url('mahasiswa/nilai') ?>" class="btn btn-sm btn-primary">
			Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
		</a>
	</div>

	<?php if (empty($recentNilai)): ?>
		<div class="text-center py-5 text-muted">
			<i class="bi bi-inbox" style="font-size: 3rem;"></i>
			<p class="mt-3 mb-0 fw-semibold">Belum ada data nilai</p>
			<small>Nilai akan muncul setelah dosen menginput nilai Anda</small>
		</div>
	<?php else: ?>
		<div class="modern-table-wrapper" style="border: none; border-radius: 0;">
			<div class="scroll-indicator"></div>
			<table class="modern-table" id="recentTable">
				<thead>
					<tr>
						<th class="text-center" style="width: 50px;">No</th>
						<th style="min-width: 240px;">Mata Kuliah</th>
						<th class="text-center" style="min-width: 120px;">Semester</th>
						<th class="text-center" style="width: 90px;">Nilai</th>
						<th class="text-center" style="width: 80px;">Grade</th>
						<th class="text-center" style="width: 120px;">Status</th>
						<th class="text-center" style="width: 90px;">Aksi</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($recentNilai as $index => $nilai):
						$gradeClass = 'bg-primary text-white';
						if (in_array($nilai['nilai_huruf'] ?? '', ['A', 'A-'])) $gradeClass = 'bg-success text-white';
						elseif (in_array($nilai['nilai_huruf'] ?? '', ['B+', 'B', 'B-'])) $gradeClass = 'bg-info text-white';
						elseif (in_array($nilai['nilai_huruf'] ?? '', ['C+', 'C'])) $gradeClass = 'bg-warning text-dark';
						elseif (in_array($nilai['nilai_huruf'] ?? '', ['D', 'E'])) $gradeClass = 'bg-danger text-white';
					?>
						<tr>
							<td class="text-center fw-semibold text-muted"><?= $index + 1 ?></td>
							<td class="align-middle">
								<div class="course-name"><?= esc($nilai['nama_mk']) ?></div>
								<div class="course-info">
									<span class="course-badge course-badge-code">
										<i class="bi bi-code-square"></i> <?= esc($nilai['kode_mk']) ?>
									</span>
									<span class="course-badge course-badge-kelas">
										<i class="bi bi-people"></i> Kelas <?= esc($nilai['kelas']) ?>
									</span>
								</div>
							</td>
							<td class="text-center align-middle">
								<small class="text-muted"><?= esc($nilai['tahun_akademik']) ?></small>
							</td>
							<td class="text-center align-middle fw-bold">
								<?= $nilai['nilai_akhir'] ? number_format($nilai['nilai_akhir'], 2) : '<span class="text-muted">—</span>' ?>
							</td>
							<td class="text-center align-middle">
								<?php if ($nilai['nilai_huruf']): ?>
									<span class="grade-pill <?= $gradeClass ?>"><?= esc($nilai['nilai_huruf']) ?></span>
								<?php else: ?>
									<span class="text-muted">—</span>
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
									<i class="bi bi-eye"></i>
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	<?php endif; ?>
</div>

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

		// Bootstrap tooltips
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
		tooltipTriggerList.map(function(el) {
			return new bootstrap.Tooltip(el);
		});
	});
</script>
<?= $this->endSection() ?>

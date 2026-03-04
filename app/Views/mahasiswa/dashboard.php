<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">
<style>
	/* Hero Banner */
	.mhs-hero {
		background: linear-gradient(135deg, #064e3b 0%, #059669 60%, #34d399 100%);
		border-radius: 1.25rem;
		padding: 2.25rem 2.5rem;
		color: #fff;
		position: relative;
		overflow: hidden;
		margin-bottom: 2rem;
	}
	.mhs-hero::before {
		content: "";
		position: absolute;
		top: -40px; right: -40px;
		width: 220px; height: 220px;
		background: rgba(255,255,255,0.07);
		border-radius: 50%;
	}
	.mhs-hero::after {
		content: "";
		position: absolute;
		bottom: -60px; right: 80px;
		width: 180px; height: 180px;
		background: rgba(255,255,255,0.05);
		border-radius: 50%;
	}
	.mhs-hero-badge {
		display: inline-flex;
		align-items: center;
		gap: 6px;
		background: rgba(255,255,255,0.18);
		border: 1px solid rgba(255,255,255,0.3);
		border-radius: 999px;
		padding: 4px 14px;
		font-size: 0.8rem;
		font-weight: 600;
		margin-bottom: 0.85rem;
		letter-spacing: 0.5px;
	}
	.mhs-hero-title {
		font-size: 1.65rem;
		font-weight: 700;
		margin-bottom: 0.35rem;
	}
	.mhs-hero-meta {
		font-size: 0.88rem;
		opacity: 0.82;
		display: flex;
		flex-wrap: wrap;
		gap: 0 1rem;
		margin-top: 0.3rem;
	}
	.mhs-hero-meta span {
		display: inline-flex;
		align-items: center;
		gap: 5px;
	}
	/* Stat Cards */
	.mhs-stat {
		border-radius: 1.1rem;
		border: none;
		padding: 1.5rem 1.75rem;
		position: relative;
		overflow: hidden;
		transition: transform 0.22s cubic-bezier(.4,0,.2,1), box-shadow 0.22s;
		cursor: default;
	}
	.mhs-stat:hover {
		transform: translateY(-4px);
		box-shadow: 0 12px 28px rgba(0,0,0,0.13) !important;
	}
	.mhs-stat-icon {
		width: 52px; height: 52px;
		border-radius: 0.85rem;
		display: flex; align-items: center; justify-content: center;
		font-size: 1.5rem;
		margin-bottom: 1rem;
		background: rgba(255,255,255,0.2);
		color: #fff;
	}
	.mhs-stat-value {
		font-size: 2.2rem;
		font-weight: 800;
		line-height: 1;
		color: #fff;
		margin-bottom: 0.3rem;
	}
	.mhs-stat-label {
		font-size: 0.875rem;
		font-weight: 500;
		color: rgba(255,255,255,0.75);
	}
	.mhs-stat-bg {
		position: absolute;
		bottom: -12px; right: -10px;
		font-size: 5.5rem;
		opacity: 0.07;
		line-height: 1;
		color: #fff;
	}
	.stat-emerald { background: linear-gradient(135deg, #059669 0%, #047857 100%); }
	.stat-sky     { background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); }
	.stat-violet  { background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%); }

	/* Quick links */
	.mhs-ql {
		border-radius: 1rem;
		border: 1.5px solid #e8edf8;
		background: #fff;
		padding: 1.1rem 1.25rem;
		display: flex;
		align-items: center;
		gap: 1rem;
		text-decoration: none;
		color: #1e293b;
		transition: all 0.22s cubic-bezier(.4,0,.2,1);
		box-shadow: 0 1px 4px rgba(5,150,105,0.05);
	}
	.mhs-ql:hover {
		border-color: #059669;
		background: #f0fdf4;
		color: #047857;
		text-decoration: none;
		transform: translateY(-2px);
		box-shadow: 0 6px 18px rgba(5,150,105,0.12);
	}
	.mhs-ql-icon {
		width: 42px; height: 42px;
		border-radius: 0.7rem;
		display: flex; align-items: center; justify-content: center;
		font-size: 1.25rem;
		flex-shrink: 0;
		background: #ecfdf5;
		color: #059669;
		transition: background 0.2s;
	}
	.mhs-ql:hover .mhs-ql-icon { background: #d1fae5; }
	.mhs-ql-label { font-size: 0.9rem; font-weight: 600; line-height: 1.3; }
	.mhs-ql-desc  { font-size: 0.78rem; color: #64748b; margin-top: 1px; }

	/* Section heading */
	.mhs-section-heading {
		font-size: 1rem;
		font-weight: 700;
		color: #1e293b;
		margin-bottom: 1rem;
		display: flex;
		align-items: center;
		gap: 8px;
	}
	.mhs-section-heading::after {
		content: "";
		flex: 1;
		height: 1px;
		background: linear-gradient(90deg, #e2e8f0 0%, transparent 100%);
		margin-left: 4px;
	}

	/* Nilai card */
	.nilai-card {
		background: #fff;
		border: 1.5px solid #e8edf8;
		border-radius: 1.1rem;
		overflow: hidden;
		box-shadow: 0 1px 4px rgba(0,0,0,0.04);
	}
	.nilai-card-header {
		display: flex;
		align-items: center;
		justify-content: space-between;
		padding: 1rem 1.5rem;
		border-bottom: 1px solid #f1f5f9;
		background: #f8fafc;
	}
	.nilai-card-title {
		display: flex;
		align-items: center;
		gap: 0.5rem;
		font-size: 0.875rem;
		font-weight: 700;
		color: #1e293b;
	}
	.course-name { font-size: 0.9rem; font-weight: 600; color: #1e293b; line-height: 1.3; }
	.course-info { display: flex; flex-wrap: wrap; gap: 0.25rem; margin-top: 0.3rem; }
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
		width: 2rem; height: 2rem;
		border-radius: 0.375rem;
		font-size: 0.78rem;
		font-weight: 700;
	}
	.badge-status { font-size: 0.72rem; padding: 0.3rem 0.55rem; }

	/* Empty state */
	.empty-state {
		text-align: center;
		padding: 3.5rem 1.5rem;
		color: #94a3b8;
	}
	.empty-state i { font-size: 3rem; margin-bottom: 0.75rem; display: block; }
	.empty-state p { font-size: 0.875rem; font-weight: 600; margin: 0; color: #64748b; }
	.empty-state small { font-size: 0.78rem; color: #94a3b8; }
</style>

<!-- Hero Banner -->
<div class="mhs-hero shadow-sm">
	<div class="mhs-hero-badge">
		<i class="bi bi-mortarboard-fill"></i> Portal Mahasiswa OBE TI UPR
	</div>
	<div class="mhs-hero-title">Selamat Datang, <?= esc($mahasiswa['nama_lengkap']) ?>!</div>
	<div class="mhs-hero-meta">
		<span><i class="bi bi-building"></i> <?= esc(ucwords(strtolower($programStudi))) ?></span>
		<span><i class="bi bi-calendar3"></i> Angkatan <?= esc($mahasiswa['tahun_angkatan']) ?></span>
		<span><i class="bi bi-person-badge"></i> <?= esc(session('nim')) ?></span>
	</div>
</div>

<!-- Stat Cards -->
<div class="row g-3 mb-4">
	<div class="col-sm-6 col-lg-4">
		<div class="mhs-stat stat-emerald shadow-sm h-100">
			<div class="mhs-stat-icon"><i class="bi bi-book-fill"></i></div>
			<div class="mhs-stat-value"><?= $totalNilai ?></div>
			<div class="mhs-stat-label">Total Mata Kuliah</div>
			<i class="bi bi-book-fill mhs-stat-bg"></i>
		</div>
	</div>
	<div class="col-sm-6 col-lg-4">
		<div class="mhs-stat stat-sky shadow-sm h-100">
			<div class="mhs-stat-icon"><i class="bi bi-check-circle-fill"></i></div>
			<div class="mhs-stat-value"><?= $nilaiLulus ?></div>
			<div class="mhs-stat-label">Mata Kuliah Lulus</div>
			<i class="bi bi-check-circle-fill mhs-stat-bg"></i>
		</div>
	</div>
	<div class="col-sm-6 col-lg-4">
		<div class="mhs-stat stat-violet shadow-sm h-100">
			<div class="mhs-stat-icon"><i class="bi bi-person-badge-fill"></i></div>
			<div style="font-size:1.3rem; font-weight:800; color:#fff; line-height:1; margin-bottom:0.3rem;">
				<?= esc($mahasiswa['status_mahasiswa']) ?>
			</div>
			<div class="mhs-stat-label">Status Mahasiswa</div>
			<i class="bi bi-person-badge-fill mhs-stat-bg"></i>
		</div>
	</div>
</div>

<!-- Quick Links -->
<div class="mhs-section-heading"><i class="bi bi-lightning-charge-fill text-warning"></i> Menu Utama</div>
<div class="row g-3 mb-4">
	<div class="col-sm-6 col-lg-4">
		<a href="<?= base_url('mahasiswa/nilai') ?>" class="mhs-ql">
			<div class="mhs-ql-icon"><i class="bi bi-file-earmark-bar-graph"></i></div>
			<div>
				<div class="mhs-ql-label">Nilai Saya</div>
				<div class="mhs-ql-desc">Lihat seluruh nilai mata kuliah</div>
			</div>
		</a>
	</div>
	<div class="col-sm-6 col-lg-4">
		<a href="<?= base_url('mahasiswa/profil-cpl') ?>" class="mhs-ql">
			<div class="mhs-ql-icon"><i class="bi bi-graph-up"></i></div>
			<div>
				<div class="mhs-ql-label">Profil CPL</div>
				<div class="mhs-ql-desc">Capaian Pembelajaran Lulusan Anda</div>
			</div>
		</a>
	</div>
	<div class="col-sm-6 col-lg-4">
		<a href="<?= base_url('mahasiswa/laporan-cpmk') ?>" class="mhs-ql">
			<div class="mhs-ql-icon"><i class="bi bi-file-earmark-text"></i></div>
			<div>
				<div class="mhs-ql-label">Laporan CPMK</div>
				<div class="mhs-ql-desc">Laporan capaian CPMK per MK</div>
			</div>
		</a>
	</div>
	<div class="col-sm-6 col-lg-4">
		<a href="<?= base_url('mahasiswa/laporan-cpl') ?>" class="mhs-ql">
			<div class="mhs-ql-icon"><i class="bi bi-clipboard2-data"></i></div>
			<div>
				<div class="mhs-ql-label">Laporan CPL</div>
				<div class="mhs-ql-desc">Laporan capaian CPL program studi</div>
			</div>
		</a>
	</div>
	<div class="col-sm-6 col-lg-4">
		<a href="<?= base_url('mahasiswa/profil') ?>" class="mhs-ql">
			<div class="mhs-ql-icon"><i class="bi bi-person-circle"></i></div>
			<div>
				<div class="mhs-ql-label">Profil Saya</div>
				<div class="mhs-ql-desc">Data pribadi dan informasi akun</div>
			</div>
		</a>
	</div>
</div>

<!-- Recent Grades -->
<div class="mhs-section-heading"><i class="bi bi-clock-history text-primary"></i> Nilai Terbaru</div>
<div class="nilai-card">
	<div class="nilai-card-header">
		<div class="nilai-card-title">
			<i class="bi bi-journal-check text-success"></i> Riwayat Nilai Terakhir
		</div>
		<a href="<?= base_url('mahasiswa/nilai') ?>" class="btn btn-sm btn-outline-success" style="border-radius:0.6rem; font-size:0.8rem; font-weight:600;">
			Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
		</a>
	</div>

	<?php if (empty($recentNilai)): ?>
		<div class="empty-state">
			<i class="bi bi-inbox"></i>
			<p>Belum ada data nilai</p>
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
		const tableWrapper = document.querySelector('.modern-table-wrapper');
		if (tableWrapper) {
			function checkScroll() {
				const hasHorizontalScroll = tableWrapper.scrollWidth > tableWrapper.clientWidth;
				const isScrolledToEnd = tableWrapper.scrollLeft >= (tableWrapper.scrollWidth - tableWrapper.clientWidth - 10);
				tableWrapper.classList.toggle('has-scroll', hasHorizontalScroll && !isScrolledToEnd);
			}
			checkScroll();
			window.addEventListener('resize', checkScroll);
			tableWrapper.addEventListener('scroll', checkScroll);
		}

		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
		tooltipTriggerList.map(function(el) { return new bootstrap.Tooltip(el); });
	});
</script>
<?= $this->endSection() ?>

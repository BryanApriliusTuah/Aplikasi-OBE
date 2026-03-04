<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<style>
	.info-card {
		background: #fff;
		border: 1px solid #e5e7eb;
		border-radius: 0.75rem;
		padding: 1.5rem;
		margin-bottom: 1.5rem;
		box-shadow: 0 1px 3px rgba(0,0,0,0.05);
	}

	.info-card-header {
		display: flex;
		align-items: center;
		gap: 0.5rem;
		font-size: 0.85rem;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.05em;
		color: #475569;
		margin-bottom: 1rem;
		padding-bottom: 0.75rem;
		border-bottom: 1px solid #f1f5f9;
	}

	.info-grid {
		display: grid;
		grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
		gap: 1rem;
	}

	.info-item label {
		display: block;
		font-size: 0.72rem;
		font-weight: 500;
		text-transform: uppercase;
		letter-spacing: 0.05em;
		color: #94a3b8;
		margin-bottom: 0.2rem;
	}

	.info-item span {
		font-size: 0.9rem;
		font-weight: 600;
		color: #1e293b;
	}

	.stat-card {
		background: #f8fafc;
		border: 1px solid #e5e7eb;
		border-radius: 0.75rem;
		padding: 1.25rem;
		text-align: center;
		flex: 1;
	}

	.stat-card .stat-label {
		font-size: 0.72rem;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.05em;
		color: #94a3b8;
		margin-bottom: 0.5rem;
	}

	.stat-card .stat-value {
		font-size: 2rem;
		font-weight: 700;
		color: #1e293b;
		line-height: 1;
	}

	.grade-pill {
		display: inline-flex;
		align-items: center;
		justify-content: center;
		width: 3rem;
		height: 3rem;
		border-radius: 0.5rem;
		font-size: 1.25rem;
		font-weight: 700;
	}

	.progress-bar-thin {
		height: 6px;
		border-radius: 3px;
		background: #e5e7eb;
		overflow: hidden;
		margin-top: 0.4rem;
	}

	.progress-bar-thin .fill {
		height: 100%;
		border-radius: 3px;
		transition: width 0.4s ease;
	}

	.cpmk-badge {
		display: inline-flex;
		align-items: center;
		gap: 0.25rem;
		font-size: 0.72rem;
		font-weight: 600;
		padding: 0.25rem 0.6rem;
		border-radius: 0.375rem;
		background: #f0f4ff;
		color: #3b5bdb;
		border: 1px solid #c5d0fa;
		white-space: nowrap;
	}

	.legend-item {
		display: inline-flex;
		align-items: center;
		gap: 0.4rem;
		font-size: 0.8rem;
		color: #64748b;
	}

	.legend-dot {
		width: 0.65rem;
		height: 0.65rem;
		border-radius: 50%;
	}
</style>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
	<div>
		<h2 class="mb-1 fw-bold">Detail Nilai</h2>
		<p class="text-muted mb-0">
			<span class="me-1"><?= esc($nilai['kode_mk']) ?></span>
			<span class="text-muted">—</span>
			<span class="ms-1"><?= esc($nilai['nama_mk']) ?></span>
		</p>
	</div>
	<a href="<?= base_url('mahasiswa/nilai') ?>" class="btn btn-outline-secondary">
		<i class="bi bi-arrow-left"></i> Kembali
	</a>
</div>

<!-- Course Info -->
<div class="info-card">
	<div class="info-card-header">
		<i class="bi bi-journal-bookmark-fill text-primary"></i>
		Informasi Mata Kuliah
	</div>
	<div class="info-grid">
		<div class="info-item">
			<label>Kode MK</label>
			<span><?= esc($nilai['kode_mk']) ?></span>
		</div>
		<div class="info-item">
			<label>Nama MK</label>
			<span><?= esc($nilai['nama_mk']) ?></span>
		</div>
		<div class="info-item">
			<label>SKS</label>
			<span><?= esc($nilai['sks']) ?> SKS</span>
		</div>
		<div class="info-item">
			<label>Kelas</label>
			<span>Kelas <?= esc($nilai['kelas']) ?></span>
		</div>
		<div class="info-item">
			<label>Semester</label>
			<span><?= esc($nilai['tahun_akademik']) ?></span>
		</div>
	</div>
</div>

<!-- Final Grade Stats -->
<div class="info-card">
	<div class="info-card-header">
		<i class="bi bi-award-fill text-warning"></i>
		Nilai Akhir
	</div>
	<div class="d-flex gap-3 flex-wrap">
		<!-- Nilai Akhir -->
		<div class="stat-card">
			<div class="stat-label">Nilai Akhir</div>
			<div class="stat-value">
				<?= $nilai['nilai_akhir'] ? number_format($nilai['nilai_akhir'], 2) : '<span class="text-muted" style="font-size:1.5rem;">—</span>' ?>
			</div>
		</div>

		<!-- Grade -->
		<div class="stat-card">
			<div class="stat-label">Grade</div>
			<?php if ($nilai['nilai_huruf']): ?>
				<?php
				$gradeClass = 'bg-primary text-white';
				if (in_array($nilai['nilai_huruf'], ['A', 'A-'])) $gradeClass = 'bg-success text-white';
				elseif (in_array($nilai['nilai_huruf'], ['B+', 'B', 'B-'])) $gradeClass = 'bg-info text-white';
				elseif (in_array($nilai['nilai_huruf'], ['C+', 'C'])) $gradeClass = 'bg-warning text-dark';
				elseif (in_array($nilai['nilai_huruf'], ['D', 'E'])) $gradeClass = 'bg-danger text-white';
				?>
				<div class="d-flex justify-content-center">
					<span class="grade-pill <?= $gradeClass ?>"><?= esc($nilai['nilai_huruf']) ?></span>
				</div>
			<?php else: ?>
				<div class="stat-value text-muted">—</div>
			<?php endif; ?>
		</div>

		<!-- Status -->
		<div class="stat-card">
			<div class="stat-label">Status</div>
			<?php if ($nilai['status_kelulusan'] == 'Lulus'): ?>
				<div class="d-flex flex-column align-items-center gap-1">
					<i class="bi bi-check-circle-fill text-success" style="font-size: 1.75rem;"></i>
					<span class="badge bg-success" style="font-size: 0.75rem;">Lulus</span>
				</div>
			<?php elseif ($nilai['status_kelulusan'] == 'Tidak Lulus'): ?>
				<div class="d-flex flex-column align-items-center gap-1">
					<i class="bi bi-x-circle-fill text-danger" style="font-size: 1.75rem;"></i>
					<span class="badge bg-danger" style="font-size: 0.75rem;">Tidak Lulus</span>
				</div>
			<?php else: ?>
				<div class="d-flex flex-column align-items-center gap-1">
					<i class="bi bi-hourglass-split text-warning" style="font-size: 1.75rem;"></i>
					<span class="badge bg-warning text-dark" style="font-size: 0.75rem;">Diproses</span>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<!-- Nilai CPMK Table -->
<div class="info-card" style="padding: 0; overflow: hidden;">
	<div class="info-card-header" style="margin: 0; padding: 1rem 1.5rem; border-radius: 0.75rem 0.75rem 0 0; border-bottom: 1px solid #e5e7eb;">
		<i class="bi bi-graph-up text-success"></i>
		Nilai CPMK
	</div>

	<?php if (empty($nilaiCpmk)): ?>
		<div class="text-center py-5 text-muted">
			<i class="bi bi-inbox" style="font-size: 3rem;"></i>
			<p class="mt-3 mb-0 fw-semibold">Belum ada data nilai CPMK</p>
			<small>Data akan muncul setelah nilai diinput oleh dosen</small>
		</div>
	<?php else: ?>
		<div class="modern-table-wrapper" style="border: none; border-radius: 0;">
			<div class="scroll-indicator"></div>
			<table class="modern-table" id="cpmkTable">
				<thead>
					<tr>
						<th class="text-center" style="width: 50px;">No</th>
						<th class="text-center" style="width: 120px;">Kode CPMK</th>
						<th style="min-width: 280px;">Deskripsi</th>
						<th class="text-center" style="width: 120px;">Nilai CPMK</th>
						<th class="text-center" style="width: 160px;">Capaian</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($nilaiCpmk as $index => $cpmk):
						$bobotCpmk = 0;
						foreach ($teknik_list as $teknik) {
							if ($teknik['cpmk_id'] == $cpmk['cpmk_id']) {
								$bobotCpmk += $teknik['bobot'];
							}
						}
						$capaianPersen = $bobotCpmk > 0 ? ($cpmk['nilai_cpmk'] / $bobotCpmk) * 100 : 0;
						$capaianPersen = min($capaianPersen, 100);
						$isLulus = $capaianPersen >= 60;
						$progressColor = $isLulus ? '#22c55e' : '#ef4444';
					?>
						<tr>
							<td class="text-center fw-semibold text-muted"><?= $index + 1 ?></td>
							<td class="text-center align-middle">
								<span class="cpmk-badge">
									<i class="bi bi-bookmark-fill"></i>
									<?= esc($cpmk['kode_cpmk']) ?>
								</span>
							</td>
							<td class="align-middle">
								<small class="text-dark"><?= esc($cpmk['deskripsi']) ?></small>
							</td>
							<td class="text-center align-middle fw-bold">
								<?= number_format($cpmk['nilai_cpmk'], 2) ?>
							</td>
							<td class="text-center align-middle">
								<div class="d-flex flex-column align-items-center gap-1">
									<span class="fw-bold" style="color: <?= $progressColor ?>; font-size: 0.9rem;">
										<?= number_format($capaianPersen, 1) ?>%
									</span>
									<div class="progress-bar-thin" style="width: 80px;">
										<div class="fill" style="width: <?= $capaianPersen ?>%; background: <?= $progressColor ?>;"></div>
									</div>
									<span class="badge <?= $isLulus ? 'bg-success' : 'bg-danger' ?>" style="font-size: 0.65rem; padding: 0.2rem 0.45rem;">
										<?= $isLulus ? 'Tercapai' : 'Belum' ?>
									</span>
								</div>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<!-- Legend -->
		<div class="px-4 py-3" style="border-top: 1px solid #f1f5f9; background: #f8fafc;">
			<div class="d-flex align-items-center gap-4 flex-wrap">
				<small class="fw-semibold text-muted text-uppercase" style="letter-spacing: 0.05em; font-size: 0.7rem;">Keterangan:</small>
				<span class="legend-item">
					<span class="legend-dot" style="background: #22c55e;"></span>
					Tercapai (≥ 60%)
				</span>
				<span class="legend-item">
					<span class="legend-dot" style="background: #ef4444;"></span>
					Belum Tercapai (&lt; 60%)
				</span>
			</div>
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

		// Initialize Bootstrap tooltips
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
		tooltipTriggerList.map(function(el) {
			return new bootstrap.Tooltip(el);
		});
	});
</script>
<?= $this->endSection() ?>

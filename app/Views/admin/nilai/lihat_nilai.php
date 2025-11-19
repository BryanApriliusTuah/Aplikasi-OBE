<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4" style="overflow-x: hidden;">
	<div class="row mb-4">
		<div class="col-12">
			<nav aria-label="breadcrumb" class="mb-3">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">
						<a href="<?= base_url('admin/nilai') ?>" class="text-decoration-none">
							<i class="bi bi-house-door me-1"></i>Penilaian
						</a>
					</li>
					<li class="breadcrumb-item active">Lihat Nilai</li>
				</ol>
			</nav>

			<div class="d-flex justify-content-between align-items-center">
				<div>
					<h2 class="fw-bold mb-1">Lihat Nilai (Read-Only)</h2>
					<p class="text-muted mb-0">Menampilkan nilai untuk setiap teknik penilaian (Kehadiran, Tugas, UTS, UAS, dll)</p>
					<?php if (isset($jadwal['is_nilai_validated']) && $jadwal['is_nilai_validated'] == 1): ?>
						<div class="alert alert-success alert-sm mt-2 mb-0 py-2">
							<i class="bi bi-check-circle-fill me-1"></i>
							<strong>Nilai telah divalidasi</strong>
							<?php if (isset($jadwal['validated_by_name'])): ?>
								oleh <strong><?= esc($jadwal['validated_by_name']) ?></strong>
							<?php endif; ?>
							pada <?= date('d/m/Y H:i', strtotime($jadwal['validated_at'])) ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="d-flex gap-2">
					<a href="<?= base_url('admin/nilai/unduh-dpna/' . $jadwal['id']) ?>"
						class="btn btn-success"
						target="_blank"
						title="Unduh Daftar Penilaian Nilai Akhir">
						<i class="bi bi-download me-2"></i>Unduh DPNA
					</a>
					<a href="<?= base_url('admin/nilai') ?>" class="btn btn-outline-secondary">
						<i class="bi bi-arrow-left me-2"></i>Kembali
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="card border-0 shadow-sm mb-4">
		<div class="card-body bg-light">
			<div class="row g-3">
				<div class="col-md-4">
					<div class="d-flex align-items-center">
						<div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
							<i class="bi bi-book text-primary fs-5"></i>
						</div>
						<div>
							<small class="text-muted">Mata Kuliah</small>
							<h6 class="mb-0 fw-semibold"><?= esc($jadwal['nama_mk']) ?></h6>
						</div>
					</div>
				</div>
				<div class="col-md-2">
					<div class="d-flex align-items-center">
						<div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
							<i class="bi bi-people text-success fs-5"></i>
						</div>
						<div>
							<small class="text-muted">Kelas</small>
							<h6 class="mb-0 fw-semibold"><?= esc($jadwal['kelas']) ?></h6>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="d-flex align-items-center">
						<div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
							<i class="bi bi-calendar text-info fs-5"></i>
						</div>
						<div>
							<small class="text-muted">Tahun Akademik</small>
							<h6 class="mb-0 fw-semibold"><?= esc($jadwal['tahun_akademik']) ?></h6>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="d-flex align-items-center">
						<div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
							<i class="bi bi-person-badge text-warning fs-5"></i>
						</div>
						<div>
							<small class="text-muted">Dosen Pengampu</small>
							<h6 class="mb-0 fw-semibold"><?= esc($jadwal['dosen_ketua'] ?? 'N/A') ?></h6>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Quick Info & RPS Link -->
	<?php if (!empty($teknik_by_tahap)): ?>
		<div class="card border-0 shadow-sm mb-4">
			<div class="card-body">
				<div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
					<div class="d-flex align-items-center flex-grow-1">
						<div class="bg-info bg-opacity-10 rounded-circle p-2 me-3 flex-shrink-0">
							<i class="bi bi-lightbulb-fill text-info fs-5"></i>
						</div>
						<div>
							<h6 class="fw-bold mb-1">Penilaian Otomatis (Combined Mode)</h6>
							<small class="text-muted">
								Sistem menghitung <strong>Nilai CPMK = Σ(Bobot × Nilai)</strong> dari <?= count($combined_list) ?> teknik penilaian (digabung) dalam <?= count($teknik_by_tahap) ?> tahap
							</small>
						</div>
					</div>
					<div class="d-flex gap-2 flex-shrink-0">
						<?php
						// Get RPS ID from the first combined teknik item
						$db = \Config\Database::connect();
						$first_rps_mingguan_id = $combined_list[0]['rps_mingguan_ids'][0]['rps_mingguan_id'] ?? null;
						$rps_id = null;
						if ($first_rps_mingguan_id) {
							$first_rps_mingguan = $db->table('rps_mingguan')
								->select('rps_id')
								->where('id', $first_rps_mingguan_id)
								->get()
								->getRowArray();
							$rps_id = $first_rps_mingguan['rps_id'] ?? null;
						}
						?>
						<?php if ($rps_id): ?>
							<a href="<?= base_url('rps/preview/' . $rps_id) ?>"
								class="btn btn-sm btn-outline-primary"
								target="_blank"
								title="Lihat RPS">
								<i class="bi bi-file-text"></i>
								<span class="d-none d-lg-inline ms-1">RPS</span>
							</a>
							<a href="<?= base_url('rps/mingguan/' . $rps_id) ?>"
								class="btn btn-sm btn-outline-secondary"
								target="_blank"
								title="Kelola RPS Mingguan">
								<i class="bi bi-calendar-week"></i>
								<span class="d-none d-lg-inline ms-1">Mingguan</span>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div class="card border-0 shadow-sm" style="overflow: hidden;">
		<div class="card-header bg-info text-white py-3">
			<div class="d-flex justify-content-between align-items-center">
				<div>
					<h5 class="mb-0"><i class="bi bi-eye me-2"></i>Tampilan Nilai Teknik Penilaian (Read-Only)</h5>
					<small class="opacity-75">Nilai ini hanya dapat dilihat, tidak dapat diubah</small>
				</div>
				<?php if (!empty($mahasiswa_list) && !empty($combined_list)): ?>
					<div class="text-end">
						<small class="opacity-75">
							<?= count($mahasiswa_list) ?> Mahasiswa | <?= count($combined_list) ?> Teknik Penilaian (Combined)
						</small>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="card-body p-0" style="overflow: hidden;">
			<?php if (empty($mahasiswa_list) || empty($combined_list)): ?>
				<div class="text-center py-5">
					<div class="mb-4">
						<i class="bi bi-exclamation-triangle display-1 text-warning opacity-25"></i>
					</div>
					<h5 class="text-muted">Data Tidak Tersedia</h5>
					<p class="text-muted mb-4">
						Tidak ditemukan data mahasiswa atau teknik penilaian untuk mata kuliah ini.<br>
						Pastikan RPS Mingguan sudah dilengkapi dengan teknik penilaian dan bobotnya.
					</p>
					<a href="<?= base_url('admin/nilai') ?>" class="btn btn-primary">
						<i class="bi bi-arrow-left me-2"></i>Kembali ke Jadwal
					</a>
				</div>
			<?php else: ?>
				<div class="table-responsive" style="max-height: 70vh; overflow: auto;">
					<table class="table table-bordered mb-0" id="nilaiTable">
						<thead>
							<tr>
								<th rowspan="2" style="width: 50px;">No</th>
								<th rowspan="2" style="width: 120px;">NIM</th>
								<th rowspan="2" style="width: 250px;">Nama</th>
								<?php foreach ($combined_list as $item): ?>
									<th rowspan="2" style="width: 80px;">
										<?php
										// Simplify label by removing text in parentheses
										$label = $item['teknik_label'];
										$simplified_label = preg_replace('/\s*\([^)]*\)/', '', $label);
										echo esc($simplified_label);
										?>
										<?php if ($item['total_bobot'] > 0): ?>
											<br><small style="font-weight: normal; font-size: 0.85em;">(<?= number_format($item['total_bobot'], 1) ?>%)</small>
										<?php endif; ?>
									</th>
								<?php endforeach; ?>
								<th colspan="2" style="width: 150px;">Nilai Akhir</th>
								<th rowspan="2" style="width: 100px;">Keterangan</th>
							</tr>
							<tr>
								<th style="width: 80px;">Angka</th>
								<th style="width: 70px;">Huruf</th>
							</tr>
						</thead>
						<tbody>
							<?php
							// Helper function to calculate keterangan based on grade
							$getKeterangan = function ($grade) {
								$failingGrades = ['B', 'BC', 'C', 'D', 'E'];
								if (in_array(strtoupper($grade), $failingGrades)) {
									return 'TM'; // Tidak Memenuhi
								}
								return 'Lulus';
							};
							?>
							<?php foreach ($mahasiswa_list as $index => $mahasiswa) : ?>
								<?php
								// Get final scores for this student
								$nilai_akhir = $final_scores_map[$mahasiswa['id']]['nilai_akhir'] ?? 0;
								$nilai_huruf = $final_scores_map[$mahasiswa['id']]['nilai_huruf'] ?? '-';
								$keterangan = $getKeterangan($nilai_huruf);

								// Determine background class based on grade
								$grade_class = '';
								switch (strtoupper($nilai_huruf)) {
									case 'A': $grade_class = 'grade-a'; break;
									case 'AB': $grade_class = 'grade-ab'; break;
									case 'B': $grade_class = 'grade-b'; break;
									case 'BC': $grade_class = 'grade-bc'; break;
									case 'C': $grade_class = 'grade-c'; break;
									case 'D': $grade_class = 'grade-d'; break;
									case 'E': $grade_class = 'grade-e'; break;
								}
								?>
								<tr>
									<td><?= $index + 1 ?></td>
									<td><?= esc($mahasiswa['nim']) ?></td>
									<td class="left"><?= esc($mahasiswa['nama_lengkap']) ?></td>
									<?php foreach ($combined_list as $item): ?>
										<td>
											<?php
											$score = $existing_scores[$mahasiswa['id']][$item['teknik_key']] ?? '';
											echo $score !== '' ? number_format($score, 2) : '-';
											?>
										</td>
									<?php endforeach; ?>
									<td class="<?= $grade_class ?>">
										<?= number_format($nilai_akhir, 2) ?>
									</td>
									<td class="<?= $grade_class ?>">
										<strong><?= esc($nilai_huruf) ?></strong>
									</td>
									<td class="<?= $grade_class ?>">
										<?= esc($keterangan) ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>

				<div class="card-footer bg-light border-0 py-3">
					<div class="row align-items-center">
						<div class="col-12">
							<div class="d-flex align-items-center gap-3 justify-content-center">
								<small class="text-muted">
									<i class="bi bi-info-circle me-1"></i>
									Total: <?= count($mahasiswa_list) ?> mahasiswa dengan <?= count($combined_list) ?> teknik penilaian (combined mode)
								</small>
								<small class="text-muted">
									<i class="bi bi-lock me-1"></i>
									Mode tampilan saja (Read-Only)
								</small>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
	.table-responsive {
		border: 1px solid #dee2e6;
		overflow-x: auto;
		overflow-y: auto;
		-webkit-overflow-scrolling: touch;
		box-shadow: 0 2px 4px rgba(0,0,0,0.1);
	}

	#nilaiTable {
		width: 100%;
		border-collapse: collapse;
		background-color: #fff;
	}

	/* Table header styling */
	#nilaiTable thead th {
		background-color: #2c3e50;
		color: white;
		padding: 12px;
		text-align: center;
		font-weight: bold;
		border: 1px solid #34495e;
		vertical-align: middle;
		position: sticky;
		top: 0;
		z-index: 10;
	}

	/* Table cell styling */
	#nilaiTable tbody td {
		padding: 10px;
		border: 1px solid #ddd;
		text-align: center;
		vertical-align: middle;
	}

	#nilaiTable tbody td.left {
		text-align: left;
	}

	/* Zebra striping for rows */
	#nilaiTable tbody tr:nth-child(even) {
		background-color: #f9f9f9;
	}

	#nilaiTable tbody tr:hover {
		background-color: #f0f0f0;
	}

	/* Grade color coding - consistent with DPNA */
	.grade-a {
		background-color: #d4edda !important;
		font-weight: bold;
	}
	.grade-ab {
		background-color: #d1ecf1 !important;
	}
	.grade-b {
		background-color: #fff3cd !important;
	}
	.grade-bc {
		background-color: #ffe0b2 !important;
	}
	.grade-c {
		background-color: #f8d7da !important;
	}
	.grade-d {
		background-color: #f5c6cb !important;
		font-weight: bold;
	}
	.grade-e {
		background-color: #f8d7da !important;
		font-weight: bold;
		color: #721c24;
	}

	/* Ensure body doesn't scroll horizontally */
	body {
		overflow-x: hidden;
	}

	@media (max-width: 768px) {
		.table-responsive {
			max-height: 60vh;
		}
	}

	@media print {
		.table-responsive {
			overflow: visible !important;
			max-height: none !important;
			box-shadow: none;
			border: 1px solid #ddd;
		}

		#nilaiTable thead th {
			position: static;
		}
	}
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<?= $this->endSection() ?>

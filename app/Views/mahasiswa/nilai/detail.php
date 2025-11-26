<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
	<div>
		<h2 class="mb-1">Detail Nilai</h2>
		<p class="text-muted"><?= esc($nilai['kode_mk']) ?> - <?= esc($nilai['nama_mk']) ?></p>
	</div>
	<a href="<?= base_url('mahasiswa/nilai') ?>" class="btn btn-outline-secondary">
		<i class="bi bi-arrow-left"></i> Kembali
	</a>
</div>

<!-- Course Information Card -->
<div class="card mb-4">
	<div class="card-header">
		<h5 class="mb-0">Informasi Mata Kuliah</h5>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-md-6">
				<table class="table table-borderless mb-0">
					<tr>
						<td class="text-muted" style="width: 40%;">Kode MK</td>
						<td><strong><?= esc($nilai['kode_mk']) ?></strong></td>
					</tr>
					<tr>
						<td class="text-muted">Nama MK</td>
						<td><strong><?= esc($nilai['nama_mk']) ?></strong></td>
					</tr>
					<tr>
						<td class="text-muted">SKS</td>
						<td><?= esc($nilai['sks']) ?></td>
					</tr>
					<tr>
						<td class="text-muted">Semester</td>
						<td><?= esc($nilai['semester']) ?></td>
					</tr>
				</table>
			</div>
			<div class="col-md-6">
				<table class="table table-borderless mb-0">
					<tr>
						<td class="text-muted" style="width: 40%;">Tahun Akademik</td>
						<td><?= esc($nilai['tahun_akademik']) ?></td>
					</tr>
					<tr>
						<td class="text-muted">Kelas</td>
						<td><?= esc($nilai['kelas']) ?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
</div>

<!-- Final Grade Card -->
<div class="card mb-4">
	<div class="card-header">
		<h5 class="mb-0">Nilai Akhir</h5>
	</div>
	<div class="card-body">
		<div class="row text-center">
			<div class="col-md-4">
				<div class="border rounded p-3">
					<small class="text-muted d-block mb-1">Nilai Akhir</small>
					<h3 class="mb-0">
						<?= $nilai['nilai_akhir'] ? number_format($nilai['nilai_akhir'], 2) : '-' ?>
					</h3>
				</div>
			</div>
			<div class="col-md-4">
				<div class="border rounded p-3">
					<small class="text-muted d-block mb-1">Grade</small>
					<?php if ($nilai['nilai_huruf']): ?>
						<?php
						$gradeClass = 'bg-primary';
						if (in_array($nilai['nilai_huruf'], ['A', 'A-'])) $gradeClass = 'bg-success';
						elseif (in_array($nilai['nilai_huruf'], ['B+', 'B', 'B-'])) $gradeClass = 'bg-info';
						elseif (in_array($nilai['nilai_huruf'], ['C+', 'C'])) $gradeClass = 'bg-warning';
						elseif (in_array($nilai['nilai_huruf'], ['D', 'E'])) $gradeClass = 'bg-danger';
						?>
						<h3 class="mb-0">
							<span class="badge <?= $gradeClass ?> fs-4"><?= esc($nilai['nilai_huruf']) ?></span>
						</h3>
					<?php else: ?>
						<h3 class="mb-0 text-muted">-</h3>
					<?php endif; ?>
				</div>
			</div>
			<div class="col-md-4">
				<div class="border rounded p-3">
					<small class="text-muted d-block mb-1">Status</small>
					<?php if ($nilai['status_kelulusan'] == 'Lulus'): ?>
						<span class="badge bg-success fs-6"><i class="bi bi-check-circle"></i>
						</span>
					<?php elseif ($nilai['status_kelulusan'] == 'Tidak Lulus'): ?>
						<span class="badge bg-danger fs-6"><i class="bi bi-x-circle"></i> Tidak Lulus</span>
					<?php else: ?>
						<span class="badge bg-warning fs-6"><i class="bi bi-hourglass-split"></i> Diproses</span>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Nilai CPMK Card -->
<div class="card">
	<div class="card-header">
		<h5 class="mb-0">Nilai CPMK</h5>
	</div>
	<div class="card-body">
		<?php if (empty($nilaiCpmk)): ?>
			<div class="text-center py-4 text-muted">
				<i class="bi bi-inbox" style="font-size: 3rem;"></i>
				<p class="mt-2 mb-0">Belum ada data nilai CPMK</p>
				<small>Data akan muncul setelah nilai diinput oleh dosen</small>
			</div>
		<?php else: ?>
			<div class="table-responsive">
				<table class="table table-bordered table-hover mb-0 align-middle">
					<thead class="table-light">
						<tr>
							<th class="text-center" style="width: 50px;">No</th>
							<th style="min-width: 120px;">Kode CPMK</th>
							<th style="min-width: 250px;">Deskripsi</th>
							<th class="text-center" style="width: 120px;">Nilai CPMK</th>
							<th class="text-center" style="width: 120px;">Capaian CPMK</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$totalNilaiCpmk = 0;
						$countCpmk = 0;
						foreach ($nilaiCpmk as $index => $cpmk):
							// Get bobot from teknik_list for this CPMK
							$bobotCpmk = 0;
							foreach ($teknik_list as $teknik) {
								if ($teknik['cpmk_id'] == $cpmk['cpmk_id']) {
									$bobotCpmk += $teknik['bobot'];
								}
							}

							// Calculate capaian percentage
							$capaianPersen = $bobotCpmk > 0 ? ($cpmk['nilai_cpmk'] / $bobotCpmk) * 100 : 0;

							if ($cpmk['nilai_cpmk'] > 0) {
								$totalNilaiCpmk += $cpmk['nilai_cpmk'];
								$countCpmk++;
							}

							// Determine badge color based on capaian
							$badgeClass = $capaianPersen >= 60 ? 'bg-success' : 'bg-danger';
						?>
							<tr>
								<td class="text-center"><?= $index + 1 ?></td>
								<td class="text-center">
									<strong class="text-primary"><?= esc($cpmk['kode_cpmk']) ?></strong>
								</td>
								<td>
									<small class="text-dark"><?= esc($cpmk['deskripsi']) ?></small>
								</td>
								<td class="text-center">
									<strong><?= number_format($cpmk['nilai_cpmk'], 2) ?></strong>
								</td>
								<td class="text-center">
									<span class="badge <?= $badgeClass ?> fs-6">
										<?= number_format($capaianPersen, 2) ?>%
									</span>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>

			<!-- Legend -->
			<div class="mt-3 p-3 bg-light rounded">
				<h6 class="mb-2"><i class="bi bi-info-circle"></i> Keterangan Capaian:</h6>
				<div class="d-flex flex-wrap gap-3">
					<div>
						<span class="badge bg-success">≥ 60%</span>
						<small class="text-muted ms-1"> . </small>
					</div>
					<div>
						<span class="badge bg-danger">
							< 60%</span>
								<small class="text-muted ms-1"> . </small>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>

<?= $this->endSection() ?>
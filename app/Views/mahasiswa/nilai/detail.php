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
						<span class="badge bg-success fs-6"><i class="bi bi-check-circle"></i> Lulus</span>
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

<!-- Teknik Penilaian Scores Card -->
<div class="card">
	<div class="card-header">
		<h5 class="mb-0">Nilai Teknik Penilaian</h5>
	</div>
	<div class="card-body">
		<?php if (empty($teknikPenilaianByTahap)): ?>
			<div class="text-center py-4 text-muted">
				<i class="bi bi-inbox" style="font-size: 3rem;"></i>
				<p class="mt-2 mb-0">Belum ada data teknik penilaian</p>
				<small>Data akan muncul setelah RPS mata kuliah dibuat</small>
			</div>
		<?php else: ?>
			<?php
			$totalNilai = 0;
			$totalBobot = 0;
			$totalWeighted = 0;

			// Define tahap order for display
			$tahapOrder = ['Perkuliahan', 'Tengah Semester', 'Akhir Semester'];
			?>

			<?php foreach ($tahapOrder as $tahapName): ?>
				<?php if (isset($teknikPenilaianByTahap[$tahapName]) && !empty($teknikPenilaianByTahap[$tahapName]['items'])): ?>
					<div class="mb-4">
						<h6 class="text-primary mb-3">
							<i class="bi bi-bookmark-fill"></i> <?= esc($tahapName) ?>
						</h6>
						<div class="table-responsive">
							<table class="table table-hover table-bordered">
								<thead class="table-light">
									<tr>
										<th>Teknik Penilaian</th>
										<th class="text-center" style="width: 100px;">Bobot (%)</th>
										<th class="text-center" style="width: 100px;">Nilai</th>
										<th class="text-center" style="width: 120px;">Nilai x Bobot</th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($teknikPenilaianByTahap[$tahapName]['items'] as $teknik): ?>
										<?php
										$teknikKey = $teknik['teknik_key'];
										$bobot = $teknik['total_bobot'];
										$nilaiTeknik = $mahasiswaScores[$teknikKey] ?? null;
										$weighted = ($nilaiTeknik !== null && $bobot > 0) ? ($nilaiTeknik * $bobot / 100) : 0;

										$totalBobot += $bobot;
										if ($nilaiTeknik !== null) {
											$totalNilai++;
											$totalWeighted += $weighted;
										}
										?>
										<tr>
											<td><?= esc($teknik['teknik_label']) ?></td>
											<td class="text-center"><?= number_format($bobot, 1) ?>%</td>
											<td class="text-center">
												<?php if ($nilaiTeknik !== null): ?>
													<strong><?= number_format($nilaiTeknik, 2) ?></strong>
												<?php else: ?>
													<span class="text-muted">-</span>
												<?php endif; ?>
											</td>
											<td class="text-center">
												<?php if ($nilaiTeknik !== null): ?>
													<?= number_format($weighted, 2) ?>
												<?php else: ?>
													<span class="text-muted">-</span>
												<?php endif; ?>
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</div>
					</div>
				<?php endif; ?>
			<?php endforeach; ?>

			<!-- Summary -->
			<div class="mt-3 p-3 bg-light rounded">
				<div class="row text-center">
					<div class="col-md-4">
						<small class="text-muted">Total Bobot</small>
						<div class="fw-bold"><?= number_format($totalBobot, 1) ?>%</div>
					</div>
					<div class="col-md-4">
						<small class="text-muted">Total Nilai x Bobot</small>
						<div class="fw-bold"><?= number_format($totalWeighted, 2) ?></div>
					</div>
					<div class="col-md-4">
						<small class="text-muted">Komponen Dinilai</small>
						<div class="fw-bold"><?= $totalNilai ?> komponen</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>

<?= $this->endSection() ?>

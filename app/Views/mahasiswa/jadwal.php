<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<div class="mb-4">
	<h2 class="mb-1">Jadwal Kuliah</h2>
	<p class="text-muted">Jadwal perkuliahan semester aktif</p>
</div>

<!-- Filter Section -->
<div class="card mb-4">
	<div class="card-body">
		<form method="get" class="row g-3">
			<div class="col-md-4">
				<label class="form-label">Tahun Akademik</label>
				<select class="form-select" name="tahun_akademik">
					<option value="">Semua Tahun Akademik</option>
					<option value="2025/2026 Ganjil" selected>2025/2026 Ganjil</option>
					<option value="2025/2026 Genap">2025/2026 Genap</option>
					<option value="2024/2025 Genap">2024/2025 Genap</option>
				</select>
			</div>
			<div class="col-md-4">
				<label class="form-label">Hari</label>
				<select class="form-select" name="hari">
					<option value="">Semua Hari</option>
					<option value="Senin">Senin</option>
					<option value="Selasa">Selasa</option>
					<option value="Rabu">Rabu</option>
					<option value="Kamis">Kamis</option>
					<option value="Jumat">Jumat</option>
					<option value="Sabtu">Sabtu</option>
				</select>
			</div>
			<div class="col-md-4">
				<label class="form-label">&nbsp;</label>
				<button type="submit" class="btn btn-primary w-100">
					<i class="bi bi-search"></i> Filter
				</button>
			</div>
		</form>
	</div>
</div>

<?php if (empty($jadwalList)): ?>
	<div class="card">
		<div class="card-body">
			<div class="text-center py-5 text-muted">
				<i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
				<p class="mt-3">Belum ada jadwal kuliah yang terdaftar</p>
				<small>Hubungi admin untuk mendaftarkan mata kuliah</small>
			</div>
		</div>
	</div>
<?php else: ?>
	<!-- Schedule by Day -->
	<?php
	$days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
	foreach ($days as $day):
	?>
		<div class="card mb-3">
			<div class="card-header d-flex justify-content-between align-items-center">
				<h5 class="mb-0">
					<i class="bi bi-calendar-event"></i> <?= $day ?>
				</h5>
				<?php if (!empty($scheduleByDay[$day])): ?>
					<span class="badge bg-primary"><?= count($scheduleByDay[$day]) ?> Kelas</span>
				<?php endif; ?>
			</div>
			<div class="card-body">
				<?php if (empty($scheduleByDay[$day])): ?>
					<div class="text-center py-3 text-muted">
						<i class="bi bi-calendar-x" style="font-size: 2rem;"></i>
						<p class="mt-2">Tidak ada jadwal</p>
					</div>
				<?php else: ?>
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Waktu</th>
									<th>Kode MK</th>
									<th>Mata Kuliah</th>
									<th>Kelas</th>
									<th>Ruang</th>
									<th>Dosen</th>
									<th>SKS</th>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($scheduleByDay[$day] as $schedule): ?>
									<tr>
										<td>
											<?php if ($schedule['jam_mulai'] && $schedule['jam_selesai']): ?>
												<strong><?= date('H:i', strtotime($schedule['jam_mulai'])) ?> - <?= date('H:i', strtotime($schedule['jam_selesai'])) ?></strong>
											<?php else: ?>
												<span class="text-muted">-</span>
											<?php endif; ?>
										</td>
										<td><?= esc($schedule['kode_mk']) ?></td>
										<td><?= esc($schedule['nama_mk']) ?></td>
										<td><span class="badge bg-info"><?= esc($schedule['kelas']) ?></span></td>
										<td>
											<?php if ($schedule['ruang']): ?>
												<i class="bi bi-geo-alt"></i> <?= esc($schedule['ruang']) ?>
											<?php else: ?>
												<span class="text-muted">-</span>
											<?php endif; ?>
										</td>
										<td><?= $schedule['dosen_pengampu'] ? esc($schedule['dosen_pengampu']) : '<span class="text-muted">-</span>' ?></td>
										<td><?= esc($schedule['sks']) ?> SKS</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endforeach; ?>

	<!-- Summary Card -->
	<div class="card">
		<div class="card-body">
			<h5 class="card-title">Ringkasan Jadwal</h5>
			<div class="row g-3 mt-2">
				<div class="col-md-3">
					<div class="text-center p-3 bg-light rounded">
						<h3 class="text-primary mb-0"><?= $totalSKS ?></h3>
						<small class="text-muted">Total SKS</small>
					</div>
				</div>
				<div class="col-md-3">
					<div class="text-center p-3 bg-light rounded">
						<h3 class="text-success mb-0"><?= $totalMK ?></h3>
						<small class="text-muted">Mata Kuliah</small>
					</div>
				</div>
				<div class="col-md-3">
					<div class="text-center p-3 bg-light rounded">
						<h3 class="text-info mb-0"><?= $activeDays ?></h3>
						<small class="text-muted">Hari Aktif</small>
					</div>
				</div>
				<div class="col-md-3">
					<div class="text-center p-3 bg-light rounded">
						<h3 class="text-warning mb-0"><?= $totalDosen ?></h3>
						<small class="text-muted">Dosen Pengampu</small>
					</div>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

<?= $this->endSection() ?>
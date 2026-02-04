<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<div class="mb-4">
	<h2 class="mb-1">Selamat Datang, <?= esc($mahasiswa['nama_lengkap']) ?>!</h2>
	<p class="text-muted">Program Studi: <?= esc($mahasiswa['program_studi']) ?> | Angkatan <?= esc($mahasiswa['tahun_angkatan']) ?></p>
</div>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
	<div class="col-md-4">
		<div class="card stat-card primary">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<h6 class="text-muted mb-2">Total Mata Kuliah</h6>
						<h2 class="mb-0"><?= $totalNilai ?></h2>
					</div>
					<div class="text-primary" style="font-size: 2.5rem;">
						<i class="bi bi-book"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-4">
		<div class="card stat-card success">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<h6 class="text-muted mb-2">Mata Kuliah Lulus</h6>
						<h2 class="mb-0"><?= $nilaiLulus ?></h2>
					</div>
					<div class="text-success" style="font-size: 2.5rem;">
						<i class="bi bi-check-circle"></i>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-4">
		<div class="card stat-card warning">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<h6 class="text-muted mb-2">Status</h6>
						<h5 class="mb-0">
							<?php if ($mahasiswa['status_mahasiswa'] == 'Aktif'): ?>
								<span class="badge bg-success">Aktif</span>
							<?php else: ?>
								<span class="badge bg-secondary"><?= esc($mahasiswa['status_mahasiswa']) ?></span>
							<?php endif; ?>
						</h5>
					</div>
					<div class="text-warning" style="font-size: 2.5rem;">
						<i class="bi bi-person-badge"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Recent Grades -->
<div class="card">
	<div class="card-header d-flex justify-content-between align-items-center">
		<h5 class="mb-0">Nilai Terbaru</h5>
		<a href="<?= base_url('mahasiswa/nilai') ?>" class="btn btn-sm btn-primary">
			Lihat Semua <i class="bi bi-arrow-right"></i>
		</a>
	</div>
	<div class="card-body">
		<?php if (empty($recentNilai)): ?>
			<div class="text-center py-5 text-muted">
				<i class="bi bi-inbox" style="font-size: 3rem;"></i>
				<p class="mt-3">Belum ada data nilai</p>
			</div>
		<?php else: ?>
			<div class="table-responsive">
				<table class="table table-hover">
					<thead>
						<tr>
							<th>Kode MK</th>
							<th>Mata Kuliah</th>
							<th>Kelas</th>
							<th>Tahun Akademik</th>
							<th>Nilai</th>
							<th>Grade</th>
							<th>Status</th>
							<th>Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($recentNilai as $nilai): ?>
							<tr>
								<td><?= esc($nilai['kode_mk']) ?></td>
								<td><?= esc($nilai['nama_mk']) ?></td>
								<td><?= esc($nilai['kelas']) ?></td>
								<td><?= esc($nilai['tahun_akademik']) ?></td>
								<td><?= $nilai['nilai_akhir'] ? number_format($nilai['nilai_akhir'], 2) : '-' ?></td>
								<td>
									<?php if ($nilai['nilai_huruf']): ?>
										<span class="badge bg-primary"><?= esc($nilai['nilai_huruf']) ?></span>
									<?php else: ?>
										-
									<?php endif; ?>
								</td>
								<td>
									<?php if ($nilai['status_kelulusan'] == 'Lulus'): ?>
										<span class="badge bg-success">Lulus</span>
									<?php elseif ($nilai['status_kelulusan'] == 'Tidak Lulus'): ?>
										<span class="badge bg-danger">Tidak Lulus</span>
									<?php else: ?>
										<span class="badge bg-warning">Diproses</span>
									<?php endif; ?>
								</td>
								<td>
									<a href="<?= base_url('mahasiswa/nilai/detail/' . $nilai['jadwal_id']) ?>"
										class="btn btn-sm btn-outline-primary">
										<i class="bi bi-eye"></i> Detail
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
	</div>
</div>

<?= $this->endSection() ?>
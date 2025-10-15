<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<div class="mb-4">
	<h2 class="mb-1">Kegiatan MBKM</h2>
	<p class="text-muted">Merdeka Belajar Kampus Merdeka - Program dan Kegiatan Saya</p>
</div>

<!-- MBKM Info Alert -->
<div class="alert alert-info alert-dismissible fade show" role="alert">
	<h5 class="alert-heading">
		<i class="bi bi-info-circle"></i> Tentang MBKM
	</h5>
	<p class="mb-0">
		MBKM adalah kebijakan Kemendikbudristek yang memberikan kesempatan mahasiswa untuk mengasah kemampuan sesuai bakat dan minat melalui berbagai kegiatan pembelajaran di luar program studi. Mahasiswa dapat mengambil hingga <strong>20 SKS</strong> dari kegiatan MBKM.
	</p>
	<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<!-- Statistics -->
<div class="row g-4 mb-4">
	<div class="col-md-3">
		<div class="card stat-card primary">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<h6 class="text-muted mb-2">Total Kegiatan</h6>
						<h2 class="mb-0"><?= $totalKegiatan ?></h2>
					</div>
					<div class="text-primary" style="font-size: 2.5rem;">
						<i class="bi bi-clipboard-check"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card stat-card success">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<h6 class="text-muted mb-2">SKS Terkonversi</h6>
						<h2 class="mb-0"><?= $totalSKS ?></h2>
					</div>
					<div class="text-success" style="font-size: 2.5rem;">
						<i class="bi bi-award"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card stat-card warning">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<h6 class="text-muted mb-2">Status</h6>
						<h5 class="mb-0">
							<span class="badge bg-<?= $statusBadge ?>"><?= $statusText ?></span>
						</h5>
					</div>
					<div class="text-warning" style="font-size: 2.5rem;">
						<i class="bi bi-hourglass-split"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card stat-card info">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<h6 class="text-muted mb-2">Nilai Akhir</h6>
						<h2 class="mb-0"><?= $nilaiHuruf ?></h2>
					</div>
					<div class="text-info" style="font-size: 2.5rem;">
						<i class="bi bi-star-fill"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- My MBKM Activities -->
<div class="card mb-4">
	<div class="card-header d-flex justify-content-between align-items-center">
		<h5 class="mb-0">Kegiatan MBKM Saya</h5>
		<a href="<?= base_url('mahasiswa/mbkm/daftar') ?>" class="btn btn-sm btn-primary">
			<i class="bi bi-plus-circle"></i> Daftar Kegiatan Baru
		</a>
	</div>
	<div class="card-body">
		<?php if (empty($kegiatanList)): ?>
			<div class="text-center py-5 text-muted">
				<i class="bi bi-inbox" style="font-size: 3rem;"></i>
				<p class="mt-3">Belum ada kegiatan MBKM</p>
				<a href="<?= base_url('mahasiswa/mbkm/daftar') ?>" class="btn btn-primary mt-2">
					<i class="bi bi-plus-circle"></i> Daftar Kegiatan
				</a>
			</div>
		<?php else: ?>
			<?php foreach ($kegiatanList as $kegiatan): ?>
				<!-- Activity Item -->
				<div class="border rounded p-3 mb-3">
					<div class="row">
						<div class="col-md-8">
							<div class="d-flex align-items-start">
								<div class="bg-primary bg-opacity-10 rounded p-3 me-3">
									<i class="bi bi-briefcase text-primary" style="font-size: 2rem;"></i>
								</div>
								<div class="flex-grow-1">
									<h5 class="mb-1"><?= esc($kegiatan['judul_kegiatan']) ?></h5>
									<p class="text-muted mb-2">
										<i class="bi bi-building"></i> <?= esc($kegiatan['tempat_kegiatan']) ?>
									</p>
									<div class="mb-2">
										<span class="badge bg-info"><?= esc($kegiatan['nama_kegiatan']) ?></span>
										<?php
										$statusClass = 'secondary';
										$statusLabel = ucfirst($kegiatan['status_kegiatan']);
										switch ($kegiatan['status_kegiatan']) {
											case 'selesai':
												$statusClass = 'success';
												$statusLabel = 'Selesai';
												break;
											case 'berlangsung':
												$statusClass = 'primary';
												$statusLabel = 'Berlangsung';
												break;
											case 'disetujui':
												$statusClass = 'info';
												$statusLabel = 'Disetujui';
												break;
											case 'diajukan':
												$statusClass = 'warning';
												$statusLabel = 'Diajukan';
												break;
											case 'ditolak':
												$statusClass = 'danger';
												$statusLabel = 'Ditolak';
												break;
										}
										?>
										<span class="badge bg-<?= $statusClass ?>"><?= $statusLabel ?></span>
									</div>
									<p class="mb-2">
										<i class="bi bi-calendar-range"></i>
										<strong>Periode:</strong> <?= date('d M Y', strtotime($kegiatan['tanggal_mulai'])) ?> - <?= date('d M Y', strtotime($kegiatan['tanggal_selesai'])) ?> (<?= $kegiatan['durasi_minggu'] ?> minggu)
									</p>
									<?php if ($kegiatan['pembimbing_lapangan']): ?>
										<p class="mb-0">
											<i class="bi bi-person-badge"></i>
											<strong>Pembimbing Lapangan:</strong> <?= esc($kegiatan['pembimbing_lapangan']) ?>
										</p>
									<?php endif; ?>
									<?php if ($kegiatan['dosen_pembimbing']): ?>
										<p class="mb-0">
											<i class="bi bi-person"></i>
											<strong>Dosen Pembimbing:</strong> <?= esc($kegiatan['dosen_pembimbing']) ?>
										</p>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="card bg-light">
								<div class="card-body">
									<h6 class="card-title">Penilaian</h6>
									<?php if ($kegiatan['nilai_angka']): ?>
										<div class="d-flex justify-content-between mb-2">
											<span>Nilai Angka:</span>
											<strong class="text-primary"><?= number_format($kegiatan['nilai_angka'], 2) ?></strong>
										</div>
										<div class="d-flex justify-content-between mb-2">
											<span>Nilai Huruf:</span>
											<strong class="text-primary"><?= esc($kegiatan['nilai_huruf']) ?></strong>
										</div>
										<div class="d-flex justify-content-between mb-2">
											<span>SKS Konversi:</span>
											<strong><?= $kegiatan['sks_dikonversi'] ?> SKS</strong>
										</div>
										<hr>
										<div class="d-flex justify-content-between">
											<span>Status:</span>
											<span class="badge bg-<?= $kegiatan['status_kelulusan'] == 'Lulus' ? 'success' : 'danger' ?>">
												<?= esc($kegiatan['status_kelulusan']) ?>
											</span>
										</div>
									<?php else: ?>
										<p class="text-muted mb-2">Belum ada nilai</p>
										<div class="d-flex justify-content-between mb-2">
											<span>SKS Konversi:</span>
											<strong><?= $kegiatan['sks_dikonversi'] ?> SKS</strong>
										</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</div>
					<hr>
					<div class="d-flex gap-2">
						<a href="<?= base_url('mahasiswa/mbkm/detail/' . $kegiatan['id']) ?>" class="btn btn-sm btn-outline-primary">
							<i class="bi bi-eye"></i> Lihat Detail
						</a>
						<?php if ($kegiatan['dokumen_pendukung']): ?>
							<a href="<?= base_url('uploads/mbkm/' . $kegiatan['dokumen_pendukung']) ?>" class="btn btn-sm btn-outline-secondary" target="_blank">
								<i class="bi bi-file-earmark-text"></i> Lihat Dokumen
							</a>
						<?php endif; ?>
						<?php if ($kegiatan['status_kegiatan'] == 'selesai' && $kegiatan['nilai_huruf']): ?>
							<button class="btn btn-sm btn-outline-info">
								<i class="bi bi-download"></i> Unduh Sertifikat
							</button>
						<?php endif; ?>
					</div>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
	</div>
</div>

<!-- MBKM Programs Available -->
<div class="card">
	<div class="card-header">
		<h5 class="mb-0">Program MBKM yang Tersedia</h5>
	</div>
	<div class="card-body">
		<div class="row g-3">
			<?php
			$icons = [
				'MBKM01' => 'briefcase',
				'MBKM02' => 'arrow-left-right',
				'MBKM03' => 'person-video3',
				'MBKM04' => 'search',
				'MBKM05' => 'heart',
				'MBKM06' => 'rocket',
				'MBKM07' => 'lightbulb',
				'MBKM08' => 'house-heart',
			];

			$colors = ['primary', 'success', 'info', 'warning', 'danger', 'secondary', 'dark'];
			?>
			<?php foreach ($jenisKegiatan as $index => $jenis): ?>
				<div class="col-md-3">
					<div class="card h-100 border-<?= $colors[$index % count($colors)] ?> hover-shadow" style="cursor: pointer;">
						<div class="card-body text-center">
							<div class="bg-<?= $colors[$index % count($colors)] ?> bg-opacity-10 rounded-circle p-3 d-inline-flex mb-3">
								<i class="bi bi-<?= $icons[$jenis['kode_kegiatan']] ?? 'star' ?> text-<?= $colors[$index % count($colors)] ?>" style="font-size: 2rem;"></i>
							</div>
							<h6 class="card-title"><?= esc($jenis['nama_kegiatan']) ?></h6>
							<small class="text-muted"><?= $jenis['sks_konversi'] ?> SKS</small>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<style>
	.hover-shadow:hover {
		box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
		transform: translateY(-2px);
		transition: all 0.3s;
	}
</style>
<?= $this->endSection() ?>
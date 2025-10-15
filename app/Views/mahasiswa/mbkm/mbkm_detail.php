<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<div class="mb-4">
	<div class="d-flex justify-content-between align-items-center">
		<div>
			<h2 class="mb-1">Detail Kegiatan MBKM</h2>
			<p class="text-muted"><?= esc($kegiatan['judul_kegiatan']) ?></p>
		</div>
		<a href="<?= base_url('mahasiswa/mbkm') ?>" class="btn btn-outline-secondary">
			<i class="bi bi-arrow-left"></i> Kembali
		</a>
	</div>
</div>

<!-- Status Card -->
<div class="card mb-4">
	<div class="card-header">
		<h5 class="mb-0">Status Kegiatan</h5>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-md-3">
				<div class="text-center p-3">
					<?php
					$statusClass = 'secondary';
					$statusIcon = 'hourglass-split';
					$statusText = ucfirst($kegiatan['status_kegiatan']);
					switch ($kegiatan['status_kegiatan']) {
						case 'selesai':
							$statusClass = 'success';
							$statusIcon = 'check-circle';
							$statusText = 'Selesai';
							break;
						case 'berlangsung':
							$statusClass = 'primary';
							$statusIcon = 'play-circle';
							$statusText = 'Berlangsung';
							break;
						case 'disetujui':
							$statusClass = 'info';
							$statusIcon = 'check-square';
							$statusText = 'Disetujui';
							break;
						case 'diajukan':
							$statusClass = 'warning';
							$statusIcon = 'clock-history';
							$statusText = 'Menunggu Persetujuan';
							break;
						case 'ditolak':
							$statusClass = 'danger';
							$statusIcon = 'x-circle';
							$statusText = 'Ditolak';
							break;
					}
					?>
					<i class="bi bi-<?= $statusIcon ?> text-<?= $statusClass ?>" style="font-size: 3rem;"></i>
					<h5 class="mt-2 mb-0">
						<span class="badge bg-<?= $statusClass ?>"><?= $statusText ?></span>
					</h5>
				</div>
			</div>
			<div class="col-md-9">
				<div class="row">
					<div class="col-md-6 mb-3">
						<h6 class="text-muted">Jenis Kegiatan</h6>
						<p class="mb-0"><span class="badge bg-info"><?= esc($kegiatan['nama_kegiatan']) ?></span></p>
					</div>
					<div class="col-md-6 mb-3">
						<h6 class="text-muted">SKS Konversi</h6>
						<p class="mb-0"><strong><?= $kegiatan['sks_dikonversi'] ?> SKS</strong></p>
					</div>
					<div class="col-md-6 mb-3">
						<h6 class="text-muted">Periode</h6>
						<p class="mb-0">
							<?= date('d M Y', strtotime($kegiatan['tanggal_mulai'])) ?> -
							<?= date('d M Y', strtotime($kegiatan['tanggal_selesai'])) ?>
							<br><small class="text-muted">(<?= $kegiatan['durasi_minggu'] ?> minggu)</small>
						</p>
					</div>
					<div class="col-md-6 mb-3">
						<h6 class="text-muted">Tahun Akademik</h6>
						<p class="mb-0"><?= esc($kegiatan['tahun_akademik']) ?></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Informasi Kegiatan -->
<div class="card mb-4">
	<div class="card-header">
		<h5 class="mb-0">Informasi Kegiatan</h5>
	</div>
	<div class="card-body">
		<div class="row">
			<div class="col-md-6 mb-3">
				<h6><i class="bi bi-building"></i> Tempat Kegiatan</h6>
				<p class="ms-4"><?= esc($kegiatan['tempat_kegiatan']) ?></p>
			</div>
			<?php if ($kegiatan['pembimbing_lapangan']): ?>
				<div class="col-md-6 mb-3">
					<h6><i class="bi bi-person-badge"></i> Pembimbing Lapangan</h6>
					<p class="ms-4">
						<?= esc($kegiatan['pembimbing_lapangan']) ?>
						<?php if ($kegiatan['kontak_pembimbing']): ?>
							<br><small class="text-muted"><?= esc($kegiatan['kontak_pembimbing']) ?></small>
						<?php endif; ?>
					</p>
				</div>
			<?php endif; ?>
			<?php if ($kegiatan['dosen_pembimbing']): ?>
				<div class="col-md-6 mb-3">
					<h6><i class="bi bi-person"></i> Dosen Pembimbing</h6>
					<p class="ms-4"><?= esc($kegiatan['dosen_pembimbing']) ?></p>
				</div>
			<?php endif; ?>
		</div>

		<?php if ($kegiatan['deskripsi_kegiatan']): ?>
			<hr>
			<h6><i class="bi bi-file-text"></i> Deskripsi Kegiatan</h6>
			<p class="ms-4"><?= nl2br(esc($kegiatan['deskripsi_kegiatan'])) ?></p>
		<?php endif; ?>

		<?php if ($kegiatan['dokumen_pendukung']): ?>
			<hr>
			<div>
				<h6><i class="bi bi-paperclip"></i> Dokumen Pendukung</h6>
				<div class="ms-4">
					<a href="<?= base_url('uploads/mbkm/' . $kegiatan['dokumen_pendukung']) ?>"
						class="btn btn-outline-primary btn-sm" target="_blank">
						<i class="bi bi-file-earmark-text"></i> Lihat Dokumen
					</a>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>

<!-- Penilaian -->
<?php if (!empty($nilaiKomponen) || $kegiatan['nilai_angka']): ?>
	<div class="card mb-4">
		<div class="card-header">
			<h5 class="mb-0">Penilaian</h5>
		</div>
		<div class="card-body">
			<?php if (!empty($nilaiKomponen)): ?>
				<h6 class="mb-3">Rincian Nilai Komponen</h6>
				<div class="table-responsive">
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Komponen</th>
								<th width="100" class="text-center">Bobot</th>
								<th width="100" class="text-center">Nilai</th>
								<th width="120" class="text-center">Nilai x Bobot</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$totalNilaiBerbobot = 0;
							foreach ($nilaiKomponen as $komponen):
								$nilaiBerbobot = ($komponen['nilai'] * $komponen['bobot']) / 100;
								$totalNilaiBerbobot += $nilaiBerbobot;
							?>
								<tr>
									<td><?= esc($komponen['nama_komponen']) ?></td>
									<td class="text-center"><?= number_format($komponen['bobot'], 0) ?>%</td>
									<td class="text-center"><?= number_format($komponen['nilai'], 2) ?></td>
									<td class="text-center"><?= number_format($nilaiBerbobot, 2) ?></td>
								</tr>
								<?php if ($komponen['catatan']): ?>
									<tr>
										<td colspan="4" class="bg-light">
											<small class="text-muted">
												<i class="bi bi-chat-left-text"></i> <?= esc($komponen['catatan']) ?>
											</small>
										</td>
									</tr>
								<?php endif; ?>
							<?php endforeach; ?>
						</tbody>
						<tfoot>
							<tr class="table-active">
								<td colspan="3" class="text-end"><strong>Total Nilai Akhir:</strong></td>
								<td class="text-center"><strong><?= number_format($totalNilaiBerbobot, 2) ?></strong></td>
							</tr>
						</tfoot>
					</table>
				</div>
			<?php endif; ?>

			<?php if ($kegiatan['nilai_angka']): ?>
				<div class="row mt-4">
					<div class="col-md-4">
						<div class="card bg-light">
							<div class="card-body text-center">
								<h6 class="text-muted mb-2">Nilai Angka</h6>
								<h2 class="text-primary mb-0"><?= number_format($kegiatan['nilai_angka'], 2) ?></h2>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card bg-light">
							<div class="card-body text-center">
								<h6 class="text-muted mb-2">Nilai Huruf</h6>
								<h2 class="text-primary mb-0"><?= esc($kegiatan['nilai_huruf']) ?></h2>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card bg-light">
							<div class="card-body text-center">
								<h6 class="text-muted mb-2">Status Kelulusan</h6>
								<h2 class="mb-0">
									<span class="badge bg-<?= $kegiatan['status_kelulusan'] == 'Lulus' ? 'success' : 'danger' ?>">
										<?= esc($kegiatan['status_kelulusan']) ?>
									</span>
								</h2>
							</div>
						</div>
					</div>
				</div>

				<?php if ($kegiatan['catatan_akhir']): ?>
					<div class="alert alert-info mt-3">
						<h6 class="alert-heading"><i class="bi bi-chat-left-quote"></i> Catatan Dosen Pembimbing</h6>
						<p class="mb-0"><?= nl2br(esc($kegiatan['catatan_akhir'])) ?></p>
					</div>
				<?php endif; ?>
			<?php else: ?>
				<div class="alert alert-warning">
					<i class="bi bi-info-circle"></i> Penilaian belum tersedia. Nilai akan diinput oleh dosen pembimbing setelah kegiatan selesai.
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>

<!-- Action Buttons -->
<?php if ($kegiatan['status_kegiatan'] == 'selesai' && $kegiatan['nilai_huruf']): ?>
	<div class="card">
		<div class="card-body text-center">
			<h5>Unduh Dokumen</h5>
			<p class="text-muted">Kegiatan MBKM Anda telah selesai dan dinilai</p>
			<button class="btn btn-primary" disabled>
				<i class="bi bi-download"></i> Unduh Sertifikat (Segera Hadir)
			</button>
			<button class="btn btn-outline-secondary" disabled>
				<i class="bi bi-file-pdf"></i> Unduh Transkrip Nilai (Segera Hadir)
			</button>
		</div>
	</div>
<?php endif; ?>

<?= $this->endSection() ?>
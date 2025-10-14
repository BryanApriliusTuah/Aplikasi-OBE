<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
	<div class="d-flex justify-content-between align-items-center my-4">
		<div>
			<h2 class="fw-bold mb-1">Tambah Komponen Penilaian</h2>
			<p class="text-muted mb-0">
				<i class="bi bi-tag"></i> <?= esc($jenis['kode_kegiatan']) ?> - <?= esc($jenis['nama_kegiatan']) ?>
			</p>
		</div>
		<a href="<?= base_url('admin/mbkm-komponen/' . $jenis['id']) ?>" class="btn btn-outline-secondary">
			<i class="bi bi-arrow-left"></i> Kembali
		</a>
	</div>

	<?php if (session()->getFlashdata('errors')): ?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<strong>Terdapat kesalahan:</strong>
			<ul class="mb-0">
				<?php foreach (session()->getFlashdata('errors') as $error): ?>
					<li><?= esc($error) ?></li>
				<?php endforeach; ?>
			</ul>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>

	<?php if (session()->getFlashdata('error')): ?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<?= esc(session()->getFlashdata('error')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>

	<div class="row justify-content-center">
		<div class="col-md-8">
			<!-- Weight Info Card -->
			<div class="card bg-light mb-3">
				<div class="card-body">
					<div class="row text-center">
						<div class="col-md-6">
							<h6 class="text-muted mb-1">Total Bobot Terpakai</h6>
							<h2 class="mb-0 text-secondary"><?= number_format(100 - $remaining_weight, 1) ?>%</h2>
							<small class="text-muted">Komponen yang sudah ada</small>
						</div>
						<div class="col-md-6">
							<h6 class="text-muted mb-1">Bobot Tersedia</h6>
							<h2 class="mb-0 text-<?= $remaining_weight > 0 ? 'success' : 'danger' ?>"><?= number_format($remaining_weight, 1) ?>%</h2>
							<small class="text-muted">Tersedia untuk komponen baru</small>
						</div>
					</div>
				</div>
			</div>

			<?php if ($remaining_weight <= 0): ?>
				<div class="alert alert-danger">
					<i class="bi bi-exclamation-triangle"></i> <strong>Bobot Sudah Penuh!</strong><br>
					Total bobot sudah mencapai 100%. Tidak dapat menambah komponen baru.
					Silakan edit atau hapus komponen yang sudah ada jika ingin menambah komponen baru.
				</div>
				<a href="<?= base_url('admin/mbkm-komponen/' . $jenis['id']) ?>" class="btn btn-secondary">
					<i class="bi bi-arrow-left"></i> Kembali ke Daftar Komponen
				</a>
			<?php else: ?>
				<div class="card shadow-sm">
					<div class="card-body p-4">
						<form action="<?= base_url('admin/mbkm-komponen/store') ?>" method="POST">
							<?= csrf_field() ?>
							<input type="hidden" name="jenis_kegiatan_id" value="<?= $jenis['id'] ?>">

							<div class="mb-3">
								<label for="nama_komponen" class="form-label">
									Nama Komponen <span class="text-danger">*</span>
								</label>
								<input type="text"
									class="form-control"
									id="nama_komponen"
									name="nama_komponen"
									value="<?= old('nama_komponen') ?>"
									required
									placeholder="Contoh: Kehadiran dan Kedisiplinan">
								<small class="text-muted">Nama komponen penilaian (maksimal 100 karakter)</small>
							</div>

							<div class="mb-3">
								<label for="bobot" class="form-label">
									Bobot (%) <span class="text-danger">*</span>
								</label>
								<input type="number"
									class="form-control"
									id="bobot"
									name="bobot"
									value="<?= old('bobot') ?>"
									required
									min="0.01"
									max="<?= $remaining_weight ?>"
									step="0.01">
								<small class="text-muted">
									Bobot komponen dalam persen (maksimal: <?= number_format($remaining_weight, 2) ?>%)
								</small>
							</div>

							<div class="mb-3">
								<label for="deskripsi" class="form-label">Deskripsi</label>
								<textarea class="form-control"
									id="deskripsi"
									name="deskripsi"
									rows="4"
									placeholder="Jelaskan kriteria penilaian untuk komponen ini..."><?= old('deskripsi') ?></textarea>
								<small class="text-muted">Deskripsi kriteria penilaian komponen ini</small>
							</div>

							<div class="alert alert-info">
								<i class="bi bi-lightbulb"></i> <strong>Tips Membuat Komponen:</strong>
								<ul class="mb-0 mt-2">
									<li>Gunakan nama komponen yang jelas dan spesifik</li>
									<li>Pastikan total bobot semua komponen = 100%</li>
									<li>Komponen dengan bobot lebih besar akan lebih berpengaruh pada nilai akhir</li>
									<li>Contoh komponen umum: Kehadiran (15-20%), Kinerja (30-40%), Laporan (25-30%)</li>
								</ul>
							</div>

							<hr>

							<div class="d-flex justify-content-end gap-2">
								<a href="<?= base_url('admin/mbkm-komponen/' . $jenis['id']) ?>" class="btn btn-secondary">
									<i class="bi bi-x-circle"></i> Batal
								</a>
								<button type="submit" class="btn btn-primary">
									<i class="bi bi-save"></i> Simpan Komponen
								</button>
							</div>
						</form>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		const bobotInput = document.getElementById('bobot');
		const maxBobot = parseFloat(<?= $remaining_weight ?>);

		bobotInput.addEventListener('input', function() {
			const value = parseFloat(this.value);
			if (value > maxBobot) {
				this.value = maxBobot;
				alert(`Bobot maksimal adalah ${maxBobot}% (sesuai sisa bobot yang tersedia)`);
			}
		});
	});
</script>
<?= $this->endSection() ?>
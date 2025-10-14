<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
	<div class="d-flex justify-content-between align-items-center my-4">
		<h2 class="fw-bold">Tambah Jenis Kegiatan MBKM</h2>
		<a href="<?= base_url('admin/mbkm-jenis') ?>" class="btn btn-outline-secondary">
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

	<div class="row justify-content-center">
		<div class="col-md-8">
			<div class="card shadow-sm">
				<div class="card-body p-4">
					<form action="<?= base_url('admin/mbkm-jenis/store') ?>" method="POST">
						<?= csrf_field() ?>

						<div class="mb-3">
							<label for="kode_kegiatan" class="form-label">
								Kode Kegiatan <span class="text-danger">*</span>
							</label>
							<input type="text"
								class="form-control"
								id="kode_kegiatan"
								name="kode_kegiatan"
								value="<?= old('kode_kegiatan') ?>"
								required
								placeholder="Contoh: MBKM09">
							<small class="text-muted">Kode unik untuk jenis kegiatan (maksimal 20 karakter)</small>
						</div>

						<div class="mb-3">
							<label for="nama_kegiatan" class="form-label">
								Nama Kegiatan <span class="text-danger">*</span>
							</label>
							<input type="text"
								class="form-control"
								id="nama_kegiatan"
								name="nama_kegiatan"
								value="<?= old('nama_kegiatan') ?>"
								required
								placeholder="Contoh: Program Sertifikasi Industri">
							<small class="text-muted">Nama lengkap jenis kegiatan MBKM</small>
						</div>

						<div class="mb-3">
							<label for="deskripsi" class="form-label">Deskripsi</label>
							<textarea class="form-control"
								id="deskripsi"
								name="deskripsi"
								rows="4"
								placeholder="Jelaskan jenis kegiatan ini..."><?= old('deskripsi') ?></textarea>
							<small class="text-muted">Deskripsi singkat tentang jenis kegiatan ini</small>
						</div>

						<div class="mb-3">
							<label for="sks_konversi" class="form-label">
								SKS Konversi <span class="text-danger">*</span>
							</label>
							<input type="number"
								class="form-control"
								id="sks_konversi"
								name="sks_konversi"
								value="<?= old('sks_konversi', 20) ?>"
								required
								min="1"
								max="24">
							<small class="text-muted">Jumlah SKS yang dapat dikonversi (umumnya 20 SKS)</small>
						</div>

						<div class="alert alert-warning">
							<i class="bi bi-exclamation-triangle"></i> <strong>Catatan Penting:</strong>
							<ul class="mb-0 mt-2">
								<li>Setelah membuat jenis kegiatan, Anda perlu menambahkan <strong>Komponen Penilaian</strong></li>
								<li>Total bobot komponen penilaian harus <strong>100%</strong></li>
								<li>Kode kegiatan harus unik dan tidak boleh sama dengan yang sudah ada</li>
							</ul>
						</div>

						<hr>

						<div class="d-flex justify-content-end gap-2">
							<a href="<?= base_url('admin/mbkm-jenis') ?>" class="btn btn-secondary">
								<i class="bi bi-x-circle"></i> Batal
							</a>
							<button type="submit" class="btn btn-primary">
								<i class="bi bi-save"></i> Simpan Jenis Kegiatan
							</button>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<?= $this->endSection() ?>
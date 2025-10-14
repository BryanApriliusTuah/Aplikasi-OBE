<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
	<div class="d-flex justify-content-between align-items-center my-4">
		<h2 class="fw-bold">Edit Jenis Kegiatan MBKM</h2>
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
					<form action="<?= base_url('admin/mbkm-jenis/update/' . $jenis['id']) ?>" method="POST">
						<?= csrf_field() ?>
						
						<div class="mb-3">
							<label for="kode_kegiatan" class="form-label">
								Kode Kegiatan <span class="text-danger">*</span>
							</label>
							<input type="text" 
								   class="form-control" 
								   id="kode_kegiatan" 
								   name="kode_kegiatan" 
								   value="<?= esc($jenis['kode_kegiatan']) ?>" 
								   required>
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
								   value="<?= esc($jenis['nama_kegiatan']) ?>" 
								   required>
							<small class="text-muted">Nama lengkap jenis kegiatan MBKM</small>
						</div>

						<div class="mb-3">
							<label for="deskripsi" class="form-label">Deskripsi</label>
							<textarea class="form-control" 
									  id="deskripsi" 
									  name="deskripsi" 
									  rows="4"><?= esc($jenis['deskripsi'] ?? '') ?></textarea>
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
								   value="<?= esc($jenis['sks_konversi']) ?>" 
								   required
								   min="1"
								   max="24">
							<small class="text-muted">Jumlah SKS yang dapat dikonversi</small>
						</div>

						<div class="mb-3">
							<label for="status" class="form-label">
								Status <span class="text-danger">*</span>
							</label>
							<select class="form-select" id="status" name="status" required>
								<option value="aktif" <?= $jenis['status'] == 'aktif' ? 'selected' : '' ?>>Aktif</option>
								<option value="nonaktif" <?= $jenis['status'] == 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
							</select>
							<small class="text-muted">Jenis kegiatan nonaktif tidak akan muncul di form pembuatan kegiatan</small>
						</div>

						<div class="alert alert-info">
							<i class="bi bi-info-circle"></i> <strong>Informasi:</strong>
							<ul class="mb-0 mt-2">
								<li>Untuk mengelola komponen penilaian, klik tombol <strong>"Kelola Komponen"</strong> di halaman daftar</li>
								<li>Perubahan SKS konversi tidak akan mempengaruhi kegiatan yang sudah ada</li>
							</ul>
						</div>

						<hr>

						<div class="d-flex justify-content-between">
							<button type="button" class="btn btn-danger" onclick="confirmDelete()">
								<i class="bi bi-trash"></i> Hapus Jenis Kegiatan
							</button>
							<div class="d-flex gap-2">
								<a href="<?= base_url('admin/mbkm-jenis') ?>" class="btn btn-secondary">
									<i class="bi bi-x-circle"></i> Batal
								</a>
								<a href="<?= base_url('admin/mbkm-komponen/' . $jenis['id']) ?>" class="btn btn-info">
									<i class="bi bi-list-check"></i> Kelola Komponen
								</a>
								<button type="submit" class="btn btn-primary">
									<i class="bi bi-save"></i> Simpan Perubahan
								</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	function confirmDelete() {
		if (confirm('Apakah Anda yakin ingin menghapus jenis kegiatan ini?\n\nSemua komponen penilaian terkait juga akan dihapus!')) {
			window.location.href = '<?= base_url('admin/mbkm-jenis/delete/' . $jenis['id']) ?>';
		}
	}
</script>
<?= $this->endSection() ?>
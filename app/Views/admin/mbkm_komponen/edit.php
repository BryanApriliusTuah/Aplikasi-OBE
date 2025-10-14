<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
	<div class="d-flex justify-content-between align-items-center my-4">
		<div>
			<h2 class="fw-bold mb-1">Edit Komponen Penilaian</h2>
			<p class="text-muted mb-0">
				<i class="bi bi-tag"></i> <?= esc($komponen['nama_kegiatan']) ?>
			</p>
		</div>
		<a href="<?= base_url('admin/mbkm-komponen/' . $komponen['jenis_kegiatan_id']) ?>" class="btn btn-outline-secondary">
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
							<h2 class="mb-0 text-primary"><?= number_format(100 - $remaining_weight, 1) ?>%</h2>
							<small class="text-muted">Komponen lain yang sudah diisi</small>
						</div>
						<div class="col-md-6">
							<h6 class="text-muted mb-1">Bobot Tersedia</h6>
							<h2 class="mb-0 text-<?= $remaining_weight > 0 ? 'success' : 'warning' ?>"><?= number_format($remaining_weight, 1) ?>%</h2>
							<small class="text-muted">Bisa digunakan untuk komponen ini</small>
						</div>
					</div>
				</div>
			</div>

			<div class="card shadow-sm">
				<div class="card-body p-4">
					<form action="<?= base_url('admin/mbkm-komponen/update/' . $komponen['id']) ?>" method="POST">
						<?= csrf_field() ?>
						
						<div class="mb-3">
							<label for="nama_komponen" class="form-label">
								Nama Komponen <span class="text-danger">*</span>
							</label>
							<input type="text" 
								   class="form-control" 
								   id="nama_komponen" 
								   name="nama_komponen" 
								   value="<?= esc($komponen['nama_komponen']) ?>" 
								   required>
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
								   value="<?= esc($komponen['bobot']) ?>" 
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
									  rows="4"><?= esc($komponen['deskripsi'] ?? '') ?></textarea>
							<small class="text-muted">Deskripsi kriteria penilaian komponen ini</small>
						</div>

						<div class="alert alert-info">
							<i class="bi bi-info-circle"></i> <strong>Informasi:</strong>
							<ul class="mb-0 mt-2">
								<li>Komponen lain menggunakan <strong><?= number_format(100 - $remaining_weight, 1) ?>%</strong> dari total bobot</li>
								<li>Anda dapat menggunakan maksimal <strong><?= number_format($remaining_weight, 1) ?>%</strong> untuk komponen ini</li>
								<li>Total semua komponen harus = <strong>100%</strong></li>
							</ul>
						</div>

						<hr>

						<div class="d-flex justify-content-between">
							<button type="button" class="btn btn-danger" onclick="confirmDelete()">
								<i class="bi bi-trash"></i> Hapus Komponen
							</button>
							<div class="d-flex gap-2">
								<a href="<?= base_url('admin/mbkm-komponen/' . $komponen['jenis_kegiatan_id']) ?>" class="btn btn-secondary">
									<i class="bi bi-x-circle"></i> Batal
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
	document.addEventListener('DOMContentLoaded', function() {
		const bobotInput = document.getElementById('bobot');
		const maxBobot = parseFloat(<?= $remaining_weight + $komponen['bobot'] ?>);
		
		bobotInput.addEventListener('input', function() {
			const value = parseFloat(this.value);
			if (value > maxBobot) {
				this.value = maxBobot;
				alert(`Bobot maksimal adalah ${maxBobot}% (sesuai sisa bobot yang tersedia)`);
			}
		});
	});

	function confirmDelete() {
		if (confirm('Apakah Anda yakin ingin menghapus komponen ini?\n\nKomponen yang sudah digunakan tidak dapat dihapus.')) {
			window.location.href = '<?= base_url('admin/mbkm-komponen/delete/' . $komponen['id']) ?>';
		}
	}
</script>
<?= $this->endSection() ?>
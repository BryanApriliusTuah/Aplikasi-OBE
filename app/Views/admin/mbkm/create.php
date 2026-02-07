<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
	<div class="d-flex justify-content-between align-items-center my-4">
		<h2 class="fw-bold">Tambah Kegiatan MBKM</h2>
		<a href="<?= base_url('admin/mbkm') ?>" class="btn btn-outline-secondary">
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

	<div class="card shadow-sm">
		<div class="card-body">
			<form action="<?= base_url('admin/mbkm/store') ?>" method="POST" enctype="multipart/form-data">
				<?= csrf_field() ?>

				<div class="row">
					<!-- Left Column -->
					<div class="col-md-6">
						<h5 class="mb-3 text-primary"><i class="bi bi-person-circle"></i> Informasi Mahasiswa</h5>

						<div class="mb-3">
							<label for="mahasiswa_ids" class="form-label">Mahasiswa <span class="text-danger">*</span></label>
							<small class="d-block text-muted mb-2">Pilih satu atau lebih mahasiswa yang terlibat dalam kegiatan ini</small>
							<select class="form-select" id="mahasiswa_ids" name="mahasiswa_ids[]" multiple="multiple" required>
								<?php foreach ($mahasiswa as $mhs): ?>
									<option value="<?= $mhs['id'] ?>">
										<?= esc($mhs['nama_lengkap']) ?> (<?= esc($mhs['nim']) ?>)
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<h5 class="mb-3 mt-4 text-primary"><i class="bi bi-bookmark"></i> Detail Kegiatan</h5>

						<div class="row">
							<div class="col-md-6 mb-3">
								<label for="program" class="form-label">Program</label>
								<input type="text" class="form-control" id="program" name="program"
									value="" placeholder="Contoh: MSIB, Riset, Kampus Mengajar">
								<small class="text-muted">Program utama MBKM</small>
							</div>
							<div class="col-md-6 mb-3">
								<label for="sub_program" class="form-label">Sub Program</label>
								<input type="text" class="form-control" id="sub_program" name="sub_program"
									value="" placeholder="Contoh: Magang, Studi Independen, Penelitian di Desa">
								<small class="text-muted">Sub program atau kategori spesifik</small>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6 mb-3">
								<label for="tujuan" class="form-label">Tujuan (Perusahaan/Universitas)</label>
								<input type="text" class="form-control" id="tujuan" name="tujuan"
									value="" placeholder="Contoh: PT Telkom Indonesia, Universitas Gadjah Mada">
								<small class="text-muted">Nama perusahaan atau institusi tujuan</small>
							</div>
						</div>
					</div>
				</div>

				<hr>

				<div class="d-flex justify-content-end gap-2">
					<a href="<?= base_url('admin/mbkm') ?>" class="btn btn-secondary">
						<i class="bi bi-x-circle"></i> Batal
					</a>
					<button type="submit" class="btn btn-primary">
						<i class="bi bi-save"></i> Simpan Kegiatan
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Initialize Select2
		$('#mahasiswa_ids').select2({
			theme: 'bootstrap-5',
			placeholder: 'Cari dan pilih mahasiswa...',
			allowClear: true,
			width: '100%'
		});

		$('#dosen_pembimbing_id').select2({
			theme: 'bootstrap-5',
			placeholder: 'Pilih Dosen Pembimbing',
			allowClear: true,
			width: '100%'
		});
	});
</script>
<?= $this->endSection() ?>
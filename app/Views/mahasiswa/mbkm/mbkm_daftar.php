<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<div class="mb-4">
	<div class="d-flex justify-content-between align-items-center">
		<div>
			<h2 class="mb-1">Daftar Kegiatan MBKM</h2>
			<p class="text-muted">Ajukan kegiatan MBKM Anda</p>
		</div>
		<a href="<?= base_url('mahasiswa/mbkm') ?>" class="btn btn-outline-secondary">
			<i class="bi bi-arrow-left"></i> Kembali
		</a>
	</div>
</div>

<?php if (session()->getFlashdata('errors')): ?>
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		<h6 class="alert-heading">
			<i class="bi bi-exclamation-triangle"></i> Terjadi Kesalahan
		</h6>
		<ul class="mb-0">
			<?php foreach (session()->getFlashdata('errors') as $error): ?>
				<li><?= esc($error) ?></li>
			<?php endforeach; ?>
		</ul>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
<?php endif; ?>

<div class="card">
	<div class="card-header">
		<h5 class="mb-0">Formulir Pendaftaran MBKM</h5>
	</div>
	<div class="card-body">
		<form action="<?= base_url('mahasiswa/mbkm/daftar/store') ?>" method="post" enctype="multipart/form-data">
			<?= csrf_field() ?>

			<!-- Jenis Kegiatan -->
			<div class="row mb-3">
				<label class="col-sm-3 col-form-label">Jenis Kegiatan <span class="text-danger">*</span></label>
				<div class="col-sm-9">
					<select class="form-select" name="jenis_kegiatan_id" id="jenis_kegiatan_id" required>
						<option value="">-- Pilih Jenis Kegiatan --</option>
						<?php foreach ($jenisKegiatan as $jenis): ?>
							<option value="<?= $jenis['id'] ?>"
								<?= old('jenis_kegiatan_id') == $jenis['id'] ? 'selected' : '' ?>>
								<?= esc($jenis['nama_kegiatan']) ?> (<?= $jenis['sks_konversi'] ?> SKS)
							</option>
						<?php endforeach; ?>
					</select>
					<small class="text-muted">Pilih jenis kegiatan MBKM yang akan Anda ikuti</small>
				</div>
			</div>

			<!-- Judul Kegiatan -->
			<div class="row mb-3">
				<label class="col-sm-3 col-form-label">Judul Kegiatan <span class="text-danger">*</span></label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="judul_kegiatan"
						value="<?= old('judul_kegiatan') ?>" required
						placeholder="Contoh: Magang sebagai Software Developer di PT. ABC">
					<small class="text-muted">Berikan judul yang jelas dan deskriptif</small>
				</div>
			</div>

			<!-- Tempat Kegiatan -->
			<div class="row mb-3">
				<label class="col-sm-3 col-form-label">Tempat Kegiatan <span class="text-danger">*</span></label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="tempat_kegiatan"
						value="<?= old('tempat_kegiatan') ?>" required
						placeholder="Contoh: PT. Tech Indonesia, Jakarta">
				</div>
			</div>

			<!-- Tanggal -->
			<div class="row mb-3">
				<label class="col-sm-3 col-form-label">Periode Kegiatan <span class="text-danger">*</span></label>
				<div class="col-sm-9">
					<div class="row g-2">
						<div class="col-md-6">
							<label class="form-label small">Tanggal Mulai</label>
							<input type="date" class="form-control" name="tanggal_mulai"
								value="<?= old('tanggal_mulai') ?>" required>
						</div>
						<div class="col-md-6">
							<label class="form-label small">Tanggal Selesai</label>
							<input type="date" class="form-control" name="tanggal_selesai"
								value="<?= old('tanggal_selesai') ?>" required>
						</div>
					</div>
					<small class="text-muted">Minimal durasi kegiatan MBKM adalah 4 bulan (16 minggu)</small>
				</div>
			</div>

			<!-- Pembimbing Lapangan -->
			<div class="row mb-3">
				<label class="col-sm-3 col-form-label">Pembimbing Lapangan</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="pembimbing_lapangan"
						value="<?= old('pembimbing_lapangan') ?>"
						placeholder="Nama pembimbing di tempat kegiatan">
					<small class="text-muted">Nama pembimbing dari instansi/perusahaan (jika ada)</small>
				</div>
			</div>

			<!-- Kontak Pembimbing -->
			<div class="row mb-3">
				<label class="col-sm-3 col-form-label">Kontak Pembimbing</label>
				<div class="col-sm-9">
					<input type="text" class="form-control" name="kontak_pembimbing"
						value="<?= old('kontak_pembimbing') ?>"
						placeholder="Email atau nomor telepon">
				</div>
			</div>

			<!-- Dosen Pembimbing -->
			<div class="row mb-3">
				<label class="col-sm-3 col-form-label">Dosen Pembimbing</label>
				<div class="col-sm-9">
					<select class="form-select" name="dosen_pembimbing_id">
						<option value="">-- Pilih Dosen Pembimbing (Opsional) --</option>
						<?php foreach ($dosenList as $dosen): ?>
							<option value="<?= $dosen['id'] ?>"
								<?= old('dosen_pembimbing_id') == $dosen['id'] ? 'selected' : '' ?>>
								<?= esc($dosen['nama_lengkap']) ?>
								<?= $dosen['jabatan_fungsional'] ? ' - ' . esc($dosen['jabatan_fungsional']) : '' ?>
							</option>
						<?php endforeach; ?>
					</select>
					<small class="text-muted">Dosen pembimbing akan ditentukan kemudian jika tidak dipilih</small>
				</div>
			</div>

			<!-- Deskripsi Kegiatan -->
			<div class="row mb-3">
				<label class="col-sm-3 col-form-label">Deskripsi Kegiatan</label>
				<div class="col-sm-9">
					<textarea class="form-control" name="deskripsi_kegiatan" rows="5"
						placeholder="Jelaskan detail kegiatan yang akan Anda lakukan..."><?= old('deskripsi_kegiatan') ?></textarea>
					<small class="text-muted">Jelaskan secara detail aktivitas yang akan dilakukan selama kegiatan MBKM</small>
				</div>
			</div>

			<!-- Dokumen Pendukung -->
			<div class="row mb-3">
				<label class="col-sm-3 col-form-label">Dokumen Pendukung</label>
				<div class="col-sm-9">
					<input type="file" class="form-control" name="dokumen_pendukung"
						accept=".pdf,.doc,.docx">
					<small class="text-muted">Upload surat penerimaan/proposal (PDF, DOC, DOCX - Max 2MB)</small>
				</div>
			</div>

			<!-- Info Box -->
			<div class="alert alert-info">
				<h6 class="alert-heading"><i class="bi bi-info-circle"></i> Informasi Penting</h6>
				<ul class="mb-0 ps-3">
					<li>Kegiatan MBKM dapat dikonversi menjadi maksimal 20 SKS</li>
					<li>Durasi minimal kegiatan adalah 4 bulan (16 minggu)</li>
					<li>Pengajuan akan diproses oleh admin dan dosen pembimbing</li>
					<li>Pastikan semua data yang diisi sudah benar</li>
				</ul>
			</div>

			<!-- Buttons -->
			<div class="row">
				<div class="col-sm-9 offset-sm-3">
					<button type="submit" class="btn btn-primary">
						<i class="bi bi-send"></i> Ajukan Pendaftaran
					</button>
					<a href="<?= base_url('mahasiswa/mbkm') ?>" class="btn btn-secondary">
						<i class="bi bi-x-circle"></i> Batal
					</a>
				</div>
			</div>
		</form>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	// Validate dates
	document.querySelector('form').addEventListener('submit', function(e) {
		const tanggalMulai = new Date(document.querySelector('[name="tanggal_mulai"]').value);
		const tanggalSelesai = new Date(document.querySelector('[name="tanggal_selesai"]').value);

		if (tanggalSelesai <= tanggalMulai) {
			e.preventDefault();
			alert('Tanggal selesai harus setelah tanggal mulai!');
			return false;
		}

		// Calculate duration in weeks
		const diffTime = Math.abs(tanggalSelesai - tanggalMulai);
		const diffWeeks = Math.ceil(diffTime / (1000 * 60 * 60 * 24 * 7));

		if (diffWeeks < 16) {
			e.preventDefault();
			alert('Durasi kegiatan minimal 16 minggu (4 bulan)!\nDurasi saat ini: ' + diffWeeks + ' minggu');
			return false;
		}
	});

	// Show description based on selected activity type
	const jenisSelect = document.getElementById('jenis_kegiatan_id');
	const descriptions = {
		<?php foreach ($jenisKegiatan as $jenis): ?>
			<?= $jenis['id'] ?>: <?= json_encode($jenis['deskripsi']) ?>
		<?php endforeach; ?>
	}

	jenisSelect.addEventListener('change', function() {
		const description = descriptions[this.value];
		if (description) {
			// You can show a tooltip or info box here
			console.log('Selected activity description:', description);
		}
	});
</script>
<?= $this->endSection() ?>
<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="card">
	<div class="card-header">
		<div class="d-flex justify-content-between align-items-center">
			<h5 class="mb-0">Form Edit Mahasiswa</h5>
			<a href="<?= base_url('admin/mahasiswa') ?>" class="btn btn-secondary btn-sm">
				<i class="bi bi-arrow-left"></i> Kembali
			</a>
		</div>
	</div>
	<div class="card-body">
		<?php
		// Get manual errors from the session flashdata
		$errors = session()->getFlashdata('errors') ?? [];
		?>

		<?php if (!empty($errors)) : ?>
			<div class="alert alert-danger" role="alert">
				<strong>Terdapat kesalahan:</strong>
				<ul>
					<?php foreach ($errors as $error) : ?>
						<li><?= esc($error) ?></li>
					<?php endforeach ?>
				</ul>
			</div>
		<?php endif; ?>

		<form action="<?= base_url('admin/mahasiswa/update/' . $mahasiswa['id']) ?>" method="post">
			<?= csrf_field() ?>

			<div class="mb-3">
				<label for="nim" class="form-label">NIM</label>
				<input type="text" class="form-control <?= isset($errors['nim']) ? 'is-invalid' : '' ?>" id="nim" name="nim" value="<?= old('nim', esc($mahasiswa['nim'])) ?>" placeholder="Contoh: JTE2025001" required>
				<?php if (isset($errors['nim'])) : ?>
					<div class="invalid-feedback">
						<?= esc($errors['nim']) ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="mb-3">
				<label for="nama_lengkap" class="form-label">Nama Lengkap</label>
				<input type="text" class="form-control <?= isset($errors['nama_lengkap']) ? 'is-invalid' : '' ?>" id="nama_lengkap" name="nama_lengkap" value="<?= old('nama_lengkap', esc($mahasiswa['nama_lengkap'])) ?>" placeholder="Masukkan nama lengkap" required>
				<?php if (isset($errors['nama_lengkap'])) : ?>
					<div class="invalid-feedback">
						<?= esc($errors['nama_lengkap']) ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="mb-3">
				<label for="jenis_kelamin" class="form-label">Jenis Kelamin</label>
				<select class="form-select <?= isset($errors['jenis_kelamin']) ? 'is-invalid' : '' ?>" id="jenis_kelamin" name="jenis_kelamin">
					<?php $currentJK = old('jenis_kelamin', $mahasiswa['jenis_kelamin']); ?>
					<option value="" <?= empty($currentJK) ? 'selected' : '' ?>>-- Pilih Jenis Kelamin --</option>
					<option value="L" <?= $currentJK === 'L' ? 'selected' : '' ?>>Laki-laki</option>
					<option value="P" <?= $currentJK === 'P' ? 'selected' : '' ?>>Perempuan</option>
				</select>
				<?php if (isset($errors['jenis_kelamin'])) : ?>
					<div class="invalid-feedback">
						<?= esc($errors['jenis_kelamin']) ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="mb-3">
				<label for="email" class="form-label">Email</label>
				<input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= old('email', esc($mahasiswa['email'])) ?>" placeholder="Masukkan email">
				<?php if (isset($errors['email'])) : ?>
					<div class="invalid-feedback">
						<?= esc($errors['email']) ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="mb-3">
				<label for="no_hp" class="form-label">No. HP</label>
				<input type="text" class="form-control <?= isset($errors['no_hp']) ? 'is-invalid' : '' ?>" id="no_hp" name="no_hp" value="<?= old('no_hp', esc($mahasiswa['no_hp'])) ?>" placeholder="Masukkan nomor HP">
				<?php if (isset($errors['no_hp'])) : ?>
					<div class="invalid-feedback">
						<?= esc($errors['no_hp']) ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="mb-3">
				<label for="program_studi_kode" class="form-label">Program Studi</label>
				<select class="form-select <?= isset($errors['program_studi_kode']) ? 'is-invalid' : '' ?>" id="program_studi_kode" name="program_studi_kode">
					<option value="" disabled <?= empty(old('program_studi_kode', $mahasiswa['program_studi_kode'])) ? 'selected' : '' ?>>-- Pilih Program Studi --</option>
					<?php $currentProdi = old('program_studi_kode', $mahasiswa['program_studi_kode']); ?>
					<?php foreach ($programStudi as $prodi) : ?>
						<option value="<?= esc($prodi['kode']) ?>" <?= (string)$currentProdi === (string)$prodi['kode'] ? 'selected' : '' ?>><?= esc($prodi['nama_resmi']) ?></option>
					<?php endforeach; ?>
				</select>
				<?php if (isset($errors['program_studi_kode'])) : ?>
					<div class="invalid-feedback">
						<?= esc($errors['program_studi_kode']) ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="mb-3">
				<label for="tahun_angkatan" class="form-label">Tahun Angkatan</label>
				<input type="number" class="form-control <?= isset($errors['tahun_angkatan']) ? 'is-invalid' : '' ?>" id="tahun_angkatan" name="tahun_angkatan" value="<?= old('tahun_angkatan', esc($mahasiswa['tahun_angkatan'])) ?>" min="2000" max="<?= date('Y') + 1 ?>" placeholder="YYYY" required>
				<?php if (isset($errors['tahun_angkatan'])) : ?>
					<div class="invalid-feedback">
						<?= esc($errors['tahun_angkatan']) ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="mb-3">
				<label for="status_mahasiswa" class="form-label">Status Mahasiswa</label>
				<select class="form-select <?= isset($errors['status_mahasiswa']) ? 'is-invalid' : '' ?>" id="status_mahasiswa" name="status_mahasiswa" required>
					<?php $currentStatus = old('status_mahasiswa', $mahasiswa['status_mahasiswa']); ?>
					<option value="Aktif" <?= $currentStatus === 'Aktif' ? 'selected' : '' ?>>Aktif</option>
					<option value="Cuti" <?= $currentStatus === 'Cuti' ? 'selected' : '' ?>>Cuti</option>
					<option value="Lulus" <?= $currentStatus === 'Lulus' ? 'selected' : '' ?>>Lulus</option>
					<option value="Mengundurkan Diri" <?= $currentStatus === 'Mengundurkan Diri' ? 'selected' : '' ?>>Mengundurkan Diri</option>
					<option value="DO" <?= $currentStatus === 'DO' ? 'selected' : '' ?>>DO (Drop Out)</option>
				</select>
				<?php if (isset($errors['status_mahasiswa'])) : ?>
					<div class="invalid-feedback">
						<?= esc($errors['status_mahasiswa']) ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="d-flex justify-content-end">
				<button type="submit" class="btn btn-primary">
					<i class="bi bi-save"></i> Update Perubahan
				</button>
			</div>
		</form>
	</div>
</div>

<?= $this->endSection() ?>

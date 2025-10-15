<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<div class="mb-4">
	<h2 class="mb-1">Profil Saya</h2>
	<p class="text-muted">Informasi profil dan akun Anda</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<i class="bi bi-check-circle"></i> <?= session()->getFlashdata('success') ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		<i class="bi bi-exclamation-triangle"></i> <?= session()->getFlashdata('error') ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
<?php endif; ?>

<div class="row">
	<!-- Profile Card -->
	<div class="col-md-4">
		<div class="card mb-4">
			<div class="card-body text-center">
				<div class="user-avatar mx-auto mb-3" style="width: 120px; height: 120px; font-size: 3rem;">
					<?php
					$nama = $mahasiswa['nama_lengkap'] ?? 'M';
					echo strtoupper(substr($nama, 0, 1));
					?>
				</div>
				<h4 class="mb-1"><?= esc($mahasiswa['nama_lengkap']) ?></h4>
				<p class="text-muted mb-2"><?= esc($mahasiswa['nim']) ?></p>
				<span class="badge bg-primary mb-3"><?= esc($mahasiswa['program_studi']) ?></span>

				<hr>

				<div class="d-grid gap-2">
					<?php if ($user): ?>
						<button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
							<i class="bi bi-key"></i> Ubah Password
						</button>
					<?php else: ?>
						<div class="alert alert-warning mb-0">
							<small><i class="bi bi-exclamation-triangle"></i> Akun belum terhubung</small>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<!-- Quick Info Card -->
		<div class="card">
			<div class="card-header">
				<h6 class="mb-0">Informasi Akademik</h6>
			</div>
			<div class="card-body">
				<div class="d-flex justify-content-between mb-2">
					<span class="text-muted">NIM</span>
					<strong><?= esc($mahasiswa['nim']) ?></strong>
				</div>
				<div class="d-flex justify-content-between mb-2">
					<span class="text-muted">Angkatan</span>
					<strong><?= esc($mahasiswa['tahun_angkatan']) ?></strong>
				</div>
				<div class="d-flex justify-content-between mb-2">
					<span class="text-muted">Status</span>
					<?php if ($mahasiswa['status_mahasiswa'] == 'Aktif'): ?>
						<span class="badge bg-success">Aktif</span>
					<?php else: ?>
						<span class="badge bg-secondary"><?= esc($mahasiswa['status_mahasiswa']) ?></span>
					<?php endif; ?>
				</div>
				<div class="d-flex justify-content-between mb-2">
					<span class="text-muted">Program Studi</span>
					<strong><?= esc($mahasiswa['program_studi']) ?></strong>
				</div>
			</div>
		</div>
	</div>

	<!-- Profile Information -->
	<div class="col-md-8">
		<div class="card mb-4">
			<div class="card-header">
				<h5 class="mb-0">Informasi Mahasiswa</h5>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table table-borderless">
						<tbody>
							<tr>
								<td width="200" class="fw-bold">NIM</td>
								<td>:</td>
								<td><?= esc($mahasiswa['nim']) ?></td>
							</tr>
							<tr>
								<td class="fw-bold">Nama Lengkap</td>
								<td>:</td>
								<td><?= esc($mahasiswa['nama_lengkap']) ?></td>
							</tr>
							<tr>
								<td class="fw-bold">Email Institusi</td>
								<td>:</td>
								<td><?= esc($mahasiswa['nim']) ?>@student.upr.ac.id</td>
							</tr>
							<tr>
								<td class="fw-bold">Program Studi</td>
								<td>:</td>
								<td><?= esc($mahasiswa['program_studi']) ?></td>
							</tr>
							<tr>
								<td class="fw-bold">Tahun Angkatan</td>
								<td>:</td>
								<td><?= esc($mahasiswa['tahun_angkatan']) ?></td>
							</tr>
							<tr>
								<td class="fw-bold">Status Mahasiswa</td>
								<td>:</td>
								<td>
									<?php if ($mahasiswa['status_mahasiswa'] == 'Aktif'): ?>
										<span class="badge bg-success p-2">
											<i class="bi bi-check-circle"></i> Aktif
										</span>
									<?php elseif ($mahasiswa['status_mahasiswa'] == 'Cuti'): ?>
										<span class="badge bg-warning p-2">
											<i class="bi bi-pause-circle"></i> Cuti
										</span>
									<?php elseif ($mahasiswa['status_mahasiswa'] == 'Lulus'): ?>
										<span class="badge bg-info p-2">
											<i class="bi bi-mortarboard"></i> Lulus
										</span>
									<?php elseif ($mahasiswa['status_mahasiswa'] == 'DO'): ?>
										<span class="badge bg-danger p-2">
											<i class="bi bi-x-circle"></i> DO
										</span>
									<?php else: ?>
										<span class="badge bg-secondary p-2"><?= esc($mahasiswa['status_mahasiswa']) ?></span>
									<?php endif; ?>
								</td>
							</tr>
							<tr>
								<td class="fw-bold">Terdaftar Sejak</td>
								<td>:</td>
								<td><?= date('d F Y', strtotime($mahasiswa['created_at'])) ?></td>
							</tr>
							<tr>
								<td class="fw-bold">Terakhir Diperbarui</td>
								<td>:</td>
								<td><?= date('d F Y H:i', strtotime($mahasiswa['updated_at'])) ?> WIB</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<!-- Account Settings -->
		<div class="card">
			<div class="card-header">
				<h5 class="mb-0">Pengaturan Akun</h5>
			</div>
			<div class="card-body">
				<?php if ($user): ?>
					<div class="table-responsive">
						<table class="table table-borderless">
							<tbody>
								<tr>
									<td width="200" class="fw-bold">Username</td>
									<td>:</td>
									<td>
										<?= esc($user['username']) ?>
									</td>
								</tr>
								<tr>
									<td class="fw-bold">Role</td>
									<td>:</td>
									<td>
										<span class="badge bg-primary"><?= ucfirst($user['role']) ?></span>
									</td>
								</tr>
								<tr>
									<td class="fw-bold">Password</td>
									<td>:</td>
									<td>
										<span class="text-muted">••••••••</span>
										<br>
										<button type="button" class="btn btn-sm btn-outline-primary mt-2" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
											<i class="bi bi-key"></i> Ubah Password
										</button>
									</td>
								</tr>
								<tr>
									<td class="fw-bold">Akun Dibuat</td>
									<td>:</td>
									<td><?= date('d F Y', strtotime($user['created_at'])) ?></td>
								</tr>
							</tbody>
						</table>
					</div>
				<?php else: ?>
					<div class="alert alert-warning">
						<i class="bi bi-exclamation-triangle"></i>
						<strong>Akun belum terhubung!</strong>
						<p class="mb-0">Data mahasiswa Anda belum terhubung dengan akun pengguna. Silakan hubungi admin untuk menghubungkan akun.</p>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Info Alert -->
		<div class="alert alert-info mt-4">
			<i class="bi bi-info-circle"></i>
			<strong>Catatan:</strong> Data profil mahasiswa dikelola oleh admin program studi. Jika terdapat kesalahan data atau perlu perubahan informasi, silakan hubungi admin program studi atau bagian akademik.
		</div>
	</div>
</div>

<!-- Change Password Modal -->
<?php if ($user): ?>
	<div class="modal fade" id="changePasswordModal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Ubah Password</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<form method="post" action="<?= base_url('mahasiswa/profil/change-password') ?>">
					<?= csrf_field() ?>
					<div class="modal-body">
						<div class="mb-3">
							<label class="form-label">Password Lama <span class="text-danger">*</span></label>
							<input type="password" class="form-control <?= session('errors.old_password') ? 'is-invalid' : '' ?>" name="old_password" required>
							<?php if (session('errors.old_password')): ?>
								<div class="invalid-feedback">
									<?= session('errors.old_password') ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="mb-3">
							<label class="form-label">Password Baru <span class="text-danger">*</span></label>
							<input type="password" class="form-control <?= session('errors.new_password') ? 'is-invalid' : '' ?>" name="new_password" required minlength="8">
							<small class="text-muted">Minimal 8 karakter</small>
							<?php if (session('errors.new_password')): ?>
								<div class="invalid-feedback">
									<?= session('errors.new_password') ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="mb-3">
							<label class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
							<input type="password" class="form-control <?= session('errors.confirm_password') ? 'is-invalid' : '' ?>" name="confirm_password" required>
							<?php if (session('errors.confirm_password')): ?>
								<div class="invalid-feedback">
									<?= session('errors.confirm_password') ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
						<button type="submit" class="btn btn-primary">
							<i class="bi bi-save"></i> Simpan
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php endif; ?>

<?= $this->endSection() ?>
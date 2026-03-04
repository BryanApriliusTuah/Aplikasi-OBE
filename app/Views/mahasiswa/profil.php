<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<style>
	.profile-panel {
		background: #fff;
		border: 1px solid #e5e7eb;
		border-radius: 0.875rem;
		box-shadow: 0 1px 3px rgba(0,0,0,0.05);
		overflow: hidden;
		margin-bottom: 1.25rem;
	}

	.profile-panel-header {
		display: flex;
		align-items: center;
		gap: 0.5rem;
		font-size: 0.8rem;
		font-weight: 700;
		text-transform: uppercase;
		letter-spacing: 0.05em;
		color: #475569;
		padding: 1rem 1.5rem;
		border-bottom: 1px solid #f1f5f9;
		background: #f8fafc;
	}

	.profile-panel-body {
		padding: 1.5rem;
	}

	/* Avatar */
	.avatar-circle {
		width: 5rem;
		height: 5rem;
		border-radius: 50%;
		background: linear-gradient(135deg, #6366f1, #8b5cf6);
		color: #fff;
		font-size: 2rem;
		font-weight: 700;
		display: flex;
		align-items: center;
		justify-content: center;
		margin: 0 auto 1rem;
		box-shadow: 0 4px 12px rgba(99,102,241,0.3);
	}

	/* Info rows */
	.info-row {
		display: flex;
		align-items: flex-start;
		padding: 0.7rem 0;
		border-bottom: 1px solid #f8fafc;
	}

	.info-row:last-child {
		border-bottom: none;
	}

	.info-row-label {
		font-size: 0.78rem;
		font-weight: 600;
		color: #94a3b8;
		text-transform: uppercase;
		letter-spacing: 0.04em;
		width: 10rem;
		flex-shrink: 0;
		padding-top: 0.05rem;
	}

	.info-row-value {
		font-size: 0.9rem;
		font-weight: 500;
		color: #1e293b;
	}

	/* Sidebar quick-stat */
	.quick-stat {
		display: flex;
		justify-content: space-between;
		align-items: center;
		padding: 0.6rem 0;
		border-bottom: 1px solid #f8fafc;
		font-size: 0.85rem;
	}

	.quick-stat:last-child {
		border-bottom: none;
	}

	.quick-stat-label {
		color: #94a3b8;
		font-size: 0.78rem;
		font-weight: 500;
	}

	.quick-stat-value {
		font-weight: 600;
		color: #1e293b;
	}
</style>

<!-- Page Header -->
<div class="mb-4">
	<h2 class="mb-1 fw-bold">Profil Saya</h2>
	<p class="text-muted mb-0">Informasi profil dan akun Anda</p>
</div>

<?php if (session()->getFlashdata('success')): ?>
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<i class="bi bi-check-circle-fill me-1"></i> <?= session()->getFlashdata('success') ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		<i class="bi bi-exclamation-triangle-fill me-1"></i> <?= session()->getFlashdata('error') ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
<?php endif; ?>

<div class="row g-4">

	<!-- Left Sidebar -->
	<div class="col-md-4">

		<!-- Identity card -->
		<div class="profile-panel">
			<div class="profile-panel-body text-center">
				<div class="avatar-circle">
					<?= strtoupper(substr($mahasiswa['nama_lengkap'] ?? 'M', 0, 1)) ?>
				</div>
				<h5 class="fw-bold mb-1"><?= esc($mahasiswa['nama_lengkap']) ?></h5>
				<p class="text-muted mb-2" style="font-size: 0.85rem;"><?= esc($mahasiswa['nim']) ?></p>
				<span class="badge bg-primary" style="font-size: 0.75rem; padding: 0.35rem 0.75rem;">
					<?= esc($programStudi['nama_resmi']) ?>
				</span>

				<?php if ($user): ?>
					<div class="mt-4">
						<button type="button" class="btn btn-outline-secondary w-100" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
							<i class="bi bi-key me-1"></i> Ubah Password
						</button>
					</div>
				<?php else: ?>
					<div class="alert alert-warning mt-4 mb-0 text-start" style="font-size: 0.82rem;">
						<i class="bi bi-exclamation-triangle me-1"></i> Akun belum terhubung
					</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Quick academic info -->
		<div class="profile-panel">
			<div class="profile-panel-header">
				<i class="bi bi-mortarboard-fill text-primary"></i>
				Akademik
			</div>
			<div class="profile-panel-body" style="padding: 0.75rem 1.5rem;">
				<div class="quick-stat">
					<span class="quick-stat-label">NIM</span>
					<span class="quick-stat-value"><?= esc($mahasiswa['nim']) ?></span>
				</div>
				<div class="quick-stat">
					<span class="quick-stat-label">Angkatan</span>
					<span class="quick-stat-value"><?= esc($mahasiswa['tahun_angkatan']) ?></span>
				</div>
				<div class="quick-stat">
					<span class="quick-stat-label">Status</span>
					<span>
						<?php if ($mahasiswa['status_mahasiswa'] == 'Aktif'): ?>
							<span class="badge bg-success" style="font-size: 0.72rem;">Aktif</span>
						<?php elseif ($mahasiswa['status_mahasiswa'] == 'Cuti'): ?>
							<span class="badge bg-warning text-dark" style="font-size: 0.72rem;">Cuti</span>
						<?php elseif ($mahasiswa['status_mahasiswa'] == 'Lulus'): ?>
							<span class="badge bg-info" style="font-size: 0.72rem;">Lulus</span>
						<?php elseif ($mahasiswa['status_mahasiswa'] == 'DO'): ?>
							<span class="badge bg-danger" style="font-size: 0.72rem;">DO</span>
						<?php else: ?>
							<span class="badge bg-secondary" style="font-size: 0.72rem;"><?= esc($mahasiswa['status_mahasiswa']) ?></span>
						<?php endif; ?>
					</span>
				</div>
				<div class="quick-stat">
					<span class="quick-stat-label">Program Studi</span>
					<span class="quick-stat-value text-end" style="max-width: 60%; font-size: 0.8rem;"><?= esc($programStudi['nama_resmi']) ?></span>
				</div>
			</div>
		</div>

	</div>

	<!-- Right Content -->
	<div class="col-md-8">

		<!-- Student Info -->
		<div class="profile-panel">
			<div class="profile-panel-header">
				<i class="bi bi-person-fill text-primary"></i>
				Informasi Mahasiswa
			</div>
			<div class="profile-panel-body">
				<div class="info-row">
					<span class="info-row-label">NIM</span>
					<span class="info-row-value"><?= esc($mahasiswa['nim']) ?></span>
				</div>
				<div class="info-row">
					<span class="info-row-label">Nama Lengkap</span>
					<span class="info-row-value"><?= esc($mahasiswa['nama_lengkap']) ?></span>
				</div>
				<div class="info-row">
					<span class="info-row-label">Email Institusi</span>
					<span class="info-row-value"><?= esc($mahasiswa['nim']) ?>@student.upr.ac.id</span>
				</div>
				<div class="info-row">
					<span class="info-row-label">Program Studi</span>
					<span class="info-row-value"><?= esc($programStudi['nama_resmi']) ?></span>
				</div>
				<div class="info-row">
					<span class="info-row-label">Tahun Angkatan</span>
					<span class="info-row-value"><?= esc($mahasiswa['tahun_angkatan']) ?></span>
				</div>
				<div class="info-row">
					<span class="info-row-label">Status</span>
					<span class="info-row-value">
						<?php if ($mahasiswa['status_mahasiswa'] == 'Aktif'): ?>
							<span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Aktif</span>
						<?php elseif ($mahasiswa['status_mahasiswa'] == 'Cuti'): ?>
							<span class="badge bg-warning text-dark"><i class="bi bi-pause-circle-fill me-1"></i>Cuti</span>
						<?php elseif ($mahasiswa['status_mahasiswa'] == 'Lulus'): ?>
							<span class="badge bg-info"><i class="bi bi-mortarboard-fill me-1"></i>Lulus</span>
						<?php elseif ($mahasiswa['status_mahasiswa'] == 'DO'): ?>
							<span class="badge bg-danger"><i class="bi bi-x-circle-fill me-1"></i>DO</span>
						<?php else: ?>
							<span class="badge bg-secondary"><?= esc($mahasiswa['status_mahasiswa']) ?></span>
						<?php endif; ?>
					</span>
				</div>
				<div class="info-row">
					<span class="info-row-label">Terdaftar</span>
					<span class="info-row-value"><?= date('d F Y', strtotime($mahasiswa['created_at'])) ?></span>
				</div>
				<div class="info-row">
					<span class="info-row-label">Diperbarui</span>
					<span class="info-row-value"><?= date('d F Y, H:i', strtotime($mahasiswa['updated_at'])) ?> WIB</span>
				</div>
			</div>
		</div>

		<!-- Account Settings -->
		<div class="profile-panel">
			<div class="profile-panel-header">
				<i class="bi bi-shield-lock-fill text-primary"></i>
				Pengaturan Akun
			</div>
			<div class="profile-panel-body">
				<?php if ($user): ?>
					<div class="info-row">
						<span class="info-row-label">Username</span>
						<span class="info-row-value"><?= esc($user['username']) ?></span>
					</div>
					<div class="info-row">
						<span class="info-row-label">Role</span>
						<span class="info-row-value">
							<span class="badge bg-primary" style="font-size: 0.75rem;"><?= ucfirst($user['role']) ?></span>
						</span>
					</div>
					<div class="info-row">
						<span class="info-row-label">Password</span>
						<span class="info-row-value d-flex align-items-center gap-3">
							<span class="text-muted" style="letter-spacing: 0.15em;">••••••••</span>
							<button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
								<i class="bi bi-key me-1"></i> Ubah
							</button>
						</span>
					</div>
					<div class="info-row">
						<span class="info-row-label">Akun Dibuat</span>
						<span class="info-row-value"><?= date('d F Y', strtotime($user['created_at'])) ?></span>
					</div>
				<?php else: ?>
					<div class="alert alert-warning mb-0">
						<i class="bi bi-exclamation-triangle-fill me-1"></i>
						<strong>Akun belum terhubung!</strong>
						<p class="mb-0 mt-1" style="font-size: 0.85rem;">Data mahasiswa Anda belum terhubung dengan akun pengguna. Silakan hubungi admin untuk menghubungkan akun.</p>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<!-- Info Note -->
		<div class="alert alert-info border-0" style="font-size: 0.85rem; border-radius: 0.75rem;">
			<i class="bi bi-info-circle-fill me-1"></i>
			<strong>Catatan:</strong> Data profil mahasiswa dikelola oleh admin program studi. Jika terdapat kesalahan data atau perlu perubahan informasi, silakan hubungi admin program studi atau bagian akademik.
		</div>

	</div>
</div>

<!-- Change Password Modal -->
<?php if ($user): ?>
	<div class="modal fade" id="changePasswordModal" tabindex="-1">
		<div class="modal-dialog">
			<div class="modal-content" style="border-radius: 0.875rem; border: 1px solid #e5e7eb;">
				<div class="modal-header" style="border-bottom: 1px solid #f1f5f9; background: #f8fafc; border-radius: 0.875rem 0.875rem 0 0;">
					<h5 class="modal-title fw-bold" style="font-size: 0.95rem;">
						<i class="bi bi-key-fill me-2 text-primary"></i>Ubah Password
					</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<form method="post" action="<?= base_url('mahasiswa/profil/change-password') ?>">
					<?= csrf_field() ?>
					<div class="modal-body px-4 py-3">
						<div class="mb-3">
							<label class="form-label fw-semibold" style="font-size: 0.85rem;">Password Lama <span class="text-danger">*</span></label>
							<input type="password" class="form-control <?= session('errors.old_password') ? 'is-invalid' : '' ?>" name="old_password" required>
							<?php if (session('errors.old_password')): ?>
								<div class="invalid-feedback"><?= session('errors.old_password') ?></div>
							<?php endif; ?>
						</div>
						<div class="mb-3">
							<label class="form-label fw-semibold" style="font-size: 0.85rem;">Password Baru <span class="text-danger">*</span></label>
							<input type="password" class="form-control <?= session('errors.new_password') ? 'is-invalid' : '' ?>" name="new_password" required minlength="8">
							<small class="text-muted">Minimal 8 karakter</small>
							<?php if (session('errors.new_password')): ?>
								<div class="invalid-feedback"><?= session('errors.new_password') ?></div>
							<?php endif; ?>
						</div>
						<div class="mb-3">
							<label class="form-label fw-semibold" style="font-size: 0.85rem;">Konfirmasi Password Baru <span class="text-danger">*</span></label>
							<input type="password" class="form-control <?= session('errors.confirm_password') ? 'is-invalid' : '' ?>" name="confirm_password" required>
							<?php if (session('errors.confirm_password')): ?>
								<div class="invalid-feedback"><?= session('errors.confirm_password') ?></div>
							<?php endif; ?>
						</div>
					</div>
					<div class="modal-footer" style="border-top: 1px solid #f1f5f9; background: #f8fafc; border-radius: 0 0 0.875rem 0.875rem;">
						<button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
						<button type="submit" class="btn btn-primary">
							<i class="bi bi-save me-1"></i> Simpan
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
<?php endif; ?>

<?= $this->endSection() ?>

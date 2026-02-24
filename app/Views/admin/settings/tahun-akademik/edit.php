<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="card border-0 shadow-sm">
	<div class="card-body p-4">
		<!-- Header -->
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h4 class="mb-0 fw-semibold">Edit Tahun Akademik</h4>
			<a href="<?= base_url('admin/settings/tahun-akademik') ?>" class="btn btn-outline-secondary btn-sm">
				<i class="bi bi-arrow-left"></i> Kembali
			</a>
		</div>

		<!-- Flash Messages -->
		<?php if (session()->getFlashdata('error')): ?>
			<div class="alert alert-danger alert-dismissible fade show border-0" role="alert">
				<?= session()->getFlashdata('error') ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
			</div>
		<?php endif; ?>

		<form action="<?= base_url('admin/settings/tahun-akademik/update/' . $tahun_akademik['id']) ?>" method="post">
			<?= csrf_field() ?>

			<div class="row g-4">
				<div class="col-md-6">
					<label for="tahun" class="form-label small text-muted mb-2">Tahun <span class="text-danger">*</span></label>
					<input type="text"
						class="form-control"
						id="tahun"
						name="tahun"
						value="<?= old('tahun', $tahun_akademik['tahun']) ?>"
						placeholder="Contoh: 2024/2025"
						pattern="^\d{4}\/\d{4}$"
						required>
					<div class="form-text">Format: YYYY/YYYY, contoh <code>2024/2025</code></div>
				</div>

				<div class="col-md-6">
					<label for="semester" class="form-label small text-muted mb-2">Semester <span class="text-danger">*</span></label>
					<select class="form-select" id="semester" name="semester" required>
						<option value="">-- Pilih Semester --</option>
						<option value="Ganjil" <?= old('semester', $tahun_akademik['semester']) === 'Ganjil' ? 'selected' : '' ?>>Ganjil</option>
						<option value="Genap" <?= old('semester', $tahun_akademik['semester']) === 'Genap' ? 'selected' : '' ?>>Genap</option>
					</select>
				</div>

				<div class="col-md-6">
					<label class="form-label small text-muted mb-2">Status Aktif</label>
					<div class="d-flex gap-3">
						<div class="form-check">
							<input class="form-check-input" type="radio" name="is_active" id="is_active_yes" value="1"
								<?= old('is_active', $tahun_akademik['is_active']) ? 'checked' : '' ?>>
							<label class="form-check-label" for="is_active_yes">Aktif</label>
						</div>
						<div class="form-check">
							<input class="form-check-input" type="radio" name="is_active" id="is_active_no" value="0"
								<?= !old('is_active', $tahun_akademik['is_active']) ? 'checked' : '' ?>>
							<label class="form-check-label" for="is_active_no">Nonaktif</label>
						</div>
					</div>
				</div>
			</div>

			<div class="d-flex gap-2 mt-5">
				<button type="submit" class="btn btn-dark">Perbarui</button>
				<a href="<?= base_url('admin/settings/tahun-akademik') ?>" class="btn btn-outline-secondary">Batal</a>
			</div>
		</form>
	</div>
</div>

<?= $this->endSection() ?>

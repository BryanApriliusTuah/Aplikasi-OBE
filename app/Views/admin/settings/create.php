<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="card border-0 shadow-sm">
	<div class="card-body p-4">
		<!-- Header -->
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h4 class="mb-0 fw-semibold">Tambah Konfigurasi Nilai</h4>
			<a href="<?= base_url('admin/settings') ?>" class="btn btn-outline-secondary btn-sm">
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

		<form action="<?= base_url('admin/settings/store') ?>" method="post">
			<?= csrf_field() ?>

			<div class="row g-4">
				<div class="col-md-6">
					<label for="grade_letter" class="form-label small text-muted mb-2">Huruf Mutu <span class="text-danger">*</span></label>
					<input type="text"
						class="form-control"
						id="grade_letter"
						name="grade_letter"
						value="<?= old('grade_letter') ?>"
						placeholder="A"
						maxlength="10"
						required>
				</div>

				<div class="col-md-6">
					<label for="order_number" class="form-label small text-muted mb-2">Urutan <span class="text-danger">*</span></label>
					<input type="number"
						class="form-control"
						id="order_number"
						name="order_number"
						value="<?= old('order_number') ?>"
						placeholder="1"
						required>
				</div>

				<div class="col-md-6">
					<label for="min_score" class="form-label small text-muted mb-2">Nilai Minimum <span class="text-danger">*</span></label>
					<input type="number"
						class="form-control"
						id="min_score"
						name="min_score"
						value="<?= old('min_score') ?>"
						step="0.01"
						min="0"
						max="100"
						placeholder="0.00"
						required>
				</div>

				<div class="col-md-6">
					<label for="max_score" class="form-label small text-muted mb-2">Nilai Maksimum <span class="text-danger">*</span></label>
					<input type="number"
						class="form-control"
						id="max_score"
						name="max_score"
						value="<?= old('max_score') ?>"
						step="0.01"
						min="0"
						max="100"
						placeholder="100.00"
						required>
				</div>

				<div class="col-md-6">
					<label for="grade_point" class="form-label small text-muted mb-2">Grade Point</label>
					<input type="number"
						class="form-control"
						id="grade_point"
						name="grade_point"
						value="<?= old('grade_point') ?>"
						step="0.01"
						min="0"
						max="4"
						placeholder="4.00">
				</div>

				<div class="col-md-6">
					<label for="description" class="form-label small text-muted mb-2">Deskripsi</label>
					<input type="text"
						class="form-control"
						id="description"
						name="description"
						value="<?= old('description') ?>"
						placeholder="Istimewa"
						maxlength="100">
				</div>

				<div class="col-md-6">
					<label class="form-label small text-muted mb-2">Status Kelulusan <span class="text-danger">*</span></label>
					<div class="d-flex gap-3">
						<div class="form-check">
							<input class="form-check-input"
								type="radio"
								name="is_passing"
								id="is_passing_yes"
								value="1"
								<?= old('is_passing', '1') == '1' ? 'checked' : '' ?>>
							<label class="form-check-label" for="is_passing_yes">Lulus</label>
						</div>
						<div class="form-check">
							<input class="form-check-input"
								type="radio"
								name="is_passing"
								id="is_passing_no"
								value="0"
								<?= old('is_passing') == '0' ? 'checked' : '' ?>>
							<label class="form-check-label" for="is_passing_no">Tidak Lulus</label>
						</div>
					</div>
				</div>

				<div class="col-md-6">
					<label class="form-label small text-muted mb-2">Status Aktif <span class="text-danger">*</span></label>
					<div class="d-flex gap-3">
						<div class="form-check">
							<input class="form-check-input"
								type="radio"
								name="is_active"
								id="is_active_yes"
								value="1"
								<?= old('is_active', '1') == '1' ? 'checked' : '' ?>>
							<label class="form-check-label" for="is_active_yes">Aktif</label>
						</div>
						<div class="form-check">
							<input class="form-check-input"
								type="radio"
								name="is_active"
								id="is_active_no"
								value="0"
								<?= old('is_active') == '0' ? 'checked' : '' ?>>
							<label class="form-check-label" for="is_active_no">Nonaktif</label>
						</div>
					</div>
				</div>
			</div>

			<div class="d-flex gap-2 mt-5">
				<button type="submit" class="btn btn-dark">
					Simpan
				</button>
				<a href="<?= base_url('admin/settings') ?>" class="btn btn-outline-secondary">
					Batal
				</a>
			</div>
		</form>
	</div>
</div>

<?= $this->endSection() ?>
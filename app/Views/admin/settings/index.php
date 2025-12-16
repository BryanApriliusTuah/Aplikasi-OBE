<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="card border-0 shadow-sm">
	<div class="card-body p-4">
		<!-- Header -->
		<div class="d-flex justify-content-between align-items-center mb-5">
			<h4 class="mb-0 fw-semibold">Pengaturan Sistem Penilaian</h4>
			<div class="d-flex gap-2">
				<a href="<?= base_url('admin/settings/create') ?>" class="btn btn-dark btn-sm">
					<i class="bi bi-plus-lg"></i> Tambah
				</a>
			</div>
		</div>

		<!-- Flash Messages -->
		<?php if (session()->getFlashdata('success')): ?>
			<div class="alert alert-success alert-dismissible fade show border-0" role="alert">
				<?= session()->getFlashdata('success') ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
			</div>
		<?php endif; ?>

		<?php if (session()->getFlashdata('error')): ?>
			<div class="alert alert-danger alert-dismissible fade show border-0" role="alert">
				<?= session()->getFlashdata('error') ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
			</div>
		<?php endif; ?>

		<!-- CPMK Threshold -->
		<div class="border rounded p-4 mb-5">
			<h6 class="mb-3 fw-semibold">Standar Minimal Capaian CPMK</h6>
			<form action="<?= base_url('admin/settings/update-standar-cpmk') ?>" method="post" class="row g-3 align-items-end">
				<?= csrf_field() ?>
				<div class="col-auto">
					<label for="persentase" class="form-label small text-muted mb-1">Persentase Minimal (%)</label>
					<div class="input-group">
						<input type="number"
							class="form-control"
							id="persentase"
							name="persentase"
							value="<?= esc($standar_cpmk) ?>"
							min="0"
							max="100"
							step="0.01"
							style="width: 120px;"
							required>
						<span class="input-group-text bg-white">%</span>
					</div>
				</div>
				<div class="col-auto">
					<button type="submit" class="btn btn-dark btn-sm">
						Simpan
					</button>
				</div>
			</form>
		</div>

		<!-- Grade Configuration Table -->
		<div class="modern-table-wrapper">
			<table class="modern-table">
				<thead>
					<tr>
						<th scope="col" class="text-center" style="width: 5%;">No</th>
						<th scope="col" class="text-center" style="width: 10%;">Huruf</th>
						<th scope="col" class="text-center" style="width: 15%;">Range</th>
						<th scope="col" class="text-center" style="width: 10%;">Point</th>
						<th scope="col" style="width: 25%;">Deskripsi</th>
						<th scope="col" class="text-center" style="width: 10%;">Lulus</th>
						<th scope="col" class="text-center" style="width: 10%;">Status</th>
						<th scope="col" class="text-center" style="width: 15%;">Aksi</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($grades)): ?>
						<tr>
							<td colspan="8" class="text-center py-5">
								<div class="text-muted">
									<i class="bi bi-inbox" style="font-size: 2.5rem; opacity: 0.3;"></i>
									<p class="mt-2 mb-0 small">Belum ada konfigurasi</p>
								</div>
							</td>
						</tr>
					<?php else: ?>
						<?php $no = 1;
						foreach ($grades as $grade): ?>
							<tr>
								<td class="text-center"><?= $no++; ?></td>
								<td class="text-center">
									<span class="fw-semibold fs-5"><?= esc($grade['grade_letter']); ?></span>
								</td>
								<td class="text-center text-muted small">
									<?= number_format($grade['min_score'], 2); ?> - <?= number_format($grade['max_score'], 2); ?>
								</td>
								<td class="text-center">
									<?= $grade['grade_point'] ? number_format($grade['grade_point'], 2) : '-'; ?>
								</td>
								<td class="small"><?= esc($grade['description']); ?></td>
								<td class="text-center">
									<?php if ($grade['is_passing']): ?>
										<i class="bi bi-check-circle-fill text-success"></i>
									<?php else: ?>
										<i class="bi bi-x-circle-fill text-danger"></i>
									<?php endif; ?>
								</td>
								<td class="text-center">
									<?php if ($grade['is_active']): ?>
										<span class="fw-bold text-success">Aktif</span>
									<?php else: ?>
										<span class="fw-bold text-muted">Nonaktif</span>
									<?php endif; ?>
								</td>
								<td class="text-center">
									<div class="d-flex gap-2 justify-content-center">
										<a href="<?= base_url('admin/settings/edit/' . $grade['id']); ?>"
											class="btn btn-sm btn-light border-0"
											data-bs-toggle="tooltip"
											title="Edit"
											style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
											<i class="bi bi-pencil"></i>
										</a>
										<a href="<?= base_url('admin/settings/toggle/' . $grade['id']); ?>"
											class="btn btn-sm btn-light border-0"
											data-bs-toggle="tooltip"
											title="<?= $grade['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>"
											style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
											<i class="bi bi-toggle-<?= $grade['is_active'] ? 'on' : 'off' ?>"></i>
										</a>
										<a href="<?= base_url('admin/settings/delete/' . $grade['id']); ?>"
											class="btn btn-sm btn-light border-0 text-danger"
											onclick="return confirm('Hapus konfigurasi ini?')"
											data-bs-toggle="tooltip"
											title="Hapus"
											style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
											<i class="bi bi-trash3"></i>
										</a>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?= $this->endSection() ?>
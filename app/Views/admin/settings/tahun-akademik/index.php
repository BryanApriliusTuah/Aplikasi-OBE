<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="card border-0 shadow-sm">
	<div class="card-body p-4">
		<!-- Header -->
		<div class="d-flex justify-content-between align-items-center mb-4">
			<div>
				<h4 class="mb-0 fw-semibold">Manajemen Tahun Akademik</h4>
				<p class="text-muted small mb-0 mt-1">Kelola daftar tahun akademik yang digunakan sebagai referensi pada jadwal mengajar.</p>
			</div>
			<a href="<?= base_url('admin/settings/tahun-akademik/create') ?>" class="btn btn-dark btn-sm">
				<i class="bi bi-plus-lg"></i> Tambah
			</a>
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

		<!-- Table -->
		<div class="modern-table-wrapper">
			<table class="modern-table">
				<thead>
					<tr>
						<th class="text-center" style="width: 5%;">No</th>
						<th style="width: 30%;">Tahun</th>
						<th style="width: 20%;">Semester</th>
						<th style="width: 25%;">Nama Lengkap</th>
						<th class="text-center" style="width: 10%;">Status</th>
						<th class="text-center" style="width: 10%;">Aksi</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($tahun_akademik)): ?>
						<tr>
							<td colspan="6" class="text-center py-5">
								<div class="text-muted">
									<i class="bi bi-calendar-x" style="font-size: 2.5rem; opacity: 0.3;"></i>
									<p class="mt-2 mb-0 small">Belum ada tahun akademik. Klik <strong>Tambah</strong> untuk menambahkan.</p>
								</div>
							</td>
						</tr>
					<?php else: ?>
						<?php $no = 1; foreach ($tahun_akademik as $row): ?>
							<tr>
								<td class="text-center"><?= $no++ ?></td>
								<td class="fw-semibold"><?= esc($row['tahun']) ?></td>
								<td><?= esc($row['semester']) ?></td>
								<td class="text-muted small"><?= esc($row['tahun'] . ' ' . $row['semester']) ?></td>
								<td class="text-center">
									<?php if ($row['is_active']): ?>
										<span class="fw-bold text-success">Aktif</span>
									<?php else: ?>
										<span class="fw-bold text-muted">Nonaktif</span>
									<?php endif; ?>
								</td>
								<td class="text-center">
									<div class="d-flex gap-2 justify-content-center">
										<a href="<?= base_url('admin/settings/tahun-akademik/edit/' . $row['id']) ?>"
											class="btn btn-sm btn-light border-0"
											data-bs-toggle="tooltip" title="Edit"
											style="width:32px;height:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
											<i class="bi bi-pencil"></i>
										</a>
										<a href="<?= base_url('admin/settings/tahun-akademik/toggle/' . $row['id']) ?>"
											class="btn btn-sm btn-light border-0"
											data-bs-toggle="tooltip" title="<?= $row['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>"
											style="width:32px;height:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
											<i class="bi bi-toggle-<?= $row['is_active'] ? 'on' : 'off' ?>"></i>
										</a>
										<a href="<?= base_url('admin/settings/tahun-akademik/delete/' . $row['id']) ?>"
											class="btn btn-sm btn-light border-0 text-danger"
											onclick="return confirm('Hapus tahun akademik <?= esc($row['tahun'] . ' ' . $row['semester']) ?>?')"
											data-bs-toggle="tooltip" title="Hapus"
											style="width:32px;height:32px;padding:0;display:inline-flex;align-items:center;justify-content:center;">
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

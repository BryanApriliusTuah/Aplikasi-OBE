<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
	<div class="d-flex justify-content-between align-items-center my-4">
		<h2 class="fw-bold">Manajemen Jenis Kegiatan MBKM</h2>
		<div class="d-flex gap-2">
			<a href="<?= base_url('admin/mbkm') ?>" class="btn btn-outline-secondary">
				<i class="bi bi-arrow-left"></i> Kembali ke MBKM
			</a>
			<a href="<?= base_url('admin/mbkm-jenis/create') ?>" class="btn btn-primary">
				<i class="bi bi-plus-circle"></i> Tambah Jenis Kegiatan
			</a>
		</div>
	</div>

	<?php if (session()->getFlashdata('success')): ?>
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			<?= esc(session()->getFlashdata('success')) ?>
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
			<div class="table-responsive">
				<table class="table table-hover table-striped">
					<thead class="table-primary">
						<tr>
							<th width="5%">#</th>
							<th width="12%">Kode</th>
							<th width="25%">Nama Kegiatan</th>
							<th width="35%">Deskripsi</th>
							<th width="8%" class="text-center">SKS</th>
							<th width="8%" class="text-center">Status</th>
							<th width="7%" class="text-center">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($jenis_kegiatan)): ?>
							<tr>
								<td colspan="7" class="text-center text-muted py-4">
									<i class="bi bi-inbox fs-1"></i>
									<p class="mt-2">Belum ada jenis kegiatan</p>
								</td>
							</tr>
						<?php else: ?>
							<?php $no = 1;
							foreach ($jenis_kegiatan as $jenis): ?>
								<tr>
									<td><?= $no++ ?></td>
									<td><span class="badge bg-info"><?= esc($jenis['kode_kegiatan']) ?></span></td>
									<td><strong><?= esc($jenis['nama_kegiatan']) ?></strong></td>
									<td class="small"><?= esc($jenis['deskripsi'] ?? '-') ?></td>
									<td class="text-center">
										<span class="badge bg-success"><?= $jenis['sks_konversi'] ?></span>
									</td>
									<td class="text-center">
										<?php if ($jenis['status'] == 'aktif'): ?>
											<span class="badge bg-success">Aktif</span>
										<?php else: ?>
											<span class="badge bg-secondary">Nonaktif</span>
										<?php endif; ?>
									</td>
									<td class="text-center">
										<div class="btn-group btn-group-sm" role="group">
											<a href="<?= base_url('admin/mbkm-komponen/' . $jenis['id']) ?>"
												class="btn btn-outline-info"
												title="Kelola Komponen Nilai">
												<i class="bi bi-list-check"></i>
											</a>
											<a href="<?= base_url('admin/mbkm-jenis/edit/' . $jenis['id']) ?>"
												class="btn btn-outline-warning"
												title="Edit">
												<i class="bi bi-pencil"></i>
											</a>
											<button class="btn btn-outline-danger"
												onclick="confirmDelete(<?= $jenis['id'] ?>)"
												title="Hapus">
												<i class="bi bi-trash"></i>
											</button>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</div>

			<?php if (!empty($jenis_kegiatan)): ?>
				<div class="alert alert-info mt-3">
					<i class="bi bi-info-circle"></i> <strong>Informasi:</strong>
					<ul class="mb-0 mt-2">
						<li>Klik <i class="bi bi-list-check"></i> untuk mengelola komponen penilaian</li>
						<li>Total jenis kegiatan: <strong><?= count($jenis_kegiatan) ?></strong></li>
						<li>Jenis kegiatan yang sudah digunakan tidak dapat dihapus</li>
					</ul>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	function confirmDelete(id) {
		if (confirm('Apakah Anda yakin ingin menghapus jenis kegiatan ini?\n\nPeringatan: Jenis kegiatan yang sudah digunakan tidak dapat dihapus.')) {
			window.location.href = `<?= base_url('admin/mbkm-jenis/delete/') ?>${id}`;
		}
	}
</script>
<?= $this->endSection() ?>
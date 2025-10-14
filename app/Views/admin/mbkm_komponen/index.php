<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
	<div class="d-flex justify-content-between align-items-center my-4">
		<div>
			<h2 class="fw-bold mb-1">Komponen Penilaian</h2>
			<p class="text-muted mb-0">
				<i class="bi bi-tag"></i> <?= esc($jenis['kode_kegiatan']) ?> - <?= esc($jenis['nama_kegiatan']) ?>
			</p>
		</div>
		<div class="d-flex gap-2">
			<a href="<?= base_url('admin/mbkm-jenis') ?>" class="btn btn-outline-secondary">
				<i class="bi bi-arrow-left"></i> Kembali
			</a>
			<?php if ($total_bobot < 100): ?>
				<a href="<?= base_url('admin/mbkm-komponen/create/' . $jenis['id']) ?>" class="btn btn-primary">
					<i class="bi bi-plus-circle"></i> Tambah Komponen
				</a>
			<?php endif; ?>
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

	<!-- Summary Card -->
	<div class="row mb-4">
		<div class="col-md-3">
			<div class="card bg-primary text-white">
				<div class="card-body text-center">
					<h3 class="mb-0"><?= count($komponen) ?></h3>
					<small>Total Komponen</small>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card bg-<?= $total_bobot == 100 ? 'success' : 'warning' ?> text-white">
				<div class="card-body text-center">
					<h3 class="mb-0"><?= number_format($total_bobot, 1) ?>%</h3>
					<small>Total Bobot</small>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card bg-info text-white">
				<div class="card-body text-center">
					<h3 class="mb-0"><?= number_format(100 - $total_bobot, 1) ?>%</h3>
					<small>Ruang Kosong</small>
				</div>
			</div>
		</div>
		<div class="col-md-3">
			<div class="card bg-secondary text-white">
				<div class="card-body text-center">
					<h3 class="mb-0"><?= $jenis['sks_konversi'] ?></h3>
					<small>SKS Konversi</small>
				</div>
			</div>
		</div>
	</div>

	<!-- Visual Progress Bar -->
	<div class="card mb-4">
		<div class="card-body">
			<h6 class="mb-2">Progress Bobot</h6>
			<div class="progress" style="height: 30px;">
				<div class="progress-bar bg-success"
					role="progressbar"
					style="width: <?= $total_bobot ?>%"
					aria-valuenow="<?= $total_bobot ?>"
					aria-valuemin="0"
					aria-valuemax="100">
					<?= number_format($total_bobot, 1) ?>%
				</div>
				<?php if ($total_bobot < 100): ?>
					<div class="progress-bar bg-light text-dark"
						role="progressbar"
						style="width: <?= 100 - $total_bobot ?>%">
						<?= number_format(100 - $total_bobot, 1) ?>% kosong
					</div>
				<?php endif; ?>
			</div>
			<small class="text-muted d-block mt-2">
				<i class="bi bi-info-circle"></i>
				Target: 100% | Terisi: <?= number_format($total_bobot, 1) ?>% | Tersisa: <?= number_format(100 - $total_bobot, 1) ?>%
			</small>
		</div>
	</div>

	<?php if ($total_bobot != 100): ?>
		<div class="alert alert-warning">
			<i class="bi bi-exclamation-triangle"></i> <strong>Perhatian!</strong>
			Total bobot belum mencapai 100%. Silakan tambah atau sesuaikan komponen agar total bobot = 100%.
		</div>
	<?php else: ?>
		<div class="alert alert-success">
			<i class="bi bi-check-circle"></i> <strong>Sempurna!</strong>
			Total bobot sudah mencapai 100%. Komponen penilaian sudah lengkap.
		</div>
	<?php endif; ?>

	<div class="card shadow-sm">
		<div class="card-body">
			<div class="table-responsive">
				<table class="table table-hover table-striped">
					<thead class="table-success">
						<tr>
							<th width="5%">#</th>
							<th width="30%">Nama Komponen</th>
							<th width="40%">Deskripsi</th>
							<th width="12%" class="text-center">Bobot (%)</th>
							<th width="13%" class="text-center">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php if (empty($komponen)): ?>
							<tr>
								<td colspan="5" class="text-center text-muted py-5">
									<i class="bi bi-inbox fs-1"></i>
									<p class="mt-2">Belum ada komponen penilaian</p>
									<a href="<?= base_url('admin/mbkm-komponen/create/' . $jenis['id']) ?>" class="btn btn-primary btn-sm">
										<i class="bi bi-plus-circle"></i> Tambah Komponen Pertama
									</a>
								</td>
							</tr>
						<?php else: ?>
							<?php $no = 1;
							foreach ($komponen as $k): ?>
								<tr>
									<td><?= $no++ ?></td>
									<td><strong><?= esc($k['nama_komponen']) ?></strong></td>
									<td class="small"><?= esc($k['deskripsi'] ?? '-') ?></td>
									<td class="text-center">
										<span class="badge bg-primary fs-6"><?= number_format($k['bobot'], 1) ?>%</span>
									</td>
									<td class="text-center">
										<div class="btn-group btn-group-sm" role="group">
											<a href="<?= base_url('admin/mbkm-komponen/edit/' . $k['id']) ?>"
												class="btn btn-outline-warning"
												title="Edit">
												<i class="bi bi-pencil"></i>
											</a>
											<button class="btn btn-outline-danger"
												onclick="confirmDelete(<?= $k['id'] ?>)"
												title="Hapus">
												<i class="bi bi-trash"></i>
											</button>
										</div>
									</td>
								</tr>
							<?php endforeach; ?>
							<tr class="table-light">
								<td colspan="3" class="text-end"><strong>TOTAL</strong></td>
								<td class="text-center">
									<strong>
										<span class="badge bg-<?= $total_bobot == 100 ? 'success' : 'warning' ?> fs-6">
											<?= number_format($total_bobot, 1) ?>%
										</span>
									</strong>
								</td>
								<td></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>

			<?php if (!empty($komponen)): ?>
				<div class="alert alert-info mt-3">
					<i class="bi bi-info-circle"></i> <strong>Informasi:</strong>
					<ul class="mb-0 mt-2">
						<li>Komponen penilaian digunakan untuk menghitung nilai akhir kegiatan MBKM</li>
						<li>Total bobot harus <strong>100%</strong> agar sistem dapat menghitung nilai</li>
						<li>Komponen yang sudah digunakan untuk penilaian tidak dapat dihapus</li>
						<li>Formula: Nilai Akhir = Σ (Nilai × Bobot / 100)</li>
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
		if (confirm('Apakah Anda yakin ingin menghapus komponen ini?\n\nPeringatan: Komponen yang sudah digunakan tidak dapat dihapus.')) {
			window.location.href = `<?= base_url('admin/mbkm-komponen/delete/') ?>${id}`;
		}
	}
</script>
<?= $this->endSection() ?>
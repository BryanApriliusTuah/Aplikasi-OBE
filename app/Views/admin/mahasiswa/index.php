<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="card">
	<div class="card-body">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h2 class="mb-0">Master Data Mahasiswa</h2>
			<div class="d-flex">
				<?php if (session()->get('role') === 'admin') : ?>
					<a href="<?= base_url('admin/mahasiswa/create') ?>" class="btn btn-primary">
						<i class="bi bi-plus-lg"></i> Tambah Mahasiswa
					</a>
				<?php endif; ?>
			</div>
		</div>

		<?php if (session()->getFlashdata('success')) : ?>
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				<?= session()->getFlashdata('success') ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		<?php endif; ?>

		<?php if (session()->getFlashdata('error')) : ?>
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<?= session()->getFlashdata('error') ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		<?php endif; ?>
		<div class="table-responsive">
			<table class="table table-hover">
				<thead class="table-light">
					<tr>
						<th scope="col">No</th>
						<th scope="col">NIM</th>
						<th scope="col">Nama Lengkap</th>
						<th scope="col">Program Studi</th>
						<th scope="col">Angkatan</th>
						<th scope="col">Status</th>

						<?php if (session()->get('role') === 'admin') : ?>
							<th scope="col" class="text-center">Aksi</th>
						<?php endif; ?>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($mahasiswa)) : ?>
						<tr>
							<td colspan="7" class="text-center">Belum ada data mahasiswa.</td>
						</tr>
					<?php else : ?>
						<?php $no = 1;
						foreach ($mahasiswa as $m) : ?>
							<tr>
								<td><?= $no++; ?></td>
								<td><?= esc($m['nim']); ?></td>
								<td><?= esc($m['nama_lengkap']); ?></td>
								<td><?= esc($m['program_studi']); ?></td>
								<td><?= esc($m['tahun_angkatan']); ?></td>
								<td>
									<?php
									$status = esc($m['status_mahasiswa']);
									$badgeClass = 'bg-secondary'; // Default badge
									if ($status === 'Aktif') {
										$badgeClass = 'bg-success';
									} elseif ($status === 'Cuti') {
										$badgeClass = 'bg-warning';
									} elseif ($status === 'Lulus') {
										$badgeClass = 'bg-primary';
									} elseif ($status === 'DO' || $status === 'Mengundurkan Diri') {
										$badgeClass = 'bg-danger';
									}
									?>
									<span class="badge <?= $badgeClass ?>"><?= $status ?></span>
								</td>

								<?php if (session()->get('role') === 'admin') : ?>
									<td class="text-center">
										<a href="<?= base_url('admin/mahasiswa/edit/' . $m['id']); ?>" class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip" title="Edit">
											<i class="bi bi-pencil-square"></i>
										</a>
										<a href="<?= base_url('admin/mahasiswa/delete/' . $m['id']); ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" data-bs-toggle="tooltip" title="Hapus">
											<i class="bi bi-trash3"></i>
										</a>
									</td>
								<?php endif; ?>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<?= $this->endSection() ?>
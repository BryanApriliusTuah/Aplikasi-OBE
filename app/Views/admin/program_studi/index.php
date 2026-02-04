<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="card">
	<div class="card-body">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h2 class="mb-0">Master Data Program Studi</h2>
			<div class="d-flex gap-2">
				<?php if (session()->get('role') === 'admin'): ?>
					<form action="<?= base_url('admin/program-studi/sync') ?>" method="post" class="d-inline" onsubmit="return confirm('Sinkronisasi data program studi dari API? Data yang sudah ada akan diperbarui. Pastikan data fakultas sudah disinkronisasi terlebih dahulu.');">
						<button type="submit" class="btn btn-warning">
							<i class="bi bi-arrow-repeat"></i> Sinkronisasi
						</button>
					</form>
				<?php endif; ?>
			</div>
		</div>

		<?php if (session()->getFlashdata('success')): ?>
			<div class="alert alert-success alert-dismissible fade show" role="alert">
				<?= session()->getFlashdata('success') ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		<?php endif; ?>
		<?php if (session()->getFlashdata('error')): ?>
			<div class="alert alert-danger alert-dismissible fade show" role="alert">
				<?= session()->getFlashdata('error') ?>
				<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
			</div>
		<?php endif; ?>

		<?= view('components/modern_filter', [
			'title' => 'Filter Program Studi',
			'action' => base_url('admin/program-studi'),
			'filters' => [
				[
					'type' => 'select',
					'name' => 'fakultas_kode',
					'label' => 'Fakultas',
					'icon' => 'bi-bank',
					'col' => 'col-md-5',
					'options' => $fakultas_options,
					'selected' => $filters['fakultas_kode'] ?? ''
				],
				[
					'type' => 'text',
					'name' => 'search',
					'label' => 'Cari',
					'icon' => 'bi-search',
					'col' => 'col-md-5',
					'placeholder' => 'Cari berdasarkan nama atau kaprodi...',
					'value' => $filters['search'] ?? ''
				]
			],
			'buttonCol' => 'col-md-2',
			'buttonText' => 'Cari',
			'showReset' => true
		]) ?>

		<form method="get" class="mb-3">
			<?php if (!empty($filters['search'])): ?>
				<input type="hidden" name="search" value="<?= esc($filters['search']) ?>">
			<?php endif; ?>
			<?php if (!empty($filters['fakultas_kode'])): ?>
				<input type="hidden" name="fakultas_kode" value="<?= esc($filters['fakultas_kode']) ?>">
			<?php endif; ?>
			<label for="perPage" class="form-label mb-0 me-2">Tampilkan</label>
			<select name="perPage" id="perPage" class="form-select d-inline-block w-auto me-2" onchange="this.form.submit()">
				<option value="5" <?= $perPage == 5 ? 'selected' : '' ?>>5</option>
				<option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
				<option value="20" <?= $perPage == 20 ? 'selected' : '' ?>>20</option>
				<option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
				<option value="1000" <?= $perPage == 1000 ? 'selected' : '' ?>>Semua</option>
			</select>
			<span>baris per halaman</span>
		</form>

		<div class="modern-table-wrapper">
			<table class="table table-hover modern-table">
				<thead>
					<tr>

						<th scope="col">Nama Singkat</th>
						<th scope="col">Nama Resmi</th>
						<th scope="col">Fakultas</th>
						<th scope="col">Telepon</th>
						<th scope="col">Email</th>
						<th scope="col">Website</th>
						<th scope="col">NIP Kaprodi</th>
						<th scope="col">Ketua Prodi</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($program_studi)): ?>
						<tr>
							<td colspan="10" class="text-center">Belum ada data program studi. Sinkronisasi data fakultas terlebih dahulu, kemudian klik tombol sinkronisasi.</td>
						</tr>
					<?php else: ?>
						<?php $no = ($page - 1) * $perPage + 1;
						foreach ($program_studi as $ps): ?>
							<tr>
								<td><?= esc($ps['nama_singkat']); ?></td>
								<td><?= esc($ps['nama_resmi']); ?></td>
								<td><?= esc($ps['fakultas_nama'] ?? '-'); ?></td>
								<td><?= esc($ps['telepon']); ?></td>
								<td><?= esc($ps['email']); ?></td>
								<td><?= esc($ps['website']); ?></td>
								<td><?= esc($ps['nip_kaprodi']); ?></td>
								<td><?= esc($ps['nama_kaprodi']); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>

		<?php
		$queryParams = $filters;
		$queryParams['perPage'] = $perPage;
		$startEntry = $total === 0 ? 0 : (($page - 1) * $perPage) + 1;
		$endEntry = min($page * $perPage, $total);
		?>
		<div class="d-flex justify-content-between align-items-center mt-3">
			<div class="text-muted small">
				Menampilkan <?= $startEntry ?> sampai <?= $endEntry ?> dari <?= $total ?> data
			</div>
			<?php if ($totalPages > 1): ?>
				<nav>
					<ul class="pagination pagination-sm mb-0">
						<li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
							<a class="page-link" href="?<?= http_build_query(array_merge($queryParams, ['page' => $page - 1])) ?>">«</a>
						</li>
						<?php for ($i = 1; $i <= $totalPages; $i++): ?>
							<li class="page-item<?= $page == $i ? ' active' : '' ?>">
								<a class="page-link" href="?<?= http_build_query(array_merge($queryParams, ['page' => $i])) ?>"><?= $i ?></a>
							</li>
						<?php endfor; ?>
						<li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
							<a class="page-link" href="?<?= http_build_query(array_merge($queryParams, ['page' => $page + 1])) ?>">»</a>
						</li>
					</ul>
				</nav>
			<?php endif; ?>
		</div>
	</div>
</div>

<?= $this->endSection() ?>
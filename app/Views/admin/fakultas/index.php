<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="card">
	<div class="card-body">
		<div class="d-flex justify-content-between align-items-center mb-4">
			<h2 class="mb-0">Master Data Fakultas</h2>
			<div class="d-flex gap-2">
				<?php if (session()->get('role') === 'admin'): ?>
					<form action="<?= base_url('admin/fakultas/sync') ?>" method="post" class="d-inline" onsubmit="return confirm('Sinkronisasi data fakultas dari API? Data yang sudah ada akan diperbarui.');">
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
			'title' => 'Filter Fakultas',
			'action' => base_url('admin/fakultas'),
			'filters' => [
				[
					'type' => 'text',
					'name' => 'search',
					'label' => 'Cari',
					'icon' => 'bi-search',
					'col' => 'col-md-10',
					'placeholder' => 'Cari berdasarkan nama atau dekan...',
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
						<th scope="col">Telepon</th>
						<th scope="col">Email</th>
						<th scope="col">NIP Dekan</th>
						<th scope="col">Nama Dekan</th>
					</tr>
				</thead>
				<tbody>
					<?php if (empty($fakultas)): ?>
						<tr>
							<td colspan="8" class="text-center">Belum ada data fakultas. Klik tombol sinkronisasi untuk mengambil data dari API.</td>
						</tr>
					<?php else: ?>
						<?php $no = ($page - 1) * $perPage + 1;
						foreach ($fakultas as $f): ?>
							<tr>
								<td><?= esc($f['nama_singkat']); ?></td>
								<td><?= esc($f['nama_resmi']); ?></td>
								<td><?= esc($f['telepon']); ?></td>
								<td><?= esc($f['email']); ?></td>
								<td><?= esc($f['nip_dekan']); ?></td>
								<td><?= esc($f['nama_dekan']); ?></td>
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
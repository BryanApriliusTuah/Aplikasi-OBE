<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="fw-bold mb-0">Rumusan Nilai Akhir Mata Kuliah</h2>
	<div class="btn-group">
		<button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
			<i class="bi bi-download"></i> Download
		</button>
		<ul class="dropdown-menu dropdown-menu-end">
			<li>
				<a class="dropdown-item" href="<?= base_url('rumusan-akhir-mk/export/pdf') ?>">
					<i class="bi bi-file-earmark-pdf text-danger"></i> PDF
				</a>
			</li>
			<li>
				<a class="dropdown-item" href="<?= base_url('rumusan-akhir-mk/export/excel') ?>">
					<i class="bi bi-file-earmark-excel text-success"></i> Excel
				</a>
			</li>
		</ul>
	</div>
</div>

<?php if (empty($rekap)): ?>
	<div class="shadow-sm border-0">
		<div class="text-center text-muted py-5">
			<i class="bi bi-clipboard-data fs-1"></i>
			<p class="mt-3 fw-semibold">Belum ada data rumusan nilai akhir</p>
		</div>
	</div>
<?php else: ?>
	<?php foreach ($rekap as $mkid => $mk): ?>
		<div class="shadow-sm border-0 mb-4">
			<div class="p-0">
				<div class="modern-table-wrapper">
					<table class="modern-table">
						<thead>
							<tr>
								<th class="text-center" style="min-width: 200px;">MK</th>
								<th class="text-center" style="min-width: 100px;">CPL</th>
								<th class="text-center" style="min-width: 100px;">CPMK</th>
								<th class="text-center" style="min-width: 100px;">Skor Maks</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($mk['detail'] as $i => $row): ?>
								<tr>
									<?php if ($i == 0): ?>
										<td rowspan="<?= count($mk['detail']) ?>" class="align-middle fw-bold" style="white-space:normal;"><?= esc($mk['nama_mk']) ?></td>
									<?php endif ?>
									<td class="text-center"><?= esc($row['kode_cpl']) ?></td>
									<td class="text-center"><?= esc($row['kode_cpmk']) ?></td>
									<td class="text-center"><?= esc($row['bobot']) ?></td>
								</tr>
							<?php endforeach ?>
							<tr style="background-color: #d1e7dd;">
								<td colspan="3" class="fw-bold text-end">Nilai MK <?= esc($mk['nama_mk']) ?> =</td>
								<td class="fw-bold text-center"><?= esc($mk['total']) ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php endforeach ?>
<?php endif ?>

<?= $this->endSection() ?>

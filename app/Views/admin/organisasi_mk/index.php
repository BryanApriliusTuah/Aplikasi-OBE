<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="container-fluid px-0">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h2 class="fw-bold mb-0">Organisasi Mata Kuliah</h2>
		<div class="btn-group">
			<button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
				<i class="bi bi-download"></i> Download
			</button>
			<ul class="dropdown-menu dropdown-menu-end">
				<li>
					<a class="dropdown-item" href="<?= base_url('admin/organisasi-mk/exportExcel') ?>">
						<i class="bi bi-file-earmark-excel text-success"></i> Excel
					</a>
				</li>
				<li>
					<a class="dropdown-item" href="<?= base_url('admin/organisasi-mk/exportPdf') ?>">
						<i class="bi bi-file-earmark-pdf text-danger"></i> PDF
					</a>
				</li>
			</ul>
		</div>
	</div>

	<div class="shadow-sm border-0">
		<div class="p-0">
			<div class="modern-table-wrapper">
				<div class="scroll-indicator"></div>
				<table class="modern-table" id="organisasiMkTable">
					<thead>
						<tr>
							<th class="text-center sticky-col" rowspan="2" style="vertical-align: middle; min-width: 70px;">Smt</th>
							<th class="text-center" rowspan="2" style="vertical-align: middle; min-width: 70px;">SKS</th>
							<th class="text-center" rowspan="2" style="vertical-align: middle; min-width: 80px;">Jml MK</th>
							<th class="text-center" colspan="2">MK Wajib</th>
							<th class="text-center" rowspan="2" style="vertical-align: middle; min-width: 200px;">MK Pilihan</th>
							<th class="text-center" rowspan="2" style="vertical-align: middle; min-width: 200px;">MKWK</th>
						</tr>
						<tr>
							<th class="text-center" style="min-width: 200px;">Teori</th>
							<th class="text-center" style="min-width: 200px;">Praktikum</th>
						</tr>
					</thead>
					<tbody>
						<?php for ($i = 1; $i <= 8; $i++): ?>
							<tr>
								<td class="text-center fw-bold sticky-col"><?= $i ?></td>
								<td class="text-center"><?= $total_sks[$i] ?? 0 ?></td>
								<td class="text-center"><?= $jumlah_mk[$i] ?? 0 ?></td>
								<td style="white-space: normal;"><?= !empty($matkul[$i]['wajib_teori']) ? implode(', ', array_column($matkul[$i]['wajib_teori'], 'nama_mk')) : '' ?></td>
								<td style="white-space: normal;"><?= !empty($matkul[$i]['wajib_praktikum']) ? implode(', ', array_column($matkul[$i]['wajib_praktikum'], 'nama_mk')) : '' ?></td>
								<td style="white-space: normal;"><?= !empty($matkul[$i]['pilihan']) ? implode(', ', array_column($matkul[$i]['pilihan'], 'nama_mk')) : '' ?></td>
								<td style="white-space: normal;"><?= !empty($matkul[$i]['mkwk']) ? implode(', ', array_column($matkul[$i]['mkwk'], 'nama_mk')) : '' ?></td>
							</tr>
						<?php endfor ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<?= $this->section('js') ?>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		const tableWrapper = document.querySelector('.modern-table-wrapper');
		if (tableWrapper) {
			function checkScroll() {
				const hasHorizontalScroll = tableWrapper.scrollWidth > tableWrapper.clientWidth;
				const isScrolledToEnd = tableWrapper.scrollLeft >= (tableWrapper.scrollWidth - tableWrapper.clientWidth - 10);
				if (hasHorizontalScroll && !isScrolledToEnd) {
					tableWrapper.classList.add('has-scroll');
				} else {
					tableWrapper.classList.remove('has-scroll');
				}
			}
			checkScroll();
			window.addEventListener('resize', checkScroll);
			tableWrapper.addEventListener('scroll', checkScroll);
		}
	});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>

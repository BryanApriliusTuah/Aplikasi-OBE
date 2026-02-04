<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="fw-bold mb-0">Tahap dan Mekanisme Penilaian</h2>
	<div class="btn-group">
		<button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
			<i class="bi bi-download"></i> Download
		</button>
		<ul class="dropdown-menu dropdown-menu-end">
			<li>
				<a class="dropdown-item" href="<?= base_url('tahap-mekanisme-penilaian/export/pdf') ?>">
					<i class="bi bi-file-earmark-pdf text-danger"></i> PDF
				</a>
			</li>
			<li>
				<a class="dropdown-item" href="<?= base_url('tahap-mekanisme-penilaian/export/excel') ?>">
					<i class="bi bi-file-earmark-excel text-success"></i> Excel
				</a>
			</li>
		</ul>
	</div>
</div>

<form method="get" class="mb-3">
	<label for="perPage" class="form-label mb-0 me-2">Tampilkan</label>
	<select name="perPage" id="perPage" class="form-select d-inline-block w-auto me-2" onchange="this.form.submit()">
		<option value="5" <?= $perPage == 5 ? 'selected' : '' ?>>5</option>
		<option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
		<option value="20" <?= $perPage == 20 ? 'selected' : '' ?>>20</option>
		<option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
		<option value="1000" <?= $perPage == 1000 ? 'selected' : '' ?>>Semua</option>
	</select>
	<span>baris per halaman</span>
	<?php if (isset($page)): ?>
		<input type="hidden" name="page" value="<?= esc($page) ?>">
	<?php endif ?>
</form>

<div class="shadow-sm border-0">
	<div class="p-0">
		<?php if (empty($penilaian)): ?>
			<div class="text-center text-muted py-5">
				<i class="bi bi-clipboard-data fs-1"></i>
				<p class="mt-3 fw-semibold">Data tidak ditemukan</p>
			</div>
		<?php else: ?>
			<div class="modern-table-wrapper">
				<div class="scroll-indicator"></div>
				<table class="modern-table" id="tahapMekanismeTable">
					<thead>
						<tr>
							<th class="text-center sticky-col" style="min-width: 80px;">CPL</th>
							<th class="text-center" style="min-width: 180px;">Mata Kuliah</th>
							<th class="text-center" style="min-width: 100px;">CPMK</th>
							<th class="text-center" style="min-width: 100px;">SubCPMK</th>
							<th class="text-center" style="min-width: 140px;">Tahap Penilaian</th>
							<th class="text-center" style="min-width: 140px;">Teknik Penilaian</th>
							<th class="text-center" style="min-width: 140px;">Instrumen</th>
							<th class="text-center" style="min-width: 140px;">Kriteria</th>
							<th class="text-center" style="min-width: 80px;">Bobot</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$rowspan_data = [];
						foreach ($penilaian as $row) {
							$key_cpl_mk = $row['kode_cpl'] . '|' . $row['nama_mk'];
							if (!isset($rowspan_data[$key_cpl_mk])) {
								$rowspan_data[$key_cpl_mk] = 0;
							}
							$rowspan_data[$key_cpl_mk]++;
						}
						$printed_keys = [];
						foreach ($penilaian as $row):
							$key_cpl_mk = $row['kode_cpl'] . '|' . $row['nama_mk'];
						?>
							<tr>
								<?php if (!isset($printed_keys[$key_cpl_mk])): ?>
									<td rowspan="<?= $rowspan_data[$key_cpl_mk] ?>" class="text-center fw-bold sticky-col" style="vertical-align:middle;"><?= esc($row['kode_cpl']) ?></td>
									<td rowspan="<?= $rowspan_data[$key_cpl_mk] ?>" style="vertical-align:middle;white-space:normal;"><?= esc($row['nama_mk']) ?></td>
									<?php $printed_keys[$key_cpl_mk] = true; ?>
								<?php endif ?>

								<td><?= esc($row['kode_cpmk']) ?></td>
								<td><?= esc($row['kode_sub_cpmk'] ?? '-') ?></td>
								<td style="white-space:normal;"><?= $row['tahap_penilaian'] ?></td>
								<td style="white-space:normal;"><?= $row['teknik_penilaian'] ?></td>
								<td style="white-space:normal;"><?= $row['instrumen'] ?></td>
								<td style="white-space:normal;"><?= $row['kriteria_penilaian'] ?></td>
								<td class="text-center"><?= esc($row['bobot']) ?></td>
							</tr>
						<?php endforeach ?>
					</tbody>
					<tfoot>
						<?php foreach ($total_bobot_per_mk as $mk => $total): ?>
							<tr>
								<th colspan="8" class="text-end">Total Bobot (<?= esc($mk) ?>)</th>
								<th class="text-center"><?= $total ?></th>
							</tr>
						<?php endforeach ?>
					</tfoot>
				</table>
			</div>
		<?php endif; ?>
	</div>
</div>

<?php if (isset($totalPages) && $totalPages > 1): ?>
	<nav class="mt-3">
		<ul class="pagination pagination-sm justify-content-center">
			<li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
				<a class="page-link" href="?perPage=<?= $perPage ?>&page=<?= $page - 1 ?>">Previous</a>
			</li>
			<?php for ($i = 1; $i <= $totalPages; $i++): ?>
				<li class="page-item<?= $page == $i ? ' active' : '' ?>">
					<a class="page-link" href="?perPage=<?= $perPage ?>&page=<?= $i ?>"><?= $i ?></a>
				</li>
			<?php endfor ?>
			<li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
				<a class="page-link" href="?perPage=<?= $perPage ?>&page=<?= $page + 1 ?>">Next</a>
			</li>
		</ul>
	</nav>
<?php endif ?>

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

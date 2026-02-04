<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="fw-bold mb-0">Teknik Penilaian CPMK</h2>
	<div class="btn-group">
		<button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
			<i class="bi bi-download"></i> Download
		</button>
		<ul class="dropdown-menu dropdown-menu-end">
			<li>
				<a class="dropdown-item" href="<?= base_url('teknik-penilaian-cpmk/export/pdf') ?>" target="_blank">
					<i class="bi bi-file-pdf text-danger"></i> PDF
				</a>
			</li>
			<li>
				<a class="dropdown-item" href="<?= base_url('teknik-penilaian-cpmk/export/excel') ?>">
					<i class="bi bi-file-earmark-excel text-success"></i> Excel
				</a>
			</li>
		</ul>
	</div>
</div>

<!-- Rows per page -->
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
</form>

<div class="shadow-sm border-0">
	<div class="p-0">
		<?php if (empty($penilaian)): ?>
			<div class="text-center text-muted py-5">
				<i class="bi bi-clipboard-data fs-1"></i>
				<p class="mt-3 fw-semibold">Belum ada data teknik penilaian</p>
			</div>
		<?php else: ?>
			<div class="modern-table-wrapper">
				<div class="scroll-indicator"></div>
				<table class="modern-table" id="teknikPenilaianTable">
					<thead>
						<tr>
							<th class="text-center sticky-col" style="min-width: 80px;">CPL</th>
							<th class="text-center" style="min-width: 180px;">Mata Kuliah</th>
							<th class="text-center" style="min-width: 100px;">CPMK</th>
							<th class="text-center" style="min-width: 100px;">Partisipasi</th>
							<th class="text-center" style="min-width: 100px;">Observasi</th>
							<th class="text-center" style="min-width: 100px;">Unjuk Kerja</th>
							<th class="text-center" style="min-width: 140px;">Case Method/Project Based</th>
							<th class="text-center" style="min-width: 80px;">UTS</th>
							<th class="text-center" style="min-width: 80px;">UAS</th>
							<th class="text-center" style="min-width: 80px;">Lisan</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$cpl_mk_count = [];
						foreach ($penilaian as $row) {
							$key = $row['kode_cpl'] . '|' . $row['nama_mk'];
							if (!isset($cpl_mk_count[$key])) $cpl_mk_count[$key] = 0;
							$cpl_mk_count[$key]++;
						}

						$printed_cpl_mk = [];
						foreach ($penilaian as $row):
							$key = $row['kode_cpl'] . '|' . $row['nama_mk'];
							echo '<tr>';
							if (!isset($printed_cpl_mk[$key])) {
								echo '<td rowspan="' . $cpl_mk_count[$key] . '" class="text-center fw-bold sticky-col" style="vertical-align:middle;">' . esc($row['kode_cpl']) . '</td>';
								echo '<td rowspan="' . $cpl_mk_count[$key] . '" style="vertical-align:middle;white-space:normal;">' . esc($row['nama_mk']) . '</td>';
								$printed_cpl_mk[$key] = 1;
							}
							echo '<td class="text-center">' . esc($row['kode_cpmk']) . '</td>';

							$teknikData = $row['teknik'];
							if (is_string($teknikData)) {
								$teknikData = json_decode($teknikData, true) ?: [];
							}

							$teknik_list = [
								'partisipasi',
								'observasi',
								'unjuk_kerja',
								'case_method',
								'tes_tulis_uts',
								'tes_tulis_uas',
								'tes_lisan'
							];
							foreach ($teknik_list as $teknik) {
								echo '<td class="text-center">';
								if (isset($teknikData[$teknik]) && $teknikData[$teknik]) {
									echo esc($teknikData[$teknik]);
								}
								echo '</td>';
							}
							echo '</tr>';
						endforeach;
						?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
	</div>
</div>

<!-- Server-side Pagination -->
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
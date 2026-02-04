<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="fw-bold mb-0">Bobot Penilaian Berdasarkan CPL</h2>
	<div class="btn-group">
		<button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
			<i class="bi bi-download"></i> Download
		</button>
		<ul class="dropdown-menu dropdown-menu-end">
			<li>
				<a class="dropdown-item" href="<?= base_url('bobot-penilaian-cpl/export/pdf') ?>">
					<i class="bi bi-file-earmark-pdf text-danger"></i> PDF
				</a>
			</li>
			<li>
				<a class="dropdown-item" href="<?= base_url('bobot-penilaian-cpl/export/excel') ?>">
					<i class="bi bi-file-earmark-excel text-success"></i> Excel
				</a>
			</li>
		</ul>
	</div>
</div>

<div class="shadow-sm border-0">
	<div class="p-0">
		<?php if (empty($penilaian)): ?>
			<div class="text-center text-muted py-5">
				<i class="bi bi-clipboard-data fs-1"></i>
				<p class="mt-3 fw-semibold">Belum ada data bobot penilaian</p>
			</div>
		<?php else: ?>
			<div class="modern-table-wrapper">
				<div class="scroll-indicator"></div>
				<table class="modern-table" id="bobotCplTable">
					<thead>
						<tr>
							<th class="text-center sticky-col" style="min-width: 80px;">CPL</th>
							<th class="text-center" style="min-width: 80px;">MK</th>
							<th class="text-center" style="min-width: 180px;">Nama MK</th>
							<th class="text-center" style="min-width: 90px;">CPMK</th>
							<th class="text-center" style="min-width: 100px;">Partisipasi</th>
							<th class="text-center" style="min-width: 100px;">Observasi</th>
							<th class="text-center" style="min-width: 100px;">Unjuk Kerja</th>
							<th class="text-center" style="min-width: 120px;">Project Based</th>
							<th class="text-center" style="min-width: 80px;">UTS</th>
							<th class="text-center" style="min-width: 80px;">UAS</th>
							<th class="text-center" style="min-width: 90px;">Tes Lisan</th>
							<th class="text-center" style="min-width: 80px;">Total</th>
						</tr>
					</thead>
					<?php
					$teknik_list = [
						'partisipasi'   => 'Partisipasi',
						'observasi'     => 'Observasi',
						'unjuk_kerja'   => 'Unjuk Kerja',
						'case_method'   => 'Case Method/Project Based',
						'tes_tulis_uts' => 'UTS',
						'tes_tulis_uas' => 'UAS',
						'tes_lisan'     => 'Tes Lisan'
					];

					$grouped = [];
					foreach ($penilaian as $row) {
						$grouped[$row['kode_cpl']][$row['kode_mk']]['nama_mk'] = $row['nama_mk'];
						$grouped[$row['kode_cpl']][$row['kode_mk']]['items'][] = $row;
					}
					?>
					<tbody>
						<?php
						foreach ($grouped as $kode_cpl => $mks) {
							$rowspan_cpl = array_sum(array_map(fn($mk) => count($mk['items']), $mks));
							$cpl_printed = false;
							foreach ($mks as $kode_mk => $mk) {
								$rowspan_mk = count($mk['items']);
								$mk_printed = false;
								foreach ($mk['items'] as $row) {
									echo '<tr>';
									if (!$cpl_printed) {
										echo '<td rowspan="' . $rowspan_cpl . '" class="align-middle text-center fw-bold sticky-col" style="white-space:nowrap;">' . esc($kode_cpl) . '</td>';
										$cpl_printed = true;
									}
									if (!$mk_printed) {
										echo '<td rowspan="' . $rowspan_mk . '" class="align-middle text-center" style="white-space:nowrap;">' . esc($kode_mk) . '</td>';
										echo '<td rowspan="' . $rowspan_mk . '" class="align-middle text-start" style="white-space:normal;">' . esc($mk['nama_mk']) . '</td>';
										$mk_printed = true;
									}
									echo '<td class="align-middle" style="white-space:nowrap;">' . esc($row['kode_cpmk']) . '</td>';
									$total = 0;
									foreach ($teknik_list as $key => $label) {
										$bobot = isset($row[$key]) ? (int)$row[$key] : 0;
										echo '<td class="text-center">' . ($bobot ? '<b>' . $bobot . '</b>' : '0') . '</td>';
										$total += $bobot;
									}
									echo '<td class="text-center fw-bold">' . $total . '</td>';
									echo '</tr>';
								}
							}
						}
						?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
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

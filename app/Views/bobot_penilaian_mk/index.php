<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="fw-bold mb-0">Bobot Penilaian Berdasarkan MK</h2>
	<div class="btn-group">
		<button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
			<i class="bi bi-download"></i> Download
		</button>
		<ul class="dropdown-menu dropdown-menu-end">
			<li>
				<a class="dropdown-item" href="<?= base_url('bobot-mk/export/pdf') ?>">
					<i class="bi bi-file-earmark-pdf text-danger"></i> PDF
				</a>
			</li>
			<li>
				<a class="dropdown-item" href="<?= base_url('bobot-mk/export/excel') ?>">
					<i class="bi bi-file-earmark-excel text-success"></i> Excel
				</a>
			</li>
		</ul>
	</div>
</div>

<div class="shadow-sm border-0">
	<div class="p-0">
		<?php if (empty($grouped)): ?>
			<div class="text-center text-muted py-5">
				<i class="bi bi-clipboard-data fs-1"></i>
				<p class="mt-3 fw-semibold">Belum ada data bobot penilaian</p>
			</div>
		<?php else: ?>
			<div class="modern-table-wrapper">
				<div class="scroll-indicator"></div>
				<table class="modern-table" id="bobotMkTable">
					<thead>
						<tr>
							<th class="text-center sticky-col" style="min-width: 150px;">MK</th>
							<th class="text-center" style="min-width: 80px;">CPL</th>
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
					<tbody>
						<?php
						foreach ($grouped as $mk => $cpls) {
							$rowspan_mk = array_sum(array_map('count', $cpls));
							$mk_printed = false;
							$total_per_mk = array_fill_keys(array_keys($teknik_list), 0);
							$grand_total_per_mk = 0;
							foreach ($cpls as $cpl => $cpmks) {
								$rowspan_cpl = count($cpmks);
								$cpl_printed = false;
								foreach ($cpmks as $row) {
									$teknik_array = json_decode($row['teknik_penilaian'] ?? '{}', true) ?: [];
									echo '<tr>';
									if (!$mk_printed) {
										echo '<td rowspan="' . $rowspan_mk . '" class="align-middle sticky-col" style="white-space:normal;">' . esc($mk) . '</td>';
										$mk_printed = true;
									}
									if (!$cpl_printed) {
										echo '<td rowspan="' . $rowspan_cpl . '" class="align-middle text-center">' . esc($cpl) . '</td>';
										$cpl_printed = true;
									}
									echo '<td class="align-middle">' . esc($row['kode_cpmk']) . '</td>';

									$total = 0;
									foreach ($teknik_list as $key => $label) {
										$bobot = isset($teknik_array[$key]) ? (int)$teknik_array[$key] : 0;
										echo '<td class="text-center">' . ($bobot ? '<b>' . $bobot . '</b>' : '0') . '</td>';
										$total += $bobot;
										$total_per_mk[$key] += $bobot;
									}
									echo '<td class="text-center fw-bold">' . $total . '</td>';
									$grand_total_per_mk += $total;
									echo '</tr>';
								}
							}
							// Total row per MK
							echo '<tr class="fw-bold text-center" style="background-color: var(--modern-table-header-bg-start);">';
							echo '<td colspan="3" class="sticky-col text-end" style="background-color: var(--modern-table-header-bg-start);">Total</td>';
							foreach ($teknik_list as $key => $label) {
								echo '<td>' . $total_per_mk[$key] . '</td>';
							}
							echo '<td>' . $grand_total_per_mk . '</td>';
							echo '</tr>';
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

<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="d-flex justify-content-between align-items-center mb-4">
	<h2 class="fw-bold mb-0">Pemetaan CPL–BK–MK</h2>
	<div class="btn-group">
		<button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
			<i class="bi bi-download"></i> Download
		</button>
		<ul class="dropdown-menu dropdown-menu-end">
			<li>
				<a class="dropdown-item" href="<?= base_url('admin/cpl-bk-mk/exportExcel') ?>">
					<i class="bi bi-file-earmark-excel text-success"></i> Excel
				</a>
			</li>
			<li>
				<a class="dropdown-item" href="<?= base_url('admin/cpl-bk-mk/exportPdf') ?>">
					<i class="bi bi-file-earmark-pdf text-danger"></i> PDF
				</a>
			</li>
		</ul>
	</div>
</div>

<div class="shadow-sm border-0">
	<div class="p-0">
		<?php if (empty($bk)): ?>
			<div class="text-center text-muted py-5">
				<i class="bi bi-grid-3x3-gap fs-1"></i>
				<p class="mt-3 fw-semibold">Belum ada data Bahan Kajian</p>
				<p class="small">Silakan tambah data Bahan Kajian terlebih dahulu</p>
			</div>
		<?php else: ?>
			<div class="modern-table-wrapper">
				<div class="scroll-indicator"></div>
				<table class="modern-table" id="cplBkMkTable">
					<thead>
						<tr>
							<th class="text-center sticky-col" style="min-width: 120px;">Kode BK</th>
							<?php foreach ($cpl as $c): ?>
								<th class="text-center" style="min-width: 120px;"><?= esc($c['kode_cpl']) ?></th>
							<?php endforeach ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($bk as $b): ?>
							<tr>
								<td class="fw-bold text-center sticky-col" style="white-space: normal;"><?= esc($b['kode_bk']) ?></td>
								<?php foreach ($cpl as $c): ?>
									<td class="text-start" style="white-space: normal;">
										<?php if (isset($mapping[$b['id']][$c['id']])): ?>
											<?= implode(', ', array_map('esc', $mapping[$b['id']][$c['id']])) ?>
										<?php endif ?>
									</td>
								<?php endforeach ?>
							</tr>
						<?php endforeach ?>
					</tbody>
				</table>
			</div>

			<div class="card-footer bg-light border-0 py-3">
				<div class="d-flex align-items-center gap-3 justify-content-center">
					<small class="text-muted">
						<i class="bi bi-info-circle me-1"></i>
						Total: <?= count($bk) ?> Bahan Kajian, <?= count($cpl) ?> CPL
					</small>
				</div>
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

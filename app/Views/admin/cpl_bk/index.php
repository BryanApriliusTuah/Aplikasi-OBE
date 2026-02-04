<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<?php if (session()->getFlashdata('success')): ?>
	<div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
		<?= session()->getFlashdata('success') ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
	<div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
		<?= session()->getFlashdata('error') ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
<?php endif; ?>

<div class="container-fluid px-0">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h2 class="fw-bold mb-0">Pemetaan CPL ke Bahan Kajian</h2>
		<?php if (session('role') === 'admin'): ?>
			<div class="d-flex gap-2">
				<div class="btn-group">
					<button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
						<i class="bi bi-download"></i> Download
					</button>
					<ul class="dropdown-menu dropdown-menu-end">
						<li>
							<a class="dropdown-item" href="<?= base_url('admin/cpl-bk/exportPdf') ?>">
								<i class="bi bi-file-earmark-pdf text-danger"></i> PDF
							</a>
						</li>
						<li>
							<a class="dropdown-item" href="<?= base_url('admin/cpl-bk/exportExcel') ?>">
								<i class="bi bi-file-earmark-excel text-success"></i> Excel
							</a>
						</li>
					</ul>
				</div>
				<a href="<?= base_url('admin/cpl-bk/create') ?>" class="btn btn-primary">
					<i class="bi bi-plus-circle"></i> Tambah Pemetaan
				</a>
			</div>
		<?php endif; ?>
	</div>

	<div class="shadow-sm border-0">
		<div class="p-0">
			<?php if (empty($matrix)): ?>
				<div class="text-center text-muted py-5">
					<i class="bi bi-grid-3x3-gap fs-1"></i>
					<p class="mt-3 fw-semibold">Belum ada data pemetaan</p>
					<p class="small">Silakan tambah pemetaan CPL ke Bahan Kajian</p>
				</div>
			<?php else: ?>
				<div class="modern-table-wrapper">
					<div class="scroll-indicator"></div>
					<table class="modern-table" id="cplBkTable">
						<thead>
							<tr>
								<th class="text-center sticky-col" style="min-width: 120px;">Kode BK</th>
								<?php foreach ($cplList as $cpl): ?>
									<th class="text-center" style="min-width: 90px;"><?= esc($cpl['kode_cpl']) ?></th>
								<?php endforeach ?>
								<th class="text-center" style="min-width: 80px;">Jumlah</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($matrix as $bk): ?>
								<tr>
									<td class="text-center fw-bold sticky-col text-nowrap"><?= esc($bk['kode_bk']) ?></td>
									<?php foreach ($cplList as $cpl): ?>
										<td class="text-center">
											<?php if (!empty($bk['cpl'][$cpl['id']])): ?>
												<?php if (session('role') === 'admin'): ?>
													<button type="button"
														class="btn btn-sm btn-outline-success delete-btn p-0"
														style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;"
														data-action="<?= base_url('admin/cpl-bk/delete/' . $bk['cpl'][$cpl['id']]) ?>"
														data-bk="<?= esc($bk['kode_bk']) ?>"
														data-cpl="<?= esc($cpl['kode_cpl']) ?>"
														title="Klik untuk hapus pemetaan">
														<i class="bi bi-check2-square" style="font-size: 20px;"></i>
													</button>
												<?php else: ?>
													<i class="bi bi-check2-square text-success" style="font-size: 20px;"></i>
												<?php endif; ?>
											<?php endif ?>
										</td>
									<?php endforeach ?>
									<td class="text-center fw-bold"><?= $bk['jumlah'] ?></td>
								</tr>
							<?php endforeach ?>
						</tbody>
					</table>
				</div>

				<div class="card-footer bg-light border-0 py-3">
					<div class="d-flex align-items-center gap-3 justify-content-center">
						<small class="text-muted">
							<i class="bi bi-info-circle me-1"></i>
							Total: <?= count($matrix) ?> Bahan Kajian, <?= count($cplList) ?> CPL
						</small>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<?php if (session('role') === 'admin'): ?>
	<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Konfirmasi Hapus</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					Yakin ingin menghapus pemetaan BK <strong id="modalBkCode"></strong> ke CPL <strong id="modalCplCode"></strong>?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
					<form id="deleteForm" method="post" action="">
						<button type="submit" class="btn btn-danger">Hapus</button>
					</form>
				</div>
			</div>
		</div>
	</div>
<?php endif; ?>

<?= $this->section('js') ?>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Delete modal handler
		<?php if (session('role') === 'admin'): ?>
			const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
			const deleteForm = document.getElementById('deleteForm');
			const modalBkCode = document.getElementById('modalBkCode');
			const modalCplCode = document.getElementById('modalCplCode');

			document.querySelectorAll('.delete-btn').forEach(btn => {
				btn.addEventListener('click', function() {
					const actionUrl = this.getAttribute('data-action');
					const bk = this.getAttribute('data-bk');
					const cpl = this.getAttribute('data-cpl');
					deleteForm.setAttribute('action', actionUrl);
					modalBkCode.textContent = bk;
					modalCplCode.textContent = cpl;
					confirmDeleteModal.show();
				});
			});
		<?php endif; ?>

		// Scroll indicator
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

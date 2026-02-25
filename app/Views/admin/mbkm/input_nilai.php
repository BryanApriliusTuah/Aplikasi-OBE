<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="container-fluid px-4" style="overflow-x: hidden;">
	<div class="row mb-4">
		<div class="col-12">
			<div class="d-flex justify-content-between align-items-center">
				<div>
					<h2 class="fw-bold mb-1">Input Penilaian MBKM</h2>
				</div>
				<div class="d-flex gap-2">
					<a href="<?= base_url('admin/mbkm') ?>" class="btn btn-outline-secondary">
						<i class="bi bi-arrow-left me-2"></i>Kembali
					</a>
				</div>
			</div>
		</div>
	</div>

	<?php if (session()->getFlashdata('success')): ?>
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			<i class="bi bi-check-circle me-2"></i><?= esc(session()->getFlashdata('success')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>

	<?php if (session()->getFlashdata('error')): ?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<i class="bi bi-exclamation-triangle me-2"></i><?= esc(session()->getFlashdata('error')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>

	<!-- Mahasiswa Info -->
	<div class="card border-0 shadow-sm mb-4">
		<div class="card-body bg-light">
			<div class="row g-3">
				<div class="col-md-3">
					<div class="d-flex align-items-center">
						<div>
							<small class="text-muted">NIM</small>
							<h6 class="mb-0 fw-semibold"><?= esc($mahasiswa['nim'] ?? '-') ?></h6>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="d-flex align-items-center">
						<div>
							<small class="text-muted">Nama Mahasiswa</small>
							<h6 class="mb-0 fw-semibold"><?= esc(ucwords(strtolower($mahasiswa['nama_lengkap'] ?? '-'))) ?></h6>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="d-flex align-items-center">
						<div>
							<small class="text-muted">Program Studi</small>
							<h6 class="mb-0 fw-semibold"><?= esc(ucwords(strtolower($program_studi['nama_resmi'])) ?? '-') ?></h6>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="card border-0 shadow-sm">
		<div class="card-body p-0">
			<?php if (empty($konversi_mk)): ?>
				<div class="text-center py-5">
					<div class="mb-4">
						<i class="bi bi-exclamation-triangle display-1 text-warning opacity-25"></i>
					</div>
					<h5 class="text-muted">Data Tidak Tersedia</h5>
					<p class="text-muted mb-4">
						Tidak ditemukan mata kuliah dengan kelas "KM" untuk mahasiswa ini.<br>
						Pastikan jadwal MBKM sudah disinkronisasi dari API.
					</p>
					<a href="<?= base_url('admin/mbkm') ?>" class="btn btn-primary">
						<i class="bi bi-arrow-left me-2"></i>Kembali
					</a>
				</div>
			<?php else: ?>
				<div id="form-alert" class="alert alert-dismissible fade show m-3 d-none" role="alert">
					<i class="bi bi-exclamation-triangle-fill me-2"></i>
					<span id="form-alert-message"></span>
				</div>

				<form action="<?= base_url('admin/mbkm/save-nilai/' . $kegiatan['id']) ?>" method="post" id="nilaiForm">
					<?= csrf_field() ?>

					<div class="bg-light border-bottom p-3">
						<div class="row align-items-center">
							<div class="col-md-8">
								<small class="text-muted">
									<i class="bi bi-info-circle me-1"></i>
									Total: <?= count($konversi_mk) ?> mata kuliah dengan
									<?php
									$total_cpmk = 0;
									foreach ($konversi_mk as $mk) {
										$total_cpmk += count($mk['cpmk_list'] ?? []);
									}
									echo $total_cpmk;
									?> CPMK
								</small>
							</div>
							<div class="col-md-4 text-end">
								<button type="submit" class="btn btn-primary">
									<i class="bi bi-save me-2"></i>Simpan Perubahan
								</button>
							</div>
						</div>
					</div>

					<div class="modern-table-wrapper" style="max-height: 70vh;">
						<div class="scroll-indicator"></div>
						<table class="modern-table" id="nilaiTable">
							<thead>
								<tr>
									<th class="text-center align-middle sticky-col" style="width: 60px; min-width: 60px;" rowspan="2">No</th>
									<th class="align-middle sticky-col" style="width: 150px; min-width: 150px;" rowspan="2">Kode MK</th>
									<th class="align-middle sticky-col" style="min-width: 250px;" rowspan="2">Nama Mata Kuliah</th>
									<th class="text-center align-middle sticky-col" style="width: 100px; min-width: 100px;" rowspan="2">SKS</th>
									<?php
									foreach ($konversi_mk as $mk):
										if (!empty($mk['cpmk_list'])):
											$cpmk_count = count($mk['cpmk_list']);
									?>
											<th class="text-center align-middle bg-secondary bg-opacity-10" colspan="<?= $cpmk_count ?>">
												CPMK - <?= esc($mk['nama_mk']) ?>
											</th>
										<?php
										else:
										?>
											<th class="text-center align-middle bg-danger bg-opacity-10" style="min-width: 220px;">
												<i class="bi bi-x-circle-fill text-danger me-1"></i><?= esc($mk['nama_mk']) ?>
											</th>
									<?php
										endif;
									endforeach;
									?>
								</tr>
								<tr>
									<?php foreach ($konversi_mk as $mk): ?>
										<?php if (!empty($mk['cpmk_list'])): ?>
											<?php foreach ($mk['cpmk_list'] as $cpmk): ?>
												<th class="text-center align-middle" style="width: 120px; min-width: 120px;">
													<div class="d-flex flex-column align-items-center gap-1">
														<div class="d-flex align-items-center gap-1">
															<small class="fw-bold" style="font-size: 0.75rem; line-height: 1.2;">
																<?= esc($cpmk['kode_cpmk']) ?>
															</small>
															<?php if (!empty($cpmk['deskripsi_cpmk'])): ?>
																<i class="bi bi-info-circle text-primary cpmk-info"
																	style="font-size: 0.75rem; cursor: help;"
																	data-bs-toggle="tooltip"
																	data-bs-placement="top"
																	data-bs-html="true"
																	title="<?= esc($cpmk['deskripsi_cpmk']) ?>"></i>
															<?php endif; ?>
														</div>
														<?php
														if ($cpmk['bobot'] > 0) { ?>
															<span class="badge bg-success" style="font-size: 0.65rem;">
																<?= number_format($cpmk['bobot'], 1) ?>%
															</span>
														<?php }
														?>
													</div>
												</th>
											<?php endforeach; ?>
										<?php else: ?>
											<th class="text-center align-middle bg-danger bg-opacity-10" style="min-width: 220px;">
												<small class="text-danger"><i class="bi bi-x-circle me-1"></i>Belum ada CPMK</small>
											</th>
										<?php endif; ?>
									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
								<?php $no = 1;
								foreach ($konversi_mk as $mk_row): ?>
									<tr>
										<td class="text-center align-middle fw-bold text-muted sticky-col">
											<?= $no++ ?>
										</td>
										<td class="align-middle sticky-col">
											<code class="fw-semibold"><?= esc($mk_row['kode_mk']) ?></code>
										</td>
										<td class="align-middle sticky-col">
											<?= esc($mk_row['nama_mk']) ?>
										</td>
										<td class="text-center align-middle sticky-col">
											<span class="badge bg-primary"><?= esc($mk_row['bobot_mk'] ?? 0) ?> SKS</span>
										</td>
										<?php
										// Loop through ALL courses' CPMKs to match header structure
										foreach ($konversi_mk as $mk_header): ?>
											<?php if ($mk_row['mata_kuliah_id'] == $mk_header['mata_kuliah_id']): ?>
												<?php if (empty($mk_header['cpmk_list'])): ?>
													<td class="align-middle p-2 bg-danger bg-opacity-10" style="min-width: 220px;">
														<div class="d-flex align-items-center gap-2 text-danger-emphasis" style="font-size: 0.78rem;">
															<i class="bi bi-x-circle-fill text-danger flex-shrink-0"></i>
															<span>Belum ada pemetaan CPL-CPMK-MK. Tambahkan pemetaan terlebih dahulu.</span>
														</div>
													</td>
												<?php else: ?>
													<?php
													// Check if this MK has any CPMK with bobot > 0 (i.e. RPS exists)
													$mkHasRps = false;
													foreach ($mk_header['cpmk_list'] as $c) {
														if (($c['bobot'] ?? 0) > 0) {
															$mkHasRps = true;
															break;
														}
													}
													?>
													<?php if (!$mkHasRps): ?>
														<td class="align-middle p-2 bg-warning bg-opacity-10" colspan="<?= count($mk_header['cpmk_list']) ?>">
															<div class="d-flex align-items-center gap-2 text-warning-emphasis" style="font-size: 0.78rem;">
																<i class="bi bi-exclamation-triangle-fill text-warning flex-shrink-0"></i>
																<span>RPS belum tersedia untuk mata kuliah ini. Tambahkan RPS terlebih dahulu agar nilai CPMK dapat diinput.</span>
															</div>
														</td>
													<?php else: ?>
														<?php foreach ($mk_header['cpmk_list'] as $cpmk): ?>
															<?php
															$existing_nilai = $existing_scores[$mk_row['mata_kuliah_id']][$cpmk['cpmk_id']] ?? '';
															?>
															<td class="align-middle p-1">
																<input type="text"
																	inputmode="decimal"
																	class="form-control form-control-sm text-center nilai-input"
																	name="nilai_cpmk[<?= $mk_row['mata_kuliah_id'] ?>][<?= $cpmk['cpmk_id'] ?>]"
																	value="<?= esc($existing_nilai) ?>"
																	data-bobot="<?= $cpmk['bobot'] ?>"
																	placeholder="0-100"
																	style="width: max-content; background: transparent; padding: 0;">
															</td>
														<?php endforeach; ?>
													<?php endif; ?>
												<?php endif; ?>
											<?php else: ?>
												<?php if (!empty($mk_header['cpmk_list'])): ?>
													<?php foreach ($mk_header['cpmk_list'] as $cpmk): ?>
														<td class="align-middle p-1 bg-light bg-opacity-50">
															<div class="text-center text-muted" style="font-size: 0.7rem; opacity: 0.3;">—</div>
														</td>
													<?php endforeach; ?>
												<?php else: ?>
													<td class="align-middle p-1 bg-light bg-opacity-50" style="min-width: 220px;">
														<div class="text-center text-muted" style="font-size: 0.7rem; opacity: 0.3;">—</div>
													</td>
												<?php endif; ?>
											<?php endif; ?>
										<?php endforeach; ?>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>

					<div class="card-footer bg-light border-0 py-3">
						<div class="row align-items-center">
							<div class="col-md-8">
								<small class="text-muted" id="saveStatus"></small>
							</div>
						</div>
					</div>
				</form>
			<?php endif; ?>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('css') ?>
<style>
	.sticky-top {
		position: sticky;
		top: 0;
		z-index: 1020;
	}

	.nilai-input:focus {
		border-color: #0d6efd;
		box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
	}

	.nilai-input.is-valid {
		border-color: #198754;
		background-color: #f8fff8;
	}

	.nilai-input.is-invalid {
		border-color: #dc3545;
		background-color: #fff8f8;
	}

	.card {
		transition: all 0.3s ease;
	}

	/* Ensure body doesn't scroll horizontally */
	body {
		overflow-x: hidden;
	}

	/* CPMK info icon styling */
	.cpmk-info {
		transition: color 0.2s ease, transform 0.2s ease;
	}

	.cpmk-info:hover {
		color: #0056b3 !important;
		transform: scale(1.15);
	}

	/* Tooltip styling */
	.tooltip {
		z-index: 9999;
	}

	.tooltip-inner {
		max-width: 300px;
		text-align: left;
		padding: 0.5rem 0.75rem;
	}

	@media (max-width: 768px) {
		.modern-table-wrapper {
			max-height: 60vh !important;
		}
	}
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Initialize Bootstrap tooltips for CPMK descriptions
		const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
		tooltipTriggerList.map(function(tooltipTriggerEl) {
			return new bootstrap.Tooltip(tooltipTriggerEl, {
				boundary: 'window'
			});
		});

		const nilaiInputs = document.querySelectorAll('.nilai-input');
		const formAlertEl = document.getElementById('form-alert');
		const formAlertMessageEl = document.getElementById('form-alert-message');

		function showFormAlert(message, type = 'warning') {
			formAlertMessageEl.innerHTML = message;
			formAlertEl.className = `alert alert-${type} alert-dismissible fade show m-3`;
			formAlertEl.classList.remove('d-none');
			formAlertEl.scrollIntoView({
				behavior: 'smooth',
				block: 'start'
			});
		}

		function hideFormAlert() {
			formAlertEl.classList.add('d-none');
		}

		setInterval(hideFormAlert, 10000);

		function validateInput(input) {
			const rawValue = input.value.trim();
			input.classList.remove('is-valid', 'is-invalid');

			if (rawValue === '') {
				return;
			}
			const normalizedValue = rawValue.replace(',', '.');
			const hasInvalidChars = /[^0-9.]/.test(normalizedValue);
			const hasMultipleDots = (normalizedValue.match(/\./g) || []).length > 1;

			if (hasInvalidChars || hasMultipleDots) {
				input.classList.add('is-invalid');
				return;
			}
			const value = parseFloat(normalizedValue);
			if (!isNaN(value) && value >= 0 && value <= 100) {
				input.classList.add('is-valid');
			} else {
				input.classList.add('is-invalid');
			}
		}

		nilaiInputs.forEach(input => {
			input.addEventListener('input', function() {
				validateInput(this);
			});
			validateInput(input);
		});

		// Form Submission Validation Logic
		document.getElementById('nilaiForm').addEventListener('submit', function(e) {
			hideFormAlert();

			// Check for invalid inputs
			const invalidInputs = document.querySelectorAll('.nilai-input.is-invalid');
			if (invalidInputs.length > 0) {
				e.preventDefault();
				showFormAlert('Terdapat nilai yang tidak valid. Pastikan semua nilai adalah angka antara 0-100.', 'danger');
				invalidInputs[0].focus();
				return;
			}

			// Show loading state
			const submitButtons = document.querySelectorAll('button[type="submit"]');
			submitButtons.forEach(btn => {
				btn.disabled = true;
				btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Menyimpan...';
			});
		});
	});

	function updateSaveStatus(message) {
		const statusEl = document.getElementById('saveStatus');
		if (statusEl) {
			statusEl.textContent = message;
			statusEl.style.color = '#198754';
			setTimeout(() => {
				statusEl.textContent = '';
			}, 3000);
		}
	}

	// Handle scroll indicator for modern table
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

		// Check on load and resize
		checkScroll();
		window.addEventListener('resize', checkScroll);
		tableWrapper.addEventListener('scroll', checkScroll);
	}

	// Dynamic sticky column positioning
	const table = document.getElementById('nilaiTable');
	if (table) {
		function updateStickyPositions() {
			// Get all sticky columns from the first row (header)
			const headerRow = table.querySelector('thead tr');
			if (!headerRow) return;

			const stickyColumns = headerRow.querySelectorAll('.sticky-col');
			let cumulativeLeft = 0;

			stickyColumns.forEach((col, index) => {
				// Set the left position for this column
				const varName = `--sticky-col-${index + 1}-left`;
				table.style.setProperty(varName, `${cumulativeLeft}px`);

				// Add this column's width to the cumulative total for the next column
				cumulativeLeft += col.offsetWidth;
			});
		}

		// Update positions on load
		updateStickyPositions();

		// Update on window resize with debouncing for performance
		let resizeTimeout;
		window.addEventListener('resize', function() {
			clearTimeout(resizeTimeout);
			resizeTimeout = setTimeout(updateStickyPositions, 100);
		});

		// Update after fonts load (can affect column widths)
		if (document.fonts && document.fonts.ready) {
			document.fonts.ready.then(updateStickyPositions);
		}
	}
</script>
<?= $this->endSection() ?>
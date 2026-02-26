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
									Masukkan satu nilai per mata kuliah. Nilai akan didistribusikan ke semua CPMK sesuai bobot masing-masing.
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
									<th class="text-center" style="width: 60px; min-width: 60px;">No</th>
									<th style="width: 130px; min-width: 130px;">Kode MK</th>
									<th style="min-width: 220px;">Nama Mata Kuliah</th>
									<th class="text-center" style="width: 80px; min-width: 80px;">SKS</th>
									<th style="min-width: 280px;">CPMK &amp; Bobot</th>
									<th class="text-center" style="width: 140px; min-width: 140px;">Nilai (0–100)</th>
								</tr>
							</thead>
							<tbody>
								<?php $no = 1;
								foreach ($konversi_mk as $mk_row): ?>
									<?php
									$mk_id = $mk_row['mata_kuliah_id'];
									$cpmk_list = $mk_row['cpmk_list'] ?? [];

									// Determine if this MK is inputtable
									$mkHasRps = false;
									foreach ($cpmk_list as $c) {
										if (($c['bobot'] ?? 0) > 0) {
											$mkHasRps = true;
											break;
										}
									}

									$existing_nilai = $existing_scores[$mk_id] ?? '';
									?>
									<tr>
										<td class="text-center fw-bold text-muted"><?= $no++ ?></td>
										<td><code class="fw-semibold"><?= esc($mk_row['kode_mk']) ?></code></td>
										<td><?= esc($mk_row['nama_mk']) ?></td>
										<td class="text-center">
											<span class="badge bg-primary"><?= esc($mk_row['bobot_mk'] ?? 0) ?> SKS</span>
										</td>
										<td>
											<?php if (empty($cpmk_list)): ?>
												<div class="d-flex align-items-center gap-2 text-danger" style="font-size: 0.78rem;">
													<i class="bi bi-x-circle-fill flex-shrink-0"></i>
													<span>Belum ada pemetaan CPL-CPMK-MK.</span>
												</div>
											<?php elseif (!$mkHasRps): ?>
												<div class="d-flex align-items-center gap-2 text-warning-emphasis" style="font-size: 0.78rem;">
													<i class="bi bi-exclamation-triangle-fill text-warning flex-shrink-0"></i>
													<span>RPS belum tersedia. Tambahkan RPS terlebih dahulu.</span>
												</div>
											<?php else: ?>
												<div class="d-flex flex-wrap gap-1">
													<?php foreach ($cpmk_list as $cpmk): ?>
														<?php if (($cpmk['bobot'] ?? 0) > 0): ?>
															<span class="badge bg-secondary bg-opacity-75 d-flex align-items-center gap-1"
																style="font-size: 0.7rem; font-weight: 500;"
																<?php if (!empty($cpmk['deskripsi_cpmk'])): ?>
																data-bs-toggle="tooltip"
																data-bs-placement="top"
																title="<?= esc($cpmk['deskripsi_cpmk']) ?>"
																<?php endif; ?>>
																<?= esc($cpmk['kode_cpmk']) ?>
																<span class="badge bg-success ms-1" style="font-size: 0.6rem;"><?= number_format($cpmk['bobot'], 1) ?>%</span>
															</span>
														<?php endif; ?>
													<?php endforeach; ?>
												</div>
											<?php endif; ?>
										</td>
										<td class="text-center">
											<?php if (!empty($cpmk_list) && $mkHasRps): ?>
												<input type="text"
													inputmode="decimal"
													class="form-control form-control-sm text-center nilai-input mx-auto"
													name="nilai_mk[<?= $mk_id ?>]"
													value="<?= esc($existing_nilai) ?>"
													placeholder="0-100"
													style="max-width: 90px;">
											<?php else: ?>
												<span class="text-muted">—</span>
											<?php endif; ?>
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div><!-- /.modern-table-wrapper -->

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
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Initialize Bootstrap tooltips
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

			if (rawValue === '') return;

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

		document.getElementById('nilaiForm').addEventListener('submit', function(e) {
			hideFormAlert();

			const invalidInputs = document.querySelectorAll('.nilai-input.is-invalid');
			if (invalidInputs.length > 0) {
				e.preventDefault();
				showFormAlert('Terdapat nilai yang tidak valid. Pastikan semua nilai adalah angka antara 0-100.', 'danger');
				invalidInputs[0].focus();
				return;
			}

			const submitButtons = document.querySelectorAll('button[type="submit"]');
			submitButtons.forEach(btn => {
				btn.disabled = true;
				btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Menyimpan...';
			});
		});
	});
</script>
<?= $this->endSection() ?>
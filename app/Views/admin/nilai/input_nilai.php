<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
	<div class="row mb-4">
		<div class="col-12">
			<nav aria-label="breadcrumb" class="mb-3">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">
						<a href="<?= base_url('admin/nilai') ?>" class="text-decoration-none">
							<i class="bi bi-house-door me-1"></i>Penilaian
						</a>
					</li>
					<li class="breadcrumb-item active">Input Nilai</li>
				</ol>
			</nav>

			<div class="d-flex justify-content-between align-items-center">
				<div>
					<h2 class="fw-bold text-primary mb-1">Input Nilai Mahasiswa</h2>
					<p class="text-muted mb-0">Masukkan nilai CPMK untuk setiap mahasiswa</p>
				</div>
				<a href="<?= base_url('admin/nilai') ?>" class="btn btn-outline-secondary">
					<i class="bi bi-arrow-left me-2"></i>Kembali
				</a>
			</div>
		</div>
	</div>

	<div class="card border-0 shadow-sm mb-4">
		<div class="card-body bg-light">
			<div class="row g-3">
				<div class="col-md-4">
					<div class="d-flex align-items-center">
						<div class="bg-primary bg-opacity-10 rounded-circle p-2 me-3">
							<i class="bi bi-book text-primary fs-5"></i>
						</div>
						<div>
							<small class="text-muted">Mata Kuliah</small>
							<h6 class="mb-0 fw-semibold"><?= esc($jadwal['nama_mk']) ?></h6>
						</div>
					</div>
				</div>
				<div class="col-md-2">
					<div class="d-flex align-items-center">
						<div class="bg-success bg-opacity-10 rounded-circle p-2 me-3">
							<i class="bi bi-people text-success fs-5"></i>
						</div>
						<div>
							<small class="text-muted">Kelas</small>
							<h6 class="mb-0 fw-semibold"><?= esc($jadwal['kelas']) ?></h6>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="d-flex align-items-center">
						<div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
							<i class="bi bi-calendar text-info fs-5"></i>
						</div>
						<div>
							<small class="text-muted">Tahun Akademik</small>
							<h6 class="mb-0 fw-semibold"><?= esc($jadwal['tahun_akademik']) ?></h6>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="d-flex align-items-center">
						<div class="bg-warning bg-opacity-10 rounded-circle p-2 me-3">
							<i class="bi bi-person-badge text-warning fs-5"></i>
						</div>
						<div>
							<small class="text-muted">Dosen Pengampu</small>
							<h6 class="mb-0 fw-semibold"><?= esc($jadwal['dosen_ketua'] ?? 'N/A') ?></h6>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- CPMK Weight Information Box -->
	<?php if (!empty($cpmk_list)): ?>
		<div class="card border-0 shadow-sm mb-4">
			<div class="card-header bg-info bg-opacity-10 border-0 py-3">
				<h6 class="mb-0 text-info">
					<i class="bi bi-calculator me-2"></i>Informasi Bobot CPMK (dari RPS Mingguan)
				</h6>
			</div>
			<div class="card-body">
				<div class="row g-3">
					<?php foreach ($cpmk_list as $cpmk): ?>
						<div class="col-md-3 col-sm-6">
							<div class="d-flex align-items-center p-2 bg-light rounded">
								<div class="bg-info bg-opacity-25 rounded p-2 me-3">
									<i class="bi bi-percent text-info"></i>
								</div>
								<div>
									<div class="fw-bold text-dark"><?= esc($cpmk['kode_cpmk']) ?></div>
									<div class="text-muted small">
										Bobot: <span class="fw-semibold text-info"><?= number_format($cpmk['bobot_cpmk'], 1) ?>%</span>
									</div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
				
				<hr class="my-3">
				
				<div class="d-flex align-items-center justify-content-between">
					<div>
						<strong class="text-dark">Total Bobot:</strong> 
						<span class="fs-5 fw-bold <?= abs($total_weight - 100) > 0.01 ? 'text-warning' : 'text-success' ?>">
							<?= number_format($total_weight, 1) ?>%
						</span>
					</div>
					
					<?php if (abs($total_weight - 100) > 0.01): ?>
						<div class="alert alert-warning mb-0 py-2 px-3" role="alert">
							<i class="bi bi-exclamation-triangle-fill me-2"></i>
							<small>
								<strong>Perhatian:</strong> Total bobot tidak 100%. 
								Sistem akan menggunakan proporsi relatif dalam perhitungan nilai akhir.
							</small>
						</div>
					<?php else: ?>
						<div class="alert alert-success mb-0 py-2 px-3" role="alert">
							<i class="bi bi-check-circle-fill me-2"></i>
							<small><strong>Bobot valid.</strong> Total bobot = 100%</small>
						</div>
					<?php endif; ?>
				</div>

				<?php if ($total_weight == 0): ?>
					<div class="alert alert-danger mt-3 mb-0" role="alert">
						<i class="bi bi-x-circle-fill me-2"></i>
						<strong>Bobot belum diatur!</strong> 
						Tidak ada bobot CPMK yang ditemukan dari RPS Mingguan. 
						Sistem akan menggunakan rata-rata sederhana untuk perhitungan nilai akhir.
						<br>
						<small class="mt-2 d-block">
							<i class="bi bi-info-circle me-1"></i>
							Harap lengkapi RPS Mingguan untuk mata kuliah ini dengan mengisi bobot pada setiap minggu pembelajaran.
						</small>
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<div class="card border-0 shadow-sm">
		<div class="card-header bg-primary text-white py-3">
			<div class="d-flex justify-content-between align-items-center">
				<div>
					<h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Form Input Nilai</h5>
					<small class="opacity-75">Masukkan nilai antara 0-100 untuk setiap CPMK</small>
				</div>
				<?php if (!empty($mahasiswa_list) && !empty($cpmk_list)): ?>
					<div class="text-end">
						<small class="opacity-75">
							<?= count($mahasiswa_list) ?> Mahasiswa | <?= count($cpmk_list) ?> CPMK
						</small>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="card-body p-0">
			<?php if (empty($mahasiswa_list) || empty($cpmk_list)): ?>
				<div class="text-center py-5">
					<div class="mb-4">
						<i class="bi bi-exclamation-triangle display-1 text-warning opacity-25"></i>
					</div>
					<h5 class="text-muted">Data Tidak Tersedia</h5>
					<p class="text-muted mb-4">
						Tidak ditemukan data mahasiswa atau CPMK untuk mata kuliah ini.<br>
						Pastikan CPMK sudah dipetakan dan ada data mahasiswa di program studi terkait.
					</p>
					<a href="<?= base_url('admin/nilai') ?>" class="btn btn-primary">
						<i class="bi bi-arrow-left me-2"></i>Kembali ke Jadwal
					</a>
				</div>
			<?php else: ?>
				<div id="form-alert" class="alert alert-dismissible fade show m-3 d-none" role="alert">
					<i class="bi bi-exclamation-triangle-fill me-2"></i>
					<span id="form-alert-message"></span>
				</div>

				<form action="<?= base_url('admin/nilai/save-nilai/' . $jadwal['id']) ?>" method="post" id="nilaiForm">
					<?= csrf_field() ?>

					<div class="bg-light border-bottom p-3">
						<div class="row align-items-center">
							<div class="col-md-8">
								<div class="d-flex gap-2 flex-wrap">
									<button type="button" class="btn btn-outline-secondary btn-sm" onclick="fillAllValues(0)">
										<i class="bi bi-arrow-down-circle me-1"></i>Set Semua 0
									</button>
									<button type="button" class="btn btn-outline-secondary btn-sm" onclick="fillAllValues(75)">
										<i class="bi bi-arrow-up-circle me-1"></i>Set Semua 75
									</button>
									<button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearAllValues()">
										<i class="bi bi-eraser me-1"></i>Kosongkan Semua
									</button>
								</div>
							</div>
							<div class="col-md-4 text-end">
								<button type="submit" class="btn btn-primary">
									<i class="bi bi-save me-2"></i>Simpan Semua Nilai
								</button>
							</div>
						</div>
					</div>

					<div class="table-responsive" style="max-height: 70vh; overflow-y: auto;">
						<table class="table table-hover mb-0" id="nilaiTable">
							<thead class="table-dark sticky-top">
								<tr>
									<th class="text-center align-middle" style="width: 60px; min-width: 60px;">No</th>
									<th class="align-middle" style="width: 130px; min-width: 130px;">
										<div class="d-flex align-items-center">
											<i class="bi bi-hash me-2"></i>NIM
										</div>
									</th>
									<th class="align-middle" style="min-width: 200px;">
										<div class="d-flex align-items-center">
											<i class="bi bi-person me-2"></i>Nama Mahasiswa
										</div>
									</th>
									<?php foreach ($cpmk_list as $cpmk) : ?>
										<th class="text-center align-middle" style="width: 130px; min-width: 130px;"
											title="<?= esc($cpmk['deskripsi']) ?>" data-bs-toggle="tooltip">
											<div class="d-flex flex-column align-items-center">
												<span class="fw-bold"><?= esc($cpmk['kode_cpmk']) ?></span>
												<small class="opacity-75">(0-100)</small>
												<span class="badge bg-info mt-1" style="font-size: 0.7rem;">
													<?= number_format($cpmk['bobot_cpmk'], 0) ?>%
												</span>
											</div>
										</th>
									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($mahasiswa_list as $index => $mahasiswa) : ?>
									<tr class="<?= $index % 2 === 0 ? 'bg-light bg-opacity-50' : '' ?>">
										<td class="text-center align-middle fw-bold text-muted">
											<?= $index + 1 ?>
										</td>
										<td class="align-middle">
											<span class="fw-semibold text-primary"><?= esc($mahasiswa['nim']) ?></span>
										</td>
										<td class="align-middle">
											<div class="d-flex align-items-center">
												<span><?= esc($mahasiswa['nama_lengkap']) ?></span>
											</div>
										</td>
										<?php foreach ($cpmk_list as $cpmk) : ?>
											<td class="align-middle">
												<div class="input-group input-group-sm">
													<input
														type="text"
														inputmode="decimal"
														class="form-control text-center nilai-input"
														name="nilai[<?= $mahasiswa['id'] ?>][<?= $cpmk['id'] ?>]"
														value="<?= esc($existing_scores[$mahasiswa['id']][$cpmk['id']] ?? '') ?>"
														data-mahasiswa="<?= $mahasiswa['id'] ?>"
														data-cpmk="<?= $cpmk['id'] ?>"
														placeholder="0-100">
												</div>
											</td>
										<?php endforeach; ?>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>

					<div class="card-footer bg-light border-0 py-3">
						<div class="row align-items-center">
							<div class="col-md-8">
								<div class="d-flex align-items-center gap-3">
									<small class="text-muted">
										<i class="bi bi-info-circle me-1"></i>
										Total: <?= count($mahasiswa_list) ?> mahasiswa dengan <?= count($cpmk_list) ?> CPMK
									</small>
									<small class="text-muted" id="saveStatus"></small>
								</div>
							</div>
							<div class="col-md-4 text-end">
								<button type="submit" class="btn btn-primary btn-lg">
									<i class="bi bi-cloud-upload me-2"></i>Simpan Semua Nilai
								</button>
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

	.table-responsive {
		border: 1px solid #dee2e6;
		border-radius: 0;
	}

	.card {
		transition: all 0.3s ease;
	}

	@media (max-width: 768px) {
		.table-responsive {
			max-height: 60vh;
		}
	}
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Initialize tooltips
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
		var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
			return new bootstrap.Tooltip(tooltipTriggerEl);
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

		setInterval(hideFormAlert, 10000); // Auto-hide alert after 10 seconds

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
			hideFormAlert(); // Hide previous alerts on new submission attempt

			// 1. Check for invalid inputs (e.g., letters, out of range)
			const invalidInputs = document.querySelectorAll('.nilai-input.is-invalid');
			if (invalidInputs.length > 0) {
				e.preventDefault();
				showFormAlert('Terdapat nilai yang tidak valid. Pastikan semua nilai adalah angka antara 0-100.', 'danger');
				invalidInputs[0].focus();
				return;
			}

			// 2. Check for empty inputs
			const allInputs = document.querySelectorAll('.nilai-input');
			const emptyInputs = Array.from(allInputs).filter(input => input.value.trim() === '');
			if (emptyInputs.length > 0) {
				e.preventDefault();
				showFormAlert(`Terdapat <strong>${emptyInputs.length}</strong> kolom nilai yang masih kosong. Harap isi semua nilai sebelum menyimpan.`, 'warning');
				emptyInputs[0].focus();
				return;
			}

			// 3. If all checks pass, show loading state
			const submitButtons = document.querySelectorAll('button[type="submit"]');
			submitButtons.forEach(btn => {
				btn.disabled = true;
				btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Menyimpan...';
			});
		});
	});

	// Utility functions for bulk operations
	function fillAllValues(value) {
		const inputs = document.querySelectorAll('.nilai-input');
		inputs.forEach(input => {
			input.value = value;
			input.dispatchEvent(new Event('input'));
		});
		updateSaveStatus(`Semua nilai diatur ke ${value}`);
	}

	function clearAllValues() {
		if (confirm('Apakah Anda yakin ingin mengosongkan semua nilai?')) {
			const inputs = document.querySelectorAll('.nilai-input');
			inputs.forEach(input => {
				input.value = '';
				input.classList.remove('is-valid', 'is-invalid');
			});
			updateSaveStatus('Semua nilai dikosongkan.');
		}
	}

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
</script>
<?= $this->endSection() ?>
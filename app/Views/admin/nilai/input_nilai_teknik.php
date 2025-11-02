<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4" style="overflow-x: hidden;">
	<div class="row mb-4">
		<div class="col-12">
			<nav aria-label="breadcrumb" class="mb-3">
				<ol class="breadcrumb">
					<li class="breadcrumb-item">
						<a href="<?= base_url('admin/nilai') ?>" class="text-decoration-none">
							<i class="bi bi-house-door me-1"></i>Penilaian
						</a>
					</li>
					<li class="breadcrumb-item active">Input Nilai by Teknik Penilaian</li>
				</ol>
			</nav>

			<div class="d-flex justify-content-between align-items-center">
				<div>
					<h2 class="fw-bold mb-1">Input Nilai Berdasarkan Teknik Penilaian</h2>
					<p class="text-muted mb-0">Masukkan nilai untuk setiap teknik penilaian (Kehadiran, Tugas, UTS, UAS, dll)</p>
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

	<!-- Quick Info & RPS Link -->
	<?php if (!empty($teknik_by_tahap)): ?>
		<div class="card border-0 shadow-sm mb-4">
			<div class="card-body">
				<div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
					<div class="d-flex align-items-center flex-grow-1">
						<div class="bg-info bg-opacity-10 rounded-circle p-2 me-3 flex-shrink-0">
							<i class="bi bi-lightbulb-fill text-info fs-5"></i>
						</div>
						<div>
							<h6 class="fw-bold mb-1">Penilaian Otomatis (Combined Mode)</h6>
							<small class="text-muted">
								Sistem menghitung <strong>Nilai CPMK = Σ(Bobot × Nilai)</strong> dari <?= count($combined_list) ?> teknik penilaian (digabung) dalam <?= count($teknik_by_tahap) ?> tahap
							</small>
						</div>
					</div>
					<div class="d-flex gap-2 flex-shrink-0">
						<?php
						// Get RPS ID from the first combined teknik item
						$db = \Config\Database::connect();
						$first_rps_mingguan_id = $combined_list[0]['rps_mingguan_ids'][0]['rps_mingguan_id'] ?? null;
						$rps_id = null;
						if ($first_rps_mingguan_id) {
							$first_rps_mingguan = $db->table('rps_mingguan')
								->select('rps_id')
								->where('id', $first_rps_mingguan_id)
								->get()
								->getRowArray();
							$rps_id = $first_rps_mingguan['rps_id'] ?? null;
						}
						?>
						<?php if ($rps_id): ?>
							<a href="<?= base_url('rps/preview/' . $rps_id) ?>"
							   class="btn btn-sm btn-outline-primary"
							   target="_blank"
							   title="Lihat RPS">
								<i class="bi bi-file-text"></i>
								<span class="d-none d-lg-inline ms-1">RPS</span>
							</a>
							<a href="<?= base_url('rps/mingguan/' . $rps_id) ?>"
							   class="btn btn-sm btn-outline-secondary"
							   target="_blank"
							   title="Kelola RPS Mingguan">
								<i class="bi bi-calendar-week"></i>
								<span class="d-none d-lg-inline ms-1">Mingguan</span>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>

	<div class="card border-0 shadow-sm" style="overflow: hidden;">
		<div class="card-header bg-primary text-white py-3">
			<div class="d-flex justify-content-between align-items-center">
				<div>
					<h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Form Input Nilai Teknik Penilaian</h5>
					<small class="opacity-75">Masukkan nilai antara 0-100 untuk setiap teknik penilaian</small>
				</div>
				<?php if (!empty($mahasiswa_list) && !empty($combined_list)): ?>
					<div class="text-end">
						<small class="opacity-75">
							<?= count($mahasiswa_list) ?> Mahasiswa | <?= count($combined_list) ?> Teknik Penilaian (Combined)
						</small>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<div class="card-body p-0" style="overflow: hidden;">
			<?php if (empty($mahasiswa_list) || empty($combined_list)): ?>
				<div class="text-center py-5">
					<div class="mb-4">
						<i class="bi bi-exclamation-triangle display-1 text-warning opacity-25"></i>
					</div>
					<h5 class="text-muted">Data Tidak Tersedia</h5>
					<p class="text-muted mb-4">
						Tidak ditemukan data mahasiswa atau teknik penilaian untuk mata kuliah ini.<br>
						Pastikan RPS Mingguan sudah dilengkapi dengan teknik penilaian dan bobotnya.
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

				<form action="<?= base_url('admin/nilai/save-nilai-teknik/' . $jadwal['id']) ?>" method="post" id="nilaiForm">
					<?= csrf_field() ?>

					<div class="bg-light border-bottom p-3">
						<div class="row align-items-center">
							<div class="col-md-8">
								<div class="d-flex gap-2 flex-wrap">
									<button type="button" class="btn btn-success btn-sm" onclick="fillAllValues()">
										<i class="bi bi-lightning-fill me-1"></i>Isi Semua (Testing)
									</button>
									<button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearAllValues()">
										<i class="bi bi-eraser me-1"></i>Kosongkan Semua
									</button>
								</div>
							</div>
							<div class="col-md-4 text-end">
								<button type="submit" class="btn btn-primary">
									<i class="bi bi-save me-2"></i>Simpan & Hitung CPMK
								</button>
							</div>
						</div>
					</div>

					<div class="table-responsive" style="max-height: 70vh; overflow: auto;">
						<table class="table table-hover table-bordered mb-0 table-sm" id="nilaiTable">
							<thead class="table-dark sticky-top">
								<tr>
									<th class="text-center align-middle" style="width: 60px; min-width: 60px;" rowspan="2">No</th>
									<th class="align-middle" style="width: 130px; min-width: 130px;" rowspan="2">
										<div class="d-flex align-items-center">
											<i class="bi bi-hash me-2"></i>NIM
										</div>
									</th>
									<th class="align-middle" style="min-width: 200px;" rowspan="2">
										<div class="d-flex align-items-center">
											<i class="bi bi-person me-2"></i>Nama Mahasiswa
										</div>
									</th>
									<?php
									$tahap_count = count($teknik_by_tahap);
									$tahap_index = 0;
									?>
									<?php foreach ($teknik_by_tahap as $tahap => $tahap_data): ?>
										<?php
										$tahap_index++;
										$is_last_tahap = ($tahap_index === $tahap_count);
										?>
										<th class="text-center align-middle bg-primary bg-opacity-25 <?= $is_last_tahap ? '' : 'tahap-border-right' ?>" colspan="<?= count($tahap_data['items']) ?>">
											<?= esc($tahap) ?>
										</th>
									<?php endforeach; ?>
								</tr>
								<tr>
									<?php
									$tahap_keys = array_keys($teknik_by_tahap);
									$last_tahap_key = end($tahap_keys);
									?>
									<?php foreach ($teknik_by_tahap as $tahap => $tahap_data): ?>
										<?php
										$item_count = count($tahap_data['items']);
										$item_index = 0;
										$is_last_tahap_group = ($tahap === $last_tahap_key);
										?>
										<?php foreach ($tahap_data['items'] as $item): ?>
											<?php
											$item_index++;
											$is_last_in_group = ($item_index === $item_count);
											$show_border = $is_last_in_group && !$is_last_tahap_group;
											// Build tooltip with week details
											$weeks = array_map(function($rps) { return $rps['minggu']; }, $item['rps_mingguan_ids']);
											$weeks_display = count($weeks) > 5 ? 'W' . min($weeks) . '-' . max($weeks) : implode(',', $weeks);
											$tooltip = esc($item['teknik_label']) . " - Minggu " . implode(', ', $weeks) . " (" . number_format($item['total_bobot'], 1) . "%)";
											?>
											<th class="text-center align-middle <?= $show_border ? 'tahap-border-right' : '' ?>" style="width: 110px; min-width: 110px;"
												title="<?= $tooltip ?>"
												data-bs-toggle="tooltip">
												<div class="d-flex flex-column align-items-center">
													<small class="fw-bold" style="font-size: 0.75rem; line-height: 1.2;">
														<?php
														// Abbreviate long names
														$label = $item['teknik_label'];
														if (strlen($label) > 20) {
															$label = substr($label, 0, 17) . '...';
														}
														echo esc($label);
														?>
													</small>
													<small class="opacity-75" style="font-size: 0.65rem;">
														Minggu: <?= $weeks_display ?>
													</small>
													<span class="badge bg-success" style="font-size: 0.65rem;">
														<?= number_format($item['total_bobot'], 1) ?>%
													</span>
												</div>
											</th>
										<?php endforeach; ?>
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
										<?php
										// Build a map to know which columns are the last in their tahap group (but not the very last group)
										$last_in_group = [];
										$tahap_keys = array_keys($teknik_by_tahap);
										$last_tahap_key = end($tahap_keys);
										foreach ($teknik_by_tahap as $tahap => $tahap_data) {
											$items = $tahap_data['items'];
											if (!empty($items) && $tahap !== $last_tahap_key) {
												$last_item = end($items);
												$last_in_group[$last_item['teknik_key']] = true;
											}
										}
										?>
										<?php foreach ($combined_list as $item) : ?>
											<?php $is_last = isset($last_in_group[$item['teknik_key']]); ?>
											<td class="align-middle p-1 <?= $is_last ? 'tahap-border-right' : '' ?>">
												<input
													type="text"
													inputmode="decimal"
													class="form-control form-control-sm text-center nilai-input"
													name="nilai[<?= $mahasiswa['id'] ?>][<?= $item['teknik_key'] ?>]"
													value="<?= esc($existing_scores[$mahasiswa['id']][$item['teknik_key']] ?? '') ?>"
													data-mahasiswa="<?= $mahasiswa['id'] ?>"
													data-teknik="<?= $item['teknik_key'] ?>"
													placeholder="0-100"
													style="min-width: 85px;">
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
										Total: <?= count($mahasiswa_list) ?> mahasiswa dengan <?= count($combined_list) ?> teknik penilaian (combined mode)
									</small>
									<small class="text-muted" id="saveStatus"></small>
								</div>
							</div>
							<div class="col-md-4 text-end">
								<button type="submit" class="btn btn-primary btn-lg">
									<i class="bi bi-cloud-upload me-2"></i>Simpan & Hitung CPMK
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
		overflow-x: auto !important;
		overflow-y: auto !important;
		-webkit-overflow-scrolling: touch;
		width: 100%;
		max-width: 100%;
	}

	/* Prevent table from expanding container */
	#nilaiTable {
		width: max-content;
		max-width: none;
	}

	/* Sticky first columns for better UX */
	#nilaiTable thead th:nth-child(1),
	#nilaiTable thead th:nth-child(2),
	#nilaiTable thead th:nth-child(3),
	#nilaiTable tbody td:nth-child(1),
	#nilaiTable tbody td:nth-child(2),
	#nilaiTable tbody td:nth-child(3) {
		position: sticky;
		background-color: white;
		z-index: 10;
	}

	#nilaiTable thead th:nth-child(1),
	#nilaiTable tbody td:nth-child(1) {
		left: 0;
	}

	#nilaiTable thead th:nth-child(2),
	#nilaiTable tbody td:nth-child(2) {
		left: 60px;
	}

	#nilaiTable thead th:nth-child(3),
	#nilaiTable tbody td:nth-child(3) {
		left: 190px;
	}

	#nilaiTable thead th {
		z-index: 20;
		background-color: #212529;
	}

	#nilaiTable tbody tr:nth-of-type(even) td:nth-child(-n+3) {
		background-color: rgba(0, 0, 0, 0.05);
	}

	.card {
		transition: all 0.3s ease;
	}

	/* Border to separate tahap penilaian groups */
	#nilaiTable th.tahap-border-right,
	#nilaiTable td.tahap-border-right {
		border-right: 4px solid #ffc107 !important;
	}

	/* Ensure body doesn't scroll horizontally */
	body {
		overflow-x: hidden;
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
				btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>Menyimpan & Menghitung...';
			});
		});
	});

	function fillAllValues() {
		if (confirm('Apakah Anda yakin ingin mengisi semua nilai dengan data testing (70-95)?')) {
			const inputs = document.querySelectorAll('.nilai-input');
			let filledCount = 0;
			inputs.forEach(input => {
				// Generate random score between 70-95 for realistic test data
				const randomScore = Math.floor(Math.random() * (95 - 70 + 1)) + 70;
				input.value = randomScore;
				input.classList.remove('is-invalid');
				input.classList.add('is-valid');
				filledCount++;
			});
			updateSaveStatus(`${filledCount} nilai berhasil diisi dengan data testing.`);
		}
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

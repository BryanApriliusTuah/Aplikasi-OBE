<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="container-fluid px-4" style="overflow-x: hidden;">
	<div class="row mb-4">
		<div class="col-12">
			<div class="d-flex justify-content-between align-items-center">
				<div>
					<h2 class="fw-bold mb-1">Input Penilaian</h2>
					<?php if (isset($jadwal['is_nilai_validated']) && $jadwal['is_nilai_validated'] == 1): ?>
						<div class="alert alert-success alert-sm mt-2 mb-0 py-2">
							<i class="bi bi-check-circle-fill me-1"></i>
							<strong>Nilai telah divalidasi</strong>
							<?php if (isset($jadwal['validated_by_name'])): ?>
								oleh <strong><?= esc($jadwal['validated_by_name']) ?></strong>
							<?php endif; ?>
							pada <?= date('d/m/Y H:i', strtotime($jadwal['validated_at'])) ?>
						</div>
					<?php endif; ?>
				</div>
				<div class="d-flex gap-2">
					<a href="<?= base_url('admin/nilai/unduh-dpna/' . $jadwal['id']) ?>"
						class="btn btn-success"
						target="_blank"
						title="Unduh Daftar Penilaian Nilai Akhir">
						<i class="bi bi-download me-2"></i>Unduh DPNA
					</a>
					<?php if (!isset($jadwal['is_nilai_validated']) || $jadwal['is_nilai_validated'] == 0): ?>
						<button type="button"
							class="btn btn-primary"
							data-bs-toggle="modal"
							data-bs-target="#uploadNilaiModal"
							title="Unggah/Import Nilai dari Excel">
							<i class="bi bi-upload me-2"></i>Unggah
						</button>
					<?php endif; ?>
					<a href="<?= base_url('admin/nilai') ?>" class="btn btn-outline-secondary">
						<i class="bi bi-arrow-left me-2"></i>Kembali
					</a>
				</div>
			</div>
		</div>
	</div>

	<div class="card border-0 shadow-sm mb-4">
		<div class="card-body bg-light">
			<div class="row g-3">
				<div class="col-md-3">
					<div class="d-flex align-items-center">
						<div>
							<small class="text-muted">Mata Kuliah</small>
							<h6 class="mb-0 fw-semibold"><?= esc($jadwal['nama_mk']) ?></h6>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="d-flex align-items-center">
						<div>
							<small class="text-muted">Kelas</small>
							<h6 class="mb-0 fw-semibold"><?= esc($jadwal['kelas']) ?></h6>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="d-flex align-items-center">
						<div>
							<small class="text-muted">Tahun Akademik</small>
							<h6 class="mb-0 fw-semibold"><?= esc($jadwal['tahun_akademik']) ?></h6>
						</div>
					</div>
				</div>
				<div class="col-md-3">
					<div class="d-flex align-items-center">
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
							<h6 class="fw-bold mb-1">Informasi Mata Kuliah</h6>
							<small class="text-muted">
								Berikut adalah informasi singkat mengenai RPS dan teknik penilaian mingguan yang digunakan pada mata kuliah ini.
							</small>
						</div>
					</div>
					<div class="d-flex gap-2 flex-shrink-0">
						<?php
						// Get RPS ID from the first teknik item
						$db = \Config\Database::connect();
						$first_rps_mingguan_id = $teknik_list[0]['rps_mingguan_id'] ?? null;
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

	<div class="card border-0 shadow-sm">
		<div class="card-body p-0">
			<?php if (empty($mahasiswa_list) || empty($teknik_list)): ?>
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
									<?php
									$isValidated = isset($jadwal['is_nilai_validated']) && $jadwal['is_nilai_validated'] == 1;
									$isDosen = session()->get('role') === 'dosen';
									$isAdmin = session()->get('role') === 'admin';
									$canEdit = !$isValidated;
									?>
									<?php if (!$isValidated): ?>
										<button type="button" class="btn btn-success btn-sm" onclick="fillAllValues()">
											<i class="bi bi-lightning-fill me-1"></i>Isi Semua (Testing)
										</button>
										<button type="button" class="btn btn-outline-secondary btn-sm" onclick="clearAllValues()">
											<i class="bi bi-eraser me-1"></i>Kosongkan Semua
										</button>
										<button type="button" class="btn btn-primary btn-sm" onclick="validateNilai()">
											<i class="bi bi-check-circle me-1"></i>Validasi Nilai
										</button>
									<?php endif; ?>

									<?php if ($isValidated && $isAdmin): ?>
										<button type="button" class="btn btn-warning btn-sm" onclick="unvalidateNilai()">
											<i class="bi bi-x-circle me-1"></i>Batal Validasi
										</button>
									<?php endif; ?>
								</div>
							</div>
							<div class="col-md-4 text-end">
								<?php if ($canEdit): ?>
									<button type="submit" class="btn btn-primary">
										<i class="bi bi-save me-2"></i>Simpan Perubahan
									</button>
								<?php else: ?>
									<button type="button" class="btn btn-secondary" disabled>
										<i class="bi bi-lock me-2"></i>Nilai Sudah Divalidasi
									</button>
								<?php endif; ?>
							</div>
						</div>
					</div>

					<div class="modern-table-wrapper" style="max-height: 70vh;">
						<div class="scroll-indicator"></div>
						<table class="modern-table" id="nilaiTable">
							<thead>
								<tr>
									<th class="text-center align-middle sticky-col" style="width: 60px; min-width: 60px;" rowspan="2">No</th>
									<th class="align-middle sticky-col" style="width: 130px; min-width: 130px;" rowspan="2">NIM</th>
									<th class="align-middle sticky-col" style="min-width: 200px;" rowspan="2">Nama Mahasiswa</th>
									<?php
									$tahap_count = count($teknik_by_tahap);
									$tahap_index = 0;
									?>
									<?php foreach ($teknik_by_tahap as $tahap => $tahap_items): ?>
										<?php
										$tahap_index++;
										$is_last_tahap = ($tahap_index === $tahap_count);
										?>
										<th class="text-center align-middle bg-secondary bg-opacity-10 <?= $is_last_tahap ? '' : 'tahap-border-right' ?>" colspan="<?= count($tahap_items) ?>">
											<?= esc($tahap) ?>
										</th>
									<?php endforeach; ?>
									<th class="text-center align-middle bg-success bg-opacity-10" style="width: 120px; min-width: 120px;" rowspan="2">
										<div class="d-flex flex-column align-items-center">
											<span class="fw-bold">Nilai Huruf</span>
										</div>
									</th>
									<th class="text-center align-middle bg-warning bg-opacity-10" style="width: 150px; min-width: 150px;" rowspan="2">
										<div class="d-flex flex-column align-items-center">
											<span class="fw-bold">Keterangan</span>
										</div>
									</th>
								</tr>
								<tr>
									<?php
									$tahap_keys = array_keys($teknik_by_tahap);
									$last_tahap_key = end($tahap_keys);
									?>
									<?php foreach ($teknik_by_tahap as $tahap => $tahap_items): ?>
										<?php
										$item_count = count($tahap_items);
										$item_index = 0;
										$is_last_tahap_group = ($tahap === $last_tahap_key);
										?>
										<?php foreach ($tahap_items as $item): ?>
											<?php
											$item_index++;
											$is_last_in_group = ($item_index === $item_count);
											$show_border = $is_last_in_group && !$is_last_tahap_group;
											// Build tooltip with week, CPMK, and bobot
											$cpmk_display = $item['kode_cpmk'] ?? $item['cpmk_code'] ?? 'N/A';
											$tooltip = esc($item['teknik_label']) . " - Minggu " . $item['minggu'] . " - CPMK: " . esc($cpmk_display) . " (" . number_format($item['bobot'], 1) . "%)";
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
														Minggu: <?= $item['minggu'] ?><br />
														<?= esc($item['kode_cpmk'] ?? $item['cpmk_code'] ?? '') ?>
													</small>
													<span class="badge bg-success" style="font-size: 0.65rem;">
														<?= number_format($item['bobot'], 1) ?>%
													</span>
												</div>
											</th>
										<?php endforeach; ?>
									<?php endforeach; ?>
								</tr>
							</thead>
							<tbody>
								<?php foreach ($mahasiswa_list as $index => $mahasiswa) : ?>
									<tr>
										<td class="text-center align-middle fw-bold text-muted sticky-col">
											<?= $index + 1 ?>
										</td>
										<td class="align-middle sticky-col">
											<span class="fw-semibold"><?= esc($mahasiswa['nim']) ?></span>
										</td>
										<td class="align-middle sticky-col">
											<div class="d-flex align-items-center">
												<span><?= esc($mahasiswa['nama_lengkap']) ?></span>
											</div>
										</td>
										<?php
										// Build a map to know which columns are the last in their tahap group (but not the very last group)
										$last_in_group = [];
										$tahap_keys = array_keys($teknik_by_tahap);
										$last_tahap_key = end($tahap_keys);
										foreach ($teknik_by_tahap as $tahap => $tahap_items) {
											if (!empty($tahap_items) && $tahap !== $last_tahap_key) {
												$last_item = end($tahap_items);
												// Use rps_mingguan_id + teknik_key as unique identifier
												$last_in_group[$last_item['rps_mingguan_id'] . '_' . $last_item['teknik_key']] = true;
											}
										}
										?>
										<?php foreach ($teknik_list as $item) : ?>
											<?php
											$unique_key = $item['rps_mingguan_id'] . '_' . $item['teknik_key'];
											$is_last = isset($last_in_group[$unique_key]);
											?>
											<td class="align-middle p-1 <?= $is_last ? 'tahap-border-right' : '' ?>">
												<input
													type="text"
													inputmode="decimal"
													class="form-control form-control-sm text-center nilai-input"
													name="nilai[<?= $mahasiswa['id'] ?>][<?= $item['rps_mingguan_id'] ?>][<?= $item['teknik_key'] ?>]"
													value="<?= esc($existing_scores[$mahasiswa['id']][$item['rps_mingguan_id']][$item['teknik_key']] ?? '') ?>"
													data-mahasiswa="<?= $mahasiswa['id'] ?>"
													data-rps="<?= $item['rps_mingguan_id'] ?>"
													data-teknik="<?= $item['teknik_key'] ?>"
													data-bobot="<?= $item['bobot'] ?>"
													placeholder="0-100"
													style="width: max-content; background: transparent; padding: 0;"
													<?= (!$canEdit) ? 'readonly' : '' ?>>
											</td>
										<?php endforeach; ?>
										<td class="align-middle text-center">
											<span class="nilai-huruf-display"
												data-mahasiswa="<?= $mahasiswa['id'] ?>"
												style="font-size: 1rem; min-width: 50px;">-</span>
										</td>
										<td class="align-middle text-center">
											<span class="keterangan-display"
												data-mahasiswa="<?= $mahasiswa['id'] ?>"
												style="font-size: 1rem;">-</span>
										</td>
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
										Total: <?= count($mahasiswa_list) ?> mahasiswa dengan <?= count($teknik_list) ?> teknik penilaian
									</small>
									<small class="text-muted" id="saveStatus"></small>
								</div>
							</div>
						</div>
					</div>
				</form>
			<?php endif; ?>
		</div>
	</div>
</div>

<!-- Upload Nilai Modal -->
<div class="modal fade" id="uploadNilaiModal" tabindex="-1" aria-labelledby="uploadNilaiModalLabel" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title" id="uploadNilaiModalLabel">
					<i class="bi bi-upload me-2"></i>Unggah Nilai dari Excel
				</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<form id="uploadNilaiForm" enctype="multipart/form-data">
				<div class="modal-body">
					<div class="alert alert-info">
						<i class="bi bi-info-circle me-2"></i>
						<strong>Instruksi:</strong>
						<ul class="mb-0 mt-2">
							<li>File harus dalam format Excel (.xlsx)</li>
							<li>Gunakan template dari tombol "Unduh DPNA" untuk memastikan format yang benar</li>
							<li>Kolom yang akan diimport: Tugas, UTS, UAS</li>
							<li>Pastikan NIM mahasiswa sesuai dengan data di sistem</li>
							<li>Nilai yang di-upload akan menggantikan nilai yang sudah ada</li>
						</ul>
					</div>

					<div class="mb-3">
						<label for="fileNilai" class="form-label fw-bold">Pilih File Excel</label>
						<input type="file"
							class="form-control"
							id="fileNilai"
							name="file_nilai"
							accept=".xlsx,.xls"
							required>
						<div class="form-text">Format: .xlsx atau .xls (maksimal 5MB)</div>
					</div>

					<div id="uploadProgress" class="d-none">
						<div class="progress">
							<div class="progress-bar progress-bar-striped progress-bar-animated"
								role="progressbar"
								style="width: 100%">
								Mengupload...
							</div>
						</div>
					</div>

					<div id="uploadResult" class="mt-3"></div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
					<button type="submit" class="btn btn-primary" id="btnUpload">
						<i class="bi bi-upload me-2"></i>Unggah
					</button>
				</div>
			</form>
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
		.modern-table-wrapper {
			max-height: 60vh !important;
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

		// Dynamic grade configuration from database
		const gradeConfig = <?= json_encode($grade_config ?? []) ?>;

		// Function to convert numeric grade to letter grade using dynamic config
		function getNilaiHuruf(score) {
			// Use dynamic grade configuration from database
			if (gradeConfig && gradeConfig.length > 0) {
				for (let i = 0; i < gradeConfig.length; i++) {
					const grade = gradeConfig[i];
					const minScore = parseFloat(grade.min_score);
					const maxScore = parseFloat(grade.max_score);

					if (score > minScore && score <= maxScore) {
						// Determine color based on is_passing and score range
						let color = 'secondary';
						if (grade.is_passing == 1) {
							color = 'success';
						} else {
							color = 'danger';
						}

						return {
							grade: grade.grade_letter,
							color: color,
							is_passing: grade.is_passing == 1
						};
					}
				}
			}

			// Fallback to hardcoded values if no config found
			if (score > 80) return {
				grade: 'A',
				color: 'success',
				is_passing: true
			};
			if (score > 70) return {
				grade: 'AB',
				color: 'success',
				is_passing: true
			};
			if (score > 65) return {
				grade: 'B',
				color: 'info',
				is_passing: true
			};
			if (score > 60) return {
				grade: 'BC',
				color: 'info',
				is_passing: true
			};
			if (score > 50) return {
				grade: 'C',
				color: 'warning',
				is_passing: true
			};
			if (score > 40) return {
				grade: 'D',
				color: 'danger',
				is_passing: false
			};
			return {
				grade: 'E',
				color: 'danger',
				is_passing: false
			};
		}

		// Function to get keterangan based on grade info
		function getKeterangan(gradeInfo) {
			if (gradeInfo.is_passing) {
				return {
					text: 'Lulus',
					color: 'success',
					title: 'Lulus'
				};
			} else {
				return {
					text: 'Tidak Lulus',
					color: 'danger',
					title: 'Tidak Lulus'
				};
			}
		}

		// Function to calculate final score for a student
		function calculateFinalScore(mahasiswaId) {
			const inputs = document.querySelectorAll(`.nilai-input[data-mahasiswa="${mahasiswaId}"]`);
			let totalScore = 0;
			let totalBobot = 0;
			let validCount = 0;

			inputs.forEach(input => {
				const value = parseFloat(input.value.replace(',', '.'));
				const bobot = parseFloat(input.getAttribute('data-bobot')) || 0;

				if (!isNaN(value) && value >= 0 && value <= 100) {
					totalScore += value * bobot;
					totalBobot += bobot;
					validCount++;
				}
			});

			// If all inputs are filled and valid
			if (validCount === inputs.length && validCount > 0) {
				// Calculate weighted average
				const finalScore = totalBobot > 0 ? totalScore / totalBobot : totalScore / validCount;
				return finalScore;
			}

			return null;
		}

		// Function to update nilai huruf and keterangan displays
		function updateGradeDisplays(mahasiswaId) {
			const finalScore = calculateFinalScore(mahasiswaId);
			const nilaiHurufEl = document.querySelector(`.nilai-huruf-display[data-mahasiswa="${mahasiswaId}"]`);
			const keteranganEl = document.querySelector(`.keterangan-display[data-mahasiswa="${mahasiswaId}"]`);

			if (finalScore !== null) {
				const gradeInfo = getNilaiHuruf(finalScore);
				const keteranganInfo = getKeterangan(gradeInfo);

				// Update Nilai Huruf
				nilaiHurufEl.textContent = gradeInfo.grade;
				nilaiHurufEl.className = `fw-bold text-${gradeInfo.color} nilai-huruf-display`;
				nilaiHurufEl.setAttribute('data-mahasiswa', mahasiswaId);
				nilaiHurufEl.style.fontSize = '1rem';
				nilaiHurufEl.style.minWidth = '50px';
				nilaiHurufEl.title = `Nilai Akhir: ${finalScore.toFixed(2)}`;

				// Update Keterangan
				keteranganEl.textContent = keteranganInfo.text;
				keteranganEl.className = `fw-bold text-${keteranganInfo.color} keterangan-display`;
				keteranganEl.setAttribute('data-mahasiswa', mahasiswaId);
				keteranganEl.style.fontSize = '0.85rem';
				keteranganEl.title = keteranganInfo.title;
			} else {
				// Reset to default if scores are incomplete
				nilaiHurufEl.textContent = '-';
				nilaiHurufEl.className = 'fw-bold nilai-huruf-display';
				nilaiHurufEl.setAttribute('data-mahasiswa', mahasiswaId);
				nilaiHurufEl.style.fontSize = '1rem';
				nilaiHurufEl.style.minWidth = '50px';
				nilaiHurufEl.title = '';

				keteranganEl.textContent = '-';
				keteranganEl.className = 'fw-bold keterangan-display';
				keteranganEl.setAttribute('data-mahasiswa', mahasiswaId);
				keteranganEl.style.fontSize = '0.85rem';
				keteranganEl.title = '';
			}
		}

		nilaiInputs.forEach(input => {
			input.addEventListener('input', function() {
				validateInput(this);
				// Update grade displays when input changes
				const mahasiswaId = this.getAttribute('data-mahasiswa');
				updateGradeDisplays(mahasiswaId);
			});
			validateInput(input);
			// Initial calculation for existing values
			const mahasiswaId = input.getAttribute('data-mahasiswa');
			updateGradeDisplays(mahasiswaId);
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
		if (confirm('Apakah Anda yakin ingin mengisi semua nilai dengan data testing (1-100)?')) {
			const inputs = document.querySelectorAll('.nilai-input');
			const updatedMahasiswa = new Set();
			let filledCount = 0;

			inputs.forEach(input => {
				// Generate random score between 1-100 for realistic test data
				const randomScore = Math.floor(Math.random() * (100 - 1 + 1)) + 1;
				input.value = randomScore;
				input.classList.remove('is-invalid');
				input.classList.add('is-valid');
				updatedMahasiswa.add(input.getAttribute('data-mahasiswa'));
				filledCount++;
			});

			// Update grade displays for all affected students
			updatedMahasiswa.forEach(mahasiswaId => {
				const firstInput = document.querySelector(`.nilai-input[data-mahasiswa="${mahasiswaId}"]`);
				if (firstInput) {
					const event = new Event('input');
					firstInput.dispatchEvent(event);
				}
			});

			updateSaveStatus(`${filledCount} nilai berhasil diisi dengan data testing.`);
		}
	}

	function clearAllValues() {
		if (confirm('Apakah Anda yakin ingin mengosongkan semua nilai?')) {
			const inputs = document.querySelectorAll('.nilai-input');
			const updatedMahasiswa = new Set();

			inputs.forEach(input => {
				input.value = '';
				input.classList.remove('is-valid', 'is-invalid');
				updatedMahasiswa.add(input.getAttribute('data-mahasiswa'));
			});

			// Reset grade displays for all students
			updatedMahasiswa.forEach(mahasiswaId => {
				const nilaiHurufEl = document.querySelector(`.nilai-huruf-display[data-mahasiswa="${mahasiswaId}"]`);
				const keteranganEl = document.querySelector(`.keterangan-display[data-mahasiswa="${mahasiswaId}"]`);

				if (nilaiHurufEl) {
					nilaiHurufEl.textContent = '-';
					nilaiHurufEl.className = 'fw-bold nilai-huruf-display';
					nilaiHurufEl.title = '';
				}

				if (keteranganEl) {
					keteranganEl.textContent = '-';
					keteranganEl.className = 'fw-bold keterangan-display';
					keteranganEl.title = '';
				}
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

	function validateNilai() {
		if (confirm('Apakah Anda yakin ingin memvalidasi nilai ini?\n\nSetelah divalidasi, nilai tidak dapat lagi diedit.\nHanya admin yang dapat membatalkan validasi.')) {
			// Create a form and submit
			const form = document.createElement('form');
			form.method = 'POST';
			form.action = '<?= base_url('admin/nilai/validate/' . $jadwal['id']) ?>';

			// Add CSRF token
			const csrfInput = document.createElement('input');
			csrfInput.type = 'hidden';
			csrfInput.name = '<?= csrf_token() ?>';
			csrfInput.value = '<?= csrf_hash() ?>';
			form.appendChild(csrfInput);

			document.body.appendChild(form);
			form.submit();
		}
	}

	function unvalidateNilai() {
		if (confirm('Apakah Anda yakin ingin membatalkan validasi nilai ini?\n\nSetelah dibatalkan, dosen dapat kembali mengedit nilai.')) {
			// Create a form and submit
			const form = document.createElement('form');
			form.method = 'POST';
			form.action = '<?= base_url('admin/nilai/unvalidate/' . $jadwal['id']) ?>';

			// Add CSRF token
			const csrfInput = document.createElement('input');
			csrfInput.type = 'hidden';
			csrfInput.name = '<?= csrf_token() ?>';
			csrfInput.value = '<?= csrf_hash() ?>';
			form.appendChild(csrfInput);

			document.body.appendChild(form);
			form.submit();
		}
	}

	// Handle upload form submission
	document.getElementById('uploadNilaiForm').addEventListener('submit', function(e) {
		e.preventDefault();

		const fileInput = document.getElementById('fileNilai');
		const uploadProgress = document.getElementById('uploadProgress');
		const uploadResult = document.getElementById('uploadResult');
		const btnUpload = document.getElementById('btnUpload');

		// Validate file
		if (!fileInput.files || fileInput.files.length === 0) {
			uploadResult.innerHTML = '<div class="alert alert-danger"><i class="bi bi-x-circle me-2"></i>Silakan pilih file terlebih dahulu.</div>';
			return;
		}

		const file = fileInput.files[0];
		const maxSize = 5 * 1024 * 1024; // 5MB

		if (file.size > maxSize) {
			uploadResult.innerHTML = '<div class="alert alert-danger"><i class="bi bi-x-circle me-2"></i>Ukuran file terlalu besar. Maksimal 5MB.</div>';
			return;
		}

		// Show progress
		uploadProgress.classList.remove('d-none');
		uploadResult.innerHTML = '';
		btnUpload.disabled = true;
		btnUpload.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Mengupload...';

		// Create FormData
		const formData = new FormData();
		formData.append('file_nilai', file);

		// Send AJAX request
		fetch('<?= base_url('admin/nilai/import-nilai-excel/' . $jadwal['id']) ?>', {
				method: 'POST',
				body: formData,
				headers: {
					'X-Requested-With': 'XMLHttpRequest',
					'<?= csrf_header() ?>': '<?= csrf_hash() ?>'
				}
			})
			.then(response => response.json())
			.then(data => {
				uploadProgress.classList.add('d-none');
				btnUpload.disabled = false;
				btnUpload.innerHTML = '<i class="bi bi-upload me-2"></i>Unggah';

				if (data.status === 'success') {
					uploadResult.innerHTML = `
					<div class="alert alert-success">
						<i class="bi bi-check-circle me-2"></i>
						<strong>Berhasil!</strong> ${data.message}
						<br><small>Total: ${data.imported_count} mahasiswa diimport.</small>
					</div>
				`;

					// Reload page after 2 seconds
					setTimeout(function() {
						window.location.reload();
					}, 2000);
				} else {
					uploadResult.innerHTML = `
					<div class="alert alert-danger">
						<i class="bi bi-x-circle me-2"></i>
						<strong>Gagal!</strong> ${data.message}
						${data.errors ? '<ul class="mb-0 mt-2">' + data.errors.map(err => '<li>' + err + '</li>').join('') + '</ul>' : ''}
					</div>
				`;
				}
			})
			.catch(error => {
				uploadProgress.classList.add('d-none');
				btnUpload.disabled = false;
				btnUpload.innerHTML = '<i class="bi bi-upload me-2"></i>Unggah';

				uploadResult.innerHTML = `
				<div class="alert alert-danger">
					<i class="bi bi-x-circle me-2"></i>
					<strong>Error!</strong> Terjadi kesalahan saat mengupload file.
					<br><small>${error.message}</small>
				</div>
			`;
			});
	});

	// Reset modal when closed
	document.getElementById('uploadNilaiModal').addEventListener('hidden.bs.modal', function() {
		document.getElementById('uploadNilaiForm').reset();
		document.getElementById('uploadProgress').classList.add('d-none');
		document.getElementById('uploadResult').innerHTML = '';
		document.getElementById('btnUpload').disabled = false;
		document.getElementById('btnUpload').innerHTML = '<i class="bi bi-upload me-2"></i>Unggah';
	});

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
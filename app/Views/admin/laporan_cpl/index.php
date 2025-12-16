<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
	<div class="row mb-4">
		<div class="col-12">
			<h2 class="fw-bold">Laporan CPL</h2>
			<p class="text-muted">Laporan Pemenuhan Capaian Pembelajaran Lulusan (CPL)</p>
		</div>
	</div>

	<?php if (session()->getFlashdata('error')): ?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<?= session()->getFlashdata('error') ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
		</div>
	<?php endif; ?>

	<div class="row">
		<div class="col-lg-8">
			<div class="card shadow-sm">
				<div class="card-header bg-primary text-white">
					<h5 class="mb-0">Filter Laporan CPL</h5>
				</div>
				<div class="card-body">
					<form action="<?= base_url('admin/laporan-cpl/generate') ?>" method="get">
						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label for="program_studi" class="form-label fw-bold">Program Studi <span class="text-danger">*</span></label>
									<select class="form-select" id="program_studi" name="program_studi" required>
										<option value="">-- Pilih Program Studi --</option>
										<?php foreach ($programStudi as $ps): ?>
											<option value="<?= esc($ps['program_studi']) ?>" <?= $ps['program_studi'] === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($ps['program_studi']) ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>

							<div class="col-md-6">
								<div class="mb-3">
									<label for="tahun_akademik" class="form-label fw-bold">Tahun Akademik <span class="text-danger">*</span></label>
									<select class="form-select" id="tahun_akademik" name="tahun_akademik" required>
										<option value="">-- Pilih Tahun Akademik --</option>
										<?php foreach ($tahunAkademik as $ta): ?>
											<option value="<?= esc($ta['tahun_akademik']) ?>"><?= esc($ta['tahun_akademik']) ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
						</div>

						<div class="mb-3">
							<label for="angkatan" class="form-label fw-bold">Angkatan <span class="text-danger">*</span></label>
							<select class="form-select" id="angkatan" name="angkatan" required disabled>
								<option value="">-- Pilih Program Studi Terlebih Dahulu --</option>
							</select>
						</div>

						<div class="d-flex gap-2">
							<button type="submit" class="btn btn-primary" id="btnGenerate" disabled>
								<i class="bi bi-file-earmark-text"></i> Generate Laporan
							</button>
							<button type="reset" class="btn btn-outline-secondary" id="btnReset">
								<i class="bi bi-arrow-clockwise"></i> Reset
							</button>
						</div>
					</form>
				</div>
			</div>

			<div class="card shadow-sm mt-4">
				<div class="card-body">
					<h6 class="fw-bold mb-3">Informasi Laporan CPL</h6>
					<p class="mb-2">Laporan CPL mencakup:</p>
					<ul class="small">
						<li>Identitas program studi</li>
						<li>Daftar CPL program studi</li>
						<li>Matriks CPMK terhadap CPL</li>
						<li>Rekapitulasi capaian CPL berdasarkan CPMK untuk satu angkatan</li>
						<li>Analisis pemenuhan CPL (CPL tercapai dan tidak tercapai)</li>
						<li>Tindak lanjut dan rencana perbaikan (CQI)</li>
						<li>Kesimpulan umum pemenuhan CPL</li>
					</ul>
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="card shadow-sm bg-light">
				<div class="card-body">
					<h6 class="fw-bold mb-3">
						<i class="bi bi-info-circle"></i> Petunjuk Penggunaan
					</h6>
					<ol class="small mb-0">
						<li class="mb-2">Pilih <strong>Program Studi</strong> yang akan dilaporkan</li>
						<li class="mb-2">Pilih <strong>Tahun Akademik</strong> periode pelaporan</li>
						<li class="mb-2">Pilih <strong>Angkatan</strong> mahasiswa yang akan dilaporkan</li>
						<li class="mb-2">Klik tombol <strong>Generate Laporan</strong></li>
						<li class="mb-2">Laporan akan ditampilkan dan dapat dicetak atau diunduh</li>
					</ol>
				</div>
			</div>

			<div class="card shadow-sm mt-3">
				<div class="card-body">
					<h6 class="fw-bold mb-3">
						<i class="bi bi-exclamation-triangle"></i> Catatan Penting
					</h6>
					<ul class="small mb-0">
						<li class="mb-2">Pastikan data nilai CPMK mahasiswa sudah lengkap</li>
						<li class="mb-2">Laporan dihitung berdasarkan rata-rata capaian mahasiswa dalam satu angkatan</li>
						<li class="mb-2">Standar minimal CPL adalah 75%</li>
						<li class="mb-2">CPL dihitung berdasarkan bobot CPMK kontributor dari RPS</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	$(document).ready(function() {
		const programStudiSelect = $('#program_studi');
		const angkatanSelect = $('#angkatan');
		const btnGenerate = $('#btnGenerate');
		const btnReset = $('#btnReset');

		// Function to load angkatan data
		function loadAngkatan(programStudi) {
			if (programStudi) {
				// Enable angkatan select
				angkatanSelect.prop('disabled', false);
				angkatanSelect.html('<option value="">-- Memuat data... --</option>');

				// Fetch angkatan data
				$.ajax({
					url: '<?= base_url('admin/laporan-cpl/get-angkatan') ?>',
					type: 'GET',
					data: {
						program_studi: programStudi
					},
					dataType: 'json',
					success: function(response) {
						let options = '<option value="">-- Pilih Angkatan --</option>';

						if (response && response.length > 0) {
							response.forEach(function(item) {
								options += `<option value="${item.tahun_angkatan}">${item.tahun_angkatan}</option>`;
							});
						} else {
							options = '<option value="">-- Tidak ada data angkatan --</option>';
						}

						angkatanSelect.html(options);
					},
					error: function() {
						angkatanSelect.html('<option value="">-- Error memuat data --</option>');
					}
				});
			} else {
				angkatanSelect.prop('disabled', true);
				angkatanSelect.html('<option value="">-- Pilih Program Studi Terlebih Dahulu --</option>');
				btnGenerate.prop('disabled', true);
			}
		}

		// Load angkatan on page load if program studi is pre-selected
		if (programStudiSelect.val()) {
			loadAngkatan(programStudiSelect.val());
		}

		// Load angkatan when program studi is selected
		programStudiSelect.on('change', function() {
			loadAngkatan($(this).val());
		});

		// Enable generate button when all required fields are filled
		$('form').on('change', 'select', function() {
			const programStudi = programStudiSelect.val();
			const tahunAkademik = $('#tahun_akademik').val();
			const angkatan = angkatanSelect.val();

			if (programStudi && tahunAkademik && angkatan) {
				btnGenerate.prop('disabled', false);
			} else {
				btnGenerate.prop('disabled', true);
			}
		});

		// Reset form
		btnReset.on('click', function() {
			angkatanSelect.prop('disabled', true);
			angkatanSelect.html('<option value="">-- Pilih Program Studi Terlebih Dahulu --</option>');
			btnGenerate.prop('disabled', true);
		});
	});
</script>
<?= $this->endSection() ?>
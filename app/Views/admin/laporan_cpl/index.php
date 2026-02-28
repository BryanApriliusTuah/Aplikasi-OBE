<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="d-flex justify-content-between align-items-center mb-3">
	<div>
		<h2 class="mb-0 fw-bold">Laporan CPL</h2>
		<p class="text-muted mb-0 small">Laporan Pemenuhan Capaian Pembelajaran Lulusan (CPL)</p>
	</div>
</div>

<?php if (session()->getFlashdata('error')): ?>
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		<?= session()->getFlashdata('error') ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
<?php endif; ?>

<div class="row g-3">
	<div class="col-lg-8">

		<div class="modern-filter-wrapper mb-3">
			<div class="modern-filter-header">
				<div class="d-flex align-items-center gap-2">
					<i class="bi bi-funnel-fill text-primary"></i>
					<span class="modern-filter-title">Filter Laporan CPL</span>
				</div>
			</div>
			<div class="modern-filter-body">
				<form action="<?= base_url('admin/laporan-cpl/generate') ?>" method="get">
					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label for="program_studi" class="modern-filter-label">
									Program Studi <span class="text-danger">*</span>
								</label>
								<select class="form-select modern-filter-input" id="program_studi" name="program_studi" required>
									<option value="">-- Pilih Program Studi --</option>
									<?php foreach ($programStudi as $kode => $nama): ?>
										<option value="<?= esc($kode) ?>" <?= ucwords(strtolower($nama)) === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($nama) ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="mb-3">
								<label for="tahun_akademik" class="modern-filter-label">
									Tahun Akademik <span class="text-danger">*</span>
								</label>
								<select class="form-select modern-filter-input" id="tahun_akademik" name="tahun_akademik" required>
									<option value="">-- Pilih Tahun Akademik --</option>
									<?php foreach ($tahunAkademik as $ta): ?>
										<option value="<?= esc($ta['tahun_akademik']) ?>"><?= esc($ta['tahun_akademik']) ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>

					<div class="mb-3">
						<label for="angkatan" class="modern-filter-label">
							Angkatan <span class="text-danger">*</span>
						</label>
						<select class="form-select modern-filter-input" id="angkatan" name="angkatan" required disabled>
							<option value="">-- Pilih Program Studi Terlebih Dahulu --</option>
						</select>
					</div>

					<div class="d-flex gap-2">
						<button type="submit" class="btn btn-primary modern-filter-btn" id="btnGenerate" disabled>
							<i class="bi bi-file-earmark-text"></i> Generate Laporan
						</button>
						<button type="reset" class="btn btn-outline-secondary modern-filter-btn-reset" id="btnReset" title="Reset">
							<i class="bi bi-arrow-clockwise"></i>
						</button>
					</div>
				</form>
			</div>
		</div>

		<div class="modern-filter-wrapper">
			<div class="modern-filter-header">
				<div class="d-flex align-items-center gap-2">
					<i class="bi bi-info-circle text-primary"></i>
					<span class="modern-filter-title">Cakupan Laporan CPL</span>
				</div>
			</div>
			<div class="modern-filter-body">
				<ul class="small mb-0">
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

		<div class="modern-filter-wrapper mb-3">
			<div class="modern-filter-header">
				<div class="d-flex align-items-center gap-2">
					<i class="bi bi-list-ol text-primary"></i>
					<span class="modern-filter-title">Petunjuk Penggunaan</span>
				</div>
			</div>
			<div class="modern-filter-body">
				<ol class="small mb-0">
					<li class="mb-1">Pilih <strong>Program Studi</strong> yang akan dilaporkan</li>
					<li class="mb-1">Pilih <strong>Tahun Akademik</strong> periode pelaporan</li>
					<li class="mb-1">Pilih <strong>Angkatan</strong> mahasiswa yang akan dilaporkan</li>
					<li class="mb-1">Klik tombol <strong>Generate Laporan</strong></li>
					<li>Laporan dapat dicetak atau diunduh</li>
				</ol>
			</div>
		</div>

		<div class="modern-filter-wrapper">
			<div class="modern-filter-header">
				<div class="d-flex align-items-center gap-2">
					<i class="bi bi-exclamation-triangle text-warning"></i>
					<span class="modern-filter-title">Catatan Penting</span>
				</div>
			</div>
			<div class="modern-filter-body">
				<ul class="small mb-0">
					<li class="mb-1">Pastikan data nilai CPMK mahasiswa sudah lengkap</li>
					<li class="mb-1">Laporan dihitung berdasarkan rata-rata capaian mahasiswa dalam satu angkatan</li>
					<li class="mb-1">Standar minimal CPL adalah 75%</li>
					<li>CPL dihitung berdasarkan bobot CPMK kontributor dari RPS</li>
				</ul>
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

		function loadAngkatan(programStudi) {
			if (programStudi) {
				angkatanSelect.prop('disabled', false);
				angkatanSelect.html('<option value="">-- Memuat data... --</option>');

				$.ajax({
					url: '<?= base_url('admin/laporan-cpl/get-angkatan') ?>',
					type: 'GET',
					data: { program_studi_kode: programStudi },
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

		if (programStudiSelect.val()) {
			loadAngkatan(programStudiSelect.val());
		}

		programStudiSelect.on('change', function() {
			loadAngkatan($(this).val());
		});

		$('form').on('change', 'select', function() {
			const programStudi = programStudiSelect.val();
			const tahunAkademik = $('#tahun_akademik').val();
			const angkatan = angkatanSelect.val();
			btnGenerate.prop('disabled', !(programStudi && tahunAkademik && angkatan));
		});

		btnReset.on('click', function() {
			angkatanSelect.prop('disabled', true);
			angkatanSelect.html('<option value="">-- Pilih Program Studi Terlebih Dahulu --</option>');
			btnGenerate.prop('disabled', true);
		});
	});
</script>
<?= $this->endSection() ?>

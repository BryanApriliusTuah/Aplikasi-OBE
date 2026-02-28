<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

<div class="d-flex justify-content-between align-items-center mb-3">
	<div>
		<h2 class="mb-0 fw-bold">Laporan CPMK</h2>
		<p class="text-muted mb-0 small">Pilih mata kuliah dan tahun akademik untuk generate portofolio mata kuliah</p>
	</div>
	<a href="<?= base_url('admin/laporan-cpmk/templates') ?>" class="btn btn-outline-primary">
		<i class="bi bi-file-earmark-text"></i> Kelola Template Analisis
	</a>
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
					<span class="modern-filter-title">Filter Portofolio Mata Kuliah</span>
				</div>
			</div>
			<div class="modern-filter-body">
				<form action="<?= base_url('admin/laporan-cpmk/generate') ?>" method="get">
					<div class="mb-3">
						<label for="mata_kuliah_id" class="modern-filter-label">
							Mata Kuliah <span class="text-danger">*</span>
						</label>
						<select class="form-select modern-filter-input select2" id="mata_kuliah_id" name="mata_kuliah_id" required>
							<option value="">-- Pilih Mata Kuliah --</option>
							<?php foreach ($mataKuliah as $mk): ?>
								<option value="<?= $mk['id'] ?>"><?= esc($mk['kode_mk']) ?> - <?= esc($mk['nama_mk']) ?></option>
							<?php endforeach; ?>
						</select>
					</div>

					<div class="row">
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

						<div class="col-md-6">
							<div class="mb-3">
								<label for="program_studi" class="modern-filter-label">
									Program Studi
								</label>
								<select class="form-select modern-filter-input" id="program_studi" name="program_studi">
									<option value="">-- Semua Program Studi --</option>
									<?php foreach ($programStudi as $kode => $nama_resmi): ?>
										<option value="<?= esc($kode) ?>" <?= ucwords(strtolower($nama_resmi)) === "Teknik Informatika" ? 'selected' : '' ?>><?= esc($nama_resmi) ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					</div>

					<div class="d-flex gap-2">
						<button type="submit" class="btn btn-primary modern-filter-btn">
							<i class="bi bi-file-earmark-text"></i> Generate Portofolio
						</button>
						<button type="reset" class="btn btn-outline-secondary modern-filter-btn-reset" title="Reset">
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
					<span class="modern-filter-title">Cakupan Portofolio Mata Kuliah</span>
				</div>
			</div>
			<div class="modern-filter-body">
				<ul class="small mb-0">
					<li>Identitas mata kuliah (nama, kode, semester, SKS, dosen pengampu)</li>
					<li>Capaian Pembelajaran Mata Kuliah (CPMK) beserta keterkaitan dengan CPL</li>
					<li>Metode pembelajaran dan metode asesmen untuk setiap CPMK</li>
					<li>Rencana dan realisasi penilaian CPMK (bobot, teknik penilaian, nilai rata-rata)</li>
					<li>Analisis pencapaian CPMK (CPMK tercapai dan tidak tercapai)</li>
					<li>Rekomendasi tindak lanjut dan Continuous Quality Improvement (CQI)</li>
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
					<li class="mb-1">Pilih <strong>Mata Kuliah</strong> yang ingin dibuat portofolionya</li>
					<li class="mb-1">Pilih <strong>Tahun Akademik</strong> sesuai periode pembelajaran</li>
					<li class="mb-1">Pilih <strong>Program Studi</strong> (opsional) untuk filter berdasarkan prodi</li>
					<li class="mb-1">Klik tombol <strong>Generate Portofolio</strong></li>
					<li>Portofolio dapat dicetak atau diunduh</li>
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
					<li class="mb-1">Pastikan data nilai mahasiswa sudah diinput untuk laporan yang akurat</li>
					<li class="mb-1">Data yang ditampilkan berdasarkan nilai yang telah divalidasi</li>
					<li>Standar minimal capaian mengikuti konfigurasi grade yang aktif</li>
				</ul>
			</div>
		</div>

	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
	$(document).ready(function() {
		$('.select2').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Mata Kuliah --',
			allowClear: true
		});
	});
</script>
<?= $this->endSection() ?>

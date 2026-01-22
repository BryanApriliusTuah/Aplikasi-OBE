<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
	<div class="row mb-4">
		<div class="col-12">
			<div class="d-flex justify-content-between align-items-center">
				<div>
					<h2 class="fw-bold">Laporan CPMK</h2>
					<p class="text-muted mb-0">Pilih mata kuliah dan tahun akademik untuk generate portofolio mata kuliah</p>
				</div>
				<a href="<?= base_url('admin/laporan-cpmk/templates') ?>" class="btn btn-outline-primary">
					<i class="bi bi-file-earmark-text"></i> Kelola Template Analisis
				</a>
			</div>
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
					<h5 class="mb-0">Filter Portofolio Mata Kuliah</h5>
				</div>
				<div class="card-body">
					<form action="<?= base_url('admin/laporan-cpmk/generate') ?>" method="get">
						<div class="mb-3">
							<label for="mata_kuliah_id" class="form-label fw-bold">Mata Kuliah <span class="text-danger">*</span></label>
							<select class="form-select select2" id="mata_kuliah_id" name="mata_kuliah_id" required>
								<option value="">-- Pilih Mata Kuliah --</option>
								<?php foreach ($mataKuliah as $mk): ?>
									<option value="<?= $mk['id'] ?>"><?= esc($mk['kode_mk']) ?> - <?= esc($mk['nama_mk']) ?></option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="row">
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

							<div class="col-md-6">
								<div class="mb-3">
									<label for="program_studi" class="form-label fw-bold">Program Studi</label>
									<select class="form-select" id="program_studi" name="program_studi">
										<option value="">-- Semua Program Studi --</option>
										<?php foreach ($programStudi as $ps): ?>
											<option value="<?= esc($ps['program_studi']) ?>" <?= $ps['program_studi'] === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($ps['program_studi']) ?></option>
										<?php endforeach; ?>
									</select>
								</div>
							</div>
						</div>

						<div class="d-flex gap-2">
							<button type="submit" class="btn btn-primary">
								<i class="bi bi-file-earmark-text"></i> Generate Portofolio
							</button>
							<button type="reset" class="btn btn-outline-secondary">
								<i class="bi bi-arrow-clockwise"></i> Reset
							</button>
						</div>
					</form>
				</div>
			</div>

			<div class="card shadow-sm mt-4">
				<div class="card-body">
					<h6 class="fw-bold mb-3">Informasi Portofolio Mata Kuliah</h6>
					<p class="mb-2">Portofolio mata kuliah mencakup:</p>
					<ul class="small">
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
			<div class="card shadow-sm bg-light">
				<div class="card-body">
					<h6 class="fw-bold mb-3">
						<i class="bi bi-info-circle"></i> Petunjuk Penggunaan
					</h6>
					<ol class="small mb-0">
						<li class="mb-2">Pilih <strong>Mata Kuliah</strong> yang ingin dibuat portofolionya</li>
						<li class="mb-2">Pilih <strong>Tahun Akademik</strong> sesuai periode pembelajaran</li>
						<li class="mb-2">Pilih <strong>Program Studi</strong> (opsional) jika ingin filter berdasarkan prodi tertentu</li>
						<li class="mb-2">Klik tombol <strong>Generate Portofolio</strong></li>
						<li class="mb-2">Portofolio akan ditampilkan dan dapat dicetak atau diunduh</li>
					</ol>
				</div>
			</div>

			<div class="card shadow-sm mt-3">
				<div class="card-body">
					<h6 class="fw-bold mb-3">
						<i class="bi bi-exclamation-triangle"></i> Catatan Penting
					</h6>
					<ul class="small mb-0">
						<li class="mb-2">Pastikan data nilai mahasiswa sudah diinput untuk mendapatkan laporan yang akurat</li>
						<li class="mb-2">Data yang ditampilkan berdasarkan nilai yang telah divalidasi</li>
						<li class="mb-2">Standar minimal capaian mengikuti konfigurasi grade yang aktif</li>
					</ul>
				</div>
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
		// Initialize Select2
		$('.select2').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Mata Kuliah --',
			allowClear: true
		});
	});
</script>
<?= $this->endSection() ?>
<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<style>
	/* Modern Chart Styling */
	canvas {
		font-family: 'Inter', 'Segoe UI', -apple-system, BlinkMacSystemFont, sans-serif;
	}

	.card-body canvas {
		border-radius: 8px;
	}

	/* Export button hover effect */
	.btn-outline-primary:hover {
		transform: translateY(-2px);
		box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
		transition: all 0.3s ease;
	}

	/* Chart container animation */
	#chartSectionIndividual .card,
	#chartSectionComparative .card,
	#chartSectionKeseluruhan .card {
		animation: fadeInUp 0.5s ease-out;
	}

	@keyframes fadeInUp {
		from {
			opacity: 0;
			transform: translateY(20px);
		}

		to {
			opacity: 1;
			transform: translateY(0);
		}
	}

	/* Segmented Control Tab Navigation Styling */
	.modern-tab-nav {
		display: inline-flex;
		gap: 0.25rem;
		/* background: #e9ecef; */
		padding: 0.35rem;
		border-radius: 10px;
		margin-bottom: 1.5rem;
		box-shadow: rgb(204, 219, 232, 0.35) -3px -3px 3px 0 inset, rgba(255, 255, 255, 0.5) 3px 3px 3px 0 inset;
	}

	.modern-tab-item {
		background: transparent;
		border: none;
		padding: 0.65rem 1.5rem;
		cursor: pointer;
		transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
		color: #495057;
		font-weight: 500;
		font-size: 0.9rem;
		display: flex;
		align-items: center;
		gap: 0.5rem;
		position: relative;
		border-radius: 8px;
		z-index: 1;
	}

	.modern-tab-item i {
		font-size: 1.1rem;
		transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
	}

	.modern-tab-item:hover:not(.active) {
		color: #212529;
	}

	.modern-tab-item:hover:not(.active) i {
		transform: scale(1.1);
	}

	.modern-tab-item.active {
		background: #ffffff;
		color: #0d6efd;
		font-weight: 600;
		box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1),
			0 1px 2px rgba(0, 0, 0, 0.06);
	}

	.modern-tab-item.active i {
		color: #0d6efd;
	}
</style>

<div class="card">
	<div class="card-body">
		<h2 class="mb-4">Capaian CPMK</h2>

		<!-- Modern Tab Navigation -->
		<div class="modern-tab-nav mb-4" id="cpmkTabs" role="tablist">
			<div class="modern-tab-item active" id="individual-tab" data-bs-toggle="tab" data-bs-target="#individual" role="tab">
				<i class="bi bi-person-fill"></i>
				<span>Mahasiswa</span>
			</div>
			<div class="modern-tab-item" id="comparative-tab" data-bs-toggle="tab" data-bs-target="#comparative" role="tab">
				<i class="bi bi-people-fill"></i>
				<span>Angkatan</span>
			</div>
			<div class="modern-tab-item" id="keseluruhan-tab" data-bs-toggle="tab" data-bs-target="#keseluruhan" role="tab">
				<i class="bi bi-bar-chart-line-fill"></i>
				<span>Keseluruhan</span>
			</div>
		</div>

		<!-- Tab Content -->
		<div class="tab-content" id="cpmkTabContent">
			<!-- Individual Tab (Mahasiswa) -->
			<div class="tab-pane fade show active" id="individual" role="tabpanel">
				<!-- Sub-tabs for different filter types -->
				<div class="row g-3 mb-4">
					<div class="col-md-4">
						<div class="card filter-card h-100 border-primary shadow-sm" id="filter1-card" role="button" data-bs-toggle="tab" data-bs-target="#filter1" style="cursor: pointer; transition: all 0.3s;">
							<div class="card-body text-center p-3">
								<div class="mb-2">
									<i class="bi bi-person-check-fill text-primary" style="font-size: 1.8rem;"></i>
								</div>
								<h6 class="card-title mb-2 text-primary fw-bold">CPMK Mahasiswa</h6>
								<p class="card-text text-muted small mb-0">Perhitungan CPMK mahasiswa dilakukan dengan menjumlahkan seluruh teknik penilaian dikali bobot penilaiannya dari berbagai mata kuliah yang telah mahasiswa tempuh.</p>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card filter-card h-100 shadow-sm" id="filter2-card" role="button" data-bs-toggle="tab" data-bs-target="#filter2" style="cursor: pointer; transition: all 0.3s;">
							<div class="card-body text-center p-3">
								<div class="mb-2">
									<i class="bi bi-calendar-range text-secondary" style="font-size: 1.8rem;"></i>
								</div>
								<h6 class="card-title mb-2">CPMK per Semester</h6>
								<p class="card-text text-muted small mb-0">Perhitungan CPMK mahasiswa per semester dilakukan dengan menjumlahkan seluruh teknik penilaian dikali bobot penilaiannya dari berbagai mata kuliah dalam satu semester.</p>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card filter-card h-100 shadow-sm" id="filter3-card" role="button" data-bs-toggle="tab" data-bs-target="#filter3" style="cursor: pointer; transition: all 0.3s;">
							<div class="card-body text-center p-3">
								<div class="mb-2">
									<i class="bi bi-calendar2-event text-secondary" style="font-size: 1.8rem;"></i>
								</div>
								<h6 class="card-title mb-2">CPMK per Tahun Akademik</h6>
								<p class="card-text text-muted small mb-0">Perhitungan CPMK mahasiswa per tahun dilakukan dengan cara menjumlahkan seluruh teknik penilaian dikali bobot penilaiannya dari berbagai mata kuliah dalam 1 tahun akademik.</p>
							</div>
						</div>
					</div>
				</div>

				<div class="tab-content" id="filterSubTabContent">
					<!-- Filter 1: CPMK Mahasiswa -->
					<div class="tab-pane fade show active" id="filter1" role="tabpanel">
						<div class="modern-filter-wrapper mb-4">
							<div class="modern-filter-header">
								<div class="d-flex align-items-center gap-2">
									<i class="bi bi-funnel-fill text-primary"></i>
									<span class="modern-filter-title">Filter CPMK Mahasiswa</span>
								</div>
							</div>
							<div class="modern-filter-body">
								<form id="filterIndividualForm1">
									<div class="row g-3 align-items-end">
										<div class="col-md-4">
											<label for="programStudiSelect1" class="modern-filter-label">
												<i class="bi bi-mortarboard-fill me-1"></i> Program Studi
											</label>
											<select class="form-select modern-filter-input" id="programStudiSelect1" name="program_studi">
												<option value="">Semua Program Studi</option>
												<?php foreach ($programStudi as $kode => $prodi): ?>
													<option value="<?= esc($kode) ?>" <?= ucwords(strtolower($prodi)) === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-3">
											<label for="tahunAngkatanSelect1" class="modern-filter-label">
												<i class="bi bi-calendar3 me-1"></i> Tahun Angkatan
											</label>
											<select class="form-select modern-filter-input" id="tahunAngkatanSelect1" name="tahun_angkatan">
												<option value="">Semua Tahun</option>
												<?php foreach ($tahunAngkatan as $tahun): ?>
													<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-4">
											<label for="mahasiswaSelect1" class="modern-filter-label">
												<i class="bi bi-person-fill me-1"></i> Mahasiswa <span class="text-danger">*</span>
											</label>
											<select class="form-select modern-filter-input" id="mahasiswaSelect1" name="mahasiswa_id">
												<option value="">Pilih Mahasiswa</option>
											</select>
										</div>
										<div class="col-md-1 d-flex gap-2">
											<button type="submit" class="btn btn-primary modern-filter-btn flex-fill">
												<i class="bi bi-search"></i>
											</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>

					<!-- Filter 2: CPMK Mahasiswa per Semester -->
					<div class="tab-pane fade" id="filter2" role="tabpanel">
						<div class="modern-filter-wrapper mb-4">
							<div class="modern-filter-header">
								<div class="d-flex align-items-center gap-2">
									<i class="bi bi-funnel-fill text-primary"></i>
									<span class="modern-filter-title">Filter CPMK Mahasiswa per Semester</span>
								</div>
							</div>
							<div class="modern-filter-body">
								<form id="filterIndividualForm2">
									<div class="row g-3 mb-3">
										<div class="col-md-6">
											<label for="programStudiSelect2" class="modern-filter-label">
												<i class="bi bi-mortarboard-fill me-1"></i> Program Studi
											</label>
											<select class="form-select modern-filter-input" id="programStudiSelect2" name="program_studi">
												<option value="">Semua Program Studi</option>
												<?php foreach ($programStudi as $kode => $prodi): ?>
													<option value="<?= esc($kode) ?>" <?= ucwords(strtolower($prodi)) === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-6">
											<label for="tahunAngkatanSelect2" class="modern-filter-label">
												<i class="bi bi-calendar3 me-1"></i> Tahun Angkatan
											</label>
											<select class="form-select modern-filter-input" id="tahunAngkatanSelect2" name="tahun_angkatan">
												<option value="">Semua Tahun</option>
												<?php foreach ($tahunAngkatan as $tahun): ?>
													<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="row g-3 align-items-end">
										<div class="col-md-5">
											<label for="mahasiswaSelect2" class="modern-filter-label">
												<i class="bi bi-person-fill me-1"></i> Mahasiswa <span class="text-danger">*</span>
											</label>
											<select class="form-select modern-filter-input" id="mahasiswaSelect2" name="mahasiswa_id">
												<option value="">Pilih Mahasiswa</option>
											</select>
										</div>
										<div class="col-md-5">
											<label for="semesterSelect2" class="modern-filter-label">
												<i class="bi bi-bookmark-fill me-1"></i> Semester <span class="text-danger">*</span>
											</label>
											<select class="form-select modern-filter-input" id="semesterSelect2" name="semester">
												<option value="">Pilih Semester</option>
												<?php foreach ($semesterList as $semester): ?>
													<option value="<?= esc($semester) ?>"><?= esc($semester) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-2">
											<button type="submit" class="btn btn-primary modern-filter-btn w-100">
												<i class="bi bi-search"></i> Tampilkan
											</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>

					<!-- Filter 3: CPMK Mahasiswa per Tahun Akademik -->
					<div class="tab-pane fade" id="filter3" role="tabpanel">
						<div class="modern-filter-wrapper mb-4">
							<div class="modern-filter-header">
								<div class="d-flex align-items-center gap-2">
									<i class="bi bi-funnel-fill text-primary"></i>
									<span class="modern-filter-title">Filter CPMK Mahasiswa per Tahun Akademik</span>
								</div>
							</div>
							<div class="modern-filter-body">
								<form id="filterIndividualForm3">
									<div class="row g-3 mb-3">
										<div class="col-md-6">
											<label for="programStudiSelect3" class="modern-filter-label">
												<i class="bi bi-mortarboard-fill me-1"></i> Program Studi
											</label>
											<select class="form-select modern-filter-input" id="programStudiSelect3" name="program_studi">
												<option value="">Semua Program Studi</option>
												<?php foreach ($programStudi as $kode => $prodi): ?>
													<option value="<?= esc($kode) ?>" <?= ucwords(strtolower($prodi)) === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-6">
											<label for="tahunAngkatanSelect3" class="modern-filter-label">
												<i class="bi bi-calendar3 me-1"></i> Tahun Angkatan
											</label>
											<select class="form-select modern-filter-input" id="tahunAngkatanSelect3" name="tahun_angkatan">
												<option value="">Semua Tahun</option>
												<?php foreach ($tahunAngkatan as $tahun): ?>
													<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="row g-3 align-items-end">
										<div class="col-md-5">
											<label for="mahasiswaSelect3" class="modern-filter-label">
												<i class="bi bi-person-fill me-1"></i> Mahasiswa <span class="text-danger">*</span>
											</label>
											<select class="form-select modern-filter-input" id="mahasiswaSelect3" name="mahasiswa_id">
												<option value="">Pilih Mahasiswa</option>
											</select>
										</div>
										<div class="col-md-5">
											<label for="tahunAkademikSelect3" class="modern-filter-label">
												<i class="bi bi-calendar-event me-1"></i> Tahun Akademik <span class="text-danger">*</span>
											</label>
											<select class="form-select modern-filter-input" id="tahunAkademikSelect3" name="tahun_akademik_filter">
												<option value="">Pilih Tahun Akademik</option>
												<?php foreach ($tahunAkademikList as $ta): ?>
													<option value="<?= esc($ta) ?>"><?= esc($ta) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-2">
											<button type="submit" class="btn btn-primary modern-filter-btn w-100">
												<i class="bi bi-search"></i> Tampilkan
											</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<!-- Chart Section Individual -->
				<div id="chartSectionIndividual" class="d-none"></div>

				<!-- Detailed Calculation Table Individual -->
				<div id="detailCalculationIndividual" class="d-none">
					<div class="card mt-4">
						<div class="card-body">
							<div id="detailCalculationContent"></div>
						</div>
					</div>
				</div>

				<!-- Empty State Individual -->
				<div id="emptyStateIndividual" class="text-center py-5">
					<i class="bi bi-bar-chart" style="font-size: 4rem; color: #ccc;"></i>
					<p class="text-muted mt-3">Pilih mahasiswa dan klik tombol search untuk melihat grafik capaian CPMK</p>
				</div>
			</div>

			<!-- Comparative Tab (Angkatan) -->
			<div class="tab-pane fade" id="comparative" role="tabpanel">
				<!-- Sub-tabs for different filter types -->
				<div class="row g-3 mb-4">
					<div class="col-md-4">
						<div class="card filter-card-comparative h-100 border-primary shadow-sm" id="filterComparative1-card" role="button" data-bs-toggle="tab" data-bs-target="#filterComparative1" style="cursor: pointer; transition: all 0.3s;">
							<div class="card-body text-center p-3">
								<div class="mb-2">
									<i class="bi bi-people-fill text-primary" style="font-size: 1.8rem;"></i>
								</div>
								<h6 class="card-title mb-2 text-primary fw-bold">CPMK Angkatan</h6>
								<p class="card-text text-muted small mb-0">Perhitungan rata-rata CPMK angkatan dilakukan dengan menjumlahkan seluruh capaian CPMK mahasiswa dalam satu angkatan.</p>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card filter-card-comparative h-100 shadow-sm" id="filterComparative2-card" role="button" data-bs-toggle="tab" data-bs-target="#filterComparative2" style="cursor: pointer; transition: all 0.3s;">
							<div class="card-body text-center p-3">
								<div class="mb-2">
									<i class="bi bi-calendar-range text-secondary" style="font-size: 1.8rem;"></i>
								</div>
								<h6 class="card-title mb-2">CPMK Angkatan per Semester</h6>
								<p class="card-text text-muted small mb-0">Perhitungan rata-rata CPMK angkatan per semester dilakukan dengan menjumlahkan capaian CPMK mahasiswa dalam satu semester.</p>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card filter-card-comparative h-100 shadow-sm" id="filterComparative3-card" role="button" data-bs-toggle="tab" data-bs-target="#filterComparative3" style="cursor: pointer; transition: all 0.3s;">
							<div class="card-body text-center p-3">
								<div class="mb-2">
									<i class="bi bi-calendar2-event text-secondary" style="font-size: 1.8rem;"></i>
								</div>
								<h6 class="card-title mb-2">CPMK Angkatan per Tahun Akademik</h6>
								<p class="card-text text-muted small mb-0">Perhitungan rata-rata CPMK angkatan per tahun akademik dilakukan dengan menjumlahkan capaian CPMK mahasiswa dalam 1 tahun akademik.</p>
							</div>
						</div>
					</div>
				</div>

				<div class="tab-content" id="filterComparativeSubTabContent">
					<!-- Filter 1: CPMK Angkatan -->
					<div class="tab-pane fade show active" id="filterComparative1" role="tabpanel">
						<div class="modern-filter-wrapper mb-4">
							<div class="modern-filter-header">
								<div class="d-flex align-items-center gap-2">
									<i class="bi bi-funnel-fill text-primary"></i>
									<span class="modern-filter-title">Filter CPMK Angkatan</span>
								</div>
							</div>
							<div class="modern-filter-body">
								<form id="filterComparativeForm1">
									<div class="row g-3 align-items-end">
										<div class="col-md-5">
											<label for="programStudiComparativeSelect1" class="modern-filter-label">
												<i class="bi bi-mortarboard-fill me-1"></i> Program Studi <span class="text-danger">*</span>
											</label>
											<select class="form-select modern-filter-input" id="programStudiComparativeSelect1" name="program_studi" required>
												<option value="">Pilih Program Studi</option>
												<?php foreach ($programStudi as $kode => $prodi): ?>
													<option value="<?= esc($kode) ?>" <?= ucwords(strtolower($prodi)) === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-5">
											<label for="tahunAngkatanComparativeSelect1" class="modern-filter-label">
												<i class="bi bi-calendar3 me-1"></i> Tahun Angkatan <span class="text-danger">*</span>
											</label>
											<select class="form-select modern-filter-input" id="tahunAngkatanComparativeSelect1" name="tahun_angkatan" required>
												<option value="">Pilih Tahun Angkatan</option>
												<?php foreach ($tahunAngkatan as $tahun): ?>
													<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-2 d-flex gap-2">
											<button type="submit" class="btn btn-primary modern-filter-btn flex-fill">
												<i class="bi bi-search"></i>
											</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>

					<!-- Filter 2: CPMK Angkatan per Semester -->
					<div class="tab-pane fade" id="filterComparative2" role="tabpanel">
						<div class="modern-filter-wrapper mb-4">
							<div class="modern-filter-header">
								<div class="d-flex align-items-center gap-2">
									<i class="bi bi-funnel-fill text-primary"></i>
									<span class="modern-filter-title">Filter CPMK Angkatan per Semester</span>
								</div>
							</div>
							<div class="modern-filter-body">
								<form id="filterComparativeForm2">
									<div class="row g-3 align-items-end">
										<div class="col-md-4">
											<label for="programStudiComparativeSelect2" class="modern-filter-label">
												<i class="bi bi-mortarboard-fill me-1"></i> Program Studi <span class="text-danger">*</span>
											</label>
											<select class="form-select modern-filter-input" id="programStudiComparativeSelect2" name="program_studi" required>
												<option value="">Pilih Program Studi</option>
												<?php foreach ($programStudi as $kode => $prodi): ?>
													<option value="<?= esc($kode) ?>" <?= ucwords(strtolower($prodi)) === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-3">
											<label for="tahunAngkatanComparativeSelect2" class="modern-filter-label">
												<i class="bi bi-calendar3 me-1"></i> Tahun Angkatan <span class="text-danger">*</span>
											</label>
											<select class="form-select modern-filter-input" id="tahunAngkatanComparativeSelect2" name="tahun_angkatan" required>
												<option value="">Pilih Tahun Angkatan</option>
												<?php foreach ($tahunAngkatan as $tahun): ?>
													<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-3">
											<label for="semesterComparativeSelect2" class="modern-filter-label">
												<i class="bi bi-bookmark-fill me-1"></i> Semester <span class="text-danger">*</span>
											</label>
											<select class="form-select modern-filter-input" id="semesterComparativeSelect2" name="semester" required>
												<option value="">Pilih Semester</option>
												<?php foreach ($semesterList as $semester): ?>
													<option value="<?= esc($semester) ?>"><?= esc($semester) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-2">
											<button type="submit" class="btn btn-primary modern-filter-btn w-100">
												<i class="bi bi-search"></i>
											</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>

					<!-- Filter 3: CPMK Angkatan per Tahun Akademik -->
					<div class="tab-pane fade" id="filterComparative3" role="tabpanel">
						<div class="modern-filter-wrapper mb-4">
							<div class="modern-filter-header">
								<div class="d-flex align-items-center gap-2">
									<i class="bi bi-funnel-fill text-primary"></i>
									<span class="modern-filter-title">Filter CPMK Angkatan per Tahun Akademik</span>
								</div>
							</div>
							<div class="modern-filter-body">
								<form id="filterComparativeForm3">
									<div class="row g-3 align-items-end">
										<div class="col-md-4">
											<label for="programStudiComparativeSelect3" class="modern-filter-label">
												<i class="bi bi-mortarboard-fill me-1"></i> Program Studi <span class="text-danger">*</span>
											</label>
											<select class="form-select modern-filter-input" id="programStudiComparativeSelect3" name="program_studi" required>
												<option value="">Pilih Program Studi</option>
												<?php foreach ($programStudi as $kode => $prodi): ?>
													<option value="<?= esc($kode) ?>" <?= ucwords(strtolower($prodi)) === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-3">
											<label for="tahunAngkatanComparativeSelect3" class="modern-filter-label">
												<i class="bi bi-calendar3 me-1"></i> Tahun Angkatan <span class="text-danger">*</span>
											</label>
											<select class="form-select modern-filter-input" id="tahunAngkatanComparativeSelect3" name="tahun_angkatan" required>
												<option value="">Pilih Tahun Angkatan</option>
												<?php foreach ($tahunAngkatan as $tahun): ?>
													<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-3">
											<label for="tahunAkademikComparativeSelect3" class="modern-filter-label">
												<i class="bi bi-calendar-event me-1"></i> Tahun Akademik <span class="text-danger">*</span>
											</label>
											<select class="form-select modern-filter-input" id="tahunAkademikComparativeSelect3" name="tahun_akademik_filter" required>
												<option value="">Pilih Tahun Akademik</option>
												<?php foreach ($tahunAkademikList as $ta): ?>
													<option value="<?= esc($ta) ?>"><?= esc($ta) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-2">
											<button type="submit" class="btn btn-primary modern-filter-btn w-100">
												<i class="bi bi-search"></i>
											</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>

				<!-- Chart Section Comparative -->
				<div id="chartSectionComparative" class="d-none"></div>

				<!-- Detailed Calculation Table Comparative -->
				<div id="detailCalculationComparative" class="d-none">
					<div class="card mt-4">
						<div class="card-body">
							<div id="detailCalculationComparativeContent"></div>
						</div>
					</div>
				</div>

				<!-- Empty State Comparative -->
				<div id="emptyStateComparative" class="text-center py-5">
					<i class="bi bi-people" style="font-size: 4rem; color: #ccc;"></i>
					<p class="text-muted mt-3">Pilih program studi dan tahun angkatan untuk melihat grafik rata-rata capaian CPMK</p>
				</div>
			</div>

			<!-- Keseluruhan Tab -->
			<div class="tab-pane fade" id="keseluruhan" role="tabpanel">
				<!-- Filter Section Keseluruhan -->
				<div class="modern-filter-wrapper mb-4">
					<div class="modern-filter-header">
						<div class="d-flex align-items-center gap-2">
							<i class="bi bi-funnel-fill text-primary"></i>
							<span class="modern-filter-title">Filter CPMK Keseluruhan</span>
						</div>
					</div>
					<div class="modern-filter-body">
						<form id="filterKeseluruhanForm">
							<div class="row g-3 align-items-end">
								<div class="col-md-10">
									<label for="programStudiKeseluruhanSelect" class="modern-filter-label">
										<i class="bi bi-mortarboard-fill me-1"></i> Program Studi <span class="text-danger">*</span>
									</label>
									<select class="form-select modern-filter-input" id="programStudiKeseluruhanSelect" name="program_studi" required>
										<option value="">Pilih Program Studi</option>
										<?php foreach ($programStudi as $kode => $prodi): ?>
											<option value="<?= esc($kode) ?>" <?= ucwords(strtolower($prodi)) === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-md-2 d-flex gap-2">
									<button type="submit" class="btn btn-primary modern-filter-btn flex-fill">
										<i class="bi bi-search"></i>
									</button>
								</div>
							</div>
						</form>
					</div>
				</div>

				<!-- Chart Section Keseluruhan -->
				<div id="chartSectionKeseluruhan" class="d-none"></div>

				<!-- Detailed Calculation Table Keseluruhan -->
				<div id="detailCalculationKeseluruhan" class="d-none">
					<div class="card mt-4">
						<div class="card-body">
							<div id="detailCalculationKeseluruhanContent"></div>
						</div>
					</div>
				</div>

				<!-- Empty State Keseluruhan -->
				<div id="emptyStateKeseluruhan" class="text-center py-5">
					<i class="bi bi-bar-chart-line" style="font-size: 4rem; color: #ccc;"></i>
					<p class="text-muted mt-3">Pilih program studi untuk melihat grafik rata-rata capaian CPMK dari semua angkatan</p>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Detail CPMK -->
<div class="modal fade" id="detailCpmkModal" tabindex="-1">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-body">
				<div id="detailCpmkModalContent">
					<div class="text-center py-4">
						<div class="spinner-border text-primary" role="status">
							<span class="visually-hidden">Loading...</span>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.2.0/dist/chartjs-plugin-datalabels.min.js"></script>
<script src="<?= base_url('js/modern-chart-component.js') ?>"></script>

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<!-- Modern Table CSS -->
<link href="<?= base_url('css/modern-table.css') ?>" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
	// Dynamic passing threshold from grade configuration
	const passingThreshold = <?= json_encode($passing_threshold ?? 65) ?>;

	// Chart.js plugin to add white background
	const whiteBackgroundPlugin = {
		id: 'whiteBackground',
		beforeDraw: (chart) => {
			const ctx = chart.ctx;
			ctx.save();
			ctx.fillStyle = '#ffffff';
			ctx.fillRect(0, 0, chart.width, chart.height);
			ctx.restore();
		}
	};

	let cpmkChartIndividual = null;
	let cpmkChartComparative = null;
	let cpmkChartKeseluruhan = null;
	let currentIndividualData = null;
	let currentComparativeData = null;
	let currentKeseluruhanData = null;
	let currentActiveFilter = 1; // Track which filter is currently active for Individual tab (1, 2, or 3)
	let currentActiveComparativeFilter = 1; // Track which filter is currently active for Comparative tab (1, 2, or 3)

	// Pagination Helper Function
	function initPagination(tableId, rowsPerPage = 10) {
		const table = document.getElementById(tableId);
		if (!table) return;

		const tbody = table.querySelector('tbody');
		const rows = Array.from(tbody.querySelectorAll('tr'));
		const totalRows = rows.length;
		const totalPages = Math.ceil(totalRows / rowsPerPage);
		let currentPage = 1;

		// Create pagination controls
		const paginationId = `${tableId}_pagination`;
		let paginationContainer = document.getElementById(paginationId);

		if (!paginationContainer) {
			paginationContainer = document.createElement('div');
			paginationContainer.id = paginationId;
			paginationContainer.className = 'd-flex justify-content-between align-items-center mt-3';
			table.parentElement.appendChild(paginationContainer);
		}

		function showPage(page) {
			currentPage = page;
			const start = (page - 1) * rowsPerPage;
			const end = start + rowsPerPage;

			rows.forEach((row, index) => {
				row.style.display = (index >= start && index < end) ? '' : 'none';
			});

			renderPagination();
		}

		function renderPagination() {
			const startEntry = totalRows === 0 ? 0 : ((currentPage - 1) * rowsPerPage) + 1;
			const endEntry = Math.min(currentPage * rowsPerPage, totalRows);

			let html = `
				<div class="text-muted small">
					Menampilkan ${startEntry} sampai ${endEntry} dari ${totalRows} data
				</div>
				<nav>
					<ul class="pagination pagination-sm mb-0">
			`;

			// Previous button
			html += `
				<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
					<a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
				</li>
			`;

			// Page numbers
			const maxVisiblePages = 5;
			let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
			let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

			if (endPage - startPage + 1 < maxVisiblePages) {
				startPage = Math.max(1, endPage - maxVisiblePages + 1);
			}

			if (startPage > 1) {
				html += `<li class="page-item"><a class="page-link" href="#" data-page="1">1</a></li>`;
				if (startPage > 2) {
					html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
				}
			}

			for (let i = startPage; i <= endPage; i++) {
				html += `
					<li class="page-item ${i === currentPage ? 'active' : ''}">
						<a class="page-link" href="#" data-page="${i}">${i}</a>
					</li>
				`;
			}

			if (endPage < totalPages) {
				if (endPage < totalPages - 1) {
					html += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
				}
				html += `<li class="page-item"><a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a></li>`;
			}

			// Next button
			html += `
				<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
					<a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
				</li>
			`;

			html += `
					</ul>
				</nav>
			`;

			paginationContainer.innerHTML = html;

			// Add event listeners
			paginationContainer.querySelectorAll('a.page-link').forEach(link => {
				link.addEventListener('click', function(e) {
					e.preventDefault();
					const page = parseInt(this.getAttribute('data-page'));
					if (page >= 1 && page <= totalPages) {
						showPage(page);
					}
				});
			});
		}

		// Initialize first page
		showPage(1);
	}

	$(document).ready(function() {
		// Handle modern tab item interactions
		$('.modern-tab-item').on('click', function() {
			// Remove active state from all tab items
			$('.modern-tab-item').removeClass('active');

			// Add active state to clicked item
			$(this).addClass('active');
		});

		// Handle filter card interactions and active states for Individual (Mahasiswa) tab
		$('.filter-card').on('click', function() {
			const targetTab = $(this).data('bs-target');

			// Remove active state from all cards
			$('.filter-card').removeClass('border-primary shadow').addClass('shadow-sm');
			$('.filter-card .card-title').removeClass('text-primary fw-bold');
			$('.filter-card i').removeClass('text-primary').addClass('text-secondary');

			// Add active state to clicked card
			$(this).removeClass('shadow-sm').addClass('border-primary shadow');
			$(this).find('.card-title').addClass('text-primary fw-bold');
			$(this).find('i').removeClass('text-secondary').addClass('text-primary');

			// Hide all tab panes
			$('#filterSubTabContent .tab-pane').removeClass('show active');

			// Show the target tab pane
			$(targetTab).addClass('show active');
		});

		// Add hover effect for filter cards
		$('.filter-card').hover(
			function() {
				if (!$(this).hasClass('border-primary')) {
					$(this).removeClass('shadow-sm').addClass('shadow');
				}
			},
			function() {
				if (!$(this).hasClass('border-primary')) {
					$(this).removeClass('shadow').addClass('shadow-sm');
				}
			}
		);

		// Handle filter card interactions and active states for Comparative (Angkatan) tab
		$('.filter-card-comparative').on('click', function() {
			const targetTab = $(this).data('bs-target');

			// Remove active state from all cards
			$('.filter-card-comparative').removeClass('border-primary shadow').addClass('shadow-sm');
			$('.filter-card-comparative .card-title').removeClass('text-primary fw-bold');
			$('.filter-card-comparative i').removeClass('text-primary').addClass('text-secondary');

			// Add active state to clicked card
			$(this).removeClass('shadow-sm').addClass('border-primary shadow');
			$(this).find('.card-title').addClass('text-primary fw-bold');
			$(this).find('i').removeClass('text-secondary').addClass('text-primary');

			// Hide all tab panes
			$('#filterComparativeSubTabContent .tab-pane').removeClass('show active');

			// Show the target tab pane
			$(targetTab).addClass('show active');
		});

		// Add hover effect for filter cards
		$('.filter-card-comparative').hover(
			function() {
				if (!$(this).hasClass('border-primary')) {
					$(this).removeClass('shadow-sm').addClass('shadow');
				}
			},
			function() {
				if (!$(this).hasClass('border-primary')) {
					$(this).removeClass('shadow').addClass('shadow-sm');
				}
			}
		);

		// Initialize Select2 for Filter 1 (CPMK Mahasiswa)
		$('#programStudiSelect1').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Semua Program Studi --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Program studi tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				}
			}
		});

		$('#tahunAngkatanSelect1').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Semua Tahun --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Tahun angkatan tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				}
			}
		});

		$('#mahasiswaSelect1').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Mahasiswa --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Mahasiswa tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				},
				inputTooShort: function() {
					return "Ketik untuk mencari...";
				}
			}
		});

		// Initialize Select2 for Filter 2 (CPMK per Semester)
		$('#programStudiSelect2').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Semua Program Studi --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Program studi tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				}
			}
		});

		$('#tahunAngkatanSelect2').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Semua Tahun --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Tahun angkatan tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				}
			}
		});

		$('#mahasiswaSelect2').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Mahasiswa --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Mahasiswa tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				},
				inputTooShort: function() {
					return "Ketik untuk mencari...";
				}
			}
		});

		$('#semesterSelect2').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Semester --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Semester tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				}
			}
		});

		// Initialize Select2 for Filter 3 (CPMK per Tahun Akademik)
		$('#programStudiSelect3').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Semua Program Studi --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Program studi tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				}
			}
		});

		$('#tahunAngkatanSelect3').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Semua Tahun --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Tahun angkatan tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				}
			}
		});

		$('#mahasiswaSelect3').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Mahasiswa --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Mahasiswa tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				},
				inputTooShort: function() {
					return "Ketik untuk mencari...";
				}
			}
		});

		$('#tahunAkademikSelect3').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Tahun Akademik --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Tahun akademik tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				}
			}
		});

		// Initialize Select2 for Comparative Filter 1
		$('#programStudiComparativeSelect1').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Program Studi --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Program studi tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				}
			}
		});

		$('#tahunAngkatanComparativeSelect1').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Tahun Angkatan --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Tahun angkatan tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				}
			}
		});

		// Initialize Select2 for Comparative Filter 2
		$('#programStudiComparativeSelect2').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Program Studi --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Program studi tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				}
			}
		});

		$('#tahunAngkatanComparativeSelect2').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Tahun Angkatan --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Tahun angkatan tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				}
			}
		});

		$('#semesterComparativeSelect2').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Semester --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Semester tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				}
			}
		});

		// Initialize Select2 for Comparative Filter 3
		$('#programStudiComparativeSelect3').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Program Studi --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Program studi tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				}
			}
		});

		$('#tahunAngkatanComparativeSelect3').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Tahun Angkatan --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Tahun angkatan tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				}
			}
		});

		$('#tahunAkademikComparativeSelect3').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Tahun Akademik --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Tahun akademik tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				}
			}
		});

		// Initialize Select2 on Program Studi dropdown (Keseluruhan Tab)
		$('#programStudiKeseluruhanSelect').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Program Studi --',
			allowClear: true,
			width: '100%',
			language: {
				noResults: function() {
					return "Program studi tidak ditemukan";
				},
				searching: function() {
					return "Mencari...";
				}
			}
		});

		// Load initial data for all filters
		loadMahasiswa(1);
		loadMahasiswa(2);
		loadMahasiswa(3);

		// Individual Tab Events - Filter 1
		$('#programStudiSelect1, #tahunAngkatanSelect1').on('change', function() {
			loadMahasiswa(1);
		});

		$('#filterIndividualForm1').on('submit', function(e) {
			e.preventDefault();
			currentActiveFilter = 1;
			loadIndividualChartData(1);
		});

		// Individual Tab Events - Filter 2
		$('#programStudiSelect2, #tahunAngkatanSelect2').on('change', function() {
			loadMahasiswa(2);
		});

		$('#filterIndividualForm2').on('submit', function(e) {
			e.preventDefault();
			currentActiveFilter = 2;
			loadIndividualChartData(2);
		});

		// Individual Tab Events - Filter 3
		$('#programStudiSelect3, #tahunAngkatanSelect3').on('change', function() {
			loadMahasiswa(3);
		});

		$('#filterIndividualForm3').on('submit', function(e) {
			e.preventDefault();
			currentActiveFilter = 3;
			loadIndividualChartData(3);
		});

		// Comparative Tab Events - Filter 1
		$('#filterComparativeForm1').on('submit', function(e) {
			e.preventDefault();
			currentActiveComparativeFilter = 1;
			loadComparativeChartData(1);
		});

		// Comparative Tab Events - Filter 2
		$('#filterComparativeForm2').on('submit', function(e) {
			e.preventDefault();
			currentActiveComparativeFilter = 2;
			loadComparativeChartData(2);
		});

		// Comparative Tab Events - Filter 3
		$('#filterComparativeForm3').on('submit', function(e) {
			e.preventDefault();
			currentActiveComparativeFilter = 3;
			loadComparativeChartData(3);
		});

		// Keseluruhan Tab Events
		$('#filterKeseluruhanForm').on('submit', function(e) {
			e.preventDefault();
			loadKeseluruhanChartData();
		});

		// Auto-load data when Keseluruhan tab is shown
		$('#keseluruhan-tab').on('shown.bs.tab', function() {
			const programStudi = $('#programStudiKeseluruhanSelect').val();
			// If a program studi is already selected (default is Teknik Informatika), load the data automatically
			if (programStudi) {
				loadKeseluruhanChartData();
			}
		});
	});

	function loadMahasiswa(filterNum) {
		const programStudi = $(`#programStudiSelect${filterNum}`).val();
		const tahunAngkatan = $(`#tahunAngkatanSelect${filterNum}`).val();

		console.log(`Loading mahasiswa for filter ${filterNum} with filters:`, {
			programStudi,
			tahunAngkatan
		});

		$.ajax({
			url: '<?= base_url("admin/capaian-cpmk/mahasiswa") ?>',
			method: 'GET',
			data: {
				program_studi: programStudi,
				tahun_angkatan: tahunAngkatan
			},
			success: function(response) {
				console.log(`Mahasiswa response for filter ${filterNum}:`, response);
				const mahasiswaSelect = $(`#mahasiswaSelect${filterNum}`);

				// Clear existing options
				mahasiswaSelect.html('<option value="">-- Pilih Mahasiswa --</option>');

				if (response && response.length > 0) {
					response.forEach(function(mhs) {
						mahasiswaSelect.append(`<option value="${mhs.id}">${mhs.nim} - ${mhs.nama_lengkap}</option>`);
					});
					mahasiswaSelect.prop('disabled', false);
					console.log(`Loaded ${response.length} mahasiswa for filter ${filterNum}`);
				} else {
					mahasiswaSelect.prop('disabled', true);
					console.log(`No mahasiswa found for filter ${filterNum}`);
				}

				// Refresh Select2 after updating options
				mahasiswaSelect.trigger('change.select2');
			},
			error: function(xhr, status, error) {
				console.error(`Error loading mahasiswa for filter ${filterNum}:`, {
					xhr,
					status,
					error
				});
				console.error('Response text:', xhr.responseText);
				alert('Error loading mahasiswa: ' + error);
			}
		});
	}

	function loadIndividualChartData(filterNum) {
		const mahasiswaId = $(`#mahasiswaSelect${filterNum}`).val();

		// Build the validation and data object based on filter type
		let validationMessage = 'Silakan pilih mahasiswa terlebih dahulu';
		let ajaxData = {
			mahasiswa_id: mahasiswaId,
			program_studi: $(`#programStudiSelect${filterNum}`).val(),
			tahun_angkatan: $(`#tahunAngkatanSelect${filterNum}`).val()
		};

		// Filter-specific validation and data
		if (filterNum === 2) {
			const semester = $(`#semesterSelect${filterNum}`).val();
			if (!mahasiswaId || !semester) {
				alert('Silakan pilih mahasiswa dan semester terlebih dahulu');
				return;
			}
			ajaxData.semester = semester;
		} else if (filterNum === 3) {
			const tahunAkademik = $(`#tahunAkademikSelect${filterNum}`).val();
			if (!mahasiswaId || !tahunAkademik) {
				alert('Silakan pilih mahasiswa dan tahun akademik terlebih dahulu');
				return;
			}
			ajaxData.tahun_akademik = tahunAkademik;
		} else {
			// Filter 1 - just mahasiswa
			if (!mahasiswaId) {
				alert(validationMessage);
				return;
			}
		}

		$('#emptyStateIndividual').addClass('d-none');
		$('#detailCalculationIndividual').addClass('d-none');
		$('#chartSectionIndividual').removeClass('d-none').html(getLoadingHTML());

		$.ajax({
			url: '<?= base_url("admin/capaian-cpmk/chartDataIndividual") ?>',
			method: 'GET',
			data: ajaxData,
			success: function(response) {
				if (response.success) {
					currentIndividualData = response;
					displayIndividualChart(response);
					displayIndividualInfo(response);
				} else {
					showError('emptyStateIndividual', 'chartSectionIndividual', response.message);
				}
			},
			error: function() {
				showError('emptyStateIndividual', 'chartSectionIndividual', 'Terjadi kesalahan saat memuat data');
			}
		});
	}

	function loadComparativeChartData(filterNum) {
		const programStudi = $(`#programStudiComparativeSelect${filterNum}`).val();
		const tahunAngkatan = $(`#tahunAngkatanComparativeSelect${filterNum}`).val();

		// Build the validation and data object based on filter type
		let ajaxData = {
			program_studi: programStudi,
			tahun_angkatan: tahunAngkatan
		};

		// Filter-specific validation and data
		if (filterNum === 2) {
			const semester = $(`#semesterComparativeSelect${filterNum}`).val();
			if (!programStudi || !tahunAngkatan || !semester) {
				alert('Silakan pilih program studi, tahun angkatan, dan semester');
				return;
			}
			ajaxData.semester = semester;
		} else if (filterNum === 3) {
			const tahunAkademik = $(`#tahunAkademikComparativeSelect${filterNum}`).val();
			if (!programStudi || !tahunAngkatan || !tahunAkademik) {
				alert('Silakan pilih program studi, tahun angkatan, dan tahun akademik');
				return;
			}
			ajaxData.tahun_akademik = tahunAkademik;
		} else {
			// Filter 1 - just program studi and tahun angkatan
			if (!programStudi || !tahunAngkatan) {
				alert('Silakan pilih program studi dan tahun angkatan');
				return;
			}
		}

		$('#emptyStateComparative').addClass('d-none');
		$('#detailCalculationComparative').addClass('d-none');
		$('#chartSectionComparative').removeClass('d-none').html(getLoadingHTML('success'));

		$.ajax({
			url: '<?= base_url("admin/capaian-cpmk/comparativeData") ?>',
			method: 'GET',
			data: ajaxData,
			success: function(response) {
				if (response.success) {
					currentComparativeData = response;
					displayComparativeChart(response);
					displayComparativeInfo(response);
				} else {
					showError('emptyStateComparative', 'chartSectionComparative', response.message);
				}
			},
			error: function() {
				showError('emptyStateComparative', 'chartSectionComparative', 'Terjadi kesalahan saat memuat data');
			}
		});
	}

	function loadKeseluruhanChartData() {
		const programStudi = $('#programStudiKeseluruhanSelect').val();

		if (!programStudi) {
			alert('Silakan pilih program studi');
			return;
		}

		$('#emptyStateKeseluruhan').addClass('d-none');
		$('#detailCalculationKeseluruhan').addClass('d-none');
		$('#chartSectionKeseluruhan').removeClass('d-none').html(getLoadingHTML('info'));

		$.ajax({
			url: '<?= base_url("admin/capaian-cpmk/keseluruhanData") ?>',
			method: 'GET',
			data: {
				program_studi: programStudi
			},
			success: function(response) {
				if (response.success) {
					currentKeseluruhanData = response;
					displayKeseluruhanChart(response);
					displayKeseluruhanInfo(response);
				} else {
					showError('emptyStateKeseluruhan', 'chartSectionKeseluruhan', response.message);
				}
			},
			error: function() {
				showError('emptyStateKeseluruhan', 'chartSectionKeseluruhan', 'Terjadi kesalahan saat memuat data');
			}
		});
	}

	function displayIndividualInfo(response) {
		const info = `
        <div class="row">
            <div class="col-md-3">
                <strong>NIM:</strong> ${response.mahasiswa.nim}
            </div>
            <div class="col-md-4">
                <strong>Nama:</strong> ${response.mahasiswa.nama_lengkap}
            </div>
            <div class="col-md-3">
                <strong>Program Studi:</strong> ${response.mahasiswa.program_studi}
            </div>
            <div class="col-md-2">
                <strong>Angkatan:</strong> ${response.mahasiswa.tahun_angkatan}
            </div>
        </div>
    `;
		$('#infoContentIndividual').html(info);
		$('#infoSectionIndividual').removeClass('d-none');
	}

	function displayComparativeInfo(response) {
		const info = `
        <div class="row">
            <div class="col-md-4">
                <strong>Program Studi:</strong> ${response.programStudi}
            </div>
            <div class="col-md-4">
                <strong>Tahun Angkatan:</strong> ${response.tahunAngkatan}
            </div>
            <div class="col-md-4">
                <strong>Total Mahasiswa:</strong> ${response.totalMahasiswa} orang
            </div>
        </div>
    `;
		$('#infoContentComparative').html(info);
		$('#infoSectionComparative').removeClass('d-none');
	}

	function displayKeseluruhanInfo(response) {
		const angkatanStr = response.angkatanList.join(', ');
		const info = `
        <div class="row">
            <div class="col-md-4">
                <strong>Program Studi:</strong> ${response.programStudi}
            </div>
            <div class="col-md-4">
                <strong>Total Mahasiswa:</strong> ${response.totalMahasiswa} orang
            </div>
            <div class="col-md-4">
                <strong>Angkatan:</strong> ${angkatanStr}
            </div>
        </div>
    `;
		$('#infoContentKeseluruhan').html(info);
		$('#infoSectionKeseluruhan').removeClass('d-none');
	}

	function displayIndividualChart(response) {
		const chartHTML = createChartHTML('Individual', 'primary', 'cpmkChartIndividual');
		$('#chartSectionIndividual').html(chartHTML);

		const ctx = document.getElementById('cpmkChartIndividual').getContext('2d');
		if (cpmkChartIndividual) cpmkChartIndividual.destroy();

		cpmkChartIndividual = createBarChart(ctx, response.chartData, 'Capaian CPMK Mahasiswa', 'rgba(13, 110, 253, 0.8)', 'rgba(13, 110, 253, 1)');

		// Bind export button AFTER chart is created
		bindExportButton('#exportChartIndividualBtn', cpmkChartIndividual, 'capaian-cpmk-individual.png');

		// Display detailed calculation breakdown
		displayDetailedCalculation(response);
	}

	function displayDetailedCalculation(response) {
		const mahasiswaId = $('#mahasiswaSelect').val();

		if (!response.chartData || !response.chartData.details) {
			$('#detailCalculationIndividual').addClass('d-none');
			return;
		}

		// Group details by CPMK code
		const groupedByCpmk = {};
		response.chartData.details.forEach(item => {
			if (!groupedByCpmk[item.kode_cpmk]) {
				groupedByCpmk[item.kode_cpmk] = {
					kode_cpmk: item.kode_cpmk,
					cpmk_id: item.cpmk_id,
					deskripsi: item.deskripsi,
					courses: []
				};
			}
			groupedByCpmk[item.kode_cpmk].courses.push(item);
		});

		// Get aggregated values from chartData
		const cpmkValues = {};
		response.chartData.labels.forEach((label, index) => {
			cpmkValues[label] = response.chartData.data[index];
		});

		let html = '<div class="modern-table-wrapper"><table id="individualDetailTable" class="modern-table">';
		html += '<thead>';
		html += '<tr>';
		html += '<th width="5%" class="text-center">No</th>';
		html += '<th width="15%">Kode CPMK</th>';
		html += '<th width="35%">Deskripsi CPMK</th>';
		html += '<th width="12%" class="text-center">Jumlah MK</th>';
		html += '<th width="13%" class="text-center">Capaian (%)</th>';
		html += '<th width="10%" class="text-center">Aksi</th>';
		html += '</tr>';
		html += '</thead>';
		html += '<tbody>';

		let rowIndex = 0;
		Object.keys(groupedByCpmk).forEach(kodeCpmk => {
			const cpmk = groupedByCpmk[kodeCpmk];
			const nilai = cpmkValues[kodeCpmk] || 0;
			const statusBadge = getStatusBadge(nilai);

			html += `
				<tr data-row-index="${rowIndex}">
					<td class="text-center">${rowIndex + 1}</td>
					<td><strong>${cpmk.kode_cpmk}</strong></td>
					<td><small>${cpmk.deskripsi}</small></td>
					<td class="text-center">${cpmk.courses.length}</td>
					<td class="text-center"><strong>${nilai.toFixed(2)}%</strong></td>
					<td class="text-center">
						<button class="btn btn-sm btn-primary" onclick="loadIndividualCpmkDetail('${cpmk.kode_cpmk}', ${rowIndex})">
							<i class="bi bi-calculator"></i> Detail
						</button>
					</td>
				</tr>
			`;
			rowIndex++;
		});

		html += '</tbody></table></div>';

		$('#detailCalculationContent').html(html);
		$('#detailCalculationIndividual').removeClass('d-none');

		// Initialize pagination
		setTimeout(() => initPagination('individualDetailTable', 10), 100);
	}

	function loadIndividualCpmkDetail(kodeCpmk, index) {
		// Set modal title
		$('#detailCpmkModalTitle').text(`Detail Perhitungan ${kodeCpmk}`);

		// Show loading state in modal
		$('#detailCpmkModalContent').html(`
			<div class="text-center py-4">
				<div class="spinner-border text-primary" role="status">
					<span class="visually-hidden">Loading...</span>
				</div>
				<p class="mt-3 text-muted">Memuat detail perhitungan...</p>
			</div>
		`);

		// Show modal
		const modal = new bootstrap.Modal(document.getElementById('detailCpmkModal'));
		modal.show();

		// Use currentActiveFilter to get the right mahasiswa ID and filter values
		const mahasiswaId = $(`#mahasiswaSelect${currentActiveFilter}`).val();

		// Build ajax data based on active filter
		let ajaxData = {
			mahasiswa_id: mahasiswaId,
			kode_cpmk: kodeCpmk
		};

		// Add filter-specific data
		if (currentActiveFilter === 2) {
			ajaxData.semester = $(`#semesterSelect${currentActiveFilter}`).val();
		} else if (currentActiveFilter === 3) {
			ajaxData.tahun_akademik = $(`#tahunAkademikSelect${currentActiveFilter}`).val();
		}

		$.ajax({
			url: '<?= base_url("admin/capaian-cpmk/individualCpmkDetailCalculation") ?>',
			method: 'GET',
			data: ajaxData,
			success: function(response) {
				if (response.success) {
					displayIndividualCpmkCalculationDetail(index, response);
				} else {
					$('#detailCpmkModalContent').html(`
						<div class="alert alert-warning mb-0">
							<i class="bi bi-exclamation-triangle"></i> ${response.message || 'Gagal memuat detail perhitungan'}
						</div>
					`);
				}
			},
			error: function(xhr, status, error) {
				console.error('Error loading detail:', error);
				$('#detailCpmkModalContent').html(`
					<div class="alert alert-danger mb-0">
						<i class="bi bi-x-circle"></i> Terjadi kesalahan saat memuat detail perhitungan
					</div>
				`);
			}
		});
	}

	function displayIndividualCpmkCalculationDetail(index, data) {

		let html = ``;

		// Display each course with its CPMK score breakdown
		if (data.courses && data.courses.length > 0) {
			html += `
				<div class="modern-table-wrapper mb-4">
					<table class="modern-table">
						<thead>
							<tr>
								<th>Mata Kuliah</th>
								<th class="text-center">Semester</th>
								<th class="text-center">Kelas</th>
								<th class="text-center">Nilai CPMK</th>
								<th class="text-center">Bobot</th>
								<th class="text-center">Capaian (%)</th>
							</tr>
						</thead>
						<tbody>
			`;

			data.courses.forEach(course => {
				const kelasLabel = course.kelas === 'KM'
					? '<span class="badge bg-info">MBKM</span>'
					: course.kelas;

				html += `
					<tr>
						<td><strong>${course.kode_mk}</strong> - ${course.nama_mk}</td>
						<td class="text-center">${course.tahun_akademik}</td>
						<td class="text-center">${kelasLabel}</td>
						<td class="text-center">${course.nilai_cpmk.toFixed(2)}</td>
						<td class="text-center">${course.bobot}</td>
						<td class="text-center">${course.capaian}%</td>
					</tr>
				`;
			});

			html += `
						</tbody>
						<tfoot>
							<tr>
								<td colspan="3" class="text-end"><strong>Total</strong></td>
								<td class="text-center"><strong>${data.summary.grand_total_nilai_cpmk}</strong></td>
								<td class="text-center"><strong>${data.summary.grand_total_bobot}</strong></td>
								<td class="text-center"><strong>${data.summary.capaian}%</strong></td>
							</tr>
						</tfoot>
					</table>
				</div>
			`;
		} else {
			html += `<p class="text-muted">Belum ada nilai</p>`;
		}

		// Display formula and final capaian
		html += `
			<div class="alert alert-primary mb-0">
				<h6 class="mb-2"><i class="bi bi-calculator"></i> Capaian ${data.kode_cpmk}:</h6>
				<p class="mb-1"><strong>Capaian CPMK</strong> = ${data.summary.grand_total_nilai_cpmk} / ${data.summary.grand_total_bobot} &times; 100 = ${data.summary.capaian}%</p>
			</div>
		`;

		$('#detailCpmkModalContent').html(html);
	}

	function displayComparativeChart(response) {
		const chartHTML = createChartHTML('Comparative', 'primary', 'cpmkChartComparative');
		$('#chartSectionComparative').html(chartHTML);

		const ctx = document.getElementById('cpmkChartComparative').getContext('2d');
		if (cpmkChartComparative) cpmkChartComparative.destroy();

		cpmkChartComparative = createBarChart(ctx, response.chartData, 'Rata-rata Capaian CPMK Angkatan', 'rgba(13, 110, 253, 0.8)', 'rgba(13, 110, 253, 1)');

		// Bind export button AFTER chart is created
		bindExportButton('#exportChartComparativeBtn', cpmkChartComparative, 'capaian-cpmk-comparative.png');

		// Display detailed calculation
		displayComparativeDetailedCalculation(response);
	}

	function displayKeseluruhanChart(response) {
		const chartHTML = createChartHTML('Keseluruhan', 'primary', 'cpmkChartKeseluruhan');
		$('#chartSectionKeseluruhan').html(chartHTML);

		const ctx = document.getElementById('cpmkChartKeseluruhan').getContext('2d');
		if (cpmkChartKeseluruhan) cpmkChartKeseluruhan.destroy();

		cpmkChartKeseluruhan = createBarChart(ctx, response.chartData, 'Rata-rata Capaian CPMK Keseluruhan', 'rgba(13, 110, 253, 0.8)', 'rgba(13, 110, 253, 1)');

		// Bind export button AFTER chart is created
		bindExportButton('#exportChartKeseluruhanBtn', cpmkChartKeseluruhan, 'capaian-cpmk-keseluruhan.png');

		// Display detailed calculation
		displayKeseluruhanDetailedCalculation(response);
	}

	function displayComparativeDetailedCalculation(response) {
		if (!response.chartData || !response.chartData.details) {
			$('#detailCalculationComparative').addClass('d-none');
			return;
		}

		let html = '<div class="modern-table-wrapper"><table id="comparativeDetailTable" class="modern-table">';
		html += '<thead>';
		html += '<tr>';
		html += '<th width="5%" class="text-center">No</th>';
		html += '<th width="15%">Kode CPMK</th>';
		html += '<th width="50%">Deskripsi CPMK</th>';
		html += '<th width="10%" class="text-center">Jumlah Mahasiswa</th>';
		html += '<th width="10%" class="text-center">Rata-rata (%)</th>';
		html += '<th width="10%" class="text-center">Aksi</th>';
		html += '</tr>';
		html += '</thead>';
		html += '<tbody>';

		response.chartData.details.forEach((cpmk, index) => {
			const statusBadge = getStatusBadge(cpmk.rata_rata);
			html += `
				<tr data-row-index="${index}" data-cpmk-id="${cpmk.cpmk_id}" data-kode-cpmk="${cpmk.kode_cpmk}">
					<td class="text-center">${index + 1}</td>
					<td><strong>${cpmk.kode_cpmk}</strong></td>
					<td><small>${cpmk.deskripsi}</small></td>
					<td class="text-center">${cpmk.jumlah_mahasiswa}</td>
					<td class="text-center"><strong>${cpmk.rata_rata.toFixed(2)}%</strong></td>
					<td class="text-center">
						<button class="btn btn-sm btn-primary" onclick="loadComparativeCpmkDetail(${cpmk.cpmk_id}, '${cpmk.kode_cpmk}', ${index})">
							<i class="bi bi-eye"></i> Detail
						</button>
					</td>
				</tr>
			`;
		});

		html += '</tbody></table></div>';

		$('#detailCalculationComparativeContent').html(html);
		$('#detailCalculationComparative').removeClass('d-none');

		// Initialize pagination
		setTimeout(() => initPagination('comparativeDetailTable', 10), 100);
	}

	function displayKeseluruhanDetailedCalculation(response) {
		if (!response.chartData || !response.chartData.details) {
			$('#detailCalculationKeseluruhan').addClass('d-none');
			return;
		}

		let html = '<div class="modern-table-wrapper"><table id="keseluruhanDetailTable" class="modern-table">';
		html += '<thead>';
		html += '<tr>';
		html += '<th width="5%" class="text-center">No</th>';
		html += '<th width="15%">Kode CPMK</th>';
		html += '<th width="50%">Deskripsi CPMK</th>';
		html += '<th width="10%" class="text-center">Jumlah Mahasiswa</th>';
		html += '<th width="10%" class="text-center">Rata-rata (%)</th>';
		html += '<th width="10%" class="text-center">Aksi</th>';
		html += '</tr>';
		html += '</thead>';
		html += '<tbody>';

		response.chartData.details.forEach((cpmk, index) => {
			const statusBadge = getStatusBadge(cpmk.rata_rata);
			html += `
				<tr data-row-index="${index}" data-cpmk-id="${cpmk.cpmk_id}" data-kode-cpmk="${cpmk.kode_cpmk}">
					<td class="text-center">${index + 1}</td>
					<td><strong>${cpmk.kode_cpmk}</strong></td>
					<td><small>${cpmk.deskripsi}</small></td>
					<td class="text-center">${cpmk.jumlah_mahasiswa}</td>
					<td class="text-center"><strong>${cpmk.rata_rata.toFixed(2)}%</strong></td>
					<td class="text-center">
						<button class="btn btn-sm btn-primary" onclick="loadKeseluruhanCpmkDetail(${cpmk.cpmk_id}, '${cpmk.kode_cpmk}', ${index})">
							<i class="bi bi-eye"></i> Detail
						</button>
					</td>
				</tr>
			`;
		});

		html += '</tbody></table></div>';

		$('#detailCalculationKeseluruhanContent').html(html);
		$('#detailCalculationKeseluruhan').removeClass('d-none');

		// Initialize pagination
		setTimeout(() => initPagination('keseluruhanDetailTable', 10), 100);
	}

	function loadComparativeCpmkDetail(cpmkId, kodeCpmk, index) {
		// Set modal title
		$('#detailCpmkModalTitle').text(`Detail Perhitungan ${kodeCpmk} - Angkatan`);

		// Show loading state in modal
		$('#detailCpmkModalContent').html(`
			<div class="text-center py-4">
				<div class="spinner-border text-primary" role="status">
					<span class="visually-hidden">Loading...</span>
				</div>
				<p class="mt-3 text-muted">Memuat detail perhitungan...</p>
			</div>
		`);

		// Show modal
		const modal = new bootstrap.Modal(document.getElementById('detailCpmkModal'));
		modal.show();

		// Use currentActiveComparativeFilter to get the right filter values
		const programStudi = $(`#programStudiComparativeSelect${currentActiveComparativeFilter}`).val();
		const tahunAngkatan = $(`#tahunAngkatanComparativeSelect${currentActiveComparativeFilter}`).val();

		// Build ajax data based on active filter
		let ajaxData = {
			cpmk_id: cpmkId,
			program_studi: programStudi,
			tahun_angkatan: tahunAngkatan
		};

		// Add filter-specific data
		if (currentActiveComparativeFilter === 2) {
			ajaxData.semester = $(`#semesterComparativeSelect${currentActiveComparativeFilter}`).val();
		} else if (currentActiveComparativeFilter === 3) {
			ajaxData.tahun_akademik = $(`#tahunAkademikComparativeSelect${currentActiveComparativeFilter}`).val();
		}

		$.ajax({
			url: '<?= base_url("admin/capaian-cpmk/comparativeDetailCalculation") ?>',
			method: 'GET',
			data: ajaxData,
			success: function(response) {
				if (response.success) {
					displayComparativeCpmkCalculationDetail(index, kodeCpmk, response.data, response.summary);
				} else {
					$('#detailCpmkModalContent').html('<div class="alert alert-warning mb-0">' + (response.message || 'Tidak ada data') + '</div>');
				}
			},
			error: function() {
				$('#detailCpmkModalContent').html('<div class="alert alert-danger mb-0">Terjadi kesalahan saat memuat data</div>');
			}
		});
	}

	function displayComparativeCpmkCalculationDetail(index, kodeCpmk, data, summary) {
		let html = ``;

		if (data.length === 0) {
			html += '<div class="alert alert-info mb-0">Belum ada mahasiswa yang memiliki nilai untuk CPMK ini</div>';
		} else {
			html += `
				<div class="modern-table-wrapper">
					<table id="comparativeCpmkDetailTable_${index}" class="modern-table mb-0">
						<thead>
							<tr>
								<th width="10%" class="text-center">No</th>
								<th width="20%">NIM</th>
								<th width="50%">Nama Mahasiswa</th>
								<th width="20%" class="text-center">Capaian CPMK (%)</th>
							</tr>
						</thead>
						<tbody>
			`;

			data.forEach((mhs, idx) => {
				html += `
					<tr>
						<td class="text-center">${idx + 1}</td>
						<td>${mhs.nim}</td>
						<td>${mhs.nama_lengkap}</td>
						<td class="text-center"><strong>${mhs.nilai_cpmk.toFixed(2)}%</strong></td>
					</tr>
				`;
			});

			html += `
						</tbody>
						<tfoot style="background: #f8f9fa;">
							<tr>
								<td colspan="3" class="text-end"><strong>Total dari ${summary.jumlah_mahasiswa} mahasiswa:</strong></td>
								<td class="text-center"><strong>${summary.total_nilai.toFixed(2)}%</strong></td>
							</tr>
							<tr style="background: #d1e7dd;">
								<td colspan="3" class="text-end"><strong>Rata - rata  = ${summary.total_nilai.toFixed(2)} / ${summary.jumlah_mahasiswa} mahasiswa</strong></td>
								<td class="text-center"><h6 class="mb-0"><strong>${summary.rata_rata.toFixed(2)}%</strong></h6></td>
							</tr>
						</tfoot>
					</table>
				</div>
			`;
		}

		$('#detailCpmkModalContent').html(html);

		// Initialize pagination
		if (data.length > 0) {
			setTimeout(() => initPagination(`comparativeCpmkDetailTable_${index}`, 10), 100);
		}
	}

	function loadKeseluruhanCpmkDetail(cpmkId, kodeCpmk, index) {
		// Set modal title
		$('#detailCpmkModalTitle').text(`Detail Perhitungan ${kodeCpmk} - Semua Angkatan`);

		// Show loading state in modal
		$('#detailCpmkModalContent').html(`
			<div class="text-center py-4">
				<div class="spinner-border text-primary" role="status">
					<span class="visually-hidden">Loading...</span>
				</div>
				<p class="mt-3 text-muted">Memuat detail perhitungan...</p>
			</div>
		`);

		// Show modal
		const modal = new bootstrap.Modal(document.getElementById('detailCpmkModal'));
		modal.show();

		const programStudi = $('#programStudiKeseluruhanSelect').val();

		$.ajax({
			url: '<?= base_url("admin/capaian-cpmk/keseluruhanDetailCalculation") ?>',
			method: 'GET',
			data: {
				cpmk_id: cpmkId,
				program_studi: programStudi
			},
			success: function(response) {
				if (response.success) {
					displayKeseluruhanCpmkCalculationDetail(index, kodeCpmk, response.data, response.summary);
				} else {
					$('#detailCpmkModalContent').html('<div class="alert alert-warning mb-0">' + (response.message || 'Tidak ada data') + '</div>');
				}
			},
			error: function() {
				$('#detailCpmkModalContent').html('<div class="alert alert-danger mb-0">Terjadi kesalahan saat memuat data</div>');
			}
		});
	}

	function displayKeseluruhanCpmkCalculationDetail(index, kodeCpmk, data, summary) {
		let html = ``;

		if (data.length === 0) {
			html += '<div class="alert alert-info mb-0">Belum ada mahasiswa yang memiliki nilai untuk CPMK ini</div>';
		} else {
			html += `
				<div class="modern-table-wrapper">
					<table id="keseluruhanCpmkDetailTable_${index}" class="modern-table mb-0">
						<thead>
							<tr>
								<th width="8%" class="text-center">No</th>
								<th width="15%">NIM</th>
								<th width="40%">Nama Mahasiswa</th>
								<th width="12%" class="text-center">Angkatan</th>
								<th width="25%" class="text-center">Capaian CPMK</th>
							</tr>
						</thead>
						<tbody>
			`;

			data.forEach((mhs, idx) => {
				html += `
					<tr>
						<td class="text-center">${idx + 1}</td>
						<td>${mhs.nim}</td>
						<td>${mhs.nama_lengkap}</td>
						<td class="text-center"><span class="badge bg-secondary">${mhs.tahun_angkatan}</span></td>
						<td class="text-center"><strong>${mhs.nilai_cpmk.toFixed(2)}%</strong></td>
					</tr>
				`;
			});

			html += `
						</tbody>
						<tfoot style="background: #f8f9fa;">
							<tr>
								<td colspan="4" class="text-end"><strong>Total dari ${summary.jumlah_mahasiswa} mahasiswa (semua angkatan):</strong></td>
								<td class="text-center"><strong>${summary.total_nilai.toFixed(2)}%</strong></td>
							</tr>
							<tr style="background: #d1e7dd;">
								<td colspan="4" class="text-end"><strong>Rata - rata = ${summary.total_nilai.toFixed(2)}% / ${summary.jumlah_mahasiswa} mahasiswa</strong></td>
								<td class="text-center"><h6 class="mb-0"><strong>${summary.rata_rata.toFixed(2)}%</strong></h6></td>
							</tr>
						</tfoot>
					</table>
				</div>
			`;
		}

		$('#detailCpmkModalContent').html(html);

		// Initialize pagination
		if (data.length > 0) {
			setTimeout(() => initPagination(`keseluruhanCpmkDetailTable_${index}`, 10), 100);
		}
	}

	// Helper Functions
	function createBarChart(ctx, chartData, title, backgroundColor, borderColor) {
		// Create modern gradient colors
		const createGradient = (ctx, color1, color2) => {
			const gradient = ctx.createLinearGradient(0, 0, 0, 400);
			gradient.addColorStop(0, color1);
			gradient.addColorStop(1, color2);
			return gradient;
		};

		// Create conditional colors based on passing threshold with gradients
		const backgroundColors = chartData.data.map((value, index) => {
			if (value < passingThreshold) {
				return createGradient(ctx, 'rgba(220, 53, 69, 0.9)', 'rgba(220, 53, 69, 0.6)');
			} else {
				return createGradient(ctx, 'rgba(13, 110, 253, 0.9)', 'rgba(13, 110, 253, 0.6)');
			}
		});

		const borderColors = chartData.data.map(value =>
			value < passingThreshold ? 'rgba(220, 53, 69, 1)' : 'rgba(13, 110, 253, 1)'
		);

		return new Chart(ctx, {
			type: 'bar',
			data: {
				labels: chartData.labels,
				datasets: [{
					label: 'Capaian CPMK',
					data: chartData.data,
					backgroundColor: backgroundColors,
					borderColor: borderColors,
					borderWidth: 0,
					borderRadius: 8,
					barThickness: 'flex',
					maxBarThickness: 60,
					// Add shadow effect
					shadowOffsetX: 0,
					shadowOffsetY: 4,
					shadowBlur: 8,
					shadowColor: 'rgba(0, 0, 0, 0.15)'
				}]
			},
			plugins: [ChartDataLabels, whiteBackgroundPlugin],
			options: {
				responsive: true,
				maintainAspectRatio: true,
				interaction: {
					intersect: false,
					mode: 'index'
				},
				animation: {
					duration: 1000,
					easing: 'easeInOutQuart'
				},
				plugins: {
					legend: {
						display: true,
						position: 'bottom',
						align: 'end',
						labels: {
							usePointStyle: true,
							pointStyle: 'circle',
							font: {
								size: 13,
								family: "'Inter', 'Segoe UI', sans-serif",
								weight: '500'
							},
							generateLabels: function(chart) {
								const data = chart.data.datasets[0].data;
								const labels = [];

								// Check if there are values >= threshold (blue bars)
								const hasAboveThreshold = data.some(value => value >= passingThreshold);
								if (hasAboveThreshold) {
									labels.push({
										text: `Capaian ≥ ${passingThreshold}%`,
										fillStyle: 'rgba(13, 110, 253, 0.9)',
										strokeStyle: 'rgba(13, 110, 253, 1)',
										lineWidth: 0,
										hidden: false,
										index: 0
									});
								}

								// Check if there are values < threshold (red bars)
								const hasBelowThreshold = data.some(value => value < passingThreshold);
								if (hasBelowThreshold) {
									labels.push({
										text: `Capaian < ${passingThreshold}%`,
										fillStyle: 'rgba(220, 53, 69, 0.9)',
										strokeStyle: 'rgba(220, 53, 69, 1)',
										lineWidth: 0,
										hidden: false,
										index: 1
									});
								}

								return labels;
							}
						},
						// Add spacing below the legend to prevent overlap with data labels
						padding: {
							bottom: 30
						}
					},
					tooltip: {
						backgroundColor: 'rgba(30, 39, 46, 0.95)',
						padding: 16,
						cornerRadius: 8,
						titleFont: {
							size: 14,
							weight: '600',
							family: "'Inter', 'Segoe UI', sans-serif"
						},
						bodyFont: {
							size: 13,
							family: "'Inter', 'Segoe UI', sans-serif"
						},
						borderColor: 'rgba(255, 255, 255, 0.1)',
						borderWidth: 1,
						displayColors: true,
						callbacks: {
							title: function(context) {
								return context[0].label;
							},
							label: function(context) {
								const value = context.parsed.y;
								const status = value >= passingThreshold ? 'Memenuhi' : 'Belum Memenuhi';
								return [
									`Capaian: ${value.toFixed(2)}%`,
									`Status: ${status}`
								];
							}
						}
					},
					datalabels: {
						anchor: 'end',
						align: 'top',
						offset: 4,
						formatter: function(value) {
							return value.toFixed(1) + '%';
						},
						font: {
							weight: '600',
							size: 11,
							family: "'Inter', 'Segoe UI', sans-serif"
						},
						color: function(context) {
							return context.dataset.data[context.dataIndex] < passingThreshold ?
								'rgba(220, 53, 69, 1)' :
								'rgba(13, 110, 253, 1)';
						},
						backgroundColor: function(context) {
							return context.dataset.data[context.dataIndex] < passingThreshold ?
								'rgba(220, 53, 69, 0.1)' :
								'rgba(13, 110, 253, 0.1)';
						},
						borderRadius: 4,
						padding: {
							top: 4,
							bottom: 4,
							left: 8,
							right: 8
						}
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						max: 100,
						title: {
							display: true,
							text: 'Persentase Capaian (%)',
							font: {
								size: 13,
								weight: '600',
								family: "'Inter', 'Segoe UI', sans-serif"
							},
							color: '#2c3e50',
							padding: {
								bottom: 10
							}
						},
						ticks: {
							callback: function(value) {
								return value + '%';
							},
							font: {
								size: 11,
								family: "'Inter', 'Segoe UI', sans-serif"
							},
							color: '#5a6c7d',
							padding: 8
						},
						grid: {
							display: true,
							color: 'rgba(0, 0, 0, 0.04)',
							lineWidth: 1,
							drawBorder: false,
							drawTicks: false
						},
						border: {
							display: false
						}
					},
					x: {
						title: {
							display: true,
							text: 'Kode CPMK',
							font: {
								size: 13,
								weight: '600',
								family: "'Inter', 'Segoe UI', sans-serif"
							},
							color: '#2c3e50',
							padding: {
								top: 10
							}
						},
						ticks: {
							font: {
								size: 11,
								weight: '500',
								family: "'Inter', 'Segoe UI', sans-serif"
							},
							color: '#2c3e50',
							padding: 8
						},
						grid: {
							display: false,
							drawBorder: false
						},
						border: {
							display: false
						}
					}
				},
				layout: {
					padding: {
						top: 20,
						right: 20,
						bottom: 10,
						left: 10
					}
				}
			}
		});
	}

	function createChartHTML(type, color, canvasId, title = 'Grafik Capaian CPMK') {
		return `
        <div class="card shadow-sm border-0" style="border-radius: 12px; overflow: hidden;">
            <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center" style="padding: 1.5rem;">
                <div>
                    <h5 class="mb-1" style="color: #2c3e50; font-weight: 600;">
                        <i class="bi bi-bar-chart-fill" style="color: #0d6efd;"></i> ${title}
                    </h5>
                    <p class="text-muted mb-0 small">Visualisasi data capaian pembelajaran</p>
                </div>
                <button class="btn btn-outline-primary btn-sm" id="exportChart${type}Btn" style="border-radius: 8px; padding: 0.5rem 1rem;">
                    <i class="bi bi-download"></i> Export PNG
                </button>
            </div>
            <div class="card-body" style="padding: 2rem; background: linear-gradient(to bottom, #ffffff 0%, #f8f9fa 100%);">
                <canvas id="${canvasId}" height="80"></canvas>
            </div>
        </div>
    `;
	}

	function getStatusBadge(nilai) {
		// Use dynamic threshold: passing+10 for "good", passing for "fair"
		const goodThreshold = passingThreshold + 10;
		if (nilai >= goodThreshold) return '<span class="badge bg-success">Baik</span>';
		if (nilai >= passingThreshold) return '<span class="badge bg-warning">Cukup</span>';
		return '<span class="badge bg-danger">Kurang</span>';
	}

	function getLoadingHTML(color = 'primary') {
		return `
        <div class="text-center py-5">
            <div class="spinner-border text-${color}" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-3">Memuat data...</p>
        </div>
    `;
	}

	function showError(emptyStateId, chartSectionId, message) {
		$(`#${chartSectionId}`).addClass('d-none');
		$('#detailCalculationIndividual').addClass('d-none');
		$('#detailCalculationComparative').addClass('d-none');
		$('#detailCalculationKeseluruhan').addClass('d-none');
		$(`#${emptyStateId}`).removeClass('d-none').html(`
        <i class="bi bi-exclamation-circle" style="font-size: 4rem; color: #dc3545;"></i>
        <p class="text-danger mt-3">${message}</p>
    `);
	}

	function bindExportButton(selector, chart, filename) {
		$(document).off('click', selector);
		$(document).on('click', selector, function() {
			if (chart) {
				exportChartWithBackground(chart, filename);
			}
		});
	}

	function exportChart(chart, filename) {
		if (chart) {
			exportChartWithBackground(chart, filename);
		}
	}

	function exportChartWithBackground(chart, filename) {
		try {
			if (!chart || !chart.canvas) {
				console.error('Chart or canvas not found');
				alert('Error: Chart tidak ditemukan. Silakan refresh halaman.');
				return;
			}

			// Get the original canvas
			const originalCanvas = chart.canvas;

			// Create a new canvas with higher resolution for better quality
			const exportCanvas = document.createElement('canvas');
			const exportCtx = exportCanvas.getContext('2d');

			// Set canvas size (multiply by 2 for better quality)
			const scale = 2;
			exportCanvas.width = originalCanvas.width * scale;
			exportCanvas.height = originalCanvas.height * scale;


			// Fill with white background FIRST (before scaling)
			exportCtx.fillStyle = '#ffffff';
			exportCtx.fillRect(0, 0, exportCanvas.width, exportCanvas.height);
			// Scale the context to match
			exportCtx.scale(scale, scale);

			// Fill with white background (matching the card background)
			// Draw the chart on top of the white background
			exportCtx.drawImage(originalCanvas, 0, 0, originalCanvas.width, originalCanvas.height);

			// Try using toBlob first (better quality)
			if (exportCanvas.toBlob) {
				exportCanvas.toBlob(function(blob) {
					if (blob) {
						const url = URL.createObjectURL(blob);
						const link = document.createElement('a');
						link.download = filename;
						link.href = url;
						document.body.appendChild(link);
						link.click();
						document.body.removeChild(link);

						// Clean up
						setTimeout(() => URL.revokeObjectURL(url), 100);
					} else {
						// Fallback to dataURL if blob fails
						downloadUsingDataURL(exportCanvas, filename);
					}
				}, 'image/png', 1.0);
			} else {
				// Fallback for browsers that don't support toBlob
				downloadUsingDataURL(exportCanvas, filename);
			}
		} catch (error) {
			console.error('Error exporting chart:', error);
			alert('Error saat export chart: ' + error.message);
		}
	}

	function downloadUsingDataURL(canvas, filename) {
		try {
			const dataURL = canvas.toDataURL('image/png', 1.0);
			const link = document.createElement('a');
			link.download = filename;
			link.href = dataURL;
			document.body.appendChild(link);
			link.click();
			document.body.removeChild(link);
		} catch (error) {
			console.error('Error in downloadUsingDataURL:', error);
			alert('Error saat download: ' + error.message);
		}
	}
</script>

<?= $this->endSection() ?>
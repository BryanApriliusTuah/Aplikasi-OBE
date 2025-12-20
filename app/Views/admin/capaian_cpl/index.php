<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<style>
	/* Filter Card Styling */
	.filter-card,
	.filter-card-comparative {
		border: 2px solid #e0e0e0;
		background: #ffffff;
	}

	.filter-card:hover,
	.filter-card-comparative:hover {
		transform: translateY(-5px);
		border-color: #0d6efd !important;
	}

	.filter-card.border-primary,
	.filter-card-comparative.border-primary {
		background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
	}

	.filter-card i,
	.filter-card-comparative i {
		transition: all 0.3s ease;
	}

	.filter-card:hover i,
	.filter-card-comparative:hover i {
		transform: scale(1.1);
	}

	.filter-card .card-title,
	.filter-card-comparative .card-title {
		transition: all 0.3s ease;
		font-size: 1.1rem;
	}

	.filter-card .card-text,
	.filter-card-comparative .card-text {
		line-height: 1.4;
		min-height: 40px;
	}

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
</style>

<div class="card">
	<div class="card-body">
		<h2 class="mb-4">Capaian CPL</h2>

		<!-- Tab Navigation -->
		<ul class="nav nav-tabs mb-4" id="cplTabs" role="tablist">
			<li class="nav-item" role="presentation">
				<button class="nav-link active" id="individual-tab" data-bs-toggle="tab" data-bs-target="#individual" type="button" role="tab">
					<i class="bi bi-person"></i> Mahasiswa
				</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="comparative-tab" data-bs-toggle="tab" data-bs-target="#comparative" type="button" role="tab">
					<i class="bi bi-people"></i> Angkatan
				</button>
			</li>
			<li class="nav-item" role="presentation">
				<button class="nav-link" id="keseluruhan-tab" data-bs-toggle="tab" data-bs-target="#keseluruhan" type="button" role="tab">
					<i class="bi bi-bar-chart-line"></i> Keseluruhan
				</button>
			</li>
		</ul>

		<!-- Tab Content -->
		<div class="tab-content" id="cplTabContent">
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
								<h6 class="card-title mb-2 text-primary fw-bold">CPL Mahasiswa</h6>
								<p class="card-text text-muted small mb-0">Perhitungan CPL mahasiswa dilakukan dengan menjumlahkan seluruh CPMK pada berbagai mata kuliah yang telah ditempuh.</p>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card filter-card h-100 shadow-sm" id="filter2-card" role="button" data-bs-toggle="tab" data-bs-target="#filter2" style="cursor: pointer; transition: all 0.3s;">
							<div class="card-body text-center p-3">
								<div class="mb-2">
									<i class="bi bi-calendar-range text-secondary" style="font-size: 1.8rem;"></i>
								</div>
								<h6 class="card-title mb-2">CPL per Semester</h6>
								<p class="card-text text-muted small mb-0">Perhitungan CPL mahasiswa per semester dilakukan dengan menjumlahkan CPMK dari berbagai mata kuliah dalam satu semester.</p>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card filter-card h-100 shadow-sm" id="filter3-card" role="button" data-bs-toggle="tab" data-bs-target="#filter3" style="cursor: pointer; transition: all 0.3s;">
							<div class="card-body text-center p-3">
								<div class="mb-2">
									<i class="bi bi-calendar2-event text-secondary" style="font-size: 1.8rem;"></i>
								</div>
								<h6 class="card-title mb-2">CPL per Tahun Akademik</h6>
								<p class="card-text text-muted small mb-0">Perhitungan CPL mahasiswa per tahun akademik dilakukan dengan menjumlahkan CPMK dari berbagai mata kuliah dalam 1 tahun akademik.</p>
							</div>
						</div>
					</div>
				</div>

				<div class="tab-content" id="filterSubTabContent">
					<!-- Filter 1: CPL Mahasiswa -->
					<div class="tab-pane fade show active" id="filter1" role="tabpanel">
						<div class="modern-filter-wrapper mb-4">
							<div class="modern-filter-header">
								<div class="d-flex align-items-center gap-2">
									<i class="bi bi-funnel-fill text-primary"></i>
									<span class="modern-filter-title">Filter CPL Mahasiswa</span>
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
												<?php foreach ($programStudi as $prodi): ?>
													<option value="<?= esc($prodi) ?>" <?= $prodi === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
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

					<!-- Filter 2: CPL Mahasiswa per Semester -->
					<div class="tab-pane fade" id="filter2" role="tabpanel">
						<div class="modern-filter-wrapper mb-4">
							<div class="modern-filter-header">
								<div class="d-flex align-items-center gap-2">
									<i class="bi bi-funnel-fill text-primary"></i>
									<span class="modern-filter-title">Filter CPL Mahasiswa per Semester</span>
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
												<?php foreach ($programStudi as $prodi): ?>
													<option value="<?= esc($prodi) ?>" <?= $prodi === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
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

					<!-- Filter 3: CPL Mahasiswa per Tahun Akademik -->
					<div class="tab-pane fade" id="filter3" role="tabpanel">
						<div class="modern-filter-wrapper mb-4">
							<div class="modern-filter-header">
								<div class="d-flex align-items-center gap-2">
									<i class="bi bi-funnel-fill text-primary"></i>
									<span class="modern-filter-title">Filter CPL Mahasiswa per Tahun Akademik</span>
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
												<?php foreach ($programStudi as $prodi): ?>
													<option value="<?= esc($prodi) ?>" <?= $prodi === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
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
					<p class="text-muted mt-3">Pilih mahasiswa dan klik tombol search untuk melihat grafik capaian CPL</p>
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
								<h6 class="card-title mb-2 text-primary fw-bold">CPL Angkatan</h6>
								<p class="card-text text-muted small mb-0">Perhitungan rata-rata CPL angkatan dilakukan dengan menjumlahkan seluruh capaian CPL mahasiswa dalam satu angkatan.</p>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card filter-card-comparative h-100 shadow-sm" id="filterComparative2-card" role="button" data-bs-toggle="tab" data-bs-target="#filterComparative2" style="cursor: pointer; transition: all 0.3s;">
							<div class="card-body text-center p-3">
								<div class="mb-2">
									<i class="bi bi-calendar-range text-secondary" style="font-size: 1.8rem;"></i>
								</div>
								<h6 class="card-title mb-2">CPL Angkatan per Semester</h6>
								<p class="card-text text-muted small mb-0">Perhitungan rata-rata CPL angkatan per semester dilakukan dengan menjumlahkan seluruh nilai CPL mahasiswa pada suatu semester, lalu dibagi dengan total mahasiswa pada angkatan tersebut.</p>
							</div>
						</div>
					</div>
					<div class="col-md-4">
						<div class="card filter-card-comparative h-100 shadow-sm" id="filterComparative3-card" role="button" data-bs-toggle="tab" data-bs-target="#filterComparative3" style="cursor: pointer; transition: all 0.3s;">
							<div class="card-body text-center p-3">
								<div class="mb-2">
									<i class="bi bi-calendar2-event text-secondary" style="font-size: 1.8rem;"></i>
								</div>
								<h6 class="card-title mb-2">CPL Angkatan per Tahun Akademik</h6>
								<p class="card-text text-muted small mb-0">Perhitungan rata-rata CPL angkatan per tahun akademik dilakukan dengan menjumlahkan seluruh nilai CPL mahasiswa pada tahun akademik tertentu, lalu dibagi dengan total mahasiswa pada angkatan tersebut</p>
							</div>
						</div>
					</div>
				</div>

				<div class="tab-content" id="filterComparativeSubTabContent">
					<!-- Filter 1: CPL Angkatan -->
					<div class="tab-pane fade show active" id="filterComparative1" role="tabpanel">
						<div class="modern-filter-wrapper mb-4">
							<div class="modern-filter-header">
								<div class="d-flex align-items-center gap-2">
									<i class="bi bi-funnel-fill text-primary"></i>
									<span class="modern-filter-title">Filter CPL Angkatan</span>
								</div>
							</div>
							<div class="modern-filter-body">
								<form id="filterComparativeForm1">
									<div class="row g-3 align-items-end">
										<div class="col-md-5">
											<label for="programStudiComparativeSelect1" class="modern-filter-label">
												<i class="bi bi-mortarboard-fill me-1"></i> Program Studi
											</label>
											<select class="form-select modern-filter-input" id="programStudiComparativeSelect1" name="program_studi" required>
												<option value="">-- Pilih Program Studi --</option>
												<?php foreach ($programStudi as $prodi): ?>
													<option value="<?= esc($prodi) ?>" <?= $prodi === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-5">
											<label for="tahunAngkatanComparativeSelect1" class="modern-filter-label">
												<i class="bi bi-calendar3 me-1"></i> Tahun Angkatan
											</label>
											<select class="form-select modern-filter-input" id="tahunAngkatanComparativeSelect1" name="tahun_angkatan" required>
												<option value="">-- Pilih Tahun Angkatan --</option>
												<?php foreach ($tahunAngkatan as $tahun): ?>
													<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
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

					<!-- Filter 2: CPL Angkatan per Semester -->
					<div class="tab-pane fade" id="filterComparative2" role="tabpanel">
						<div class="modern-filter-wrapper mb-4">
							<div class="modern-filter-header">
								<div class="d-flex align-items-center gap-2">
									<i class="bi bi-funnel-fill text-primary"></i>
									<span class="modern-filter-title">Filter CPL Angkatan per Semester</span>
								</div>
							</div>
							<div class="modern-filter-body">
								<form id="filterComparativeForm2">
									<div class="row g-3 align-items-end">
										<div class="col-md-4">
											<label for="programStudiComparativeSelect2" class="modern-filter-label">
												<i class="bi bi-mortarboard-fill me-1"></i> Program Studi
											</label>
											<select class="form-select modern-filter-input" id="programStudiComparativeSelect2" name="program_studi" required>
												<option value="">-- Pilih Program Studi --</option>
												<?php foreach ($programStudi as $prodi): ?>
													<option value="<?= esc($prodi) ?>" <?= $prodi === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-3">
											<label for="tahunAngkatanComparativeSelect2" class="modern-filter-label">
												<i class="bi bi-calendar3 me-1"></i> Tahun Angkatan
											</label>
											<select class="form-select modern-filter-input" id="tahunAngkatanComparativeSelect2" name="tahun_angkatan" required>
												<option value="">-- Pilih Tahun Angkatan --</option>
												<?php foreach ($tahunAngkatan as $tahun): ?>
													<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-3">
											<label for="semesterComparativeSelect2" class="modern-filter-label">
												<i class="bi bi-grid-3x3-gap me-1"></i> Semester
											</label>
											<select class="form-select modern-filter-input" id="semesterComparativeSelect2" name="semester" required>
												<option value="">-- Pilih Semester --</option>
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

					<!-- Filter 3: CPL Angkatan per Tahun Akademik -->
					<div class="tab-pane fade" id="filterComparative3" role="tabpanel">
						<div class="modern-filter-wrapper mb-4">
							<div class="modern-filter-header">
								<div class="d-flex align-items-center gap-2">
									<i class="bi bi-funnel-fill text-primary"></i>
									<span class="modern-filter-title">Filter CPL Angkatan per Tahun Akademik</span>
								</div>
							</div>
							<div class="modern-filter-body">
								<form id="filterComparativeForm3">
									<div class="row g-3 align-items-end">
										<div class="col-md-4">
											<label for="programStudiComparativeSelect3" class="modern-filter-label">
												<i class="bi bi-mortarboard-fill me-1"></i> Program Studi
											</label>
											<select class="form-select modern-filter-input" id="programStudiComparativeSelect3" name="program_studi" required>
												<option value="">-- Pilih Program Studi --</option>
												<?php foreach ($programStudi as $prodi): ?>
													<option value="<?= esc($prodi) ?>" <?= $prodi === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-3">
											<label for="tahunAngkatanComparativeSelect3" class="modern-filter-label">
												<i class="bi bi-calendar3 me-1"></i> Tahun Angkatan
											</label>
											<select class="form-select modern-filter-input" id="tahunAngkatanComparativeSelect3" name="tahun_angkatan" required>
												<option value="">-- Pilih Tahun Angkatan --</option>
												<?php foreach ($tahunAngkatan as $tahun): ?>
													<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-3">
											<label for="tahunAkademikComparativeSelect3" class="modern-filter-label">
												<i class="bi bi-calendar-event me-1"></i> Tahun Akademik
											</label>
											<select class="form-select modern-filter-input" id="tahunAkademikComparativeSelect3" name="tahun_akademik_filter" required>
												<option value="">-- Pilih Tahun Akademik --</option>
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
					<p class="text-muted mt-3">Pilih program studi dan tahun angkatan untuk melihat grafik rata-rata capaian CPL</p>
				</div>
			</div>

			<!-- Keseluruhan Tab -->
			<div class="tab-pane fade" id="keseluruhan" role="tabpanel">
				<!-- Filter Section Keseluruhan -->
				<div class="modern-filter-wrapper mb-4">
					<div class="modern-filter-header">
						<div class="d-flex align-items-center gap-2">
							<i class="bi bi-funnel-fill text-primary"></i>
							<span class="modern-filter-title">Filter CPL Keseluruhan</span>
						</div>
					</div>
					<div class="modern-filter-body">
						<form id="filterKeseluruhanForm">
							<div class="row g-3 align-items-end">
								<div class="col-md-10">
									<label for="programStudiKeseluruhanSelect" class="modern-filter-label">
										<i class="bi bi-mortarboard-fill me-1"></i> Program Studi
									</label>
									<select class="form-select modern-filter-input" id="programStudiKeseluruhanSelect" name="program_studi" required>
										<option value="">-- Pilih Program Studi --</option>
										<?php foreach ($programStudi as $prodi): ?>
											<option value="<?= esc($prodi) ?>" <?= $prodi === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
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
					<p class="text-muted mt-3">Pilih program studi untuk melihat grafik rata-rata capaian CPL dari semua angkatan</p>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Modal Detail CPL -->
<div class="modal fade" id="detailCplModal" tabindex="-1">
	<div class="modal-dialog modal-xl">
		<div class="modal-content">
			<div class="modal-body">
				<div id="detailCplModalContent">
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

	let cplChartIndividual = null;
	let cplChartComparative = null;
	let cplChartKeseluruhan = null;
	let cplChartAllSubjects = null;
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
		// Initialize Select2 for Filter 1 (CPL Mahasiswa)
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

		// Initialize Select2 for Filter 2 (CPL per Semester)
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

		// Initialize Select2 for Filter 3 (CPL per Tahun Akademik)
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

		// All Subjects Tab Events
		$('#filterAllSubjectsForm').on('submit', function(e) {
			e.preventDefault();
			loadAllSubjectsChartData();
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
			url: '<?= base_url("admin/capaian-cpl/mahasiswa") ?>',
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
		$('#calculationExplanationIndividual').addClass('d-none');
		$('#detailCalculationIndividual').addClass('d-none');
		$('#chartSectionIndividual').removeClass('d-none').html(getLoadingHTML());

		$.ajax({
			url: '<?= base_url("admin/capaian-cpl/chart-data") ?>',
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
			url: '<?= base_url("admin/capaian-cpl/comparative-data") ?>',
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
			url: '<?= base_url("admin/capaian-cpl/keseluruhan-data") ?>',
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

	function loadAllSubjectsChartData() {
		const programStudi = $('#programStudiAllSelect').val();

		if (!programStudi) {
			alert('Silakan pilih program studi');
			return;
		}

		$('#emptyStateAllSubjects').addClass('d-none');
		$('#chartSectionAllSubjects').removeClass('d-none').html(getLoadingHTML('secondary'));

		$.ajax({
			url: '<?= base_url("admin/capaian-cpl/all-subjects-data") ?>',
			method: 'GET',
			data: {
				program_studi: programStudi
			},
			success: function(response) {
				if (response.success) {
					displayAllSubjectsChart(response);
					displayAllSubjectsInfo(response);
				} else {
					showError('emptyStateAllSubjects', 'chartSectionAllSubjects', response.message);
				}
			},
			error: function() {
				showError('emptyStateAllSubjects', 'chartSectionAllSubjects', 'Terjadi kesalahan saat memuat data');
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

	function displayAllSubjectsInfo(response) {
		const info = `
        <div class="row">
            <div class="col-md-6">
                <strong>Program Studi:</strong> ${response.programStudi}
            </div>
            <div class="col-md-6">
                <strong>Total Mata Kuliah (Semua Tahun):</strong> ${response.totalMataKuliah} mata kuliah aktif
            </div>
        </div>
    `;
		$('#infoContentAllSubjects').html(info);
		$('#infoSectionAllSubjects').removeClass('d-none');
	}

	function displayIndividualChart(response) {
		// Destroy existing chart if any
		if (cplChartIndividual) {
			cplChartIndividual.destroy();
		}

		// Create and render modern chart
		cplChartIndividual = createModernChart(
			'chartSectionIndividual',
			response.chartData,
			'Capaian CPL Mahasiswa',
			'capaian-cpl-individual.png'
		);
		cplChartIndividual.render();

		// Show chart section
		$('#chartSectionIndividual').removeClass('d-none');

		// Show calculation explanation
		$('#calculationExplanationIndividual').removeClass('d-none');

		// Display detailed calculation breakdown
		displayDetailedCalculation(response);
	}

	function displayDetailedCalculation(response) {
		const mahasiswaId = $('#mahasiswaSelect').val();

		if (!response.chartData || !response.chartData.details) {
			$('#detailCalculationIndividual').addClass('d-none');
			return;
		}

		let html = '<div class="modern-table-wrapper"><table id="individualDetailTable" class="modern-table">';
		html += '<thead>';
		html += '<tr>';
		html += '<th width="5%" class="text-center">No</th>';
		html += '<th width="12%">Kode CPL</th>';
		html += '<th width="35%">Deskripsi CPL</th>';
		html += '<th width="12%" class="text-center">Jenis CPL</th>';
		html += '<th width="10%" class="text-center">Jumlah CPMK</th>';
		html += '<th width="10%" class="text-center">Jumlah MK</th>';
		html += '<th width="10%" class="text-center">Capaian (%)</th>';
		html += '<th width="6%" class="text-center">Aksi</th>';
		html += '</tr>';
		html += '</thead>';
		html += '<tbody>';

		response.chartData.details.forEach((cpl, index) => {
			html += `
				<tr>
					<td class="text-center">${index + 1}</td>
					<td><strong>${cpl.kode_cpl}</strong></td>
					<td>${cpl.deskripsi}</td>
					<td class="text-center"><span class="badge bg-primary">${cpl.jenis_cpl}</span></td>
					<td class="text-center">${cpl.jumlah_cpmk}</td>
					<td class="text-center">${cpl.jumlah_mk}</td>
					<td class="text-center"><strong>${cpl.rata_rata.toFixed(2)}%</strong></td>
					<td class="text-center">
						<button class="btn btn-sm btn-primary" onclick="loadCplCalculationDetail(${cpl.cpl_id}, '${cpl.kode_cpl}')">
							<i class="bi bi-eye"></i>
						</button>
					</td>
				</tr>
			`;
		});

		html += '</tbody></table></div>';

		$('#detailCalculationContent').html(html);
		$('#detailCalculationIndividual').removeClass('d-none');

		// Initialize custom pagination
		setTimeout(() => initPagination('individualDetailTable', 10), 100);
	}

	function loadCplCalculationDetail(cplId, kodeCpl) {
		// Use currentActiveFilter to get the right mahasiswa ID and filter values
		const mahasiswaId = $(`#mahasiswaSelect${currentActiveFilter}`).val();

		// Show loading state in modal
		$('#detailCplModalContent').html(`
			<div class="text-center py-4">
				<div class="spinner-border text-primary" role="status">
					<span class="visually-hidden">Loading...</span>
				</div>
				<p class="mt-3 text-muted">Memuat detail perhitungan...</p>
			</div>
		`);

		// Show modal
		const modal = new bootstrap.Modal(document.getElementById('detailCplModal'));
		modal.show();

		// Build ajax data based on active filter
		let ajaxData = {
			mahasiswa_id: mahasiswaId,
			cpl_id: cplId
		};

		// Add filter-specific data
		if (currentActiveFilter === 2) {
			ajaxData.semester = $(`#semesterSelect${currentActiveFilter}`).val();
		} else if (currentActiveFilter === 3) {
			ajaxData.tahun_akademik = $(`#tahunAkademikSelect${currentActiveFilter}`).val();
		}

		$.ajax({
			url: '<?= base_url("admin/capaian-cpl/detail-calculation") ?>',
			method: 'GET',
			data: ajaxData,
			success: function(response) {
				if (response.success) {
					displayCplCalculationDetail(kodeCpl, response.data, response.summary);
				} else {
					$('#detailCplModalContent').html('<div class="alert alert-warning mb-0">' + (response.message || 'Tidak ada data') + '</div>');
				}
			},
			error: function() {
				$('#detailCplModalContent').html('<div class="alert alert-danger mb-0">Terjadi kesalahan saat memuat data</div>');
			}
		});
	}

	function displayCplCalculationDetail(kodeCpl, data, summary) {
		let html = `
			<div class="modern-table-wrapper">
				<table class="modern-table mb-0">
					<thead>
						<tr>
							<th width="5%" class="text-center">No</th>
							<th width="15%">Kode CPMK</th>
							<th width="25%">Mata Kuliah</th>
							<th width="12%" class="text-center">Tahun Akademik</th>
							<th width="10%" class="text-center">Kelas</th>
							<th width="10%" class="text-center">Nilai CPMK</th>
							<th width="10%" class="text-center">Bobot (%)</th>
						</tr>
					</thead>
					<tbody>
		`;

		if (data.length === 0) {
			html += `
				<tr>
					<td colspan="7" class="text-center text-muted">Belum ada data nilai untuk CPL ini</td>
				</tr>
			`;
		} else {
			data.forEach((item, index) => {
				html += `
					<tr>
						<td class="text-center">${index + 1}</td>
						<td><strong>${item.kode_cpmk}</strong></td>
						<td><small>${item.kode_mk} - ${item.nama_mk}</small></td>
						<td class="text-center">${item.tahun_akademik}</td>
						<td class="text-center">${item.kelas}</td>
						<td class="text-center">${parseFloat(item.nilai_cpmk).toFixed(2)}</td>
						<td class="text-center">${parseFloat(item.bobot).toFixed(0)}%</td>
					</tr>
				`;
			});
		}

		// Summary row
		html += `
					</tbody>
					<tfoot>
						<tr>
							<td colspan="5" class="text-end"><strong>TOTAL:</strong></td>
							<td class="text-center"><strong>${summary.nilai_cpl.toFixed(2)}</strong></td>
							<td class="text-center"><strong>${summary.total_bobot.toFixed(0)}%</strong></td>
						</tr>
						<tr style="background-color: #d1e7dd;">
							<td colspan="6" class="text-end"><strong>Capaian CPL (%) = (${summary.nilai_cpl.toFixed(2)} / ${summary.total_bobot.toFixed(0)}) × 100</strong></td>
							<td class="text-center"><h6 class="mb-0"><strong>${summary.capaian_cpl.toFixed(2)}%</strong></h6></td>
						</tr>
					</tfoot>
				</table>
			</div>
		`;

		$('#detailCplModalContent').html(html);
	}

	function displayComparativeChart(response) {
		// Destroy existing chart if any
		if (cplChartComparative) {
			cplChartComparative.destroy();
		}

		// Create and render modern chart
		cplChartComparative = createModernChart(
			'chartSectionComparative',
			response.chartData,
			'Rata-rata Capaian CPL (Angkatan)',
			'capaian-cpl-comparative.png'
		);
		cplChartComparative.render();

		// Show chart section
		$('#chartSectionComparative').removeClass('d-none');

		// Display detailed calculation
		displayComparativeDetailedCalculation(response);
	}

	function displayKeseluruhanChart(response) {
		// Destroy existing chart if any
		if (cplChartKeseluruhan) {
			cplChartKeseluruhan.destroy();
		}

		// Create and render modern chart
		cplChartKeseluruhan = createModernChart(
			'chartSectionKeseluruhan',
			response.chartData,
			'Rata-rata Capaian CPL Keseluruhan',
			'capaian-cpl-keseluruhan.png'
		);
		cplChartKeseluruhan.render();

		// Show chart section
		$('#chartSectionKeseluruhan').removeClass('d-none');

		// Display detailed calculation
		displayKeseluruhanDetailedCalculation(response);
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
		html += '<th width="12%">Kode CPL</th>';
		html += '<th width="43%">Deskripsi CPL</th>';
		html += '<th width="12%" class="text-center">Jenis CPL</th>';
		html += '<th width="10%" class="text-center">Jumlah Mahasiswa</th>';
		html += '<th width="11%" class="text-center">Rata-rata (%)</th>';
		html += '<th width="7%" class="text-center">Aksi</th>';
		html += '</tr>';
		html += '</thead>';
		html += '<tbody>';

		response.chartData.details.forEach((cpl, index) => {
			html += `
				<tr>
					<td class="text-center">${index + 1}</td>
					<td><strong>${cpl.kode_cpl}</strong></td>
					<td>${cpl.deskripsi}</td>
					<td class="text-center"><span class="badge bg-primary">${cpl.jenis_cpl}</span></td>
					<td class="text-center">${cpl.jumlah_mahasiswa}</td>
					<td class="text-center"><strong>${cpl.rata_rata.toFixed(2)}%</strong></td>
					<td class="text-center">
						<button class="btn btn-sm btn-primary" onclick="loadKeseluruhanCplDetail(${cpl.cpl_id}, '${cpl.kode_cpl}', ${index})">
							<i class="bi bi-eye"></i>
						</button>
					</td>
				</tr>
			`;
		});

		html += '</tbody></table></div>';

		$('#detailCalculationKeseluruhanContent').html(html);
		$('#detailCalculationKeseluruhan').removeClass('d-none');

		// Initialize custom pagination
		setTimeout(() => initPagination('keseluruhanDetailTable', 10), 100);
	}

	function loadKeseluruhanCplDetail(cplId, kodeCpl, index) {
		const programStudi = $('#programStudiKeseluruhanSelect').val();

		// Show loading state in modal
		$('#detailCplModalContent').html(`
			<div class="text-center py-4">
				<div class="spinner-border text-primary" role="status">
					<span class="visually-hidden">Loading...</span>
				</div>
				<p class="mt-3 text-muted">Memuat detail perhitungan...</p>
			</div>
		`);

		// Show modal
		const modal = new bootstrap.Modal(document.getElementById('detailCplModal'));
		modal.show();

		$.ajax({
			url: '<?= base_url("admin/capaian-cpl/keseluruhan-detail-calculation") ?>',
			method: 'GET',
			data: {
				cpl_id: cplId,
				program_studi: programStudi
			},
			success: function(response) {
				if (response.success) {
					displayKeseluruhanCplCalculationDetail(index, kodeCpl, response.data, response.summary);
				} else {
					$('#detailCplModalContent').html('<div class="alert alert-warning mb-0">' + (response.message || 'Tidak ada data') + '</div>');
				}
			},
			error: function() {
				$('#detailCplModalContent').html('<div class="alert alert-danger mb-0">Terjadi kesalahan saat memuat data</div>');
			}
		});
	}

	function displayKeseluruhanCplCalculationDetail(index, kodeCpl, data, summary) {
		let html = ``;

		if (data.length === 0) {
			html += '<div class="alert alert-info mb-0">Belum ada mahasiswa yang memiliki nilai untuk CPL ini</div>';
		} else {
			html += `
				<div class="modern-table-wrapper">
					<table id="keseluruhanCplDetailTable_${index}" class="modern-table mb-0">
						<thead>
							<tr>
								<th width="8%" class="text-center">No</th>
								<th width="15%">NIM</th>
								<th width="40%">Nama Mahasiswa</th>
								<th width="12%" class="text-center">Angkatan</th>
								<th width="25%" class="text-center">Capaian CPL (%)</th>
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
						<td class="text-center"><strong>${mhs.capaian_cpl.toFixed(2)}%</strong></td>
					</tr>
				`;
			});

			html += `
						</tbody>
						<tfoot>
							<tr>
								<td colspan="4" class="text-end"><strong>Total dari ${summary.jumlah_mahasiswa} mahasiswa (semua angkatan):</strong></td>
								<td class="text-center"><strong>${summary.total_cpl.toFixed(2)}%</strong></td>
							</tr>
							<tr style="background-color: #d1e7dd;">
								<td colspan="4" class="text-end"><strong>Rata-rata CPL = ${summary.total_cpl.toFixed(2)}% / ${summary.jumlah_mahasiswa} mahasiswa</strong></td>
								<td class="text-center"><h6 class="mb-0"><strong>${summary.rata_rata.toFixed(2)}%</strong></h6></td>
							</tr>
						</tfoot>
					</table>
				</div>
			`;
		}

		$('#detailCplModalContent').html(html);

		// Initialize custom pagination
		if (data.length > 0) {
			setTimeout(() => initPagination(`keseluruhanCplDetailTable_${index}`, 10), 100);
		}
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
		html += '<th width="12%">Kode CPL</th>';
		html += '<th width="38%">Deskripsi CPL</th>';
		html += '<th width="12%" class="text-center">Jenis CPL</th>';
		html += '<th width="10%" class="text-center">Jumlah Mahasiswa</th>';
		html += '<th width="13%" class="text-center">Rata-rata (%)</th>';
		html += '<th width="10%" class="text-center">Aksi</th>';
		html += '</tr>';
		html += '</thead>';
		html += '<tbody>';

		response.chartData.details.forEach((cpl, index) => {
			html += `
				<tr>
					<td class="text-center">${index + 1}</td>
					<td><strong>${cpl.kode_cpl}</strong></td>
					<td>${cpl.deskripsi}</td>
					<td class="text-center"><span class="badge bg-primary">${cpl.jenis_cpl}</span></td>
					<td class="text-center">${cpl.jumlah_mahasiswa}</td>
					<td class="text-center"><strong>${cpl.rata_rata.toFixed(2)}%</strong></td>
					<td class="text-center">
						<button class="btn btn-sm btn-primary" onclick="loadComparativeCplDetail(${cpl.cpl_id}, '${cpl.kode_cpl}', ${index})">
							<i class="bi bi-eye"></i>
						</button>
					</td>
				</tr>
			`;
		});

		html += '</tbody></table></div>';

		$('#detailCalculationComparativeContent').html(html);
		$('#detailCalculationComparative').removeClass('d-none');

		// Initialize custom pagination
		setTimeout(() => initPagination('comparativeDetailTable', 10), 100);
	}

	function loadComparativeCplDetail(cplId, kodeCpl, index) {
		// Use currentActiveComparativeFilter to get the right filter values
		const programStudi = $(`#programStudiComparativeSelect${currentActiveComparativeFilter}`).val();
		const tahunAngkatan = $(`#tahunAngkatanComparativeSelect${currentActiveComparativeFilter}`).val();

		// Show loading state in modal
		$('#detailCplModalContent').html(`
			<div class="text-center py-4">
				<div class="spinner-border text-primary" role="status">
					<span class="visually-hidden">Loading...</span>
				</div>
				<p class="mt-3 text-muted">Memuat detail perhitungan...</p>
			</div>
		`);

		// Show modal
		const modal = new bootstrap.Modal(document.getElementById('detailCplModal'));
		modal.show();

		// Build ajax data based on active filter
		let ajaxData = {
			cpl_id: cplId,
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
			url: '<?= base_url("admin/capaian-cpl/comparative-detail-calculation") ?>',
			method: 'GET',
			data: ajaxData,
			success: function(response) {
				if (response.success) {
					displayComparativeCplCalculationDetail(index, kodeCpl, response.data, response.summary);
				} else {
					$('#detailCplModalContent').html('<div class="alert alert-warning mb-0">' + (response.message || 'Tidak ada data') + '</div>');
				}
			},
			error: function() {
				$('#detailCplModalContent').html('<div class="alert alert-danger mb-0">Terjadi kesalahan saat memuat data</div>');
			}
		});
	}

	function displayComparativeCplCalculationDetail(index, kodeCpl, data, summary) {
		let html = ``;

		if (data.length === 0) {
			html += '<div class="alert alert-info mb-0">Belum ada mahasiswa yang memiliki nilai untuk CPL ini</div>';
		} else {
			html += `
				<div class="modern-table-wrapper">
					<table id="comparativeCplDetailTable_${index}" class="modern-table mb-0">
						<thead>
							<tr>
								<th width="10%" class="text-center">No</th>
								<th width="20%">NIM</th>
								<th width="50%">Nama Mahasiswa</th>
								<th width="20%" class="text-center">Capaian CPL (%)</th>
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
						<td class="text-center"><strong>${mhs.capaian_cpl.toFixed(2)}%</strong></td>
					</tr>
				`;
			});

			html += `
						</tbody>
						<tfoot>
							<tr>
								<td colspan="3" class="text-end"><strong>Total dari ${summary.jumlah_mahasiswa} mahasiswa:</strong></td>
								<td class="text-center"><strong>${summary.total_cpl.toFixed(2)}%</strong></td>
							</tr>
							<tr style="background-color: #d1e7dd;">
								<td colspan="3" class="text-end"><strong>Rata-rata CPL = ${summary.total_cpl.toFixed(2)}% / ${summary.jumlah_mahasiswa} mahasiswa</strong></td>
								<td class="text-center"><h6 class="mb-0"><strong>${summary.rata_rata.toFixed(2)}%</strong></h6></td>
							</tr>
						</tfoot>
					</table>
				</div>
			`;
		}

		$('#detailCplModalContent').html(html);

		// Initialize custom pagination
		if (data.length > 0) {
			setTimeout(() => initPagination(`comparativeCplDetailTable_${index}`, 10), 100);
		}
	}


	function displayAllSubjectsChart(response) {
		const chartHTML = `
        <div class="card">
            <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> Capaian CPL di Semua Mata Kuliah (Semua Tahun)</h5>
                <button class="btn btn-light btn-sm" id="exportChartAllSubjectsBtn">
                    <i class="bi bi-download"></i> Export PNG
                </button>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> <strong>Info:</strong> Grafik menampilkan ${response.chartData.datasets.length} mata kuliah dari semua tahun akademik.
                </div>
                <canvas id="cplChartAllSubjects" height="120"></canvas>
            </div>
        </div>
    `;
		$('#chartSectionAllSubjects').html(chartHTML);

		bindExportButton('#exportChartAllSubjectsBtn', cplChartAllSubjects, 'capaian-cpl-all-subjects.png');

		const ctx = document.getElementById('cplChartAllSubjects').getContext('2d');
		if (cplChartAllSubjects) cplChartAllSubjects.destroy();

		cplChartAllSubjects = new Chart(ctx, {
			type: 'bar',
			data: {
				labels: response.chartData.labels,
				datasets: response.chartData.datasets
			},
			plugins: [ChartDataLabels],
			options: {
				responsive: true,
				maintainAspectRatio: true,
				plugins: {
					legend: {
						display: true,
						position: 'top',
						labels: {
							boxWidth: 12,
							font: {
								size: 10
							},
							padding: 8
						}
					},
					title: {
						display: true,
						text: 'Capaian CPL di Semua Mata Kuliah (Semua Tahun Akademik)',
						font: {
							size: 16,
							weight: 'bold'
						}
					},
					tooltip: {
						backgroundColor: 'rgba(0, 0, 0, 0.8)',
						padding: 12,
						callbacks: {
							label: function(context) {
								return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + '%';
							}
						}
					},
					datalabels: {
						anchor: 'end',
						align: 'top',
						formatter: function(value) {
							return value.toFixed(2) + '%';
						},
						font: {
							weight: 'bold',
							size: 10
						},
						color: '#333'
					}
				},
				scales: {
					y: {
						beginAtZero: true,
						max: 100,
						title: {
							display: true,
							text: 'Capaian CPL (%)',
							font: {
								size: 14,
								weight: 'bold'
							}
						},
						ticks: {
							callback: function(value) {
								return value + '%';
							}
						},
						grid: {
							display: true,
							color: 'rgba(0, 0, 0, 0.05)'
						}
					},
					x: {
						title: {
							display: true,
							text: 'Kode CPL',
							font: {
								size: 14,
								weight: 'bold'
							}
						},
						grid: {
							display: false
						}
					}
				}
			}
		});
	}

	function displayAllSubjectsSummaryTable(summaryData) {
		let html = `
        <div class="card mt-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-table"></i> Ringkasan Capaian per Mata Kuliah</h5>
            </div>
            <div class="card-body">
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Kode MK</th>
                                <th width="28%">Nama Mata Kuliah</th>
                                <th width="8%" class="text-center">Kelas</th>
                                <th width="8%" class="text-center">Semester</th>
                                <th width="12%" class="text-center">Tahun Akademik</th>
                                <th width="10%" class="text-center">Jumlah Mahasiswa</th>
                                <th width="10%" class="text-center">Rata-rata CPL (%)</th>
                                <th width="9%" class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
    `;

		summaryData.forEach((item, index) => {
			const statusBadge = getStatusBadge(item.rata_rata_keseluruhan);
			html += `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${item.kode_mk}</strong></td>
                <td>${item.nama_mk}</td>
                <td class="text-center">${item.kelas}</td>
                <td class="text-center">${item.semester}</td>
                <td class="text-center"><span class="badge bg-primary">${item.tahun_akademik}</span></td>
                <td class="text-center">${item.jumlah_mahasiswa}</td>
                <td class="text-center"><strong>${item.rata_rata_keseluruhan.toFixed(2)}%</strong></td>
                <td class="text-center">${statusBadge}</td>
            </tr>
        `;
		});

		html += `
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    `;

		$('#chartSectionAllSubjects').append(html);
	}

	function displayIndividualDetailTable(details) {
		let html = '';
		details.forEach((item, index) => {
			const statusBadge = getStatusBadge(item.rata_rata);
			html += `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${item.kode_cpl}</strong></td>
                <td>${item.deskripsi}</td>
                <td><span class="badge bg-info">${item.jenis_cpl}</span></td>
                <td class="text-center">${item.jumlah_cpmk}</td>
                <td class="text-center"><strong>${item.rata_rata.toFixed(2)}%</strong></td>
                <td class="text-center">${statusBadge}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-info" onclick="showCplDetail(${item.cpl_id}, '${item.kode_cpl}')">
                        <i class="bi bi-eye"></i>
                    </button>
                </td>
            </tr>
        `;
		});
		$('#detailTableBodyIndividual').html(html);
	}

	function displayComparativeDetailTable(details) {
		let html = '';
		details.forEach((item, index) => {
			const statusBadge = getStatusBadge(item.rata_rata);
			html += `
            <tr>
                <td>${index + 1}</td>
                <td><strong>${item.kode_cpl}</strong></td>
                <td>${item.deskripsi}</td>
                <td><span class="badge bg-info">${item.jenis_cpl}</span></td>
                <td class="text-center">${item.jumlah_mahasiswa}</td>
                <td class="text-center"><strong>${item.rata_rata.toFixed(2)}%</strong></td>
                <td class="text-center">${statusBadge}</td>
            </tr>
        `;
		});
		$('#detailTableBodyComparative').html(html);
	}

	function showCplDetail(cplId, kodeCpl) {
		const mahasiswaId = $('#mahasiswaSelect').val();

		$('#detailCplModal').modal('show');
		$('#detailCplModalContent').html(getLoadingHTML());

		$.ajax({
			url: '<?= base_url("admin/capaian-cpl/detail-data") ?>',
			method: 'GET',
			data: {
				mahasiswa_id: mahasiswaId,
				cpl_id: cplId
			},
			success: function(response) {
				if (response.success) {
					displayCplDetailModal(response.data, response.cpl);
				} else {
					$('#detailCplModalContent').html(`<div class="alert alert-warning">${response.message || 'Tidak ada data'}</div>`);
				}
			},
			error: function() {
				$('#detailCplModalContent').html(`<div class="alert alert-danger">Terjadi kesalahan saat memuat data</div>`);
			}
		});
	}

	function displayCplDetailModal(data, cpl) {
		let html = `
        <div class="mb-4">
            <h6><strong>CPL:</strong> ${cpl.kode_cpl}</h6>
            <p class="text-muted">${cpl.deskripsi}</p>
            <span class="badge bg-info">Jenis: ${getJenisCplLabel(cpl.jenis_cpl)}</span>
        </div>
    `;

		if (data.length === 0) {
			html += `<div class="alert alert-info"><i class="bi bi-info-circle"></i> Belum ada data CPMK yang terkait dengan CPL ini atau mahasiswa belum memiliki nilai.</div>`;
		} else {
			html += `
            <div class="modern-table-wrapper">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Kode CPMK</th>
                            <th width="38%">Deskripsi CPMK</th>
                            <th width="10%" class="text-center">Jumlah MK</th>
                            <th width="10%" class="text-center">Rata-rata</th>
                            <th width="25%">Detail Mata Kuliah</th>
                        </tr>
                    </thead>
                    <tbody>
        `;

			data.forEach((item, index) => {
				// Use dynamic threshold: passing+10 for "good", passing for "fair"
				const goodThreshold = passingThreshold + 10;
				const badgeClass = item.rata_rata >= goodThreshold ? 'success' : (item.rata_rata >= passingThreshold ? 'warning' : 'danger');

				let detailMk = '<ul class="mb-0" style="font-size: 0.85rem;">';
				if (item.detail_mk.length === 0) {
					detailMk += '<li class="text-muted">Belum ada nilai</li>';
				} else {
					item.detail_mk.forEach(mk => {
						detailMk += `<li>${mk.kode_mk} (${mk.tahun_akademik} - ${mk.kelas}): <strong>${parseFloat(mk.nilai_cpmk).toFixed(2)}</strong></li>`;
					});
				}
				detailMk += '</ul>';

				html += `
                <tr>
                    <td>${index + 1}</td>
                    <td><strong>${item.kode_cpmk}</strong></td>
                    <td><small>${item.deskripsi_cpmk}</small></td>
                    <td class="text-center">${item.jumlah_nilai}</td>
                    <td class="text-center"><span class="badge bg-${badgeClass}">${item.rata_rata.toFixed(2)}</span></td>
                    <td>${detailMk}</td>
                </tr>
            `;
			});

			html += `</tbody></table></div>`;
		}

		$('#detailCplModalContent').html(html);
	}

	// Helper Functions
	function createModernChart(containerId, chartData, title, exportFilename) {
		return new ModernChartComponent({
			containerId: containerId,
			chartData: chartData,
			config: {
				title: title,
				type: 'bar',
				passingThreshold: passingThreshold,
				showExportButton: true,
				showSubtitle: false,
				exportFilename: exportFilename,
				height: 80,
				labels: {
					yAxis: 'Persentase Capaian (%)',
					xAxis: 'Kode CPL'
				}
			}
		});
	}

	function createTableHTML(type, showAction) {
		const actionHeader = showAction ? '<th width="5%" class="text-center">Aksi</th>' : '';

		return `
        <div class="card mt-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="bi bi-table"></i> Detail Capaian CPL</h5>
            </div>
            <div class="card-body">
                <div class="modern-table-wrapper">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th width="5%">No</th>
                                <th width="10%">Kode CPL</th>
                                <th width="35%">Deskripsi CPL</th>
                                <th width="15%">Jenis CPL</th>
                                <th width="10%" class="text-center">${type === 'Individual' ? 'Jumlah CPMK' : 'Jumlah Mhs'}</th>
                                ${type === 'Subject' ? '<th width="10%" class="text-center">Jumlah CPMK</th>' : ''}
                                <th width="10%" class="text-center">Capaian (%)</th>
                                <th width="10%" class="text-center">Status</th>
                                ${actionHeader}
                            </tr>
                        </thead>
                        <tbody id="detailTableBody${type}">
                        </tbody>
                    </table>
                </div>
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

	function getJenisCplLabel(jenis) {
		const labels = {
			'P': 'Pengetahuan',
			'KK': 'Keterampilan Khusus',
			'S': 'Sikap',
			'KU': 'Keterampilan Umum'
		};
		return labels[jenis] || jenis;
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
		$('#calculationExplanationIndividual').addClass('d-none');
		$('#detailCalculationIndividual').addClass('d-none');
		$('#detailCalculationComparative').addClass('d-none');
		$(`#${emptyStateId}`).removeClass('d-none').html(`
        <i class="bi bi-exclamation-circle" style="font-size: 4rem; color: #dc3545;"></i>
        <p class="text-danger mt-3">${message}</p>
    `);
	}

	function bindExportButton(selector, chart, filename) {
		$(document).off('click', selector);
		$(document).on('click', selector, function() {
			if (chart) {
				const link = document.createElement('a');
				link.download = filename;
				link.href = chart.toBase64Image();
				link.click();
			}
		});
	}

	function exportChart(chart, filename) {
		if (chart) {
			const link = document.createElement('a');
			link.download = filename;
			link.href = chart.toBase64Image();
			link.click();
		}
	}
</script>

<?= $this->endSection() ?>
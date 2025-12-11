<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<style>
	.filter-card {
		border: 2px solid #e0e0e0;
		background: #ffffff;
	}

	.filter-card:hover {
		transform: translateY(-5px);
		border-color: #0d6efd !important;
	}

	.filter-card.border-primary {
		background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
	}

	.filter-card i {
		transition: all 0.3s ease;
	}

	.filter-card:hover i {
		transform: scale(1.1);
	}

	.filter-card .card-title {
		transition: all 0.3s ease;
		font-size: 1.1rem;
	}

	.filter-card .card-text {
		line-height: 1.4;
		min-height: 40px;
	}
</style>

<div class="card">
	<div class="card-body">
		<h2 class="mb-4">Capaian CPMK</h2>

		<!-- Tab Navigation -->
		<ul class="nav nav-tabs mb-4" id="cpmkTabs" role="tablist">
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
						<div class="card mb-4">
							<div class="card-header bg-light">
								<h5 class="mb-0"><i class="bi bi-funnel"></i> Filter CPMK Mahasiswa</h5>
							</div>
							<div class="card-body">
								<form id="filterIndividualForm1">
									<div class="row g-3 mb-3">
										<div class="col-md-6">
											<label for="programStudiSelect1" class="form-label">Program Studi</label>
											<select class="form-select" id="programStudiSelect1" name="program_studi">
												<option value="">-- Semua Program Studi --</option>
												<?php foreach ($programStudi as $prodi): ?>
													<option value="<?= esc($prodi) ?>" <?= $prodi === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-6">
											<label for="tahunAngkatanSelect1" class="form-label">Tahun Angkatan</label>
											<select class="form-select" id="tahunAngkatanSelect1" name="tahun_angkatan">
												<option value="">-- Semua Tahun --</option>
												<?php foreach ($tahunAngkatan as $tahun): ?>
													<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="row g-3">
										<div class="col-md-10">
											<label for="mahasiswaSelect1" class="form-label">Mahasiswa <span class="text-danger">*</span></label>
											<select class="form-select" id="mahasiswaSelect1" name="mahasiswa_id">
												<option value="">-- Pilih Mahasiswa --</option>
											</select>
										</div>
										<div class="col-md-2 d-flex align-items-end">
											<button type="submit" class="btn btn-primary w-100">
												<i class="bi bi-search"></i> Tampilkan
											</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>

					<!-- Filter 2: CPMK Mahasiswa per Semester -->
					<div class="tab-pane fade" id="filter2" role="tabpanel">
						<div class="card mb-4">
							<div class="card-header bg-light">
								<h5 class="mb-0"><i class="bi bi-funnel"></i> Filter CPMK Mahasiswa per Semester</h5>
							</div>
							<div class="card-body">
								<form id="filterIndividualForm2">
									<div class="row g-3 mb-3">
										<div class="col-md-6">
											<label for="programStudiSelect2" class="form-label">Program Studi</label>
											<select class="form-select" id="programStudiSelect2" name="program_studi">
												<option value="">-- Semua Program Studi --</option>
												<?php foreach ($programStudi as $prodi): ?>
													<option value="<?= esc($prodi) ?>" <?= $prodi === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-6">
											<label for="tahunAngkatanSelect2" class="form-label">Tahun Angkatan</label>
											<select class="form-select" id="tahunAngkatanSelect2" name="tahun_angkatan">
												<option value="">-- Semua Tahun --</option>
												<?php foreach ($tahunAngkatan as $tahun): ?>
													<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="row g-3 mb-3">
										<div class="col-md-6">
											<label for="mahasiswaSelect2" class="form-label">Mahasiswa <span class="text-danger">*</span></label>
											<select class="form-select" id="mahasiswaSelect2" name="mahasiswa_id">
												<option value="">-- Pilih Mahasiswa --</option>
											</select>
										</div>
										<div class="col-md-6">
											<label for="semesterSelect2" class="form-label">Semester <span class="text-danger">*</span></label>
											<select class="form-select" id="semesterSelect2" name="semester">
												<option value="">-- Pilih Semester --</option>
												<?php foreach ($semesterList as $semester): ?>
													<option value="<?= esc($semester) ?>"><?= esc($semester) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="row g-3">
										<div class="col-md-12 text-end">
											<button type="submit" class="btn btn-primary">
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
						<div class="card mb-4">
							<div class="card-header bg-light">
								<h5 class="mb-0"><i class="bi bi-funnel"></i> Filter CPMK Mahasiswa per Tahun Akademik</h5>
							</div>
							<div class="card-body">
								<form id="filterIndividualForm3">
									<div class="row g-3 mb-3">
										<div class="col-md-6">
											<label for="programStudiSelect3" class="form-label">Program Studi</label>
											<select class="form-select" id="programStudiSelect3" name="program_studi">
												<option value="">-- Semua Program Studi --</option>
												<?php foreach ($programStudi as $prodi): ?>
													<option value="<?= esc($prodi) ?>" <?= $prodi === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-6">
											<label for="tahunAngkatanSelect3" class="form-label">Tahun Angkatan</label>
											<select class="form-select" id="tahunAngkatanSelect3" name="tahun_angkatan">
												<option value="">-- Semua Tahun --</option>
												<?php foreach ($tahunAngkatan as $tahun): ?>
													<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="row g-3 mb-3">
										<div class="col-md-6">
											<label for="mahasiswaSelect3" class="form-label">Mahasiswa <span class="text-danger">*</span></label>
											<select class="form-select" id="mahasiswaSelect3" name="mahasiswa_id">
												<option value="">-- Pilih Mahasiswa --</option>
											</select>
										</div>
										<div class="col-md-6">
											<label for="tahunAkademikSelect3" class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
											<select class="form-select" id="tahunAkademikSelect3" name="tahun_akademik_filter">
												<option value="">-- Pilih Tahun Akademik --</option>
												<?php foreach ($tahunAkademikList as $ta): ?>
													<option value="<?= esc($ta) ?>"><?= esc($ta) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="row g-3">
										<div class="col-md-12 text-end">
											<button type="submit" class="btn btn-primary">
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
						<div class="card-header bg-secondary text-white">
							<h5 class="mb-0"><i class="bi bi-table"></i> Detail Perhitungan CPMK</h5>
						</div>
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
						<div class="card mb-4">
							<div class="card-header bg-light">
								<h5 class="mb-0"><i class="bi bi-funnel"></i> Filter CPMK Angkatan</h5>
							</div>
							<div class="card-body">
								<form id="filterComparativeForm1">
									<div class="row g-3 mb-3">
										<div class="col-md-6">
											<label for="programStudiComparativeSelect1" class="form-label">Program Studi <span class="text-danger">*</span></label>
											<select class="form-select" id="programStudiComparativeSelect1" name="program_studi" required>
												<option value="">-- Pilih Program Studi --</option>
												<?php foreach ($programStudi as $prodi): ?>
													<option value="<?= esc($prodi) ?>" <?= $prodi === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-6">
											<label for="tahunAngkatanComparativeSelect1" class="form-label">Tahun Angkatan <span class="text-danger">*</span></label>
											<select class="form-select" id="tahunAngkatanComparativeSelect1" name="tahun_angkatan" required>
												<option value="">-- Pilih Tahun Angkatan --</option>
												<?php foreach ($tahunAngkatan as $tahun): ?>
													<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="row g-3">
										<div class="col-md-12 text-end">
											<button type="submit" class="btn btn-primary">
												<i class="bi bi-search"></i> Tampilkan
											</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>

					<!-- Filter 2: CPMK Angkatan per Semester -->
					<div class="tab-pane fade" id="filterComparative2" role="tabpanel">
						<div class="card mb-4">
							<div class="card-header bg-light">
								<h5 class="mb-0"><i class="bi bi-funnel"></i> Filter CPMK Angkatan per Semester</h5>
							</div>
							<div class="card-body">
								<form id="filterComparativeForm2">
									<div class="row g-3 mb-3">
										<div class="col-md-6">
											<label for="programStudiComparativeSelect2" class="form-label">Program Studi <span class="text-danger">*</span></label>
											<select class="form-select" id="programStudiComparativeSelect2" name="program_studi" required>
												<option value="">-- Pilih Program Studi --</option>
												<?php foreach ($programStudi as $prodi): ?>
													<option value="<?= esc($prodi) ?>" <?= $prodi === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-6">
											<label for="tahunAngkatanComparativeSelect2" class="form-label">Tahun Angkatan <span class="text-danger">*</span></label>
											<select class="form-select" id="tahunAngkatanComparativeSelect2" name="tahun_angkatan" required>
												<option value="">-- Pilih Tahun Angkatan --</option>
												<?php foreach ($tahunAngkatan as $tahun): ?>
													<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="row g-3 mb-3">
										<div class="col-md-6">
											<label for="semesterComparativeSelect2" class="form-label">Semester <span class="text-danger">*</span></label>
											<select class="form-select" id="semesterComparativeSelect2" name="semester" required>
												<option value="">-- Pilih Semester --</option>
												<?php foreach ($semesterList as $semester): ?>
													<option value="<?= esc($semester) ?>"><?= esc($semester) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="row g-3">
										<div class="col-md-12 text-end">
											<button type="submit" class="btn btn-primary">
												<i class="bi bi-search"></i> Tampilkan
											</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>

					<!-- Filter 3: CPMK Angkatan per Tahun Akademik -->
					<div class="tab-pane fade" id="filterComparative3" role="tabpanel">
						<div class="card mb-4">
							<div class="card-header bg-light">
								<h5 class="mb-0"><i class="bi bi-funnel"></i> Filter CPMK Angkatan per Tahun Akademik</h5>
							</div>
							<div class="card-body">
								<form id="filterComparativeForm3">
									<div class="row g-3 mb-3">
										<div class="col-md-6">
											<label for="programStudiComparativeSelect3" class="form-label">Program Studi <span class="text-danger">*</span></label>
											<select class="form-select" id="programStudiComparativeSelect3" name="program_studi" required>
												<option value="">-- Pilih Program Studi --</option>
												<?php foreach ($programStudi as $prodi): ?>
													<option value="<?= esc($prodi) ?>" <?= $prodi === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
										<div class="col-md-6">
											<label for="tahunAngkatanComparativeSelect3" class="form-label">Tahun Angkatan <span class="text-danger">*</span></label>
											<select class="form-select" id="tahunAngkatanComparativeSelect3" name="tahun_angkatan" required>
												<option value="">-- Pilih Tahun Angkatan --</option>
												<?php foreach ($tahunAngkatan as $tahun): ?>
													<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="row g-3 mb-3">
										<div class="col-md-6">
											<label for="tahunAkademikComparativeSelect3" class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
											<select class="form-select" id="tahunAkademikComparativeSelect3" name="tahun_akademik_filter" required>
												<option value="">-- Pilih Tahun Akademik --</option>
												<?php foreach ($tahunAkademikList as $ta): ?>
													<option value="<?= esc($ta) ?>"><?= esc($ta) ?></option>
												<?php endforeach; ?>
											</select>
										</div>
									</div>
									<div class="row g-3">
										<div class="col-md-12 text-end">
											<button type="submit" class="btn btn-primary">
												<i class="bi bi-search"></i> Tampilkan
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
						<div class="card-header bg-secondary text-white">
							<h5 class="mb-0"><i class="bi bi-table"></i> Detail Perhitungan CPMK per Angkatan</h5>
						</div>
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
				<div class="card mb-4">
					<div class="card-header bg-light">
						<h5 class="mb-0"><i class="bi bi-funnel"></i> Filter</h5>
					</div>
					<div class="card-body">
						<form id="filterKeseluruhanForm">
							<div class="row g-3">
								<div class="col-md-10">
									<label for="programStudiKeseluruhanSelect" class="form-label">Program Studi <span class="text-danger">*</span></label>
									<select class="form-select" id="programStudiKeseluruhanSelect" name="program_studi" required>
										<option value="">-- Pilih Program Studi --</option>
										<?php foreach ($programStudi as $prodi): ?>
											<option value="<?= esc($prodi) ?>" <?= $prodi === 'Teknik Informatika' ? 'selected' : '' ?>><?= esc($prodi) ?></option>
										<?php endforeach; ?>
									</select>
								</div>
								<div class="col-md-2 d-flex align-items-end">
									<button type="submit" class="btn btn-primary w-100">
										<i class="bi bi-search"></i> Tampilkan
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
						<div class="card-header bg-secondary text-white">
							<h5 class="mb-0"><i class="bi bi-table"></i> Detail Perhitungan CPMK Keseluruhan</h5>
						</div>
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
			<div class="modal-header bg-primary text-white">
				<h5 class="modal-title" id="detailCpmkModalTitle">Detail Capaian CPMK</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
			</div>
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

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

<!-- DataTables CSS -->
<link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

<script>
	// Dynamic passing threshold from grade configuration
	const passingThreshold = <?= json_encode($passing_threshold ?? 65) ?>;

	let cpmkChartIndividual = null;
	let cpmkChartComparative = null;
	let cpmkChartKeseluruhan = null;
	let currentIndividualData = null;
	let currentComparativeData = null;
	let currentKeseluruhanData = null;
	let currentActiveFilter = 1; // Track which filter is currently active for Individual tab (1, 2, or 3)
	let currentActiveComparativeFilter = 1; // Track which filter is currently active for Comparative tab (1, 2, or 3)

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

		bindExportButton('#exportChartIndividualBtn', cpmkChartIndividual, 'capaian-cpmk-individual.png');

		const ctx = document.getElementById('cpmkChartIndividual').getContext('2d');
		if (cpmkChartIndividual) cpmkChartIndividual.destroy();

		cpmkChartIndividual = createBarChart(ctx, response.chartData, 'Capaian CPMK Mahasiswa', 'rgba(13, 110, 253, 0.8)', 'rgba(13, 110, 253, 1)');

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

		let html = '<div class="table-responsive"><table id="individualDetailTable" class="table table-bordered table-hover">';
		html += '<thead class="table-light">';
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

		// Destroy existing DataTable if it exists
		if ($.fn.DataTable.isDataTable('#individualDetailTable')) {
			$('#individualDetailTable').DataTable().destroy();
		}

		// Initialize DataTable with pagination
		window.individualDetailTable = $('#individualDetailTable').DataTable({
			pageLength: 10,
			language: {
				url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
			},
			columnDefs: [{
				orderable: false,
				targets: -1
			}]
		});
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

		let totalCpmk = 0;
		let totalBobot = 0;

		// Display each course with its assessment breakdown
		data.courses.forEach((course, idx) => {
			html += `
				<div class="mb-4">
					<h6><strong>${course.kode_mk}</strong> - ${course.nama_mk}</h6>
					<p class="mb-2"><small class="text-muted">${course.tahun_akademik} / ${course.kelas}</small></p>
			`;

			if (course.assessments && course.assessments.length > 0) {
				html += `
					<div class="table-responsive">
						<table class="table table-sm table-bordered">
							<thead class="table-light">
								<tr>
									<th>Teknik Penilaian</th>
									<th class="text-center">Nilai</th>
									<th class="text-center">Bobot (%)</th>
									<th class="text-center">CPMK</th>
								</tr>
							</thead>
							<tbody>
				`;

				course.assessments.forEach(assessment => {
					html += `
						<tr>
							<td>${assessment.teknik}</td>
							<td class="text-center">${assessment.nilai}</td>
							<td class="text-center">${assessment.bobot}%</td>
							<td class="text-center">${assessment.weighted.toFixed(2)}</td>
						</tr>
					`;
				});

				const nilaiCpmk = course.total_weighted;

				html += `
							</tbody>
							<tfoot class="table-light">
								<tr>
									<td colspan="3" class="text-end"><strong>Total</strong></td>
									<td class="text-center"><strong>${nilaiCpmk.toFixed(2)}</strong></td>
								</tr>
							</tfoot>
						</table>
					</div>
				`;
			} else {
				html += `<p class="text-muted">Belum ada nilai</p>`;
			}

			html += `</div>`;
		});

		// Display formula and final capaian
		html += `
			<div class="alert alert-primary mb-0">
				<h6 class="mb-2"><i class="bi bi-calculator"></i> Capaian ${data.kode_cpmk}:</h6>
				<p class="mb-1"><strong>Capaian CPMK</strong> = ${data.summary.grand_total_weighted} / ${data.summary.grand_total_bobot} × 100 = ${data.summary.capaian}%</p>

			</div>
		`;

		$('#detailCpmkModalContent').html(html);
	}

	function displayComparativeChart(response) {
		const chartHTML = createChartHTML('Comparative', 'primary', 'cpmkChartComparative');
		$('#chartSectionComparative').html(chartHTML);

		bindExportButton('#exportChartComparativeBtn', cpmkChartComparative, 'capaian-cpmk-comparative.png');

		const ctx = document.getElementById('cpmkChartComparative').getContext('2d');
		if (cpmkChartComparative) cpmkChartComparative.destroy();

		cpmkChartComparative = createBarChart(ctx, response.chartData, 'Rata-rata Capaian CPMK Angkatan', 'rgba(13, 110, 253, 0.8)', 'rgba(13, 110, 253, 1)');

		// Display detailed calculation
		displayComparativeDetailedCalculation(response);
	}

	function displayKeseluruhanChart(response) {
		const chartHTML = createChartHTML('Keseluruhan', 'primary', 'cpmkChartKeseluruhan');
		$('#chartSectionKeseluruhan').html(chartHTML);

		bindExportButton('#exportChartKeseluruhanBtn', cpmkChartKeseluruhan, 'capaian-cpmk-keseluruhan.png');

		const ctx = document.getElementById('cpmkChartKeseluruhan').getContext('2d');
		if (cpmkChartKeseluruhan) cpmkChartKeseluruhan.destroy();

		cpmkChartKeseluruhan = createBarChart(ctx, response.chartData, 'Rata-rata Capaian CPMK Keseluruhan', 'rgba(13, 110, 253, 0.8)', 'rgba(13, 110, 253, 1)');

		// Display detailed calculation
		displayKeseluruhanDetailedCalculation(response);
	}

	function displayComparativeDetailedCalculation(response) {
		if (!response.chartData || !response.chartData.details) {
			$('#detailCalculationComparative').addClass('d-none');
			return;
		}

		let html = '<div class="table-responsive"><table id="comparativeDetailTable" class="table table-bordered table-hover">';
		html += '<thead class="table-light">';
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

		// Destroy existing DataTable if it exists
		if ($.fn.DataTable.isDataTable('#comparativeDetailTable')) {
			$('#comparativeDetailTable').DataTable().destroy();
		}

		// Initialize DataTable with pagination
		window.comparativeDetailTable = $('#comparativeDetailTable').DataTable({
			pageLength: 10,
			language: {
				url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
			},
			columnDefs: [{
				orderable: false,
				targets: -1
			}]
		});
	}

	function displayKeseluruhanDetailedCalculation(response) {
		if (!response.chartData || !response.chartData.details) {
			$('#detailCalculationKeseluruhan').addClass('d-none');
			return;
		}

		let html = '<div class="table-responsive"><table id="keseluruhanDetailTable" class="table table-bordered table-hover">';
		html += '<thead class="table-light">';
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

		// Destroy existing DataTable if it exists
		if ($.fn.DataTable.isDataTable('#keseluruhanDetailTable')) {
			$('#keseluruhanDetailTable').DataTable().destroy();
		}

		// Initialize DataTable with pagination
		window.keseluruhanDetailTable = $('#keseluruhanDetailTable').DataTable({
			pageLength: 10,
			language: {
				url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
			},
			columnDefs: [{
				orderable: false,
				targets: -1
			}]
		});
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
				<div class="table-responsive">
					<table id="comparativeCpmkDetailTable_${index}" class="table table-bordered table-sm table-hover mb-0">
						<thead class="table-primary">
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
						<tfoot class="table-light">
							<tr>
								<td colspan="3" class="text-end"><strong>Total dari ${summary.jumlah_mahasiswa} mahasiswa:</strong></td>
								<td class="text-center"><strong>${summary.total_nilai.toFixed(2)}%</strong></td>
							</tr>
							<tr class="table-success">
								<td colspan="3" class="text-end"><strong>Rata - rata  = ${summary.total_nilai.toFixed(2)} / ${summary.jumlah_mahasiswa} mahasiswa</strong></td>
								<td class="text-center"><h6 class="mb-0"><strong>${summary.rata_rata.toFixed(2)}%</strong></h6></td>
							</tr>
						</tfoot>
					</table>
				</div>
			`;
		}

		$('#detailCpmkModalContent').html(html);

		// Initialize DataTable with pagination
		if (data.length > 0) {
			// Destroy existing DataTable if it exists
			if ($.fn.DataTable.isDataTable(`#comparativeCpmkDetailTable_${index}`)) {
				$(`#comparativeCpmkDetailTable_${index}`).DataTable().destroy();
			}

			$(`#comparativeCpmkDetailTable_${index}`).DataTable({
				pageLength: 10,
				language: {
					url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
				}
			});
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
				<div class="table-responsive">
					<table id="keseluruhanCpmkDetailTable_${index}" class="table table-bordered table-sm table-hover mb-0">
						<thead class="table-primary">
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
						<tfoot class="table-light">
							<tr>
								<td colspan="4" class="text-end"><strong>Total dari ${summary.jumlah_mahasiswa} mahasiswa (semua angkatan):</strong></td>
								<td class="text-center"><strong>${summary.total_nilai.toFixed(2)}%</strong></td>
							</tr>
							<tr class="table-success">
								<td colspan="4" class="text-end"><strong>Rata - rata = ${summary.total_nilai.toFixed(2)}% / ${summary.jumlah_mahasiswa} mahasiswa</strong></td>
								<td class="text-center"><h6 class="mb-0"><strong>${summary.rata_rata.toFixed(2)}%</strong></h6></td>
							</tr>
						</tfoot>
					</table>
				</div>
			`;
		}

		$('#detailCpmkModalContent').html(html);

		// Initialize DataTable with pagination
		if (data.length > 0) {
			// Destroy existing DataTable if it exists
			if ($.fn.DataTable.isDataTable(`#keseluruhanCpmkDetailTable_${index}`)) {
				$(`#keseluruhanCpmkDetailTable_${index}`).DataTable().destroy();
			}

			$(`#keseluruhanCpmkDetailTable_${index}`).DataTable({
				pageLength: 10,
				language: {
					url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
				}
			});
		}
	}

	// Helper Functions
	function createBarChart(ctx, chartData, title, backgroundColor, borderColor) {
		// Create conditional colors based on passing threshold
		const backgroundColors = chartData.data.map(value =>
			value < passingThreshold ? 'rgba(220, 53, 69, 0.8)' : backgroundColor
		);
		const borderColors = chartData.data.map(value =>
			value < passingThreshold ? 'rgba(220, 53, 69, 1)' : borderColor
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
					borderWidth: 2,
					borderRadius: 5
				}]
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
							generateLabels: function(chart) {
								const data = chart.data.datasets[0].data;
								const labels = [];

								// Check if there are values >= threshold (blue bars)
								const hasAboveThreshold = data.some(value => value >= passingThreshold);
								if (hasAboveThreshold) {
									labels.push({
										text: `Capaian ≥ ${passingThreshold}%`,
										fillStyle: 'rgba(13, 110, 253, 0.8)',
										strokeStyle: 'rgba(13, 110, 253, 1)',
										lineWidth: 2,
										hidden: false,
										index: 0
									});
								}

								// Check if there are values < threshold (red bars)
								const hasBelowThreshold = data.some(value => value < passingThreshold);
								if (hasBelowThreshold) {
									labels.push({
										text: `Capaian < ${passingThreshold}%`,
										fillStyle: 'rgba(220, 53, 69, 0.8)',
										strokeStyle: 'rgba(220, 53, 69, 1)',
										lineWidth: 2,
										hidden: false,
										index: 1
									});
								}

								return labels;
							}
						}
					},
					title: {
						display: true,
						text: title,
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
								return 'Capaian CPMK: ' + context.parsed.y.toFixed(2) + '%';
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
							size: 12
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
							text: 'Capaian CPMK',
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
							text: 'Kode CPMK',
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

	function createChartHTML(type, color, canvasId, title = 'Grafik Capaian CPMK') {
		return `
        <div class="card">
            <div class="card-header bg-${color} text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-bar-chart-fill"></i> ${title}</h5>
                <button class="btn btn-light btn-sm" id="exportChart${type}Btn">
                    <i class="bi bi-download"></i> Export PNG
                </button>
            </div>
            <div class="card-body">
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
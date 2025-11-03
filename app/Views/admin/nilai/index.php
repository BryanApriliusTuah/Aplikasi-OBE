<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
	<h2 class="fw-bold my-4 text-center">Penilaian Jadwal Mengajar</h2>

	<?php if (session()->getFlashdata('success')): ?>
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			<?= esc(session()->getFlashdata('success')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>
	<?php if (session()->getFlashdata('error')): ?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<?= esc(session()->getFlashdata('error')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>

	<div class="card shadow-sm mb-4">
		<div class="card-header bg-light p-3">
			<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
				<div class="d-flex align-items-center gap-2">
					<i class="bi bi-funnel-fill fs-5 text-primary"></i>
					<h5 class="mb-0">Filter Jadwal</h5>
				</div>
			</div>
		</div>
		<div class="card-body">
			<form method="GET" action="<?= current_url() ?>">
				<div class="row g-3 align-items-end">
					<div class="col-md-4">
						<label for="filter_program_studi" class="form-label">Program Studi</label>
						<select class="form-select" id="filter_program_studi" name="program_studi">
							<option value="">Semua Program Studi</option>
							<option value="Teknik Informatika" <?= ($filters['program_studi'] ?? '') == 'Teknik Informatika' ? 'selected' : '' ?>>Teknik Informatika</option>
							<option value="Sistem Informasi" <?= ($filters['program_studi'] ?? '') == 'Sistem Informasi' ? 'selected' : '' ?>>Sistem Informasi</option>
							<option value="Teknik Komputer" <?= ($filters['program_studi'] ?? '') == 'Teknik Komputer' ? 'selected' : '' ?>>Teknik Komputer</option>
						</select>
					</div>
					<div class="col-md-4">
						<label for="filter_tahun_akademik" class="form-label">Tahun Akademik</label>
						<input type="text" class="form-control" name="tahun_akademik" value="<?= esc($filters['tahun_akademik'] ?? '') ?>" placeholder="e.g. 2025/2026 Ganjil">
					</div>
					<div class="col-md-4 d-flex gap-2">
						<button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Terapkan</button>
						<a href="<?= current_url() ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
					</div>
				</div>
			</form>
		</div>
	</div>

	<div class="row g-3">
		<?php foreach ($jadwal_by_day as $day => $schedules): ?>
			<div class="col-12 col-md-6 col-xl-4">
				<div class="card shadow-sm h-100">
					<div class="card-header fw-bold text-center bg-light">
						<?= esc($day) ?> <span class="badge bg-secondary rounded-pill"><?= count($schedules) ?></span>
					</div>
					<div class="card-body">
						<?php if (empty($schedules)): ?>
							<div class="text-center text-muted pt-5">
								<i class="bi bi-moon-stars fs-1"></i>
								<p class="mt-2 small">Tidak ada jadwal</p>
							</div>
						<?php else: ?>
							<?php foreach ($schedules as $jadwal): ?>
								<div class="card mb-3 shadow-sm">
									<div class="card-body p-3">
										<div class="d-flex justify-content-between align-items-start mb-1">
											<div>
												<p class="card-title fw-bold mb-0">
													<?= esc($jadwal['nama_mk']) ?>
												</p>
												<small class="text-muted">
													<?php if (isset($jadwal['kode_mk'])): ?>
														<i class="bi bi-code-square"></i> <?= esc($jadwal['kode_mk']) ?> •
													<?php endif; ?>
													<i class="bi bi-people-fill"></i> Kelas <?= esc($jadwal['kelas']) ?>
												</small>
											</div>
											<?php if (isset($jadwal['is_nilai_validated']) && $jadwal['is_nilai_validated'] == 1): ?>
												<span class="badge bg-success" title="Nilai telah divalidasi">
													<i class="bi bi-check-circle-fill"></i> Tervalidasi
												</span>
											<?php else: ?>
												<span class="badge bg-warning text-dark" title="Nilai belum divalidasi">
													<i class="bi bi-clock-history"></i> Belum Validasi
												</span>
											<?php endif; ?>
										</div>
										<div class="small text-muted mb-2">
											<i class="bi bi-clock"></i> <?= date('H:i', strtotime($jadwal['jam_mulai'])) ?> - <?= date('H:i', strtotime($jadwal['jam_selesai'])) ?> |
											<?php if (isset($jadwal['ruang'])): ?>
												<i class="bi bi-geo-alt"></i> <?= esc($jadwal['ruang']) ?>
											<?php else: ?>
												<i class="bi bi-geo-alt"></i> Belum ada ruang
											<?php endif; ?>
										</div>
										<div class="small text-muted">
											<i class="bi bi-person-check"></i> <?= esc($jadwal['dosen_ketua'] ?? 'Belum ada ketua') ?>
										</div>
										<div class="mt-2">
											<span class="small text-muted">Program Studi: <?= esc($jadwal['program_studi']) ?></span> <br />
											<span class="small text-muted">Tahun Akademik: <?= esc($jadwal['tahun_akademik']) ?></span>
										</div>
										<?php if (isset($jadwal['score_completion'])): ?>
											<?php
											$completed = $jadwal['score_completion']['completed'];
											$total = $jadwal['score_completion']['total'];
											$percentage = $total > 0 ? round(($completed / $total) * 100) : 0;
											$progress_color = $percentage == 100 ? 'success' : ($percentage >= 50 ? 'warning' : 'danger');
											?>
											<div class="mt-2 pt-2 border-top">
												<div class="d-flex justify-content-between align-items-center mb-1">
													<small class="text-muted">
														<i class="bi bi-clipboard-data"></i> Jumlah Terisi
													</small>
													<small class="fw-bold">
														<?= $completed ?>/<?= $total ?> Mahasiswa
													</small>
												</div>
												<div class="progress" style="height: 6px;">
													<div class="progress-bar bg-<?= $progress_color ?>" role="progressbar" style="width: <?= $percentage ?>%" aria-valuenow="<?= $percentage ?>" aria-valuemin="0" aria-valuemax="100"></div>
												</div>
											</div>
										<?php endif; ?>
									</div>
									<div class="card-footer bg-white p-2 border-top-0">
										<div class="d-flex gap-2 justify-content-end">
											<a href="<?= base_url('admin/nilai/lihat-nilai/' . $jadwal['id']) ?>" class="btn btn-sm btn-outline-info">
												<i class="bi bi-eye"></i> Lihat Nilai
											</a>

											<?php if (isset($jadwal['can_input_grades']) && $jadwal['can_input_grades']): ?>
												<div class="btn-group" role="group">
													<button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
														<i class="bi bi-pencil-square"></i> Input Nilai
													</button>
													<ul class="dropdown-menu dropdown-menu-end">
														<li>
															<a class="dropdown-item" href="<?= base_url('admin/nilai/input-nilai-teknik/' . $jadwal['id']) ?>">
																<i class="bi bi-clipboard-check text-primary me-2"></i>
																<strong>By Teknik Penilaian</strong>
																<small class="d-block text-muted">Kehadiran, Tugas, UTS, UAS, dll</small>
															</a>
														</li>
														<li><hr class="dropdown-divider"></li>
														<li>
															<a class="dropdown-item" href="<?= base_url('admin/nilai/input-nilai/' . $jadwal['id']) ?>">
																<i class="bi bi-list-check text-secondary me-2"></i>
																By CPMK (Langsung)
																<small class="d-block text-muted">Input langsung per CPMK</small>
															</a>
														</li>
													</ul>
												</div>
											<?php else: ?>
												<span class="btn btn-sm btn-secondary disabled" title="Hanya dosen pengampu yang dapat menginput nilai">
													<i class="bi bi-lock"></i> Terbatas
												</span>
											<?php endif; ?>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<?= $this->endSection() ?>
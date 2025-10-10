<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
	<!-- Header Section -->
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h1 class="h3 mb-1">Penilaian Jadwal Mengajar</h1>
			<p class="text-muted mb-0">Kelola nilai dan penilaian mahasiswa</p>
		</div>
		<div>
			<?php
			$total_schedules = 0;
			foreach ($jadwal_by_day as $schedules) {
				$total_schedules += count($schedules);
			}
			?>
			<span class="badge bg-primary fs-6 px-3 py-2"><?= $total_schedules ?> Jadwal</span>
		</div>
	</div>

	<!-- Flash Messages -->
	<?php if (session()->getFlashdata('success')): ?>
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			<i class="bi bi-check-circle me-2"></i><?= esc(session()->getFlashdata('success')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
		</div>
	<?php endif; ?>
	<?php if (session()->getFlashdata('error')): ?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<i class="bi bi-exclamation-triangle me-2"></i><?= esc(session()->getFlashdata('error')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
		</div>
	<?php endif; ?>

	<!-- Filter Section -->
	<div class="card shadow-sm mb-4">
		<div class="card-body">
			<form method="GET" action="<?= current_url() ?>">
				<div class="row g-3 align-items-end">
					<div class="col-md-4">
						<label class="form-label fw-semibold">Program Studi</label>
						<select class="form-select" name="program_studi">
							<option value="">Semua Program Studi</option>
							<option value="Teknik Informatika" <?= ($filters['program_studi'] ?? '') == 'Teknik Informatika' ? 'selected' : '' ?>>Teknik Informatika</option>
							<option value="Sistem Informasi" <?= ($filters['program_studi'] ?? '') == 'Sistem Informasi' ? 'selected' : '' ?>>Sistem Informasi</option>
							<option value="Teknik Komputer" <?= ($filters['program_studi'] ?? '') == 'Teknik Komputer' ? 'selected' : '' ?>>Teknik Komputer</option>
						</select>
					</div>
					<div class="col-md-5">
						<label class="form-label fw-semibold">Tahun Akademik</label>
						<input type="text" class="form-control" name="tahun_akademik" value="<?= esc($filters['tahun_akademik'] ?? '') ?>" placeholder="Contoh: 2025/2026 Ganjil">
					</div>
					<div class="col-md-3">
						<div class="d-flex gap-2">
							<button type="submit" class="btn btn-primary flex-fill">
								<i class="bi bi-search me-1"></i>Filter
							</button>
							<a href="<?= current_url() ?>" class="btn btn-outline-secondary">
								<i class="bi bi-arrow-clockwise"></i>
							</a>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- Main Table -->
	<div class="card shadow-sm">
		<div class="card-header bg-light">
			<h5 class="mb-0"><i class="bi bi-table me-2"></i>Daftar Jadwal Mengajar</h5>
		</div>
		<div class="card-body p-0">
			<div class="table-responsive">
				<table class="table table-hover mb-0">
					<thead class="table-dark">
						<tr>
							<th width="5%">#</th>
							<th width="8%">Hari</th>
							<th width="15%">Mata Kuliah</th>
							<th width="10%">Kelas</th>
							<th width="12%">Program Studi</th>
							<th width="10%">Waktu</th>
							<th width="8%">Ruang</th>
							<th width="15%">Dosen</th>
							<th width="5%">SKS</th>
							<th width="12%">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$no = 1;
						$hasData = false;
						$dayColors = [
							'Senin' => 'primary',
							'Selasa' => 'success',
							'Rabu' => 'info',
							'Kamis' => 'warning',
							'Jumat' => 'danger',
							'Sabtu' => 'dark'
						];
						?>
						<?php foreach ($jadwal_by_day as $day => $schedules): ?>
							<?php if (!empty($schedules)): ?>
								<?php $hasData = true; ?>
								<?php foreach ($schedules as $jadwal): ?>
									<tr>
										<td class="text-center fw-bold text-muted"><?= $no++ ?></td>
										<td>
											<span class="badge bg-<?= $dayColors[$day] ?> px-2 py-1">
												<?= esc($day) ?>
											</span>
										</td>
										<td>
											<div class="fw-semibold text-dark"><?= esc($jadwal['nama_mk']) ?></div>
											<small class="text-muted"><?= esc($jadwal['kode_mk'] ?? '') ?></small>
										</td>
										<td class="text-center">
											<span class="badge bg-secondary"><?= esc($jadwal['kelas']) ?></span>
										</td>
										<td>
											<small class="text-muted"><?= esc($jadwal['program_studi']) ?></small>
										</td>
										<td class="text-center">
											<div class="small">
												<i class="bi bi-clock text-<?= $dayColors[$day] ?>"></i>
												<?= date('H:i', strtotime($jadwal['jam_mulai'])) ?>-<?= date('H:i', strtotime($jadwal['jam_selesai'])) ?>
											</div>
										</td>
										<td class="text-center">
											<span class="badge bg-light text-dark border">
												<i class="bi bi-geo-alt"></i> <?= esc($jadwal['ruang'] ?: 'TBD') ?>
											</span>
										</td>
										<td>
											<div class="small">
												<i class="bi bi-person-check text-success"></i>
												<?= esc($jadwal['dosen_ketua'] ?? 'Belum ditentukan') ?>
											</div>
										</td>
										<td class="text-center">
											<span class="badge bg-info text-white"><?= esc($jadwal['sks']) ?></span>
										</td>
										<td>
											<div class="dropdown">
												<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
													<i class="bi bi-gear"></i> Aksi
												</button>
												<ul class="dropdown-menu">
													<li>
														<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#detailNilaiModal" data-jadwal-id="<?= $jadwal['id'] ?>">
															<i class="bi bi-graph-up text-info me-2"></i>Detail Nilai
														</a>
													</li>
													<li>
														<a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#dpnaModal" data-jadwal-id="<?= $jadwal['id'] ?>">
															<i class="bi bi-file-text text-success me-2"></i>Lihat DPNA
														</a>
													</li>
													<li>
														<hr class="dropdown-divider">
													</li>
													<li>
														<a class="dropdown-item" href="<?= base_url('admin/nilai/input-nilai/' . $jadwal['id']) ?>">
															<i class="bi bi-pencil-square text-primary me-2"></i>Input Nilai
														</a>
													</li>
												</ul>
											</div>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php endforeach; ?>

						<?php if (!$hasData): ?>
							<tr>
								<td colspan="10" class="text-center py-5 text-muted">
									<i class="bi bi-calendar-x fs-1 opacity-50"></i>
									<p class="mt-3 mb-0">Tidak ada jadwal yang ditemukan</p>
									<small>Coba ubah filter pencarian</small>
								</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			</div>
		</div>
		<?php if ($hasData): ?>
			<div class="card-footer bg-light">
				<div class="d-flex justify-content-between align-items-center">
					<small class="text-muted">
						Menampilkan <?= $total_schedules ?> jadwal mengajar
					</small>
					<div class="d-flex gap-2">
						<span class="badge bg-primary">Senin</span>
						<span class="badge bg-success">Selasa</span>
						<span class="badge bg-info">Rabu</span>
						<span class="badge bg-warning">Kamis</span>
						<span class="badge bg-danger">Jumat</span>
						<span class="badge bg-dark">Sabtu</span>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
</div>

<!-- Detail Nilai Modal -->
<div class="modal fade" id="detailNilaiModal" tabindex="-1" aria-labelledby="detailNilaiModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header bg-info text-white">
				<h5 class="modal-title" id="detailNilaiModalLabel">
					<i class="bi bi-graph-up me-2"></i>Detail Nilai CPMK Mahasiswa
				</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body" id="detailNilaiModalBody">
				<!-- Content loaded via AJAX -->
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
					<i class="bi bi-x-lg me-1"></i>Tutup
				</button>
			</div>
		</div>
	</div>
</div>

<!-- DPNA Modal -->
<div class="modal fade" id="dpnaModal" tabindex="-1" aria-labelledby="dpnaModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header bg-success text-white">
				<h5 class="modal-title" id="dpnaModalLabel">
					<i class="bi bi-file-text me-2"></i>Daftar Penilaian Nilai Akhir (DPNA)
				</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body" id="dpnaModalBody">
				<!-- Content loaded via AJAX -->
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
					<i class="bi bi-x-lg me-1"></i>Tutup
				</button>
			</div>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		const spinner = `
			<div class="text-center p-4">
				<div class="spinner-border text-primary" role="status">
					<span class="visually-hidden">Loading...</span>
				</div>
				<p class="mt-2 text-muted">Memuat data...</p>
			</div>`;

		// Detail Nilai CPMK Modal
		const detailNilaiModal = document.getElementById('detailNilaiModal');
		if (detailNilaiModal) {
			detailNilaiModal.addEventListener('show.bs.modal', function(event) {
				const button = event.relatedTarget;
				const jadwalId = button.getAttribute('data-jadwal-id');
				const modalBody = detailNilaiModal.querySelector('#detailNilaiModalBody');
				modalBody.innerHTML = spinner;

				fetch(`<?= base_url('admin/nilai/detail-nilai/') ?>${jadwalId}`, {
						headers: {
							'X-Requested-With': 'XMLHttpRequest'
						}
					})
					.then(response => response.json())
					.then(data => {
						if (data.cpmk_list.length === 0 || data.scores.length === 0) {
							modalBody.innerHTML = `
								<div class="alert alert-warning">
									<i class="bi bi-exclamation-triangle me-2"></i>
									Belum ada data CPMK atau mahasiswa untuk mata kuliah ini.
								</div>`;
							return;
						}

						let tableHtml = `
							<div class="table-responsive">
								<table class="table table-striped table-hover">
									<thead class="table-info">
										<tr>
											<th width="5%" class="text-center">#</th>
											<th width="15%">NIM</th>
											<th width="25%">Nama Mahasiswa</th>`;

						data.cpmk_list.forEach(cpmk => {
							tableHtml += `<th class="text-center" title="${cpmk.deskripsi}">${cpmk.kode_cpmk}</th>`;
						});
						tableHtml += `</tr></thead><tbody>`;

						data.scores.forEach((mhs, index) => {
							tableHtml += `
								<tr>
									<td class="text-center fw-bold text-muted">${index + 1}</td>
									<td class="fw-semibold">${mhs.nim}</td>
									<td>${mhs.nama_lengkap}</td>`;

							data.cpmk_list.forEach(cpmk => {
								const score = mhs.scores[cpmk.id] !== undefined ? mhs.scores[cpmk.id] : '-';
								let scoreClass = 'text-muted';
								if (score !== '-') {
									scoreClass = score >= 75 ? 'text-success fw-bold' :
										score >= 60 ? 'text-warning fw-bold' : 'text-danger fw-bold';
								}
								tableHtml += `<td class="text-center ${scoreClass}">${score}</td>`;
							});
							tableHtml += `</tr>`;
						});

						tableHtml += `</tbody></table></div>`;
						modalBody.innerHTML = tableHtml;
					})
					.catch(error => {
						modalBody.innerHTML = `
							<div class="alert alert-danger">
								<i class="bi bi-exclamation-circle me-2"></i>
								Gagal memuat detail nilai.
							</div>`;
					});
			});
		}

		// DPNA Modal
		const dpnaModal = document.getElementById('dpnaModal');
		if (dpnaModal) {
			dpnaModal.addEventListener('show.bs.modal', function(event) {
				const button = event.relatedTarget;
				const jadwalId = button.getAttribute('data-jadwal-id');
				const modalBody = dpnaModal.querySelector('#dpnaModalBody');
				modalBody.innerHTML = spinner;

				fetch(`<?= base_url('admin/nilai/dpna/') ?>${jadwalId}`, {
						headers: {
							'X-Requested-With': 'XMLHttpRequest'
						}
					})
					.then(response => response.json())
					.then(data => {
						if (!data.jadwal || data.nilai_mahasiswa.length === 0) {
							modalBody.innerHTML = `
								<div class="alert alert-warning">
									<i class="bi bi-exclamation-triangle me-2"></i>
									Belum ada nilai akhir yang diinput untuk kelas ini.
								</div>`;
							return;
						}

						let header = `
							<div class="alert alert-info">
								<h6 class="fw-bold mb-2">
									<i class="bi bi-book me-2"></i>${data.jadwal.nama_mk}
								</h6>
								<div class="row">
									<div class="col-md-6">
										<small><strong>Dosen:</strong> ${data.jadwal.dosen_ketua || 'N/A'}</small>
									</div>
									<div class="col-md-6">
										<small><strong>Tahun Akademik:</strong> ${data.jadwal.tahun_akademik}</small>
									</div>
								</div>
							</div>`;

						let tableHtml = header + `
							<div class="table-responsive">
								<table class="table table-striped table-hover">
									<thead class="table-success">
										<tr>
											<th width="5%" class="text-center">#</th>
											<th width="15%">NIM</th>
											<th width="30%">Nama Mahasiswa</th>
											<th width="15%" class="text-center">Nilai Akhir</th>
											<th width="10%" class="text-center">Huruf</th>
											<th width="25%" class="text-center">Status</th>
										</tr>
									</thead>
									<tbody>`;

						data.nilai_mahasiswa.forEach((item, index) => {
							const statusClass = item.status_kelulusan === 'Lulus' ? 'success' : 'danger';
							const gradeClass = item.nilai_akhir >= 75 ? 'success' :
								item.nilai_akhir >= 60 ? 'warning' : 'danger';

							tableHtml += `
								<tr>
									<td class="text-center fw-bold text-muted">${index + 1}</td>
									<td class="fw-semibold">${item.nim}</td>
									<td>${item.nama_lengkap}</td>
									<td class="text-center fw-bold text-${gradeClass}">
										${parseFloat(item.nilai_akhir).toFixed(2)}
									</td>
									<td class="text-center">
										<span class="badge bg-${gradeClass}">${item.nilai_huruf}</span>
									</td>
									<td class="text-center">
										<span class="badge bg-${statusClass}">
											${item.status_kelulusan}
										</span>
									</td>
								</tr>`;
						});

						tableHtml += `</tbody></table></div>`;
						modalBody.innerHTML = tableHtml;
					})
					.catch(error => {
						modalBody.innerHTML = `
							<div class="alert alert-danger">
								<i class="bi bi-exclamation-circle me-2"></i>
								Gagal memuat DPNA.
							</div>`;
					});
			});
		}
	});
</script>
<?= $this->endSection() ?>
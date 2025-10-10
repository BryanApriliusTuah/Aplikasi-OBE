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
										<p class="card-title fw-bold mb-1"><?= esc($jadwal['nama_mk']) ?> (<?= esc($jadwal['kelas']) ?>)</p>
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
									</div>
									<div class="card-footer bg-white p-2 border-top-0">
										<div class="d-flex gap-2 justify-content-end">
											<button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailNilaiModal" data-jadwal-id="<?= $jadwal['id'] ?>"><i class="bi bi-bar-chart-steps"></i> Detail Nilai</button>
											<button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#dpnaModal" data-jadwal-id="<?= $jadwal['id'] ?>"><i class="bi bi-file-earmark-text"></i> Lihat DPNA</button>

											<?php if (isset($jadwal['can_input_grades']) && $jadwal['can_input_grades']): ?>
												<a href="<?= base_url('admin/nilai/input-nilai/' . $jadwal['id']) ?>" class="btn btn-sm btn-primary"><i class="bi bi-pencil-square"></i> Input Nilai</a>
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

<div class="modal fade" id="detailNilaiModal" tabindex="-1" aria-labelledby="detailNilaiModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="detailNilaiModalLabel">Detail Nilai CPMK Mahasiswa</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="detailNilaiModalBody">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="dpnaModal" tabindex="-1" aria-labelledby="dpnaModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="dpnaModalLabel">Daftar Penilaian Nilai Akhir (DPNA)</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="dpnaModalBody">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		const spinner = `<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>`;

		// --- Detail Nilai CPMK Modal Logic ---
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
							modalBody.innerHTML = `<div class="alert alert-warning">Belum ada data CPMK atau mahasiswa untuk mata kuliah ini.</div>`;
							return;
						}

						let tableHtml = `<div class="table-responsive"><table class="table table-bordered table-striped table-hover small"><thead><tr><th>#</th><th>NIM</th><th>Nama Mahasiswa</th>`;
						data.cpmk_list.forEach(cpmk => {
							tableHtml += `<th class="text-center" title="${cpmk.deskripsi}">${cpmk.kode_cpmk}</th>`;
						});
						tableHtml += `</tr></thead><tbody>`;

						data.scores.forEach((mhs, index) => {
							tableHtml += `<tr><td>${index + 1}</td><td>${mhs.nim}</td><td>${mhs.nama_lengkap}</td>`;
							data.cpmk_list.forEach(cpmk => {
								const score = mhs.scores[cpmk.id] !== undefined ? mhs.scores[cpmk.id] : '-';
								tableHtml += `<td class="text-center">${score}</td>`;
							});
							tableHtml += `</tr>`;
						});

						tableHtml += `</tbody></table></div>`;
						modalBody.innerHTML = tableHtml;
					})
					.catch(error => {
						modalBody.innerHTML = '<div class="alert alert-danger">Gagal memuat detail nilai.</div>';
						console.error('Error:', error);
					});
			});
		}

		// --- DPNA Modal Logic ---
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
							modalBody.innerHTML = `<div class="alert alert-warning">Belum ada nilai akhir yang diinput untuk kelas ini.</div>`;
							return;
						}

						let header = `
                        <h6><strong>Mata Kuliah:</strong> ${data.jadwal.nama_mk}</h6>
                        <p class="mb-1 small"><strong>Dosen Pengampu:</strong> ${data.jadwal.dosen_ketua || 'N/A'}</p>
                        <p class="small"><strong>Tahun Akademik:</strong> ${data.jadwal.tahun_akademik}</p>
                        <hr>`;

						let tableHtml = header + `<table class="table table-bordered table-striped"><thead><tr>
                        <th>#</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th class="text-center">Nilai Akhir</th>
                        <th class="text-center">Nilai Huruf</th>
                        <th class="text-center">Status</th>
                        </tr></thead><tbody>`;

						data.nilai_mahasiswa.forEach((item, index) => {
							const statusClass = item.status_kelulusan === 'Lulus' ? 'success' : 'danger';
							tableHtml += `<tr>
                            <td>${index + 1}</td>
                            <td>${item.nim}</td>
                            <td>${item.nama_lengkap}</td>
                            <td class="text-center">${parseFloat(item.nilai_akhir).toFixed(2)}</td>
                            <td class="text-center fw-bold">${item.nilai_huruf}</td>
                            <td class="text-center"><span class="badge bg-${statusClass}">${item.status_kelulusan}</span></td>
                         </tr>`;
						});

						tableHtml += `</tbody></table>`;
						modalBody.innerHTML = tableHtml;
					})
					.catch(error => {
						modalBody.innerHTML = '<div class="alert alert-danger">Gagal memuat DPNA.</div>';
						console.error('Error:', error);
					});
			});
		}
	});
</script>
<?= $this->endSection() ?>
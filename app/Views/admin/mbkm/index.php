<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
	<h2 class="fw-bold my-4 text-center">Manajemen Kegiatan MBKM (Merdeka Belajar Kampus Merdeka)</h2>

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
					<h5 class="mb-0">Filter Kegiatan</h5>
				</div>
				<?php if (session()->get('role') === 'admin'): ?>
					<a href="<?= base_url('admin/mbkm/create') ?>" class="btn btn-primary">
						<i class="bi bi-plus-circle"></i> Tambah Kegiatan
					</a>
				<?php endif; ?>
			</div>
		</div>
		<div class="card-body">
			<form method="GET" action="<?= current_url() ?>">
				<div class="row g-3 align-items-end">
					<div class="col-md-3">
						<label for="filter_program_studi" class="form-label">Program Studi</label>
						<select class="form-select" id="filter_program_studi" name="program_studi">
							<option value="">Semua Program Studi</option>
							<option value="Teknik Informatika" <?= ($filters['program_studi'] ?? '') == 'Teknik Informatika' ? 'selected' : '' ?>>Teknik Informatika</option>
							<option value="Sistem Informasi" <?= ($filters['program_studi'] ?? '') == 'Sistem Informasi' ? 'selected' : '' ?>>Sistem Informasi</option>
							<option value="Teknik Komputer" <?= ($filters['program_studi'] ?? '') == 'Teknik Komputer' ? 'selected' : '' ?>>Teknik Komputer</option>
						</select>
					</div>
					<div class="col-md-3">
						<label for="filter_jenis" class="form-label">Jenis Kegiatan</label>
						<select class="form-select" id="filter_jenis" name="jenis_kegiatan">
							<option value="">Semua Jenis</option>
							<?php foreach ($jenis_kegiatan as $jk): ?>
								<option value="<?= esc($jk['nama_kegiatan']) ?>" <?= ($filters['jenis_kegiatan'] ?? '') == $jk['nama_kegiatan'] ? 'selected' : '' ?>>
									<?= esc($jk['nama_kegiatan']) ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-2">
						<label for="filter_status" class="form-label">Status</label>
						<select class="form-select" id="filter_status" name="status_kegiatan">
							<option value="">Semua Status</option>
							<option value="diajukan" <?= ($filters['status_kegiatan'] ?? '') == 'diajukan' ? 'selected' : '' ?>>Diajukan</option>
							<option value="disetujui" <?= ($filters['status_kegiatan'] ?? '') == 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
							<option value="ditolak" <?= ($filters['status_kegiatan'] ?? '') == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
							<option value="berlangsung" <?= ($filters['status_kegiatan'] ?? '') == 'berlangsung' ? 'selected' : '' ?>>Berlangsung</option>
							<option value="selesai" <?= ($filters['status_kegiatan'] ?? '') == 'selesai' ? 'selected' : '' ?>>Selesai</option>
						</select>
					</div>
					<div class="col-md-2">
						<label for="filter_tahun_akademik" class="form-label">Tahun Akademik</label>
						<input type="text" class="form-control" name="tahun_akademik" value="<?= esc($filters['tahun_akademik'] ?? '') ?>" placeholder="2025/2026">
					</div>
					<div class="col-md-2 d-flex gap-2">
						<button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Terapkan</button>
						<a href="<?= current_url() ?>" class="btn btn-outline-secondary"><i class="bi bi-arrow-clockwise"></i></a>
					</div>
				</div>
			</form>
		</div>
	</div>

	<?php
	$status_labels = [
		'diajukan' => ['label' => 'Diajukan', 'icon' => 'bi-hourglass-split', 'color' => 'warning'],
		'disetujui' => ['label' => 'Disetujui', 'icon' => 'bi-check-circle', 'color' => 'info'],
		'ditolak' => ['label' => 'Ditolak', 'icon' => 'bi-x-circle', 'color' => 'danger'],
		'berlangsung' => ['label' => 'Berlangsung', 'icon' => 'bi-play-circle', 'color' => 'primary'],
		'selesai' => ['label' => 'Selesai', 'icon' => 'bi-check-circle-fill', 'color' => 'success']
	];

	// Combine all kegiatan into one array with status info
	$all_kegiatan = [];
	foreach ($status_labels as $status_key => $status_info) {
		if (!empty($kegiatan_by_status[$status_key])) {
			foreach ($kegiatan_by_status[$status_key] as $kegiatan) {
				$kegiatan['status_info'] = $status_info;
				$kegiatan['status_key'] = $status_key;
				$all_kegiatan[] = $kegiatan;
			}
		}
	}
	?>

	<div class="modern-filter-wrapper mb-4">
		<div class="modern-filter-header">
			<div class="d-flex justify-content-between align-items-center">
				<div class="modern-filter-title">
					<i class="bi bi-list-check"></i> Daftar Kegiatan MBKM
				</div>
				<span class="badge bg-primary rounded-pill">
					Total: <?= count($all_kegiatan) ?> Kegiatan
				</span>
			</div>
		</div>

		<div class="modern-table-wrapper">
			<?php if (empty($all_kegiatan)): ?>
				<div class="text-center text-muted py-5">
					<i class="bi bi-inbox fs-1"></i>
					<p class="mt-2 small">Tidak ada kegiatan</p>
				</div>
			<?php else: ?>
				<table class="modern-table">
					<thead>
						<tr>
							<th style="min-width: 130px;" class="text-center">Status</th>
							<th style="min-width: 200px;" class="text-center">Judul Kegiatan</th>
							<th style="min-width: 150px;" class="text-center">Mahasiswa</th>
							<th style="min-width: 120px;" class="text-center">NIM</th>
							<th style="min-width: 150px;" class="text-center">Program Studi</th>
							<th style="min-width: 150px;" class="text-center">Jenis Kegiatan</th>
							<th style="min-width: 150px;" class="text-center">Tempat</th>
							<th style="min-width: 180px;" class="text-center">Periode</th>
							<th style="min-width: 100px;" class="text-center">Durasi</th>
							<th style="min-width: 150px;" class="text-center">Dosen Pembimbing</th>
							<th style="min-width: 120px;" class="text-center">Nilai</th>
							<th style="min-width: 250px;" class="text-center">Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($all_kegiatan as $kegiatan): ?>
							<tr>
								<td class="text-center">
									<span class="badge bg-secondary">
										<i class="bi <?= $kegiatan['status_info']['icon'] ?>"></i>
										<?= esc($kegiatan['status_info']['label']) ?>
									</span>
								</td>
								<td class="fw-bold"><?= esc($kegiatan['judul_kegiatan']) ?></td>
								<td>
									<?php
									$mahasiswa_list = $kegiatan['nama_mahasiswa_list'] ?? '-';
									if ($mahasiswa_list !== '-') {
										$mahasiswa_array = explode(',', $mahasiswa_list);
										foreach ($mahasiswa_array as $index => $mhs) {
											echo esc(trim($mhs));
											if ($index < count($mahasiswa_array) - 1) {
												echo '<br>';
											}
										}
									} else {
										echo '-';
									}
									?>
								</td>
								<td>
									<?php
									$nim_list = $kegiatan['nim_list'] ?? '-';
									if ($nim_list !== '-') {
										$nim_array = explode(',', $nim_list);
										foreach ($nim_array as $index => $nim) {
											echo esc(trim($nim));
											if ($index < count($nim_array) - 1) {
												echo '<br>';
											}
										}
									} else {
										echo '-';
									}
									?>
								</td>
								<td><?= esc($kegiatan['program_studi'] ?? '-') ?></td>
								<td><?= esc($kegiatan['jenis_kegiatan']) ?></td>
								<td><?= esc($kegiatan['tempat_kegiatan']) ?></td>
								<td>
									<?= date('d/m/Y', strtotime($kegiatan['tanggal_mulai'])) ?> -
									<?= date('d/m/Y', strtotime($kegiatan['tanggal_selesai'])) ?>
								</td>
								<td><?= $kegiatan['durasi_minggu'] ?> minggu</td>
								<td><?= esc($kegiatan['dosen_pembimbing'] ?? '-') ?></td>
								<td class="text-center">
									<?php if (!empty($kegiatan['nilai_huruf'])): ?>
										<span class="badge bg-secondary"><?= esc($kegiatan['nilai_huruf']) ?></span>
										<br>
										<span class="badge bg-secondary">
											<?= esc($kegiatan['status_kelulusan']) ?>
										</span>
									<?php else: ?>
										-
									<?php endif; ?>
								</td>
								<td>
									<div class="d-flex gap-2 justify-content-center flex-wrap">
										<button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailModal" data-kegiatan-id="<?= $kegiatan['id'] ?>">
											<i class="bi bi-eye"></i> Detail
										</button>

										<?php if (session()->get('role') === 'admin'): ?>
											<?php if ($kegiatan['status_key'] == 'disetujui' || $kegiatan['status_key'] == 'berlangsung' || $kegiatan['status_key'] == 'selesai'): ?>
												<a href="<?= base_url('admin/mbkm/input-nilai/' . $kegiatan['id']) ?>" class="btn btn-sm btn-outline-success">
													<i class="bi bi-pencil-square"></i> Input Nilai
												</a>
											<?php endif; ?>

											<a href="<?= base_url('admin/mbkm/edit/' . $kegiatan['id']) ?>" class="btn btn-sm btn-outline-warning">
												<i class="bi bi-pencil"></i> Edit
											</a>

											<button class="btn btn-sm btn-outline-danger" onclick="confirmDelete(<?= $kegiatan['id'] ?>)">
												<i class="bi bi-trash"></i> Hapus
											</button>
										<?php endif; ?>
									</div>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
	</div>
</div>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="detailModalLabel">Detail Kegiatan MBKM</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="detailModalBody">
				<div class="text-center p-5">
					<div class="spinner-border text-primary" role="status">
						<span class="visually-hidden">Loading...</span>
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
<script>
	document.addEventListener('DOMContentLoaded', function() {
		const detailModal = document.getElementById('detailModal');
		if (detailModal) {
			detailModal.addEventListener('show.bs.modal', function(event) {
				const button = event.relatedTarget;
				const kegiatanId = button.getAttribute('data-kegiatan-id');
				const modalBody = detailModal.querySelector('#detailModalBody');

				modalBody.innerHTML = `<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>`;

				fetch(`<?= base_url('admin/mbkm/detail-nilai/') ?>${kegiatanId}`, {
						headers: {
							'X-Requested-With': 'XMLHttpRequest'
						}
					})
					.then(response => response.json())
					.then(data => {
						const kegiatan = data.kegiatan;
						const capaian = data.capaian;

						let html = `
						<div class="row">
							<div class="col-md-6">
								<h6 class="fw-bold">Informasi Kegiatan</h6>
								<table class="table table-sm">
									<tr><th width="40%">Judul Kegiatan</th><td>${kegiatan.judul_kegiatan}</td></tr>
									<tr><th>Mahasiswa</th><td>${kegiatan.nama_mahasiswa} (${kegiatan.nim})</td></tr>
									<tr><th>Program Studi</th><td>${kegiatan.program_studi_kode || '-'}</td></tr>
									<tr><th>Jenis Kegiatan</th><td>${kegiatan.nama_kegiatan}</td></tr>
									<tr><th>Tempat Kegiatan</th><td>${kegiatan.tempat_kegiatan}</td></tr>
									<tr><th>Periode</th><td>${new Date(kegiatan.tanggal_mulai).toLocaleDateString('id-ID')} - ${new Date(kegiatan.tanggal_selesai).toLocaleDateString('id-ID')}</td></tr>
									<tr><th>Durasi</th><td>${kegiatan.durasi_minggu} minggu</td></tr>
									<tr><th>SKS Dikonversi</th><td>${kegiatan.sks_dikonversi} SKS</td></tr>
									<tr><th>Dosen Pembimbing</th><td>${kegiatan.nama_dosen_pembimbing || '-'}</td></tr>
									<tr><th>Pembimbing Lapangan</th><td>${kegiatan.pembimbing_lapangan || '-'}</td></tr>
									<tr><th>Status</th><td><span class="badge bg-primary">${kegiatan.status_kegiatan}</span></td></tr>
								</table>
							</div>
							<div class="col-md-6">
								<h6 class="fw-bold">Penilaian & Capaian</h6>
								${kegiatan.nilai_huruf ? `
									<div class="card bg-light mb-3">
										<div class="card-body text-center">
											<div class="row">
												<div class="col-4">
													<h6 class="text-muted mb-1">Nilai Angka</h6>
													<h3 class="text-primary mb-0">${kegiatan.nilai_angka}</h3>
												</div>
												<div class="col-4">
													<h6 class="text-muted mb-1">Nilai Huruf</h6>
													<h3 class="text-success mb-0">${kegiatan.nilai_huruf}</h3>
												</div>
												<div class="col-4">
													<h6 class="text-muted mb-1">Status</h6>
													<h3 class="mb-0 ${kegiatan.status_kelulusan === 'Lulus' ? 'text-success' : 'text-danger'}">${kegiatan.status_kelulusan}</h3>
												</div>
											</div>
										</div>
									</div>
									${capaian ? `
										<div class="alert alert-info">
											<strong>Capaian ${capaian.type}:</strong><br>
											<span class="badge bg-primary">${capaian.kode}</span><br>
											<small class="text-muted">${capaian.deskripsi}</small>
										</div>
									` : ''}
									${kegiatan.catatan_akhir ? `
										<div class="alert alert-secondary">
											<strong>Catatan:</strong><br>
											${kegiatan.catatan_akhir}
										</div>
									` : ''}
								` : '<div class="alert alert-warning">Belum ada penilaian</div>'}
							</div>
						</div>
						${kegiatan.deskripsi_kegiatan ? `
							<div class="mt-3">
								<h6 class="fw-bold">Deskripsi Kegiatan</h6>
								<p>${kegiatan.deskripsi_kegiatan}</p>
							</div>
						` : ''}
					`;

						modalBody.innerHTML = html;
					})
					.catch(error => {
						modalBody.innerHTML = '<div class="alert alert-danger">Gagal memuat detail kegiatan.</div>';
						console.error('Error:', error);
					});
			});
		}
	});

	function confirmDelete(id) {
		if (confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')) {
			window.location.href = `<?= base_url('admin/mbkm/delete/') ?>${id}`;
		}
	}
</script>
<?= $this->endSection() ?>
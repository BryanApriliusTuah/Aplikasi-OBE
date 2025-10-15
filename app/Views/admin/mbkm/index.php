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

	<div class="row g-3">
		<?php
		$status_labels = [
			'diajukan' => ['label' => 'Diajukan', 'icon' => 'bi-hourglass-split', 'color' => 'warning'],
			'disetujui' => ['label' => 'Disetujui', 'icon' => 'bi-check-circle', 'color' => 'info'],
			'ditolak' => ['label' => 'Ditolak', 'icon' => 'bi-x-circle', 'color' => 'danger'],
			'berlangsung' => ['label' => 'Berlangsung', 'icon' => 'bi-play-circle', 'color' => 'primary'],
			'selesai' => ['label' => 'Selesai', 'icon' => 'bi-check-circle-fill', 'color' => 'success']
		];
		?>

		<?php foreach ($status_labels as $status_key => $status_info): ?>
			<div class="col-12 col-md-6 col-xl-4">
				<div class="card shadow-sm h-100">
					<div class="card-header fw-bold text-center bg-<?= $status_info['color'] ?> text-white">
						<i class="bi <?= $status_info['icon'] ?>"></i> <?= esc($status_info['label']) ?>
						<span class="badge bg-white text-<?= $status_info['color'] ?> rounded-pill">
							<?= count($kegiatan_by_status[$status_key] ?? []) ?>
						</span>
					</div>
					<div class="card-body">
						<?php if (empty($kegiatan_by_status[$status_key])): ?>
							<div class="text-center text-muted pt-5">
								<i class="bi bi-inbox fs-1"></i>
								<p class="mt-2 small">Tidak ada kegiatan</p>
							</div>
						<?php else: ?>
							<?php foreach ($kegiatan_by_status[$status_key] as $kegiatan): ?>
								<div class="card mb-3 shadow-sm">
									<div class="card-body p-3">
										<p class="card-title fw-bold mb-1"><?= esc($kegiatan['judul_kegiatan']) ?></p>
										<div class="small text-muted mb-2">
											<i class="bi bi-people"></i> <?= esc($kegiatan['nama_mahasiswa_list'] ?? '-') ?>
										</div>
										<div class="small text-muted mb-2">
											<i class="bi bi-card-text"></i> NIM: <?= esc($kegiatan['nim_list'] ?? '-') ?>
										</div>
										<div class="small text-muted mb-2">
											<i class="bi bi-mortarboard"></i> <?= esc($kegiatan['program_studi'] ?? '-') ?>
										</div>
										<div class="small text-muted mb-2">
											<i class="bi bi-building"></i> <?= esc($kegiatan['tempat_kegiatan']) ?>
										</div>
										<div class="small text-muted mb-2">
											<i class="bi bi-tag"></i> <?= esc($kegiatan['jenis_kegiatan']) ?>
										</div>
										<div class="small text-muted mb-2">
											<i class="bi bi-calendar-range"></i>
											<?= date('d/m/Y', strtotime($kegiatan['tanggal_mulai'])) ?> -
											<?= date('d/m/Y', strtotime($kegiatan['tanggal_selesai'])) ?>
											(<?= $kegiatan['durasi_minggu'] ?> minggu)
										</div>
										<?php if (!empty($kegiatan['dosen_pembimbing'])): ?>
											<div class="small text-muted mb-2">
												<i class="bi bi-person-check"></i> <?= esc($kegiatan['dosen_pembimbing']) ?>
											</div>
										<?php endif; ?>
										<?php if (!empty($kegiatan['nilai_huruf'])): ?>
											<div class="mt-2">
												<span class="badge bg-success">Nilai: <?= esc($kegiatan['nilai_huruf']) ?> (<?= esc($kegiatan['nilai_angka']) ?>)</span>
												<span class="badge bg-<?= $kegiatan['status_kelulusan'] == 'Lulus' ? 'success' : 'danger' ?>">
													<?= esc($kegiatan['status_kelulusan']) ?>
												</span>
											</div>
										<?php endif; ?>
									</div>
									<div class="card-footer bg-white p-2 border-top-0">
										<div class="d-flex gap-2 justify-content-end flex-wrap">
											<button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#detailModal" data-kegiatan-id="<?= $kegiatan['id'] ?>">
												<i class="bi bi-eye"></i> Detail
											</button>

											<?php if (session()->get('role') === 'admin'): ?>
												<?php if ($status_key == 'disetujui' || $status_key == 'berlangsung' || $status_key == 'selesai'): ?>
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
						const komponen = data.komponen;

						let html = `
						<div class="row">
							<div class="col-md-6">
								<h6 class="fw-bold">Informasi Kegiatan</h6>
								<table class="table table-sm">
									<tr><th width="40%">Judul Kegiatan</th><td>${kegiatan.judul_kegiatan}</td></tr>
									<tr><th>Mahasiswa</th><td>${kegiatan.nama_mahasiswa} (${kegiatan.nim})</td></tr>
									<tr><th>Program Studi</th><td>${kegiatan.program_studi}</td></tr>
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
								<h6 class="fw-bold">Rincian Nilai</h6>
								${komponen.length > 0 ? `
									<table class="table table-sm table-bordered">
										<thead>
											<tr>
												<th>Komponen</th>
												<th class="text-center">Bobot</th>
												<th class="text-center">Nilai</th>
											</tr>
										</thead>
										<tbody>
											${komponen.map(k => `
												<tr>
													<td>${k.nama_komponen}</td>
													<td class="text-center">${k.bobot}%</td>
													<td class="text-center">${k.nilai}</td>
												</tr>
											`).join('')}
										</tbody>
									</table>
									${kegiatan.nilai_huruf ? `
										<div class="alert alert-success mt-3">
											<strong>Nilai Akhir:</strong> ${kegiatan.nilai_huruf} (${kegiatan.nilai_angka})
											<br><strong>Status:</strong> ${kegiatan.status_kelulusan}
										</div>
									` : '<div class="alert alert-warning mt-3">Nilai belum lengkap</div>'}
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
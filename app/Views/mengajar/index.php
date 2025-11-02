<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">

	<h2 class="fw-bold my-4 text-center">Jadwal Mengajar</h2>

	<?php if (session()->getFlashdata('success')): ?>
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			<?= esc(session()->getFlashdata('success')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>
	<?php if (session()->getFlashdata('error')): ?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<?php $e = session()->getFlashdata('error');
			echo is_array($e) ? implode('<br>', array_map('esc', $e)) : esc($e); ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>

	<div class="card shadow-sm mb-4">
		<div class="card-header bg-light p-3">
			<div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
				<div class="d-flex align-items-center gap-2">
					<i class="bi bi-funnel-fill fs-5 text-primary"></i>
					<h5 class="mb-0">Filter & Kontrol</h5>
				</div>
				<div class="d-flex gap-2">
					<?php if (session()->get('role') === 'admin'): ?>
						<a href="<?= base_url('admin/mengajar/create') ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Jadwal</a>
					<?php endif; ?>
					<div class="btn-group">
						<button type="button" class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
							<i class="bi bi-download"></i> Export
						</button>
						<ul class="dropdown-menu dropdown-menu-end">
							<li><a class="dropdown-item" href="<?= base_url('admin/mengajar/exportExcel') ?>"><i class="bi bi-file-earmark-excel me-2"></i>Excel</a></li>
							<li><a class="dropdown-item" href="<?= base_url('admin/mengajar/exportPdf') ?>"><i class="bi bi-file-earmark-pdf me-2"></i>PDF</a></li>
						</ul>
					</div>
				</div>
			</div>
		</div>
		<div class="card-body">
			<form method="GET" action="<?= current_url() ?>">
				<div class="row g-3 align-items-end">
					<div class="col-md-4">
						<label for="filter_program_studi" class="form-label">Program Studi</label>
						<select class="form-select" id="filter_program_studi" name="program_studi">
							<!-- <option value="">Semua Program Studi</option> -->
							<?php foreach ($program_studi_list as $prodi): ?>
								<option value="<?= esc($prodi) ?>" <?= (empty($filters['program_studi']) && $prodi == 'Teknik Informatika') || ($filters['program_studi'] ?? '') == $prodi ? 'selected' : '' ?>><?= esc($prodi) ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-4">
						<label for="filter_tahun_akademik" class="form-label">Tahun Akademik</label>
						<select class="form-select" id="filter_tahun_akademik" name="tahun_akademik">
							<option value="">Semua Tahun Akademik</option>
							<?php foreach ($tahun_akademik_list as $tahun): ?>
								<option value="<?= esc($tahun) ?>" <?= ($filters['tahun_akademik'] ?? '') == $tahun ? 'selected' : '' ?>><?= esc($tahun) ?></option>
							<?php endforeach; ?>
						</select>
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
			<div class="col-12 col-md-6 col-lg-4">
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
								<div class="card mb-2 shadow-sm">
									<div class="card-body p-2">
										<div class="d-flex justify-content-between">
											<p class="card-title small fw-bold mb-1"><?= esc($jadwal['nama_mk']) ?></p>
											<div class="dropdown">
												<button class="btn btn-sm p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-three-dots-vertical"></i></button>
												<ul class="dropdown-menu dropdown-menu-end">
													<li><button class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#detailModal" data-id="<?= $jadwal['id'] ?>"><i class="bi bi-eye me-2"></i>Lihat Detail</button></li>
													<?php if (session()->get('role') === 'admin'): ?>
														<li><a class="dropdown-item" href="<?= base_url('admin/mengajar/edit/' . (int)$jadwal['id']) ?>"><i class="bi bi-pencil-square me-2"></i>Edit</a></li>
														<li>
															<hr class="dropdown-divider">
														</li>
														<li><button class="dropdown-item text-danger" type="button" data-bs-toggle="modal" data-bs-target="#deleteModal" data-url="<?= base_url('admin/mengajar/delete/' . (int)$jadwal['id']) ?>" data-nama="<?= esc($jadwal['nama_mk']) ?>"><i class="bi bi-trash me-2"></i>Hapus</button></li>
													<?php endif; ?>
												</ul>
											</div>
										</div>
										<div class="small text-muted mb-2">
											<i class="bi bi-clock"></i>
											<?= !empty($jadwal['jam_mulai']) ? date('H:i', strtotime($jadwal['jam_mulai'])) . ' - ' . date('H:i', strtotime($jadwal['jam_selesai'])) : 'Waktu belum diatur' ?>
											<br>
											<i class="bi bi-geo-alt"></i>
											<?= !empty($jadwal['ruang']) ? esc($jadwal['ruang']) : 'Ruang belum diatur' ?>
										</div>
										<?php if (!empty($jadwal['dosen_list'])): ?>
											<?php
											// Find the lecturer with the 'leader' role
											$leader = null;
											foreach ($jadwal['dosen_list'] as $dosen) {
												if ($dosen['role'] === 'leader') {
													$leader = $dosen;
													break;
												}
											}
											// If no leader is found, default to the first lecturer
											if (!$leader) {
												$leader = $jadwal['dosen_list'][0];
											}
											?>
											<span class="badge bg-primary fw-normal"><i class="bi bi-star-fill me-1"></i> <?= esc($leader['nama_lengkap']) ?></span>
											<?php if (count($jadwal['dosen_list']) > 1): ?>
												<span class="badge bg-secondary fw-normal">+<?= count($jadwal['dosen_list']) - 1 ?> lainnya</span>
											<?php endif; ?>
										<?php endif; ?>
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

<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="detailModalLabel">Detail Jadwal Mengajar</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body" id="detailModalBody">
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Konfirmasi Penghapusan</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p>Apakah Anda yakin ingin menghapus jadwal untuk:</p><strong id="deleteItemName" class="text-danger"></strong>
				<p class="mt-2 text-muted small">Tindakan ini tidak dapat dibatalkan.</p>
			</div>
			<div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
				<form id="deleteForm" action="" method="post" class="d-inline"><?= csrf_field() ?><input type="hidden" name="_method" value="DELETE"><button type="submit" class="btn btn-danger">Ya, Hapus</button></form>
			</div>
		</div>
	</div>
</div>

<?= $this->section('js') ?>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		// --- Detail Modal Logic ---
		const detailModal = document.getElementById('detailModal');
		if (detailModal) {
			detailModal.addEventListener('show.bs.modal', function(event) {
				const button = event.relatedTarget;
				const jadwalId = button.getAttribute('data-id');
				const modalBody = detailModal.querySelector('#detailModalBody');

				// Show a loading state
				modalBody.innerHTML = `<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>`;

				// Fetch the details
				fetch(`<?= base_url('admin/mengajar/show/') ?>${jadwalId}`, {
						headers: {
							'X-Requested-With': 'XMLHttpRequest'
						}
					})
					.then(response => {
						if (!response.ok) {
							throw new Error('Network response was not ok');
						}
						return response.json();
					})
					.then(data => {
						const jadwal = data.jadwal;
						const jamMulai = jadwal.jam_mulai ? new Date('1970-01-01T' + jadwal.jam_mulai).toLocaleTimeString('id-ID', {
							hour: '2-digit',
							minute: '2-digit'
						}) : '';
						const jamSelesai = jadwal.jam_selesai ? new Date('1970-01-01T' + jadwal.jam_selesai).toLocaleTimeString('id-ID', {
							hour: '2-digit',
							minute: '2-digit'
						}) : '';
						const waktuText = [jadwal.hari, (jamMulai && jamSelesai ? `${jamMulai} - ${jamSelesai}` : '')].filter(Boolean).join(', ') || '<span class="text-muted">Belum diatur</span>';

						let dosenHtml = '<p class="text-muted">Belum ada dosen yang ditugaskan.</p>';
						if (jadwal.dosen_list && jadwal.dosen_list.length > 0) {
							// Sort the array to place the 'leader' first
							jadwal.dosen_list.sort((a, b) => {
								if (a.role === 'leader') return -1;
								if (b.role === 'leader') return 1;
								return 0;
							});

							dosenHtml = '<ul class="list-group list-group-flush">';
							jadwal.dosen_list.forEach(dosen => {
								dosenHtml += `<li class="list-group-item d-flex align-items-center ps-0">
                            ${dosen.role === 'leader' ? '<i class="bi bi-star-fill text-primary me-2"></i><span class="fw-bold">' + dosen.nama_lengkap + ' (Ketua)</span>' : '<i class="bi bi-person-fill text-muted me-2"></i><span>' + dosen.nama_lengkap + ' (Anggota)</span>'}
                        </li>`;
							});
							dosenHtml += '</ul>';
						}

						// Populate the modal with the fetched data
						modalBody.innerHTML = `
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6><i class="bi bi-book-fill text-primary"></i> Detail Mata Kuliah</h6><hr class="mt-2">
                            <dl class="row"><dt class="col-sm-5">Kode MK</dt><dd class="col-sm-7"><span class="badge bg-primary">${jadwal.kode_mk}</span></dd><dt class="col-sm-5">Nama Mata Kuliah</dt><dd class="col-sm-7">${jadwal.nama_mk}</dd><dt class="col-sm-5">Semester</dt><dd class="col-sm-7">${jadwal.semester}</dd><dt class="col-sm-5">Jumlah SKS</dt><dd class="col-sm-7">${jadwal.sks}</dd></dl>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="bi bi-calendar-week-fill text-primary"></i> Detail Jadwal</h6><hr class="mt-2">
                            <dl class="row"><dt class="col-sm-5">Program Studi</dt><dd class="col-sm-7">${jadwal.program_studi}</dd><dt class="col-sm-5">Tahun Akademik</dt><dd class="col-sm-7">${jadwal.tahun_akademik}</dd><dt class="col-sm-5">Kelas</dt><dd class="col-sm-7"><span class="badge bg-secondary">${jadwal.kelas}</span></dd><dt class="col-sm-5">Hari & Waktu</dt><dd class="col-sm-7">${waktuText}</dd><dt class="col-sm-5">Ruang</dt><dd class="col-sm-7">${jadwal.ruang || '<span class="text-muted">Belum diatur</span>'}</dd></dl>
                        </div>
                    </div>
                    <h6 class="mt-4"><i class="bi bi-people-fill text-primary"></i> Dosen Pengampu</h6><hr class="mt-2">
                    ${dosenHtml}
                `;
					})
					.catch(error => {
						modalBody.innerHTML = '<div class="alert alert-danger">Gagal memuat detail. Silakan coba lagi.</div>';
						console.error('Error fetching details:', error);
					});
			});
		}

		// --- Delete Modal Logic (Unchanged) ---
		const deleteModal = document.getElementById('deleteModal');
		if (deleteModal) {
			deleteModal.addEventListener('show.bs.modal', function(event) {
				const button = event.relatedTarget;
				const url = button.getAttribute('data-url');
				const nama = button.getAttribute('data-nama');
				const form = deleteModal.querySelector('#deleteForm');
				const nameElement = deleteModal.querySelector('#deleteItemName');
				form.action = url;
				nameElement.textContent = nama;
			});
		}
	});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
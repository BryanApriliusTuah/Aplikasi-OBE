<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table CSS -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

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

	<!-- Action Buttons -->
	<div class="d-flex justify-content-end gap-2 mb-3">
		<?php if (session()->get('role') === 'admin'): ?>
			<a href="<?= base_url('admin/mengajar/syncFromApi') ?>" class="btn btn-info text-white"
				onclick="return confirm('Sinkronisasi jadwal dari API? Hanya mata kuliah yang sudah memiliki RPS yang akan disinkronkan.');">
				<i class="bi bi-cloud-arrow-down"></i> Sync dari API
			</a>
			<a href="<?= base_url('admin/mengajar/create') ?>" class="btn btn-primary">
				<i class="bi bi-plus-lg"></i> Tambah Jadwal
			</a>
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

	<!-- Modern Filter Component -->
	<div class="modern-filter-wrapper mb-4">
		<div class="modern-filter-header">
			<div class="d-flex align-items-center gap-2">
				<i class="bi bi-funnel-fill text-primary"></i>
				<span class="modern-filter-title">Filter Jadwal</span>
			</div>
		</div>
		<div class="modern-filter-body">
			<form method="GET" action="<?= current_url() ?>">
				<div class="row g-3 align-items-end">
					<div class="col-md-5">
						<label for="filter_program_studi" class="modern-filter-label">
							<i class="bi bi-mortarboard-fill me-1"></i>
							Program Studi
						</label>
						<select class="form-select modern-filter-input" id="filter_program_studi" name="program_studi_kode">
							<option value="">Semua Program Studi</option>
							<?php foreach ($program_studi_list as $prodi): ?>
								<option value="<?= esc($prodi['kode']) ?>" <?= ($filters['program_studi_kode'] ?? '') == $prodi['kode'] ? 'selected' : '' ?>>
									<?= esc($prodi['nama_resmi']) ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-5">
						<label for="filter_tahun_akademik" class="modern-filter-label">
							<i class="bi bi-calendar-event me-1"></i>
							Tahun Akademik
						</label>
						<select class="form-select modern-filter-input" id="filter_tahun_akademik" name="tahun_akademik">
							<option value="">Semua Tahun Akademik</option>
							<?php foreach ($tahun_akademik_list as $tahun): ?>
								<option value="<?= esc($tahun) ?>" <?= ($filters['tahun_akademik'] ?? '') == $tahun ? 'selected' : '' ?>>
									<?= esc($tahun) ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<div class="col-md-2 d-flex gap-2">
						<button type="submit" class="btn btn-primary modern-filter-btn flex-fill">
							<i class="bi bi-search"></i> Terapkan
						</button>
						<a href="<?= current_url() ?>"
							class="btn btn-outline-secondary modern-filter-btn-reset"
							data-bs-toggle="tooltip"
							title="Reset Filter">
							<i class="bi bi-arrow-clockwise text-secondary"></i>
						</a>
					</div>
				</div>
			</form>
		</div>
	</div>

	<!-- Schedule Cards Grid -->
	<div class="row g-3">
		<?php foreach ($jadwal_by_day as $day => $schedules): ?>
			<div class="col-12 col-md-6 col-lg-4">
				<div class="card border-0 h-100" style="border: 1px solid var(--modern-table-border) !important; box-shadow: var(--modern-table-shadow-sm);">
					<div class="card-header fw-bold text-center" style="background: linear-gradient(to bottom, var(--modern-table-header-bg-start), var(--modern-table-header-bg-end)); border-bottom: 2px solid var(--modern-table-border-header); color: var(--modern-table-header-text); font-size: 0.875rem; text-transform: uppercase; letter-spacing: 0.025em; padding: 0.875rem;">
						<i class="bi bi-calendar-day me-1"></i>
						<?= esc($day) ?>
						<span class="badge bg-secondary rounded-pill ms-2"><?= count($schedules) ?></span>
					</div>
					<div class="card-body" style="background: white;">
						<?php if (empty($schedules)): ?>
							<div class="text-center text-muted pt-5 pb-3">
								<i class="bi bi-moon-stars fs-1" style="opacity: 0.3;"></i>
								<p class="mt-3 small mb-0" style="color: var(--modern-table-header-text);">Tidak ada jadwal</p>
							</div>
						<?php else: ?>
							<?php foreach ($schedules as $jadwal): ?>
								<div class="card mb-2 border-0" style="border: 1px solid var(--modern-table-border-light) !important; transition: all 0.2s ease; background: white;">
									<div class="card-body p-3" style="font-size: 0.875rem;">
										<div class="d-flex justify-content-between align-items-start mb-2">
											<h6 class="card-title mb-0 fw-semibold" style="color: var(--modern-table-text); font-size: 0.9375rem; line-height: 1.4;">
												<?= esc($jadwal['nama_mk']) ?>
											</h6>
											<div class="dropdown">
												<button class="btn btn-sm p-0 ms-2"
													type="button"
													data-bs-toggle="dropdown"
													aria-expanded="false"
													style="color: var(--modern-table-header-text); width: 28px; height: 28px; border-radius: 0.25rem; transition: all 0.2s ease;"
													onmouseover="this.style.backgroundColor='#f1f5f9'"
													onmouseout="this.style.backgroundColor='transparent'">
													<i class="bi bi-three-dots-vertical"></i>
												</button>
												<ul class="dropdown-menu dropdown-menu-end"
													style="border: 1px solid var(--modern-table-border); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); border-radius: 0.375rem; padding: 0.5rem; min-width: 180px;">
													<li>
														<button class="dropdown-item"
															type="button"
															data-bs-toggle="modal"
															data-bs-target="#detailModal"
															data-id="<?= $jadwal['id'] ?>"
															style="border-radius: 0.25rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; transition: all 0.2s ease;">
															<i class="bi bi-eye me-2 text-primary"></i>Lihat Detail
														</button>
													</li>
													<?php if (session()->get('role') === 'admin'): ?>
														<li>
															<a class="dropdown-item"
																href="<?= base_url('admin/mengajar/edit/' . (int)$jadwal['id']) ?>"
																style="border-radius: 0.25rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; transition: all 0.2s ease;">
																<i class="bi bi-pencil-square me-2 text-success"></i>Edit
															</a>
														</li>
														<li>
															<hr class="dropdown-divider" style="margin: 0.5rem 0;">
														</li>
														<li>
															<button class="dropdown-item text-danger"
																type="button"
																data-bs-toggle="modal"
																data-bs-target="#deleteModal"
																data-url="<?= base_url('admin/mengajar/delete/' . (int)$jadwal['id']) ?>"
																data-nama="<?= esc($jadwal['nama_mk']) ?>"
																style="border-radius: 0.25rem; padding: 0.5rem 0.75rem; font-size: 0.875rem; transition: all 0.2s ease;">
																<i class="bi bi-trash me-2"></i>Hapus
															</button>
														</li>
													<?php endif; ?>
												</ul>
											</div>
										</div>
										<div class="small mb-3" style="color: var(--modern-table-header-text); line-height: 1.6;">
											<div class="mb-1">
												<i class="bi bi-clock me-1"></i>
												<?= !empty($jadwal['jam_mulai']) ? date('H:i', strtotime($jadwal['jam_mulai'])) . ' - ' . date('H:i', strtotime($jadwal['jam_selesai'])) : 'Waktu belum diatur' ?>
											</div>
											<div class="mb-1">
												<i class="bi bi-geo-alt me-1"></i>
												<?= !empty($jadwal['ruang']) ? esc($jadwal['ruang']) : 'Ruang belum diatur' ?>
											</div>
											<?php if (!empty($jadwal['kelas_jenis'])): ?>
												<div class="mb-1">
													<i class="bi bi-diagram-3 me-1"></i>
													<?= esc($jadwal['kelas_jenis']) ?>
													<?php if (!empty($jadwal['kelas'])): ?>
														<span class="badge bg-secondary ms-1" style="font-size: 0.7rem;"><?= esc($jadwal['kelas']) ?></span>
													<?php endif; ?>
												</div>
											<?php endif; ?>
											<?php if (!empty($jadwal['total_mahasiswa'])): ?>
												<div>
													<i class="bi bi-people me-1"></i>
													<?= (int) $jadwal['total_mahasiswa'] ?> mahasiswa
												</div>
											<?php endif; ?>
										</div>
										<?php if (!empty($jadwal['dosen_list'])): ?>
											<div class="d-flex flex-wrap gap-1">
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
												<span class="badge bg-primary fw-normal" style="font-size: 0.75rem;">
													<i class="bi bi-star-fill me-1"></i> <?= esc($leader['nama_lengkap']) ?>
												</span>
												<?php if (count($jadwal['dosen_list']) > 1): ?>
													<span class="badge bg-secondary fw-normal" style="font-size: 0.75rem;">
														+<?= count($jadwal['dosen_list']) - 1 ?> lainnya
													</span>
												<?php endif; ?>
											</div>
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

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg modal-dialog-centered">
		<div class="modal-content border-0" style="box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); border-radius: 0.5rem;">
			<div class="modal-header" style="background: linear-gradient(to bottom, var(--modern-table-header-bg-start), var(--modern-table-header-bg-end)); border-bottom: 2px solid var(--modern-table-border-header); border-radius: 0.5rem 0.5rem 0 0;">
				<h5 class="modal-title fw-semibold" id="detailModalLabel" style="color: var(--modern-table-header-text); font-size: 1rem; text-transform: uppercase; letter-spacing: 0.025em;">
					<i class="bi bi-info-circle-fill me-2"></i>Detail Jadwal Mengajar
				</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body p-4" id="detailModalBody" style="background: white;">
				<!-- Content will be loaded here dynamically -->
			</div>
			<div class="modal-footer border-top" style="background: #f8f9fa;">
				<button type="button" class="btn btn-secondary modern-filter-btn" data-bs-dismiss="modal">
					<i class="bi bi-x-circle me-1"></i>Tutup
				</button>
			</div>
		</div>
	</div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content border-0" style="box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1); border-radius: 0.5rem;">
			<div class="modal-header" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); border-bottom: none; border-radius: 0.5rem 0.5rem 0 0;">
				<h5 class="modal-title fw-semibold text-white" style="font-size: 1rem; text-transform: uppercase; letter-spacing: 0.025em;">
					<i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Penghapusan
				</h5>
				<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body p-4" style="background: white;">
				<p class="mb-2" style="color: var(--modern-table-text);">Apakah Anda yakin ingin menghapus jadwal untuk:</p>
				<div class="alert alert-danger d-flex align-items-center mb-3" role="alert" style="border-left: 4px solid #dc3545;">
					<i class="bi bi-exclamation-circle-fill me-2"></i>
					<strong id="deleteItemName"></strong>
				</div>
				<p class="text-muted small mb-0">
					<i class="bi bi-info-circle me-1"></i>
					Tindakan ini tidak dapat dibatalkan.
				</p>
			</div>
			<div class="modal-footer border-top" style="background: #f8f9fa;">
				<button type="button" class="btn btn-secondary modern-filter-btn" data-bs-dismiss="modal">
					<i class="bi bi-x-circle me-1"></i>Batal
				</button>
				<form id="deleteForm" action="" method="post" class="d-inline">
					<?= csrf_field() ?>
					<input type="hidden" name="_method" value="DELETE">
					<button type="submit" class="btn btn-danger modern-filter-btn">
						<i class="bi bi-trash-fill me-1"></i>Ya, Hapus
					</button>
				</form>
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

				// Show a modern loading state
				modalBody.innerHTML = `
					<div class="text-center p-5">
						<div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
							<span class="visually-hidden">Loading...</span>
						</div>
						<p class="mt-3 text-muted">Memuat data...</p>
					</div>`;

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
						const waktuText = [jadwal.hari, (jamMulai && jamSelesai ? `${jamMulai} - ${jamSelesai}` : '')].filter(Boolean).join(', ') || '<span class="text-muted small">Belum diatur</span>';

						let dosenHtml = `
							<div class="alert alert-light border" style="border-left: 4px solid #e5e7eb !important;">
								<i class="bi bi-info-circle me-2"></i>
								<span class="text-muted">Belum ada dosen yang ditugaskan.</span>
							</div>`;

						if (jadwal.dosen_list && jadwal.dosen_list.length > 0) {
							// Sort the array to place the 'leader' first
							jadwal.dosen_list.sort((a, b) => {
								if (a.role === 'leader') return -1;
								if (b.role === 'leader') return 1;
								return 0;
							});

							dosenHtml = '<div class="row g-2">';
							jadwal.dosen_list.forEach((dosen, index) => {
								const isLeader = dosen.role === 'leader';
								dosenHtml += `
									<div class="col-md-6">
										<div class="card border" style="border: 1px solid ${isLeader ? '#0d6efd' : 'var(--modern-table-border)'} !important; border-left: 4px solid ${isLeader ? '#0d6efd' : '#6c757d'} !important; transition: all 0.2s ease;">
											<div class="card-body p-3">
												<div class="d-flex align-items-center">
													<div class="flex-shrink-0">
														${isLeader ?
															'<div class="rounded-circle bg-primary bg-opacity-10 p-2"><i class="bi bi-star-fill text-primary fs-5"></i></div>' :
															'<div class="rounded-circle bg-secondary bg-opacity-10 p-2"><i class="bi bi-person-fill text-secondary fs-5"></i></div>'}
													</div>
													<div class="flex-grow-1 ms-3">
														<h6 class="mb-0" style="font-size: 0.9375rem; color: var(--modern-table-text);">${dosen.nama_lengkap}</h6>
														<small class="text-muted">
															<i class="bi bi-shield-check me-1"></i>${isLeader ? 'Dosen Koordinator' : 'Dosen Pengampu'}
														</small>
													</div>
												</div>
											</div>
										</div>
									</div>`;
							});
							dosenHtml += '</div>';
						}

						// Populate the modal with the fetched data using modern design
						modalBody.innerHTML = `
							<div class="row g-4">
								<!-- Mata Kuliah Section -->
								<div class="col-md-6">
									<div class="modern-detail-section">
										<h6 class="modern-detail-header">
											<i class="bi bi-book-fill text-primary me-2"></i>Detail Mata Kuliah
										</h6>
										<div class="modern-detail-body">
											<div class="modern-detail-item">
												<span class="modern-detail-label">Kode MK</span>
												<span class="modern-detail-value">
													<span class="badge bg-primary" style="font-size: 0.8125rem;">${jadwal.kode_mk}</span>
												</span>
											</div>
											<div class="modern-detail-item">
												<span class="modern-detail-label">Nama Mata Kuliah</span>
												<span class="modern-detail-value fw-semibold">${jadwal.nama_mk}</span>
											</div>
											<div class="modern-detail-item">
												<span class="modern-detail-label">Semester</span>
												<span class="modern-detail-value">${jadwal.semester}</span>
											</div>
											<div class="modern-detail-item">
												<span class="modern-detail-label">Jumlah SKS</span>
												<span class="modern-detail-value">
													<span class="badge bg-primary" style="font-size: 0.8125rem;">${jadwal.sks} SKS</span>
												</span>
											</div>
										</div>
									</div>
								</div>

								<!-- Jadwal Section -->
								<div class="col-md-6">
									<div class="modern-detail-section">
										<h6 class="modern-detail-header">
											<i class="bi bi-calendar-week-fill text-primary me-2"></i>Detail Jadwal
										</h6>
										<div class="modern-detail-body">
											<div class="modern-detail-item">
												<span class="modern-detail-label">Program Studi</span>
												<span class="modern-detail-value">${jadwal.program_studi}</span>
											</div>
											<div class="modern-detail-item">
												<span class="modern-detail-label">Tahun Akademik</span>
												<span class="modern-detail-value">${jadwal.tahun_akademik}</span>
											</div>
											<div class="modern-detail-item">
												<span class="modern-detail-label">Kelas</span>
												<span class="modern-detail-value">
													<span class="badge bg-secondary" style="font-size: 0.8125rem;">${jadwal.kelas}</span>
												</span>
											</div>
											${jadwal.kelas_jenis ? `<div class="modern-detail-item">
												<span class="modern-detail-label">Jenis Kelas</span>
												<span class="modern-detail-value">${jadwal.kelas_jenis}</span>
											</div>` : ''}
											${jadwal.kelas_status ? `<div class="modern-detail-item">
												<span class="modern-detail-label">Status Kelas</span>
												<span class="modern-detail-value">
													<span class="badge ${jadwal.kelas_status === 'Aktif' ? 'bg-success' : 'bg-secondary'}" style="font-size: 0.8125rem;">${jadwal.kelas_status}</span>
												</span>
											</div>` : ''}
											${jadwal.total_mahasiswa ? `<div class="modern-detail-item">
												<span class="modern-detail-label">Total Mahasiswa</span>
												<span class="modern-detail-value">
													<span class="badge bg-info" style="font-size: 0.8125rem;">${jadwal.total_mahasiswa} mahasiswa</span>
												</span>
											</div>` : ''}
											<div class="modern-detail-item">
												<span class="modern-detail-label">Hari & Waktu</span>
												<span class="modern-detail-value">
													<i class="bi bi-clock me-1 text-primary"></i>${waktuText}
												</span>
											</div>
											<div class="modern-detail-item">
												<span class="modern-detail-label">Ruang</span>
												<span class="modern-detail-value">
													<i class="bi bi-geo-alt me-1 text-primary"></i>${jadwal.ruang || '<span class="text-muted small">Belum diatur</span>'}
												</span>
											</div>
										</div>
									</div>
								</div>
							</div>

							<!-- Dosen Pengampu Section -->
							<div class="modern-detail-section mt-4">
								<h6 class="modern-detail-header">
									<i class="bi bi-people-fill text-primary me-2"></i>Dosen Pengampu
								</h6>
								<div class="modern-detail-body">
									${dosenHtml}
								</div>
							</div>

							<style>
								.modern-detail-section {
									background: linear-gradient(to bottom, #fafbfc, #ffffff);
									border: 1px solid var(--modern-table-border-light);
									border-radius: 0.375rem;
									overflow: hidden;
								}
								.modern-detail-header {
									background: linear-gradient(to bottom, var(--modern-table-header-bg-start), var(--modern-table-header-bg-end));
									color: var(--modern-table-header-text);
									font-size: 0.875rem;
									font-weight: 600;
									text-transform: uppercase;
									letter-spacing: 0.025em;
									padding: 0.75rem 1rem;
									margin: 0;
									border-bottom: 2px solid var(--modern-table-border-header);
								}
								.modern-detail-body {
									padding: 1rem;
								}
								.modern-detail-item {
									display: flex;
									justify-content: space-between;
									align-items: center;
									padding: 0.625rem 0;
									border-bottom: 1px solid var(--modern-table-border-light);
									font-size: 0.875rem;
								}
								.modern-detail-item:last-child {
									border-bottom: none;
									padding-bottom: 0;
								}
								.modern-detail-item:first-child {
									padding-top: 0;
								}
								.modern-detail-label {
									color: var(--modern-table-header-text);
									font-weight: 500;
									flex: 0 0 40%;
								}
								.modern-detail-value {
									color: var(--modern-table-text);
									text-align: right;
									flex: 1;
								}
							</style>
						`;
					})
					.catch(error => {
						modalBody.innerHTML = `
							<div class="alert alert-danger border-0" style="border-left: 4px solid #dc3545 !important;">
								<div class="d-flex align-items-center">
									<i class="bi bi-exclamation-triangle-fill me-3 fs-4"></i>
									<div>
										<h6 class="mb-1">Gagal Memuat Data</h6>
										<p class="mb-0 small">Terjadi kesalahan saat memuat detail jadwal. Silakan coba lagi.</p>
									</div>
								</div>
							</div>`;
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
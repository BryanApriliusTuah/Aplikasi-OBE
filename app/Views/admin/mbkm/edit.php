<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
	<div class="d-flex justify-content-between align-items-center my-4">
		<h2 class="fw-bold">Edit Kegiatan MBKM</h2>
		<a href="<?= base_url('admin/mbkm') ?>" class="btn btn-outline-secondary">
			<i class="bi bi-arrow-left"></i> Kembali
		</a>
	</div>

	<?php if (session()->getFlashdata('errors')): ?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<strong>Terdapat kesalahan:</strong>
			<ul class="mb-0">
				<?php foreach (session()->getFlashdata('errors') as $error): ?>
					<li><?= esc($error) ?></li>
				<?php endforeach; ?>
			</ul>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>

	<?php if (session()->getFlashdata('error')): ?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<?= esc(session()->getFlashdata('error')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>

	<div class="card shadow-sm">
		<div class="card-body">
			<form action="<?= base_url('admin/mbkm/update/' . $kegiatan['id']) ?>" method="POST" enctype="multipart/form-data">
				<?= csrf_field() ?>

				<div class="row">
					<!-- Left Column -->
					<div class="col-md-6">
						<h5 class="mb-3 text-primary"><i class="bi bi-person-circle"></i> Informasi Mahasiswa</h5>

						<div class="mb-3">
							<label class="form-label">Mahasiswa <span class="text-danger">*</span></label>
							<small class="d-block text-muted mb-2">Pilih satu atau lebih mahasiswa yang terlibat dalam kegiatan ini</small>

							<?php
							$selected_ids = array_column($kegiatan_mahasiswa, 'mahasiswa_id');
							?>

							<div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
								<?php foreach ($mahasiswa as $mhs): ?>
									<div class="form-check mb-2">
										<input class="form-check-input mahasiswa-checkbox"
											type="checkbox"
											name="mahasiswa_ids[]"
											value="<?= $mhs['id'] ?>"
											id="mhs_<?= $mhs['id'] ?>"
											<?= in_array($mhs['id'], $selected_ids) ? 'checked' : '' ?>>
										<label class="form-check-label" for="mhs_<?= $mhs['id'] ?>">
											<strong><?= esc($mhs['nama_lengkap']) ?></strong><br>
											<small class="text-muted"><?= esc($mhs['nim']) ?> - <?= esc($mhs['program_studi_kode'] ?? '-') ?></small>
										</label>
									</div>
								<?php endforeach; ?>
							</div>
							<small class="text-muted">
								<span id="selected-count"><?= count($selected_ids) ?></span> mahasiswa dipilih
							</small>
						</div>

						<div class="alert alert-info" id="mahasiswa-alert" style="display:<?= count($selected_ids) > 0 ? 'block' : 'none' ?>;">
							<i class="bi bi-info-circle"></i> <strong>Mahasiswa yang dipilih:</strong>
							<ul id="selected-mahasiswa-list" class="mb-0 mt-2">
								<?php foreach ($kegiatan_mahasiswa as $km): ?>
									<li><?= esc($km['nama_lengkap']) ?></li>
								<?php endforeach; ?>
							</ul>
						</div>

						<h5 class="mb-3 mt-4 text-primary"><i class="bi bi-bookmark"></i> Detail Kegiatan</h5>

						<div class="mb-3">
							<label for="jenis_kegiatan" class="form-label">Jenis Kegiatan <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="jenis_kegiatan" name="jenis_kegiatan"
								value="<?= esc($kegiatan['jenis_kegiatan'] ?? '') ?>" required placeholder="Contoh: Magang, Pertukaran Mahasiswa, Studi Independen, dll.">
						</div>

						<div class="mb-3">
							<label for="judul_kegiatan" class="form-label">Judul Kegiatan <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="judul_kegiatan" name="judul_kegiatan"
								value="<?= esc($kegiatan['judul_kegiatan']) ?>" required placeholder="Contoh: Magang di PT. XYZ sebagai Software Developer">
						</div>

						<div class="mb-3">
							<label for="tempat_kegiatan" class="form-label">Tempat Kegiatan <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="tempat_kegiatan" name="tempat_kegiatan"
								value="<?= esc($kegiatan['tempat_kegiatan']) ?>" required placeholder="Contoh: PT. XYZ, Jakarta">
						</div>

						<div class="row">
							<div class="col-md-6 mb-3">
								<label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
								<input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai"
									value="<?= esc($kegiatan['tanggal_mulai']) ?>" required>
							</div>
							<div class="col-md-6 mb-3">
								<label for="tanggal_selesai" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
								<input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai"
									value="<?= esc($kegiatan['tanggal_selesai']) ?>" required>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6 mb-3">
								<label for="sks_dikonversi" class="form-label">SKS Dikonversi <span class="text-danger">*</span></label>
								<input type="number" class="form-control" id="sks_dikonversi" name="sks_dikonversi"
									value="<?= esc($kegiatan['sks_dikonversi']) ?>" required min="1" max="20">
							</div>
							<div class="col-md-6 mb-3">
								<label for="tahun_akademik" class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
								<input type="text" class="form-control" id="tahun_akademik" name="tahun_akademik"
									value="<?= esc($kegiatan['tahun_akademik']) ?>" required placeholder="2025/2026">
							</div>
						</div>

						<div class="mb-3">
							<label for="status_kegiatan" class="form-label">Status Kegiatan <span class="text-danger">*</span></label>
							<select class="form-select" id="status_kegiatan" name="status_kegiatan" required>
								<option value="diajukan" <?= $kegiatan['status_kegiatan'] == 'diajukan' ? 'selected' : '' ?>>Diajukan</option>
								<option value="disetujui" <?= $kegiatan['status_kegiatan'] == 'disetujui' ? 'selected' : '' ?>>Disetujui</option>
								<option value="ditolak" <?= $kegiatan['status_kegiatan'] == 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
								<option value="berlangsung" <?= $kegiatan['status_kegiatan'] == 'berlangsung' ? 'selected' : '' ?>>Berlangsung</option>
								<option value="selesai" <?= $kegiatan['status_kegiatan'] == 'selesai' ? 'selected' : '' ?>>Selesai</option>
							</select>
						</div>
					</div>

					<!-- Right Column -->
					<div class="col-md-6">
						<h5 class="mb-3 text-primary"><i class="bi bi-award"></i> Capaian Pembelajaran</h5>

						<div class="mb-3">
							<label class="form-label">Jenis Capaian <span class="text-danger">*</span></label>
							<div class="d-flex gap-4">
								<div class="form-check">
									<input class="form-check-input" type="radio" name="nilai_type" id="nilai_type_cpmk" value="cpmk"
										<?= ($kegiatan['nilai_type'] ?? '') === 'cpmk' ? 'checked' : '' ?> required>
									<label class="form-check-label" for="nilai_type_cpmk">
										<strong>CPMK</strong> (Capaian Pembelajaran Mata Kuliah)
									</label>
								</div>
								<div class="form-check">
									<input class="form-check-input" type="radio" name="nilai_type" id="nilai_type_cpl" value="cpl"
										<?= ($kegiatan['nilai_type'] ?? '') === 'cpl' ? 'checked' : '' ?>>
									<label class="form-check-label" for="nilai_type_cpl">
										<strong>CPL</strong> (Capaian Pembelajaran Lulusan)
									</label>
								</div>
							</div>
						</div>

						<!-- CPMK Selection -->
						<div class="mb-3" id="cpmk_selection" style="display: none;">
							<label for="cpmk_id" class="form-label">Pilih CPMK <span class="text-danger">*</span></label>
							<select class="form-select" id="cpmk_id" name="cpmk_id">
								<option value="">-- Pilih CPMK --</option>
								<?php foreach ($cpmk_list as $cpmk): ?>
									<option value="<?= $cpmk['id'] ?>"
										<?= ($kegiatan['cpmk_id'] ?? '') == $cpmk['id'] ? 'selected' : '' ?>
										data-deskripsi="<?= esc($cpmk['deskripsi']) ?>">
										<?= esc($cpmk['kode_cpmk']) ?> - <?= esc(substr($cpmk['deskripsi'], 0, 60)) ?>...
									</option>
								<?php endforeach; ?>
							</select>
							<div id="cpmk_deskripsi" class="form-text text-muted mt-2"></div>
						</div>

						<!-- CPL Selection -->
						<div class="mb-3" id="cpl_selection" style="display: none;">
							<label for="cpl_id" class="form-label">Pilih CPL <span class="text-danger">*</span></label>
							<select class="form-select" id="cpl_id" name="cpl_id">
								<option value="">-- Pilih CPL --</option>
								<?php foreach ($cpl_list as $cpl): ?>
									<option value="<?= $cpl['id'] ?>"
										<?= ($kegiatan['cpl_id'] ?? '') == $cpl['id'] ? 'selected' : '' ?>
										data-deskripsi="<?= esc($cpl['deskripsi']) ?>">
										<?= esc($cpl['kode_cpl']) ?> - <?= esc(substr($cpl['deskripsi'], 0, 60)) ?>...
									</option>
								<?php endforeach; ?>
							</select>
							<div id="cpl_deskripsi" class="form-text text-muted mt-2"></div>
						</div>

						<h5 class="mb-3 mt-4 text-primary"><i class="bi bi-people"></i> Pembimbing</h5>

						<div class="mb-3">
							<label for="dosen_pembimbing_id" class="form-label">Dosen Pembimbing</label>
							<select class="form-select" id="dosen_pembimbing_id" name="dosen_pembimbing_id">
								<option value="">Pilih Dosen Pembimbing</option>
								<?php foreach ($dosen as $d): ?>
									<option value="<?= $d['id'] ?>" <?= ($kegiatan['dosen_pembimbing_id'] ?? '') == $d['id'] ? 'selected' : '' ?>>
										<?= esc($d['nama_lengkap']) ?> (<?= esc($d['nip']) ?>)
									</option>
								<?php endforeach; ?>
							</select>
							<small class="text-muted">Pilih dosen pembimbing dari kampus</small>
						</div>

						<div class="mb-3">
							<label for="pembimbing_lapangan" class="form-label">Pembimbing Lapangan</label>
							<input type="text" class="form-control" id="pembimbing_lapangan" name="pembimbing_lapangan"
								value="<?= esc($kegiatan['pembimbing_lapangan'] ?? '') ?>" placeholder="Nama pembimbing di tempat kegiatan">
							<small class="text-muted">Nama pembimbing dari tempat kegiatan (perusahaan/instansi)</small>
						</div>

						<div class="mb-3">
							<label for="kontak_pembimbing" class="form-label">Kontak Pembimbing Lapangan</label>
							<input type="text" class="form-control" id="kontak_pembimbing" name="kontak_pembimbing"
								value="<?= esc($kegiatan['kontak_pembimbing'] ?? '') ?>" placeholder="Email atau nomor telepon">
						</div>

						<h5 class="mb-3 mt-4 text-primary"><i class="bi bi-file-text"></i> Informasi Tambahan</h5>

						<div class="mb-3">
							<label for="deskripsi_kegiatan" class="form-label">Deskripsi Kegiatan</label>
							<textarea class="form-control" id="deskripsi_kegiatan" name="deskripsi_kegiatan"
								rows="4" placeholder="Deskripsikan kegiatan yang akan dilakukan..."><?= esc($kegiatan['deskripsi_kegiatan'] ?? '') ?></textarea>
						</div>

						<?php if (!empty($kegiatan['nilai_huruf'])): ?>
							<div class="alert alert-success">
								<h6 class="alert-heading"><i class="bi bi-check-circle"></i> Status Penilaian</h6>
								<hr>
								<p class="mb-1"><strong>Nilai Akhir:</strong> <?= esc($kegiatan['nilai_huruf']) ?> (<?= esc($kegiatan['nilai_angka']) ?>)</p>
								<p class="mb-0"><strong>Status:</strong>
									<span class="badge bg-<?= $kegiatan['status_kelulusan'] == 'Lulus' ? 'success' : 'danger' ?>">
										<?= esc($kegiatan['status_kelulusan']) ?>
									</span>
								</p>
							</div>
						<?php endif; ?>
					</div>
				</div>

				<hr>

				<div class="d-flex justify-content-between">
					<button type="button" class="btn btn-danger" onclick="confirmDelete()">
						<i class="bi bi-trash"></i> Hapus Kegiatan
					</button>
					<div class="d-flex gap-2">
						<a href="<?= base_url('admin/mbkm') ?>" class="btn btn-secondary">
							<i class="bi bi-x-circle"></i> Batal
						</a>
						<button type="submit" class="btn btn-primary">
							<i class="bi bi-save"></i> Simpan Perubahan
						</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	function confirmDelete() {
		if (confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')) {
			window.location.href = '<?= base_url('admin/mbkm/delete/' . $kegiatan['id']) ?>';
		}
	}

	document.addEventListener('DOMContentLoaded', function() {
		// Track selected students
		const checkboxes = document.querySelectorAll('.mahasiswa-checkbox');
		const selectedCount = document.getElementById('selected-count');
		const mahasiswaAlert = document.getElementById('mahasiswa-alert');
		const selectedList = document.getElementById('selected-mahasiswa-list');

		function updateSelectedStudents() {
			const checked = document.querySelectorAll('.mahasiswa-checkbox:checked');
			selectedCount.textContent = checked.length;

			if (checked.length > 0) {
				mahasiswaAlert.style.display = 'block';
				selectedList.innerHTML = '';
				checked.forEach(cb => {
					const label = document.querySelector(`label[for="${cb.id}"]`);
					const li = document.createElement('li');
					li.textContent = label.querySelector('strong').textContent;
					selectedList.appendChild(li);
				});
			} else {
				mahasiswaAlert.style.display = 'none';
			}
		}

		checkboxes.forEach(cb => {
			cb.addEventListener('change', updateSelectedStudents);
		});

		// Initialize count on page load
		updateSelectedStudents();

		// CPL/CPMK toggle
		const nilaiTypeRadios = document.querySelectorAll('input[name="nilai_type"]');
		const cpmkSelection = document.getElementById('cpmk_selection');
		const cplSelection = document.getElementById('cpl_selection');
		const cpmkSelect = document.getElementById('cpmk_id');
		const cplSelect = document.getElementById('cpl_id');

		function toggleSelections() {
			const selectedType = document.querySelector('input[name="nilai_type"]:checked')?.value;

			if (selectedType === 'cpmk') {
				cpmkSelection.style.display = 'block';
				cplSelection.style.display = 'none';
				cpmkSelect.required = true;
				cplSelect.required = false;
				cplSelect.value = '';
			} else if (selectedType === 'cpl') {
				cpmkSelection.style.display = 'none';
				cplSelection.style.display = 'block';
				cpmkSelect.required = false;
				cplSelect.required = true;
				cpmkSelect.value = '';
			} else {
				cpmkSelection.style.display = 'none';
				cplSelection.style.display = 'none';
			}
		}

		nilaiTypeRadios.forEach(radio => {
			radio.addEventListener('change', toggleSelections);
		});

		// Initialize on page load
		toggleSelections();

		// Show description when CPMK is selected
		cpmkSelect.addEventListener('change', function() {
			const selectedOption = this.options[this.selectedIndex];
			const deskripsi = selectedOption.getAttribute('data-deskripsi') || '';
			document.getElementById('cpmk_deskripsi').textContent = deskripsi;
		});

		// Show description when CPL is selected
		cplSelect.addEventListener('change', function() {
			const selectedOption = this.options[this.selectedIndex];
			const deskripsi = selectedOption.getAttribute('data-deskripsi') || '';
			document.getElementById('cpl_deskripsi').textContent = deskripsi;
		});

		// Trigger description display for existing selections
		if (cpmkSelect.value) {
			cpmkSelect.dispatchEvent(new Event('change'));
		}
		if (cplSelect.value) {
			cplSelect.dispatchEvent(new Event('change'));
		}

		// Calculate duration in weeks
		const tanggalMulai = document.getElementById('tanggal_mulai');
		const tanggalSelesai = document.getElementById('tanggal_selesai');

		function hitungDurasi() {
			if (tanggalMulai.value && tanggalSelesai.value) {
				const mulai = new Date(tanggalMulai.value);
				const selesai = new Date(tanggalSelesai.value);
				const diffTime = Math.abs(selesai - mulai);
				const diffWeeks = Math.ceil(diffTime / (1000 * 60 * 60 * 24 * 7));

				if (diffWeeks > 0) {
					console.log(`Durasi: ${diffWeeks} minggu`);
				}
			}
		}

		tanggalMulai.addEventListener('change', hitungDurasi);
		tanggalSelesai.addEventListener('change', hitungDurasi);
	});
</script>
<?= $this->endSection() ?>

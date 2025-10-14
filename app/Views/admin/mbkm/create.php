<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
	<div class="d-flex justify-content-between align-items-center my-4">
		<h2 class="fw-bold">Tambah Kegiatan MBKM</h2>
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

	<div class="card shadow-sm">
		<div class="card-body">
			<form action="<?= base_url('admin/mbkm/store') ?>" method="POST" enctype="multipart/form-data">
				<?= csrf_field() ?>

				<div class="row">
					<!-- Left Column -->
					<div class="col-md-6">
						<h5 class="mb-3 text-primary"><i class="bi bi-person-circle"></i> Informasi Mahasiswa</h5>

						<div class="mb-3">
							<label class="form-label">Mahasiswa <span class="text-danger">*</span></label>
							<small class="d-block text-muted mb-2">Pilih satu atau lebih mahasiswa yang terlibat dalam kegiatan ini</small>

							<div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
								<?php foreach ($mahasiswa as $mhs): ?>
									<div class="form-check mb-2">
										<input class="form-check-input mahasiswa-checkbox"
											type="checkbox"
											name="mahasiswa_ids[]"
											value="<?= $mhs['id'] ?>"
											id="mhs_<?= $mhs['id'] ?>"
											<?= is_array(old('mahasiswa_ids')) && in_array($mhs['id'], old('mahasiswa_ids')) ? 'checked' : '' ?>>
										<label class="form-check-label" for="mhs_<?= $mhs['id'] ?>">
											<strong><?= esc($mhs['nama_lengkap']) ?></strong><br>
											<small class="text-muted"><?= esc($mhs['nim']) ?> - <?= esc($mhs['program_studi']) ?></small>
										</label>
									</div>
								<?php endforeach; ?>
							</div>
							<small class="text-muted">
								<span id="selected-count">0</span> mahasiswa dipilih
							</small>
						</div>

						<div class="alert alert-info" id="mahasiswa-alert" style="display:none;">
							<i class="bi bi-info-circle"></i> <strong>Mahasiswa yang dipilih:</strong>
							<ul id="selected-mahasiswa-list" class="mb-0 mt-2"></ul>
						</div>

						<h5 class="mb-3 mt-4 text-primary"><i class="bi bi-bookmark"></i> Detail Kegiatan</h5>

						<div class="mb-3">
							<label for="jenis_kegiatan_id" class="form-label">Jenis Kegiatan <span class="text-danger">*</span></label>
							<select class="form-select" id="jenis_kegiatan_id" name="jenis_kegiatan_id" required>
								<option value="">Pilih Jenis Kegiatan</option>
								<?php foreach ($jenis_kegiatan as $jk): ?>
									<option value="<?= $jk['id'] ?>" data-sks="<?= $jk['sks_konversi'] ?>" <?= old('jenis_kegiatan_id') == $jk['id'] ? 'selected' : '' ?>>
										<?= esc($jk['nama_kegiatan']) ?> (<?= $jk['sks_konversi'] ?> SKS)
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<div class="mb-3">
							<label for="judul_kegiatan" class="form-label">Judul Kegiatan <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="judul_kegiatan" name="judul_kegiatan"
								value="<?= old('judul_kegiatan') ?>" required placeholder="Contoh: Magang di PT. XYZ sebagai Software Developer">
						</div>

						<div class="mb-3">
							<label for="tempat_kegiatan" class="form-label">Tempat Kegiatan <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="tempat_kegiatan" name="tempat_kegiatan"
								value="<?= old('tempat_kegiatan') ?>" required placeholder="Contoh: PT. XYZ, Jakarta">
						</div>

						<div class="row">
							<div class="col-md-6 mb-3">
								<label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
								<input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai"
									value="<?= old('tanggal_mulai') ?>" required>
							</div>
							<div class="col-md-6 mb-3">
								<label for="tanggal_selesai" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
								<input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai"
									value="<?= old('tanggal_selesai') ?>" required>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6 mb-3">
								<label for="sks_dikonversi" class="form-label">SKS Dikonversi <span class="text-danger">*</span></label>
								<input type="number" class="form-control" id="sks_dikonversi" name="sks_dikonversi"
									value="<?= old('sks_dikonversi', 20) ?>" required min="1" max="20">
							</div>
							<div class="col-md-6 mb-3">
								<label for="tahun_akademik" class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
								<input type="text" class="form-control" id="tahun_akademik" name="tahun_akademik"
									value="<?= old('tahun_akademik', '2025/2026') ?>" required placeholder="2025/2026">
							</div>
						</div>
					</div>

					<!-- Right Column -->
					<div class="col-md-6">
						<h5 class="mb-3 text-primary"><i class="bi bi-people"></i> Pembimbing</h5>

						<div class="mb-3">
							<label for="dosen_pembimbing_id" class="form-label">Dosen Pembimbing</label>
							<select class="form-select" id="dosen_pembimbing_id" name="dosen_pembimbing_id">
								<option value="">Pilih Dosen Pembimbing</option>
								<?php foreach ($dosen as $d): ?>
									<option value="<?= $d['id'] ?>" <?= old('dosen_pembimbing_id') == $d['id'] ? 'selected' : '' ?>>
										<?= esc($d['nama_lengkap']) ?> (<?= esc($d['nip']) ?>)
									</option>
								<?php endforeach; ?>
							</select>
							<small class="text-muted">Pilih dosen pembimbing dari kampus</small>
						</div>

						<div class="mb-3">
							<label for="pembimbing_lapangan" class="form-label">Pembimbing Lapangan</label>
							<input type="text" class="form-control" id="pembimbing_lapangan" name="pembimbing_lapangan"
								value="<?= old('pembimbing_lapangan') ?>" placeholder="Nama pembimbing di tempat kegiatan">
							<small class="text-muted">Nama pembimbing dari tempat kegiatan (perusahaan/instansi)</small>
						</div>

						<div class="mb-3">
							<label for="kontak_pembimbing" class="form-label">Kontak Pembimbing Lapangan</label>
							<input type="text" class="form-control" id="kontak_pembimbing" name="kontak_pembimbing"
								value="<?= old('kontak_pembimbing') ?>" placeholder="Email atau nomor telepon">
						</div>

						<h5 class="mb-3 mt-4 text-primary"><i class="bi bi-file-text"></i> Informasi Tambahan</h5>

						<div class="mb-3">
							<label for="deskripsi_kegiatan" class="form-label">Deskripsi Kegiatan</label>
							<textarea class="form-control" id="deskripsi_kegiatan" name="deskripsi_kegiatan"
								rows="6" placeholder="Deskripsikan kegiatan yang akan dilakukan..."><?= old('deskripsi_kegiatan') ?></textarea>
						</div>

						<div class="alert alert-info">
							<i class="bi bi-info-circle"></i> <strong>Catatan:</strong>
							<ul class="mb-0 mt-2">
								<li>Pastikan data mahasiswa dan jenis kegiatan sudah benar</li>
								<li>SKS akan dikonversi sesuai dengan jenis kegiatan</li>
								<li>Status awal kegiatan adalah "Diajukan"</li>
							</ul>
						</div>
					</div>
				</div>

				<hr>

				<div class="d-flex justify-content-end gap-2">
					<a href="<?= base_url('admin/mbkm') ?>" class="btn btn-secondary">
						<i class="bi bi-x-circle"></i> Batal
					</a>
					<button type="submit" class="btn btn-primary">
						<i class="bi bi-save"></i> Simpan Kegiatan
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
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

		// Auto-fill SKS based on jenis kegiatan
		const jenisSelect = document.getElementById('jenis_kegiatan_id');
		const sksInput = document.getElementById('sks_dikonversi');

		jenisSelect.addEventListener('change', function() {
			const selectedOption = this.options[this.selectedIndex];
			const sksDefault = selectedOption.getAttribute('data-sks');
			if (sksDefault) {
				sksInput.value = sksDefault;
			}
		});

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
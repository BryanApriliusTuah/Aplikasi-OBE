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

						<div class="row">
							<div class="col-md-6 mb-3">
								<label for="program" class="form-label">Program</label>
								<input type="text" class="form-control" id="program" name="program"
									value="<?= esc($kegiatan['program'] ?? '') ?>" placeholder="Contoh: MSIB, Riset, Kampus Mengajar">
								<small class="text-muted">Program utama MBKM</small>
							</div>
							<div class="col-md-6 mb-3">
								<label for="sub_program" class="form-label">Sub Program</label>
								<input type="text" class="form-control" id="sub_program" name="sub_program"
									value="<?= esc($kegiatan['sub_program'] ?? '') ?>" placeholder="Contoh: Magang, Studi Independen, Penelitian di Desa">
								<small class="text-muted">Sub program atau kategori spesifik</small>
							</div>
						</div>


						<div class="row">
							<div class="col-md-6 mb-3">
								<label for="tujuan" class="form-label">Tujuan (Perusahaan/Universitas)</label>
								<input type="text" class="form-control" id="tujuan" name="tujuan"
									value="<?= esc($kegiatan['tujuan'] ?? '') ?>" placeholder="Contoh: PT Telkom Indonesia, Universitas Gadjah Mada">
								<small class="text-muted">Nama perusahaan atau institusi tujuan</small>
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
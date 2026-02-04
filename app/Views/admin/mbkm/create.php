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

	<?php if (session()->getFlashdata('error')): ?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<?= esc(session()->getFlashdata('error')) ?>
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
							<label for="mahasiswa_ids" class="form-label">Mahasiswa <span class="text-danger">*</span></label>
							<small class="d-block text-muted mb-2">Pilih satu atau lebih mahasiswa yang terlibat dalam kegiatan ini</small>
							<select class="form-select" id="mahasiswa_ids" name="mahasiswa_ids[]" multiple="multiple" required>
								<?php foreach ($mahasiswa as $mhs): ?>
									<option value="<?= $mhs['id'] ?>">
										<?= esc($mhs['nama_lengkap']) ?> (<?= esc($mhs['nim']) ?>)
									</option>
								<?php endforeach; ?>
							</select>
						</div>

						<h5 class="mb-3 mt-4 text-primary"><i class="bi bi-bookmark"></i> Detail Kegiatan</h5>

						<div class="mb-3">
							<label for="jenis_kegiatan" class="form-label">Jenis Kegiatan <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="jenis_kegiatan" name="jenis_kegiatan"
								value="" required placeholder="Contoh: Magang, Pertukaran Mahasiswa, Studi Independen, dll.">
						</div>

						<div class="mb-3">
							<label for="judul_kegiatan" class="form-label">Judul Kegiatan <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="judul_kegiatan" name="judul_kegiatan"
								value="" required placeholder="Contoh: Magang di PT. XYZ sebagai Software Developer">
						</div>

						<div class="mb-3">
							<label for="tempat_kegiatan" class="form-label">Tempat Kegiatan <span class="text-danger">*</span></label>
							<input type="text" class="form-control" id="tempat_kegiatan" name="tempat_kegiatan"
								value="" required placeholder="Contoh: PT. XYZ, Jakarta">
						</div>

						<div class="row">
							<div class="col-md-6 mb-3">
								<label for="tanggal_mulai" class="form-label">Tanggal Mulai <span class="text-danger">*</span></label>
								<input type="date" class="form-control" id="tanggal_mulai" name="tanggal_mulai"
									value="" required>
							</div>
							<div class="col-md-6 mb-3">
								<label for="tanggal_selesai" class="form-label">Tanggal Selesai <span class="text-danger">*</span></label>
								<input type="date" class="form-control" id="tanggal_selesai" name="tanggal_selesai"
									value="" required>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6 mb-3">
								<label for="sks_dikonversi" class="form-label">SKS Dikonversi <span class="text-danger">*</span></label>
								<input type="number" class="form-control" id="sks_dikonversi" name="sks_dikonversi"
									value="20" required min="1" max="20">
							</div>
							<div class="col-md-6 mb-3">
								<label for="tahun_akademik" class="form-label">Tahun Akademik <span class="text-danger">*</span></label>
								<input type="text" class="form-control" id="tahun_akademik" name="tahun_akademik"
									value="2025/2026" required placeholder="2025/2026">
							</div>
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
										required>
									<label class="form-check-label" for="nilai_type_cpmk">
										<strong>CPMK</strong> (Capaian Pembelajaran Mata Kuliah)
									</label>
								</div>
								<div class="form-check">
									<input class="form-check-input" type="radio" name="nilai_type" id="nilai_type_cpl" value="cpl">
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
									<option value="<?= $d['id'] ?>">
										<?= esc($d['nama_lengkap']) ?> (<?= esc($d['nip']) ?>)
									</option>
								<?php endforeach; ?>
							</select>
							<small class="text-muted">Pilih dosen pembimbing dari kampus</small>
						</div>

						<div class="mb-3">
							<label for="pembimbing_lapangan" class="form-label">Pembimbing Lapangan</label>
							<input type="text" class="form-control" id="pembimbing_lapangan" name="pembimbing_lapangan"
								value="" placeholder="Nama pembimbing di tempat kegiatan">
							<small class="text-muted">Nama pembimbing dari tempat kegiatan (perusahaan/instansi)</small>
						</div>

						<div class="mb-3">
							<label for="kontak_pembimbing" class="form-label">Kontak Pembimbing Lapangan</label>
							<input type="text" class="form-control" id="kontak_pembimbing" name="kontak_pembimbing"
								value="" placeholder="Email atau nomor telepon">
						</div>

						<h5 class="mb-3 mt-4 text-primary"><i class="bi bi-file-text"></i> Informasi Tambahan</h5>

						<div class="mb-3">
							<label for="deskripsi_kegiatan" class="form-label">Deskripsi Kegiatan</label>
							<textarea class="form-control" id="deskripsi_kegiatan" name="deskripsi_kegiatan"
								rows="4" placeholder="Deskripsikan kegiatan yang akan dilakukan..."></textarea>
						</div>

						<div class="alert alert-info">
							<i class="bi bi-info-circle"></i> <strong>Catatan:</strong>
							<ul class="mb-0 mt-2">
								<li>Pastikan data mahasiswa dan capaian pembelajaran sudah benar</li>
								<li>Nilai akan diinput setelah kegiatan selesai</li>
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
		// Initialize Select2
		$('#mahasiswa_ids').select2({
			theme: 'bootstrap-5',
			placeholder: 'Cari dan pilih mahasiswa...',
			allowClear: true,
			width: '100%'
		});

		$('#cpmk_id').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih CPMK --',
			allowClear: true,
			width: '100%'
		});

		$('#cpl_id').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih CPL --',
			allowClear: true,
			width: '100%'
		});

		$('#dosen_pembimbing_id').select2({
			theme: 'bootstrap-5',
			placeholder: 'Pilih Dosen Pembimbing',
			allowClear: true,
			width: '100%'
		});

		// CPL/CPMK toggle
		const nilaiTypeRadios = document.querySelectorAll('input[name="nilai_type"]');
		const cpmkSelection = document.getElementById('cpmk_selection');
		const cplSelection = document.getElementById('cpl_selection');

		function toggleSelections() {
			const selectedType = document.querySelector('input[name="nilai_type"]:checked')?.value;

			if (selectedType === 'cpmk') {
				cpmkSelection.style.display = 'block';
				cplSelection.style.display = 'none';
				$('#cpmk_id').prop('required', true);
				$('#cpl_id').prop('required', false).val('').trigger('change');
			} else if (selectedType === 'cpl') {
				cpmkSelection.style.display = 'none';
				cplSelection.style.display = 'block';
				$('#cpmk_id').prop('required', false).val('').trigger('change');
				$('#cpl_id').prop('required', true);
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
		$('#cpmk_id').on('change', function() {
			const deskripsi = $(this).find(':selected').data('deskripsi') || '';
			$('#cpmk_deskripsi').text(deskripsi);
		});

		// Show description when CPL is selected
		$('#cpl_id').on('change', function() {
			const deskripsi = $(this).find(':selected').data('deskripsi') || '';
			$('#cpl_deskripsi').text(deskripsi);
		});
	});
</script>
<?= $this->endSection() ?>
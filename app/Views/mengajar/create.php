<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-md">
	<div class="row">
		<div class="col-lg-10 mx-auto">

			<h2 class="fw-bold mb-4"><?= esc($title) ?></h2>

			<?php if (session()->getFlashdata('error')): ?>
				<div class="alert alert-danger">
					<?php $e = session()->getFlashdata('error');
					echo is_array($e) ? implode('<br>', array_map('esc', $e)) : esc($e); ?>
				</div>
			<?php endif; ?>

			<form action="<?= base_url('admin/mengajar/store') ?>" method="post">
				<?= csrf_field() ?>
				<div class="card shadow-sm">
					<div class="card-body">
						<div class="row g-3">
							<div class="col-12">
								<label for="mata_kuliah_id" class="form-label">Mata Kuliah</label>
								<select class="form-select" id="mata_kuliah_id" name="mata_kuliah_id" required>
									<option value="">-- Pilih Mata Kuliah --</option>
									<?php foreach ($mata_kuliah_list as $mk): ?>
										<option value="<?= $mk['id'] ?>" <?= old('mata_kuliah_id') == $mk['id'] ? 'selected' : '' ?>>
											[SMT <?= esc($mk['semester']) ?>] <?= esc($mk['kode_mk']) ?> - <?= esc($mk['nama_mk']) ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="col-md-6">
								<label for="program_studi" class="form-label">Program Studi</label>
								<select class="form-select" id="program_studi" name="program_studi" required>
									<option value="Teknik Informatika" <?= old('program_studi') == 'Teknik Informatika' ? 'selected' : '' ?>>Teknik Informatika</option>
									<option value="Sistem Informasi" <?= old('program_studi') == 'Sistem Informasi' ? 'selected' : '' ?>>Sistem Informasi</option>
									<option value="Teknik Komputer" <?= old('program_studi') == 'Teknik Komputer' ? 'selected' : '' ?>>Teknik Komputer</option>
								</select>
							</div>
							<div class="col-md-6">
								<label for="tahun_akademik" class="form-label">Tahun Akademik</label>
								<input type="text" class="form-control" id="tahun_akademik" name="tahun_akademik" value="<?= old('tahun_akademik') ?>" list="tahun_akademik_options" placeholder="Contoh: 2025/2026 Ganjil" required>
								<datalist id="tahun_akademik_options">
									<?php foreach ($tahun_akademik_list as $tahun): ?>
										<option value="<?= esc($tahun) ?>">
										<?php endforeach; ?>
								</datalist>
							</div>

							<div class="col-md-6">
								<label for="kelas" class="form-label">Kelas</label>
								<input type="text" class="form-control" id="kelas" name="kelas" value="<?= old('kelas', 'A') ?>" required>
							</div>
							<div class="col-md-6">
								<label for="ruang" class="form-label">Ruang</label>
								<input type="text" class="form-control" id="ruang" name="ruang" value="<?= old('ruang') ?>">
							</div>

							<div class="col-md-4">
								<label for="hari" class="form-label">Hari</label>
								<select class="form-select" id="hari" name="hari">
									<option value="">-- Pilih Hari --</option>
									<?php $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']; ?>
									<?php foreach ($days as $day): ?>
										<option value="<?= $day ?>" <?= old('hari') == $day ? 'selected' : '' ?>><?= $day ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-4">
								<label for="jam_mulai" class="form-label">Jam Mulai</label>
								<input type="time" class="form-control" id="jam_mulai" name="jam_mulai" value="<?= old('jam_mulai') ?>">
							</div>
							<div class="col-md-4">
								<label for="jam_selesai" class="form-label">Jam Selesai</label>
								<input type="time" class="form-control" id="jam_selesai" name="jam_selesai" value="<?= old('jam_selesai') ?>">
							</div>

							<hr class="my-3">

							<div class="col-12">
								<label for="dosen_leader" class="form-label">Dosen Koordinator</label>
								<select class="form-select" id="dosen_leader" name="dosen_leader" required>
									<option value="">-- Pilih Dosen Koordinator --</option>
									<?php foreach ($dosen_list as $dosen): ?>
										<option value="<?= $dosen['id'] ?>" <?= old('dosen_leader') == $dosen['id'] ? 'selected' : '' ?>>
											<?= esc($dosen['nama_lengkap']) ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="col-12">
								<label class="form-label">Dosen Anggota (Team Teaching)</label>
								<div id="dosen-members-container">
									<?php // Repopulate on validation error
									$old_members = old('dosen_members') ?? [];
									foreach ($old_members as $member_id): ?>
										<div class="input-group mb-2">
											<select class="form-select" name="dosen_members[]">
												<option value="">-- Pilih Dosen Anggota --</option>
												<?php foreach ($dosen_list as $dosen): ?>
													<option value="<?= $dosen['id'] ?>" <?= $member_id == $dosen['id'] ? 'selected' : '' ?>>
														<?= esc($dosen['nama_lengkap']) ?>
													</option>
												<?php endforeach; ?>
											</select>
											<button class="btn btn-outline-danger remove-member-btn" type="button">
												<i class="bi bi-x-lg"></i>
											</button>
										</div>
									<?php endforeach; ?>
								</div>
								<button type="button" id="add-member-btn" class="btn btn-outline-primary btn-sm mt-2">
									<i class="bi bi-plus"></i> Tambah Dosen Anggota
								</button>
							</div>
						</div>
					</div>

					<div class="card-footer text-end">
						<a href="<?= base_url('admin/mengajar') ?>" class="btn btn-secondary">Batal</a>
						<button type="submit" class="btn btn-primary">Simpan Jadwal</button>
					</div>
				</div>
			</form>

		</div>
	</div>
</div>

<div id="dosen-member-template" style="display: none;">
	<div class="input-group mb-2">
		<select class="form-select" name="dosen_members[]">
			<option value="">-- Pilih Dosen Anggota --</option>
			<?php foreach ($dosen_list as $dosen): ?>
				<option value="<?= $dosen['id'] ?>"><?= esc($dosen['nama_lengkap']) ?></option>
			<?php endforeach; ?>
		</select>
		<button class="btn btn-outline-danger remove-member-btn" type="button">
			<i class="bi bi-x-lg"></i>
		</button>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		const container = document.getElementById('dosen-members-container');
		const addButton = document.getElementById('add-member-btn');
		const template = document.getElementById('dosen-member-template');

		addButton.addEventListener('click', function() {
			// Clone the template's first child (the input-group div)
			const newRow = template.firstElementChild.cloneNode(true);
			container.appendChild(newRow);
		});

		container.addEventListener('click', function(event) {
			// Check if a remove button was clicked
			if (event.target.closest('.remove-member-btn')) {
				// Find the parent .input-group and remove it
				event.target.closest('.input-group').remove();
			}
		});
	});
</script>
<?= $this->endSection() ?>
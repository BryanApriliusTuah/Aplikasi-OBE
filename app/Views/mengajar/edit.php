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

			<form action="<?= base_url('admin/mengajar/update/' . $jadwal['id']) ?>" method="post">
				<?= csrf_field() ?>
				<div class="card shadow-sm">
					<div class="card-body">
						<div class="row g-3">
							<div class="col-12">
								<label for="mata_kuliah_id" class="form-label">Mata Kuliah</label>
								<select class="form-select" id="mata_kuliah_id" name="mata_kuliah_id" required>
									<option value="">-- Pilih Mata Kuliah --</option>
									<?php foreach ($mata_kuliah_list as $mk): ?>
										<option value="<?= $mk['id'] ?>" <?= old('mata_kuliah_id', $jadwal['mata_kuliah_id']) == $mk['id'] ? 'selected' : '' ?>>
											[SMT <?= esc($mk['semester']) ?>] <?= esc($mk['kode_mk']) ?> - <?= esc($mk['nama_mk']) ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="col-md-6">
								<label for="program_studi" class="form-label">Program Studi</label>
								<select class="form-select" id="program_studi" name="program_studi" required>
									<option value="Teknik Informatika" <?= old('program_studi', $jadwal['program_studi']) == 'Teknik Informatika' ? 'selected' : '' ?>>Teknik Informatika</option>
									<option value="Sistem Informasi" <?= old('program_studi', $jadwal['program_studi']) == 'Sistem Informasi' ? 'selected' : '' ?>>Sistem Informasi</option>
									<option value="Teknik Komputer" <?= old('program_studi', $jadwal['program_studi']) == 'Teknik Komputer' ? 'selected' : '' ?>>Teknik Komputer</option>
								</select>
							</div>
							<div class="col-md-6">
								<label for="tahun_akademik" class="form-label">Tahun Akademik</label>
								<input type="text" class="form-control" id="tahun_akademik" name="tahun_akademik" value="<?= old('tahun_akademik', $jadwal['tahun_akademik']) ?>" placeholder="Contoh: 2024/2025 Ganjil" required>
							</div>

							<div class="col-md-6">
								<label for="kelas" class="form-label">Kelas</label>
								<input type="text" class="form-control" id="kelas" name="kelas" value="<?= old('kelas', $jadwal['kelas']) ?>" required>
							</div>
							<div class="col-md-6">
								<label for="ruang" class="form-label">Ruang</label>
								<input type="text" class="form-control" id="ruang" name="ruang" value="<?= old('ruang', $jadwal['ruang']) ?>">
							</div>

							<div class="col-md-4">
								<label for="hari" class="form-label">Hari</label>
								<select class="form-select" id="hari" name="hari">
									<option value="">-- Pilih Hari --</option>
									<?php $days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']; ?>
									<?php foreach ($days as $day): ?>
										<option value="<?= $day ?>" <?= old('hari', $jadwal['hari']) == $day ? 'selected' : '' ?>><?= $day ?></option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-4">
								<label for="jam_mulai" class="form-label">Jam Mulai</label>
								<input type="time" class="form-control" id="jam_mulai" name="jam_mulai" value="<?= old('jam_mulai', $jadwal['jam_mulai']) ?>">
							</div>
							<div class="col-md-4">
								<label for="jam_selesai" class="form-label">Jam Selesai</label>
								<input type="time" class="form-control" id="jam_selesai" name="jam_selesai" value="<?= old('jam_selesai', $jadwal['jam_selesai']) ?>">
							</div>

							<hr class="my-3">

							<div class="col-12">
								<label for="dosen_leader" class="form-label">Dosen Koordinator</label>
								<select class="form-select" id="dosen_leader" name="dosen_leader" disabled>
									<option value="">-- Pilih Dosen Koordinator --</option>
									<?php foreach ($dosen_list as $dosen): ?>
										<option value="<?= $dosen['id'] ?>" <?= old('dosen_leader', $jadwal['dosen_leader']) == $dosen['id'] ? 'selected' : '' ?>>
											<?= esc($dosen['nama_lengkap']) ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<div class="col-12">
								<label class="form-label">Dosen Pengampu</label>
								<div id="dosen-members-container">
									<?php
									$old_members = old('dosen_members', $jadwal['dosen_members'] ?? []);
									if (!empty($old_members)):
										foreach ($old_members as $member_id): ?>
											<div class="mb-2">
												<select class="form-select" name="dosen_members[]" disabled>
													<option value="">-- Pilih Dosen Pengampu --</option>
													<?php foreach ($dosen_list as $dosen): ?>
														<option value="<?= $dosen['id'] ?>" <?= $member_id == $dosen['id'] ? 'selected' : '' ?>>
															<?= esc($dosen['nama_lengkap']) ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>
										<?php endforeach;
									else: ?>
										<p class="text-muted mb-0">Tidak ada dosen pengampu</p>
									<?php endif; ?>
								</div>
								<small class="text-muted">
									<i class="bi bi-info-circle"></i> Dosen dapat diubah melalui RPS (Rencana Pembelajaran Semester)
								</small>
							</div>
						</div>
					</div>

					<div class="card-footer text-end">
						<a href="<?= base_url('admin/mengajar') ?>" class="btn btn-secondary">Batal</a>
						<button type="submit" class="btn btn-primary">Simpan Perubahan</button>
					</div>
				</div>
			</form>

		</div>
	</div>
</div>

<?= $this->endSection() ?>
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
								<div class="alert d-none" id="rps-info">
									<i class="bi bi-info-circle"></i> <span id="rps-message"></span>
								</div>
							</div>

							<div class="col-12">
								<label for="dosen_leader" class="form-label">Dosen Koordinator</label>
								<input type="text" class="form-control" id="dosen_leader_display" readonly placeholder="Pilih mata kuliah terlebih dahulu">
								<input type="hidden" id="dosen_leader" name="dosen_leader" required>
							</div>

							<div class="col-12">
								<label class="form-label">Dosen Pengampu</label>
								<div id="dosen-members-display"></div>
								<div id="dosen-members-container" style="display: none;"></div>
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

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		const mataKuliahSelect = document.getElementById('mata_kuliah_id');
		const dosenLeaderDisplay = document.getElementById('dosen_leader_display');
		const dosenLeaderInput = document.getElementById('dosen_leader');
		const dosenMembersDisplay = document.getElementById('dosen-members-display');
		const dosenMembersContainer = document.getElementById('dosen-members-container');
		const rpsInfo = document.getElementById('rps-info');
		const rpsMessage = document.getElementById('rps-message');
		const submitButton = document.querySelector('button[type="submit"]');

		// Initially disable submit button
		submitButton.disabled = true;

		// Fetch RPS dosen data when mata kuliah is selected
		mataKuliahSelect.addEventListener('change', function() {
			const mataKuliahId = this.value;

			// Reset fields
			dosenLeaderDisplay.value = '';
			dosenLeaderInput.value = '';
			dosenMembersDisplay.innerHTML = '';
			dosenMembersContainer.innerHTML = '';
			rpsInfo.classList.add('d-none');
			rpsInfo.className = 'alert d-none';
			submitButton.disabled = true;

			if (!mataKuliahId) {
				dosenLeaderDisplay.placeholder = 'Pilih mata kuliah terlebih dahulu';
				return;
			}

			dosenLeaderDisplay.placeholder = 'Memuat data RPS...';

			// Fetch RPS dosen data via AJAX
			fetch('<?= base_url('admin/mengajar/getRpsDosen') ?>/' + mataKuliahId, {
					method: 'GET',
					headers: {
						'X-Requested-With': 'XMLHttpRequest'
					}
				})
				.then(response => response.json())
				.then(data => {
					if (data.success) {
						// Show success message
						rpsInfo.classList.remove('d-none');
						rpsInfo.classList.add('alert-success');
						rpsMessage.textContent = 'Data dosen berhasil dimuat dari RPS';

						// Set dosen koordinator
						if (data.data.koordinator) {
							dosenLeaderDisplay.value = data.data.koordinator.nama_lengkap;
							dosenLeaderInput.value = data.data.koordinator.id;
						} else {
							dosenLeaderDisplay.value = 'Tidak ada koordinator di RPS';
							rpsInfo.classList.remove('alert-success');
							rpsInfo.classList.add('alert-danger');
							rpsMessage.textContent = 'RPS tidak memiliki dosen koordinator. Harap lengkapi data RPS terlebih dahulu.';
							return;
						}

						// Display members from RPS
						if (data.data.members && data.data.members.length > 0) {
							let membersHtml = '<div class="list-group">';
							data.data.members.forEach(member => {
								membersHtml += `<div class="list-group-item">${member.nama_lengkap}</div>`;
								// Add hidden inputs for submission
								const input = document.createElement('input');
								input.type = 'hidden';
								input.name = 'dosen_members[]';
								input.value = member.id;
								dosenMembersContainer.appendChild(input);
							});
							membersHtml += '</div>';
							dosenMembersDisplay.innerHTML = membersHtml;
						} else {
							dosenMembersDisplay.innerHTML = '<p class="text-muted mb-0">Tidak ada dosen anggota</p>';
						}

						// Enable submit button
						submitButton.disabled = false;
					} else {
						// Show error message
						rpsInfo.classList.remove('d-none');
						rpsInfo.classList.add('alert-danger');
						rpsMessage.textContent = data.message || 'RPS tidak ditemukan untuk mata kuliah ini. Harap buat RPS terlebih dahulu.';
						dosenLeaderDisplay.value = '';
						dosenLeaderDisplay.placeholder = 'RPS tidak tersedia';
						submitButton.disabled = true;
					}
				})
				.catch(error => {
					console.error('Error fetching RPS data:', error);
					rpsInfo.classList.remove('d-none');
					rpsInfo.classList.add('alert-danger');
					rpsMessage.textContent = 'Terjadi kesalahan saat mengambil data RPS.';
					dosenLeaderDisplay.placeholder = 'Error memuat data';
					submitButton.disabled = true;
				});
		});
	});
</script>
<?= $this->endSection() ?>
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
										<option value="<?= $mk['id'] ?>" data-kode="<?= esc($mk['kode_mk']) ?>" <?= old('mata_kuliah_id') == $mk['id'] ? 'selected' : '' ?>>
											[SMT <?= esc($mk['semester']) ?>] <?= esc($mk['kode_mk']) ?> - <?= esc($mk['nama_mk']) ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<!-- API Kelas Selection -->
							<div class="col-12" id="api-kelas-section" style="display: none;">
								<label class="form-label">Kelas dari API <span class="badge bg-info">SIUBER</span></label>
								<div id="api-kelas-loading" class="text-muted small" style="display: none;">
									<div class="spinner-border spinner-border-sm me-1" role="status"></div> Memuat data kelas dari API...
								</div>
								<div id="api-kelas-container"></div>
								<input type="hidden" id="kelas_id" name="kelas_id">
								<input type="hidden" id="kelas_jenis" name="kelas_jenis">
								<input type="hidden" id="kelas_semester" name="kelas_semester">
								<input type="hidden" id="mk_kurikulum_kode" name="mk_kurikulum_kode">
								<input type="hidden" id="total_mahasiswa" name="total_mahasiswa">
							</div>

							<div class="col-md-6">
								<label for="program_studi_kode" class="form-label">Program Studi</label>
								<select class="form-select" id="program_studi_kode" name="program_studi_kode" required>
									<option value="">-- Pilih Program Studi --</option>
									<?php foreach ($program_studi_list as $prodi): ?>
										<option value="<?= esc($prodi['kode']) ?>" <?= old('program_studi_kode') == $prodi['kode'] ? 'selected' : '' ?>>
											<?= esc($prodi['nama_resmi']) ?>
										</option>
									<?php endforeach; ?>
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
	$(document).ready(function() {
		// Initialize Select2 on dropdowns
		$('#mata_kuliah_id').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Mata Kuliah --',
			allowClear: true,
			width: '100%'
		});

		$('#program_studi_kode').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Program Studi --',
			allowClear: true,
			width: '100%'
		});

		$('#hari').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Hari --',
			allowClear: true,
			width: '100%'
		});

		var $dosenLeaderDisplay = $('#dosen_leader_display');
		var $dosenLeaderInput = $('#dosen_leader');
		var $dosenMembersDisplay = $('#dosen-members-display');
		var $dosenMembersContainer = $('#dosen-members-container');
		var $rpsInfo = $('#rps-info');
		var $rpsMessage = $('#rps-message');
		var $submitButton = $('button[type="submit"]');

		// API kelas elements
		var $apiKelasSection = $('#api-kelas-section');
		var $apiKelasLoading = $('#api-kelas-loading');
		var $apiKelasContainer = $('#api-kelas-container');

		// Initially disable submit button
		$submitButton.prop('disabled', true);

		// Listen for Select2 change on mata kuliah
		$('#mata_kuliah_id').on('change', function() {
			var mataKuliahId = $(this).val();
			var $selectedOption = $(this).find(':selected');
			var kodeMk = $selectedOption.data('kode') || '';

			// Reset fields
			$dosenLeaderDisplay.val('');
			$dosenLeaderInput.val('');
			$dosenMembersDisplay.html('');
			$dosenMembersContainer.html('');
			$rpsInfo.addClass('d-none').attr('class', 'alert d-none');
			$submitButton.prop('disabled', true);

			// Reset API kelas fields
			$apiKelasSection.hide();
			$apiKelasContainer.html('');
			$('#kelas_id').val('');
			$('#kelas_jenis').val('');
			$('#kelas_semester').val('');
			$('#mk_kurikulum_kode').val('');
			$('#total_mahasiswa').val('');

			if (!mataKuliahId) {
				$dosenLeaderDisplay.attr('placeholder', 'Pilih mata kuliah terlebih dahulu');
				return;
			}

			$dosenLeaderDisplay.attr('placeholder', 'Memuat data RPS...');

			// Fetch RPS dosen data via AJAX
			$.ajax({
				url: '<?= base_url('admin/mengajar/getRpsDosen') ?>/' + mataKuliahId,
				method: 'GET',
				headers: {
					'X-Requested-With': 'XMLHttpRequest'
				},
				dataType: 'json',
				success: function(data) {
					if (data.success) {
						$rpsInfo.removeClass('d-none').addClass('alert-success');
						$rpsMessage.text('Data dosen berhasil dimuat dari RPS');

						if (data.data.koordinator) {
							$dosenLeaderDisplay.val(data.data.koordinator.nama_lengkap);
							$dosenLeaderInput.val(data.data.koordinator.id);
						} else {
							$dosenLeaderDisplay.val('Tidak ada koordinator di RPS');
							$rpsInfo.removeClass('alert-success').addClass('alert-danger');
							$rpsMessage.text('RPS tidak memiliki dosen koordinator. Harap lengkapi data RPS terlebih dahulu.');
							return;
						}

						if (data.data.members && data.data.members.length > 0) {
							var membersHtml = '<div class="list-group">';
							$.each(data.data.members, function(i, member) {
								membersHtml += '<div class="list-group-item">' + member.nama_lengkap + '</div>';
								$dosenMembersContainer.append('<input type="hidden" name="dosen_members[]" value="' + member.id + '">');
							});
							membersHtml += '</div>';
							$dosenMembersDisplay.html(membersHtml);
						} else {
							$dosenMembersDisplay.html('<p class="text-muted mb-0">Tidak ada dosen anggota</p>');
						}

						$submitButton.prop('disabled', false);
					} else {
						$rpsInfo.removeClass('d-none').addClass('alert-danger');
						$rpsMessage.text(data.message || 'RPS tidak ditemukan untuk mata kuliah ini. Harap buat RPS terlebih dahulu.');
						$dosenLeaderDisplay.val('').attr('placeholder', 'RPS tidak tersedia');
						$submitButton.prop('disabled', true);
					}
				},
				error: function(xhr, status, error) {
					console.error('Error fetching RPS data:', error);
					$rpsInfo.removeClass('d-none').addClass('alert-danger');
					$rpsMessage.text('Terjadi kesalahan saat mengambil data RPS.');
					$dosenLeaderDisplay.attr('placeholder', 'Error memuat data');
					$submitButton.prop('disabled', true);
				}
			});

			// Fetch API kelas data
			if (kodeMk) {
				$apiKelasSection.show();
				$apiKelasLoading.show();
				$apiKelasContainer.html('');

				$.ajax({
					url: '<?= base_url('admin/mengajar/getApiKelas') ?>',
					method: 'GET',
					data: {
						kode_mk: kodeMk
					},
					headers: {
						'X-Requested-With': 'XMLHttpRequest'
					},
					dataType: 'json',
					success: function(data) {
						$apiKelasLoading.hide();

						// Auto-fill program studi from API response
						if (data.program_studi_kode) {
							$('#program_studi_kode').val(data.program_studi_kode).trigger('change');
						}

						if (data.success && data.data.length > 0) {
							var html = '<div class="list-group">';
							$.each(data.data, function(index, kelas) {
								console.log('Kelas data:', kelas);
								var semester = kelas.kelas.semester || '-';
								var totalMhs = kelas.mahasiswa.total ? kelas.mahasiswa.total : 0;

								// Safely extract schedule data
								var hari = '';
								var jamMulai = '';
								var jamSelesai = '';
								var ruang = '';
								var gedung = '';

								// jadwal_perkuliahan is an array, access first element
								if (kelas.jadwal_perkuliahan && kelas.jadwal_perkuliahan.length > 0) {
									var jadwal = kelas.jadwal_perkuliahan[0];
									hari = jadwal.hari || '';

									// Extract time from ISO format "2026-02-07T08:20:00.000000Z" -> "08:20"
									var jamMulaiRaw = jadwal.jam.mulai || '';
									var jamSelesaiRaw = jadwal.jam.selesai || '';

									if (jamMulaiRaw) {
										jamMulai = jamMulaiRaw.substring(11, 16); // Extract HH:MM
									}
									if (jamSelesaiRaw) {
										jamSelesai = jamSelesaiRaw.substring(11, 16); // Extract HH:MM
									}

									if (jadwal.ruangan) {
										ruang = jadwal.ruangan.ruang || '';
										gedung = jadwal.ruangan.gedung || '';
									}
								}

								var ruangLengkap = gedung ? gedung + ' - ' + ruang : ruang;

								html += '<label class="list-group-item list-group-item-action d-flex align-items-center gap-3" style="cursor: pointer;">' +
									'<input type="radio" name="api_kelas_select" class="form-check-input mt-0" value="' + index + '"' +
									' data-kelas-id="' + (kelas.jadwal_kelas_id || '') + '"' +
									' data-kelas-nama="' + (kelas.kelas.nama || '') + '"' +
									' data-kelas-jenis="' + (kelas.kelas.jenis || '') + '"' +
									' data-kelas-semester="' + kelas.kelas.semester + '"' +
									' data-kelas-status="' + (kelas.kelas.status || '') + '"' +
									' data-mk-kode="' + (kelas.mata_kuliah.kode || '') + '"' +
									' data-total-mhs="' + totalMhs + '"' +
									' data-hari="' + hari + '"' +
									' data-jam-mulai="' + jamMulai + '"' +
									' data-jam-selesai="' + jamSelesai + '"' +
									' data-ruang="' + ruangLengkap + '">' +
									'<div class="flex-grow-1">' +
									'<div class="fw-semibold">Kelas ' + (kelas.kelas.nama || '-') + '</div>' +
									'<div class="small text-muted">' +
									(kelas.kelas.jenis || '-') + ' &bull; ' +
									'Semester: ' + semester + ' &bull; ' +
									'Status: <span class="badge ' + (kelas.kelas.status === 'Aktif' ? 'bg-success' : 'bg-secondary') + '">' + (kelas.kelas.status || '-') + '</span> &bull; ' +
									'<i class="bi bi-people"></i> ' + totalMhs + ' mahasiswa' +
									(hari ? ' &bull; ' + hari : '') +
									(jamMulai ? ' &bull; ' + jamMulai + '-' + jamSelesai : '') +
									(ruangLengkap ? ' &bull; ' + ruangLengkap : '') +
									'</div></div></label>';
							});
							html += '</div>';
							html += '<small class="text-muted mt-1 d-block"><i class="bi bi-info-circle"></i> Pilih kelas untuk mengisi otomatis data kelas dari API.</small>';
							$apiKelasContainer.html(html);

							// Event delegation for radio buttons
							$apiKelasContainer.off('change', 'input[name="api_kelas_select"]').on('change', 'input[name="api_kelas_select"]', function() {
								var $radio = $(this);
								$('#kelas').val($radio.data('kelas-nama'));
								$('#kelas_id').val($radio.data('kelas-id'));
								$('#kelas_jenis').val($radio.data('kelas-jenis'));
								$('#kelas_semester').val($radio.data('kelas-semester'));
								$('#mk_kurikulum_kode').val($radio.data('mk-kode'));
								$('#total_mahasiswa').val($radio.data('total-mhs'));

								// Auto-fill schedule fields (always update, even if empty to clear previous values)
								var hari = $radio.data('hari') || '';
								var jamMulai = $radio.data('jam-mulai') || '';
								var jamSelesai = $radio.data('jam-selesai') || '';
								var ruang = $radio.data('ruang') || '';

								console.log('Autofill data from radio:', {hari, jamMulai, jamSelesai, ruang});

								// Always set values (empty string will clear the field)
								$('#hari').val(hari).trigger('change');
								$('#jam_mulai').val(jamMulai);
								$('#jam_selesai').val(jamSelesai);
								$('#ruang').val(ruang);

								// Auto-fill tahun_akademik from semester code
								var semCode = String($radio.data('kelas-semester'));
								if (semCode && semCode.length >= 5) {
									var year = parseInt(semCode.substring(0, 4));
									var term = semCode.substring(4, 5);
									if (term === '1') {
										$('#tahun_akademik').val((year - 1) + '/' + year + ' Ganjil');
									} else {
										$('#tahun_akademik').val(year + '/' + (year + 1) + ' Genap');
									}
								}
							});
						} else {
							$apiKelasContainer.html('<div class="alert alert-warning mb-0"><i class="bi bi-info-circle me-1"></i> Tidak ada data kelas dari API untuk mata kuliah ini.</div>');
						}
					},
					error: function(xhr, status, error) {
						$apiKelasLoading.hide();
						$apiKelasContainer.html('<div class="alert alert-danger mb-0"><i class="bi bi-exclamation-triangle me-1"></i> Gagal memuat data kelas dari API.</div>');
						console.error('Error fetching API kelas:', error);
					}
				});
			}
		});
	});
</script>
<?= $this->endSection() ?>
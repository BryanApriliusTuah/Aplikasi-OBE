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
										<option value="<?= $mk['id'] ?>" data-kode="<?= esc($mk['kode_mk']) ?>" <?= old('mata_kuliah_id', $jadwal['mata_kuliah_id']) == $mk['id'] ? 'selected' : '' ?>>
											[SMT <?= esc($mk['semester']) ?>] <?= esc($mk['kode_mk']) ?> - <?= esc($mk['nama_mk']) ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<!-- API Kelas Info -->
							<?php if (!empty($jadwal['kelas_id'])): ?>
								<div class="col-12">
									<div class="alert alert-info mb-0">
										<i class="bi bi-cloud-check me-1"></i>
										<strong>Data API SIUBER:</strong>
										Kelas ID: <?= esc($jadwal['kelas_id']) ?>
										<?php if (!empty($jadwal['kelas_jenis'])): ?>
											&bull; <?= esc($jadwal['kelas_jenis']) ?>
										<?php endif; ?>
										<?php if (!empty($jadwal['kelas_semester'])): ?>
											<?php $year = intval(substr($jadwal['kelas_semester'], 0, 4)); ?>
											<?php $term = substr($jadwal['kelas_semester'], 4, 1); ?>
											&bull; Semester: <?= $year . ($term === '1' ? ' Ganjil' : ' Genap') ?>
										<?php endif; ?>
										<?php if (!empty($jadwal['kelas_status'])): ?>
											&bull; Status: <?= esc($jadwal['kelas_status']) ?>
										<?php endif; ?>
										<?php if (!empty($jadwal['total_mahasiswa'])): ?>
											&bull; <i class="bi bi-people"></i> <?= (int) $jadwal['total_mahasiswa'] ?> mahasiswa
										<?php endif; ?>
									</div>
								</div>
							<?php endif; ?>

							<!-- API Kelas Selection (for re-sync) -->
							<div class="col-12" id="api-kelas-section" style="display: none;">
								<label class="form-label">Kelas dari API <span class="badge bg-info">SIUBER</span></label>
								<div id="api-kelas-loading" class="text-muted small" style="display: none;">
									<div class="spinner-border spinner-border-sm me-1" role="status"></div> Memuat data kelas dari API...
								</div>
								<div id="api-kelas-container"></div>
							</div>
							<input type="hidden" id="kelas_id" name="kelas_id" value="<?= old('kelas_id', $jadwal['kelas_id'] ?? '') ?>">
							<input type="hidden" id="kelas_jenis" name="kelas_jenis" value="<?= old('kelas_jenis', $jadwal['kelas_jenis'] ?? '') ?>">
							<input type="hidden" id="kelas_semester" name="kelas_semester" value="<?= old('kelas_semester', $jadwal['kelas_semester'] ?? '') ?>">
							<input type="hidden" id="mk_kurikulum_kode" name="mk_kurikulum_kode" value="<?= old('mk_kurikulum_kode', $jadwal['mk_kurikulum_kode'] ?? '') ?>">
							<input type="hidden" id="total_mahasiswa" name="total_mahasiswa" value="<?= old('total_mahasiswa', $jadwal['total_mahasiswa'] ?? '') ?>">

							<div class="col-md-6">
								<label for="program_studi_kode" class="form-label">Program Studi</label>
								<select class="form-select" id="program_studi_kode" name="program_studi_kode" required>
									<option value="">-- Pilih Program Studi --</option>
									<?php foreach ($program_studi_list as $prodi): ?>
										<option value="<?= esc($prodi['kode']) ?>" <?= old('program_studi_kode', $jadwal['program_studi_kode'] ?? '') == $prodi['kode'] ? 'selected' : '' ?>>
											<?= esc($prodi['nama_resmi']) ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>
							<div class="col-md-6">
								<label for="tahun_akademik" class="form-label">Tahun Akademik</label>
								<?php $currentTahunAkademik = old('tahun_akademik', $jadwal['tahun_akademik']); ?>
								<select class="form-select" id="tahun_akademik" name="tahun_akademik" required>
									<option value="">-- Pilih Tahun Akademik --</option>
									<?php foreach ($tahun_akademik_list as $tahun): ?>
										<option value="<?= esc($tahun) ?>" <?= $currentTahunAkademik === $tahun ? 'selected' : '' ?>>
											<?= esc($tahun) ?>
										</option>
									<?php endforeach; ?>
									<?php
									// If current value isn't in the master list, show it as-is so data isn't lost
									if ($currentTahunAkademik && !in_array($currentTahunAkademik, $tahun_akademik_list)):
									?>
										<option value="<?= esc($currentTahunAkademik) ?>" selected>
											<?= esc($currentTahunAkademik) ?> (tidak ada di master)
										</option>
									<?php endif; ?>
								</select>
								<?php if (empty($tahun_akademik_list)): ?>
									<div class="form-text text-warning">
										<i class="bi bi-exclamation-triangle"></i>
										Belum ada tahun akademik. <a href="<?= base_url('admin/tahun-akademik/create') ?>" target="_blank">Tambahkan di sini</a>.
									</div>
								<?php endif; ?>
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
								<select class="form-select" id="dosen_leader" name="dosen_leader" required>
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
									foreach ($old_members as $member_id): ?>
										<div class="d-flex gap-2 mb-2 align-items-center">
											<select class="form-select select2-dosen-member" name="dosen_members[]">
												<option value="">-- Pilih Dosen Pengampu --</option>
												<?php foreach ($dosen_list as $dosen): ?>
													<option value="<?= $dosen['id'] ?>" <?= $member_id == $dosen['id'] ? 'selected' : '' ?>>
														<?= esc($dosen['nama_lengkap']) ?>
													</option>
												<?php endforeach; ?>
											</select>
											<button type="button" class="btn btn-outline-danger btn-sm flex-shrink-0 btn-remove-member"><i class="bi bi-trash"></i></button>
										</div>
									<?php endforeach; ?>
								</div>
								<button type="button" class="btn btn-outline-secondary btn-sm mt-2" id="btn-add-member">
									<i class="bi bi-plus"></i> Tambah Dosen Pengampu
								</button>
							</div>
						</div>
					</div>

					<div class="card-footer text-end">
						<a href="<?= base_url('admin/mengajar') ?>" class="btn btn-secondary">Batal</a>
						<button type="button" class="btn btn-info text-white" id="btn-resync-kelas">
							<i class="bi bi-cloud-arrow-down"></i> Re-sync Kelas
						</button>
						<button type="submit" class="btn btn-primary">Simpan Perubahan</button>
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

		$('#dosen_leader').select2({
			theme: 'bootstrap-5',
			placeholder: '-- Pilih Dosen Koordinator --',
			width: '100%'
		});

		// Initialize Select2 on existing member rows
		$('.select2-dosen-member').each(function() {
			$(this).select2({
				theme: 'bootstrap-5',
				placeholder: '-- Pilih Dosen Pengampu --',
				width: '100%'
			});
		});

		// Remove member row
		$(document).on('click', '.btn-remove-member', function() {
			var $row = $(this).closest('.d-flex');
			$row.find('select').select2('destroy');
			$row.remove();
		});

		// All dosen for member dropdowns
		var allDosen = <?= json_encode(array_map(fn($d) => ['id' => $d['id'], 'nama_lengkap' => $d['nama_lengkap']], $dosen_list)) ?>;

		function addMemberRow() {
			var html = '<option value="">-- Pilih Dosen Pengampu --</option>';
			allDosen.forEach(function(d) {
				html += '<option value="' + d.id + '">' + d.nama_lengkap + '</option>';
			});

			var $row = $('<div class="d-flex gap-2 mb-2 align-items-center"></div>');
			var $select = $('<select class="form-select select2-dosen-member" name="dosen_members[]">' + html + '</select>');
			var $removeBtn = $('<button type="button" class="btn btn-outline-danger btn-sm flex-shrink-0 btn-remove-member"><i class="bi bi-trash"></i></button>');

			$row.append($select).append($removeBtn);
			$('#dosen-members-container').append($row);

			$select.select2({
				theme: 'bootstrap-5',
				placeholder: '-- Pilih Dosen Pengampu --',
				width: '100%'
			});
		}

		$('#btn-add-member').on('click', function() {
			addMemberRow();
		});

		// API kelas elements
		var $apiKelasSection = $('#api-kelas-section');
		var $apiKelasLoading = $('#api-kelas-loading');
		var $apiKelasContainer = $('#api-kelas-container');

		function fetchApiKelas() {
			var $selectedOption = $('#mata_kuliah_id').find(':selected');
			var kodeMk = $selectedOption.data('kode') || '';

			if (!kodeMk) {
				$apiKelasContainer.html('<div class="alert alert-warning mb-0">Pilih mata kuliah terlebih dahulu.</div>');
				return;
			}

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

					if (data.success && data.data.length > 0) {
						var currentKelasId = $('#kelas_id').val();
						var html = '<div class="list-group">';
						$.each(data.data, function(index, kelas) {
							var year = parseInt(String(kelas.kelas.klsSemester).substring(0, 4));
							var semester = '';
							var term = String(kelas.kelas.klsSemester).substring(4, 5);
							if (term === '1') {
								semester = year + ' Ganjil';
							} else {
								semester = year + ' Genap';
							}
							var totalMhs = kelas.mahasiswa.mhsTotal || 0;
							var isSelected = String(kelas.kelas.klsId) === String(currentKelasId);
							html += '<label class="list-group-item list-group-item-action d-flex align-items-center gap-3 ' + (isSelected ? 'active' : '') + '" style="cursor: pointer;">' +
								'<input type="radio" name="api_kelas_select" class="form-check-input mt-0" value="' + index + '"' +
								(isSelected ? ' checked' : '') +
								' data-kelas-id="' + (kelas.kelas.klsId || '') + '"' +
								' data-kelas-nama="' + (kelas.kelas.klsNama || '') + '"' +
								' data-kelas-jenis="' + (kelas.kelas.klsJenis || '') + '"' +
								' data-kelas-semester="' + semester + '"' +
								' data-kelas-status="' + (kelas.kelas.klsStatus || '') + '"' +
								' data-mk-kode="' + (kelas.mata_kuliah.mkKode || '') + '"' +
								' data-total-mhs="' + totalMhs + '">' +
								'<div class="flex-grow-1">' +
								'<div class="fw-semibold">Kelas ' + (kelas.kelas.klsNama || '-') + (isSelected ? ' (Saat ini)' : '') + '</div>' +
								'<div class="small ' + (isSelected ? '' : 'text-muted') + '">' +
								(kelas.kelas.klsJenis || '-') + ' &bull; Semester: ' + semester + ' &bull; ' +
								'Status: <span class="badge ' + (kelas.kelas.klsStatus === 'Aktif' ? 'bg-success' : 'bg-secondary') + '">' + (kelas.kelas.klsStatus || '-') + '</span> &bull; ' +
								'<i class="bi bi-people"></i> ' + totalMhs + ' mahasiswa' +
								'</div></div></label>';
						});
						html += '</div>';
						$apiKelasContainer.html(html);

						$apiKelasContainer.off('change', 'input[name="api_kelas_select"]').on('change', 'input[name="api_kelas_select"]', function() {
							var $radio = $(this);
							$('#kelas').val($radio.data('kelas-nama'));
							$('#kelas_id').val($radio.data('kelas-id'));
							$('#kelas_jenis').val($radio.data('kelas-jenis'));
							$('#kelas_semester').val($radio.data('kelas-semester'));
							$('#mk_kurikulum_kode').val($radio.data('mk-kode'));
							$('#total_mahasiswa').val($radio.data('total-mhs'));

							$apiKelasContainer.find('label').removeClass('active');
							$radio.closest('label').addClass('active');

							var semCode = String($radio.data('kelas-semester'));
							if (semCode && semCode.length >= 5) {
								var year = parseInt(semCode.substring(0, 4));
								var term = semCode.substring(4, 5);
								if (term === '1') {
									$('#tahun_akademik').val(year + ' Ganjil');
								} else {
									$('#tahun_akademik').val(year + ' Genap');
								}
							}
						});
					} else {
						$apiKelasContainer.html('<div class="alert alert-warning mb-0"><i class="bi bi-info-circle me-1"></i> Tidak ada data kelas dari API untuk mata kuliah ini.</div>');
					}
				},
				error: function() {
					$apiKelasLoading.hide();
					$apiKelasContainer.html('<div class="alert alert-danger mb-0"><i class="bi bi-exclamation-triangle me-1"></i> Gagal memuat data kelas dari API.</div>');
				}
			});
		}

		// Re-sync button
		$('#btn-resync-kelas').on('click', function() {
			fetchApiKelas();
		});
	});
</script>
<?= $this->endSection() ?>
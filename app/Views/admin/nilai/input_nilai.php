<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
	<div class="row justify-content-center">
		<div class="col-lg-12">
			<a href="<?= base_url('admin/nilai') ?>" class="btn btn-outline-primary btn-sm my-4"><i class="bi bi-arrow-left"></i> Kembali ke Jadwal</a>

			<div class="card shadow-sm">
				<div class="card-header bg-light">
					<h4 class="mb-0">Input Nilai: <?= esc($jadwal['nama_mk']) ?></h4>
					<small class="text-muted">
						Kelas: <?= esc($jadwal['kelas']) ?> | Tahun Akademik: <?= esc($jadwal['tahun_akademik']) ?> | Dosen: <?= esc($jadwal['dosen_ketua'] ?? 'N/A') ?>
					</small>
				</div>
				<div class="card-body">
					<?php if (empty($mahasiswa_list) || empty($cpmk_list)): ?>
						<div class="alert alert-warning">
							Tidak ditemukan data mahasiswa atau CPMK untuk mata kuliah ini. Pastikan CPMK sudah dipetakan dan ada data mahasiswa di program studi terkait.
						</div>
					<?php else: ?>
						<form action="<?= base_url('admin/nilai/save-nilai/' . $jadwal['id']) ?>" method="post">
							<?= csrf_field() ?>
							<div class="table-responsive">
								<table class="table table-bordered table-hover" style="min-width: 800px;">
									<thead class="table-light">
										<tr>
											<th class="text-center align-middle" style="width: 50px;">No</th>
											<th class="align-middle" style="width: 120px;">NIM</th>
											<th class="align-middle">Nama Mahasiswa</th>
											<?php foreach ($cpmk_list as $cpmk) : ?>
												<th class="text-center align-middle" title="<?= esc($cpmk['deskripsi']) ?>">
													<?= esc($cpmk['kode_cpmk']) ?>
												</th>
											<?php endforeach; ?>
										</tr>
									</thead>
									<tbody>
										<?php foreach ($mahasiswa_list as $index => $mahasiswa) : ?>
											<tr>
												<td class="text-center"><?= $index + 1 ?></td>
												<td><?= esc($mahasiswa['nim']) ?></td>
												<td><?= esc($mahasiswa['nama_lengkap']) ?></td>
												<?php foreach ($cpmk_list as $cpmk) : ?>
													<td>
														<input
															type="number"
															class="form-control form-control-sm"
															name="nilai[<?= $mahasiswa['id'] ?>][<?= $cpmk['id'] ?>]"
															min="0" max="100" step="0.01"
															value="<?= esc($existing_scores[$mahasiswa['id']][$cpmk['id']] ?? '') ?>">
													</td>
												<?php endforeach; ?>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
							<div class="text-end mt-3">
								<button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Semua Nilai</button>
							</div>
						</form>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?= $this->endSection() ?>
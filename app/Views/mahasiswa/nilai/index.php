<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
	<div>
		<h2 class="mb-1">Daftar Nilai</h2>
		<p class="text-muted">Riwayat nilai seluruh mata kuliah yang telah diambil</p>
	</div>
</div>

<?php if (session()->getFlashdata('success')): ?>
	<div class="alert alert-success alert-dismissible fade show" role="alert">
		<?= session()->getFlashdata('success') ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
	<div class="alert alert-danger alert-dismissible fade show" role="alert">
		<?= session()->getFlashdata('error') ?>
		<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
	</div>
<?php endif; ?>

<div class="card">
	<div class="card-body">
		<?php if (empty($nilaiList)): ?>
			<div class="text-center py-5 text-muted">
				<i class="bi bi-inbox" style="font-size: 4rem;"></i>
				<p class="mt-3 mb-0">Belum ada data nilai tersedia</p>
				<small>Nilai akan muncul setelah dosen menginput nilai Anda</small>
			</div>
		<?php else: ?>
			<!-- Filter & Search -->
			<div class="row mb-3">
				<div class="col-md-4">
					<input type="text" id="searchInput" class="form-control" placeholder="Cari mata kuliah...">
				</div>
				<div class="col-md-3">
					<select id="filterTahun" class="form-select">
						<option value="">Semua Tahun Akademik</option>
						<?php
						$tahunList = array_unique(array_column($nilaiList, 'tahun_akademik'));
						rsort($tahunList);
						foreach ($tahunList as $tahun):
						?>
							<option value="<?= esc($tahun) ?>"><?= esc($tahun) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="col-md-3">
					<select id="filterStatus" class="form-select">
						<option value="">Semua Status</option>
						<option value="Lulus">Lulus</option>
						<option value="Tidak Lulus">Tidak Lulus</option>
						<option value="Diproses">Diproses</option>
					</select>
				</div>
			</div>

			<div class="table-responsive">
				<table class="table table-hover" id="nilaiTable">
					<thead class="table-light">
						<tr>
							<th>No</th>
							<th>Kode MK</th>
							<th>Mata Kuliah</th>
							<th>SKS</th>
							<th>Semester</th>
							<th>Kelas</th>
							<th>Tahun Akademik</th>
							<th>Nilai Akhir</th>
							<th>Grade</th>
							<th>Status</th>
							<th>Aksi</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$no = 1;
						$totalSks = 0;
						$totalNilai = 0;
						$sksLulus = 0;

						foreach ($nilaiList as $nilai):
							if ($nilai['status_kelulusan'] == 'Lulus') {
								$sksLulus += $nilai['sks'];
								$totalNilai += $nilai['nilai_akhir'] * $nilai['sks'];
								$totalSks += $nilai['sks'];
							}
						?>
							<tr data-tahun="<?= esc($nilai['tahun_akademik']) ?>" data-status="<?= esc($nilai['status_kelulusan']) ?>">
								<td><?= $no++ ?></td>
								<td><span class="badge bg-secondary"><?= esc($nilai['kode_mk']) ?></span></td>
								<td><?= esc($nilai['nama_mk']) ?></td>
								<td><?= esc($nilai['sks']) ?></td>
								<td><?= esc($nilai['semester']) ?></td>
								<td><?= esc($nilai['kelas']) ?></td>
								<td><?= esc($nilai['tahun_akademik']) ?></td>
								<td>
									<?= $nilai['nilai_akhir'] ? '<strong>' . number_format($nilai['nilai_akhir'], 2) . '</strong>' : '<span class="text-muted">-</span>' ?>
								</td>
								<td>
									<?php if ($nilai['nilai_huruf']): ?>
										<?php
										$gradeClass = 'bg-primary';
										if (in_array($nilai['nilai_huruf'], ['A', 'A-'])) $gradeClass = 'bg-success';
										elseif (in_array($nilai['nilai_huruf'], ['B+', 'B', 'B-'])) $gradeClass = 'bg-info';
										elseif (in_array($nilai['nilai_huruf'], ['C+', 'C'])) $gradeClass = 'bg-warning';
										elseif (in_array($nilai['nilai_huruf'], ['D', 'E'])) $gradeClass = 'bg-danger';
										?>
										<span class="badge <?= $gradeClass ?>"><?= esc($nilai['nilai_huruf']) ?></span>
									<?php else: ?>
										<span class="text-muted">-</span>
									<?php endif; ?>
								</td>
								<td>
									<?php if ($nilai['status_kelulusan'] == 'Lulus'): ?>
										<span class="badge bg-success"><i class="bi bi-check-circle"></i> Lulus</span>
									<?php elseif ($nilai['status_kelulusan'] == 'Tidak Lulus'): ?>
										<span class="badge bg-danger"><i class="bi bi-x-circle"></i> Tidak Lulus</span>
									<?php else: ?>
										<span class="badge bg-warning"><i class="bi bi-hourglass-split"></i> Diproses</span>
									<?php endif; ?>
								</td>
								<td>
									<a href="<?= base_url('mahasiswa/nilai/detail/' . $nilai['jadwal_id']) ?>"
										class="btn btn-sm btn-outline-primary"
										data-bs-toggle="tooltip"
										title="Lihat detail nilai CPMK">
										<i class="bi bi-eye"></i> Detail
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	$(document).ready(function() {
		// Search functionality
		$('#searchInput').on('keyup', function() {
			filterTable();
		});

		// Filter by year
		$('#filterTahun').on('change', function() {
			filterTable();
		});

		// Filter by status
		$('#filterStatus').on('change', function() {
			filterTable();
		});

		function filterTable() {
			var searchValue = $('#searchInput').val().toLowerCase();
			var tahunValue = $('#filterTahun').val();
			var statusValue = $('#filterStatus').val();

			$('#nilaiTable tbody tr').filter(function() {
				var text = $(this).text().toLowerCase();
				var tahun = $(this).data('tahun');
				var status = $(this).data('status');

				var matchSearch = text.indexOf(searchValue) > -1;
				var matchTahun = tahunValue === '' || tahun === tahunValue;
				var matchStatus = statusValue === '' || status === statusValue;

				$(this).toggle(matchSearch && matchTahun && matchStatus);
			});
		}
	});
</script>
<?= $this->endSection() ?>
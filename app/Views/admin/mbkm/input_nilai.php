<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-4">
	<div class="d-flex justify-content-between align-items-center my-4">
		<h2 class="fw-bold">Input Nilai MBKM</h2>
		<a href="<?= base_url('admin/mbkm') ?>" class="btn btn-outline-secondary">
			<i class="bi bi-arrow-left"></i> Kembali
		</a>
	</div>

	<?php if (session()->getFlashdata('success')): ?>
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			<?= esc(session()->getFlashdata('success')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>

	<?php if (session()->getFlashdata('error')): ?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<?= esc(session()->getFlashdata('error')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>

	<!-- Informasi Kegiatan -->
	<div class="card shadow-sm mb-4">
		<div class="card-header bg-primary text-white">
			<h5 class="mb-0"><i class="bi bi-info-circle"></i> Informasi Kegiatan</h5>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-6">
					<table class="table table-sm">
						<tr>
							<th width="35%">Judul Kegiatan</th>
							<td><?= esc($kegiatan['judul_kegiatan']) ?></td>
						</tr>
						<tr>
							<th>Jenis Kegiatan</th>
							<td><?= esc($kegiatan['nama_kegiatan']) ?></td>
						</tr>
						<tr>
							<th>Mahasiswa</th>
							<td><?= esc($kegiatan['nama_mahasiswa']) ?> (<?= esc($kegiatan['nim']) ?>)</td>
						</tr>
						<tr>
							<th>Program Studi</th>
							<td><?= esc($kegiatan['program_studi']) ?></td>
						</tr>
					</table>
				</div>
				<div class="col-md-6">
					<table class="table table-sm">
						<tr>
							<th width="35%">Tempat Kegiatan</th>
							<td><?= esc($kegiatan['tempat_kegiatan']) ?></td>
						</tr>
						<tr>
							<th>Periode</th>
							<td>
								<?= date('d/m/Y', strtotime($kegiatan['tanggal_mulai'])) ?> - 
								<?= date('d/m/Y', strtotime($kegiatan['tanggal_selesai'])) ?>
								<span class="badge bg-info"><?= $kegiatan['durasi_minggu'] ?> minggu</span>
							</td>
						</tr>
						<tr>
							<th>Dosen Pembimbing</th>
							<td><?= esc($kegiatan['nama_dosen_pembimbing'] ?? '-') ?></td>
						</tr>
						<tr>
							<th>SKS Dikonversi</th>
							<td><span class="badge bg-success"><?= $kegiatan['sks_dikonversi'] ?> SKS</span></td>
						</tr>
					</table>
				</div>
			</div>
		</div>
	</div>

	<!-- Form Penilaian -->
	<div class="card shadow-sm">
		<div class="card-header bg-success text-white">
			<h5 class="mb-0"><i class="bi bi-pencil-square"></i> Form Penilaian</h5>
		</div>
		<div class="card-body">
			<form action="<?= base_url('admin/mbkm/save-nilai/' . $kegiatan['id']) ?>" method="POST" id="formNilai">
				<?= csrf_field() ?>
				
				<?php if (empty($komponen)): ?>
					<div class="alert alert-warning">
						<i class="bi bi-exclamation-triangle"></i> 
						Belum ada komponen penilaian untuk jenis kegiatan ini. Silakan hubungi administrator.
					</div>
				<?php else: ?>
					<div class="table-responsive">
						<table class="table table-bordered table-hover">
							<thead class="table-light">
								<tr>
									<th width="5%" class="text-center">#</th>
									<th width="30%">Komponen Penilaian</th>
									<th width="35%">Deskripsi</th>
									<th width="10%" class="text-center">Bobot (%)</th>
									<th width="15%" class="text-center">Nilai (0-100)</th>
									<th width="5%" class="text-center">Skor</th>
								</tr>
							</thead>
							<tbody>
								<?php 
								$no = 1;
								$total_bobot = 0;
								foreach ($komponen as $k): 
									$nilai_existing = $nilai_map[$k['id']] ?? null;
									$nilai = $nilai_existing['nilai'] ?? 0;
									$total_bobot += $k['bobot'];
								?>
								<tr>
									<td class="text-center"><?= $no++ ?></td>
									<td>
										<strong><?= esc($k['nama_komponen']) ?></strong>
										<input type="hidden" name="komponen_id[]" value="<?= $k['id'] ?>">
									</td>
									<td class="small text-muted"><?= esc($k['deskripsi'] ?? '-') ?></td>
									<td class="text-center">
										<span class="badge bg-primary"><?= number_format($k['bobot'], 0) ?>%</span>
										<input type="hidden" class="bobot" value="<?= $k['bobot'] ?>">
									</td>
									<td>
										<input type="number" 
											   class="form-control form-control-sm text-center nilai-input" 
											   name="nilai[]" 
											   value="<?= $nilai ?>"
											   min="0" 
											   max="100" 
											   step="0.01"
											   data-komponen-id="<?= $k['id'] ?>"
											   required>
									</td>
									<td class="text-center">
										<strong class="skor-display">0.00</strong>
									</td>
								</tr>
								<tr>
									<td colspan="6">
										<textarea class="form-control form-control-sm" 
												  name="catatan[]" 
												  rows="2" 
												  placeholder="Catatan penilaian untuk <?= esc($k['nama_komponen']) ?> (opsional)"><?= esc($nilai_existing['catatan'] ?? '') ?></textarea>
									</td>
								</tr>
								<?php endforeach; ?>
							</tbody>
							<tfoot class="table-light">
								<tr>
									<th colspan="3" class="text-end">Total</th>
									<th class="text-center">
										<span class="badge bg-<?= $total_bobot == 100 ? 'success' : 'danger' ?>" id="totalBobot">
											<?= number_format($total_bobot, 0) ?>%
										</span>
									</th>
									<th colspan="2" class="text-center">
										<h5 class="mb-0">
											<span class="badge bg-success" id="nilaiAkhir">0.00</span>
										</h5>
									</th>
								</tr>
							</tfoot>
						</table>
					</div>

					<?php if ($total_bobot != 100): ?>
						<div class="alert alert-warning">
							<i class="bi bi-exclamation-triangle"></i> 
							<strong>Perhatian:</strong> Total bobot komponen penilaian bukan 100%. Hubungi administrator untuk memperbaiki komponen penilaian.
						</div>
					<?php endif; ?>

					<div class="alert alert-info">
						<i class="bi bi-info-circle"></i> <strong>Informasi Perhitungan:</strong>
						<ul class="mb-0 mt-2">
							<li>Nilai akhir dihitung dari: Σ (Nilai × Bobot / 100)</li>
							<li>Rentang nilai: 0 - 100</li>
							<li>Konversi nilai huruf:
								<span class="badge bg-success">A (≥85)</span>
								<span class="badge bg-success">AB (80-84)</span>
								<span class="badge bg-info">B (75-79)</span>
								<span class="badge bg-info">BC (70-74)</span>
								<span class="badge bg-warning">C (65-69)</span>
								<span class="badge bg-danger">D (50-64)</span>
								<span class="badge bg-danger">E (<50)</span>
							</li>
							<li>Minimum kelulusan: C (65)</li>
						</ul>
					</div>

					<hr>

					<div class="d-flex justify-content-end gap-2">
						<a href="<?= base_url('admin/mbkm') ?>" class="btn btn-secondary">
							<i class="bi bi-x-circle"></i> Batal
						</a>
						<button type="submit" class="btn btn-success">
							<i class="bi bi-save"></i> Simpan Nilai
						</button>
					</div>
				<?php endif; ?>
			</form>
		</div>
	</div>

	<!-- Preview Nilai Akhir -->
	<?php if (!empty($komponen)): ?>
	<div class="card shadow-sm mt-4">
		<div class="card-header bg-warning">
			<h5 class="mb-0"><i class="bi bi-eye"></i> Preview Nilai Akhir</h5>
		</div>
		<div class="card-body">
			<div class="row text-center">
				<div class="col-md-3">
					<h6 class="text-muted">Nilai Angka</h6>
					<h2 id="previewNilaiAngka" class="text-primary">0.00</h2>
				</div>
				<div class="col-md-3">
					<h6 class="text-muted">Nilai Huruf</h6>
					<h2 id="previewNilaiHuruf" class="text-success">-</h2>
				</div>
				<div class="col-md-3">
					<h6 class="text-muted">Status</h6>
					<h2 id="previewStatus" class="text-info">-</h2>
				</div>
				<div class="col-md-3">
					<h6 class="text-muted">Keterangan</h6>
					<h2 id="previewKeterangan" class="text-secondary">-</h2>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		// Function to calculate scores
		function hitungNilai() {
			let totalNilai = 0;
			const rows = document.querySelectorAll('tbody tr:nth-child(odd)');
			
			rows.forEach(row => {
				const nilaiInput = row.querySelector('.nilai-input');
				const bobotInput = row.querySelector('.bobot');
				const skorDisplay = row.querySelector('.skor-display');
				
				if (nilaiInput && bobotInput && skorDisplay) {
					const nilai = parseFloat(nilaiInput.value) || 0;
					const bobot = parseFloat(bobotInput.value) || 0;
					const skor = (nilai * bobot) / 100;
					
					skorDisplay.textContent = skor.toFixed(2);
					totalNilai += skor;
				}
			});
			
			// Update nilai akhir
			document.getElementById('nilaiAkhir').textContent = totalNilai.toFixed(2);
			document.getElementById('previewNilaiAngka').textContent = totalNilai.toFixed(2);
			
			// Konversi ke nilai huruf
			let nilaiHuruf = '-';
			let status = '-';
			let keterangan = '-';
			
			if (totalNilai >= 85) {
				nilaiHuruf = 'A';
				status = 'Lulus';
				keterangan = 'Sangat Baik';
			} else if (totalNilai >= 80) {
				nilaiHuruf = 'AB';
				status = 'Lulus';
				keterangan = 'Baik Sekali';
			} else if (totalNilai >= 75) {
				nilaiHuruf = 'B';
				status = 'Lulus';
				keterangan = 'Baik';
			} else if (totalNilai >= 70) {
				nilaiHuruf = 'BC';
				status = 'Lulus';
				keterangan = 'Cukup Baik';
			} else if (totalNilai >= 65) {
				nilaiHuruf = 'C';
				status = 'Lulus';
				keterangan = 'Cukup';
			} else if (totalNilai >= 50) {
				nilaiHuruf = 'D';
				status = 'Tidak Lulus';
				keterangan = 'Kurang';
			} else {
				nilaiHuruf = 'E';
				status = 'Tidak Lulus';
				keterangan = 'Gagal';
			}
			
			document.getElementById('previewNilaiHuruf').textContent = nilaiHuruf;
			document.getElementById('previewStatus').textContent = status;
			document.getElementById('previewKeterangan').textContent = keterangan;
			
			// Change status color
			const statusElement = document.getElementById('previewStatus');
			statusElement.className = status === 'Lulus' ? 'text-success' : 'text-danger';
		}
		
		// Add event listeners to all nilai inputs
		const nilaiInputs = document.querySelectorAll('.nilai-input');
		nilaiInputs.forEach(input => {
			input.addEventListener('input', hitungNilai);
			input.addEventListener('change', hitungNilai);
		});
		
		// Initial calculation
		hitungNilai();
		
		// Form validation
		document.getElementById('formNilai')?.addEventListener('submit', function(e) {
			const nilaiInputs = document.querySelectorAll('.nilai-input');
			let isValid = true;
			
			nilaiInputs.forEach(input => {
				const nilai = parseFloat(input.value);
				if (isNaN(nilai) || nilai < 0 || nilai > 100) {
					isValid = false;
					input.classList.add('is-invalid');
				} else {
					input.classList.remove('is-invalid');
				}
			});
			
			if (!isValid) {
				e.preventDefault();
				alert('Pastikan semua nilai berada dalam rentang 0-100');
			}
		});
	});
</script>
<?= $this->endSection() ?>
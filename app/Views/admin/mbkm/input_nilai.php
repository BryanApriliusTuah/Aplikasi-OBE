<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('css') ?>
<style>
	/* Page-specific modern styles */
	.page-header {
		background: linear-gradient(135deg, #f8fafc 0%, #e3eafc 100%);
		border: 1px solid var(--modern-table-border, #e5e7eb);
		border-radius: 0.75rem;
		padding: 1.5rem 2rem;
		margin-bottom: 1.5rem;
	}

	.page-header h2 {
		color: #1e293b;
		font-size: 1.5rem;
		letter-spacing: -0.01em;
	}

	.info-grid {
		display: grid;
		grid-template-columns: 1fr 1fr;
		gap: 1.5rem;
	}

	@media (max-width: 768px) {
		.info-grid {
			grid-template-columns: 1fr;
		}
	}

	.info-item {
		display: flex;
		align-items: flex-start;
		padding: 0.75rem 0;
		border-bottom: 1px solid #f1f5f9;
	}

	.info-item:last-child {
		border-bottom: none;
	}

	.info-label {
		flex: 0 0 140px;
		font-size: 0.8125rem;
		font-weight: 600;
		color: #64748b;
		text-transform: uppercase;
		letter-spacing: 0.025em;
	}

	.info-value {
		flex: 1;
		font-size: 0.9375rem;
		color: #1e293b;
		font-weight: 500;
	}

	/* Capaian badge */
	.capaian-card {
		display: flex;
		align-items: center;
		gap: 1rem;
		padding: 1rem 1.25rem;
		background: linear-gradient(135deg, #f0f9ff 0%, #f8fafc 100%);
		border: 1px solid #e0f2fe;
		border-radius: 0.5rem;
		transition: all 0.2s ease;
	}

	.capaian-card:hover {
		box-shadow: 0 2px 8px rgba(13, 110, 253, 0.1);
		transform: translateY(-1px);
	}

	.capaian-card .capaian-badge {
		flex-shrink: 0;
		padding: 0.5rem 1rem;
		border-radius: 0.5rem;
		font-weight: 700;
		font-size: 0.875rem;
		letter-spacing: 0.05em;
	}

	.capaian-card .capaian-badge.cpmk {
		background: linear-gradient(135deg, #224abe, #0d6efd);
		color: white;
	}

	.capaian-card .capaian-badge.cpl {
		background: linear-gradient(135deg, #059669, #10b981);
		color: white;
	}

	.capaian-card .capaian-text strong {
		color: #1e293b;
		font-size: 0.9375rem;
	}

	.capaian-card .capaian-text p {
		color: #64748b;
		font-size: 0.8125rem;
		margin: 0.25rem 0 0 0;
	}

	/* Modern form input */
	.modern-input {
		border: 2px solid #e5e7eb;
		border-radius: 0.75rem;
		padding: 0.875rem 1rem;
		font-size: 1.5rem;
		font-weight: 700;
		text-align: center;
		color: #1e293b;
		transition: all 0.2s ease;
		background: #fafbfc;
	}

	.modern-input:focus {
		border-color: #224abe;
		box-shadow: 0 0 0 4px rgba(34, 74, 190, 0.1);
		background: #fff;
		outline: none;
	}

	.modern-input::placeholder {
		font-size: 0.875rem;
		font-weight: 400;
		color: #94a3b8;
	}

	.modern-textarea {
		border: 2px solid #e5e7eb;
		border-radius: 0.75rem;
		padding: 0.75rem 1rem;
		font-size: 0.875rem;
		color: #1e293b;
		transition: all 0.2s ease;
		background: #fafbfc;
		resize: vertical;
	}

	.modern-textarea:focus {
		border-color: #224abe;
		box-shadow: 0 0 0 4px rgba(34, 74, 190, 0.1);
		background: #fff;
		outline: none;
	}

	/* Preview card */
	.preview-card {
		background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
		border: 1px solid #e5e7eb;
		border-radius: 0.75rem;
		overflow: hidden;
	}

	.preview-header {
		background: linear-gradient(135deg, #e3eafc 0%, #f1f5f9 100%);
		padding: 0.75rem 1.25rem;
		border-bottom: 1px solid #e5e7eb;
	}

	.preview-header strong {
		font-size: 0.8125rem;
		text-transform: uppercase;
		letter-spacing: 0.05em;
		color: #475569;
	}

	.preview-body {
		padding: 1.5rem;
	}

	.preview-stat {
		text-align: center;
		padding: 0.5rem;
	}

	.preview-stat .stat-label {
		font-size: 0.75rem;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.05em;
		color: #94a3b8;
		margin-bottom: 0.5rem;
	}

	.preview-stat .stat-value {
		font-size: 1.75rem;
		font-weight: 800;
		line-height: 1;
		transition: all 0.3s ease;
	}

	.preview-stat .stat-value.primary {
		color: #224abe;
	}

	.preview-stat .stat-value.success {
		color: #059669;
	}

	.preview-stat .stat-value.danger {
		color: #dc2626;
	}

	/* Grade chips */
	.grade-chips {
		display: flex;
		flex-wrap: wrap;
		gap: 0.5rem;
		margin-top: 0.5rem;
	}

	.grade-chip {
		display: inline-flex;
		align-items: center;
		gap: 0.375rem;
		padding: 0.375rem 0.75rem;
		border-radius: 2rem;
		font-size: 0.8125rem;
		font-weight: 600;
		border: 1px solid;
		transition: all 0.2s ease;
	}

	.grade-chip:hover {
		transform: translateY(-1px);
		box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
	}

	.grade-chip.excellent {
		background: #f0fdf4;
		color: #166534;
		border-color: #bbf7d0;
	}

	.grade-chip.good {
		background: #f0f9ff;
		color: #0c4a6e;
		border-color: #bae6fd;
	}

	.grade-chip.fair {
		background: #fffbeb;
		color: #92400e;
		border-color: #fde68a;
	}

	.grade-chip.poor {
		background: #fef2f2;
		color: #991b1b;
		border-color: #fecaca;
	}

	/* Modern form label */
	.modern-label {
		font-size: 0.8125rem;
		font-weight: 600;
		text-transform: uppercase;
		letter-spacing: 0.025em;
		color: #475569;
		margin-bottom: 0.625rem;
	}

	/* Action buttons */
	.btn-modern-save {
		background: linear-gradient(135deg, #059669 0%, #10b981 100%);
		color: white;
		border: none;
		padding: 0.75rem 2rem;
		border-radius: 0.75rem;
		font-weight: 600;
		font-size: 0.9375rem;
		transition: all 0.2s ease;
		box-shadow: 0 2px 8px rgba(5, 150, 105, 0.25);
	}

	.btn-modern-save:hover:not(:disabled) {
		transform: translateY(-1px);
		box-shadow: 0 4px 12px rgba(5, 150, 105, 0.35);
		color: white;
	}

	.btn-modern-save:disabled {
		opacity: 0.5;
		cursor: not-allowed;
	}

	.btn-modern-cancel {
		background: white;
		color: #475569;
		border: 2px solid #e5e7eb;
		padding: 0.75rem 2rem;
		border-radius: 0.75rem;
		font-weight: 600;
		font-size: 0.9375rem;
		transition: all 0.2s ease;
	}

	.btn-modern-cancel:hover {
		background: #f8fafc;
		border-color: #cbd5e1;
		color: #1e293b;
	}

	/* Divider */
	.modern-divider {
		height: 1px;
		background: linear-gradient(90deg, transparent, #e5e7eb, transparent);
		border: none;
		margin: 1.5rem 0;
	}

	/* Mhs chip */
	.mhs-chip {
		display: inline-flex;
		align-items: center;
		gap: 0.375rem;
		padding: 0.25rem 0.75rem;
		background: #f1f5f9;
		border: 1px solid #e2e8f0;
		border-radius: 2rem;
		font-size: 0.8125rem;
		color: #334155;
		margin: 0.125rem 0;
	}

	.mhs-chip .nim {
		color: #94a3b8;
		font-size: 0.75rem;
	}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid px-4">
	<!-- Page Header -->
	<div class="page-header d-flex justify-content-between align-items-center">
		<div>
			<h2 class="fw-bold mb-1">Input Nilai MBKM</h2>
			<p class="text-muted mb-0" style="font-size: 0.875rem;">Penilaian kegiatan Merdeka Belajar Kampus Merdeka</p>
		</div>
		<a href="<?= base_url('admin/mbkm') ?>" class="btn-modern-cancel">
			<i class="bi bi-arrow-left me-1"></i> Kembali
		</a>
	</div>

	<?php if (session()->getFlashdata('success')): ?>
		<div class="alert alert-success alert-dismissible fade show" role="alert">
			<i class="bi bi-check-circle me-2"></i><?= esc(session()->getFlashdata('success')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>

	<?php if (session()->getFlashdata('error')): ?>
		<div class="alert alert-danger alert-dismissible fade show" role="alert">
			<i class="bi bi-exclamation-triangle me-2"></i><?= esc(session()->getFlashdata('error')) ?>
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
		</div>
	<?php endif; ?>

	<!-- Informasi Kegiatan -->
	<div class="modern-filter-wrapper mb-4">
		<div class="modern-filter-header">
			<div class="modern-filter-title">
				<i class="bi bi-info-circle me-1"></i> Informasi Kegiatan
			</div>
		</div>
		<div class="modern-filter-body">
			<div class="info-grid">
				<div>
					<div class="info-item">
						<span class="info-label">Judul Kegiatan</span>
						<span class="info-value"><?= esc($kegiatan['judul_kegiatan']) ?></span>
					</div>
					<div class="info-item">
						<span class="info-label">Jenis Kegiatan</span>
						<span class="info-value"><?= esc($kegiatan['jenis_kegiatan']) ?></span>
					</div>
					<div class="info-item">
						<span class="info-label">Mahasiswa</span>
						<span class="info-value">
							<?php if (!empty($mahasiswa)): ?>
								<?php foreach ($mahasiswa as $mhs): ?>
									<div class="mhs-chip">
										<?= esc($mhs['nama_lengkap']) ?>
										<span class="nim"><?= esc($mhs['nim']) ?></span>
									</div>
								<?php endforeach; ?>
							<?php else: ?>
								<span class="text-muted">-</span>
							<?php endif; ?>
						</span>
					</div>
					<div class="info-item">
						<span class="info-label">Program Studi</span>
						<span class="info-value"><?= esc($mahasiswa[0]['program_studi_kode'] ?? '-') ?></span>
					</div>
				</div>
				<div>
					<div class="info-item">
						<span class="info-label">Tempat Kegiatan</span>
						<span class="info-value"><?= esc($kegiatan['tempat_kegiatan']) ?></span>
					</div>
					<div class="info-item">
						<span class="info-label">Periode</span>
						<span class="info-value">
							<?= date('d/m/Y', strtotime($kegiatan['tanggal_mulai'])) ?> -
							<?= date('d/m/Y', strtotime($kegiatan['tanggal_selesai'])) ?>
							<?php if (!empty($kegiatan['durasi_minggu'])): ?>
								<span class="badge bg-info ms-1" style="font-size: 0.75rem;"><?= $kegiatan['durasi_minggu'] ?> minggu</span>
							<?php endif; ?>
						</span>
					</div>
					<div class="info-item">
						<span class="info-label">Dosen Pembimbing</span>
						<span class="info-value"><?= esc($kegiatan['nama_dosen_pembimbing'] ?? '-') ?></span>
					</div>
					<div class="info-item">
						<span class="info-label">SKS Dikonversi</span>
						<span class="info-value">
							<span class="badge" style="background: linear-gradient(135deg, #059669, #10b981); font-size: 0.8125rem;"><?= $kegiatan['sks_dikonversi'] ?> SKS</span>
						</span>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Capaian Pembelajaran yang Diterapkan -->
	<div class="modern-filter-wrapper mb-4">
		<div class="modern-filter-header">
			<div class="modern-filter-title">
				<i class="bi bi-award me-1"></i> Capaian Pembelajaran
			</div>
		</div>
		<div class="modern-filter-body">
			<?php if ($kegiatan['nilai_type'] === 'cpmk'): ?>
				<div class="capaian-card">
					<span class="capaian-badge cpmk">CPMK</span>
					<div class="capaian-text">
						<strong><?= esc($kegiatan['kode_cpmk'] ?? '-') ?></strong>
						<p><?= esc($kegiatan['cpmk_deskripsi'] ?? '') ?></p>
					</div>
				</div>
			<?php elseif ($kegiatan['nilai_type'] === 'cpl'): ?>
				<div class="capaian-card">
					<span class="capaian-badge cpl">CPL</span>
					<div class="capaian-text">
						<strong><?= esc($kegiatan['kode_cpl'] ?? '-') ?></strong>
						<p><?= esc($kegiatan['cpl_deskripsi'] ?? '') ?></p>
					</div>
				</div>
			<?php else: ?>
				<div class="alert alert-warning mb-0" style="border-radius: 0.5rem;">
					<i class="bi bi-exclamation-triangle me-1"></i> Capaian pembelajaran belum ditentukan untuk kegiatan ini. Silakan edit kegiatan terlebih dahulu.
				</div>
			<?php endif; ?>
		</div>
	</div>

	<!-- Form Penilaian -->
	<div class="modern-filter-wrapper mb-4">
		<div class="modern-filter-header">
			<div class="d-flex justify-content-between align-items-center">
				<div class="modern-filter-title">
					<i class="bi bi-pencil-square me-1"></i> Form Penilaian
				</div>
			</div>
		</div>
		<div class="modern-filter-body">
			<form action="<?= base_url('admin/mbkm/save-nilai/' . $kegiatan['id']) ?>" method="POST" id="formNilai">
				<?= csrf_field() ?>

				<div class="row g-4">
					<div class="col-lg-5">
						<!-- Nilai Input -->
						<div class="mb-4">
							<label for="nilai_angka" class="modern-label">Nilai Akhir (0-100) <span class="text-danger">*</span></label>
							<input type="number"
								class="form-control modern-input"
								id="nilai_angka"
								name="nilai_angka"
								value="<?= esc($nilai_akhir['nilai_angka'] ?? '') ?>"
								min="0"
								max="100"
								step="0.01"
								placeholder="0 - 100"
								required>
						</div>

						<!-- Catatan -->
						<div class="mb-3">
							<label for="catatan" class="modern-label">Catatan <span class="text-muted" style="text-transform: none; font-weight: 400;">(Opsional)</span></label>
							<textarea class="form-control modern-textarea" id="catatan" name="catatan" rows="3"
								placeholder="Tambahkan catatan penilaian jika diperlukan"><?= esc($nilai_akhir['catatan_akhir'] ?? '') ?></textarea>
						</div>
					</div>

					<div class="col-lg-7">
						<!-- Preview Nilai -->
						<div class="preview-card mb-3">
							<div class="preview-header">
								<strong><i class="bi bi-eye me-1"></i> Preview Nilai</strong>
							</div>
							<div class="preview-body">
								<div class="row g-3">
									<div class="col-4">
										<div class="preview-stat">
											<div class="stat-label">Nilai Angka</div>
											<div class="stat-value primary" id="previewNilaiAngka"><?= esc($nilai_akhir['nilai_angka'] ?? '-') ?></div>
										</div>
									</div>
									<div class="col-4">
										<div class="preview-stat">
											<div class="stat-label">Nilai Huruf</div>
											<div class="stat-value success" id="previewNilaiHuruf"><?= esc($nilai_akhir['nilai_huruf'] ?? '-') ?></div>
										</div>
									</div>
									<div class="col-4">
										<div class="preview-stat">
											<div class="stat-label">Status</div>
											<div class="stat-value <?= ($nilai_akhir['status_kelulusan'] ?? '') === 'Lulus' ? 'success' : 'danger' ?>" id="previewStatus">
												<?= esc($nilai_akhir['status_kelulusan'] ?? '-') ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

						<!-- Grade Info -->
						<div class="preview-card">
							<div class="preview-header">
								<strong><i class="bi bi-bar-chart me-1"></i> Konversi Nilai</strong>
							</div>
							<div class="preview-body">
								<?php if (!empty($grade_config)): ?>
									<div class="grade-chips">
										<?php foreach ($grade_config as $grade): ?>
											<?php
											$chipClass = $grade['is_passing'] ? ($grade['min_score'] >= 75 ? 'excellent' : ($grade['min_score'] >= 65 ? 'good' : 'fair')) : 'poor';
											$rangeText = $grade['grade_letter'] . ' (' . number_format($grade['min_score'], 0) . '-' . number_format($grade['max_score'], 0) . ')';
											?>
											<span class="grade-chip <?= $chipClass ?>"><?= esc($rangeText) ?></span>
										<?php endforeach; ?>
									</div>
								<?php else: ?>
									<span class="text-muted" style="font-size: 0.875rem;">Konfigurasi nilai tidak tersedia</span>
								<?php endif; ?>
							</div>
						</div>
					</div>
				</div>

				<hr class="modern-divider">

				<div class="d-flex justify-content-end gap-2">
					<a href="<?= base_url('admin/mbkm') ?>" class="btn-modern-cancel">
						<i class="bi bi-x-circle me-1"></i> Batal
					</a>
					<button type="submit" class="btn-modern-save" <?= empty($kegiatan['nilai_type']) ? 'disabled' : '' ?>>
						<i class="bi bi-save me-1"></i> Simpan Nilai
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	// Dynamic grade configuration from database
	const gradeConfig = <?= json_encode($grade_config ?? []) ?>;

	document.addEventListener('DOMContentLoaded', function() {
		const nilaiInput = document.getElementById('nilai_angka');

		// Function to get grade info from dynamic configuration
		function getGradeInfo(score) {
			if (gradeConfig && gradeConfig.length > 0) {
				for (let i = 0; i < gradeConfig.length; i++) {
					const grade = gradeConfig[i];
					const minScore = parseFloat(grade.min_score);
					const maxScore = parseFloat(grade.max_score);

					if (score >= minScore && score <= maxScore) {
						return {
							letter: grade.grade_letter,
							isPassing: grade.is_passing == 1,
							description: grade.description || ''
						};
					}
				}
			}

			// Fallback if no match found
			return {
				letter: 'E',
				isPassing: false,
				description: 'Gagal'
			};
		}

		// Function to update preview
		function updatePreview() {
			const nilai = parseFloat(nilaiInput.value) || 0;

			document.getElementById('previewNilaiAngka').textContent = nilai.toFixed(2);

			if (nilai > 0) {
				const gradeInfo = getGradeInfo(nilai);
				document.getElementById('previewNilaiHuruf').textContent = gradeInfo.letter;

				const status = gradeInfo.isPassing ? 'Lulus' : 'Tidak Lulus';
				const statusElement = document.getElementById('previewStatus');
				statusElement.textContent = status;
				statusElement.className = gradeInfo.isPassing ? 'stat-value success' : 'stat-value danger';

				// Update letter class
				const hurufElement = document.getElementById('previewNilaiHuruf');
				hurufElement.className = gradeInfo.isPassing ? 'stat-value success' : 'stat-value danger';
			} else {
				document.getElementById('previewNilaiHuruf').textContent = '-';
				document.getElementById('previewNilaiHuruf').className = 'stat-value success';
				document.getElementById('previewStatus').textContent = '-';
				document.getElementById('previewStatus').className = 'stat-value';
			}
		}

		// Event listener for nilai input
		nilaiInput.addEventListener('input', updatePreview);
		nilaiInput.addEventListener('change', updatePreview);

		// Initialize on page load
		updatePreview();

		// Form validation
		document.getElementById('formNilai')?.addEventListener('submit', function(e) {
			const nilai = parseFloat(nilaiInput.value);
			if (isNaN(nilai) || nilai < 0 || nilai > 100) {
				e.preventDefault();
				alert('Nilai harus berada dalam rentang 0-100');
				return false;
			}
		});
	});
</script>
<?= $this->endSection() ?>

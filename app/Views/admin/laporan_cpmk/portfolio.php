<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">
<div class="container-fluid">
	<!-- Action Buttons -->
	<div class="row mb-3 no-print">
		<div class="col-12 d-flex justify-content-between align-items-center">
			<a href="<?= base_url('admin/laporan-cpmk') ?>" class="btn btn-outline-secondary">
				<i class="bi bi-arrow-left"></i> Kembali
			</a>
			<div class="btn-group">
				<button type="button" class="btn btn-success" onclick="exportToPDF()">
					<i class="bi bi-file-earmark-zip"></i> Download ZIP
				</button>
			</div>
		</div>
	</div>

	<!-- Portfolio Content -->
	<div id="portfolio-content" class="card shadow-sm">
		<div class="card-body p-5">
			<!-- Header -->
			<div class="text-center mb-5">
				<h2 class="fw-bold">PORTOFOLIO MATA KULIAH</h2>
			</div>

			<!-- 1. Identitas Mata Kuliah -->
			<div class="section mb-5">
				<h5 class="fw-bold mb-3">1. Identitas Mata Kuliah</h5>
				<div class="modern-table-wrapper">
					<table class="modern-table">
						<tbody>
							<tr>
								<td class="fw-bold" style="width: 30%;">Nama Mata Kuliah</td>
								<td><?= esc($portfolio['identitas']['nama_mata_kuliah']) ?></td>
							</tr>
							<tr>
								<td class="fw-bold">Kode Mata Kuliah</td>
								<td><?= esc($portfolio['identitas']['kode_mata_kuliah']) ?></td>
							</tr>
							<tr>
								<td class="fw-bold">Program Studi</td>
								<td><?= esc($portfolio['identitas']['program_studi']) ?></td>
							</tr>
							<tr>
								<td class="fw-bold">Semester</td>
								<td><?= esc($portfolio['identitas']['semester']) ?></td>
							</tr>
							<tr>
								<td class="fw-bold">Jumlah SKS</td>
								<td><?= esc($portfolio['identitas']['jumlah_sks']) ?></td>
							</tr>
							<tr>
								<td class="fw-bold">Tahun Akademik</td>
								<td><?= esc($portfolio['identitas']['tahun_akademik']) ?></td>
							</tr>
							<tr>
								<td class="fw-bold">Dosen Pengampu</td>
								<td>
									<?php if (!empty($portfolio['identitas']['dosen_pengampu'])): ?>
										<ul class="mb-0">
											<?php foreach ($portfolio['identitas']['dosen_pengampu'] as $dosen): ?>
												<?php
												$title = $dosen['role'] === 'leader' ? 'Dosen Koordinator' : ($dosen['role'] === 'member' ? 'Dosen' : '');
												?>
												<li><?= esc($dosen['nama_lengkap']) ?> (<?= esc($dosen['nip']) ?>) - <?= $title ?></li>
											<?php endforeach; ?>
										</ul>
									<?php else: ?>
										-
									<?php endif; ?>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<!-- 2. Capaian Pembelajaran Mata Kuliah (CPMK) -->
			<div class="section mb-5">
				<h5 class="fw-bold mb-3">2. Capaian Pembelajaran Mata Kuliah (CPMK)</h5>
				<div class="modern-table-wrapper">
					<table class="modern-table">
						<thead>
							<tr>
								<th class="text-center" style="width: 12%;">Kode CPMK</th>
								<th class="text-center" style="width: 38%;">Rumusan CPMK</th>
								<th class="text-center" style="width: 15%;">Keterkaitan dengan CPL</th>
								<th class="text-center" style="width: 17.5%;">Metode Pembelajaran</th>
								<th class="text-center" style="width: 17.5%;">Metode Asesmen</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($portfolio['cpmk'])): ?>
								<?php foreach ($portfolio['cpmk'] as $cpmk): ?>
									<tr>
										<td><?= esc($cpmk['kode_cpmk']) ?></td>
										<td><?= esc($cpmk['deskripsi']) ?></td>
										<td><?= esc($cpmk['keterkaitan_cpl'] ?: '-') ?></td>
										<td><?= esc($cpmk['metode_pembelajaran']) ?></td>
										<td><?= esc($cpmk['metode_asesmen']) ?></td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="5" class="text-center text-muted">Tidak ada data CPMK</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>

			<!-- 3. Rencana dan Realisasi Penilaian CPMK -->
			<div class="section mb-5">
				<h5 class="fw-bold mb-3">3. Rencana dan Realisasi Penilaian CPMK</h5>
				<div class="modern-table-wrapper">
					<table class="modern-table">
						<thead>
							<tr>
								<th class="text-center" style="width: 12%;">Kode CPMK</th>
								<th class="text-center" style="width: 10%;">Bobot (%)</th>
								<th class="text-center" style="width: 20%;">Teknik Penilaian</th>
								<th class="text-center" style="width: 28%;">Indikator Penilaian</th>
								<th class="text-center" style="width: 15%;">Nilai Rata-rata Mahasiswa</th>
								<th class="text-center" style="width: 15%;">Rata-rata Capaian</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($portfolio['assessment'])): ?>
								<?php foreach ($portfolio['assessment'] as $assessment): ?>
									<?php
									$persentase = $assessment['persentase_capaian'] ?? 0;
									$statusClass = $persentase >= $portfolio['passing_threshold'] ? 'text-success' : 'text-danger';
									?>
									<tr>
										<td><?= esc($assessment['kode_cpmk']) ?></td>
										<td class="text-center"><?= esc($assessment['bobot']) ?>%</td>
										<td><?= esc($assessment['teknik_penilaian']) ?></td>
										<td><?= esc($assessment['indikator_penilaian']) ?></td>
										<td class="text-center"><?= number_format($assessment['nilai_rata_rata'], 2) ?></td>
										<td class="text-center <?= $statusClass ?> fw-bold"><?= number_format($persentase, 2) ?>%</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="6" class="text-center text-muted">Tidak ada data penilaian</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>

			<!-- 4. Analisis Pencapaian CPMK -->
			<div class="section mb-5">
				<h5 class="fw-bold mb-3">4. Analisis Pencapaian CPMK</h5>
				<ul class="list-unstyled">
					<li class="mb-2">
						<strong>Standar Minimal Capaian:</strong> <?= number_format($portfolio['analysis']['standar_minimal'], 0) ?>%
					</li>
					<li class="mb-2">
						<strong>CPMK Tercapai:</strong>
						<?php if (!empty($portfolio['analysis']['cpmk_tercapai'])): ?>
							<span class="text-success"><?= implode(', ', $portfolio['analysis']['cpmk_tercapai']) ?></span>
						<?php else: ?>
							<span class="text-muted">-</span>
						<?php endif; ?>
					</li>
					<li class="mb-2">
						<strong>CPMK Tidak Tercapai:</strong>
						<?php if (!empty($portfolio['analysis']['cpmk_tidak_tercapai'])): ?>
							<span class="text-danger"><?= implode(', ', $portfolio['analysis']['cpmk_tidak_tercapai']) ?></span>
						<?php else: ?>
							<span class="text-muted">-</span>
						<?php endif; ?>
					</li>
					<li class="mb-2">
						<div class="d-flex justify-content-between align-items-center mb-2">
							<strong>Analisis Singkat:</strong>
							<button type="button" class="btn btn-sm btn-outline-primary no-print" onclick="toggleEditAnalysis()">
								<i class="bi bi-pencil"></i> Edit Analisis
							</button>
						</div>

						<!-- Display Mode -->
						<div id="analysis-display" class="mt-2 p-3 bg-light rounded">
							<?= esc($portfolio['analysis']['analisis_singkat']) ?>
						</div>

						<!-- Edit Mode -->
						<div id="analysis-edit" class="mt-2 p-3 border rounded bg-white" style="display: none;">
							<div class="mb-3">
								<label class="form-label fw-bold">Pilih Mode Analisis:</label>
								<div class="form-check">
									<input class="form-check-input" type="radio" name="analysis_mode" id="mode_auto" value="auto" <?= ($portfolio['analysis']['mode'] ?? 'auto') === 'auto' ? 'checked' : '' ?>>
									<label class="form-check-label" for="mode_auto">
										Otomatis - Sistem akan menghasilkan analisis berdasarkan data CPMK
									</label>
								</div>

								<!-- Auto Analysis Sub-Options -->
								<div id="auto-analysis-options" class="ms-4 mt-2" style="display: <?= ($portfolio['analysis']['mode'] ?? 'auto') === 'auto' ? 'block' : 'none' ?>;">
									<small class="text-muted d-block mb-3">Pilih salah satu template analisis yang akan digunakan (atau tidak pilih sama sekali):</small>
									<?php
									// Get saved auto_options - should be a single value now
									$savedAutoOptions = $portfolio['analysis']['auto_options'] ?? [];
									$selectedOption = is_array($savedAutoOptions) && !empty($savedAutoOptions) ? $savedAutoOptions[0] : 'default'; // Default to 'default' if nothing selected
									$templates = $portfolio['templates'] ?? [];

									// Sort templates to put 'default' first
									$sortedTemplates = [];
									if (isset($templates['default'])) {
										$sortedTemplates['default'] = $templates['default'];
									}
									foreach ($templates as $key => $template) {
										if ($key !== 'default') {
											$sortedTemplates[$key] = $template;
										}
									}
									$templates = $sortedTemplates;
									?>

									<?php foreach ($templates as $key => $template): ?>
										<div class="mb-3 border rounded p-2 bg-light">
											<div class="d-flex justify-content-between align-items-center">
												<div class="form-check flex-grow-1">
													<input class="form-check-input auto-option-radio" type="radio" name="auto_option_single" id="auto_<?= esc($key) ?>" value="<?= esc($key) ?>" <?= $selectedOption === $key ? 'checked' : '' ?>>
													<label class="form-check-label" for="auto_<?= esc($key) ?>">
														<?= esc($template['option_label']) ?>
													</label>
												</div>
												<button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleTemplateEdit('<?= esc($key) ?>')">
													<i class="bi bi-pencil"></i> Edit Template
												</button>
											</div>

											<!-- Template Edit Section -->
											<div id="template-edit-<?= esc($key) ?>" class="mt-3" style="display: none;">
												<!-- <div class="alert alert-info alert-sm mb-2">
													<small><strong>Placeholder tersedia:</strong> {total_cpmk}, {jumlah_tercapai}, {jumlah_tidak_tercapai}, {persentase_tercapai}, {cpmk_tercapai_list}, {cpmk_tidak_tercapai_list}, {standar_minimal}</small>
												</div> -->

												<div class="mb-2">
													<label class="form-label fw-bold small">Template untuk CPMK Tercapai Semua:</label>
													<textarea
														class="form-control form-control-sm font-monospace"
														name="template_tercapai_<?= esc($key) ?>"
														rows="3"
														placeholder="Template ketika semua CPMK tercapai..."><?= esc($template['template_tercapai'] ?? '') ?></textarea>
												</div>

												<div class="mb-2">
													<label class="form-label fw-bold small">Template untuk CPMK Tidak Tercapai:</label>
													<textarea
														class="form-control form-control-sm font-monospace"
														class="form-control form-control-sm font-monospace"
														name="template_tidak_tercapai_<?= esc($key) ?>"
														rows="3"
														placeholder="Template ketika ada CPMK tidak tercapai..."><?= esc($template['template_tidak_tercapai'] ?? '') ?></textarea>
												</div>
											</div>
										</div>
									<?php endforeach; ?>
								</div>

								<div class="form-check">
									<input class="form-check-input" type="radio" name="analysis_mode" id="mode_manual" value="manual" <?= ($portfolio['analysis']['mode'] ?? 'auto') === 'manual' ? 'checked' : '' ?>>
									<label class="form-check-label" for="mode_manual">
										Manual - Saya akan menulis analisis sendiri
									</label>
								</div>
							</div>

							<div id="manual-analysis-container" style="display: <?= ($portfolio['analysis']['mode'] ?? 'auto') === 'manual' ? 'block' : 'none' ?>;">
								<label class="form-label fw-bold">Tulis Analisis:</label>
								<textarea id="manual-analysis-text" class="form-control" rows="5" placeholder="Tulis analisis singkat mengenai pencapaian CPMK..."><?= ($portfolio['analysis']['mode'] ?? 'auto') === 'manual' ? esc($portfolio['analysis']['analisis_singkat']) : '' ?></textarea>
								<small class="text-muted">Jelaskan pencapaian CPMK, faktor yang mempengaruhi, dan rekomendasi perbaikan jika diperlukan.</small>
							</div>

							<div class="mt-3">
								<button type="button" class="btn btn-success" onclick="saveAnalysis()">
									<i class="bi bi-save"></i> Simpan
								</button>
								<button type="button" class="btn btn-secondary" onclick="cancelEditAnalysis()">
									Batal
								</button>
							</div>
						</div>
					</li>
				</ul>
			</div>

			<!-- 5. Tindak Lanjut & CQI (Continuous Quality Improvement) -->
			<div class="section mb-5">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h5 class="fw-bold mb-0">5. Tindak Lanjut & CQI (Continuous Quality Improvement)</h5>
					<button type="button" class="btn btn-sm btn-outline-primary no-print" onclick="toggleEditCqi()">
						<i class="bi bi-pencil"></i> Edit CQI
					</button>
				</div>

				<!-- Display Mode -->
				<div id="cqi-display" class="modern-table-wrapper">
					<table class="modern-table">
						<thead>
							<tr>
								<th class="text-center" style="width: 15%;">Kode CPMK</th>
								<th class="text-center" style="width: 25%;">Masalah</th>
								<th class="text-center" style="width: 35%;">Rencana Perbaikan</th>
								<th class="text-center" style="width: 15%;">Penanggung Jawab</th>
								<th class="text-center" style="width: 10%;">Jadwal Implementasi</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($portfolio['analysis']['cpmk_tidak_tercapai'])): ?>
								<?php foreach ($portfolio['analysis']['cpmk_tidak_tercapai'] as $cpmk): ?>
									<?php
									$cqiData = $portfolio['cqi_data'][$cpmk] ?? null;
									?>
									<tr>
										<td><?= esc($cpmk) ?></td>
										<td><?= $cqiData ? esc($cqiData['masalah']) : esc($cpmk) . ' tidak tercapai' ?></td>
										<td><?= $cqiData ? esc($cqiData['rencana_perbaikan']) : 'Revisi metode pengajaran dengan pendekatan yang lebih kontekstual dan interaktif' ?></td>
										<td><?= $cqiData ? esc($cqiData['penanggung_jawab']) : 'Dosen pengampu' ?></td>
										<td><?= $cqiData ? esc($cqiData['jadwal_pelaksanaan']) : 'Semester depan' ?></td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="5" class="text-center text-muted">
										Tidak ada masalah yang teridentifikasi. Pertahankan kualitas pembelajaran yang ada.
									</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>

				<!-- Edit Mode -->
				<div id="cqi-edit" class="border rounded bg-white p-3" style="display: none;">
					<?php if (!empty($portfolio['analysis']['cpmk_tidak_tercapai'])): ?>
						<?php foreach ($portfolio['analysis']['cpmk_tidak_tercapai'] as $index => $cpmk): ?>
							<?php
							$cqiData = $portfolio['cqi_data'][$cpmk] ?? null;
							?>
							<div class="card mb-3">
								<div class="card-header bg-light">
									<strong>CPMK: <?= esc($cpmk) ?></strong>
								</div>
								<div class="card-body">
									<input type="hidden" name="cqi[<?= $index ?>][kode_cpmk]" value="<?= esc($cpmk) ?>">

									<div class="mb-3">
										<label class="form-label fw-bold">Masalah:</label>
										<textarea class="form-control" name="cqi[<?= $index ?>][masalah]" rows="3" placeholder="Jelaskan masalah yang menyebabkan CPMK tidak tercapai..."><?= $cqiData ? esc($cqiData['masalah']) : esc($cpmk) . ' tidak tercapai' ?></textarea>
									</div>

									<div class="mb-3">
										<label class="form-label fw-bold">Rencana Perbaikan:</label>
										<textarea class="form-control" name="cqi[<?= $index ?>][rencana_perbaikan]" rows="3" placeholder="Jelaskan rencana perbaikan yang akan dilakukan..."><?= $cqiData ? esc($cqiData['rencana_perbaikan']) : 'Revisi metode pengajaran dengan pendekatan yang lebih kontekstual dan interaktif' ?></textarea>
									</div>

									<div class="row">
										<div class="col-md-6 mb-3">
											<label class="form-label fw-bold">Penanggung Jawab:</label>
											<input type="text" class="form-control" name="cqi[<?= $index ?>][penanggung_jawab]" placeholder="Contoh: Dosen pengampu" value="<?= $cqiData ? esc($cqiData['penanggung_jawab']) : 'Dosen pengampu' ?>">
										</div>

										<div class="col-md-6 mb-3">
											<label class="form-label fw-bold">Jadwal Implementasi:</label>
											<input type="text" class="form-control" name="cqi[<?= $index ?>][jadwal_pelaksanaan]" placeholder="Contoh: Semester depan" value="<?= $cqiData ? esc($cqiData['jadwal_pelaksanaan']) : 'Semester depan' ?>">
										</div>
									</div>
								</div>
							</div>
						<?php endforeach; ?>

						<div class="mt-3">
							<button type="button" class="btn btn-success" onclick="saveCqi()">
								<i class="bi bi-save"></i> Simpan
							</button>
							<button type="button" class="btn btn-secondary" onclick="cancelEditCqi()">
								Batal
							</button>
						</div>
					<?php else: ?>
						<p class="text-center text-muted mb-0">Tidak ada CPMK yang belum tercapai. Tidak perlu tindakan perbaikan.</p>
						<div class="mt-3 text-center">
							<button type="button" class="btn btn-secondary" onclick="cancelEditCqi()">
								Tutup
							</button>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- 6. Dokumen Pendukung -->
			<div class="section mb-4">
				<h5 class="fw-bold mb-3">6. Dokumen Pendukung</h5>
				<p class="mb-2 no-print">(Pilih dokumen yang akan disertakan dalam unduhan atau klik untuk unduh satu per satu)</p>
				<p class="mb-2 d-none d-print-block">(Lampirkan dalam satu file atau folder terorganisir)</p>

				<div class="no-print">
					<div class="mb-3">
						<button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="selectAllDocuments()">
							<i class="bi bi-check-square"></i> Pilih Semua
						</button>
						<button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllDocuments()">
							<i class="bi bi-square"></i> Hapus Semua
						</button>
					</div>

					<div class="form-check mb-2">
						<input class="form-check-input document-checkbox" type="checkbox" value="rps" id="doc_rps" data-label="RPS (Rencana Pembelajaran Semester)" checked onchange="updatePrintDocuments()">
						<label class="form-check-label" for="doc_rps">
							<?php if ($portfolio['rps_id']): ?>
								<a href="<?= base_url('rps/export/doc/' . $portfolio['rps_id']) ?>" class="text-decoration-none">
									RPS (Rencana Pembelajaran Semester) <i class="bi bi-file-earmark-word"></i>
								</a>
							<?php else: ?>
								RPS (Rencana Pembelajaran Semester) <span class="text-muted">(RPS tidak tersedia)</span>
							<?php endif; ?>
						</label>
					</div>
					<div class="form-check mb-2">
						<input class="form-check-input document-checkbox" type="checkbox" value="nilai" id="doc_nilai" data-label="Daftar nilai mahasiswa" checked onchange="updatePrintDocuments()">
						<label class="form-check-label" for="doc_nilai">
							<?php if ($portfolio['jadwal_mengajar_id']): ?>
								<a href="<?= base_url('admin/nilai/export-cpmk-excel/' . $portfolio['jadwal_mengajar_id']) ?>" class="text-decoration-none" target="_blank">
									Daftar nilai mahasiswa <i class="bi bi-file-earmark-excel"></i>
								</a>
							<?php else: ?>
								Daftar nilai mahasiswa <span class="text-muted">(Data tidak tersedia)</span>
							<?php endif; ?>
						</label>
					</div>
					<div class="form-check mb-2">
						<input class="form-check-input document-checkbox" type="checkbox" value="rekapitulasi" id="doc_rekapitulasi" data-label="Rekapitulasi nilai per CPMK" checked onchange="updatePrintDocuments()">
						<label class="form-check-label" for="doc_rekapitulasi">
							<?php if ($portfolio['jadwal_mengajar_id']): ?>
								<a href="<?= base_url('admin/nilai/export-cpmk-excel/' . $portfolio['jadwal_mengajar_id']) ?>" class="text-decoration-none" target="_blank">
									Rekapitulasi nilai per CPMK <i class="bi bi-file-earmark-excel"></i>
								</a>
							<?php else: ?>
								Rekapitulasi nilai per CPMK <span class="text-muted">(Data tidak tersedia)</span>
							<?php endif; ?>
						</label>
					</div>
				</div>

				<!-- Print version (dynamically updated based on selection) -->
				<ul id="print-documents-list" class="d-none d-print-block">
					<li>RPS (Rencana Pembelajaran Semester)</li>
					<li>Daftar nilai mahasiswa</li>
					<li>Rekapitulasi nilai per CPMK</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<!-- Print Styles -->
<style>
	/* Template editor styles */
	.font-monospace {
		font-family: 'Courier New', Courier, monospace;
		font-size: 0.85rem;
	}

	.alert-sm {
		padding: 0.5rem 0.75rem;
		font-size: 0.875rem;
	}

	#auto-analysis-options .border {
		transition: all 0.3s ease;
	}

	#auto-analysis-options .border:hover {
		border-color: #0d6efd !important;
	}

	[id^="template-edit-"] {
		background-color: #f8f9fa;
		border-top: 1px solid #dee2e6;
		padding-top: 1rem;
		margin-top: 0.5rem;
	}

	[id^="template-edit-"] textarea {
		border: 1px solid #ced4da;
		transition: border-color 0.15s ease-in-out;
	}

	[id^="template-edit-"] textarea:focus {
		border-color: #86b7fe;
		box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
	}

	@media print {
		.no-print {
			display: none !important;
		}

		.card {
			box-shadow: none !important;
			border: none !important;
		}

		.card-body {
			padding: 0 !important;
		}

		body {
			-webkit-print-color-adjust: exact !important;
			print-color-adjust: exact !important;
		}

		table {
			page-break-inside: auto;
		}

		tr {
			page-break-inside: avoid;
			page-break-after: auto;
		}

		.section {
			page-break-inside: avoid;
		}
	}
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	function getSelectedDocuments() {
		const selected = [];
		document.querySelectorAll('input[id^="doc_"]:checked').forEach(checkbox => {
			selected.push(checkbox.value);
		});
		return selected;
	}

	function selectAllDocuments() {
		document.querySelectorAll('.document-checkbox').forEach(checkbox => {
			checkbox.checked = true;
		});
		updatePrintDocuments();
	}

	function deselectAllDocuments() {
		document.querySelectorAll('.document-checkbox').forEach(checkbox => {
			checkbox.checked = false;
		});
		updatePrintDocuments();
	}

	function updatePrintDocuments() {
		const printList = document.getElementById('print-documents-list');
		const checkedBoxes = document.querySelectorAll('.document-checkbox:checked');

		// Clear the list
		printList.innerHTML = '';

		// Add only selected documents
		checkedBoxes.forEach(checkbox => {
			const li = document.createElement('li');
			li.textContent = checkbox.getAttribute('data-label');
			printList.appendChild(li);
		});

		// If no documents selected, show a message
		if (checkedBoxes.length === 0) {
			const li = document.createElement('li');
			li.textContent = 'Tidak ada dokumen yang dipilih';
			li.className = 'text-muted';
			printList.appendChild(li);
		}
	}

	function exportToPDF() {
		// Collect selected documents
		const selectedDocs = getSelectedDocuments();

		if (selectedDocs.length === 0) {
			alert('Silakan pilih minimal satu dokumen pendukung untuk disertakan dalam export.');
			return;
		}

		// Build URL for ZIP export with same parameters
		const urlParams = new URLSearchParams(window.location.search);

		// Add selected documents to URL
		urlParams.set('documents', selectedDocs.join(','));

		const exportUrl = '<?= base_url('admin/laporan-cpmk/export-zip') ?>?' + urlParams.toString();

		// Show loading indicator
		const loadingMsg = document.createElement('div');
		loadingMsg.id = 'zip-loading';
		loadingMsg.style.cssText = 'position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);background:rgba(0,0,0,0.8);color:white;padding:20px 40px;border-radius:8px;z-index:9999;font-size:16px;';
		loadingMsg.innerHTML = '<i class="bi bi-hourglass-split"></i> Menyiapkan dokumen...';
		document.body.appendChild(loadingMsg);

		// Use window.location to trigger download
		window.location.href = exportUrl;

		// Remove loading indicator after a short delay
		setTimeout(() => {
			const loading = document.getElementById('zip-loading');
			if (loading) {
				document.body.removeChild(loading);
			}
		}, 3000);
	}

	function toggleEditAnalysis() {
		document.getElementById('analysis-display').style.display = 'none';
		document.getElementById('analysis-edit').style.display = 'block';
	}

	function cancelEditAnalysis() {
		document.getElementById('analysis-display').style.display = 'block';
		document.getElementById('analysis-edit').style.display = 'none';
	}

	// Toggle manual analysis textarea and auto options based on selected mode
	document.querySelectorAll('input[name="analysis_mode"]').forEach(radio => {
		radio.addEventListener('change', function() {
			const manualContainer = document.getElementById('manual-analysis-container');
			const autoOptionsContainer = document.getElementById('auto-analysis-options');

			if (this.value === 'manual') {
				manualContainer.style.display = 'block';
				autoOptionsContainer.style.display = 'none';
			} else {
				manualContainer.style.display = 'none';
				autoOptionsContainer.style.display = 'block';
			}
		});
	});

	function toggleTemplateEdit(optionKey) {
		const editDiv = document.getElementById('template-edit-' + optionKey);
		if (editDiv.style.display === 'none' || editDiv.style.display === '') {
			editDiv.style.display = 'block';
		} else {
			editDiv.style.display = 'none';
		}
	}

	// Allow radio button deselection (click again to deselect)
	let lastCheckedRadio = null;
	document.addEventListener('DOMContentLoaded', function() {
		// Initialize lastCheckedRadio with the currently checked radio button
		const checkedRadio = document.querySelector('.auto-option-radio:checked');
		if (checkedRadio) {
			lastCheckedRadio = checkedRadio;
		}

		document.querySelectorAll('.auto-option-radio').forEach(radio => {
			radio.addEventListener('click', function() {
				if (this === lastCheckedRadio) {
					this.checked = false;
					lastCheckedRadio = null;
				} else {
					lastCheckedRadio = this;
				}
			});
		});
	});

	function saveAnalysis() {
		const mode = document.querySelector('input[name="analysis_mode"]:checked').value;
		const analysisText = document.getElementById('manual-analysis-text').value;

		// Validate manual mode
		if (mode === 'manual' && !analysisText.trim()) {
			alert('Silakan tulis analisis terlebih dahulu untuk mode manual.');
			return;
		}

		// Show loading
		const saveBtn = event.target;
		const originalText = saveBtn.innerHTML;
		saveBtn.disabled = true;
		saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

		// Collect auto analysis option (single selection) if auto mode is selected
		let autoOptions = [];
		if (mode === 'auto') {
			const selectedRadio = document.querySelector('input[name="auto_option_single"]:checked');
			if (selectedRadio) {
				autoOptions.push(selectedRadio.value);
			}
		}

		// Collect template data
		const templatesData = {};
		document.querySelectorAll('[name^="template_tercapai_"]').forEach(textarea => {
			const optionKey = textarea.name.replace('template_tercapai_', '');
			if (!templatesData[optionKey]) {
				templatesData[optionKey] = {};
			}
			templatesData[optionKey].template_tercapai = textarea.value;
		});
		document.querySelectorAll('[name^="template_tidak_tercapai_"]').forEach(textarea => {
			const optionKey = textarea.name.replace('template_tidak_tercapai_', '');
			if (!templatesData[optionKey]) {
				templatesData[optionKey] = {};
			}
			templatesData[optionKey].template_tidak_tercapai = textarea.value;
		});

		// Prepare data
		const formData = new FormData();
		formData.append('mata_kuliah_id', '<?= $portfolio['mata_kuliah_id'] ?>');
		formData.append('tahun_akademik', '<?= $portfolio['identitas']['tahun_akademik'] ?>');
		formData.append('program_studi', '<?= $portfolio['identitas']['program_studi'] ?>');
		formData.append('mode', mode);
		formData.append('analisis_singkat', analysisText);
		formData.append('auto_options', JSON.stringify(autoOptions));
		formData.append('templates', JSON.stringify(templatesData));

		// Send AJAX request
		fetch('<?= base_url('admin/laporan-cpmk/save-analysis') ?>', {
				method: 'POST',
				body: formData,
				headers: {
					'X-Requested-With': 'XMLHttpRequest'
				}
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					alert('Analisis berhasil disimpan! Halaman akan dimuat ulang.');
					location.reload();
				} else {
					alert('Gagal menyimpan: ' + data.message);
					saveBtn.disabled = false;
					saveBtn.innerHTML = originalText;
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert('Terjadi kesalahan saat menyimpan analisis.');
				saveBtn.disabled = false;
				saveBtn.innerHTML = originalText;
			});
	}

	function toggleEditCqi() {
		document.getElementById('cqi-display').style.display = 'none';
		document.getElementById('cqi-edit').style.display = 'block';
	}

	function cancelEditCqi() {
		document.getElementById('cqi-display').style.display = 'block';
		document.getElementById('cqi-edit').style.display = 'none';
	}

	function saveCqi() {
		// Show loading
		const saveBtn = event.target;
		const originalText = saveBtn.innerHTML;
		saveBtn.disabled = true;
		saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

		// Collect CQI data from form
		const cqiData = [];
		const cqiEditDiv = document.getElementById('cqi-edit');
		const cqiInputs = cqiEditDiv.querySelectorAll('input[type="hidden"][name*="[kode_cpmk]"]');

		cqiInputs.forEach((input, index) => {
			const kodeCpmk = input.value;
			const masalah = cqiEditDiv.querySelector(`textarea[name="cqi[${index}][masalah]"]`)?.value || '';
			const rencanaPerbaikan = cqiEditDiv.querySelector(`textarea[name="cqi[${index}][rencana_perbaikan]"]`)?.value || '';
			const penanggungJawab = cqiEditDiv.querySelector(`input[name="cqi[${index}][penanggung_jawab]"]`)?.value || '';
			const jadwalPelaksanaan = cqiEditDiv.querySelector(`input[name="cqi[${index}][jadwal_pelaksanaan]"]`)?.value || '';

			cqiData.push({
				kode_cpmk: kodeCpmk,
				masalah: masalah,
				rencana_perbaikan: rencanaPerbaikan,
				penanggung_jawab: penanggungJawab,
				jadwal_pelaksanaan: jadwalPelaksanaan
			});
		});

		// Prepare data
		const formData = new FormData();
		formData.append('jadwal_mengajar_id', '<?= $portfolio['jadwal_mengajar_id'] ?>');
		formData.append('cqi_data', JSON.stringify(cqiData));

		// Send AJAX request
		fetch('<?= base_url('admin/laporan-cpmk/save-cqi') ?>', {
				method: 'POST',
				body: formData,
				headers: {
					'X-Requested-With': 'XMLHttpRequest'
				}
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					alert('Data CQI berhasil disimpan! Halaman akan dimuat ulang.');
					location.reload();
				} else {
					alert('Gagal menyimpan: ' + data.message);
					saveBtn.disabled = false;
					saveBtn.innerHTML = originalText;
				}
			})
			.catch(error => {
				console.error('Error:', error);
				alert('Terjadi kesalahan saat menyimpan data CQI.');
				saveBtn.disabled = false;
				saveBtn.innerHTML = originalText;
			});
	}
</script>
<?= $this->endSection() ?>
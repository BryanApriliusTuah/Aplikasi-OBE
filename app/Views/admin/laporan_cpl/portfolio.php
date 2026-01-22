<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">
<div class="container-fluid">
	<!-- Action Buttons -->
	<div class="row mb-3 no-print">
		<div class="col-12 d-flex justify-content-between align-items-center">
			<a href="<?= base_url('admin/laporan-cpl') ?>" class="btn btn-outline-secondary">
				<i class="bi bi-arrow-left"></i> Kembali
			</a>
			<div class="btn-group">
				<button type="button" class="btn btn-success" onclick="exportToZIP()">
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
				<h2 class="fw-bold">LAPORAN PEMENUHAN CAPAIAN PEMBELAJARAN LULUSAN (CPL)</h2>
				<p class="mt-3">
					<strong>Program Studi:</strong> <?= esc($report['identitas']['nama_program_studi']) ?><br>
					<strong>Tahun Akademik:</strong> <?= esc($report['identitas']['tahun_akademik']) ?><br>
					<strong>Angkatan:</strong> <?= esc($report['identitas']['angkatan']) ?>
				</p>
			</div>

			<!-- 1. Identitas Program Studi -->
			<div class="section mb-5">
				<h5 class="fw-bold mb-3">1. Identitas Program Studi</h5>
				<div class="modern-table-wrapper">
					<table class="modern-table">
						<tbody>
							<tr>
								<td class="fw-bold" style="width: 30%;">Nama Program Studi</td>
								<td><?= esc($report['identitas']['nama_program_studi']) ?></td>
							</tr>
							<tr>
								<td class="fw-bold">Fakultas</td>
								<td><?= esc($report['identitas']['fakultas']) ?></td>
							</tr>
							<tr>
								<td class="fw-bold">Perguruan Tinggi</td>
								<td><?= esc($report['identitas']['perguruan_tinggi']) ?></td>
							</tr>
							<tr>
								<td class="fw-bold">Tahun Akademik</td>
								<td><?= esc($report['identitas']['tahun_akademik']) ?></td>
							</tr>
							<tr>
								<td class="fw-bold">Angkatan</td>
								<td><?= esc($report['identitas']['angkatan']) ?></td>
							</tr>
							<tr>
								<td class="fw-bold">Ketua Prodi</td>
								<td><?= esc($report['identitas']['ketua_prodi']) ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>

			<!-- 2. Daftar CPL Program Studi -->
			<div class="section mb-5">
				<h5 class="fw-bold mb-3">2. Daftar CPL Program Studi</h5>
				<div class="modern-table-wrapper">
					<table class="modern-table">
						<thead>
							<tr>
								<th style="width: 15%;">Kode CPL</th>
								<th style="width: 55%;">Rumusan CPL</th>
								<th style="width: 30%;">Sumber Turunan (Profil Lulusan)</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($report['cpl_list'])): ?>
								<?php foreach ($report['cpl_list'] as $cpl): ?>
									<tr>
										<td><?= esc($cpl['kode_cpl']) ?></td>
										<td><?= esc($cpl['deskripsi']) ?></td>
										<td><?= esc($cpl['sumber_turunan'] ?: '-') ?></td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="3" class="text-center text-muted">Tidak ada data CPL</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>

			<!-- 3. Matriks CPMK terhadap CPL -->
			<div class="section mb-5">
				<h5 class="fw-bold mb-3">3. Matriks CPMK terhadap CPL</h5>
				<div class="modern-table-wrapper">
					<table class="modern-table">
						<thead>
							<tr>
								<th style="width: 25%;">Mata Kuliah (MK)</th>
								<th style="width: 20%;">Kode CPMK</th>
								<th style="width: 15%;">Bobot CPMK pada MK</th>
								<th style="width: 40%;">CPL Terkait</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($report['cpmk_cpl_matrix'])): ?>
								<?php foreach ($report['cpmk_cpl_matrix'] as $mk): ?>
									<?php
									$cpmkList = array_values($mk['cpmk_list']);
									$rowspan = count($cpmkList);
									?>
									<?php foreach ($cpmkList as $index => $cpmk): ?>
										<tr>
											<?php if ($index === 0): ?>
												<td rowspan="<?= $rowspan ?>" class="align-middle">
													<strong><?= esc($mk['nama_mk']) ?></strong>
												</td>
											<?php endif; ?>
											<td><?= esc($cpmk['kode_cpmk']) ?></td>
											<td class="text-center"><?= esc($cpmk['bobot_cpmk']) ?></td>
											<td><?= esc(!empty($cpmk['cpl_terkait']) ? implode(', ', $cpmk['cpl_terkait']) : '-') ?></td>
										</tr>
									<?php endforeach; ?>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="4" class="text-center text-muted">Tidak ada data matriks CPMK-CPL</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>

			<!-- 4. Rekapitulasi Capaian CPL Berdasarkan CPMK Untuk Satu Angkatan -->
			<div class="section mb-5">
				<h5 class="fw-bold mb-3">4. Rekapitulasi Capaian CPL Berdasarkan CPMK Untuk Angkatan <?= esc($report['identitas']['angkatan']) ?></h5>
				<div class="modern-table-wrapper">
					<table class="modern-table">
						<thead>
							<tr>
								<th rowspan="2" style="width: 12%;" class="align-middle text-center">Kode CPL</th>
								<th rowspan="2" style="width: 15%;" class="align-middle text-center">CPMK</th>
								<th rowspan="2" style="width: 23%;" class="align-middle text-center">Mata Kuliah Kontributor</th>
								<th colspan="2" class="text-center">Capaian</th>
								<th rowspan="2" style="width: 12%;" class="align-middle text-center">Rata-rata Capaian CPL (%)</th>
							</tr>
							<tr>
								<th style="width: 15%;" class="text-center">Rata-rata CPMK</th>
								<th style="width: 15%;" class="text-center">CPL (Total Bobot)</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($report['cpl_achievement'])): ?>
								<?php foreach ($report['cpl_achievement'] as $cpl): ?>
									<?php
									$cpmkList = $cpl['cpmk_kontributor'];
									$rowspan = max(1, count($cpmkList));
									$statusClass = $cpl['capaian_cpl_persen'] >= $report['analysis']['passing_threshold'] ? 'text-success' : 'text-danger';
									?>
									<?php if (empty($cpmkList)): ?>
										<tr>
											<td class="text-center"><?= esc($cpl['kode_cpl']) ?></td>
											<td class="text-center">-</td>
											<td class="text-center">-</td>
											<td class="text-center">-</td>
											<td class="text-center">-</td>
											<td class="text-center fw-bold <?= $statusClass ?>">0%</td>
										</tr>
									<?php else: ?>
										<?php foreach ($cpmkList as $index => $cpmk): ?>
											<tr>
												<?php if ($index === 0): ?>
													<td rowspan="<?= $rowspan ?>" class="align-middle text-center">
														<strong><?= esc($cpl['kode_cpl']) ?></strong>
													</td>
												<?php endif; ?>
												<td><?= esc($cpmk['kode_cpmk']) ?></td>
												<td>
													<?php if (is_array($cpmk['mata_kuliah_names'])): ?>
														<?= esc(implode(', ', $cpmk['mata_kuliah_names'])) ?>
													<?php else: ?>
														<?= esc($cpmk['mata_kuliah_names']) ?>
													<?php endif; ?>
												</td>
												<td class="text-center"><?= number_format($cpmk['capaian_rata_rata'], 2) ?> (<?= $cpmk['bobot'] ?>)</td>
												<?php if ($index === 0): ?>
													<td rowspan="<?= $rowspan ?>" class="align-middle text-center">
														<?= number_format($cpl['capaian_cpl'], 2) ?> (<?= $cpl['total_bobot'] ?>)
													</td>
													<td rowspan="<?= $rowspan ?>" class="align-middle text-center fw-bold <?= $statusClass ?>">
														<?= number_format($cpl['capaian_cpl_persen'], 2) ?>%
													</td>
												<?php endif; ?>
											</tr>
										<?php endforeach; ?>
									<?php endif; ?>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="6" class="text-center text-muted">Tidak ada data capaian CPL</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>
			</div>

			<!-- 5. Analisis Pemenuhan CPL -->
			<div class="section mb-5">
				<h5 class="fw-bold mb-3">5. Analisis Pemenuhan CPL</h5>
				<ul class="list-unstyled">
					<li class="mb-2">
						<strong>Standar Minimal Capaian CPL:</strong>
						<span class="fs-6"><?= $report['analysis']['passing_threshold'] ?>%</span>
					</li>
					<li class="mb-2">
						<strong>CPL yang tercapai:</strong>
						<?php if (!empty($report['analysis']['cpl_tercapai'])): ?>
							<span class="text-success"><?= implode(', ', $report['analysis']['cpl_tercapai']) ?></span>
						<?php else: ?>
							<span class="text-muted">-</span>
						<?php endif; ?>
					</li>
					<li class="mb-2">
						<strong>CPL yang belum tercapai:</strong>
						<?php if (!empty($report['analysis']['cpl_tidak_tercapai'])): ?>
							<?php foreach ($report['analysis']['cpl_tidak_tercapai'] as $cpl): ?>
								<span class="text-danger"><?= esc($cpl['kode_cpl']) ?> (<?= number_format($cpl['capaian'], 2) ?>%)</span><?= $cpl !== end($report['analysis']['cpl_tidak_tercapai']) ? ', ' : '' ?>
							<?php endforeach; ?>
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
							<?= esc($report['analysis']['analisis_summary']) ?>
						</div>

						<!-- Edit Mode -->
						<div id="analysis-edit" class="mt-2 p-3 border rounded bg-white" style="display: none;">
							<div class="mb-3">
								<label class="form-label fw-bold">Pilih Mode Analisis:</label>
								<div class="form-check">
									<input class="form-check-input" type="radio" name="analysis_mode" id="mode_auto" value="auto" <?= ($report['analysis']['mode'] ?? 'auto') === 'auto' ? 'checked' : '' ?>>
									<label class="form-check-label" for="mode_auto">
										Otomatis - Sistem akan menghasilkan analisis berdasarkan data CPL
									</label>
								</div>

								<!-- Auto Analysis Sub-Options -->
								<div id="auto-analysis-options" class="ms-4 mt-2" style="display: <?= ($report['analysis']['mode'] ?? 'auto') === 'auto' ? 'block' : 'none' ?>;">
									<small class="text-muted d-block mb-3">Pilih salah satu template analisis yang akan digunakan (atau tidak pilih sama sekali):</small>
									<?php
									// Get saved auto_options - should be a single value now
									$savedAutoOptions = $report['analysis']['auto_options'] ?? [];
									$selectedOption = is_array($savedAutoOptions) && !empty($savedAutoOptions) ? $savedAutoOptions[0] : 'default';
									$templates = $report['analysis']['templates'] ?? [];

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
												<div class="mb-2">
													<label class="form-label fw-bold small">Template untuk CPL Tercapai Semua:</label>
													<textarea
														class="form-control form-control-sm font-monospace"
														name="template_tercapai_<?= esc($key) ?>"
														rows="3"
														placeholder="Template ketika semua CPL tercapai..."><?= esc($template['template_tercapai'] ?? '') ?></textarea>
												</div>

												<div class="mb-2">
													<label class="form-label fw-bold small">Template untuk CPL Tidak Tercapai:</label>
													<textarea
														class="form-control form-control-sm font-monospace"
														name="template_tidak_tercapai_<?= esc($key) ?>"
														rows="3"
														placeholder="Template ketika ada CPL tidak tercapai..."><?= esc($template['template_tidak_tercapai'] ?? '') ?></textarea>
												</div>
											</div>
										</div>
									<?php endforeach; ?>
								</div>

								<div class="form-check">
									<input class="form-check-input" type="radio" name="analysis_mode" id="mode_manual" value="manual" <?= ($report['analysis']['mode'] ?? 'auto') === 'manual' ? 'checked' : '' ?>>
									<label class="form-check-label" for="mode_manual">
										Manual - Saya akan menulis analisis sendiri
									</label>
								</div>
							</div>

							<div id="manual-analysis-container" style="display: <?= ($report['analysis']['mode'] ?? 'auto') === 'manual' ? 'block' : 'none' ?>;">
								<label class="form-label fw-bold">Tulis Analisis:</label>
								<textarea id="manual-analysis-text" class="form-control" rows="5" placeholder="Tulis analisis singkat mengenai pemenuhan CPL..."><?= ($report['analysis']['mode'] ?? 'auto') === 'manual' ? esc($report['analysis']['analisis_summary']) : '' ?></textarea>
								<small class="text-muted">Jelaskan pencapaian CPL, faktor yang mempengaruhi, dan rekomendasi perbaikan jika diperlukan.</small>
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

			<!-- 6. Tindak Lanjut dan Rencana Perbaikan (CQI) -->
			<div class="section mb-5">
				<div class="d-flex justify-content-between align-items-center mb-3">
					<h5 class="fw-bold mb-0">6. Tindak Lanjut dan Rencana Perbaikan (CQI)</h5>
					<button type="button" class="btn btn-sm btn-outline-primary no-print" onclick="toggleEditCqi()">
						<i class="bi bi-pencil"></i> Edit CQI
					</button>
				</div>

				<!-- Display Mode -->
				<div id="cqi-display" class="modern-table-wrapper">
					<table class="modern-table">
						<thead>
							<tr>
								<th style="width: 15%;">Kode CPL</th>
								<th style="width: 25%;">Masalah Utama</th>
								<th style="width: 35%;">Rencana Tindakan</th>
								<th style="width: 15%;">Penanggung Jawab</th>
								<th style="width: 10%;">Waktu Pelaksanaan</th>
							</tr>
						</thead>
						<tbody>
							<?php if (!empty($report['analysis']['cpl_tidak_tercapai'])): ?>
								<?php foreach ($report['analysis']['cpl_tidak_tercapai'] as $cpl): ?>
									<?php
									$cqiData = $report['cqi_data'][$cpl['kode_cpl']] ?? null;
									?>
									<tr>
										<td><?= esc($cpl['kode_cpl']) ?></td>
										<td><?= $cqiData ? esc($cqiData['masalah']) : 'Nilai CPL < ' . $report['analysis']['passing_threshold'] . '%' ?></td>
										<td><?= $cqiData ? esc($cqiData['rencana_perbaikan']) : 'Evaluasi mata kuliah kontributor, perbaikan metode pembelajaran dan asesmen, penambahan latihan dan studi kasus' ?></td>
										<td><?= $cqiData ? esc($cqiData['penanggung_jawab']) : 'Tim Kurikulum & Dosen MK' ?></td>
										<td><?= $cqiData ? esc($cqiData['jadwal_pelaksanaan']) : 'Semester Berikutnya' ?></td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="5" class="text-center">
										<strong>Semua CPL tercapai.</strong> Dipertahankan dan dikembangkan metode pengajaran yang sudah efektif.
									</td>
								</tr>
							<?php endif; ?>
						</tbody>
					</table>
				</div>

				<!-- Edit Mode -->
				<div id="cqi-edit" class="border rounded bg-white p-3" style="display: none;">
					<?php if (!empty($report['analysis']['cpl_tidak_tercapai'])): ?>
						<?php foreach ($report['analysis']['cpl_tidak_tercapai'] as $index => $cpl): ?>
							<?php
							$cqiData = $report['cqi_data'][$cpl['kode_cpl']] ?? null;
							?>
							<div class="card mb-3">
								<div class="card-header bg-light">
									<strong>CPL: <?= esc($cpl['kode_cpl']) ?></strong> (Capaian: <?= number_format($cpl['capaian'], 2) ?>%)
								</div>
								<div class="card-body">
									<input type="hidden" name="cqi[<?= $index ?>][kode_cpl]" value="<?= esc($cpl['kode_cpl']) ?>">

									<div class="mb-3">
										<label class="form-label fw-bold">Masalah Utama:</label>
										<textarea class="form-control" name="cqi[<?= $index ?>][masalah]" rows="3" placeholder="Jelaskan masalah utama yang menyebabkan CPL tidak tercapai..."><?= $cqiData ? esc($cqiData['masalah']) : 'Nilai CPL < ' . $report['analysis']['passing_threshold'] . '%' ?></textarea>
									</div>

									<div class="mb-3">
										<label class="form-label fw-bold">Rencana Tindakan:</label>
										<textarea class="form-control" name="cqi[<?= $index ?>][rencana_perbaikan]" rows="3" placeholder="Jelaskan rencana tindakan yang akan dilakukan..."><?= $cqiData ? esc($cqiData['rencana_perbaikan']) : 'Evaluasi mata kuliah kontributor, perbaikan metode pembelajaran dan asesmen, penambahan latihan dan studi kasus' ?></textarea>
									</div>

									<div class="row">
										<div class="col-md-6 mb-3">
											<label class="form-label fw-bold">Penanggung Jawab:</label>
											<input type="text" class="form-control" name="cqi[<?= $index ?>][penanggung_jawab]" placeholder="Contoh: Tim Kurikulum & Dosen MK" value="<?= $cqiData ? esc($cqiData['penanggung_jawab']) : 'Tim Kurikulum & Dosen MK' ?>">
										</div>

										<div class="col-md-6 mb-3">
											<label class="form-label fw-bold">Waktu Pelaksanaan:</label>
											<input type="text" class="form-control" name="cqi[<?= $index ?>][jadwal_pelaksanaan]" placeholder="Contoh: Semester Berikutnya" value="<?= $cqiData ? esc($cqiData['jadwal_pelaksanaan']) : 'Semester Berikutnya' ?>">
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
						<p class="text-center text-muted mb-0">Tidak ada CPL yang belum tercapai. Tidak perlu tindakan perbaikan.</p>
						<div class="mt-3 text-center">
							<button type="button" class="btn btn-secondary" onclick="cancelEditCqi()">
								Tutup
							</button>
						</div>
					<?php endif; ?>
				</div>
			</div>

			<!-- 7. Kesimpulan Umum -->
			<div class="section mb-5">
				<h5 class="fw-bold mb-3">7. Kesimpulan Umum</h5>
				<div class="p-3 bg-light rounded">
					<p>
						Secara umum, dari <strong><?= $report['analysis']['total_cpl'] ?></strong> CPL yang diukur,
						sebanyak <strong><?= $report['analysis']['total_tercapai'] ?></strong> CPL telah tercapai
						dengan persentase capaian minimal <?= $report['analysis']['passing_threshold'] ?>%.
						<?php if ($report['analysis']['total_tidak_tercapai'] > 0): ?>
							<strong><?= $report['analysis']['total_tidak_tercapai'] ?></strong> CPL belum tercapai dan akan
							ditindaklanjuti menyesuaikan dengan rencana CQI di atas.
						<?php else: ?>
							Semua CPL tercapai dengan baik, menunjukkan efektivitas proses pembelajaran yang telah dilaksanakan.
						<?php endif; ?>
					</p>
				</div>
			</div>

			<!-- 8. Lampiran -->
			<div class="section mb-3">
				<h5 class="fw-bold mb-3">8. Lampiran</h5>
				<p class="mb-2 no-print">(Pilih dokumen yang akan disertakan dalam unduhan atau klik untuk unduh satu per satu)</p>
				<p class="mb-2 d-none d-print-block">(Lampirkan dalam satu file atau folder terorganisir)</p>

				<div class="no-print">
					<div class="mb-3">
						<button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="selectAllLampiran()">
							<i class="bi bi-check-square"></i> Pilih Semua
						</button>
						<button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllLampiran()">
							<i class="bi bi-square"></i> Hapus Semua
						</button>
					</div>

					<div class="form-check mb-2">
						<input class="form-check-input lampiran-checkbox" type="checkbox" value="rekap_cpmk" id="lampiran_rekap_cpmk" data-label="Rekap nilai CPMK dari seluruh mata kuliah" checked onchange="updatePrintLampiran()">
						<label class="form-check-label d-block" for="lampiran_rekap_cpmk">
							Rekap nilai CPMK dari seluruh mata kuliah:
						</label>
						<ul class="mt-2">
							<?php if (!empty($report['lampiran']['rekap_cpmk'])): ?>
								<?php foreach ($report['lampiran']['rekap_cpmk'] as $item): ?>
									<li>
										<?php if ($item['jadwal_id']): ?>
											<a href="<?= base_url('admin/nilai/export-cpmk-excel/' . $item['jadwal_id']) ?>" class="text-decoration-none" target="_blank">
												<?= esc($item['nama_mk']) ?> - Kelas <?= esc($item['kelas']) ?> <i class="bi bi-file-earmark-excel"></i>
											</a>
										<?php else: ?>
											<?= esc($item['nama_mk']) ?> <span class="text-muted">(Jadwal tidak tersedia)</span>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							<?php else: ?>
								<li class="text-muted">Tidak ada mata kuliah</li>
							<?php endif; ?>
						</ul>
					</div>
					<div class="form-check mb-2">
						<input class="form-check-input lampiran-checkbox" type="checkbox" value="matriks_cpl_cpmk" id="lampiran_matriks" data-label="Matriks hubungan CPL – CPMK – MK" checked onchange="updatePrintLampiran()">
						<label class="form-check-label" for="lampiran_matriks">
							<a href="<?= base_url('admin/pemetaan-cpl-mk-cpmk/exportExcel') ?>" class="text-decoration-none" target="_blank">
								Matriks hubungan CPL – CPMK – MK <i class="bi bi-file-earmark-excel"></i>
							</a>
						</label>
					</div>
					<div class="form-check mb-2">
						<input class="form-check-input lampiran-checkbox" type="checkbox" value="rps_mk_kontributor" id="lampiran_rps" data-label="RPS dari MK Kontributor" checked onchange="updatePrintLampiran()">
						<label class="form-check-label d-block" for="lampiran_rps">
							RPS dari MK Kontributor:
						</label>
						<ul class="mt-2">
							<?php if (!empty($report['lampiran']['rps_list'])): ?>
								<?php foreach ($report['lampiran']['rps_list'] as $item): ?>
									<li>
										<?php if ($item['rps_id']): ?>
											<a href="<?= base_url('rps/export/doc/' . $item['rps_id']) ?>" class="text-decoration-none">
												<?= esc($item['nama_mk']) ?> <i class="bi bi-file-earmark-word"></i>
											</a>
										<?php else: ?>
											<?= esc($item['nama_mk']) ?> <span class="text-muted">(RPS tidak tersedia)</span>
										<?php endif; ?>
									</li>
								<?php endforeach; ?>
							<?php else: ?>
								<li class="text-muted">Tidak ada mata kuliah kontributor</li>
							<?php endif; ?>
						</ul>
					</div>
				</div>

				<!-- Print version (dynamically updated based on selection) -->
				<ul id="print-lampiran-list" class="d-none d-print-block">
					<li>Rekap nilai CPMK dari seluruh mata kuliah</li>
					<li>Matriks hubungan CPL – CPMK – MK</li>
					<li>RPS dari MK Kontributor</li>
				</ul>
			</div>
		</div>
	</div>
</div>

<style>
	@media print {
		.no-print {
			display: none !important;
		}

		.card {
			border: none !important;
			box-shadow: none !important;
		}

		body {
			-webkit-print-color-adjust: exact;
			print-color-adjust: exact;
		}

		.section {
			page-break-inside: avoid;
		}

		table {
			page-break-inside: auto;
		}

		tr {
			page-break-inside: avoid;
			page-break-after: auto;
		}
	}

	.font-monospace {
		font-family: 'Courier New', monospace;
		font-size: 0.875rem;
	}

	#auto-analysis-options .border {
		transition: all 0.3s ease;
	}

	#auto-analysis-options .border:hover {
		border-color: #0d6efd !important;
	}
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	function selectAllLampiran() {
		document.querySelectorAll('.lampiran-checkbox').forEach(checkbox => {
			checkbox.checked = true;
		});
		updatePrintLampiran();
	}

	function deselectAllLampiran() {
		document.querySelectorAll('.lampiran-checkbox').forEach(checkbox => {
			checkbox.checked = false;
		});
		updatePrintLampiran();
	}

	function updatePrintLampiran() {
		const printList = document.getElementById('print-lampiran-list');
		const checkedBoxes = document.querySelectorAll('.lampiran-checkbox:checked');

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

	function exportToZIP() {
		// Collect selected documents
		const selectedDocs = [];
		document.querySelectorAll('.lampiran-checkbox:checked').forEach(checkbox => {
			selectedDocs.push(checkbox.value);
		});

		if (selectedDocs.length === 0) {
			alert('Silakan pilih minimal satu dokumen untuk disertakan dalam ZIP.');
			return;
		}

		// Build URL for ZIP export with current parameters
		const urlParams = new URLSearchParams(window.location.search);

		// Add selected documents to URL
		urlParams.set('documents', selectedDocs.join(','));

		const exportUrl = '<?= base_url('admin/laporan-cpl/export-zip') ?>?' + urlParams.toString();

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
		if (editDiv) {
			editDiv.style.display = editDiv.style.display === 'none' ? 'block' : 'none';
		}
	}

	// Allow deselecting radio buttons by clicking again
	document.addEventListener('DOMContentLoaded', function() {
		let lastCheckedRadio = null;
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

		// Get current URL parameters
		const urlParams = new URLSearchParams(window.location.search);

		// Prepare data
		const formData = new FormData();
		formData.append('program_studi', urlParams.get('program_studi') || '<?= $report['identitas']['nama_program_studi'] ?>');
		formData.append('tahun_akademik', urlParams.get('tahun_akademik') || '<?= $report['identitas']['tahun_akademik'] ?>');
		formData.append('angkatan', urlParams.get('angkatan') || '<?= $report['identitas']['angkatan'] ?>');
		formData.append('mode', mode);
		formData.append('analisis_summary', analysisText);
		formData.append('auto_options', JSON.stringify(autoOptions));
		formData.append('templates', JSON.stringify(templatesData));

		// Send AJAX request
		fetch('<?= base_url('admin/laporan-cpl/save-analysis') ?>', {
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

		// Get current URL parameters
		const urlParams = new URLSearchParams(window.location.search);

		// Collect CQI data from form
		const cqiData = [];
		const cqiEditDiv = document.getElementById('cqi-edit');
		const cqiInputs = cqiEditDiv.querySelectorAll('input[type="hidden"][name*="[kode_cpl]"]');

		cqiInputs.forEach((input, index) => {
			const kodeCpl = input.value;
			const masalah = cqiEditDiv.querySelector(`textarea[name="cqi[${index}][masalah]"]`)?.value || '';
			const rencanaPerbaikan = cqiEditDiv.querySelector(`textarea[name="cqi[${index}][rencana_perbaikan]"]`)?.value || '';
			const penanggungJawab = cqiEditDiv.querySelector(`input[name="cqi[${index}][penanggung_jawab]"]`)?.value || '';
			const jadwalPelaksanaan = cqiEditDiv.querySelector(`input[name="cqi[${index}][jadwal_pelaksanaan]"]`)?.value || '';

			cqiData.push({
				kode_cpl: kodeCpl,
				masalah: masalah,
				rencana_perbaikan: rencanaPerbaikan,
				penanggung_jawab: penanggungJawab,
				jadwal_pelaksanaan: jadwalPelaksanaan
			});
		});

		// Prepare data
		const formData = new FormData();
		formData.append('program_studi', urlParams.get('program_studi') || '<?= $report['identitas']['nama_program_studi'] ?>');
		formData.append('tahun_akademik', urlParams.get('tahun_akademik') || '<?= $report['identitas']['tahun_akademik'] ?>');
		formData.append('angkatan', urlParams.get('angkatan') || '<?= $report['identitas']['angkatan'] ?>');
		formData.append('cqi_data', JSON.stringify(cqiData));

		// Send AJAX request
		fetch('<?= base_url('admin/laporan-cpl/save-cqi') ?>', {
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
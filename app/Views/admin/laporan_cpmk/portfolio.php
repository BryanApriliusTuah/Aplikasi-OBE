<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Action Buttons -->
    <div class="row mb-3 no-print">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <a href="<?= base_url('admin/laporan-cpmk') ?>" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <div class="btn-group">
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer"></i> Cetak
                </button>
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
                <table class="table table-bordered">
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

            <!-- 2. Capaian Pembelajaran Mata Kuliah (CPMK) -->
            <div class="section mb-5">
                <h5 class="fw-bold mb-3">2. Capaian Pembelajaran Mata Kuliah (CPMK)</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 12%;">Kode CPMK</th>
                                <th style="width: 38%;">Rumusan CPMK</th>
                                <th style="width: 15%;">Keterkaitan dengan CPL</th>
                                <th style="width: 17.5%;">Metode Pembelajaran</th>
                                <th style="width: 17.5%;">Metode Asesmen</th>
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
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 12%;">Kode CPMK</th>
                                <th style="width: 10%;">Bobot (%)</th>
                                <th style="width: 20%;">Teknik Penilaian</th>
                                <th style="width: 28%;">Indikator Penilaian</th>
                                <th style="width: 15%;">Nilai Rata-rata Mahasiswa</th>
                                <th style="width: 15%;">Persentase Capaian Target</th>
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
                <h5 class="fw-bold mb-3">5. Tindak Lanjut & CQI (Continuous Quality Improvement)</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 25%;">Masalah</th>
                                <th style="width: 35%;">Rencana Perbaikan</th>
                                <th style="width: 20%;">Penanggung Jawab</th>
                                <th style="width: 20%;">Jadwal Implementasi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($portfolio['analysis']['cpmk_tidak_tercapai'])): ?>
                                <?php foreach ($portfolio['analysis']['cpmk_tidak_tercapai'] as $cpmk): ?>
                                    <tr>
                                        <td><?= esc($cpmk) ?> tidak tercapai</td>
                                        <td>Revisi metode pengajaran dengan pendekatan yang lebih kontekstual dan interaktif</td>
                                        <td>Dosen pengampu</td>
                                        <td>Semester depan</td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td>Kurangnya latihan praktis</td>
                                    <td>Tambahan sesi tutorial dan praktikum berbasis proyek</td>
                                    <td>Koordinator MK</td>
                                    <td>Semester depan</td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        Tidak ada masalah yang teridentifikasi. Pertahankan kualitas pembelajaran yang ada.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 6. Dokumen Pendukung -->
            <div class="section mb-4">
                <h5 class="fw-bold mb-3">6. Dokumen Pendukung</h5>
                <p class="mb-2 no-print">(Pilih dokumen yang akan disertakan dalam unduhan)</p>
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
                            RPS (Rencana Pembelajaran Semester)
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input document-checkbox" type="checkbox" value="nilai" id="doc_nilai" data-label="Daftar nilai mahasiswa" checked onchange="updatePrintDocuments()">
                        <label class="form-check-label" for="doc_nilai">
                            Daftar nilai mahasiswa
                        </label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input document-checkbox" type="checkbox" value="rekapitulasi" id="doc_rekapitulasi" data-label="Rekapitulasi nilai per CPMK" checked onchange="updatePrintDocuments()">
                        <label class="form-check-label" for="doc_rekapitulasi">
                            Rekapitulasi nilai per CPMK
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

    // Toggle manual analysis textarea based on selected mode
    document.querySelectorAll('input[name="analysis_mode"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const manualContainer = document.getElementById('manual-analysis-container');
            if (this.value === 'manual') {
                manualContainer.style.display = 'block';
            } else {
                manualContainer.style.display = 'none';
            }
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

        // Prepare data
        const formData = new FormData();
        formData.append('mata_kuliah_id', '<?= $portfolio['mata_kuliah_id'] ?>');
        formData.append('tahun_akademik', '<?= $portfolio['identitas']['tahun_akademik'] ?>');
        formData.append('program_studi', '<?= $portfolio['identitas']['program_studi'] ?>');
        formData.append('mode', mode);
        formData.append('analisis_singkat', analysisText);

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
</script>
<?= $this->endSection() ?>

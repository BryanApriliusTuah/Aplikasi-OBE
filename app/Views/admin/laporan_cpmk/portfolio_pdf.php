<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Portofolio Mata Kuliah - <?= esc($portfolio['identitas']['kode_mata_kuliah']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            background: white;
            padding: 30px;
        }

        h2 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        h5 {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        p, li {
            font-size: 11px;
            line-height: 1.4;
        }

        table {
            font-size: 11px;
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            padding: 8px;
            border: 1px solid #dee2e6;
            word-wrap: break-word;
            vertical-align: top;
        }

        table thead th {
            background-color: #f8f9fa;
            font-weight: bold;
        }

        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }

        .text-center {
            text-align: center;
        }

        .fw-bold {
            font-weight: bold;
        }

        .mb-0 {
            margin-bottom: 0;
        }

        .mb-2 {
            margin-bottom: 8px;
        }

        .mb-3 {
            margin-bottom: 15px;
        }

        .mb-5 {
            margin-bottom: 25px;
        }

        .text-success {
            color: #198754;
        }

        .text-danger {
            color: #dc3545;
        }

        .text-muted {
            color: #6c757d;
        }

        .bg-light {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
        }

        ul {
            margin: 0;
            padding-left: 20px;
        }

        .list-unstyled {
            list-style: none;
            padding-left: 0;
        }
    </style>
</head>
<body>
    <div id="portfolio-content">
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
                        <td><?= esc($portfolio['identitas']['program_studi_nama_resmi']) ?></td>
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
            <table class="table table-bordered">
                <thead>
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

        <!-- 3. Rencana dan Realisasi Penilaian CPMK -->
        <div class="section mb-5">
            <h5 class="fw-bold mb-3">3. Rencana dan Realisasi Penilaian CPMK</h5>
            <table class="table table-bordered">
                <thead>
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
                    <strong>Analisis Singkat:</strong>
                    <div class="mt-2 bg-light">
                        <?= esc($portfolio['analysis']['analisis_singkat']) ?>
                    </div>
                </li>
            </ul>
        </div>

        <!-- 5. Tindak Lanjut & CQI (Continuous Quality Improvement) -->
        <div class="section mb-5">
            <h5 class="fw-bold mb-3">5. Tindak Lanjut & CQI (Continuous Quality Improvement)</h5>
            <table class="table table-bordered">
                <thead>
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
</body>
</html>

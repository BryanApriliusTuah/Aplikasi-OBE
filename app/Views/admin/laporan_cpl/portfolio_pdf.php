<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pemenuhan CPL - <?= esc($report['identitas']['nama_program_studi']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11pt;
            padding: 30px;
            line-height: 1.6;
        }

        h2 {
            font-size: 18pt;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }

        h5 {
            font-size: 14pt;
            font-weight: bold;
            margin-bottom: 10px;
            margin-top: 20px;
        }

        p, li {
            font-size: 11pt;
            line-height: 1.4;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }

        table th,
        table td {
            padding: 8px;
            border: 1px solid #000;
            word-wrap: break-word;
            vertical-align: top;
        }

        table thead th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
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
        }

        ul {
            margin: 0;
            padding-left: 20px;
        }

        .list-unstyled {
            list-style: none;
            padding-left: 0;
        }

        .align-middle {
            vertical-align: middle;
        }

        @page {
            margin: 2cm;
        }

        @media print {
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
    </style>
</head>
<body>
    <!-- Header -->
    <div class="text-center" style="margin-bottom: 30px;">
        <h2>LAPORAN PEMENUHAN CAPAIAN PEMBELAJARAN LULUSAN (CPL)</h2>
        <p style="margin-top: 15px;">
            <strong>Program Studi:</strong> <?= esc($report['identitas']['nama_program_studi']) ?><br>
            <strong>Tahun Akademik:</strong> <?= esc($report['identitas']['tahun_akademik']) ?><br>
            <strong>Angkatan:</strong> <?= esc($report['identitas']['angkatan']) ?>
        </p>
    </div>

    <!-- 1. Identitas Program Studi -->
    <div class="section">
        <h5>1. Identitas Program Studi</h5>
        <table>
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
            </tbody>
        </table>
    </div>

    <!-- 2. Daftar CPL Program Studi -->
    <div class="section">
        <h5>2. Daftar CPL Program Studi</h5>
        <table>
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

    <!-- 3. Matriks CPMK terhadap CPL -->
    <div class="section">
        <h5>3. Matriks CPMK terhadap CPL</h5>
        <table>
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

    <!-- 4. Rekapitulasi Capaian CPL Berdasarkan CPMK Untuk Satu Angkatan -->
    <div class="section">
        <h5>4. Rekapitulasi Capaian CPL Berdasarkan CPMK Untuk Satu Angkatan</h5>
        <table>
            <thead>
                <tr>
                    <th rowspan="2" style="width: 12%;" class="align-middle">Kode CPL</th>
                    <th rowspan="2" style="width: 15%;" class="align-middle">CPMK</th>
                    <th rowspan="2" style="width: 23%;" class="align-middle">Mata Kuliah Kontributor</th>
                    <th colspan="2">Capaian</th>
                    <th rowspan="2" style="width: 12%;" class="align-middle">Capaian CPL (%)</th>
                </tr>
                <tr>
                    <th style="width: 15%;">Rata-rata CPMK</th>
                    <th style="width: 15%;">CPL (Total Bobot)</th>
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

    <!-- 5. Analisis Pemenuhan CPL -->
    <div class="section">
        <h5>5. Analisis Pemenuhan CPL</h5>
        <ul class="list-unstyled">
            <li style="margin-bottom: 10px;">
                <strong>CPL yang tercapai:</strong>
                <?php if (!empty($report['analysis']['cpl_tercapai'])): ?>
                    <span class="text-success"><?= implode(', ', $report['analysis']['cpl_tercapai']) ?></span>
                <?php else: ?>
                    <span class="text-muted">-</span>
                <?php endif; ?>
            </li>
            <li style="margin-bottom: 10px;">
                <strong>CPL yang belum tercapai:</strong>
                <?php if (!empty($report['analysis']['cpl_tidak_tercapai'])): ?>
                    <?php foreach ($report['analysis']['cpl_tidak_tercapai'] as $cpl): ?>
                        <span class="text-danger"><?= esc($cpl['kode_cpl']) ?> (<?= number_format($cpl['capaian'], 2) ?>%)</span><?= $cpl !== end($report['analysis']['cpl_tidak_tercapai']) ? ', ' : '' ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span class="text-muted">-</span>
                <?php endif; ?>
            </li>
            <li style="margin-bottom: 10px;">
                <strong>Analisis Singkat:</strong>
                <div class="bg-light" style="margin-top: 10px;">
                    <?= esc($report['analysis']['analisis_summary']) ?>
                </div>
            </li>
        </ul>
    </div>

    <!-- 6. Tindak Lanjut dan Rencana Perbaikan (CQI) -->
    <div class="section">
        <h5>6. Tindak Lanjut dan Rencana Perbaikan (CQI)</h5>
        <table>
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
                        <tr>
                            <td><?= esc($cpl['kode_cpl']) ?></td>
                            <td>Nilai CPL < <?= $report['analysis']['passing_threshold'] ?>%</td>
                            <td>Evaluasi mata kuliah kontributor, perbaikan metode pembelajaran dan asesmen, penambahan latihan dan studi kasus</td>
                            <td>Tim Kurikulum & Dosen MK</td>
                            <td>Semester Berikutnya</td>
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

    <!-- 7. Kesimpulan Umum -->
    <div class="section">
        <h5>7. Kesimpulan Umum</h5>
        <div class="bg-light">
            <p>
                Secara umum, dari <strong><?= $report['analysis']['total_cpl'] ?></strong> CPL yang diukur,
                sebanyak <strong><?= $report['analysis']['total_tercapai'] ?></strong> CPL telah tercapai
                dengan persentase capaian minimal <?= $report['analysis']['passing_threshold'] ?>%.
                <?php if ($report['analysis']['total_tidak_tercapai'] > 0): ?>
                    <strong><?= $report['analysis']['total_tidak_tercapai'] ?></strong> CPL belum tercapai dan akan
                    ditindaklanjuti pada tahun ajaran berikutnya melalui revisi pendekatan pembelajaran dan asesmen.
                <?php else: ?>
                    Semua CPL tercapai dengan baik, menunjukkan efektivitas proses pembelajaran yang telah dilaksanakan.
                <?php endif; ?>
            </p>
        </div>
    </div>

    <!-- 8. Lampiran -->
    <div class="section">
        <h5>8. Lampiran</h5>
        <ul>
            <li>Rekap nilai CPMK dari seluruh mata kuliah</li>
            <li>Matriks hubungan CPL – CPMK – MK</li>
            <li>Bukti dokumentasi asesmen</li>
            <li>Notulensi rapat evaluasi CPL</li>
            <li>RPS dari MK Kontributor</li>
        </ul>
    </div>
</body>
</html>

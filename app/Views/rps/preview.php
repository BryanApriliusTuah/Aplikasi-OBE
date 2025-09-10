<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4" style="margin-top: -10px;">
    <a href="<?= base_url('rps') ?>" class="btn btn-outline-secondary d-flex align-items-center">
        <i class="bi bi-arrow-left-circle me-2"></i> Kembali
    </a>
    <div class="btn-group">
        <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="spinner-border spinner-border-sm me-2 d-none" role="status" aria-hidden="true"></span>
            <i class="bi bi-download"></i> Download
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><button class="dropdown-item" id="btn-download-pdf">PDF</button></li>
            <li><button class="dropdown-item" id="btn-download-doc">DOC</button></li>
        </ul>
    </div>
</div>

<div id="rps-content">
    <?php
    $kategoriLabel = [
        'wajib_teori' => 'Wajib Teori', 'wajib_praktikum' => 'Wajib Praktikum',
        'pilihan' => 'Pilihan', 'mkwk' => 'Mata Kuliah Wajib Kurikulum (MKWK)'
    ];
    ?>

    <table class="table table-bordered border-dark mb-0" style="width:100%;">
        <tr>
            <td rowspan="2" style="width:180px; text-align:center; vertical-align:middle; border-right:2px solid #222;">
                <img src="<?= base_url('img/image.png') ?>" alt="Logo Prodi" style="height:100px;">
            </td>
            <td style="text-align:center; border:none; padding:8px 0;">
                <span style="font-weight:bold; font-size:1.3rem;">
                    KEMENTERIAN PENDIDIKAN, RISET DAN TEKNOLOGI
                </span><br>
                <span style="font-weight:bold; font-size:1.2rem;">
                    <?= strtoupper($profil['nama_universitas'] ?? 'UNIVERSITAS') ?>
                </span><br>
                <span style="font-weight:bold; font-size:1.1rem;">
                    FAKULTAS <?= strtoupper($profil['nama_fakultas'] ?? 'FAKULTAS') ?>
                </span><br>
                <span style="font-weight:bold; font-size:1.1rem;">
                    JURUSAN  <?= strtoupper($profil['nama_prodi'] ?? 'PROGRAM STUDI') ?>
                </span>
            </td>
        </tr>
        <tr>
            <td style="text-align:center; border-top:2px solid #222; border-left:none; border-right:none; border-bottom:2px solid #222;">
                <span style="font-weight:bold; font-size:1.3rem; letter-spacing:1px;">RENCANA PEMBELAJARAN SEMESTER</span>
            </td>
        </tr>
    </table>

    <table class="table table-bordered border-dark align-middle text-center mb-0">
        <thead class="table-light">
            <tr>
                <th>MATA KULIAH (MK)</th>
                <th>Kode Mata Kuliah</th>
                <th>Kelompok MK</th>
                <th>BOBOT (sks)</th>
                <th>SEMESTER</th>
                <th>Tgl Penyusunan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><?= esc($rps['nama_mk'] ?? '-') ?></td>
                <td><?= esc($rps['kode_mk'] ?? '-') ?></td>
                <td><?= esc($kategoriLabel[$rps['kategori']] ?? '-') ?></td>
                <td><?= esc($rps['sks'] ?? '-') ?></td>
                <td><?= esc($rps['semester'] ?? '-') ?></td>
                <td><?= !empty($rps['tgl_penyusunan']) ? date('d/m/Y', strtotime($rps['tgl_penyusunan'])) : '-' ?></td>
            </tr>
        </tbody>
    </table>

    <table class="table table-bordered border-dark mb-3 text-center align-middle" style="margin-top:0;">
        <tr>
            <th class="text-center align-middle">Dekan Fakultas</th>
            <th class="text-center align-middle">Koordinator Mata Kuliah</th>
            <th class="text-center align-middle">Ketua Jurusan</th>
        </tr>
        <tr style="height: 100px;">
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>
                <span class="fw-bold">Nama: <?= esc($profil['nama_dekan'] ?? '') ?></span><br>
                <span class="fw-bold">NIP: <?= esc($profil['nip_dekan'] ?? '') ?></span>
            </td>
            <td>
                <span class="fw-bold">Nama: <?= esc($koordinator) ?></span><br>
                <span class="fw-bold">NIP: <?= esc($koordinator_nip) ?></span>
            </td>
            <td>
                <span class="fw-bold">Nama: <?= esc($profil['nama_ketua_prodi'] ?? '') ?></span><br>
                <span class="fw-bold">NIP: <?= esc($profil['nip_ketua_prodi'] ?? '') ?></span>
            </td>
        </tr>
    </table>

    <table class="table table-bordered border-dark mb-3" style="background:white;">
        <tr>
            <th style="width:230px; vertical-align:top;" rowspan="<?php
                $jenisCPL = [
                    'S'  => 'Sikap (S)', 'P'  => 'Pengetahuan (P)',
                    'KU' => 'Keterampilan Umum (KU)', 'KK' => 'Keterampilan Khusus (KK)',
                ];
                $totalBaris = 1;
                foreach($jenisCPL as $jenis => $label) {
                    $list = array_filter($cpl, fn($row) => $row['jenis_cpl'] == $jenis);
                    $totalBaris++;
                    $totalBaris += count($list) ?: 1;
                }
                $totalBaris += 1 + max(1, count($cpmk));
                $totalBaris += 1 + max(1, count($subcpmk));
                $totalBaris += 2 + max(1, count($cpmk));
                echo $totalBaris;
            ?>">
                <span style="font-weight:bold">Capaian Pembelajaran</span>
            </th>
            <th colspan="2" class="text-center fw-bold" style="white-space: normal; word-break: break-word; max-width: 400px;">
                CPL-PRODI yang dibebankan pada Mata Kuliah
            </th>
        </tr>
        <tr>
            <th style="width:120px; white-space: normal; word-break: break-word; max-width: 120px;">Kode</th>
            <th style="white-space: normal; word-break: break-word; max-width: 400px;">Deskripsi</th>
        </tr>
        <?php foreach ($jenisCPL as $jenis => $label):
            $list = array_filter($cpl, fn($row) => $row['jenis_cpl'] == $jenis); ?>
            <tr class="table-secondary">
                <td colspan="2" class="fw-bold"><?= $label ?></td>
            </tr>
            <?php if (count($list)): ?>
                <?php foreach($list as $row): ?>
                <tr>
                    <td style="white-space: normal; word-break: break-word; max-width: 120px;"><?= esc($row['kode_cpl']) ?></td>
                    <td style="white-space: normal; word-break: break-word; max-width: 400px;"><?= esc($row['deskripsi']) ?></td>
                </tr>
                <?php endforeach ?>
            <?php else: ?>
                <tr><td colspan="2" class="text-center text-muted">-</td></tr>
            <?php endif ?>
        <?php endforeach ?>
        <tr>
            <th colspan="2" class="fw-bold" style="background:#f5f5f5;">Capaian Pembelajaran Mata Kuliah (CPMK)</th>
        </tr>
        <?php if(count($cpmk)): ?>
            <?php foreach($cpmk as $row): ?>
            <tr>
                <td style="white-space: normal; word-break: break-word; max-width: 120px;"><?= esc($row['kode_cpmk']) ?></td>
                <td style="white-space: normal; word-break: break-word; max-width: 400px;"><?= esc($row['deskripsi']) ?></td>
            </tr>
            <?php endforeach ?>
        <?php else: ?>
            <tr><td colspan="2" class="text-center text-muted">-</td></tr>
        <?php endif ?>

        <tr>
            <th colspan="2" class="fw-bold" style="background:#f5f5f5;">
                Sub Capaian Pembelajaran Mata Kuliah (Sub-CPMK)
            </th>
        </tr>
        <?php if(count($subcpmk)): ?>
            <?php foreach($subcpmk as $row): ?>
                <?php if (!empty($row['kode_sub_cpmk']) && !empty($row['deskripsi'])): ?>
                <tr>
                    <td style="white-space: normal; word-break: break-word; max-width: 120px;"><?= esc($row['kode_sub_cpmk']) ?></td>
                    <td style="white-space: normal; word-break: break-word; max-width: 400px;"><?= esc($row['deskripsi']) ?></td>
                </tr>
                <?php endif ?>
            <?php endforeach ?>
        <?php else: ?>
            <tr><td colspan="2" class="text-center text-muted">-</td></tr>
        <?php endif ?>

        <tr>
            <td colspan="3" style="padding:0;">
                <table class="table table-bordered mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:160px;">CPMK - SubCPMK</th>
                            <?php foreach ($subcpmk as $sub): ?>
                                <th class="text-center"><?= esc($sub['kode_sub_cpmk']) ?></th>
                            <?php endforeach ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cpmk as $cp): ?>
                            <tr>
                                <th><?= esc($cp['kode_cpmk']) ?></th>
                                <?php foreach ($subcpmk as $sub): ?>
                                    <td class="text-center">
                                        <?php if ($sub['cpmk_id'] == $cp['id']): ?>
                                            √
                                        <?php endif ?>
                                    </td>
                                <?php endforeach ?>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>

    <table class="table table-bordered border-dark mb-0" style="background:white;">
        <tr>
            <th style="width:250px;">Deskripsi Singkat Mata Kuliah</th>
            <td style="white-space: normal; word-break: break-word; max-width: 400px;"><?= esc($rps['deskripsi_singkat'] ?? '-') ?></td>
        </tr>
    </table>

    <table class="table table-bordered border-dark mb-0" style="background:white;">
        <tr>
            <th style="width:250px;">Materi Pembelajaran</th>
            <td style="white-space: normal; word-break: break-word; max-width: 400px;">
                <ol class="mb-0">
                    <?php foreach($materi_pembelajaran as $m): ?>
                        <li><?= esc($m) ?></li>
                    <?php endforeach ?>
                </ol>
            </td>
        </tr>
    </table>

    <table class="table table-bordered border-dark mb-0" style="background:white;">
        <tr>
            <th style="width:250px;vertical-align:top;">Daftar Referensi</th>
            <td style="white-space: normal; word-break: break-word; max-width: 400px;">
                <div>
                    <strong>Referensi Utama</strong>
                    <ol class="mb-1" style="margin-left:20px;">
                        <?php if (count($referensi_utama)): ?>
                            <?php foreach($referensi_utama as $r): ?>
                                <li><?= esc($r['penulis']) ?>. <span><?= esc($r['judul']) ?>.</span> <?= esc($r['penerbit']) ?>. <?= esc($r['tahun']) ?>. <?= esc($r['keterangan']) ?></li>
                            <?php endforeach ?>
                        <?php else: ?>
                            <li>-</li>
                        <?php endif ?>
                    </ol>
                </div>
                <div>
                    <strong>Referensi Pendukung</strong>
                    <ol class="mb-1" style="margin-left:20px;">
                        <?php if (count($referensi_pendukung)): ?>
                            <?php foreach($referensi_pendukung as $r): ?>
                                <li><?= esc($r['penulis']) ?>. <span><?= esc($r['judul']) ?>.</span> <?= esc($r['penerbit']) ?>. <?= esc($r['tahun']) ?>. <?= esc($r['keterangan']) ?></li>
                            <?php endforeach ?>
                        <?php else: ?>
                            <li>-</li>
                        <?php endif ?>
                    </ol>
                </div>
            </td>
        </tr>
    </table>

    <table class="table table-bordered border-dark mb-0" style="background:white;">
        <tr>
            <th style="width:250px;">Dosen Pengampu</th>
            <td style="white-space: normal; word-break: break-word; max-width: 400px;">
                <ol class="mb-0">
                    <?php foreach($pengampu as $d): ?>
                        <li><?= esc($d) ?></li>
                    <?php endforeach ?>
                </ol>
            </td>
        </tr>
    </table>

    <table class="table table-bordered border-dark mb-4" style="background:white;">
        <tr>
            <th style="width:250px;">Mata Kuliah Prasyarat</th>
            <td style="white-space: normal; word-break: break-word; max-width: 400px;">
                <?php if (count($mk_prasyarat)): ?>
                    <ol class="mb-0">
                        <?php foreach($mk_prasyarat as $m): ?>
                            <li><?= esc($m['kode_mk'] . ' - ' . $m['nama_mk']) ?></li>
                        <?php endforeach ?>
                    </ol>
                <?php else: ?>
                    <span>-</span>
                <?php endif ?>
            </td>
        </tr>
    </table>

    <h5 class="fw-bold">Rencana Kegiatan Perkuliahan (Mingguan)</h5>
    <table class="table table-bordered border-dark">
        <thead class="table-light">
            <tr>
                <th style="white-space: nowrap; vertical-align: middle;">Minggu</th>
                <th style="vertical-align: middle;">Sub-CPMK</th>
                <th style="vertical-align: middle;">Indikator Penilaian</th>
                <th style="vertical-align: middle;">Kriteria Penilaian</th>
                <th style="white-space: nowrap; vertical-align: middle;">Teknik Penilaian</th>
                <th style="white-space: nowrap; vertical-align: middle;">Metode Pembelajaran</th>
                <th style="white-space: nowrap; vertical-align: middle;">Materi Pembelajaran</th>
                <th style="vertical-align: middle;">Bobot</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($mingguan as $m): ?>
                <tr>
                    <td class="text-center"><?= esc($m['minggu']) ?></td>
                    <td><?= $m['sub_cpmk_formatted'] ?></td>
                    <td><?= $m['indikator_formatted'] ?></td>
                    <td><?= $m['kriteria_penilaian_formatted'] ?></td>
                    <td><?= $m['teknik_penilaian_formatted'] ?></td>
                    <td><?= $m['metode_pembelajaran_formatted'] ?></td>
                    <td><?= esc($m['materi_pembelajaran']) ?></td>
                    <td class="text-center"><?= esc($m['bobot']) ?></td>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <div class="row mt-5 mb-5" style="margin-top:40px;">
        <div class="col-12 text-center">
            Palangka Raya, ...............................
        </div>
    </div>

    <table style="width:100%; border:none; border-collapse:collapse;">
        <tr>
            <td style="width:50%; text-align:center; vertical-align:top; border:none;">
                Mengetahui<br>
                Ketua Jurusan<br><br><br><br><br>
                <span style="font-weight:bold;">
                    <?= esc($profil['nama_ketua_prodi'] ?? '-') ?><br>
                    NIP. <?= esc($profil['nip_ketua_prodi'] ?? '-') ?>
                </span>
            </td>
            <td style="width:50%; text-align:center; vertical-align:top; border:none;">
                Koordinator Mata Kuliah,<br><br><br><br><br><br>
                <span style="font-weight:bold;">
                    <?= esc($koordinator ?? '-') ?><br>
                    NIP. <?= esc($koordinator_nip ?? '-') ?>
                </span>
            </td>
        </tr>
    </table>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const downloadButton = document.querySelector('.btn-outline-success');
    const spinner = downloadButton.querySelector('.spinner-border');
    const btnPdf = document.getElementById('btn-download-pdf');
    const btnDoc = document.getElementById('btn-download-doc');
    const rpsContent = document.getElementById('rps-content');
    
    const cleanFileName = "RPS_<?= preg_replace('/[^a-zA-Z0-9\-\_]/', '', $rps['nama_mk'] ?? 'File') ?>";

    function setLoading(isLoading) {
        if (isLoading) {
            spinner.classList.remove('d-none');
            btnPdf.disabled = true;
            btnDoc.disabled = true;
        } else {
            spinner.classList.add('d-none');
            btnPdf.disabled = false;
            btnDoc.disabled = false;
        }
    }

    btnPdf.addEventListener('click', function () {
        setLoading(true);
        rpsContent.style.width = '1120px';
        const opt = {
            margin:       [5, 5, 5, 5],
            filename:     `${cleanFileName}.pdf`,
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, letterRendering: true },
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };
        html2pdf().from(rpsContent).set(opt).save().then(() => {
            rpsContent.style.width = ''; 
            setLoading(false);
        }).catch((err) => {
            rpsContent.style.width = '';
            setLoading(false);
            console.error(err);
            alert('Gagal membuat PDF. Silakan coba lagi.');
        });
    });

    btnDoc.addEventListener('click', function () {
        setLoading(true);
        const styles = `
            <style>
                @page { size: A4 portrait; margin: 20mm 15mm 20mm 15mm; }
                body { font-family: Arial, sans-serif; font-size: 10pt; }
                table, th, td { border: 1px solid black; border-collapse: collapse; padding: 4px; }
                table { width: 100%; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .text-center { text-align: center; }
                .fw-bold { font-weight: bold; }
            </style>
        `;
        let header = "<html xmlns:o='urn:schemas-microsoft-com:office:office' "+
            "xmlns:w='urn:schemas-microsoft-com:office:word' "+
            "xmlns='http://www.w3.org/TR/REC-html40'>"+
            "<head><meta charset='utf-8'><title>RPS</title>" + styles + "</head><body>";
        let footer = "</body></html>";
        let sourceHTML = header + rpsContent.innerHTML + footer;
        try {
            var blob = new Blob(['\ufeff', sourceHTML], {
                type: 'application/msword'
            });
            saveAs(blob, `${cleanFileName}.doc`);
        } catch (e) {
            console.error("Gagal menyimpan file:", e);
            alert('Gagal membuat DOC. Browser Anda mungkin tidak mendukung fitur ini.');
        } finally {
            setLoading(false);
        }
    });
});
</script>
<?= $this->endSection() ?>
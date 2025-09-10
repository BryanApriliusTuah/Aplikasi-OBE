<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Bobot Penilaian Berdasarkan CPL</h2>
    <!-- DOWNLOAD DROPDOWN HIJAU -->
    <div class="btn-group">
        <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-download"></i> Download
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="<?= base_url('bobot-penilaian-cpl/export/pdf') ?>">
                    <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="<?= base_url('bobot-penilaian-cpl/export/excel') ?>">
                    <i class="bi bi-file-earmark-excel text-success"></i> Excel
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="table-responsive">
<table class="table table-bordered">
    <thead class="table-primary text-center align-middle">
        <tr>
            <th>CPL</th>
            <th>MK</th>
            <th>Nama MK</th>
            <th>CPMK</th>
            <th>Partisipasi</th>
            <th>Observasi</th>
            <th>Unjuk Kerja</th>
            <th>Project Based</th>
            <th>UTS</th>
            <th>UAS</th>
            <th>Tes Lisan</th>
            <th>Total</th>
        </tr>
    </thead>
    <?php
        // Daftar teknik penilaian
        $teknik_list = [
            'partisipasi'      => 'Partisipasi',
            'observasi'        => 'Observasi',
            'unjuk_kerja'      => 'Unjuk Kerja',
            'case_method'      => 'Case Method/Project Based',
            'tes_tulis_uts'    => 'UTS',
            'tes_tulis_uas'    => 'UAS',
            'tes_lisan'        => 'Tes Lisan'
        ];

        // Grouping data by [CPL][MK]
        $grouped = [];
        foreach ($penilaian as $row) {
            $grouped[$row['kode_cpl']][$row['kode_mk']]['nama_mk'] = $row['nama_mk'];
            $grouped[$row['kode_cpl']][$row['kode_mk']]['items'][] = $row;
        }
    ?>
    <tbody>
        <?php
        foreach ($grouped as $kode_cpl => $mks) {
            $rowspan_cpl = array_sum(array_map(fn($mk) => count($mk['items']), $mks));
            $cpl_printed = false;
            foreach ($mks as $kode_mk => $mk) {
                $rowspan_mk = count($mk['items']);
                $mk_printed = false;
                foreach ($mk['items'] as $row) {
                    echo '<tr>';
                    if (!$cpl_printed) {
                        echo '<td rowspan="'.$rowspan_cpl.'" class="align-middle text-center fw-bold" style="white-space:nowrap;">'.esc($kode_cpl).'</td>';
                        $cpl_printed = true;
                    }
                    if (!$mk_printed) {
                        echo '<td rowspan="'.$rowspan_mk.'" class="align-middle text-center" style="white-space:nowrap;">'.esc($kode_mk).'</td>';
                        echo '<td rowspan="'.$rowspan_mk.'" class="align-middle text-start">'.esc($mk['nama_mk']).'</td>';
                        $mk_printed = true;
                    }
                    echo '<td class="align-middle" style="white-space:nowrap;">'.esc($row['kode_cpmk']).'</td>';
                    // FIX: Ambil dari $row langsung (hasil dari controller), bukan dari teknik_array/json
                    $total = 0;
                    foreach ($teknik_list as $key => $label) {
                        $bobot = isset($row[$key]) ? (int)$row[$key] : 0;
                        echo '<td class="text-center">'.($bobot ? '<b>'.$bobot.'</b>' : '0').'</td>';
                        $total += $bobot;
                    }
                    echo '<td class="text-center fw-bold">'.$total.'</td>';
                    echo '</tr>';
                }
            }
        }
        ?>
    </tbody>
</table>
</div>
<?= $this->endSection() ?>

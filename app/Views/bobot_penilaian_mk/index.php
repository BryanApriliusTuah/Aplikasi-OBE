<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Bobot Penilaian Berdasarkan MK</h2>
    <div class="btn-group">
        <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-download"></i> Download
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="<?= base_url('bobot-mk/export/pdf') ?>">
                    <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="<?= base_url('bobot-mk/export/excel') ?>">
                    <i class="bi bi-file-earmark-excel text-success"></i> Excel
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="table-responsive">
<table class="table table-bordered">
    <thead class="table-warning text-center align-middle">
        <tr>
            <th>MK</th>
            <th>CPL</th>
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
    <tbody>
    <?php
    // Pakai variabel $grouped dan $teknik_list dari controller!
    foreach ($grouped as $mk => $cpls) {
        $rowspan_mk = array_sum(array_map('count', $cpls));
        $mk_printed = false;
        $total_per_mk = array_fill_keys(array_keys($teknik_list), 0);
        $grand_total_per_mk = 0;
        foreach ($cpls as $cpl => $cpmks) {
            $rowspan_cpl = count($cpmks);
            $cpl_printed = false;
            foreach ($cpmks as $row) {
                $teknik_array = json_decode($row['teknik_penilaian'] ?? '{}', true) ?: [];
                echo '<tr>';
                // MK
                if (!$mk_printed) {
                    echo '<td rowspan="'.$rowspan_mk.'" class="align-middle">'.esc($mk).'</td>';
                    $mk_printed = true;
                }
                // CPL
                if (!$cpl_printed) {
                    echo '<td rowspan="'.$rowspan_cpl.'" class="align-middle">'.esc($cpl).'</td>';
                    $cpl_printed = true;
                }
                // CPMK (kode saja)
                echo '<td class="align-middle">'.esc($row['kode_cpmk']).'</td>';

                $total = 0;
                foreach ($teknik_list as $key => $label) {
                    $bobot = isset($teknik_array[$key]) ? (int)$teknik_array[$key] : 0;
                    echo '<td class="text-center">'.($bobot ? '<b>'.$bobot.'</b>' : '0').'</td>';
                    $total += $bobot;
                    $total_per_mk[$key] += $bobot;
                }
                echo '<td class="text-center fw-bold">'.$total.'</td>';
                $grand_total_per_mk += $total;
                echo '</tr>';
            }
        }
        // Baris Total per MK
        echo '<tr class="tr-total-mk fw-bold text-center">';
        echo '<td colspan="3">Total</td>';
        foreach ($teknik_list as $key => $label) {
            echo '<td>'.$total_per_mk[$key].'</td>';
        }
        echo '<td>'.$grand_total_per_mk.'</td>';
        echo '</tr>';
    }
    ?>
    </tbody>
</table>
</div>
<?= $this->endSection() ?>

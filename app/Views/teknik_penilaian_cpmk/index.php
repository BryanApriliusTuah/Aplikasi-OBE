<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Teknik Penilaian CPMK</h2>
    <!-- DOWNLOAD DROPDOWN HIJAU -->
    <div class="btn-group">
        <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-download"></i> Download
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="<?= base_url('teknik-penilaian-cpmk/export/pdf') ?>" target="_blank">
                    <i class="bi bi-file-pdf text-danger"></i> PDF
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="<?= base_url('teknik-penilaian-cpmk/export/excel') ?>">
                    <i class="bi bi-file-earmark-excel text-success"></i> Excel
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Dropdown Jumlah Baris Per Halaman DI BAWAH JUDUL -->
<form method="get" class="mb-3">
    <label for="perPage" class="form-label mb-0 me-2">Tampilkan</label>
    <select name="perPage" id="perPage" class="form-select d-inline-block w-auto me-2" onchange="this.form.submit()">
        <option value="5" <?= $perPage == 5 ? 'selected' : '' ?>>5</option>
        <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
        <option value="20" <?= $perPage == 20 ? 'selected' : '' ?>>20</option>
        <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
        <option value="1000" <?= $perPage == 1000 ? 'selected' : '' ?>>Semua</option>
    </select>
    <span>baris per halaman</span>
</form>


<div class="table-responsive">
<table class="table table-bordered">
    <thead class="table-primary text-center align-middle">
        <tr>
            <th>CPL</th>
            <th>Mata Kuliah</th>
            <th>CPMK</th>
            <th>Partisipasi</th>
            <th>Observasi</th>
            <th>Unjuk Kerja</th>
            <th>Case Method/Project Based</th>
            <th>UTS</th>
            <th>UAS</th>
            <th>Lisan</th>
        </tr>
    </thead>
    <tbody>
    <?php
    // Hitung rowspan untuk CPL dan MK
    $cpl_mk_count = [];
    foreach ($penilaian as $row) {
        $key = $row['kode_cpl'].'|'.$row['nama_mk'];
        if (!isset($cpl_mk_count[$key])) $cpl_mk_count[$key] = 0;
        $cpl_mk_count[$key]++;
    }

    $printed_cpl_mk = [];
    foreach ($penilaian as $row):
        $key = $row['kode_cpl'].'|'.$row['nama_mk'];
        echo '<tr>';
        // Merge CPL
        if (!isset($printed_cpl_mk[$key])) {
            echo '<td rowspan="'.$cpl_mk_count[$key].'">'.esc($row['kode_cpl']).'</td>';
            echo '<td rowspan="'.$cpl_mk_count[$key].'">'.esc($row['nama_mk']).'</td>';
            $printed_cpl_mk[$key] = 1;
        }
        // CPMK
        echo '<td>'.esc($row['kode_cpmk']).'</td>';

        // Teknik penilaian
        $teknikData = $row['teknik'];
        if (is_string($teknikData)) {
            $teknikData = json_decode($teknikData, true) ?: [];
        }

        $teknik_list = [
            'partisipasi', 'observasi', 'unjuk_kerja', 'case_method', 'tes_tulis_uts', 'tes_tulis_uas', 'tes_lisan'
        ];
        foreach ($teknik_list as $teknik) {
            echo '<td class="text-center">';
            if (isset($teknikData[$teknik]) && $teknikData[$teknik]) {
                echo esc($teknikData[$teknik]); // Tampilkan bobot
            }
            echo '</td>';
        }
        echo '</tr>';
    endforeach;
    ?>
    </tbody>
</table>
</div>

<!-- Bootstrap Pagination -->
<?php if ($totalPages > 1): ?>
<nav>
    <ul class="pagination justify-content-center">
        <!-- Prev -->
        <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
            <a class="page-link" href="?perPage=<?= $perPage ?>&page=<?= $page-1 ?>" tabindex="-1">«</a>
        </li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item<?= $page == $i ? ' active' : '' ?>">
                <a class="page-link" href="?perPage=<?= $perPage ?>&page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor ?>
        <!-- Next -->
        <li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
            <a class="page-link" href="?perPage=<?= $perPage ?>&page=<?= $page+1 ?>">»</a>
        </li>
    </ul>
</nav>
<?php endif ?>

<?= $this->endSection() ?>

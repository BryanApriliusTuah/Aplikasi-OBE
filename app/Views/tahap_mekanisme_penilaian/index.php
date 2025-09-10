<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Tahap dan Mekanisme Penilaian</h2>
    <div class="btn-group">
        <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-download"></i> Download
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="<?= base_url('tahap-mekanisme-penilaian/export/pdf') ?>">
                    <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="<?= base_url('tahap-mekanisme-penilaian/export/excel') ?>">
                    <i class="bi bi-file-earmark-excel text-success"></i> Excel
                </a>
            </li>
        </ul>
    </div>
</div>

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
    <?php if (isset($page)): ?>
        <input type="hidden" name="page" value="<?= esc($page) ?>">
    <?php endif ?>
</form>


<div class="table-responsive">
    <table class="table table-bordered table-sm align-middle">
        <thead class="table-primary text-center align-middle">
            <tr>
                <th>CPL</th>
                <th>Mata Kuliah</th>
                <th>CPMK</th>
                <th>SubCPMK</th>
                <th>Tahap Penilaian</th>
                <th>Teknik Penilaian</th>
                <th>Instrumen</th>
                <th>Kriteria</th>
                <th>Bobot</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $rowspan_data = [];
        foreach ($penilaian as $row) {
            $key_cpl_mk = $row['kode_cpl'].'|'.$row['nama_mk'];
            if (!isset($rowspan_data[$key_cpl_mk])) {
                $rowspan_data[$key_cpl_mk] = 0;
            }
            $rowspan_data[$key_cpl_mk]++;
        }
        $printed_keys = [];
        foreach ($penilaian as $row):
            $key_cpl_mk = $row['kode_cpl'].'|'.$row['nama_mk'];
        ?>
            <tr>
                <?php if (!isset($printed_keys[$key_cpl_mk])): ?>
                    <td rowspan="<?= $rowspan_data[$key_cpl_mk] ?>"><?= esc($row['kode_cpl']) ?></td>
                    <td rowspan="<?= $rowspan_data[$key_cpl_mk] ?>"><?= esc($row['nama_mk']) ?></td>
                    <?php $printed_keys[$key_cpl_mk] = true; ?>
                <?php endif ?>

                <td><?= esc($row['kode_cpmk']) ?></td>
                <td><?= esc($row['kode_sub_cpmk'] ?? '-') ?></td>
                <td><?= $row['tahap_penilaian'] ?></td>
                <td><?= $row['teknik_penilaian'] ?></td>
                <td><?= $row['instrumen'] ?></td>
                <td><?= $row['kriteria_penilaian'] ?></td>
                <td class="text-center"><?= esc($row['bobot']) ?></td>
            </tr>
        <?php endforeach ?>
        <?php if (empty($penilaian)): ?>
            <tr><td colspan="9" class="text-center">Data tidak ditemukan.</td></tr>
        <?php endif; ?>
        </tbody>
        <tfoot>
            <?php foreach ($total_bobot_per_mk as $mk => $total): ?>
            <tr class="table-light">
                <th colspan="8" class="text-end">Total Bobot (<?= esc($mk) ?>)</th>
                <th class="text-center"><?= $total ?></th>
            </tr>
            <?php endforeach ?>
        </tfoot>
    </table>
</div>

<?php if (isset($totalPages) && $totalPages > 1): ?>
<nav>
    <ul class="pagination justify-content-center">
        <li class="page-item<?= $page <= 1 ? ' disabled' : '' ?>">
            <a class="page-link" href="?perPage=<?= $perPage ?>&page=<?= $page-1 ?>" tabindex="-1">«</a>
        </li>
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item<?= $page == $i ? ' active' : '' ?>">
                <a class="page-link" href="?perPage=<?= $perPage ?>&page=<?= $i ?>"><?= $i ?></a>
            </li>
        <?php endfor ?>
        <li class="page-item<?= $page >= $totalPages ? ' disabled' : '' ?>">
            <a class="page-link" href="?perPage=<?= $perPage ?>&page=<?= $page+1 ?>">»</a>
        </li>
    </ul>
</nav>
<?php endif ?>

<?= $this->endSection() ?>
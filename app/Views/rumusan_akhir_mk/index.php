<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Rumusan Nilai Akhir Mata Kuliah</h2>
    <!-- DOWNLOAD DROPDOWN HIJAU -->
    <div class="btn-group">
        <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-download"></i> Download
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="<?= base_url('rumusan-akhir-mk/export/pdf') ?>">
                    <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="<?= base_url('rumusan-akhir-mk/export/excel') ?>">
                    <i class="bi bi-file-earmark-excel text-success"></i> Excel
                </a>
            </li>
        </ul>
    </div>
</div>

<?php foreach ($rekap as $mkid => $mk): ?>
    <table class="table table-bordered mb-3">
        <thead class="table-warning">
            <tr>
                <th>MK</th>
                <th>CPL</th>
                <th>CPMK</th>
                <th>Skor Maks</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($mk['detail'] as $i => $row): ?>
            <tr>
                <?php if ($i == 0): ?>
                    <td rowspan="<?= count($mk['detail']) ?>" class="align-middle fw-bold text"><?= esc($mk['nama_mk']) ?></td>
                <?php endif ?>
                <td><?= esc($row['kode_cpl']) ?></td>
                <td><?= esc($row['kode_cpmk']) ?></td>
                <td class="text-center"><?= esc($row['bobot']) ?></td>
            </tr>
        <?php endforeach ?>
        <tr class="table-success">
            <td colspan="3" class="fw-bold text-end">Nilai MK <?= esc($mk['nama_mk']) ?> =</td>
            <td class="fw-bold text-center"><?= esc($mk['total']) ?></td>
        </tr>
        </tbody>
    </table>
<?php endforeach ?>

<?= $this->endSection() ?>

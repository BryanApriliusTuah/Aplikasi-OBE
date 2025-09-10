<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Rumusan Nilai Akhir CPL</h2>
    <!-- DOWNLOAD DROPDOWN HIJAU -->
    <div class="btn-group">
        <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-download"></i> Download
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="<?= base_url('rumusan-akhir-cpl/export/pdf') ?>">
                    <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="<?= base_url('rumusan-akhir-cpl/export/excel') ?>">
                    <i class="bi bi-file-earmark-excel text-success"></i> Excel
                </a>
            </li>
        </ul>
    </div>
</div>

<?php foreach ($rekap as $cpl => $blok): ?>
    <table class="table table-bordered mb-3">
        <thead class="table-warning">
            <tr>
                <th>CPL</th>
                <th>MK</th>
                <th>CPMK</th>
                <th>Skor Maks</th>
            </tr>
        </thead>
        <tbody>
        <?php $rowspan = count($blok['detail']); $i = 0; ?>
        <?php foreach ($blok['detail'] as $row): ?>
            <tr>
                <?php if ($i === 0): ?>
                    <td rowspan="<?= $rowspan ?>" class="align-middle fw-bold"><?= esc($cpl) ?></td>
                <?php endif ?>
                <td><?= esc($row['nama_mk']) ?></td>
                <td><?= esc($row['kode_cpmk']) ?></td>
                <td class="text-end"><?= esc($row['bobot']) ?></td>
            </tr>
            <?php $i++; ?>
        <?php endforeach ?>
        <tr class="table-success">
            <td colspan="3" class="fw-bold">Nilai <?= esc($cpl) ?> =</td>
            <td class="fw-bold text-end"><?= esc($blok['total']) ?></td>
        </tr>
        </tbody>
    </table>
<?php endforeach ?>

<?= $this->endSection() ?>

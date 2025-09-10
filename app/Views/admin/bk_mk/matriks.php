<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<h2 class="fw-bold mb-3">Matriks Pemetaan Bahan Kajian ke Mata Kuliah</h2>
<div class="d-flex justify-content-start gap-2 mb-4">
        <a href="<?= base_url('admin/bkmk') ?>" class="btn btn-secondary mb-3">
            <i class="bi bi-arrow-left"></i> Kembali ke List BK-MK
        </a>
<!-- Dropdown Download Hijau -->
        <div class="btn-group">
            <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download"></i> Download
            </button>
            <ul class="dropdown-menu dropdown-menu-start">
                <li>
                    <a class="dropdown-item" href="<?= base_url('admin/bkmk/export/matriks') ?>">
                        <i class="bi bi-file-earmark-excel text-success"></i> Excel
                    </a>
                </li>
            </ul>
        </div>
</div>
<div class="table-responsive" style="max-height:80vh;">
    <table class="table table-bordered table-sm align-middle text-center">
        <thead class="table-light align-middle sticky-top" style="z-index:2;">
            <tr>
                <th rowspan="2" class="align-middle" style="min-width:40px">No</th>
                <th rowspan="2" class="align-middle" style="min-width:90px">Kode MK</th>
                <th rowspan="2" class="align-middle" style="min-width:200px">Nama Mata Kuliah</th>
                <th rowspan="2" class="align-middle" style="min-width:40px">SKS</th>
                <th colspan="<?= count($bkList) ?>">Bahan Kajian (BK)</th>
            </tr>
            <tr>
                <?php foreach ($bkList as $bk): ?>
                    <th class="text-nowrap" style="min-width:90px"><?= esc($bk['kode_bk']) ?></th>
                <?php endforeach ?>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; foreach ($mkList as $mk): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($mk['kode_mk']) ?></td>
                    <td class="text-start"><?= esc($mk['nama_mk']) ?></td>
                    <td><?= esc($mk['sks']) ?></td>
                    <?php foreach ($bkList as $bk): ?>
                        <td>
                            <?php if (!empty($matrix[$mk['id']][$bk['id']])): ?>
                                <i class="bi bi-check-circle-fill text-success"></i>
                            <?php endif ?>
                        </td>
                    <?php endforeach ?>
                </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>

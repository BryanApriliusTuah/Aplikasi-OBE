<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Pemetaan MK-CPL–CPMK</h2>
        <div class="btn-group">
            <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download"></i> Download
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="<?= base_url('admin/mk-cpl-cpmk/export/excel') ?>">
                        <i class="bi bi-file-earmark-excel text-success"></i> Excel
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0 table-pemetaan">
                    <thead class="table-light">
                        <tr>
                            <th rowspan="2" class="text-center align-middle mk-column">Mata Kuliah</th>
                            <th class="text-center" colspan="<?= count($cpl) ?>">CPL</th>
                        </tr>
                        <tr>
                            <?php foreach ($cpl as $cp): ?>
                                <th class="text-center cpl-header"><?= esc($cp['kode_cpl']) ?></th>
                            <?php endforeach ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data as $mk): ?>
                            <tr>
                                <td class="align-middle fw-bold"><?= esc($mk['nama_mk']) ?></td>
                                <?php foreach ($cpl as $cp): ?>
                                    <td class="text-center cpl-cell">
                                        <?php if (!empty($mk['cpl'][$cp['kode_cpl']])): ?>
                                            <?= implode('<br>', $mk['cpl'][$cp['kode_cpl']]) ?>
                                        <?php endif ?>
                                    </td>
                                <?php endforeach ?>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
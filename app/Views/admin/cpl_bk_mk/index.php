<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Pemetaan CPL–BK–MK</h2>
    <div class="btn-group">
        <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-download"></i> Download
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="<?= base_url('admin/cpl-bk-mk/exportExcel') ?>">
                    <i class="bi bi-file-earmark-excel text-success"></i> Excel
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="<?= base_url('admin/cpl-bk-mk/exportPdf') ?>">
                    <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered align-middle table-hover mb-0">
                <thead class="table-primary">
                    <tr>
                        <th class="text-center align-middle" style="width:15%;">Kode BK</th>
                        <?php foreach ($cpl as $c): ?>
                            <th class="text-center align-middle"><?= esc($c['kode_cpl']) ?></th>
                        <?php endforeach ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($bk)): ?>
                        <tr>
                            <td colspan="<?= count($cpl) + 1 ?>" class="text-center text-secondary py-5">Data Bahan Kajian belum ada.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($bk as $b): ?>
                            <tr>
                                <td class="fw-bold text-center" style="white-space: normal;"><?= esc($b['kode_bk']) ?></td>
                                <?php foreach ($cpl as $c): ?>
                                    <td class="text-start" style="white-space: normal;">
                                        <?php if (isset($mapping[$b['id']][$c['id']])): ?>
                                            <?= implode(', ', array_map('esc', $mapping[$b['id']][$c['id']])) ?>
                                        <?php endif ?>
                                    </td>
                                <?php endforeach ?>
                            </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
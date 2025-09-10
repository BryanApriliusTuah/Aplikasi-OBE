<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Peta Pemenuhan CPL per Semester</h2>
        <div class="btn-group">
            <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download"></i> Download
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="<?= base_url('admin/peta-cpl/exportExcel') ?>">
                        <i class="bi bi-file-earmark-excel text-success"></i> Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= base_url('admin/peta-cpl/exportPdf') ?>">
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
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">CPL</th>
                            <?php for ($i = 1; $i <= 8; $i++): ?>
                                <th class="text-center">Semester <?= $i ?></th>
                            <?php endfor ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cplList)): ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">Belum ada data CPL.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cplList as $cpl): ?>
                                <tr>
                                    <td class="fw-bold text-center" style="white-space: nowrap;"><?= esc($cpl['kode_cpl']) ?></td>
                                    <?php for ($s = 1; $s <= 8; $s++): ?>
                                        <td style="white-space: normal;">
                                            <?php if (!empty($dataMatriks[$cpl['id']][$s])): ?>
                                                <?= esc(implode(', ', $dataMatriks[$cpl['id']][$s])) ?>
                                            <?php endif ?>
                                        </td>
                                    <?php endfor ?>
                                </tr>
                            <?php endforeach ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
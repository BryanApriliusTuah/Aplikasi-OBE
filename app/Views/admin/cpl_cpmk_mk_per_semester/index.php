<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Pemenuhan CPL & CPMK oleh Mata Kuliah per Semester</h2>
        <div class="btn-group">
            <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download"></i> Download
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="<?= base_url('admin/cpl-cpmk-mk-per-semester/exportExcel') ?>">
                        <i class="bi bi-file-earmark-excel text-success"></i> Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= base_url('admin/cpl-cpmk-mk-per-semester/exportPdf') ?>">
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
                            <th class="text-center">Kode CPL</th>
                            <th class="text-center">Kode CPMK</th>
                            <?php for ($i = 1; $i <= 8; $i++): ?>
                                <th class="text-center">Semester <?= $i ?></th>
                            <?php endfor ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($data)): ?>
                             <tr>
                                <td colspan="10" class="text-center text-secondary py-5">Belum ada data pemetaan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($data as $item): ?>
                                <?php $cpmkCount = count($item['cpmk']); $isFirst = true; ?>
                                <?php foreach ($item['cpmk'] as $cpmk): ?>
                                    <tr>
                                        <?php if ($isFirst): ?>
                                            <td rowspan="<?= $cpmkCount ?>" class="text-center fw-bold" style="vertical-align: middle;">
                                                <?= esc($item['cpl']['kode_cpl']) ?>
                                            </td>
                                            <?php $isFirst = false; ?>
                                        <?php endif; ?>
                                        <td class="text-center" style="white-space:nowrap;"><?= esc($cpmk['kode_cpmk']) ?></td>
                                        <?php for ($i = 1; $i <= 8; $i++): ?>
                                            <td style="white-space:normal;">
                                                <?= isset($cpmk['semester'][$i]) ? implode(', ', array_map('esc', $cpmk['semester'][$i])) : '' ?>
                                            </td>
                                        <?php endfor; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
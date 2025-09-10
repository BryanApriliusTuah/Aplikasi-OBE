<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= session()->getFlashdata('success') ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>
<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Pemetaan CPL ke Profil Lulusan</h2>
        <?php if (session('role') === 'admin'): ?>
        <div class="d-flex gap-2">
            <div class="btn-group">
                <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download"></i> Download
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="<?= base_url('admin/cpl-pl/exportPdf') ?>">
                            <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= base_url('admin/cpl-pl/exportExcel') ?>">
                            <i class="bi bi-file-earmark-excel text-success"></i> Excel
                        </a>
                    </li>
                </ul>
            </div>
            <a href="<?= base_url('admin/cpl-pl/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Pemetaan
            </a>
        </div>
        <?php endif; ?>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 15%;">Kode CPL</th>
                            <?php foreach ($plList as $pl): ?>
                                <th class="text-center"><?= esc($pl['kode_pl']) ?></th>
                            <?php endforeach ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($cplList)): ?>
                            <tr>
                                <td colspan="<?= count($plList) + 1 ?>" class="text-center text-muted py-4">Belum ada data CPL.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($cplList as $cpl): ?>
                                <tr>
                                    <td class="fw-bold text-center"><?= esc($cpl['kode_cpl']) ?></td>
                                    <?php foreach ($plList as $pl): ?>
                                        <td class="text-center">
                                            <?php if (isset($matriks[$cpl['id']][$pl['id']])): ?>
                                                <?php if (session('role') === 'admin'): ?>
                                                    <button type="button" class="btn btn-outline-success btn-sm p-0" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $matriks[$cpl['id']][$pl['id']] ?>" title="Klik untuk hapus pemetaan">
                                                        <i class="bi bi-check2-square" style="font-size: 20px;"></i>
                                                    </button>
                                                    <div class="modal fade" id="deleteModal<?= $matriks[$cpl['id']][$pl['id']] ?>" tabindex="-1">
                                                        <div class="modal-dialog modal-dialog-centered">
                                                            <div class="modal-content">
                                                                <form method="post" action="<?= base_url('admin/cpl-pl/delete/' . $matriks[$cpl['id']][$pl['id']]) ?>">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title">Konfirmasi Hapus</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        Yakin ingin menghapus pemetaan CPL <strong><?= esc($cpl['kode_cpl']) ?></strong> ke PL <strong><?= esc($pl['kode_pl']) ?></strong>?
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                        <button type="submit" class="btn btn-danger">Hapus</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php else: ?>
                                                    <span class="text-success" title="Sudah dipetakan">
                                                        <i class="bi bi-check-square" style="font-size: 20px;"></i>
                                                    </span>
                                                <?php endif ?>
                                            <?php endif ?>
                                        </td>
                                    <?php endforeach ?>
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
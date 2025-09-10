<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<h2 class="fw-bold mb-3">Pemetaan CPL ke Mata Kuliah</h2>

<?php if(session()->getFlashdata('success')): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?= session()->getFlashdata('success'); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif ?>
<?php if(session()->getFlashdata('error')): ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= session()->getFlashdata('error'); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif ?>

<div class="d-flex justify-content-end align-items-center mb-3">
    <?php if (session('role') === 'admin'): ?>
    <div class="d-flex gap-2">
        <div class="btn-group">
            <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download"></i> Download
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="<?= base_url('admin/cpl-mk/exportPdf') ?>">
                        <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= base_url('admin/cpl-mk/exportExcel') ?>">
                        <i class="bi bi-file-earmark-excel text-success"></i> Excel
                    </a>
                </li>
            </ul>
        </div>
        <a href="<?= base_url('admin/cpl-mk/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Pemetaan
        </a>
    </div>
    <?php endif; ?>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr class="align-middle text-center">
                        <th style="width: 50px;">No</th>
                        <th style="width: 130px;">Kode MK</th>
                        <th>Nama Mata Kuliah</th>
                        <?php foreach($cpl as $c): ?>
                            <th style="width: 80px;"><?= esc($c['kode_cpl']) ?></th>
                        <?php endforeach ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($mataKuliah)): ?>
                        <tr>
                            <td colspan="<?= 3 + count($cpl) ?>" class="text-center text-muted py-4">Belum ada data mata kuliah.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach($mataKuliah as $mk): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td class="text-center"><?= esc($mk['kode_mk']) ?></td>
                            <td style="white-space: normal;"><?= esc($mk['nama_mk']) ?></td>
                            <?php foreach($cpl as $c): ?>
                                <td class="text-center">
                                    <?php if (isset($pemetaan[$mk['id']][$c['id']])): ?>
                                        <?php if (session('role') === 'admin'): ?>
                                            <form action="<?= base_url('admin/cpl-mk/delete/'.$mk['id'].'/'.$c['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus pemetaan MK <?= esc($mk['kode_mk']) ?> ke CPL <?= esc($c['kode_cpl']) ?>?')">
                                                <button type="submit" class="btn btn-outline-success btn-sm p-0" style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;" title="Hapus Pemetaan">
                                                    <i class="bi bi-check2-square" style="font-size: 20px;"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <i class="bi bi-check2-square text-success" style="font-size: 1.5em; line-height: 1;"></i>
                                        <?php endif; ?>
                                    <?php endif; ?>
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
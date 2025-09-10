<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<h2 class="fw-bold mb-3">Pemetaan Bahan Kajian ke Mata Kuliah</h2>

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
    <div class="d-flex justify-content-end gap-2">
        <div class="btn-group">
            <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download"></i> Download
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="<?= base_url('admin/bkmk/exportPdf') ?>">
                        <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= base_url('admin/bkmk/exportExcel') ?>">
                        <i class="bi bi-file-earmark-excel text-success"></i> Excel
                    </a>
                </li>
            </ul>
        </div>
        <?php if (session('role') === 'admin'): ?>
        <a href="<?= base_url('admin/bkmk/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Pemetaan
        </a>
        <?php endif; ?>
        <a href="<?= base_url('admin/bkmk/matriks') ?>" class="btn btn-outline-secondary">
            <i class="bi bi-table"></i> Matriks
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-center">
                        <th style="width:60px">No</th>
                        <th class="text-nowrap" style="width: 15%;">Kode BK</th>
                        <th class="text-start" style="width: 25%;">Nama Bahan Kajian</th>
                        <th class="text-start">Daftar Mata Kuliah</th>
                        <th style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($data)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Belum ada data pemetaan.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($data as $g) : ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center text-nowrap"><?= esc($g['kode_bk']) ?></td>
                                <td style="white-space: normal;"><?= esc($g['nama_bk']) ?></td>
                                <td style="white-space: normal;">
                                    <?= esc(implode(', ', $g['mk_list'])) ?>
                                </td>
                                <td class="text-center">
                                    <?php if (session('role') === 'admin'): ?>
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <a href="<?= base_url('admin/bkmk/edit/'.$g['bk_id']) ?>" class="btn btn-outline-primary btn-sm" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form action="<?= base_url('admin/bkmk/delete/'.$g['bk_id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus semua pemetaan untuk <?= esc($g['kode_bk']) ?>?')">
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php endif ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
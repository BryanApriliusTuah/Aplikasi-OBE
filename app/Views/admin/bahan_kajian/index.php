<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold mb-0">Daftar Bahan Kajian</h2>
    <?php if (session('role') === 'admin'): ?>
    <div class="d-flex gap-2">
        <div class="btn-group">
            <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download"></i> Download
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="<?= base_url('admin/bahan-kajian/exportPdf') ?>">
                        <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= base_url('admin/bahan-kajian/exportExcel') ?>">
                        <i class="bi bi-file-earmark-excel text-success"></i> Excel
                    </a>
                </li>
            </ul>
        </div>
        <a href="<?= base_url('admin/bahan-kajian/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Bahan Kajian
        </a>
    </div>
    <?php endif; ?>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover m-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width:60px;">No</th>
                        <th style="width:150px;">Kode BK</th>
                        <th>Nama Bahan Kajian</th>
                        <th class="text-center" style="width:120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($bahan_kajian)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada data Bahan Kajian.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($bahan_kajian as $b): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td><?= esc($b['kode_bk']) ?></td>
                                <td style="white-space: normal;"><?= esc($b['nama_bk']) ?></td>
                                <td class="text-center">
                                    <?php if (session('role') === 'admin'): ?>
                                        <a href="<?= base_url('admin/bahan-kajian/edit/' . $b['id']) ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="<?= base_url('admin/bahan-kajian/delete/' . $b['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin hapus data ini?');">
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
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
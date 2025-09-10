<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Daftar CPL</h2>
        <?php if (session('role') === 'admin'): ?>
        <div class="d-flex gap-2">
            <div class="btn-group">
                <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download"></i> Download
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="<?= base_url('admin/cpl/exportPdf') ?>">
                            <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= base_url('admin/cpl/exportExcel') ?>">
                            <i class="bi bi-file-earmark-excel text-success"></i> Excel
                        </a>
                    </li>
                </ul>
            </div>
            <a href="<?= base_url('admin/cpl/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah CPL
            </a>
        </div>
        <?php endif; ?>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th class="text-center" style="width: 120px;">Kode CPL</th>
                            <th class="text-start">Deskripsi</th>
                            <th class="text-center" style="width: 150px;">Jenis CPL</th>
                            <th class="text-center" style="width:110px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($cpl)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-secondary py-5">Data CPL belum ada.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($cpl as $row): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center"><?= esc($row['kode_cpl']) ?></td>
                                <td class="text-start" style="white-space: normal;"><?= esc($row['deskripsi']) ?></td>
                                <td class="text-center"><?= esc($row['jenis_cpl']) ?></td>
                                <td class="text-center">
                                    <?php if (session('role') === 'admin'): ?>
                                        <a href="<?= base_url('admin/cpl/edit/' . $row['id']) ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#modalDelete<?= $row['id'] ?>" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size:14px;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>

                            <?php if (session('role') === 'admin'): ?>
                            <div class="modal fade" id="modalDelete<?= $row['id'] ?>" tabindex="-1" aria-labelledby="modalDeleteLabel<?= $row['id'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="modalDeleteLabel<?= $row['id'] ?>">Konfirmasi Hapus</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            Yakin ingin menghapus data ini?<br>
                                            <strong><?= esc($row['kode_cpl']) ?> - <?= esc($row['deskripsi']) ?></strong>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <a href="<?= base_url('admin/cpl/delete/' . $row['id']) ?>" class="btn btn-danger">Hapus</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php endforeach ?>
                        <?php endif ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
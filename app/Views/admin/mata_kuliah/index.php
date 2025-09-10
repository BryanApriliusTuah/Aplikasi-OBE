<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0 fw-bold">Daftar Mata Kuliah</h2>
    <?php if (session('role') === 'admin'): ?>
    <div class="d-flex gap-2">
        <div class="btn-group">
            <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download"></i> Download
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="<?= base_url('admin/mata-kuliah/exportPdf') ?>">
                        <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= base_url('admin/mata-kuliah/exportExcel') ?>">
                        <i class="bi bi-file-earmark-excel text-success"></i> Excel
                    </a>
                </li>
            </ul>
        </div>
        <a href="<?= base_url('admin/mata-kuliah/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Mata Kuliah
        </a>
    </div>
    <?php endif; ?>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>
<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center text-nowrap" rowspan="2">Kode MK</th>
                        <th class="text-center" rowspan="2">Nama Mata Kuliah</th>
                        <th class="text-center text-nowrap" rowspan="2">Tipe</th>
                        <th class="text-center" colspan="8">Semester</th>
                        <th class="text-center" rowspan="2">Deskripsi Singkat</th>
                        <th class="text-center" rowspan="2" style="width:100px;">Aksi</th>
                    </tr>
                    <tr>
                        <?php for($i=1; $i<=8; $i++): ?>
                            <th class="text-center" style="width:40px;"><?= $i ?></th>
                        <?php endfor ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($matakuliah)): ?>
                        <tr>
                            <td colspan="13" class="text-center text-muted py-4">Belum ada data mata kuliah.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach($matakuliah as $mk): ?>
                            <tr>
                                <td class="text-center text-nowrap"><?= esc($mk['kode_mk']) ?></td>
                                <td style="white-space: normal;"><?= esc($mk['nama_mk']) ?></td>
                                <td class="text-center text-nowrap"><?= esc($mk['tipe']) ?></td>
                                <?php for($i=1; $i<=8; $i++): ?>
                                    <td class="text-center"><?= ($mk['semester'] == $i) ? esc($mk['sks']) : '' ?></td>
                                <?php endfor ?>
                                <td style="white-space: normal;"><?= esc($mk['deskripsi_singkat']) ?></td>
                                <td class="text-center">
                                    <?php if (session('role') === 'admin'): ?>
                                    <div class="d-flex gap-1 justify-content-center">
                                        <a href="<?= base_url('admin/mata-kuliah/edit/'.$mk['id']) ?>"
                                            class="btn btn-outline-primary btn-sm"
                                            title="Edit">
                                            <i class="bi bi-pencil-square"></i>
                                        </a>
                                        <form action="<?= base_url('admin/mata-kuliah/delete/'.$mk['id']) ?>" method="post" onsubmit="return confirm('Yakin hapus mata kuliah ini?');">
                                            <button class="btn btn-outline-danger btn-sm" type="submit" title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size:14px;">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
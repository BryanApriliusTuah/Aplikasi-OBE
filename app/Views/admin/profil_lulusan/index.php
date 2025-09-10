<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Daftar Profil Lulusan</h2>
        <?php if (session('role') === 'admin'): ?>
        <div class="d-flex gap-2">
            <div class="btn-group">
                <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download"></i> Download
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="<?= base_url('admin/profil-lulusan/exportPdf') ?>">
                            <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= base_url('admin/profil-lulusan/exportExcel') ?>">
                            <i class="bi bi-file-earmark-excel text-success"></i> Excel
                        </a>
                    </li>
                </ul>
            </div>
            <a href="<?= base_url('admin/profil-lulusan/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Profil
            </a>
        </div>
        <?php endif; ?>
    </div>

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

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th class="text-center" style="width: 120px;">Kode</th>
                            <th class="text-start">Deskripsi</th>
                            <th class="text-center" style="width: 100px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($profil_lulusan)): ?>
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-5">Data profil lulusan belum ada.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($profil_lulusan as $pl): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center" style="white-space:nowrap;"><?= esc($pl['kode_pl']) ?></td>
                                <td class="text-start" style="white-space: normal;"><?= esc($pl['deskripsi']) ?></td>
                                <td class="text-center">
                                    <?php if (session('role') === 'admin'): ?>
                                        <div class="d-flex justify-content-center align-items-center gap-1">
                                            <a href="<?= base_url('admin/profil-lulusan/edit/' . $pl['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger btn-hapus"
                                                data-id="<?= $pl['id'] ?>"
                                                data-kode="<?= esc($pl['kode_pl']) ?>"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalHapus"
                                                title="Hapus">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size:14px;">-</span>
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
</div>

<?php if (session('role') === 'admin'): ?>
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="formHapus" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalHapusLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Yakin ingin menghapus data <strong id="modalKodePL"></strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->section('js') ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalHapus = document.getElementById('modalHapus');
        var formHapus = document.getElementById('formHapus');
        var kodePL = document.getElementById('modalKodePL');

        modalHapus.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var kode = button.getAttribute('data-kode');
            formHapus.action = "<?= base_url('admin/profil-lulusan/delete/') ?>" + id;
            kodePL.textContent = kode;
        });
    });
</script>
<?= $this->endSection() ?>
<?php endif; ?>

<?= $this->endSection() ?>
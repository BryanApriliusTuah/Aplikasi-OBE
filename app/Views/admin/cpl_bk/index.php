<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Pemetaan CPL ke Bahan Kajian</h2>
        <?php if (session('role') === 'admin'): ?>
        <div class="d-flex gap-2">
            <div class="btn-group">
                <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download"></i> Download
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="<?= base_url('admin/cpl-bk/exportPdf') ?>">
                            <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= base_url('admin/cpl-bk/exportExcel') ?>">
                            <i class="bi bi-file-earmark-excel text-success"></i> Excel
                        </a>
                    </li>
                </ul>
            </div>
            <a href="<?= base_url('admin/cpl-bk/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Pemetaan
            </a>
        </div>
        <?php endif; ?>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">Kode BK</th>
                            <?php foreach ($cplList as $cpl): ?>
                                <th class="text-center"><?= esc($cpl['kode_cpl']) ?></th>
                            <?php endforeach ?>
                            <th class="text-center">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(empty($matrix)): ?>
                            <tr>
                                <td colspan="<?= count($cplList) + 2 ?>" class="text-center text-secondary py-5">Data pemetaan belum ada.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($matrix as $bk): ?>
                                <tr>
                                    <td class="text-center fw-bold" style="white-space:nowrap;"><?= esc($bk['kode_bk']) ?></td>
                                    <?php foreach ($cplList as $cpl): ?>
                                        <td class="text-center">
                                            <?php if (!empty($bk['cpl'][$cpl['id']])): ?>
                                                <?php if (session('role') === 'admin'): ?>
                                                <button type="button" class="btn btn-sm btn-outline-success delete-btn p-0" style="width:30px; height:30px;" data-action="<?= base_url('admin/cpl-bk/delete/' . $bk['cpl'][$cpl['id']]) ?>" title="Hapus Pemetaan">
                                                    <i class="bi bi-check2-square fs-5"></i>
                                                </button>
                                                <?php else: ?>
                                                    <i class="bi bi-check2-square text-success fs-5"></i>
                                                <?php endif; ?>
                                            <?php endif ?>
                                        </td>
                                    <?php endforeach ?>
                                    <td class="text-center fw-bold"><?= $bk['jumlah'] ?></td>
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
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                Yakin ingin menghapus pemetaan ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="post" action="">
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    const deleteForm = document.getElementById('deleteForm');

    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const actionUrl = this.getAttribute('data-action');
            deleteForm.setAttribute('action', actionUrl);
            confirmDeleteModal.show();
        });
    });
});
</script>
<?= $this->endSection() ?>
<?php endif; ?>

<?= $this->endSection() ?>
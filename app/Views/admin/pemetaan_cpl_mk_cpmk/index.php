<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<h2 class="fw-bold mb-3 text-center"><?= esc($title ?? 'Pemetaan CPL - CPMK - MK') ?></h2>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
        <?php $e = session()->getFlashdata('error'); echo is_array($e)? implode('<br>', array_map('esc',$e)) : esc($e); ?>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="<?= base_url('admin/pemetaan-cpl-mk-cpmk/create') ?>" class="btn btn-primary">
        <i class="bi bi-plus-lg"></i> Tambah Pemetaan
    </a>
    <div class="btn-group">
        <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-download"></i> Download
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li>
                <a class="dropdown-item" href="<?= base_url('admin/pemetaan-cpl-mk-cpmk/exportExcel') ?>">
                    <i class="bi bi-file-earmark-excel"></i> Excel
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="<?= base_url('admin/pemetaan-cpl-mk-cpmk/exportPdf') ?>">
                    <i class="bi bi-file-earmark-pdf"></i> PDF
                </a>
            </li>
        </ul>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:110px">Kode CPL</th>
                        <th style="width:130px">Kode CPMK</th>
                        <th style="max-width:420px">Mata Kuliah</th>
                        <th style="width:120px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($rows)): ?>
                    <?php 
                    $lastCpl = null;
                    $cplRowspan = array_count_values(array_column($rows, 'kode_cpl'));
                    ?>
                    <?php foreach ($rows as $r): ?>
                        <tr>
                            <?php if ($lastCpl != $r['kode_cpl']): ?>
                                <td rowspan="<?= $cplRowspan[$r['kode_cpl']] ?>" class="text-center" style="vertical-align: middle;">
                                    <?= esc($r['kode_cpl']) ?>
                                </td>
                            <?php endif; ?>
                            <td><?= esc($r['kode_cpmk']) ?></td>
                            <td class="mk-cell"><?= esc($r['mk_list']) ?></td>
                            <td class="text-center">
                                <a href="<?= base_url('admin/pemetaan-cpl-mk-cpmk/edit/' . (int)$r['cpl_id'] . '/' . (int)$r['cpmk_id']) ?>"
                                    class="btn btn-sm btn-outline-primary" title="Edit">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal" data-url="<?= base_url('admin/pemetaan-cpl-mk-cpmk/deleteGroup/' . (int)$r['cpl_id'] . '/' . (int)$r['cpmk_id']) ?>" title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php $lastCpl = $r['kode_cpl']; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center text-muted py-4">Belum ada pemetaan.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Penghapusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Hapus semua pemetaan MK untuk kombinasi CPL & CPMK ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" action="" method="post" class="d-inline">
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .mk-cell { white-space: normal; word-break: break-word; }
</style>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const url = button.getAttribute('data-url');
            const form = deleteModal.querySelector('#deleteForm');
            form.action = url;
        });
    }
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
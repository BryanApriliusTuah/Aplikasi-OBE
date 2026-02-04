<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<!-- Include Modern Table Styles -->
<link rel="stylesheet" href="<?= base_url('css/modern-table.css') ?>">

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

    <div class="shadow-sm border-0">
        <div class="p-0">
            <?php if (empty($cplList)): ?>
                <div class="text-center text-muted py-5">
                    <i class="bi bi-grid-3x3-gap fs-1"></i>
                    <p class="mt-3 fw-semibold">Belum ada data CPL</p>
                    <p class="small">Silakan tambah data CPL terlebih dahulu</p>
                </div>
            <?php else: ?>
                <div class="modern-table-wrapper">
                    <div class="scroll-indicator"></div>
                    <table class="modern-table" id="cplPlTable">
                        <thead>
                            <tr>
                                <th class="text-center sticky-col" style="min-width: 120px;">Kode CPL</th>
                                <?php foreach ($plList as $pl): ?>
                                    <th class="text-center" style="min-width: 100px;"><?= esc($pl['kode_pl']) ?></th>
                                <?php endforeach ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cplList as $cpl): ?>
                                <tr>
                                    <td class="fw-bold text-center sticky-col"><?= esc($cpl['kode_cpl']) ?></td>
                                    <?php foreach ($plList as $pl): ?>
                                        <td class="text-center">
                                            <?php if (isset($matriks[$cpl['id']][$pl['id']])): ?>
                                                <?php if (session('role') === 'admin'): ?>
                                                    <button type="button"
                                                        class="btn btn-outline-success btn-sm p-0 btn-hapus-pemetaan"
                                                        style="width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;border-radius:6px;"
                                                        data-id="<?= $matriks[$cpl['id']][$pl['id']] ?>"
                                                        data-cpl="<?= esc($cpl['kode_cpl']) ?>"
                                                        data-pl="<?= esc($pl['kode_pl']) ?>"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#modalHapus"
                                                        title="Klik untuk hapus pemetaan">
                                                        <i class="bi bi-check2-square" style="font-size: 20px;"></i>
                                                    </button>
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
                        </tbody>
                    </table>
                </div>

                <div class="card-footer bg-light border-0 py-3">
                    <div class="d-flex align-items-center gap-3 justify-content-center">
                        <small class="text-muted">
                            <i class="bi bi-info-circle me-1"></i>
                            Total: <?= count($cplList) ?> CPL, <?= count($plList) ?> Profil Lulusan
                        </small>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (session('role') === 'admin'): ?>
<div class="modal fade" id="modalHapus" tabindex="-1" aria-labelledby="modalHapusLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="formHapus" method="post" action="">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalHapusLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    Yakin ingin menghapus pemetaan CPL <strong id="modalCplCode"></strong> ke PL <strong id="modalPlCode"></strong>?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Modal delete handler
    <?php if (session('role') === 'admin'): ?>
    var modalHapus = document.getElementById('modalHapus');
    modalHapus.addEventListener('show.bs.modal', function (event) {
        var button = event.relatedTarget;
        var id = button.getAttribute('data-id');
        var cpl = button.getAttribute('data-cpl');
        var pl = button.getAttribute('data-pl');
        document.getElementById('formHapus').action = "<?= base_url('admin/cpl-pl/delete/') ?>" + id;
        document.getElementById('modalCplCode').textContent = cpl;
        document.getElementById('modalPlCode').textContent = pl;
    });
    <?php endif; ?>

    // Scroll indicator
    const tableWrapper = document.querySelector('.modern-table-wrapper');
    if (tableWrapper) {
        function checkScroll() {
            const hasHorizontalScroll = tableWrapper.scrollWidth > tableWrapper.clientWidth;
            const isScrolledToEnd = tableWrapper.scrollLeft >= (tableWrapper.scrollWidth - tableWrapper.clientWidth - 10);

            if (hasHorizontalScroll && !isScrolledToEnd) {
                tableWrapper.classList.add('has-scroll');
            } else {
                tableWrapper.classList.remove('has-scroll');
            }
        }

        checkScroll();
        window.addEventListener('resize', checkScroll);
        tableWrapper.addEventListener('scroll', checkScroll);
    }
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>

<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<h2 class="fw-bold mb-3 text-center">Pemetaan MK – CPMK – SubCPMK</h2>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="<?= base_url('admin/pemetaan-mk-cpmk-sub/create') ?>" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah
        </a>
    </div>

    <div class="btn-group">
        <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-download"></i> Download
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><a class="dropdown-item" href="<?= base_url('admin/pemetaan-mk-cpmk-sub/export-pdf') ?>"><i class="bi bi-file-earmark-pdf text-danger"></i> PDF</a></li>
            <li><a class="dropdown-item" href="<?= base_url('admin/pemetaan-mk-cpmk-sub/export-excel') ?>"><i class="bi bi-file-earmark-excel text-success"></i> Excel</a></li>
        </ul>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light text-center">
            <tr>
                <th style="width: 50px;">No</th>
                <th>Kode CPL</th>
                <th>Kode CPMK</th>
                <th>Nama Mata Kuliah</th>
                <th>Kode SubCPMK</th>
                <th>Deskripsi</th>
                <th style="width: 90px;">Aksi</th>
            </tr>
        </thead>
        <tbody class="align-top">
            <?php if (empty($rows)): ?>
                <tr>
                    <td colspan="7" class="text-center">Belum ada data pemetaan.</td>
                </tr>
            <?php else: ?>
                <?php 
                $no = 1;
                $lastCpl = null; $lastCpmk = null; $lastSubCpmk = null;
                $rowspanData = [];
                
                foreach ($rows as $row) {
                    $rowspanData[$row['kode_cpl']]['count'] = ($rowspanData[$row['kode_cpl']]['count'] ?? 0) + 1;
                    $rowspanData[$row['kode_cpl']][$row['kode_cpmk']]['count'] = ($rowspanData[$row['kode_cpl']][$row['kode_cpmk']]['count'] ?? 0) + 1;
                    $rowspanData[$row['kode_cpl']][$row['kode_cpmk']][$row['id']]['count'] = ($rowspanData[$row['kode_cpl']][$row['kode_cpmk']][$row['id']]['count'] ?? 0) + 1;
                }
                ?>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <?php if ($lastSubCpmk != $row['id']): ?>
                            <td rowspan="<?= $rowspanData[$row['kode_cpl']][$row['kode_cpmk']][$row['id']]['count'] ?>" class="text-center" style="vertical-align: middle;"><?= $no++ ?></td>
                        <?php endif; ?>

                        <?php if ($lastCpl != $row['kode_cpl']): ?>
                            <td rowspan="<?= $rowspanData[$row['kode_cpl']]['count'] ?>" class="text-center" style="vertical-align: middle;"><?= esc($row['kode_cpl']) ?></td>
                        <?php endif; ?>
                        
                        <?php if ($lastCpmk != $row['kode_cpmk']): ?>
                            <td rowspan="<?= $rowspanData[$row['kode_cpl']][$row['kode_cpmk']]['count'] ?>" class="text-center" style="vertical-align: middle;"><?= esc($row['kode_cpmk']) ?></td>
                        <?php endif; ?>

                        <td style="white-space: normal;"><?= esc($row['mata_kuliah']) ?></td>

                        <?php if ($lastSubCpmk != $row['id']): ?>
                            <td rowspan="<?= $rowspanData[$row['kode_cpl']][$row['kode_cpmk']][$row['id']]['count'] ?>" style="white-space: normal; vertical-align: middle;"><?= esc($row['kode_sub_cpmk']) ?></td>
                            <td rowspan="<?= $rowspanData[$row['kode_cpl']][$row['kode_cpmk']][$row['id']]['count'] ?>" style="white-space: normal; vertical-align: middle;"><?= esc($row['deskripsi']) ?></td>
                            <td rowspan="<?= $rowspanData[$row['kode_cpl']][$row['kode_cpmk']][$row['id']]['count'] ?>" class="text-center" style="vertical-align: middle;">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="<?= base_url('admin/pemetaan-mk-cpmk-sub/edit/' . $row['id']) ?>" class="btn btn-sm border border-primary text-primary px-2">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm border border-danger text-danger px-2" data-bs-toggle="modal" data-bs-target="#deleteModal" data-id="<?= $row['id'] ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php 
                    $lastCpl = $row['kode_cpl'];
                    $lastCpmk = $row['kode_cpmk'];
                    $lastSubCpmk = $row['id'];
                    ?>
                <?php endforeach ?>
            <?php endif ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Penghapusan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Anda yakin akan menghapus SubCPMK ini?</p>
                <p class="text-danger fw-bold">RPS mingguan yang memuat SubCPMK ini juga akan terhapus dan tidak bisa dipulihkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" action="" method="post">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const form = deleteModal.querySelector('#deleteForm');
            const baseUrl = '<?= rtrim(base_url(), '/') ?>';
            form.action = `${baseUrl}/admin/pemetaan-mk-cpmk-sub/delete/${id}`;
        });
    }
});
</script>
<?= $this->endSection() ?>
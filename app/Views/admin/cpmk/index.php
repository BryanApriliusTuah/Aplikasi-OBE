<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-0">
    <h2 class="fw-bold mb-0">Rumusan CPMK</h2>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
        <?= is_array(session()->getFlashdata('error')) ? implode('<br>', array_map('esc', session()->getFlashdata('error'))) : esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>
    
    <div class="d-flex justify-content-end align-items-center my-4">
        <?php if (session('role') === 'admin'): ?>
        <div class="d-flex gap-2">
            <div class="btn-group">
                <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-download"></i> Download
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="<?= base_url('admin/cpmk/exportPdf') ?>">
                            <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= base_url('admin/cpmk/exportExcel') ?>">
                            <i class="bi bi-file-earmark-excel text-success"></i> Excel
                        </a>
                    </li>
                </ul>
            </div>
            <a href="<?= base_url('admin/cpmk/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah CPMK
            </a>
        </div>
        <?php endif; ?>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">No</th>
                            <th class="text-center" style="width: 150px;">Kode CPMK</th>
                            <th class="text-start">Deskripsi CPMK</th>
                            <th class="text-center" style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($cpmk)): ?>
                            <?php $no = 1; foreach ($cpmk as $row): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td class="text-center"><?= esc($row['kode_cpmk']) ?></td>
                                <td class="text-start" style="white-space: normal;"><?= nl2br(esc($row['deskripsi'])) ?></td>
                                <td class="text-center">
                                    <a href="<?= base_url('admin/cpmk/edit/' . $row['id']) ?>" class="btn btn-sm btn-outline-primary me-1" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn" data-kode="<?= esc($row['kode_cpmk']) ?>" data-url="<?= base_url('admin/cpmk/delete/' . $row['id']) ?>" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center text-secondary py-5">Data CPMK belum ada.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                Yakin ingin menghapus CPMK <strong id="modalCpmkCode"></strong>?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" action="" method="post" class="d-inline">
                     <?= csrf_field() ?>
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
    const modalCpmkCode = document.getElementById('modalCpmkCode');
    const deleteForm = document.getElementById('deleteForm');

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const url = this.getAttribute('data-url');
            const kode = this.getAttribute('data-kode');
            
            deleteForm.action = url;
            modalCpmkCode.textContent = kode;
            
            confirmDeleteModal.show();
        });
    });
});
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
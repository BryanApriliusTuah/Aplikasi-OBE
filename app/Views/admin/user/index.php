<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Manajemen User</h2>
            <a href="<?= base_url('admin/user/create') ?>" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah User
            </a>
        </div>
        
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Nama Lengkap</th>
                        <th>Role</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1; 
                    foreach ($users as $user): 
                        // Determine badge color based on role
                        $badge_class = 'bg-secondary'; // Default color
                        if ($user['role'] == 'admin') {
                            $badge_class = 'bg-primary';
                        } elseif ($user['role'] == 'dosen') {
                            $badge_class = 'bg-info';
                        } elseif ($user['role'] == 'mahasiswa') {
                            $badge_class = 'bg-success';
                        }
                    ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= esc($user['username']) ?></td>
                        <td><?= esc($user['nama_lengkap'] ?? '-') ?></td>
                        <td>
                            <span class="badge <?= $badge_class ?>">
                                <?= ucfirst(esc($user['role'])) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="<?= base_url('admin/user/edit/' . $user['id']) ?>" class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger btn-sm" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#deleteModal" 
                                    data-delete-url="<?= base_url('admin/user/delete/' . $user['id']) ?>"
                                    data-bs-toggle="tooltip" title="Hapus">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Apakah Anda yakin ingin menghapus user ini? Proses ini tidak dapat dibatalkan.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" action="" method="post" class="d-inline">
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
    const deleteModal = document.getElementById('deleteModal');
    if (deleteModal) {
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const deleteUrl = button.getAttribute('data-delete-url');
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.setAttribute('action', deleteUrl);
        });
    }
</script>
<?= $this->endSection() ?>
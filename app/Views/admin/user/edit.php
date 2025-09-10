<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Edit User: <?= esc($user['username']) ?></h2>
            <a href="<?= base_url('admin/user') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('admin/user/update/' . $user['id']) ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" value="<?= esc($user['username']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password Baru</label>
                <input type="password" id="password" name="password" class="form-control">
                <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="admin" <?= ($user['role'] == 'admin') ? 'selected' : '' ?>>Admin</option>
                    <option value="dosen" <?= ($user['role'] == 'dosen') ? 'selected' : '' ?>>Dosen</option>
                </select>
            </div>

            <?php if ($user['role'] === 'dosen'): ?>
                <div class="mb-3">
                    <label class="form-label">Terhubung dengan Dosen</label>
                    <input type="text" class="form-control" value="<?= esc($user['nama_lengkap'] ?? 'N/A') ?>" disabled readonly>
                    <small class="form-text text-muted">Hubungan dengan data master dosen tidak dapat diubah.</small>
                </div>
            <?php endif; ?>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Update User</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
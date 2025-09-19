<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Tambah User Baru</h2>
            <a href="<?= base_url('admin/user') ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('admin/user/store') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" id="username" name="username" class="form-control" value="<?= old('username') ?>" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select name="role" id="role" class="form-select" required>
                    <option value="">-- Pilih Role --</option>
                    <option value="admin">Admin</option>
                    <option value="dosen">Dosen</option>
                    <option value="mahasiswa">Mahasiswa</option>
                </select>
            </div>

            <div class="mb-3" id="dosen-select-field" style="display: none;">
                <label for="dosen_id" class="form-label">Hubungkan dengan Data Dosen</label>
                <select name="dosen_id" id="dosen_id" class="form-select">
                    <option value="">-- Pilih Dosen --</option>
                    <?php foreach ($dosen_list as $dosen): ?>
                        <option value="<?= $dosen['id'] ?>"><?= esc($dosen['nama_lengkap']) ?> (NIP: <?= esc($dosen['nip']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="mb-3" id="mahasiswa-select-field" style="display: none;">
                <label for="mahasiswa_id" class="form-label">Hubungkan dengan Data Mahasiswa</label>
                <select name="mahasiswa_id" id="mahasiswa_id" class="form-select">
                    <option value="">-- Pilih Mahasiswa --</option>
                    <?php foreach ($mahasiswa_list as $mahasiswa): ?>
                        <option value="<?= $mahasiswa['id'] ?>"><?= esc($mahasiswa['nama_lengkap']) ?> (NIM: <?= esc($mahasiswa['nim']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Simpan User</button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    // Script to show/hide relevant dropdown based on role selection
    document.getElementById('role').addEventListener('change', function() {
        var role = this.value;
        var dosenSelectField = document.getElementById('dosen-select-field');
        var dosenSelect = document.getElementById('dosen_id');
        var mahasiswaSelectField = document.getElementById('mahasiswa-select-field');
        var mahasiswaSelect = document.getElementById('mahasiswa_id');

        // Hide all fields first
        dosenSelectField.style.display = 'none';
        dosenSelect.removeAttribute('required');
        mahasiswaSelectField.style.display = 'none';
        mahasiswaSelect.removeAttribute('required');

        if (role === 'dosen') {
            dosenSelectField.style.display = 'block';
            dosenSelect.setAttribute('required', 'required');
        } else if (role === 'mahasiswa') {
            mahasiswaSelectField.style.display = 'block';
            mahasiswaSelect.setAttribute('required', 'required');
        }
    });
</script>
<?= $this->endSection() ?>
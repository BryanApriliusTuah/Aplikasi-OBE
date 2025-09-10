<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-0">
    <h2 class="fw-bold mb-3">Edit Profil Prodi</h2>

    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif ?>
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif ?>

    <form action="<?= base_url('admin/profil-prodi/update/' . $profil['id']) ?>" method="post" enctype="multipart/form-data">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Nama Universitas</label>
                    <input type="text" name="nama_universitas" class="form-control" value="<?= esc($profil['nama_universitas']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Fakultas</label>
                    <input type="text" name="nama_fakultas" class="form-control" value="<?= esc($profil['nama_fakultas']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Prodi</label>
                    <input type="text" name="nama_prodi" class="form-control" value="<?= esc($profil['nama_prodi']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Ketua Prodi</label>
                    <input type="text" name="nama_ketua_prodi" class="form-control" value="<?= esc($profil['nama_ketua_prodi']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">NIP Ketua Prodi</label>
                    <input type="text" name="nip_ketua_prodi" class="form-control" value="<?= esc($profil['nip_ketua_prodi']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Dekan</label>
                    <input type="text" name="nama_dekan" class="form-control" value="<?= esc($profil['nama_dekan']) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">NIP Dekan</label>
                    <input type="text" name="nip_dekan" class="form-control" value="<?= esc($profil['nip_dekan']) ?>" required>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success px-4">Simpan</button>
            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>
<h1 class="mb-1 fw-bold">Selamat Datang di Dashboard Sistem</h1>
<p class="mb-4 text-muted">Ini adalah halaman informasi umum Sistem.</p>

<div class="row mb-4 g-4">
    <div class="col-md-4">
        <div class="card shadow-sm p-3 text-center border-0 h-100">
            <div class="mb-2">
                <i class="bi bi-people-fill fs-1 text-primary"></i>
            </div>
            <div class="fw-bold fs-2"><?= $total_dosen ?></div>
            <div class="text-muted">Akun Dosen yang Terdaftar</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm p-3 text-center border-0 h-100">
            <div class="mb-2">
                <i class="bi bi-journal-bookmark-fill fs-1 text-secondary"></i>
            </div>
            <div class="fw-bold fs-2"><?= $total_mk ?></div>
            <div class="text-muted">Mata Kuliah</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm p-3 text-center border-0 h-100">
            <div class="mb-2">
                <i class="bi bi-book-fill fs-1 text-info"></i>
            </div>
            <div class="fw-bold fs-2"><?= $total_rps ?></div>
            <div class="text-muted">RPS</div>
        </div>
    </div>
</div>

<div class="card shadow-sm p-4 mb-4">
    <h5 class="fw-bold mb-3">
        <i class="bi bi-building"></i> Profil Prodi
    </h5>
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif ?>
    <?php if (!$profil_prodi): ?>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-4">
                <tr>
                    <th style="width:220px;">Nama Universitas</th>
                    <td><?= esc($profil_prodi['nama_universitas']) ?></td>
                </tr>
                <tr>
                    <th>Nama Fakultas</th>
                    <td><?= esc($profil_prodi['nama_fakultas']) ?></td>
                </tr>
                <tr>
                    <th>Nama Prodi</th>
                    <td><?= esc($profil_prodi['nama_prodi']) ?></td>
                </tr>
                <tr>
                    <th>Nama Ketua Prodi</th>
                    <td><?= esc($profil_prodi['nama_ketua_prodi']) ?> <span class="text-muted" style="font-size:90%;">(NIP: <?= esc($profil_prodi['nip_ketua_prodi']) ?>)</span></td>
                </tr>
                <tr>
                    <th>Nama Dekan</th>
                    <td><?= esc($profil_prodi['nama_dekan']) ?> <span class="text-muted" style="font-size:90%;">(NIP: <?= esc($profil_prodi['nip_dekan']) ?>)</span></td>
                </tr>
            </table>
        </div>
        <?php if (session('role') === 'admin'): ?>
        <div class="d-flex gap-2 flex-wrap">
            <a href="<?= base_url('admin/profil-prodi/edit/' . $profil_prodi['id']) ?>" class="btn btn-outline-secondary flex-fill">
                <i class="bi bi-pencil-square"></i> Edit Profil
            </a>
        </div>
        <?php endif; ?>
    <?php endif ?>
</div>

<?= $this->endSection() ?>

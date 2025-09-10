<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<h2 class="fw-bold mb-4">Tambah Data Dosen</h2>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?= base_url('admin/dosen/store') ?>" method="post">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="nip" class="form-label">NIP / NIDN</label>
                <input type="text" class="form-control" id="nip" name="nip" required>
            </div>

            <div class="mb-3">
                <label for="nama_lengkap" class="form-label">Nama Lengkap (dengan gelar)</label>
                <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
            </div>

            <div class="mb-3">
                <label for="jabatan_fungsional" class="form-label">Jabatan Fungsional</label>
                <select class="form-select" id="jabatan_fungsional" name="jabatan_fungsional[]" multiple>
                    <option value="Asisten Ahli">Asisten Ahli</option>
                    <option value="Lektor">Lektor</option>
                    <option value="Lektor Kepala">Lektor Kepala</option>
                </select>
                <div class="form-text">Anda bisa memilih lebih dari satu. Tahan tombol Ctrl (atau Cmd di Mac) lalu klik pilihan.</div>
            </div>

            <div class="mb-3">
                <label for="status_keaktifan" class="form-label">Status Keaktifan</label>
                <select class="form-select" id="status_keaktifan" name="status_keaktifan" required>
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="<?= base_url('admin/dosen') ?>" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>

<?= $this->endSection() ?>
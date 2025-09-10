<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>
<h2>Tambah Referensi</h2>
<form method="post" action="<?= base_url('rps/referensi-store/'.$rps_id) ?>">
    <div class="mb-2">
        <label>Tipe</label>
        <select name="tipe" class="form-control" required>
            <option value="utama">Utama</option>
            <option value="pendukung">Pendukung</option>
        </select>
    </div>
    <div class="mb-2">
        <label>Judul</label>
        <input type="text" name="judul" class="form-control" required>
    </div>
    <div class="mb-2">
        <label>Penulis</label>
        <input type="text" name="penulis" class="form-control">
    </div>
    <div class="mb-2">
        <label>Tahun</label>
        <input type="text" name="tahun" class="form-control">
    </div>
    <div class="mb-2">
        <label>Penerbit</label>
        <input type="text" name="penerbit" class="form-control">
    </div>
    <div class="mb-2">
        <label>Keterangan</label>
        <input type="text" name="keterangan" class="form-control">
    </div>
    <button class="btn btn-primary">Simpan</button>
    <a href="<?= base_url('rps/referensi/'.$rps_id) ?>" class="btn btn-secondary">Kembali</a>
</form>
<?= $this->endSection() ?>

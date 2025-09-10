<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>
<h2>Edit Referensi</h2>
<form method="post" action="<?= base_url('rps/referensi-update/'.$referensi['id']) ?>">
    <div class="mb-2">
        <label>Tipe</label>
        <select name="tipe" class="form-control" required>
            <option value="utama" <?= $referensi['tipe']=='utama'?'selected':'' ?>>Utama</option>
            <option value="pendukung" <?= $referensi['tipe']=='pendukung'?'selected':'' ?>>Pendukung</option>
        </select>
    </div>
    <div class="mb-2">
        <label>Judul</label>
        <input type="text" name="judul" class="form-control" value="<?= esc($referensi['judul']) ?>" required>
    </div>
    <div class="mb-2">
        <label>Penulis</label>
        <input type="text" name="penulis" class="form-control" value="<?= esc($referensi['penulis']) ?>">
    </div>
    <div class="mb-2">
        <label>Tahun</label>
        <input type="text" name="tahun" class="form-control" value="<?= esc($referensi['tahun']) ?>">
    </div>
    <div class="mb-2">
        <label>Penerbit</label>
        <input type="text" name="penerbit" class="form-control" value="<?= esc($referensi['penerbit']) ?>">
    </div>
    <div class="mb-2">
        <label>Keterangan</label>
        <input type="text" name="keterangan" class="form-control" value="<?= esc($referensi['keterangan']) ?>">
    </div>
    <button class="btn btn-primary">Update</button>
    <a href="<?= base_url('rps/referensi/'.$referensi['rps_id']) ?>" class="btn btn-secondary">Kembali</a>
</form>
<?= $this->endSection() ?>

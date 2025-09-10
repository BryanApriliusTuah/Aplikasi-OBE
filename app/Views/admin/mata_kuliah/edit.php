<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>
<h2>Edit Mata Kuliah</h2>
<form method="post" action="<?= base_url('admin/mata-kuliah/update/'.$mk['id']) ?>">
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?php 
            $errors = session()->getFlashdata('error');
            if (is_array($errors)) {
                foreach ($errors as $error) {
                    echo $error . '<br>';
                }
            } else {
                echo $errors;
            }
            ?>
        </div>
    <?php endif ?>

    <div class="mb-2">
        <label>Kode MK</label>
        <input type="text" name="kode_mk" class="form-control" value="<?= old('kode_mk', $mk['kode_mk']) ?>" required>
    </div>
    <div class="mb-2">
        <label>Nama Mata Kuliah</label>
        <input type="text" name="nama_mk" class="form-control" value="<?= old('nama_mk', $mk['nama_mk']) ?>" required>
    </div>
    <div class="mb-2">
        <label for="kategori" class="form-label">Kategori Mata Kuliah</label>
        <select class="form-select" id="kategori" name="kategori" required>
            <option value="">-- Pilih Kategori --</option>
            <option value="wajib_teori" <?= old('kategori', $mk['kategori'])=='wajib_teori'?'selected':'' ?>>Wajib Teori</option>
            <option value="wajib_praktikum" <?= old('kategori', $mk['kategori'])=='wajib_praktikum'?'selected':'' ?>>Wajib Praktikum</option>
            <option value="pilihan" <?= old('kategori', $mk['kategori'])=='pilihan'?'selected':'' ?>>Pilihan</option>
            <option value="mkwk" <?= old('kategori', $mk['kategori'])=='mkwk'?'selected':'' ?>>MKWK</option>
        </select>
    </div>
    <div class="mb-2">
        <label>Tipe</label>
        <select name="tipe" class="form-select" required>
            <option value="">-- Pilih Tipe --</option>
            <option value="wajib" <?= old('tipe', $mk['tipe']) == 'wajib' ? 'selected' : '' ?>>Wajib</option>
            <option value="pilihan" <?= old('tipe', $mk['tipe']) == 'pilihan' ? 'selected' : '' ?>>Pilihan</option>
        </select>
    </div>
    <div class="mb-2">
        <label>Semester</label>
        <select name="semester" class="form-control" required>
            <?php for($i=1; $i<=8; $i++): ?>
                <option value="<?= $i ?>" <?= old('semester', $mk['semester'])==$i?'selected':'' ?>><?= $i ?></option>
            <?php endfor ?>
        </select>
    </div>
    <div class="mb-2">
        <label>SKS</label>
        <input type="number" name="sks" class="form-control" value="<?= old('sks', $mk['sks']) ?>" required>
    </div>
    <div class="mb-2">
        <label>Deskripsi Singkat</label>
        <textarea name="deskripsi_singkat" class="form-control" required><?= old('deskripsi_singkat', $mk['deskripsi_singkat']) ?></textarea>
    </div>
    <!-- Tambahan: Prasyarat Mata Kuliah -->
    <div class="mb-2">
        <label>Prasyarat Mata Kuliah <small class="text-muted">(boleh kosong / lebih dari satu)</small></label>
        <select name="prasyarat_mk_id[]" class="form-select" multiple>
            <?php foreach($all_mk as $m): ?>
                <?php if($m['id'] != $mk['id']): // Tidak boleh pilih dirinya sendiri ?>
                    <option value="<?= $m['id'] ?>"
                        <?= in_array($m['id'], old('prasyarat_mk_id', $prasyarat_terpilih)) ? 'selected' : '' ?>>
                        <?= $m['kode_mk'] . ' - ' . $m['nama_mk'] ?>
                    </option>
                <?php endif ?>
            <?php endforeach ?>
        </select>
        <small class="text-muted">* Tekan CTRL untuk pilih lebih dari satu</small>
    </div>

    <button class="btn btn-primary">Update</button>
    <a href="<?= base_url('admin/mata-kuliah') ?>" class="btn btn-secondary">Kembali</a>
</form>
<?= $this->endSection() ?>

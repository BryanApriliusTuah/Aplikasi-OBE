<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container py-3">
    <div class="card p-4">
        <h3 class="fw-bold mb-3">Edit Bahan Kajian</h3>

        <?php
            // Ekstrak 2 digit angka dari kode lama
            $angka = preg_replace('/^\s*BK/i', '', $bk['kode_bk'] ?? '');
        ?>

        <form action="<?= base_url('admin/bahan-kajian/update/' . $bk['id']) ?>" method="post" autocomplete="off">
            <?= csrf_field() ?>

            <?php if(isset($validation) && $validation->getErrors()): ?>
                <div class="alert alert-danger">
                    <?php foreach ($validation->getErrors() as $err) echo esc($err) . '<br>'; ?>
                </div>
            <?php elseif(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                <?php 
                    $errors = session()->getFlashdata('error');
                    if (is_array($errors)) { foreach ($errors as $e) echo esc($e).'<br>'; }
                    else { echo esc($errors); }
                ?>
                </div>
            <?php endif; ?>

            <!-- Kode BK (prefix locked + 2 digit angka) -->
            <div class="mb-3">
                <label class="form-label">Kode BK</label>
                <div class="input-group">
                    <span class="input-group-text">BK</span>
                    <input
                        type="text"
                        name="no_urut"
                        id="no_urut"
                        class="form-control <?= (isset($validation) && $validation->hasError('kode_bk')) ? 'is-invalid' : '' ?>"
                        inputmode="numeric"
                        pattern="\d{2}"
                        maxlength="2"
                        placeholder="mis. 01, 02, 10"
                        value="<?= esc(old('no_urut', $angka)) ?>"
                        required
                    >
                    <?php if(isset($validation) && $validation->hasError('kode_bk')): ?>
                        <div class="invalid-feedback"><?= esc($validation->getError('kode_bk')) ?></div>
                    <?php endif ?>
                </div>
                <div class="form-text">Isi <b>2 digit angka</b> saja. Contoh hasil: <code>BK01</code>, <code>BK10</code>.</div>
            </div>

            <div class="mb-3">
                <label for="nama_bk" class="form-label">Nama Bahan Kajian</label>
                <textarea
                    class="form-control <?= (isset($validation) && $validation->hasError('nama_bk')) ? 'is-invalid' : '' ?>"
                    id="nama_bk"
                    name="nama_bk"
                    rows="3"
                    required
                ><?= esc(old('nama_bk', $bk['nama_bk'])) ?></textarea>
                <?php if(isset($validation) && $validation->hasError('nama_bk')): ?>
                    <div class="invalid-feedback"><?= esc($validation->getError('nama_bk')) ?></div>
                <?php endif ?>
            </div>

            <div class="d-flex gap-2">
                <a href="<?= base_url('admin/bahan-kajian') ?>" class="btn btn-outline-secondary w-100">
                    ← Kembali
                </a>
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-save2"></i> Update
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Batasi input ke digit & maksimal 2 karakter
document.getElementById('no_urut').addEventListener('input', function(){
    this.value = this.value.replace(/\D+/g,'').slice(0,2);
});
</script>

<?= $this->endSection() ?>

<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-0">
    <div class="col-lg-7 col-md-10 mx-auto mt-2">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="fw-bold mb-3">Edit Profil Lulusan</h3>
                <a href="<?= base_url('admin/profil-lulusan') ?>" class="btn btn-outline-secondary mb-3">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>

                <?php
                    // ekstrak 2 digit angka dari kode lama, contoh: "PL07" -> "07"
                    $angka = preg_replace('/^\s*PL/i', '', $profil_lulusan['kode_pl'] ?? '');
                ?>

                <form action="<?= base_url('admin/profil-lulusan/update/' . $profil_lulusan['id']) ?>" method="post" autocomplete="off">
                    <?= csrf_field() ?>

                    <?php if(isset($validation) && $validation->getErrors()): ?>
                        <div class="alert alert-danger">
                            <?php foreach ($validation->getErrors() as $err) echo esc($err) . '<br>'; ?>
                        </div>
                    <?php elseif(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger">
                            <?php 
                                $errors = session()->getFlashdata('error');
                                if (is_array($errors)) { foreach ($errors as $error) echo esc($error) . '<br>'; }
                                else { echo esc($errors); }
                            ?>
                        </div>
                    <?php endif ?>

                    <!-- Kode PL (prefix locked + 2 digit angka) -->
                    <div class="mb-3">
                        <label class="form-label">Kode PL</label>
                        <div class="input-group">
                            <span class="input-group-text">PL</span>
                            <input
                                type="text"
                                name="no_urut"
                                id="no_urut"
                                class="form-control <?= (isset($validation) && $validation->hasError('kode_pl')) ? 'is-invalid' : '' ?>"
                                inputmode="numeric"
                                pattern="\d{2}"
                                maxlength="2"
                                placeholder="mis. 01, 02, 10"
                                value="<?= esc(old('no_urut', $angka)) ?>"
                                required
                            >
                            <?php if(isset($validation) && $validation->hasError('kode_pl')): ?>
                                <div class="invalid-feedback"><?= esc($validation->getError('kode_pl')) ?></div>
                            <?php endif ?>
                        </div>
                        <div class="form-text">Isi <b>2 digit angka</b> saja. Contoh hasil: <code>PL01</code>, <code>PL10</code>.</div>
                    </div>

                    <div class="mb-3">
                        <label for="deskripsi" class="form-label">Deskripsi</label>
                        <textarea
                            class="form-control <?= (isset($validation) && $validation->hasError('deskripsi')) ? 'is-invalid' : '' ?>"
                            id="deskripsi"
                            name="deskripsi"
                            rows="3"
                            required
                        ><?= esc(old('deskripsi', $profil_lulusan['deskripsi'])) ?></textarea>
                        <?php if(isset($validation) && $validation->hasError('deskripsi')): ?>
                            <div class="invalid-feedback"><?= esc($validation->getError('deskripsi')) ?></div>
                        <?php endif ?>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Update
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Batasi input ke digit dan maksimal 2 karakter
document.getElementById('no_urut').addEventListener('input', function() {
    this.value = this.value.replace(/\D+/g,'').slice(0,2);
});
</script>

<?= $this->endSection() ?>

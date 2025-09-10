<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-10">
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h3 class="fw-bold mb-4">Tambah CPL</h3>
                    <a href="<?= base_url('admin/cpl') ?>" class="btn btn-outline-secondary mb-3">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>

                    <form action="<?= base_url('admin/cpl/store') ?>" method="post" autocomplete="off">
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

                        <!-- Kode CPL (prefix locked + 2 digit angka) -->
                        <div class="mb-3">
                            <label class="form-label">Kode CPL</label>
                            <div class="input-group">
                                <span class="input-group-text">CPL</span>
                                <input
                                    type="text"
                                    name="no_urut"
                                    id="no_urut"
                                    class="form-control <?= (isset($validation) && $validation->hasError('kode_cpl')) ? 'is-invalid' : '' ?>"
                                    inputmode="numeric"
                                    pattern="\d{2}"
                                    maxlength="2"
                                    placeholder="mis. 01, 02, 10"
                                    value="<?= esc(old('no_urut')) ?>"
                                    required
                                >
                                <?php if(isset($validation) && $validation->hasError('kode_cpl')): ?>
                                    <div class="invalid-feedback"><?= esc($validation->getError('kode_cpl')) ?></div>
                                <?php endif ?>
                            </div>
                            <div class="form-text">Isi <b>2 digit angka</b> saja. Contoh hasil: <code>CPL01</code>, <code>CPL10</code>.</div>
                        </div>

                        <div class="mb-3">
                            <label for="deskripsi" class="form-label">Deskripsi</label>
                            <textarea
                                class="form-control <?= (isset($validation) && $validation->hasError('deskripsi')) ? 'is-invalid' : '' ?>"
                                id="deskripsi" name="deskripsi" rows="3" required
                            ><?= esc(old('deskripsi')) ?></textarea>
                            <?php if(isset($validation) && $validation->hasError('deskripsi')): ?>
                                <div class="invalid-feedback"><?= esc($validation->getError('deskripsi')) ?></div>
                            <?php endif ?>
                        </div>

                        <div class="mb-3">
                            <label for="jenis_cpl" class="form-label">Jenis CPL</label>
                            <select
                                class="form-select <?= (isset($validation) && $validation->hasError('jenis_cpl')) ? 'is-invalid' : '' ?>"
                                id="jenis_cpl" name="jenis_cpl" required
                            >
                                <option value="" selected disabled>Pilih Jenis CPL</option>
                                <option value="S"  <?= old('jenis_cpl') == 'S'  ? 'selected' : '' ?>>S (Sikap)</option>
                                <option value="P"  <?= old('jenis_cpl') == 'P'  ? 'selected' : '' ?>>P (Pengetahuan)</option>
                                <option value="KU" <?= old('jenis_cpl') == 'KU' ? 'selected' : '' ?>>KU (Keterampilan Umum)</option>
                                <option value="KK" <?= old('jenis_cpl') == 'KK' ? 'selected' : '' ?>>KK (Keterampilan Khusus)</option>
                            </select>
                            <?php if(isset($validation) && $validation->hasError('jenis_cpl')): ?>
                                <div class="invalid-feedback"><?= esc($validation->getError('jenis_cpl')) ?></div>
                            <?php endif ?>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Batasi input ke digit & maksimal 2 karakter
const noUrut = document.getElementById('no_urut');
noUrut.addEventListener('input', function() {
    this.value = this.value.replace(/\D+/g,'').slice(0,2);
});
</script>

<?= $this->endSection() ?>

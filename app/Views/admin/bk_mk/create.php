<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container" style="max-width: 650px">
    <h2 class="fw-bold text-center mb-4">Form Pemetaan BK ke Mata Kuliah</h2>
    <div class="card shadow-sm">
        <div class="card-body">
        <form method="post" action="<?= base_url('admin/bkmk/store') ?>">
            <?php if(session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= session()->getFlashdata('error'); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif ?>
            <div class="mb-3">
                <label class="form-label">Pilih Bahan Kajian</label>
                <select name="bahan_kajian_id" class="form-select" required>
                    <option value="">-- Pilih BK --</option>
                    <?php foreach ($bk as $b): ?>
                        <option value="<?= $b['id'] ?>" <?= ($selectedBkId == $b['id']) ? 'selected' : '' ?>>
                            <?= $b['kode_bk'] ?> - <?= $b['nama_bk'] ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Pilih Mata Kuliah</label>
                <select name="mata_kuliah_id[]" class="form-select" multiple required>
                    <?php foreach ($mk as $m): ?>
                        <option value="<?= $m['id'] ?>" <?= (is_array($selectedMkId) && in_array($m['id'], $selectedMkId)) ? 'selected' : '' ?>>
                            <?= $m['kode_mk'] ?> - <?= $m['nama_mk'] ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <small class="text-muted">*Tekan Ctrl (atau Cmd di Mac) untuk memilih lebih dari satu mata kuliah</small>
            </div>
            <div class="mt-3 d-flex justify-content-end">
                <a href="<?= base_url('admin/bkmk') ?>" class="btn btn-secondary me-2">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan Pemetaan</button>
            </div>
        </form>

        </div>
    </div>
</div>

<?= $this->endSection() ?>

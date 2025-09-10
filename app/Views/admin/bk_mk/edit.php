<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container" style="max-width: 650px">
    <h2 class="fw-bold text-center mb-4">Edit Pemetaan BK ke Mata Kuliah</h2>
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="post" action="<?= base_url('admin/bkmk/update/' . $bk['id']) ?>">
                <?php if(session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error'); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif ?>
                <div class="mb-3">
                    <label class="form-label">Bahan Kajian</label>
                    <input type="text" class="form-control" value="<?= $bk['kode_bk'].' - '.$bk['nama_bk'] ?>" readonly>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pilih Mata Kuliah</label>
                    <select name="mata_kuliah_id[]" class="form-select" multiple size="8" required>
                        <?php foreach ($mk as $m): ?>
                            <option value="<?= $m['id'] ?>" <?= in_array($m['id'], $selectedMkId) ? 'selected' : '' ?>>
                                <?= $m['kode_mk'] ?> - <?= $m['nama_mk'] ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                    <small class="text-muted">Bisa pilih lebih dari satu dengan Ctrl/Shift</small>
                </div>
                <div class="mt-3 d-flex justify-content-end">
                    <a href="<?= base_url('admin/bkmk') ?>" class="btn btn-secondary me-2">Kembali</a>
                    <button type="submit" class="btn btn-primary">Update Pemetaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

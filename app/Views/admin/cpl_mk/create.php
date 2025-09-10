<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0 w-100 text-center">Tambah Pemetaan CPL ke Mata Kuliah</h2>
    </div>
    <div class="card shadow-sm mx-auto" style="max-width:600px;">
        <div class="card-body">
            <form method="post" action="<?= base_url('admin/cpl-mk/store') ?>">
                <?php if(session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error'); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif ?>
                <div class="mb-3">
                    <label class="form-label">Pilih Mata Kuliah</label>
                    <select name="mata_kuliah_id" class="form-select" required>
                        <option value="">-- Pilih MK --</option>
                        <?php foreach ($mk as $m): ?>
                            <option value="<?= $m['id'] ?>" <?= old('mata_kuliah_id') == $m['id'] ? 'selected' : '' ?>>
                                <?= esc($m['kode_mk']) ?> - <?= esc($m['nama_mk']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Pilih CPL <span class="text-muted">(bisa pilih lebih dari satu)</span></label>
                    <select name="cpl_id[]" class="form-select" multiple size="6" required>
                        <?php foreach ($cpl as $c): ?>
                            <option value="<?= $c['id'] ?>" <?= (is_array(old('cpl_id')) && in_array($c['id'], old('cpl_id', []))) ? 'selected' : '' ?>>
                                <?= esc($c['kode_cpl']) ?>
                            </option>
                        <?php endforeach ?>
                    </select>
                    <small class="text-muted">Tekan Ctrl (Windows) / Command (Mac) untuk memilih lebih dari satu CPL</small>
                </div>
                <div class="mt-3 d-flex justify-content-end">
                    <a href="<?= base_url('admin/cpl-mk') ?>" class="btn btn-secondary me-2">Kembali</a>
                    <button type="submit" class="btn btn-primary">Simpan Pemetaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

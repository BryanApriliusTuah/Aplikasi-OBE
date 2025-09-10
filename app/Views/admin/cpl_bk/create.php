<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 85vh;">
    <div style="min-width:350px;max-width:430px;width:100%;">
        <div class="text-center mb-3">
            <h3 class="fw-bold mb-0">Tambah Pemetaan CPL ke Bahan Kajian</h3>
        </div>
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?= base_url('admin/cpl-bk/store') ?>" method="post" autocomplete="off">
                    <?php if(session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error'); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif ?>

                    <div class="mb-3">
                        <label class="form-label">Pilih CPL</label>
                        <select name="cpl_id" class="form-select" required>
                            <option value="">-- Pilih CPL --</option>
                            <?php foreach ($cpl as $item): ?>
                                <option value="<?= $item['id'] ?>"><?= esc($item['kode_cpl']) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pilih Bahan Kajian</label>
                        <select name="bk_id[]" class="form-select" multiple size="<?= count($bk)<6 ? count($bk) : 6 ?>" required>
                            <?php foreach ($bk as $item): ?>
                                <option value="<?= $item['id'] ?>">
                                    <?= esc($item['kode_bk']) ?> - <?= esc($item['nama_bk']) ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                        <small class="text-muted">*Tekan <b>Ctrl</b> (Windows) / <b>Cmd</b> (Mac) untuk memilih lebih dari satu</small>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('admin/cpl-bk') ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

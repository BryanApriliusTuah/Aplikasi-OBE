<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8 col-sm-10">
            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h3 class="fw-bold mb-3">Tambah Pemetaan CPL ke PL</h3>
                    <form action="<?= base_url('admin/cpl-pl/store') ?>" method="post" autocomplete="off">
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
                            <label class="form-label">Pilih PL</label>
                            <select name="pl_id[]" class="form-select" multiple size="<?= count($pl) < 6 ? count($pl) : 6 ?>" required>
                                <?php foreach ($pl as $item): ?>
                                    <option value="<?= $item['id'] ?>"><?= esc($item['kode_pl']) ?></option>
                                <?php endforeach ?>
                            </select>
                            <small class="text-muted">*Tekan <b>Ctrl</b> (Windows) atau <b>Cmd</b> (Mac) untuk memilih lebih dari satu PL</small>
                        </div>
                        <div class="d-flex justify-content-between">
                            <a href="<?= base_url('admin/cpl-pl') ?>" class="btn btn-secondary">
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
</div>

<?= $this->endSection() ?>

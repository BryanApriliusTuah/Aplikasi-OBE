<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<h2 class="fw-bold mb-4">Tambah Konfigurasi Nilai</h2>

<div class="card shadow-sm">
    <div class="card-body">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/settings/store') ?>" method="post">
            <?= csrf_field() ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="grade_letter" class="form-label">Huruf Mutu <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control"
                               id="grade_letter"
                               name="grade_letter"
                               value="<?= old('grade_letter') ?>"
                               placeholder="e.g., A, AB, B, BC, C, D, E"
                               maxlength="10"
                               required>
                        <div class="form-text">Contoh: A, AB, B, BC, C, D, E</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="order_number" class="form-label">Urutan <span class="text-danger">*</span></label>
                        <input type="number"
                               class="form-control"
                               id="order_number"
                               name="order_number"
                               value="<?= old('order_number') ?>"
                               placeholder="1"
                               required>
                        <div class="form-text">Urutan tampilan (1 = tertinggi)</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="min_score" class="form-label">Nilai Minimum <span class="text-danger">*</span></label>
                        <input type="number"
                               class="form-control"
                               id="min_score"
                               name="min_score"
                               value="<?= old('min_score') ?>"
                               step="0.01"
                               min="0"
                               max="100"
                               placeholder="0.00"
                               required>
                        <div class="form-text">Range: 0.00 - 100.00</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="max_score" class="form-label">Nilai Maksimum <span class="text-danger">*</span></label>
                        <input type="number"
                               class="form-control"
                               id="max_score"
                               name="max_score"
                               value="<?= old('max_score') ?>"
                               step="0.01"
                               min="0"
                               max="100"
                               placeholder="100.00"
                               required>
                        <div class="form-text">Range: 0.00 - 100.00</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="grade_point" class="form-label">Grade Point</label>
                        <input type="number"
                               class="form-control"
                               id="grade_point"
                               name="grade_point"
                               value="<?= old('grade_point') ?>"
                               step="0.01"
                               min="0"
                               max="4"
                               placeholder="4.00">
                        <div class="form-text">Nilai bobot grade (0.00 - 4.00), opsional</div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="description" class="form-label">Deskripsi</label>
                        <input type="text"
                               class="form-control"
                               id="description"
                               name="description"
                               value="<?= old('description') ?>"
                               placeholder="e.g., Istimewa, Baik Sekali"
                               maxlength="100">
                        <div class="form-text">Deskripsi grade (opsional)</div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Status Kelulusan <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="radio"
                                   name="is_passing"
                                   id="is_passing_yes"
                                   value="1"
                                   <?= old('is_passing', '1') == '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_passing_yes">
                                <span class="badge bg-success">Lulus</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="radio"
                                   name="is_passing"
                                   id="is_passing_no"
                                   value="0"
                                   <?= old('is_passing') == '0' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_passing_no">
                                <span class="badge bg-danger">Tidak Lulus</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Status Aktif <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="radio"
                                   name="is_active"
                                   id="is_active_yes"
                                   value="1"
                                   <?= old('is_active', '1') == '1' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active_yes">
                                <span class="badge bg-success">Aktif</span>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input"
                                   type="radio"
                                   name="is_active"
                                   id="is_active_no"
                                   value="0"
                                   <?= old('is_active') == '0' ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active_no">
                                <span class="badge bg-secondary">Nonaktif</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <a href="<?= base_url('admin/settings') ?>" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

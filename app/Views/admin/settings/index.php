<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="mb-1">Pengaturan Sistem Penilaian</h2>
                <p class="text-muted mb-0">Kelola konfigurasi nilai dan grade untuk sistem penilaian</p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('admin/settings/reset-to-default') ?>"
                   class="btn btn-outline-warning"
                   onclick="return confirm('Apakah Anda yakin ingin mereset ke konfigurasi default? Semua konfigurasi saat ini akan dihapus.')">
                    <i class="bi bi-arrow-clockwise"></i> Reset ke Default
                </a>
                <a href="<?= base_url('admin/settings/create') ?>" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Tambah Konfigurasi
                </a>
            </div>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th scope="col" class="text-center" style="width: 5%;">No</th>
                        <th scope="col" class="text-center" style="width: 10%;">Huruf Mutu</th>
                        <th scope="col" class="text-center" style="width: 15%;">Range Nilai</th>
                        <th scope="col" class="text-center" style="width: 10%;">Grade Point</th>
                        <th scope="col" style="width: 20%;">Deskripsi</th>
                        <th scope="col" class="text-center" style="width: 10%;">Status Lulus</th>
                        <th scope="col" class="text-center" style="width: 10%;">Status</th>
                        <th scope="col" class="text-center" style="width: 20%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($grades)): ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                    <p class="mt-2 mb-0">Belum ada konfigurasi nilai.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($grades as $grade): ?>
                        <tr>
                            <td class="text-center"><?= $no++; ?></td>
                            <td class="text-center">
                                <span class="badge bg-primary fs-6"><?= esc($grade['grade_letter']); ?></span>
                            </td>
                            <td class="text-center">
                                <strong><?= number_format($grade['min_score'], 2); ?></strong> -
                                <strong><?= number_format($grade['max_score'], 2); ?></strong>
                            </td>
                            <td class="text-center">
                                <?= $grade['grade_point'] ? number_format($grade['grade_point'], 2) : '-'; ?>
                            </td>
                            <td><?= esc($grade['description']); ?></td>
                            <td class="text-center">
                                <?php if ($grade['is_passing']): ?>
                                    <span class="badge bg-success">Lulus</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Tidak Lulus</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($grade['is_active']): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="<?= base_url('admin/settings/edit/' . $grade['id']); ?>"
                                       class="btn btn-outline-primary btn-sm"
                                       data-bs-toggle="tooltip"
                                       title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="<?= base_url('admin/settings/toggle/' . $grade['id']); ?>"
                                       class="btn btn-outline-warning btn-sm"
                                       data-bs-toggle="tooltip"
                                       title="<?= $grade['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>">
                                        <i class="bi bi-toggle-<?= $grade['is_active'] ? 'on' : 'off' ?>"></i>
                                    </a>
                                    <a href="<?= base_url('admin/settings/delete/' . $grade['id']); ?>"
                                       class="btn btn-outline-danger btn-sm"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus konfigurasi ini?')"
                                       data-bs-toggle="tooltip"
                                       title="Hapus">
                                        <i class="bi bi-trash3"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="alert alert-info mt-4" role="alert">
            <i class="bi bi-info-circle-fill"></i>
            <strong>Informasi:</strong> Konfigurasi nilai ini akan digunakan untuk menghitung nilai huruf pada seluruh sistem penilaian.
            Pastikan range nilai tidak tumpang tindih dan mencakup seluruh range 0-100.
        </div>
    </div>
</div>

<?= $this->endSection() ?>

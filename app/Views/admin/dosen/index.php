<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Master Data Dosen</h2>
            <div class="d-flex">
                <?php if (session()->get('role') === 'admin'): ?>
                    <a href="<?= base_url('admin/dosen/create') ?>" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Tambah Dosen
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">NIP</th>
                        <th scope="col">Nama Lengkap (dengan Gelar)</th>
                        <th scope="col">Jabatan Fungsional</th>
                        <th scope="col">Status</th>
                        
                        <?php if (session()->get('role') === 'admin'): ?>
                            <th scope="col" class="text-center">Aksi</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($dosen)): ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada data dosen.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($dosen as $d): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= esc($d['nip']); ?></td>
                            <td><?= esc($d['nama_lengkap']); ?></td>
                            <td><?= esc($d['jabatan_fungsional']); ?></td>
                            <td>
                                <?php if ($d['status_keaktifan'] === 'Aktif'): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Tidak Aktif</span>
                                <?php endif; ?>
                            </td>
                            
                            <?php if (session()->get('role') === 'admin'): ?>
                                <td class="text-center">
                                    <a href="<?= base_url('admin/dosen/edit/' . $d['id']); ?>" class="btn btn-outline-primary btn-sm" data-bs-toggle="tooltip" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <a href="<?= base_url('admin/dosen/delete/' . $d['id']); ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')" data-bs-toggle="tooltip" title="Hapus">
                                        <i class="bi bi-trash3"></i>
                                    </a>
                                </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
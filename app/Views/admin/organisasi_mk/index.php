<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold mb-0">Organisasi Mata Kuliah</h2>
        <div class="btn-group">
            <button class="btn btn-outline-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-download"></i> Download
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a class="dropdown-item" href="<?= base_url('admin/organisasi-mk/exportExcel') ?>">
                        <i class="bi bi-file-earmark-excel text-success"></i> Excel
                    </a>
                </li>
                <li>
                    <a class="dropdown-item" href="<?= base_url('admin/organisasi-mk/exportPdf') ?>">
                        <i class="bi bi-file-earmark-pdf text-danger"></i> PDF
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered align-middle table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" rowspan="2" style="vertical-align: middle;">Smt</th>
                            <th class="text-center" rowspan="2" style="vertical-align: middle;">SKS</th>
                            <th class="text-center" rowspan="2" style="vertical-align: middle;">Jml MK</th>
                            <th class="text-center" colspan="2">MK Wajib</th>
                            <th class="text-center" rowspan="2" style="vertical-align: middle;">MK Pilihan</th>
                            <th class="text-center" rowspan="2" style="vertical-align: middle;">MKWK</th>
                        </tr>
                        <tr>
                            <th class="text-center">Teori</th>
                            <th class="text-center">Praktikum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for ($i = 1; $i <= 8; $i++): ?>
                        <tr>
                            <td class="text-center fw-bold"><?= $i ?></td>
                            <td class="text-center"><?= $total_sks[$i] ?? 0 ?></td>
                            <td class="text-center"><?= $jumlah_mk[$i] ?? 0 ?></td>
                            <td style="white-space: normal;"><?= !empty($matkul[$i]['wajib_teori']) ? implode(', ', array_column($matkul[$i]['wajib_teori'], 'nama_mk')) : '' ?></td>
                            <td style="white-space: normal;"><?= !empty($matkul[$i]['wajib_praktikum']) ? implode(', ', array_column($matkul[$i]['wajib_praktikum'], 'nama_mk')) : '' ?></td>
                            <td style="white-space: normal;"><?= !empty($matkul[$i]['pilihan']) ? implode(', ', array_column($matkul[$i]['pilihan'], 'nama_mk')) : '' ?></td>
                            <td style="white-space: normal;"><?= !empty($matkul[$i]['mkwk']) ? implode(', ', array_column($matkul[$i]['mkwk'], 'nama_mk')) : '' ?></td>
                        </tr>
                        <?php endfor ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<h2>Rencana Mingguan RPS</h2>
<div class="d-flex justify-content-between align-items-center mb-3">
    <a href="<?= base_url('rps') ?>" class="btn btn-secondary btn-lg">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar RPS
    </a>
    <a href="<?= base_url('rps/mingguan-create/'.$rps_id) ?>" class="btn btn-primary btn-lg">
        <i class="bi bi-plus-circle"></i> Tambah Mingguan
    </a>
</div>

<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif ?>

<?php
    if ($totalBobot == 100) {
        $notifClass = 'alert-success';
        $notifText = 'Total bobot sudah PAS 100%.';
    } elseif ($totalBobot < 100) {
        $notifClass = 'alert-danger';
        $notifText = 'Total bobot masih kurang (<100%).';
    } else {
        $notifClass = 'alert-warning';
        $notifText = 'Total bobot LEBIH dari 100%.';
    }
?>
<div class="alert <?= $notifClass ?> d-flex align-items-center" role="alert" style="font-size:1.1em;">
    <i class="bi bi-exclamation-circle me-2"></i>
    <div>
        <strong>Total seluruh bobot dari semua minggu:</strong>
        <span style="font-weight:bold;"><?= $totalBobot ?>%</span>
        &mdash; <?= $notifText ?>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th>No</th>
                <th>Minggu</th>
                <th>CPL</th>
                <th>CPMK</th>
                <th>SubCPMK</th>
                <th>Teknik Penilaian</th>
                <th>Bobot (%)</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; foreach($mingguan as $m): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= esc($m['minggu']) ?></td>
                
                <td><?= esc($m['kode_cpl'] ?? '-') ?></td>
                <td><?= esc($m['kode_cpmk'] ?? '-') ?></td>
                <td><?= esc($m['kode_sub_cpmk'] ?? '-') ?></td>
                
                <td>
                    <?php
                        $teknik = json_decode($m['teknik_penilaian'], true);
                        $label_teknik = [
                            'partisipasi'   => 'Partisipasi',
                            'observasi'     => 'Observasi',
                            'unjuk_kerja'   => 'Unjuk Kerja',
                            'proyek'        => 'Proyek',
                            'tes_tulis_uts' => 'Tes Tulis (UTS)',
                            'tes_tulis_uas' => 'Tes Tulis (UAS)',
                            'tes_lisan'     => 'Tes Lisan'
                        ];
                        $hasil = [];
                        if (is_array($teknik)) {
                            foreach($teknik as $k => $bobot) {
                                $nm = $label_teknik[$k] ?? ucfirst($k);
                                if ($bobot > 0) { // Hanya tampilkan yang ada bobotnya
                                    $hasil[] = $nm . ' (' . $bobot . ')';
                                }
                            }
                        }
                        echo !empty($hasil) ? implode(', ', $hasil) : '-'; 
                    ?>
                </td>
                <td><?= esc($m['bobot']) ?></td>
                <td>
                    <div class="d-flex gap-1">
                        <a href="<?= base_url('rps/mingguan-edit/'.$m['id']) ?>" 
                            class="btn btn-outline-primary btn-sm p-1" 
                            title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <form action="<?= base_url('rps/mingguan-delete/'.$m['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Yakin hapus?');">
                            <button class="btn btn-outline-danger btn-sm p-1" type="submit" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            <?php endforeach ?>
            <?php if(empty($mingguan)): ?>
            <tr>
                <td colspan="8" class="text-center text-secondary py-5">Data belum ada.</td>
            </tr>
            <?php endif ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>
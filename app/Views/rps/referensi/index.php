<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>
<h2>Referensi RPS</h2>
<a href="<?= base_url('rps/referensi-create/'.$rps_id) ?>" class="btn btn-primary mb-2">Tambah Referensi</a>
<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif ?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>No</th>
            <th>Tipe</th>
            <th>Judul</th>
            <th>Penulis</th>
            <th>Tahun</th>
            <th>Penerbit</th>
            <th>Keterangan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php $no=1; foreach($referensi as $r): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= esc($r['tipe']) ?></td>
            <td><?= esc($r['judul']) ?></td>
            <td><?= esc($r['penulis']) ?></td>
            <td><?= esc($r['tahun']) ?></td>
            <td><?= esc($r['penerbit']) ?></td>
            <td><?= esc($r['keterangan']) ?></td>
            <td>
                <a href="<?= base_url('rps/referensi-edit/'.$r['id']) ?>" class="btn btn-warning btn-sm">Edit</a>
                <form action="<?= base_url('rps/referensi-delete/'.$r['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Yakin hapus?');">
                    <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                </form>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>
<a href="<?= base_url('rps') ?>" class="btn btn-secondary">Kembali ke Daftar RPS</a>
<?= $this->endSection() ?>

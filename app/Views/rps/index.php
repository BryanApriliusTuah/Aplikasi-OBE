<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<h2>Daftar RPS</h2>
<a href="<?= base_url('rps/create') ?>" class="btn btn-primary mb-2">Tambah RPS</a>
<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
<?php endif ?>

<form method="get" class="mb-3 d-flex align-items-center flex-wrap gap-2">
    <label for="perPage" class="form-label mb-0 me-2">Tampilkan</label>
    <select name="perPage" id="perPage" class="form-select d-inline-block w-auto me-2" onchange="this.form.submit()">
        <option value="5" <?= $perPage == 5 ? 'selected' : '' ?>>5</option>
        <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
        <option value="20" <?= $perPage == 20 ? 'selected' : '' ?>>20</option>
        <option value="50" <?= $perPage == 50 ? 'selected' : '' ?>>50</option>
        <option value="1000" <?= $perPage == 1000 ? 'selected' : '' ?>>Semua</option>
    </select>
    <span>baris per halaman</span>
    <?php if (isset($currentPage)): ?>
        <input type="hidden" name="page" value="<?= esc($currentPage) ?>">
    <?php endif ?>
</form>

<div class="table-responsive">
    <table class="table table-bordered table-striped" style="table-layout: fixed; width: 100%;">
        <thead>
            <tr>
                <th style="width: 40px;">No</th>
                <th style="width: 180px;" class="text-wrap">Mata Kuliah</th>
                <th style="width: 180px;" class="text-wrap">Dosen Pengampu</th>
                <th style="width: 140px;" class="text-wrap">Koordinator MK</th>
                <th style="width: 60px;">Semester</th>
                <th style="width: 100px;">Tahun Ajaran</th>
                <th style="width: 80px;">Status</th>
                <th style="width: 160px; white-space: nowrap;">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; foreach($rps as $r): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td class="text-wrap"><?= esc($r['nama_mk']) ?></td>
                <td class="text-wrap">
                    <?php if (!empty($r['pengampu_list'])): ?>
                        <?= implode(', ', array_map('esc', $r['pengampu_list'])) ?>
                    <?php else: ?>
                        <span class="text-danger">Belum ada</span>
                    <?php endif ?>
                </td>
                <td class="text-wrap"><?= esc($r['koordinator_nama']) ?: '<span class="text-danger">Belum ada</span>' ?></td>
                <td><?= esc($r['semester']) ?></td>
                <td><?= esc($r['tahun_ajaran']) ?></td>
                <td><?= esc($r['status']) ?></td>
                <td style="white-space: nowrap; width: 160px;">
                    <div class="btn-group" role="group" aria-label="Aksi RPS">
                        <a href="<?= base_url('rps/edit/'.$r['id']) ?>" class="btn btn-sm btn-warning" title="Edit">
                            <i class="bi bi-pencil-square"></i>
                        </a>
                        <?php if ($r['status'] != 'final'): ?>
                            <a href="<?= base_url('rps/referensi/'.$r['id']) ?>" class="btn btn-sm btn-info" title="Referensi">
                                <i class="bi bi-book"></i>
                            </a>
                            <a href="<?= base_url('rps/mingguan/'.$r['id']) ?>" class="btn btn-sm btn-success" title="Kelola Mingguan">
                                <i class="bi bi-calendar-week"></i>
                            </a>
                        <?php endif; ?>
                        <a href="<?= base_url('rps/preview/'.$r['id']) ?>" class="btn btn-sm btn-primary" title="Preview RPS" target="_blank">
                            <i class="bi bi-eye"></i>
                        </a>
                        <?php if ($r['status'] != 'final'): ?>
                            <form action="<?= base_url('rps/delete/'.$r['id']) ?>" method="post" style="display:inline;" onsubmit="return confirm('Yakin hapus?');">
                                <button class="btn btn-sm btn-danger" type="submit" title="Hapus" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</div>

<?= $this->endSection() ?>

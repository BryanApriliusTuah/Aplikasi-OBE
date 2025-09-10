<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container" style="max-width: 780px;">
  <h2 class="fw-bold text-center mb-4">Edit Pemetaan CPL – MK – CPMK</h2>

  <div class="card shadow-sm">
    <div class="card-body">
      <form action="<?= base_url('admin/pemetaan-cpl-mk-cpmk/update/' . $cpl_id . '/' . $cpmk_id) ?>" method="post">
        <?= csrf_field() ?>

        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger">
            <?php
              $err = session()->getFlashdata('error');
              if (is_array($err)) {
                foreach ($err as $e) echo esc($e) . '<br>';
              } else {
                echo esc($err);
              }
            ?>
          </div>
        <?php endif; ?>

        <!-- CPL (readonly) -->
        <div class="mb-3">
          <label class="form-label">Kode CPL</label>
          <input type="text" class="form-control" value="<?= esc($kode_cpl) ?>" readonly>
        </div>

        <!-- CPMK (readonly) -->
        <div class="mb-3">
          <label class="form-label">Kode CPMK</label>
          <input type="text" class="form-control" value="<?= esc($kode_cpmk) ?>" readonly>
        </div>

        <!-- Mata Kuliah (checkbox multi-pilih) -->
        <div class="mb-3">
          <label class="form-label">Pilih Mata Kuliah</label>
          <div class="row row-cols-1 g-2">
            <?php foreach ($all_mk as $mk): ?>
              <div class="col">
                <div class="form-check">
                  <input 
                    class="form-check-input" 
                    type="checkbox" 
                    name="mata_kuliah_id[]" 
                    value="<?= $mk['id'] ?>" 
                    id="mk<?= $mk['id'] ?>"
                    <?= in_array($mk['id'], $selected_mk_ids) ? 'checked' : '' ?>
                  >
                  <label class="form-check-label" for="mk<?= $mk['id'] ?>">
                    <?= esc($mk['nama_mk']) ?>
                  </label>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>

        <!-- Tombol -->
        <div class="text-end">
          <a href="<?= base_url('admin/pemetaan-cpl-mk-cpmk') ?>" class="btn btn-secondary">Kembali</a>
          <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </div>

      </form>
    </div>
  </div>
</div>

<?= $this->endSection() ?>

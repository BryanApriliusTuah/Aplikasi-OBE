<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container" style="max-width: 780px;">
  <h2 class="fw-bold text-center mb-4">Edit SubCPMK</h2>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger">
      <?php
        $err = session()->getFlashdata('error');
        if (is_array($err)) { foreach ($err as $e) echo esc($e).'<br>'; }
        else { echo esc($err); }
      ?>
    </div>
  <?php endif; ?>

  <div class="card shadow-sm">
    <div class="card-body">
      <form action="<?= base_url('admin/pemetaan-mk-cpmk-sub/update/' . $subcpmk['id']) ?>" method="post" autocomplete="off" onsubmit="return validateForm()">
        <?= csrf_field() ?>

        <!-- CPMK (readonly) -->
        <div class="mb-3">
          <label class="form-label">CPMK</label>
          <input type="text" class="form-control" value="<?= $subcpmk['kode_cpmk'] ?>" readonly>
        </div>

        <!-- Kode SubCPMK (readonly) -->
        <div class="mb-3">
          <label class="form-label">Kode SubCPMK</label>
          <input type="text" class="form-control" value="<?= $subcpmk['kode_sub_cpmk'] ?>" readonly>
        </div>

        <!-- Deskripsi -->
        <div class="mb-3">
          <label class="form-label">Deskripsi SubCPMK</label>
          <textarea name="deskripsi" class="form-control" required><?= esc($subcpmk['deskripsi']) ?></textarea>
        </div>

        <!-- MK Checkbox -->
        <div class="mb-3">
          <label class="form-label">Pilih Mata Kuliah</label>
          <?php if (empty($mkTerkait)): ?>
            <div class="alert alert-danger">Tidak ada MK yang terhubung ke CPMK ini.</div>
          <?php else: ?>
            <div class="border rounded p-2">
              <?php foreach ($mkTerkait as $mk): ?>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="mata_kuliah_id[]" value="<?= $mk['id'] ?>"
                    <?= in_array($mk['id'], $mkTerpilih) ? 'checked' : '' ?>>
                  <label class="form-check-label"><?= esc($mk['nama_mk']) ?></label>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>

        <!-- Tombol -->
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-save"></i> Simpan
        </button>
        <a href="<?= base_url('admin/pemetaan-mk-cpmk-sub') ?>" class="btn btn-secondary">Batal</a>
      </form>
    </div>
  </div>
</div>

<!-- Script Validasi Checkbox MK -->
<script>
  function validateForm() {
    const mkCheckboxes = document.querySelectorAll('input[name="mata_kuliah_id[]"]:checked');
    if (mkCheckboxes.length < 1) {
      const existingAlert = document.querySelector('.alert.alert-danger');
      if (!existingAlert) {
        const div = document.createElement('div');
        div.className = 'alert alert-danger mt-3';
        div.innerText = 'Minimal satu mata kuliah harus dipilih.';
        document.querySelector('form').prepend(div);
      }
      return false;
    }
    return true;
  }
</script>

<?= $this->endSection() ?>

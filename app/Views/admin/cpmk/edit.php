<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-0">
  <div class="col-lg-7 col-md-10 mx-auto mt-2">
    <div class="card shadow-sm">
      <div class="card-body">
        <h3 class="fw-bold mb-3">Edit CPMK</h3>
        <a href="<?= base_url('admin/cpmk') ?>" class="btn btn-outline-secondary mb-3">
          <i class="bi bi-arrow-left"></i> Kembali
        </a>

        <?php if(session()->getFlashdata('error')): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
              $errors = session()->getFlashdata('error');
              if (is_array($errors)) { foreach ($errors as $err) echo esc($err).'<br>'; }
              else { echo esc($errors); }
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        <?php endif; ?>

        <?php
          // Ambil hanya angka dari kode lama, contoh: "CPMK021" -> "021"
          $angka = preg_replace('/^\s*CPMK/i','', $row['kode_cpmk'] ?? '');
        ?>

        <form action="<?= base_url('admin/cpmk/update/'.$row['id']) ?>" method="post" autocomplete="off">
          <?= csrf_field() ?>

          <!-- Kode CPMK (prefix terkunci + angka saja) -->
          <div class="mb-3">
            <label class="form-label">Kode CPMK</label>
            <div class="input-group">
              <span class="input-group-text">CPMK</span>
              <input
                type="text"
                name="no_urut"
                id="no_urut"
                class="form-control"
                inputmode="numeric"
                pattern="\d+"
                value="<?= esc($angka) ?>"
                required
              >
            </div>
            <div class="form-text">Isi <b>angka saja</b> (boleh leading zero). Contoh hasil: <code>CPMK011</code>.</div>
          </div>

          <!-- Deskripsi -->
          <div class="mb-3">
            <label for="deskripsi" class="form-label">Deskripsi/Uraian CPMK</label>
            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" required><?= esc($row['deskripsi'] ?? '') ?></textarea>
          </div>

          <button type="submit" class="btn btn-primary">
            <i class="bi bi-save"></i> Update
          </button>
        </form>
      </div>
    </div>
  </div>
</div>

<script>
// Batasi input no_urut hanya digit
document.getElementById('no_urut').addEventListener('input', function(){
  this.value = this.value.replace(/\D+/g,'');
});
</script>

<?= $this->endSection() ?>

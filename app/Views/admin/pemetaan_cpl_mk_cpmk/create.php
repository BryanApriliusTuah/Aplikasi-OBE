<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container" style="max-width: 780px;">
  <h2 class="fw-bold text-center mb-4">Tambah Pemetaan CPL - MK - CPMK</h2>

  <div class="card shadow-sm">
    <div class="card-body">
      <form action="<?= base_url('admin/pemetaan-cpl-mk-cpmk/store') ?>" method="post" autocomplete="off">
        <?= csrf_field() ?>

        <?php if (session()->getFlashdata('error')): ?>
          <div class="alert alert-danger">
            <?php
              $err = session()->getFlashdata('error');
              if (is_array($err)) {
                foreach ($err as $e) echo esc($e).'<br>';
              } else {
                echo esc($err);
              }
            ?>
          </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
          <div class="alert alert-success">
            <?= esc(session()->getFlashdata('success')) ?>
          </div>
        <?php endif; ?>

        <!-- CPL -->
        <div class="mb-3">
          <label for="cplSelect" class="form-label">Pilih CPL</label>
          <select class="form-select" id="cplSelect" name="cpl_id" required>
            <option value="" disabled selected>-- Pilih CPL --</option>
            <?php foreach ($cpl_list as $cpl): ?>
              <option value="<?= esc($cpl['id']) ?>" data-kode="<?= esc($cpl['kode_cpl']) ?>">
                <?= esc($cpl['kode_cpl']) ?>
              </option>
            <?php endforeach ?>
          </select>
        </div>

        <!-- CPMK -->
        <div class="mb-3">
          <label for="cpmkSelect" class="form-label">Pilih CPMK</label>
          <select class="form-select" id="cpmkSelect" name="cpmk_id" required disabled>
            <option value="">-- Pilih CPMK --</option>
          </select>
        </div>

        <!-- Mata Kuliah (checkbox multi-pilih) -->
        <div class="mb-3">
          <label class="form-label">Pilih Mata Kuliah</label>
          <div id="mkCheckboxContainer" class="row row-cols-1 g-2">
            <!-- Checkbox akan diisi via JS -->
          </div>
        </div>

        <!-- Tombol -->
        <div class="text-end">
          <a href="<?= base_url('admin/pemetaan-cpl-mk-cpmk') ?>" class="btn btn-secondary">Kembali</a>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
document.getElementById('cplSelect').addEventListener('change', function () {
  const cplId   = this.value;
  const kodeCpl = this.options[this.selectedIndex].getAttribute('data-kode');

  const cpmkSelect = document.getElementById('cpmkSelect');
  const mkContainer = document.getElementById('mkCheckboxContainer');

  // Reset isi
  cpmkSelect.innerHTML = '<option value="">-- Pilih CPMK --</option>';
  cpmkSelect.disabled  = true;
  mkContainer.innerHTML = '';

  if (!kodeCpl || !cplId) return;

  // GET CPMK by kode_cpl
  fetch('<?= base_url('admin/pemetaan-cpl-mk-cpmk/get-cpmk/') ?>' + kodeCpl)
    .then(response => response.json())
    .then(data => {
      if (data.length > 0) {
        cpmkSelect.disabled = false;
        data.forEach(item => {
          const opt = document.createElement('option');
          opt.value = item.id;
          opt.textContent = item.kode_cpmk;
          cpmkSelect.appendChild(opt);
        });
      }
    });

  // GET Mata Kuliah by cpl_id
  fetch('<?= base_url('admin/pemetaan-cpl-mk-cpmk/get-mk/') ?>' + cplId)
    .then(response => response.json())
    .then(data => {
      if (data.length > 0) {
        data.forEach(item => {
          const col = document.createElement('div');
          col.className = 'col';

          const formCheck = document.createElement('div');
          formCheck.className = 'form-check';

          const input = document.createElement('input');
          input.className = 'form-check-input';
          input.type = 'checkbox';
          input.name = 'mata_kuliah_id[]';
          input.id = 'mk' + item.id;
          input.value = item.id;

          const label = document.createElement('label');
          label.className = 'form-check-label';
          label.htmlFor = input.id;
          label.textContent = item.nama_mk;

          formCheck.appendChild(input);
          formCheck.appendChild(label);
          col.appendChild(formCheck);
          mkContainer.appendChild(col);
        });
      }
    });
});
</script>

<?= $this->endSection() ?>

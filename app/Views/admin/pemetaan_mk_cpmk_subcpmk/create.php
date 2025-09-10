<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
<h2 class="fw-bold mb-4 text-center">Tambah Pemetaan MK – CPMK – SubCPMK</h2>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="<?= base_url('admin/pemetaan-mk-cpmk-sub/store') ?>" method="post" autocomplete="off" onsubmit="return validateForm()">
            <?= csrf_field() ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= esc(session()->getFlashdata('error')) ?>
                </div>
            <?php endif ?>

            <div class="mb-3">
                <label for="cpmk_id" class="form-label">Pilih CPMK</label>
                <select name="cpmk_id" id="cpmk_id" class="form-select" required onchange="updateKodePrefix(); loadMkByCpmk(); loadNextSuffix();">
                    <option value="">-- Pilih CPMK --</option>
                    <?php foreach ($cpmk as $item): ?>
                        <option value="<?= $item['id'] ?>" data-kode="<?= esc($item['kode_cpmk']) ?>">
                            <?= esc($item['kode_cpmk']) ?> - <?= esc($item['kode_cpl']) ?>
                        </option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Kode SubCPMK</label>
                <div class="input-group">
                    <span class="input-group-text" id="kodePrefix">SubCPMK</span>
                    <input type="text" name="kode_suffix" id="kode_suffix" class="form-control" placeholder="Pilih CPMK untuk nomor otomatis..." required readonly>
                </div>
                <div class="form-text">Kode otomatis: <span id="previewKode">SubCPMK</span></div>
            </div>

            <div class="mb-3">
                <label for="deskripsi" class="form-label">Deskripsi SubCPMK</label>
                <textarea name="deskripsi" id="deskripsi" class="form-control" rows="3" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Pilih Mata Kuliah</label>
                <div id="mata_kuliah_wrapper" class="border rounded p-2" style="min-height: 50px;">
                    <p class="text-muted mb-0">Silakan pilih CPMK terlebih dahulu.</p>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Simpan
            </button>
            <a href="<?= base_url('admin/pemetaan-mk-cpmk-sub') ?>" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</div>
<?= $this->endSection() ?>


<?= $this->section('js') ?>
<script>
    function loadNextSuffix() {
        const cpmkSelect = document.getElementById('cpmk_id');
        const suffixInput = document.getElementById('kode_suffix');
        const cpmkId = cpmkSelect.value;
        const baseUrl = '<?= rtrim(base_url(), '/') ?>';

        if (cpmkId) {
            suffixInput.value = 'Memuat...';
            fetch(`${baseUrl}/admin/pemetaan-mk-cpmk-sub/get-next-suffix/${cpmkId}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.next_suffix) {
                        suffixInput.value = data.next_suffix;
                        updateKodePrefix();
                    } else {
                        suffixInput.value = 'Error';
                    }
                })
                .catch(error => {
                    console.error('Fetch error:', error);
                    suffixInput.value = 'Gagal memuat';
                });
        } else {
            suffixInput.value = '';
            suffixInput.placeholder = 'Pilih CPMK untuk nomor otomatis...';
            updateKodePrefix();
        }
    }

    function updateKodePrefix() {
        const select = document.getElementById('cpmk_id');
        const selected = select.options[select.selectedIndex];
        const kodeCpmk = selected ? selected.getAttribute('data-kode') : null;
        const suffix = document.getElementById('kode_suffix').value;

        if (kodeCpmk) {
            const no = kodeCpmk.replace('CPMK', '');
            const fullKode = 'SubCPMK' + no;
            document.getElementById('kodePrefix').textContent = fullKode;
            document.getElementById('previewKode').textContent = fullKode + suffix;
        } else {
            document.getElementById('kodePrefix').textContent = 'SubCPMK';
            document.getElementById('previewKode').textContent = 'SubCPMK';
        }
    }

    document.getElementById('kode_suffix').addEventListener('input', updateKodePrefix);

    function loadMkByCpmk() {
        const cpmkId = document.getElementById('cpmk_id').value;
        const wrapper = document.getElementById('mata_kuliah_wrapper');
        const baseUrl = '<?= rtrim(base_url(), '/') ?>';

        if (!cpmkId) {
            wrapper.innerHTML = '<p class="text-muted mb-0">Silakan pilih CPMK terlebih dahulu.</p>';
            return;
        }
        
        fetch(`${baseUrl}/admin/pemetaan-mk-cpmk-subcpmk/get-mk/${cpmkId}`)
            .then(res => res.json())
            .then(data => {
                if (data.length === 0) {
                    wrapper.innerHTML = '<p class="text-danger mb-0">Tidak ada MK yang terhubung dengan CPMK ini.</p>';
                    return;
                }
                let html = '';
                data.forEach(mk => {
                    html += `
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="mata_kuliah_id[]" value="${mk.id}" id="mk_${mk.id}">
                            <label class="form-check-label" for="mk_${mk.id}">${mk.nama_mk}</label>
                        </div>
                    `;
                });
                wrapper.innerHTML = html;
            })
            .catch(() => {
                wrapper.innerHTML = '<p class="text-danger mb-0">Gagal mengambil data MK.</p>';
            });
    }

    function validateForm() {
        const cpmkId = document.getElementById('cpmk_id').value;
        if (!cpmkId) {
            alert('Silakan pilih CPMK terlebih dahulu.');
            return false;
        }

        const mkCheckboxes = document.querySelectorAll('input[name="mata_kuliah_id[]"]:checked');
        const errorEl = document.getElementById('mk-error');
        if (errorEl) errorEl.remove();

        if (mkCheckboxes.length < 1) {
            const wrapper = document.getElementById('mata_kuliah_wrapper');
            const errorMsg = document.createElement('div');
            errorMsg.id = 'mk-error';
            errorMsg.className = 'alert alert-danger mt-2';
            errorMsg.textContent = 'Minimal satu mata kuliah harus dipilih.';
            wrapper.appendChild(errorMsg);
            return false;
        }
        return true;
    }
</script>
<?= $this->endSection() ?>
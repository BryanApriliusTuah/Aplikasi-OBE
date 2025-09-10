<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>

<style>
    .checkbox-container {
        max-height: 200px; 
        overflow-y: auto; 
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 10px;
    }
</style>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
<?php endif ?>

<h2>Tambah Rencana Mingguan</h2>
<form method="post" action="<?= base_url('rps/mingguan-store/' . $rps_id) ?>">
    <?= csrf_field() ?>
    <input type="hidden" id="total-bobot-exist" value="<?= $totalBobotExist ?>">

    <div class="mb-2">
        <label>Minggu ke-</label>
        <input type="number" name="minggu" class="form-control" value="<?= old('minggu') ?>" required>
    </div>
    <div class="mb-2">
        <label>CPL</label>
        <select name="cpl_id" id="select-cpl" class="form-control" required>
            <option value="">--Pilih--</option>
            <?php foreach ($cpl as $c) : ?>
                <option value="<?= $c['id'] ?>" <?= old('cpl_id') == $c['id'] ? 'selected' : '' ?>><?= esc($c['kode_cpl']) ?></option>
            <?php endforeach ?>
        </select>
    </div>
    <div class="mb-2">
        <label>CPMK</label>
        <select name="cpmk_id" id="select-cpmk" class="form-select" required>
            <option value="">--Pilih CPL dahulu--</option>
        </select>
    </div>
    <div class="mb-2">
        <label>SubCPMK</label>
        <select name="sub_cpmk_id" id="select-subcpmk" class="form-select" required>
            <option value="">--Pilih CPMK dahulu--</option>
        </select>
    </div>
    <div class="mb-2">
        <label>Indikator</label>
        <div id="indikator-wrapper">
            <div class="input-group mb-2">
                <input type="text" name="indikator[]" class="form-control" required>
                <button type="button" class="btn btn-outline-danger btn-hapus-indikator" style="display:none;">Hapus</button>
            </div>
        </div>
        <button type="button" id="btn-tambah-indikator" class="btn btn-sm btn-outline-success">Tambah Indikator</button>
    </div>

    <div class="mb-3">
        <label class="form-label">Kriteria Penilaian (Opsional)</label>
        <div class="checkbox-container">
            <div class="row">
                <?php foreach ($kriteria_options as $option) : ?>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="kriteria_penilaian[]" value="<?= $option ?>" id="kriteria_<?= preg_replace('/[^a-zA-Z0-9]/', '_', $option) ?>">
                            <label class="form-check-label" for="kriteria_<?= preg_replace('/[^a-zA-Z0-9]/', '_', $option) ?>"><?= $option ?></label>
                        </div>
                    </div>
                <?php endforeach; ?>
                 <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="kriteria_penilaian[]" value="Lainnya" id="kriteria_lainnya_check">
                        <label class="form-check-label" for="kriteria_lainnya_check">Lainnya</label>
                    </div>
                </div>
            </div>
        </div>
        <input type="text" name="kriteria_penilaian_lainnya" id="kriteria_lainnya_input" class="form-control mt-2" style="display:none;" placeholder="Isi kriteria lainnya...">
    </div>

    <?php $tahap_options = ['Perkuliahan', 'Tengah Semester', 'Akhir Semester']; ?>
    <div class="mb-3">
        <label class="form-label">Tahap Penilaian</label><br>
        <?php foreach ($tahap_options as $option) : ?>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" name="tahap_penilaian[]" value="<?= $option ?>" id="tahap_<?= str_replace(' ', '_', $option) ?>" onclick="cekTahapRequired(this)">
                <label class="form-check-label" for="tahap_<?= str_replace(' ', '_', $option) ?>"><?= $option ?></label>
            </div>
        <?php endforeach ?>
        <div id="tahap-warning" class="form-text text-danger" style="display:none;">Pilih minimal 1 tahap penilaian.</div>
    </div>

    <div class="mb-2">
        <label>Teknik Penilaian (Opsional)</label><br>
        <?php
        $teknikList = [
            'partisipasi'   => 'Partisipasi (Kehadiran / Quiz)', 'observasi'     => 'Observasi (Praktek / Tugas)', 'unjuk_kerja'   => 'Unjuk Kerja (Presentasi)',
            'proyek'        => 'Proyek (Case Method/Project Based)', 'tes_tulis_uts' => 'Tes Tulis (UTS)', 'tes_tulis_uas' => 'Tes Tulis (UAS)', 'tes_lisan'     => 'Tes Lisan (Tugas Kelompok)'
        ];
        ?>
        <div id="teknik-penilaian-wrapper">
            <?php foreach ($teknikList as $key => $label) : ?>
                <div class="form-check form-check-inline" style="margin-bottom:8px;vertical-align:top;">
                    <input class="form-check-input teknik-checkbox" type="checkbox" id="teknik_<?= $key ?>" value="<?= $key ?>" name="teknik_penilaian[]">
                    <label class="form-check-label" for="teknik_<?= $key ?>"><?= $label ?></label>
                    <div class="bobot-input-wrap" style="display:none; margin-top:4px;">
                        <input type="number" min="0" max="100" step="1" class="form-control form-control-sm bobot-input" placeholder="Bobot (%)" name="bobot_teknik[<?= $key ?>]" style="width:110px;">
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="mb-2">
        <label>Materi Pembelajaran</label>
        <input type="text" name="materi_pembelajaran" class="form-control" value="<?= old('materi_pembelajaran') ?>">
    </div>

    <div class="mb-3">
        <label class="form-label">Instrumen Penilaian (Opsional)</label>
        <div class="checkbox-container">
            <div class="row">
                <?php foreach ($instrumen_options as $option) : ?>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="instrumen[]" value="<?= $option ?>" id="instrumen_<?= str_replace(' ', '_', $option) ?>">
                            <label class="form-check-label" for="instrumen_<?= str_replace(' ', '_', $option) ?>"><?= $option ?></label>
                        </div>
                    </div>
                <?php endforeach; ?>
                 <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="instrumen[]" value="Lainnya" id="instrumen_lainnya_check">
                        <label class="form-check-label" for="instrumen_lainnya_check">Lainnya</label>
                    </div>
                </div>
            </div>
        </div>
        <input type="text" name="instrumen_lainnya" id="instrumen_lainnya_input" class="form-control mt-2" style="display:none;" placeholder="Isi instrumen lainnya...">
    </div>

    <div class="mb-3">
        <label class="form-label">Metode Pembelajaran</label>
        <div class="checkbox-container">
            <div class="row">
                <?php foreach ($metode_options as $option) : ?>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="metode[]" value="<?= $option ?>" id="metode_<?= preg_replace('/[^a-zA-Z0-9]/', '_', $option) ?>">
                            <label class="form-check-label" for="metode_<?= preg_replace('/[^a-zA-Z0-9]/', '_', $option) ?>"><?= $option ?></label>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="metode[]" value="Lainnya" id="metode_lainnya_check">
                        <label class="form-check-label" for="metode_lainnya_check">Lainnya</label>
                    </div>
                </div>
            </div>
        </div>
        <input type="text" name="metode_lainnya" id="metode_lainnya_input" class="form-control mt-2" style="display:none;" placeholder="Isi metode lainnya...">
    </div>

    <div class="mb-2" id="total-bobot-wrapper" style="display:none;">
        <label>Total Bobot Teknik Penilaian Minggu Ini (%)</label>
        <input type="number" id="total-bobot-minggu" class="form-control" value="0" readonly>
    </div>
    <div class="alert alert-info mt-3">
        Total Bobot Seluruh Minggu di RPS Ini akan menjadi: <strong id="live-total-bobot-rps"><?= $totalBobotExist ?>%</strong>
    </div>
    <button class="btn btn-primary">Simpan</button>
    <a href="<?= base_url('rps/mingguan/' . $rps_id) ?>" class="btn btn-secondary">Kembali</a>
</form>

<script>
    function cekTahapRequired(cb) {
        var checkboxes = document.querySelectorAll('input[name="tahap_penilaian[]"]');
        var adaYangChecked = Array.from(checkboxes).some(chk => chk.checked);
        checkboxes.forEach(function(chk) {
            chk.required = !adaYangChecked;
        });
        document.getElementById('tahap-warning').style.display = adaYangChecked ? 'none' : 'block';
    }

    document.addEventListener('DOMContentLoaded', function() {
        function setupLainnyaCheckbox(checkId, inputId) {
            const checkbox = document.getElementById(checkId);
            const input = document.getElementById(inputId);
            if (checkbox) {
                checkbox.addEventListener('change', function() {
                    input.style.display = this.checked ? 'block' : 'none';
                    if (!this.checked) {
                        input.value = '';
                    }
                });
            }
        }
        setupLainnyaCheckbox('kriteria_lainnya_check', 'kriteria_lainnya_input');
        setupLainnyaCheckbox('instrumen_lainnya_check', 'instrumen_lainnya_input');
        setupLainnyaCheckbox('metode_lainnya_check', 'metode_lainnya_input');
        const indikatorWrapper = document.getElementById('indikator-wrapper');
        document.getElementById('btn-tambah-indikator').addEventListener('click', function() {
            const newIndikator = indikatorWrapper.children[0].cloneNode(true);
            const input = newIndikator.querySelector('input');
            input.value = '';
            input.required = true;
            const deleteButton = newIndikator.querySelector('.btn-hapus-indikator');
            deleteButton.style.display = 'inline-block';
            indikatorWrapper.appendChild(newIndikator);
        });
        indikatorWrapper.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-hapus-indikator')) {
                e.target.parentElement.remove();
            }
        });
        document.querySelectorAll('input[name="tahap_penilaian[]"]').forEach(function(chk) {
            chk.addEventListener('change', function() {
                cekTahapRequired(this)
            });
        });
        cekTahapRequired();
        const totalBobotExist = parseInt(document.getElementById('total-bobot-exist').value) || 0;
        const liveTotalRpsElem = document.getElementById('live-total-bobot-rps');
        const totalBobotWrapper = document.getElementById('total-bobot-wrapper');
        const totalBobotMingguElem = document.getElementById('total-bobot-minggu');
        const teknikCheckboxes = document.querySelectorAll('.teknik-checkbox');
        function hitungTotalBobot() {
            let totalMingguIni = 0;
            document.querySelectorAll('.bobot-input').forEach(function(input) {
                if (input.closest('.form-check').querySelector('.teknik-checkbox').checked) {
                    totalMingguIni += parseInt(input.value) || 0;
                }
            });
            totalBobotMingguElem.value = totalMingguIni;
            liveTotalRpsElem.textContent = (totalBobotExist + totalMingguIni) + '%';
        }
        function checkTeknikPenilaian() {
            const adaYangChecked = Array.from(teknikCheckboxes).some(cb => cb.checked);
            totalBobotWrapper.style.display = adaYangChecked ? 'block' : 'none';
            if (!adaYangChecked) {
                document.querySelectorAll('.bobot-input').forEach(input => input.value = '');
                hitungTotalBobot();
            }
        }
        teknikCheckboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                let parent = this.closest('.form-check');
                let bobotWrap = parent.querySelector('.bobot-input-wrap');
                bobotWrap.style.display = this.checked ? 'block' : 'none';
                if (!this.checked) {
                    parent.querySelector('.bobot-input').value = '';
                }
                checkTeknikPenilaian();
                hitungTotalBobot();
            });
        });
        document.querySelectorAll('.bobot-input').forEach(input => input.addEventListener('input', hitungTotalBobot));
        checkTeknikPenilaian();

        const mkId = '<?= $mk_id ?? '' ?>';
        const cplDropdown = document.getElementById('select-cpl');
        const cpmkDropdown = document.getElementById('select-cpmk');
        const subcpmkDropdown = document.getElementById('select-subcpmk');

        function populateDropdown(dropdown, options, placeholder) {
            dropdown.innerHTML = `<option value="">-- Pilih ${placeholder} --</option>`;
            if (Array.isArray(options) && options.length > 0) {
                options.forEach(option => {
                    const opt = document.createElement('option');
                    opt.value = option.id;
                    const kode = option.kode_cpl || option.kode_cpmk || option.kode_sub_cpmk || '';
                    opt.textContent = kode;
                    dropdown.appendChild(opt);
                });
            }
        }

        cplDropdown.addEventListener('change', async function() {
            const cplId = this.value;
            populateDropdown(cpmkDropdown, [], 'CPMK');
            populateDropdown(subcpmkDropdown, [], 'SubCPMK');
            
            if (cplId && mkId) {
                cpmkDropdown.innerHTML = '<option value="">Memuat...</option>';
                try {
                    const response = await fetch(`/rps/get-cpmk/${mkId}/${cplId}`);
                    if (!response.ok) throw new Error('Gagal mengambil data CPMK');
                    const cpmkOptions = await response.json();
                    populateDropdown(cpmkDropdown, cpmkOptions, 'CPMK');
                } catch (error) {
                    console.error('Error fetching CPMK:', error);
                    cpmkDropdown.innerHTML = '<option value="">-- Gagal memuat data --</option>';
                }
            }
        });

        cpmkDropdown.addEventListener('change', async function() {
            const cpmkId = this.value;
            populateDropdown(subcpmkDropdown, [], 'SubCPMK');

            if (cpmkId && mkId) {
                subcpmkDropdown.innerHTML = '<option value="">Memuat...</option>';
                try {
                    const response = await fetch(`/rps/get-subcpmk/${mkId}/${cpmkId}`);
                    if (!response.ok) throw new Error('Gagal mengambil data SubCPMK');
                    const subcpmkOptions = await response.json();
                    populateDropdown(subcpmkDropdown, subcpmkOptions, 'SubCPMK');
                } catch (error) {
                    console.error('Error fetching SubCPMK:', error);
                    subcpmkDropdown.innerHTML = '<option value="">-- Gagal memuat data --</option>';
                }
            }
        });
    });
</script>
<?= $this->endSection() ?>
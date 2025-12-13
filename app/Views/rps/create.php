<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<h2>Tambah RPS</h2>
<form method="post" action="<?= base_url('rps/store') ?>">
    <div class="mb-2">
        <label>Mata Kuliah</label>
        <select name="mata_kuliah_id" class="form-control" required>
            <option value="">--Pilih--</option>
            <?php foreach($mata_kuliah as $mk): ?>
                <option value="<?= $mk['id'] ?>"><?= esc($mk['nama_mk']) ?></option>
            <?php endforeach ?>
        </select>
    </div>
    <div id="pengampu-wrapper" class="mb-2">
        <label>Dosen Pengampu <small>(bisa lebih dari satu)</small></label>
        <div class="row pengampu-row mb-1">
            <div class="col-10">
                <select class="form-control" name="dosen_pengampu_ids[]" required>
                    <option value="">--Pilih Dosen--</option>
                    <?php foreach($dosen as $d): ?>
                        <option value="<?= $d['id'] ?>"><?= esc($d['nama_lengkap']) ?> (<?= esc($d['nip']) ?>)</option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-success btn-add-pengampu">+</button>
            </div>
        </div>
    </div>
    <div class="mb-2">
        <label>Koordinator Mata Kuliah</label>
        <select class="form-control" name="koordinator_id" id="koordinator_id" required>
            <option value="">--Pilih Koordinator--</option>
            <!-- Diisi otomatis via JS -->
        </select>
    </div>
    <div class="mb-2">
        <label>Tahun Ajaran</label>
        <input type="text" name="tahun_ajaran" class="form-control" placeholder="misal: 2024/2025" required>
    </div>
    <div class="mb-2">
        <label>Tanggal Penyusunan</label>
        <input type="date" name="tgl_penyusunan" class="form-control" required>
    </div>
    <div class="mb-2">
        <label>Status</label>
        <select name="status" class="form-control" required>
            <option value="draft">Draft</option>
            <option value="final">Final</option>
        </select>
    </div>
    <div class="mb-2">
        <label>Catatan</label>
        <textarea name="catatan" class="form-control"></textarea>
    </div>
    <button class="btn btn-primary">Simpan</button>
    <a href="<?= base_url('rps') ?>" class="btn btn-secondary">Kembali</a>
</form>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
        function updatePengampuDropdown() {
        // Ambil semua dosen yang sudah dipilih
        let selected = [];
        $('#pengampu-wrapper select[name="dosen_pengampu_ids[]"]').each(function() {
            let val = $(this).val();
            if (val) selected.push(val);
        });

        $('#pengampu-wrapper select[name="dosen_pengampu_ids[]"]').each(function() {
            let currentVal = $(this).val();
            $(this).find('option').each(function() {
                // disable kalau sudah dipilih
                if ($(this).val() !== "" && $(this).val() !== currentVal && selected.includes($(this).val())) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            });
        });
        updateKoordinatorDropdown(); // tetap update koordinator juga
    }

    function updateKoordinatorDropdown() {
        let values = [];
        $('#pengampu-wrapper select[name="dosen_pengampu_ids[]"]').each(function() {
            let val = $(this).val();
            let text = $(this).find('option:selected').text();
            if (val) values.push({val: val, text: text});
        });
        let $koor = $('#koordinator_id');
        $koor.empty();
        $koor.append('<option value="">--Pilih Koordinator--</option>');
        values.forEach(function(row) {
            $koor.append('<option value="'+row.val+'">'+row.text+'</option>');
        });
    }

    // Tambahkan updatePengampuDropdown ke semua event
    $(document).on('change', 'select[name="dosen_pengampu_ids[]"]', updatePengampuDropdown);

    $(document).on('click', '.btn-add-pengampu', function() {
        let row = $(this).closest('.pengampu-row').clone();
        row.find('select').val('');
        row.find('.btn-add-pengampu').removeClass('btn-success btn-add-pengampu').addClass('btn-danger btn-remove-pengampu').text('-');
        $('#pengampu-wrapper').append(row);
        updatePengampuDropdown();
    });
    $(document).on('click', '.btn-remove-pengampu', function() {
        $(this).closest('.pengampu-row').remove();
        updatePengampuDropdown();
    });

    // Inisialisasi saat pertama load
    $(function() {
        updatePengampuDropdown();
    });
</script>
<?= $this->endSection() ?>

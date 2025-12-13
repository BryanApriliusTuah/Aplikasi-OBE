<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<h2>Edit RPS</h2>
<form method="post" action="<?= base_url('rps/update/'.$rps['id']) ?>">
    <?= csrf_field() ?>

    <div class="mb-2">
        <label>Mata Kuliah</label>
        <select name="mata_kuliah_id" class="form-control" required>
            <option value="">--Pilih--</option>
            <?php foreach($mata_kuliah as $mk): ?>
                <option value="<?= $mk['id'] ?>" <?= $mk['id']==$rps['mata_kuliah_id']?'selected':'' ?>><?= esc($mk['nama_mk']) ?></option>
            <?php endforeach ?>
        </select>
    </div>

    <div id="pengampu-wrapper" class="mb-2">
        <label>Dosen Pengampu <small>(bisa lebih dari satu)</small></label>
        <?php
        $pengampu_ids = isset($pengampu_ids) ? $pengampu_ids : [];
        if (empty($pengampu_ids)) $pengampu_ids = ['']; 
        foreach ($pengampu_ids as $i => $pid): ?>
        <div class="row pengampu-row mb-1">
            <div class="col-10">
                <select class="form-control" name="dosen_pengampu_ids[]" required>
                    <option value="">--Pilih Dosen--</option>
                    <?php foreach($dosen as $d): ?>
                        <option value="<?= $d['id'] ?>" <?= $pid == $d['id'] ? 'selected' : '' ?>>
                            <?= esc($d['nama_lengkap']) ?> (<?= esc($d['nip']) ?>)
                        </option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="col-2">
                <?php if ($i == 0): ?>
                    <button type="button" class="btn btn-success btn-add-pengampu">+</button>
                <?php else: ?>
                    <button type="button" class="btn btn-danger btn-remove-pengampu">-</button>
                <?php endif ?>
            </div>
        </div>
        <?php endforeach ?>
    </div>
    
    <div class="mb-2">
        <label>Koordinator Mata Kuliah</label>
        <select class="form-control" name="koordinator_id" id="koordinator_id" required>
            <option value="">--Pilih Koordinator--</option>
            <?php
            foreach ($dosen as $d):
                if (in_array($d['id'], $pengampu_ids)): ?>
                    <option value="<?= $d['id'] ?>" <?= (isset($koordinator_id) && $koordinator_id == $d['id']) ? 'selected' : '' ?>>
                        <?= esc($d['nama_lengkap']) ?> (<?= esc($d['nip']) ?>)
                    </option>
            <?php endif; endforeach; ?>
        </select>
    </div>

    <div class="mb-2">
        <label>Tahun Ajaran</label>
        <input type="text" name="tahun_ajaran" class="form-control" value="<?= esc($rps['tahun_ajaran']) ?>" required>
    </div>
    <div class="mb-2">
        <label>Tanggal Penyusunan</label>
        <input type="date" name="tgl_penyusunan" class="form-control" value="<?= esc($rps['tgl_penyusunan']) ?>" required>
    </div>
    <div class="mb-2">
        <label>Status</label>
        <select name="status" class="form-control" required>
            <option value="draft" <?= $rps['status']=='draft'?'selected':'' ?>>Draft</option>
            <option value="final" <?= $rps['status']=='final'?'selected':'' ?>>Final</option>
        </select>
    </div>
    <div class="mb-2">
        <label>Catatan</label>
        <textarea name="catatan" class="form-control"><?= esc($rps['catatan']) ?></textarea>
    </div>
    <button class="btn btn-primary">Update</button>
    <a href="<?= base_url('rps') ?>" class="btn btn-secondary">Kembali</a>
</form>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
    function updatePengampuDropdown() {
        let selected = [];
        $('#pengampu-wrapper select[name="dosen_pengampu_ids[]"]').each(function() {
            let val = $(this).val();
            if (val) selected.push(val);
        });

        $('#pengampu-wrapper select[name="dosen_pengampu_ids[]"]').each(function() {
            let currentVal = $(this).val();
            $(this).find('option').each(function() {
                if ($(this).val() !== "" && $(this).val() !== currentVal && selected.includes($(this).val())) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            });
        });
        updateKoordinatorDropdown();
    }

    function updateKoordinatorDropdown() {
        let values = [];
        $('#pengampu-wrapper select[name="dosen_pengampu_ids[]"]').each(function() {
            let val = $(this).val();
            let text = $(this).find('option:selected').text();
            if (val) values.push({val: val, text: text});
        });
        let $koor = $('#koordinator_id');
        let currentKoor = $koor.val();
        $koor.empty();
        $koor.append('<option value="">--Pilih Koordinator--</option>');
        values.forEach(function(row) {
            $koor.append('<option value="'+row.val+'"'+(currentKoor==row.val ? ' selected' : '')+'>'+row.text+'</option>');
        });
        if (currentKoor && !$koor.find('option[value="'+currentKoor+'"]').length) {
            $koor.val('');
        }
    }

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
    $(function() {
        updatePengampuDropdown();
    });
</script>
<?= $this->endSection() ?>
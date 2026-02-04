<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container-fluid px-0">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<h2 class="fw-bold mb-0 w-100 text-center">Tambah Pemetaan CPL ke Mata Kuliah</h2>
	</div>
	<div class="card shadow-sm mx-auto" style="max-width:600px;">
		<div class="card-body">
			<form method="post" action="<?= base_url('admin/cpl-mk/store') ?>">
				<?php if (session()->getFlashdata('error')): ?>
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<?= session()->getFlashdata('error'); ?>
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
				<?php endif ?>
				<div class="mb-3">
					<label class="form-label">Pilih Mata Kuliah <span class="text-muted">(bisa pilih lebih dari satu)</span></label>
					<input type="text" id="searchMk" class="form-control mb-2" placeholder="Cari mata kuliah...">
					<div id="mkCheckboxList" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.5rem;">
						<?php foreach ($mk as $m): ?>
							<div class="form-check mk-item">
								<input class="form-check-input" type="checkbox" name="mata_kuliah_id[]" value="<?= $m['id'] ?>" id="mk_<?= $m['id'] ?>"
									<?= (is_array(old('mata_kuliah_id')) && in_array($m['id'], old('mata_kuliah_id', []))) ? 'checked' : '' ?>>
								<label class="form-check-label" for="mk_<?= $m['id'] ?>">
									<?= esc($m['kode_mk']) ?> - <?= esc($m['nama_mk']) ?>
								</label>
							</div>
						<?php endforeach ?>
					</div>
					<small class="text-muted">*Centang mata kuliah yang ingin dipetakan</small>
				</div>
				<div class="mb-3">
					<label class="form-label">Pilih CPL <span class="text-muted">(bisa pilih lebih dari satu)</span></label>
					<input type="text" id="searchCpl" class="form-control mb-2" placeholder="Cari CPL...">
					<div id="cplCheckboxList" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.5rem;">
						<?php foreach ($cpl as $c): ?>
							<div class="form-check cpl-item">
								<input class="form-check-input" type="checkbox" name="cpl_id[]" value="<?= $c['id'] ?>" id="cpl_<?= $c['id'] ?>"
									<?= (is_array(old('cpl_id')) && in_array($c['id'], old('cpl_id', []))) ? 'checked' : '' ?>>
								<label class="form-check-label" for="cpl_<?= $c['id'] ?>">
									<?= esc($c['kode_cpl']) ?>
								</label>
							</div>
						<?php endforeach ?>
					</div>
					<small class="text-muted">*Centang CPL yang ingin dipetakan</small>
				</div>
				<div class="mt-3 d-flex justify-content-end">
					<a href="<?= base_url('admin/cpl-mk') ?>" class="btn btn-secondary me-2">Kembali</a>
					<button type="submit" class="btn btn-primary">Simpan Pemetaan</button>
				</div>
			</form>
		</div>
	</div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	$('#searchMk').on('keyup', function() {
		var query = $(this).val().toLowerCase();
		$('.mk-item').each(function() {
			var text = $(this).find('label').text().toLowerCase();
			$(this).toggle(text.indexOf(query) > -1);
		});
	});

	$('#searchCpl').on('keyup', function() {
		var query = $(this).val().toLowerCase();
		$('.cpl-item').each(function() {
			var text = $(this).find('label').text().toLowerCase();
			$(this).toggle(text.indexOf(query) > -1);
		});
	});

	$('form').on('submit', function(e) {
		if ($('input[name="mata_kuliah_id[]"]:checked').length === 0) {
			e.preventDefault();
			alert('Pilih minimal satu mata kuliah.');
			return;
		}
		if ($('input[name="cpl_id[]"]:checked').length === 0) {
			e.preventDefault();
			alert('Pilih minimal satu CPL.');
		}
	});
</script>
<?= $this->endSection() ?>
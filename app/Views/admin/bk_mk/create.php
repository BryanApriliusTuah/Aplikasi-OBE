<?= $this->extend('layouts/admin_layout') ?>
<?= $this->section('content') ?>

<div class="container" style="max-width: 650px">
	<h2 class="fw-bold text-center mb-4">Form Pemetaan BK ke Mata Kuliah</h2>
	<div class="card shadow-sm">
		<div class="card-body">
			<form method="post" action="<?= base_url('admin/bkmk/store') ?>">
				<?php if (session()->getFlashdata('error')): ?>
					<div class="alert alert-danger alert-dismissible fade show" role="alert">
						<?= session()->getFlashdata('error'); ?>
						<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
					</div>
				<?php endif ?>
				<div class="mb-3">
					<label class="form-label">Pilih Bahan Kajian</label>
					<select name="bahan_kajian_id" class="form-select" required>
						<option value="">-- Pilih BK --</option>
						<?php foreach ($bk as $b): ?>
							<option value="<?= $b['id'] ?>" <?= ($selectedBkId == $b['id']) ? 'selected' : '' ?>>
								<?= $b['kode_bk'] ?> - <?= $b['nama_bk'] ?>
							</option>
						<?php endforeach ?>
					</select>
				</div>
				<div class="mb-3">
					<label class="form-label">Pilih Mata Kuliah</label>
					<input type="text" id="searchMk" class="form-control mb-2" placeholder="Cari mata kuliah...">
					<div id="mkCheckboxList" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.5rem;">
						<?php foreach ($mk as $m): ?>
							<div class="form-check mk-item">
								<input class="form-check-input" type="checkbox" name="mata_kuliah_id[]" value="<?= $m['id'] ?>" id="mk_<?= $m['id'] ?>"
									<?= (is_array($selectedMkId) && in_array($m['id'], $selectedMkId)) ? 'checked' : '' ?>>
								<label class="form-check-label" for="mk_<?= $m['id'] ?>">
									<?= $m['kode_mk'] ?> - <?= $m['nama_mk'] ?>
								</label>
							</div>
						<?php endforeach ?>
					</div>
					<small class="text-muted">*Centang mata kuliah yang ingin dipetakan</small>
				</div>
				<div class="mt-3 d-flex justify-content-end">
					<a href="<?= base_url('admin/bkmk') ?>" class="btn btn-secondary me-2">Kembali</a>
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

	$('form').on('submit', function(e) {
		if ($('input[name="mata_kuliah_id[]"]:checked').length === 0) {
			e.preventDefault();
			alert('Pilih minimal satu mata kuliah.');
		}
	});
</script>
<?= $this->endSection() ?>
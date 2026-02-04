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
					<input type="text" id="searchMk" class="form-control mb-2" placeholder="Cari mata kuliah...">
					<div id="mkCheckboxContainer" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.5rem;">
						<?php foreach ($all_mk as $mk): ?>
							<div class="form-check mk-item">
								<input class="form-check-input" type="checkbox" name="mata_kuliah_id[]" value="<?= $mk['id'] ?>" id="mk<?= $mk['id'] ?>"
									<?= in_array($mk['id'], $selected_mk_ids) ? 'checked' : '' ?>>
								<label class="form-check-label" for="mk<?= $mk['id'] ?>">
									<?= esc($mk['kode_mk']) ?> - <?= esc($mk['nama_mk']) ?>
								</label>
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

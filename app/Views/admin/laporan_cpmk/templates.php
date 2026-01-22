<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h4 class="mb-1">Template Analisis CPMK</h4>
			<p class="text-muted mb-0">Kelola template untuk analisis otomatis CPMK</p>
		</div>
		<a href="<?= base_url('admin/laporan-cpmk') ?>" class="btn btn-outline-secondary">
			<i class="bi bi-arrow-left"></i> Kembali
		</a>
	</div>

	<!-- Placeholder Guide -->
	<div class="card mb-4">
		<div class="card-header bg-info text-white">
			<h6 class="mb-0"><i class="bi bi-info-circle"></i> Panduan Placeholder</h6>
		</div>
		<div class="card-body">
			<p class="mb-2">Gunakan placeholder berikut dalam template Anda. Placeholder akan diganti dengan data aktual saat analisis dibuat:</p>
			<div class="row">
				<?php foreach ($placeholders as $placeholder => $description): ?>
					<div class="col-md-6 mb-2">
						<code><?= esc($placeholder) ?></code> - <?= esc($description) ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	</div>

	<!-- Templates -->
	<div class="accordion" id="templatesAccordion">
		<?php foreach ($templates as $index => $template): ?>
			<div class="card mb-3">
				<div class="card-header" id="heading<?= $index ?>">
					<h5 class="mb-0">
						<button class="btn btn-link text-decoration-none w-100 text-start collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>" aria-expanded="false" aria-controls="collapse<?= $index ?>">
							<i class="bi bi-file-text me-2"></i><?= esc($template['option_label']) ?>
							<i class="bi bi-chevron-down float-end"></i>
						</button>
					</h5>
				</div>

				<div id="collapse<?= $index ?>" class="collapse" aria-labelledby="heading<?= $index ?>" data-bs-parent="#templatesAccordion">
					<div class="card-body">
						<form id="form-<?= esc($template['option_key']) ?>" onsubmit="saveTemplate(event, '<?= esc($template['option_key']) ?>')">
							<input type="hidden" name="option_key" value="<?= esc($template['option_key']) ?>">

							<div class="mb-3">
								<label class="form-label fw-bold">
									Template untuk CPMK Tercapai Semua
									<small class="text-muted">(digunakan ketika semua CPMK mencapai target)</small>
								</label>
								<textarea
									class="form-control font-monospace"
									name="template_tercapai"
									rows="4"
									placeholder="Masukkan template untuk kondisi tercapai..."
								><?= esc($template['template_tercapai'] ?? '') ?></textarea>
								<small class="text-muted">Contoh: Dari {total_cpmk} CPMK yang ditetapkan, seluruh CPMK telah tercapai dengan baik.</small>
							</div>

							<div class="mb-3">
								<label class="form-label fw-bold">
									Template untuk CPMK Tidak Tercapai
									<small class="text-muted">(digunakan ketika ada CPMK yang belum mencapai target)</small>
								</label>
								<textarea
									class="form-control font-monospace"
									name="template_tidak_tercapai"
									rows="4"
									placeholder="Masukkan template untuk kondisi tidak tercapai..."
								><?= esc($template['template_tidak_tercapai'] ?? '') ?></textarea>
								<small class="text-muted">Contoh: Terdapat {jumlah_tidak_tercapai} CPMK yang belum tercapai ({cpmk_tidak_tercapai_list}).</small>
							</div>

							<div class="d-flex justify-content-between align-items-center">
								<button type="submit" class="btn btn-primary">
									<i class="bi bi-save"></i> Simpan Template
								</button>
								<button type="button" class="btn btn-outline-secondary" onclick="resetForm('<?= esc($template['option_key']) ?>')">
									<i class="bi bi-arrow-counterclockwise"></i> Reset
								</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
</div>

<style>
	.font-monospace {
		font-family: 'Courier New', Courier, monospace;
		font-size: 0.9rem;
	}

	.accordion .btn-link {
		color: #333;
		font-weight: 500;
	}

	.accordion .btn-link:hover {
		color: #0d6efd;
	}

	code {
		background-color: #f8f9fa;
		padding: 2px 6px;
		border-radius: 3px;
		font-size: 0.9em;
	}
</style>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	// Store original form data for reset
	const originalFormData = {};

	document.addEventListener('DOMContentLoaded', function() {
		// Store original form data
		document.querySelectorAll('form[id^="form-"]').forEach(form => {
			const optionKey = form.querySelector('input[name="option_key"]').value;
			originalFormData[optionKey] = {
				template_tercapai: form.querySelector('textarea[name="template_tercapai"]').value,
				template_tidak_tercapai: form.querySelector('textarea[name="template_tidak_tercapai"]').value
			};
		});
	});

	function saveTemplate(event, optionKey) {
		event.preventDefault();

		const form = document.getElementById('form-' + optionKey);
		const submitBtn = form.querySelector('button[type="submit"]');
		const originalBtnText = submitBtn.innerHTML;

		// Show loading
		submitBtn.disabled = true;
		submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

		const formData = new FormData(form);

		fetch('<?= base_url('admin/laporan-cpmk/save-template') ?>', {
				method: 'POST',
				body: formData,
				headers: {
					'X-Requested-With': 'XMLHttpRequest'
				}
			})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					// Update stored original data
					originalFormData[optionKey] = {
						template_tercapai: formData.get('template_tercapai'),
						template_tidak_tercapai: formData.get('template_tidak_tercapai')
					};

					// Show success message
					showAlert('success', data.message);
				} else {
					showAlert('danger', data.message);
				}
			})
			.catch(error => {
				console.error('Error:', error);
				showAlert('danger', 'Terjadi kesalahan saat menyimpan template.');
			})
			.finally(() => {
				submitBtn.disabled = false;
				submitBtn.innerHTML = originalBtnText;
			});
	}

	function resetForm(optionKey) {
		if (!confirm('Apakah Anda yakin ingin mereset template ke versi terakhir yang disimpan?')) {
			return;
		}

		const form = document.getElementById('form-' + optionKey);
		const original = originalFormData[optionKey];

		if (original) {
			form.querySelector('textarea[name="template_tercapai"]').value = original.template_tercapai;
			form.querySelector('textarea[name="template_tidak_tercapai"]').value = original.template_tidak_tercapai;
			showAlert('info', 'Template direset ke versi terakhir yang disimpan.');
		}
	}

	function showAlert(type, message) {
		const alertDiv = document.createElement('div');
		alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
		alertDiv.style.zIndex = '9999';
		alertDiv.innerHTML = `
			${message}
			<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
		`;

		document.body.appendChild(alertDiv);

		// Auto dismiss after 5 seconds
		setTimeout(() => {
			alertDiv.remove();
		}, 5000);
	}
</script>
<?= $this->endSection() ?>

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
				<label class="form-label">Pilih CPMK</label>
				<input type="text" id="searchCpmk" class="form-control mb-2" placeholder="Cari CPMK...">
				<div id="cpmkRadioList" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.5rem;">
					<?php foreach ($cpmk as $item): ?>
						<div class="form-check cpmk-item">
							<input class="form-check-input cpmk-radio" type="radio" name="cpmk_id" value="<?= $item['id'] ?>" id="cpmk_<?= $item['id'] ?>"
								data-kode="<?= esc($item['kode_cpmk']) ?>" required>
							<label class="form-check-label" for="cpmk_<?= $item['id'] ?>">
								<?= esc($item['kode_cpmk']) ?> - <?= esc($item['kode_cpl']) ?>
							</label>
						</div>
					<?php endforeach ?>
				</div>
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
				<input type="text" id="searchMk" class="form-control mb-2" placeholder="Cari mata kuliah..." style="display:none;">
				<div id="mata_kuliah_wrapper" style="max-height: 300px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 0.375rem; padding: 0.5rem;">
					<span class="text-muted">Silakan pilih CPMK terlebih dahulu.</span>
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
	// Search filter for CPMK radios
	document.getElementById('searchCpmk').addEventListener('keyup', function() {
		const query = this.value.toLowerCase();
		document.querySelectorAll('.cpmk-item').forEach(function(item) {
			const text = item.querySelector('label').textContent.toLowerCase();
			item.style.display = text.indexOf(query) > -1 ? '' : 'none';
		});
	});

	// CPMK radio change handler
	document.querySelectorAll('.cpmk-radio').forEach(function(radio) {
		radio.addEventListener('change', function() {
			updateKodePrefix();
			loadMkByCpmk();
			loadNextSuffix();
		});
	});

	function getSelectedCpmk() {
		const selected = document.querySelector('.cpmk-radio:checked');
		return selected ? {
			id: selected.value,
			kode: selected.getAttribute('data-kode')
		} : null;
	}

	function loadNextSuffix() {
		const suffixInput = document.getElementById('kode_suffix');
		const cpmk = getSelectedCpmk();
		const baseUrl = '<?= rtrim(base_url(), '/') ?>';

		if (cpmk) {
			suffixInput.value = 'Memuat...';
			fetch(`${baseUrl}/admin/pemetaan-mk-cpmk-sub/get-next-suffix/${cpmk.id}`)
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
		const cpmk = getSelectedCpmk();
		const suffix = document.getElementById('kode_suffix').value;

		if (cpmk && cpmk.kode) {
			const no = cpmk.kode.replace('CPMK', '');
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
		const cpmk = getSelectedCpmk();
		const wrapper = document.getElementById('mata_kuliah_wrapper');
		const searchMk = document.getElementById('searchMk');
		const baseUrl = '<?= rtrim(base_url(), '/') ?>';

		searchMk.style.display = 'none';
		searchMk.value = '';

		if (!cpmk) {
			wrapper.innerHTML = '<span class="text-muted">Silakan pilih CPMK terlebih dahulu.</span>';
			return;
		}

		wrapper.innerHTML = '<span class="text-muted">Memuat...</span>';

		fetch(`${baseUrl}/admin/pemetaan-mk-cpmk-subcpmk/get-mk/${cpmk.id}`)
			.then(res => res.json())
			.then(data => {
				wrapper.innerHTML = '';
				if (data.length === 0) {
					wrapper.innerHTML = '<span class="text-danger">Tidak ada MK yang terhubung dengan CPMK ini.</span>';
					return;
				}
				searchMk.style.display = 'block';
				data.forEach(mk => {
					const formCheck = document.createElement('div');
					formCheck.className = 'form-check mk-item';

					const input = document.createElement('input');
					input.className = 'form-check-input';
					input.type = 'checkbox';
					input.name = 'mata_kuliah_id[]';
					input.value = mk.id;
					input.id = 'mk_' + mk.id;

					const label = document.createElement('label');
					label.className = 'form-check-label';
					label.htmlFor = input.id;
					label.textContent = mk.kode_mk + ' - ' + mk.nama_mk;

					formCheck.appendChild(input);
					formCheck.appendChild(label);
					wrapper.appendChild(formCheck);
				});
			})
			.catch(() => {
				wrapper.innerHTML = '<span class="text-danger">Gagal mengambil data MK.</span>';
			});
	}

	// Search filter for MK checkboxes
	document.getElementById('searchMk').addEventListener('keyup', function() {
		const query = this.value.toLowerCase();
		document.querySelectorAll('.mk-item').forEach(function(item) {
			const text = item.querySelector('label').textContent.toLowerCase();
			item.style.display = text.indexOf(query) > -1 ? '' : 'none';
		});
	});

	function validateForm() {
		const cpmk = getSelectedCpmk();
		if (!cpmk) {
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
<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<div class="mb-4">
	<h2 class="mb-1">Profil CPL</h2>
	<p class="text-muted">Capaian Pembelajaran Lulusan</p>
</div>

<?php if (empty($cplList)): ?>
	<div class="card">
		<div class="card-body text-center py-5 text-muted">
			<p class="mb-1">Belum ada data CPL</p>
			<small>Data CPL akan muncul setelah Anda memiliki nilai</small>
		</div>
	</div>
<?php else: ?>
	<!-- CPL Table -->
	<div class="card">
		<div class="card-body">
			<table class="table table-bordered">
				<thead>
					<tr>
						<th style="width: 100px;">Kode</th>
						<th>Deskripsi</th>
						<th style="width: 120px;" class="text-center">Capaian CPL (%)</th>
						<th style="width: 80px;" class="text-center">Aksi</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$cplCategories = [
						'P' => 'Pengetahuan',
						'KK' => 'Keterampilan Khusus',
						'KU' => 'Keterampilan Umum',
						'S' => 'Sikap',
					];

					foreach ($cplCategories as $key => $categoryName):
						if (!empty($cplByType[$key])):
					?>
							<tr class="table-light">
								<td colspan="4"><strong><?= $categoryName ?> (<?= $key ?>)</strong></td>
							</tr>
							<?php foreach ($cplByType[$key] as $cpl): ?>
								<tr>
									<td><?= esc($cpl['kode']) ?></td>
									<td><?= esc($cpl['deskripsi']) ?></td>
									<td class="text-center"><?= $cpl['nilai'] ?>%</td>
									<td class="text-center">
										<button type="button" class="btn btn-sm btn-outline-secondary" onclick="showDetail(<?= $cpl['id'] ?>, '<?= esc($cpl['kode']) ?>')">
											Detail
										</button>
									</td>
								</tr>
							<?php endforeach; ?>
					<?php
						endif;
					endforeach;
					?>
				</tbody>
			</table>
		</div>
	</div>
<?php endif; ?>

<!-- Detail Modal -->
<div class="modal fade" id="detailModal" tabindex="-1">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title">Detail CPMK - <span id="modalCplKode"></span></h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			</div>
			<div class="modal-body">
				<div id="detailContent">
					<div class="text-center py-4">
						<div class="spinner-border text-secondary" role="status">
							<span class="visually-hidden">Loading...</span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
function showDetail(cplId, cplKode) {
	document.getElementById('modalCplKode').textContent = cplKode;
	document.getElementById('detailContent').innerHTML = `
		<div class="text-center py-4">
			<div class="spinner-border text-secondary" role="status">
				<span class="visually-hidden">Loading...</span>
			</div>
		</div>
	`;

	const modal = new bootstrap.Modal(document.getElementById('detailModal'));
	modal.show();

	fetch(`<?= base_url('mahasiswa/profil-cpl/detail') ?>?cpl_id=${cplId}`)
		.then(response => response.json())
		.then(data => {
			if (data.success) {
				if (data.data.length === 0) {
					document.getElementById('detailContent').innerHTML = `
						<div class="text-center py-4 text-muted">
							<p>Tidak ada CPMK yang terkait dengan CPL ini</p>
						</div>
					`;
					return;
				}

				let html = '';

				// Show CPL Summary
				if (data.summary) {
					html += `<div class="alert alert-light border mb-4">
						<div class="row text-center">
							<div class="col-4">
								<small class="text-muted">Nilai CPL</small>
								<div class="fw-bold">${data.summary.nilai_cpl}</div>
							</div>
							<div class="col-4">
								<small class="text-muted">Total Bobot</small>
								<div class="fw-bold">${data.summary.total_bobot}%</div>
							</div>
							<div class="col-4">
								<small class="text-muted">Capaian CPL</small>
								<div class="fw-bold text-primary">${data.summary.capaian_cpl}%</div>
							</div>
						</div>
					</div>`;
				}

				data.data.forEach(cpmk => {
					html += `<div class="mb-4">
						<h6><strong>${cpmk.kode_cpmk}</strong> - ${cpmk.deskripsi_cpmk}</h6>
						<p class="mb-2">Nilai CPMK: <strong>${cpmk.nilai_cpmk}</strong> (Bobot: ${cpmk.bobot}%)</p>`;

					if (cpmk.detail_mk.length > 0) {
						cpmk.detail_mk.forEach(mk => {
							html += `<div class="mb-3 ps-3 border-start">
								<small class="text-muted">${mk.kode_mk} - ${mk.nama_mk}</small>
								<table class="table table-sm table-bordered mt-1 mb-0">
									<thead>
										<tr>
											<th>Teknik Penilaian</th>
											<th class="text-center">Nilai</th>
											<th class="text-center">Bobot (%)</th>
											<th class="text-center">Nilai × Bobot</th>
										</tr>
									</thead>
									<tbody>`;

							if (mk.teknik_breakdown && mk.teknik_breakdown.length > 0) {
								mk.teknik_breakdown.forEach(t => {
									html += `<tr>
										<td>${t.teknik}</td>
										<td class="text-center">${t.nilai}</td>
										<td class="text-center">${t.bobot}%</td>
										<td class="text-center">${t.weighted}</td>
									</tr>`;
								});
								html += `<tr class="table-light">
									<td colspan="3" class="text-end"><strong>Total (Nilai CPMK)</strong></td>
									<td class="text-center"><strong>${mk.nilai_cpmk}</strong></td>
								</tr>`;
							} else {
								html += `<tr><td colspan="4" class="text-center text-muted">Tidak ada data teknik penilaian</td></tr>`;
							}

							html += `</tbody></table></div>`;
						});
					} else {
						html += '<p class="text-muted ps-3">Belum ada nilai</p>';
					}

					html += '</div><hr>';
				});

				// Remove last hr
				html = html.slice(0, -4);
				document.getElementById('detailContent').innerHTML = html;
			} else {
				document.getElementById('detailContent').innerHTML = `
					<div class="alert alert-danger">${data.message}</div>
				`;
			}
		})
		.catch(error => {
			document.getElementById('detailContent').innerHTML = `
				<div class="alert alert-danger">Terjadi kesalahan saat memuat data</div>
			`;
		});
}
</script>

<?= $this->endSection() ?>

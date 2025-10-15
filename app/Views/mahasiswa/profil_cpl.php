<?= $this->extend('layouts/mahasiswa_layout') ?>

<?= $this->section('content') ?>

<div class="mb-4">
	<h2 class="mb-1">Profil CPL (Capaian Pembelajaran Lulusan)</h2>
	<p class="text-muted">Pemetaan pencapaian kompetensi lulusan berdasarkan nilai mata kuliah</p>
</div>

<!-- Overview Card -->
<div class="row g-4 mb-4">
	<div class="col-md-3">
		<div class="card stat-card primary">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<h6 class="text-muted mb-2">Rata-rata CPL</h6>
						<h2 class="mb-0"><?= $avgCPL ?></h2>
					</div>
					<div class="text-primary" style="font-size: 2.5rem;">
						<i class="bi bi-graph-up"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card stat-card success">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<h6 class="text-muted mb-2">CPL Tercapai</h6>
						<h2 class="mb-0"><?= $cplTercapai ?>/<?= $totalCPLCount ?></h2>
					</div>
					<div class="text-success" style="font-size: 2.5rem;">
						<i class="bi bi-check-circle"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card stat-card warning">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<h6 class="text-muted mb-2">Dalam Progress</h6>
						<h2 class="mb-0"><?= $totalCPLCount - $cplTercapai ?></h2>
					</div>
					<div class="text-warning" style="font-size: 2.5rem;">
						<i class="bi bi-hourglass-split"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="card stat-card info">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-center">
					<div>
						<h6 class="text-muted mb-2">Persentase</h6>
						<h2 class="mb-0"><?= $percentComplete ?>%</h2>
					</div>
					<div class="text-info" style="font-size: 2.5rem;">
						<i class="bi bi-pie-chart"></i>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php if (empty($cplList)): ?>
	<div class="card">
		<div class="card-body">
			<div class="text-center py-5 text-muted">
				<i class="bi bi-inbox" style="font-size: 3rem;"></i>
				<p class="mt-3">Belum ada data CPL</p>
				<small>Data CPL akan muncul setelah Anda memiliki nilai</small>
			</div>
		</div>
	</div>
<?php else: ?>
	<!-- CPL by Category -->
	<?php
	$cplCategories = [
		'P' => ['name' => 'Pengetahuan', 'color' => 'primary', 'icon' => 'book'],
		'KK' => ['name' => 'Keterampilan Khusus', 'color' => 'success', 'icon' => 'gear'],
		'KU' => ['name' => 'Keterampilan Umum', 'color' => 'info', 'icon' => 'people'],
		'S' => ['name' => 'Sikap', 'color' => 'warning', 'icon' => 'heart'],
	];

	foreach ($cplCategories as $key => $category):
		if (!empty($cplByType[$key])):
	?>
			<div class="card mb-4">
				<div class="card-header bg-<?= $category['color'] ?> text-white">
					<h5 class="mb-0">
						<i class="bi bi-<?= $category['icon'] ?>"></i>
						<?= $category['name'] ?> (<?= $key ?>)
					</h5>
				</div>
				<div class="card-body">
					<?php foreach ($cplByType[$key] as $cpl): ?>
						<div class="mb-4">
							<div class="d-flex justify-content-between align-items-start mb-2">
								<div class="flex-grow-1">
									<h6 class="mb-1">
										<span class="badge bg-<?= $category['color'] ?>"><?= esc($cpl['kode']) ?></span>
										<?= esc($cpl['deskripsi']) ?>
									</h6>
								</div>
								<div class="text-end ms-3">
									<h5 class="mb-0 text-<?= $category['color'] ?>"><?= $cpl['nilai'] ?></h5>
									<small class="text-muted">Nilai</small>
								</div>
							</div>
							<div class="progress" style="height: 25px;">
								<div class="progress-bar bg-<?= $category['color'] ?>"
									role="progressbar"
									style="width: <?= $cpl['nilai'] ?>%;"
									aria-valuenow="<?= $cpl['nilai'] ?>"
									aria-valuemin="0"
									aria-valuemax="100">
									<?= $cpl['nilai'] ?>%
								</div>
							</div>
							<div class="mt-2">
								<?php if ($cpl['status'] == 'Tercapai'): ?>
									<span class="badge bg-success">
										<i class="bi bi-check-circle"></i> Tercapai
									</span>
								<?php else: ?>
									<span class="badge bg-warning">
										<i class="bi bi-hourglass-split"></i> Dalam Progress
									</span>
								<?php endif; ?>
								<small class="text-muted ms-2">
									<i class="bi bi-info-circle"></i>
									Target minimal: 70
								</small>
							</div>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
	<?php
		endif;
	endforeach;
	?>

	<!-- CPL Trend Chart -->
	<div class="card">
		<div class="card-header">
			<h5 class="mb-0">
				<i class="bi bi-bar-chart"></i> Trend Pencapaian CPL
			</h5>
		</div>
		<div class="card-body">
			<div class="alert alert-info">
				<i class="bi bi-info-circle"></i>
				Grafik trend pencapaian CPL akan ditampilkan di sini. Data diambil dari nilai CPMK yang telah dikonversi ke CPL berdasarkan pemetaan kurikulum.
			</div>
			<div class="text-center py-5">
				<i class="bi bi-graph-up" style="font-size: 4rem; color: #ccc;"></i>
				<p class="text-muted mt-3">Visualisasi grafik dalam pengembangan</p>
			</div>
		</div>
	</div>
<?php endif; ?>

<?= $this->endSection() ?>
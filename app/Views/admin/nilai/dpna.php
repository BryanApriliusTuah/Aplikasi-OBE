<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?= esc($title) ?></title>
	<style>
		body {
			font-family: Arial, sans-serif;
			margin: 20px;
			background-color: #f5f5f5;
		}

		/* Hide CodeIgniter Debug Toolbar */
		#toolbarContainer {
			display: none !important;
		}

		.header {
			text-align: center;
			margin-bottom: 20px;
		}

		.header table {
			width: 100%;
			border-collapse: collapse;
		}

		.header td {
			padding: 5px;
		}

		.info-box {
			background-color: #fff;
			padding: 15px;
			margin-bottom: 20px;
			border-radius: 5px;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
		}

		.info-box table {
			width: 100%;
			border-collapse: collapse;
		}

		.info-box td {
			padding: 5px;
			font-size: 14px;
		}

		.info-box td:first-child {
			width: 250px;
			font-weight: bold;
		}

		table.dpna {
			width: 100%;
			border-collapse: collapse;
			background-color: #fff;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
			table-layout: auto;
		}

		.header-table {
			table-layout: fixed;
		}

		.dpna-container {
			width: 100%;
			overflow-x: auto;
			margin-bottom: 20px;
		}

		/* Minimum widths for critical columns */
		table.dpna col:nth-child(1) {
			min-width: 50px;
		}

		table.dpna col:nth-child(2) {
			min-width: 120px;
		}

		table.dpna col:nth-child(3) {
			min-width: 250px;
		}

		table.dpna th {
			background-color: #2c3e50;
			color: white;
			padding: 12px;
			text-align: center;
			font-weight: bold;
			border: 1px solid #34495e;
		}

		table.dpna td {
			padding: 10px;
			border: 1px solid #ddd;
			text-align: center;
		}

		table.dpna td.left {
			text-align: left;
		}

		table.dpna tbody tr:nth-child(even) {
			background-color: #f9f9f9;
		}

		table.dpna tbody tr:hover {
			background-color: #f0f0f0;
		}

		.no-print {
			margin: 20px 0;
			text-align: center;
		}

		.btn {
			padding: 10px 20px;
			margin: 0 5px;
			border: none;
			border-radius: 5px;
			cursor: pointer;
			font-size: 14px;
			text-decoration: none;
			display: inline-block;
		}

		.btn-primary {
			background-color: #3498db;
			color: white;
		}

		.btn-primary:hover {
			background-color: #2980b9;
		}

		.btn-secondary {
			background-color: #95a5a6;
			color: white;
		}

		.btn-secondary:hover {
			background-color: #7f8c8d;
		}

		.grade-a {
			background-color: #d4edda !important;
			font-weight: bold;
		}

		.grade-ab {
			background-color: #d1ecf1 !important;
		}

		.grade-b {
			background-color: #fff3cd !important;
		}

		.grade-bc {
			background-color: #ffe0b2 !important;
		}

		.grade-c {
			background-color: #f8d7da !important;
		}

		.grade-d {
			background-color: #f5c6cb !important;
			font-weight: bold;
		}

		.grade-e {
			background-color: #f8d7da !important;
			font-weight: bold;
			color: #721c24;
		}

		table.dpna th small {
			display: block;
			line-height: 1.3;
		}

		table.dpna thead tr:first-child th {
			border-bottom: 2px solid #fff;
		}

		@media print {
			body {
				background-color: white;
				margin: 10px;
			}

			.no-print {
				display: none !important;
			}

			.info-box {
				box-shadow: none;
				border: 1px solid #ddd;
			}

			table.dpna {
				box-shadow: none;
			}

			.dpna-container {
				overflow-x: visible;
			}

			/* Scale down font size for print if table is too wide */
			@page {
				size: landscape;
				margin: 10mm;
			}

			table.dpna th,
			table.dpna td {
				font-size: 0.75em;
				padding: 6px;
			}

			table.dpna th div {
				font-size: 0.7em;
			}
		}
	</style>
</head>

<body>
	<div class="no-print">
		<button onclick="window.print()" class="btn btn-primary">
			<i class="bi bi-printer"></i> Cetak / Simpan PDF
		</button>
		<a href="<?= base_url('admin/nilai/export-dpna-excel/' . $jadwal['id']) ?>" class="btn btn-success">
			<i class="bi bi-file-earmark-excel"></i> Export ke Excel
		</a>
		<a href="<?= base_url('admin/nilai/input-nilai-teknik/' . $jadwal['id']) ?>" class="btn btn-secondary">
			Kembali
		</a>
	</div>

	<?php
	// Determine semester type (Genap/Ganjil) based on semester number
	$semester_type = '';
	if (isset($jadwal['semester'])) {
		$semester_type = ($jadwal['semester'] % 2 == 0) ? 'Genap' : 'Ganjil';
	}

	// Extract year from tahun_akademik (e.g., "2023/2024 Ganjil" -> "2023/2024")
	$tahun = isset($jadwal['tahun_akademik']) ? trim(preg_replace('/(Ganjil|Genap)/', '', $jadwal['tahun_akademik'])) : '';
	?>

	<?php
	// Calculate total columns for header: 3 (No, NIM, Nama) + teknik_list + 3 (Nilai Angka, Nilai Huruf, Keterangan)
	$total_columns = 3 + count($teknik_list) + 3;
	$middle_colspan = $total_columns - 2; // Exclude logo and DPNA columns
	?>

	<div class="header">
		<table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;" class="header-table">
			<colgroup>
				<col style="width: 80px;">
				<?php for ($i = 0; $i < $middle_colspan; $i++): ?>
					<col>
				<?php endfor; ?>
				<col style="width: 150px;">
			</colgroup>
			<tr>
				<td rowspan="2" style="vertical-align: middle; text-align: center;">
					<img src="<?= base_url('img/Logo UPR.png') ?>" alt="Logo UPR" style="height: 60px;">
				</td>
				<td colspan="<?= $middle_colspan ?>" style="text-align: center; vertical-align: middle; padding: 5px;">
					<strong style="font-size: 16px;">KEMENTERIAN PENDIDIKAN TINGGI, SAINS, DAN TEKNOLOGI</strong>
				</td>
				<td rowspan="2" style="vertical-align: middle; text-align: center; padding: 10px;">
					<strong style="font-size: 16px;">DPNA<br>Semester <?= $semester_type ?> <?= $tahun ?></strong>
				</td>
			</tr>
			<tr>
				<td colspan="<?= $middle_colspan ?>" style="text-align: center; vertical-align: middle; padding: 5px;">
					<strong style="font-size: 16px;">UNIVERSITAS PALANGKA RAYA</strong>
				</td>
			</tr>
		</table>
	</div>

	<div class="info-box">
		<table>
			<tr>
				<td style="width: 250px;">MATA KULIAH</td>
				<td>: <?= strtoupper(esc($jadwal['nama_mk'])) ?></td>
			</tr>
			<tr>
				<td>KELAS/PROGRAM STUDI</td>
				<td>: <?= strtoupper(esc($jadwal['kelas'])) ?> / <?= strtoupper(esc($jadwal['program_studi'])) ?></td>
			</tr>
			<tr>
				<td>DOSEN PENGAMPU</td>
				<td>: <?= strtoupper(esc($jadwal['dosen_ketua'] ?? 'N/A')) ?></td>
			</tr>
		</table>
	</div>

	<div class="dpna-container">
		<table class="dpna">
			<colgroup>
				<col style="width: 50px;">
				<col style="width: 120px;">
				<col style="width: 250px;">
				<?php foreach ($teknik_list as $item): ?>
					<col style="width: 110px;">
				<?php endforeach; ?>
				<col style="width: 80px;">
				<col style="width: 70px;">
				<col style="width: 100px;">
			</colgroup>
			<thead>
				<tr>
					<th rowspan="2">No</th>
					<th rowspan="2">NIM</th>
					<th rowspan="2">Nama</th>
					<?php foreach ($teknik_by_tahap as $tahap => $tahap_items): ?>
						<th colspan="<?= count($tahap_items) ?>" style="background-color: #34495e;">
							<?= esc($tahap) ?>
						</th>
					<?php endforeach; ?>
					<th colspan="2">Nilai Akhir</th>
					<th rowspan="2">Keterangan</th>
				</tr>
				<tr>
					<?php foreach ($teknik_list as $item): ?>
						<?php
						$cpmk_display = $item['kode_cpmk'] ?? $item['cpmk_code'] ?? 'N/A';
						?>
						<th>
							<div style="font-size: 0.85em;">
								<strong><?= esc($item['teknik_label']) ?></strong><br>
								<small style="font-weight: normal;">Minggu: <?= $item['minggu'] ?></small><br>
								<small style="font-weight: normal;"><?= esc($cpmk_display) ?></small><br>
								<small style="background-color: #27ae60; color: white; padding: 2px 5px; border-radius: 3px;">
									<?= number_format($item['bobot'], 1) ?>%
								</small>
							</div>
						</th>
					<?php endforeach; ?>
					<th>Angka</th>
					<th>Huruf</th>
				</tr>
			</thead>
			<tbody>
				<?php if (empty($dpna_data)): ?>
					<tr>
						<td colspan="<?= 6 + count($teknik_list) ?>" style="text-align: center; padding: 30px; color: #999;">
							Tidak ada data mahasiswa
						</td>
					</tr>
				<?php else: ?>
					<?php foreach ($dpna_data as $row): ?>
						<?php
						$grade_class = '';
						switch (strtoupper($row['nilai_huruf'])) {
							case 'A':
								$grade_class = 'grade-a';
								break;
							case 'AB':
								$grade_class = 'grade-ab';
								break;
							case 'B':
								$grade_class = 'grade-b';
								break;
							case 'BC':
								$grade_class = 'grade-bc';
								break;
							case 'C':
								$grade_class = 'grade-c';
								break;
							case 'D':
								$grade_class = 'grade-d';
								break;
							case 'E':
								$grade_class = 'grade-e';
								break;
						}
						?>
						<tr>
							<td><?= $row['no'] ?></td>
							<td><?= esc($row['nim']) ?></td>
							<td class="left"><?= esc($row['nama']) ?></td>
							<?php foreach ($teknik_list as $item): ?>
								<?php
								$rps_mingguan_id = $item['rps_mingguan_id'];
								$teknik_key = $item['teknik_key'];
								?>
								<td><?= number_format($row['teknik_' . $rps_mingguan_id . '_' . $teknik_key], 2) ?></td>
							<?php endforeach; ?>
							<td><?= number_format($row['nilai_akhir'], 2) ?></td>
							<td><strong><?= esc($row['nilai_huruf']) ?></strong></td>
							<td><?= esc($row['keterangan'] ?? '-') ?></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
		</table>
	</div>

	<div style="margin-top: 50px; page-break-inside: avoid;">
		<table style="width: 100%; border-collapse: collapse;">
			<tr>
				<td style="width: 70%;"></td>
				<td style="width: 30%; text-align: left;">
					<p style="margin: 5px 0;">Palangka Raya, <?= date('d F Y') ?></p>
					<p style="margin: 5px 0;">Mengetahui</p>
					<p style="margin: 5px 0;">Dosen Koordinator Mata Kuliah</p>
					<br><br><br>
					<p style="margin: 5px 0;"><strong><?= esc($jadwal['dosen_ketua'] ?? 'Dosen Pengampu') ?></strong></p>
					<p style="margin: 5px 0;">NIP. <?= esc($nip ?? '') ?></p>
				</td>
			</tr>
		</table>
	</div>
</body>

</html>
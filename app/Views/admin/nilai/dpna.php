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
		.header {
			text-align: center;
			margin-bottom: 30px;
		}
		.header h1 {
			margin: 5px 0;
			font-size: 24px;
		}
		.header p {
			margin: 3px 0;
			font-size: 14px;
			color: #555;
		}
		.info-box {
			background-color: #fff;
			padding: 15px;
			margin-bottom: 20px;
			border-radius: 5px;
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
			width: 150px;
			font-weight: bold;
		}
		table.dpna {
			width: 100%;
			border-collapse: collapse;
			background-color: #fff;
			box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
		.grade-a { background-color: #d4edda !important; font-weight: bold; }
		.grade-ab { background-color: #d1ecf1 !important; }
		.grade-b { background-color: #fff3cd !important; }
		.grade-bc { background-color: #ffe0b2 !important; }
		.grade-c { background-color: #f8d7da !important; }
		.grade-d { background-color: #f5c6cb !important; font-weight: bold; }
		.grade-e { background-color: #f8d7da !important; font-weight: bold; color: #721c24; }
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

	<div class="header">
		<h1>DAFTAR PENILAIAN NILAI AKHIR (DPNA)</h1>
		<p><?= esc($jadwal['nama_mk']) ?></p>
		<p>Kelas: <?= esc($jadwal['kelas']) ?> | Tahun Akademik: <?= esc($jadwal['tahun_akademik']) ?></p>
	</div>

	<div class="info-box">
		<table>
			<tr>
				<td>Mata Kuliah</td>
				<td>: <?= esc($jadwal['nama_mk']) ?></td>
			</tr>
			<tr>
				<td>Kode Mata Kuliah</td>
				<td>: <?= esc($jadwal['kode_mk']) ?></td>
			</tr>
			<tr>
				<td>Kelas</td>
				<td>: <?= esc($jadwal['kelas']) ?></td>
			</tr>
			<tr>
				<td>Tahun Akademik</td>
				<td>: <?= esc($jadwal['tahun_akademik']) ?></td>
			</tr>
			<tr>
				<td>Program Studi</td>
				<td>: <?= esc($jadwal['program_studi']) ?></td>
			</tr>
			<tr>
				<td>Dosen Pengampu</td>
				<td>: <?= esc($jadwal['dosen_ketua'] ?? 'N/A') ?></td>
			</tr>
		</table>
	</div>

	<table class="dpna">
		<thead>
			<tr>
				<th rowspan="2" style="width: 50px;">No</th>
				<th rowspan="2" style="width: 120px;">NIM</th>
				<th rowspan="2" style="width: 250px;">Nama</th>
				<th rowspan="2" style="width: 80px;">
					Tugas
					<?php if (isset($weights['tugas']) && $weights['tugas'] > 0): ?>
						<br><small style="font-weight: normal; font-size: 0.85em;">(<?= $weights['tugas'] ?>%)</small>
					<?php endif; ?>
				</th>
				<th rowspan="2" style="width: 80px;">
					UTS
					<?php if (isset($weights['uts']) && $weights['uts'] > 0): ?>
						<br><small style="font-weight: normal; font-size: 0.85em;">(<?= $weights['uts'] ?>%)</small>
					<?php endif; ?>
				</th>
				<th rowspan="2" style="width: 80px;">
					UAS
					<?php if (isset($weights['uas']) && $weights['uas'] > 0): ?>
						<br><small style="font-weight: normal; font-size: 0.85em;">(<?= $weights['uas'] ?>%)</small>
					<?php endif; ?>
				</th>
				<th colspan="2" style="width: 150px;">Nilai Akhir</th>
				<th rowspan="2" style="width: 100px;">Keterangan</th>
			</tr>
			<tr>
				<th style="width: 80px;">Angka</th>
				<th style="width: 70px;">Huruf</th>
			</tr>
		</thead>
		<tbody>
			<?php if (empty($dpna_data)): ?>
				<tr>
					<td colspan="9" style="text-align: center; padding: 30px; color: #999;">
						Tidak ada data mahasiswa
					</td>
				</tr>
			<?php else: ?>
				<?php foreach ($dpna_data as $row): ?>
					<?php
						$grade_class = '';
						switch (strtoupper($row['nilai_huruf'])) {
							case 'A': $grade_class = 'grade-a'; break;
							case 'AB': $grade_class = 'grade-ab'; break;
							case 'B': $grade_class = 'grade-b'; break;
							case 'BC': $grade_class = 'grade-bc'; break;
							case 'C': $grade_class = 'grade-c'; break;
							case 'D': $grade_class = 'grade-d'; break;
							case 'E': $grade_class = 'grade-e'; break;
						}
					?>
					<tr>
						<td><?= $row['no'] ?></td>
						<td><?= esc($row['nim']) ?></td>
						<td class="left"><?= esc($row['nama']) ?></td>
						<td><?= number_format($row['tugas'], 2) ?></td>
						<td><?= number_format($row['uts'], 2) ?></td>
						<td><?= number_format($row['uas'], 2) ?></td>
						<td><?= number_format($row['nilai_akhir'], 2) ?></td>
						<td><strong><?= esc($row['nilai_huruf']) ?></strong></td>
						<td><?= esc($row['keterangan'] ?? '-') ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</tbody>
	</table>

	<div style="margin-top: 50px; text-align: right; page-break-inside: avoid;">
		<p>Dicetak pada: <?= date('d F Y, H:i') ?> WIB</p>
		<br><br><br>
		<p>_______________________________</p>
		<p><?= esc($jadwal['dosen_ketua'] ?? 'Dosen Pengampu') ?></p>
	</div>
</body>
</html>

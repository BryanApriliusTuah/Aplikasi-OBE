<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Jadwal Mengajar</title>
	<style>
		body {
			font-family: sans-serif;
			font-size: 10px;
		}

		.table {
			width: 100%;
			border-collapse: collapse;
		}

		.table th,
		.table td {
			border: 1px solid #ddd;
			padding: 6px;
		}

		.table th {
			background-color: #f2f2f2;
			text-align: left;
		}

		h2 {
			text-align: center;
		}
	</style>
</head>

<body>
	<h2>Daftar Jadwal Mengajar</h2>
	<table class="table">
		<thead>
			<tr>
				<th>No</th>
				<th>Prodi</th>
				<th>Tahun Akademik</th>
				<th>Kode MK</th>
				<th>Mata Kuliah</th>
				<th>SMT</th>
				<th>SKS</th>
				<th>Kelas</th>
				<th>Waktu & Ruang</th>
				<th>Dosen Pengampu</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($jadwal_list as $key => $jadwal): ?>
				<tr>
					<td><?= $key + 1 ?></td>
					<td><?= esc($jadwal['program_studi']) ?></td>
					<td><?= esc($jadwal['tahun_akademik']) ?></td>
					<td><?= esc($jadwal['kode_mk']) ?></td>
					<td><?= esc($jadwal['nama_mk']) ?></td>
					<td><?= esc($jadwal['semester']) ?></td>
					<td><?= esc($jadwal['sks']) ?></td>
					<td><?= esc($jadwal['kelas']) ?></td>
					<td>
						<?php
						$waktu = [];
						if (!empty($jadwal['hari'])) $waktu[] = esc($jadwal['hari']);
						if (!empty($jadwal['jam_mulai'])) $waktu[] = date('H:i', strtotime($jadwal['jam_mulai'])) . '-' . date('H:i', strtotime($jadwal['jam_selesai']));
						if (!empty($jadwal['ruang'])) $waktu[] = 'di ' . esc($jadwal['ruang']);
						echo implode(', ', $waktu);
						?>
					</td>
					<td>
						<?php
						$dosen_pengampu = [];
						foreach ($jadwal['dosen_list'] as $dosen) {
							$dosen_pengampu[] = esc($dosen['nama_lengkap']) . ($dosen['role'] == 'leader' ? ' (K)' : '');
						}
						echo implode(", ", $dosen_pengampu);
						?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
</body>

</html>
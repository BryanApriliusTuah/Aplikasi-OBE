<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<title>Portal Mahasiswa - OBE TI UPR</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
	<link rel="stylesheet" href="<?= base_url('css/custom.css') ?>">
	<style>
		/* Remove arrows/spinners from number input */

		/* Chrome, Edge, Opera */
		input[type=number]::-webkit-inner-spin-button,
		input[type=number]::-webkit-outer-spin-button {
			-webkit-appearance: none;
			margin: 0;
		}

		/* Safari fix */
		input[type=number] {
			-webkit-appearance: textfield;
			appearance: textfield;
		}

		/* Firefox */
		input[type=number] {
			-moz-appearance: textfield;
			appearance: textfield;
		}
	</style>

</head>

<body>
	<div class="topbar">
		<span class="brand">Portal Mahasiswa TI UPR</span>
		<div class="d-flex align-items-center gap-3">
			<div class="user-info text-end">
				<div class="user-name fw-bold"><?= session('nama_lengkap') ?></div>
				<div class="user-role text-muted small">
					Mahasiswa<?= session('nim') ? ' (' . session('nim') . ')' : '' ?>
				</div>
			</div>
			<a href="<?= base_url('logout') ?>" class="logout-link" data-bs-toggle="tooltip" data-bs-placement="bottom" title="Keluar dari sistem">
				<i class="bi bi-box-arrow-right"></i> Logout
			</a>
		</div>
	</div>

	<div class="wrapper">
		<div class="sidebar">
			<ul class="nav flex-column">
				<li class="nav-item">
					<a class="nav-link<?= uri_string() == 'mahasiswa' || uri_string() == 'mahasiswa/dashboard' ? ' active' : '' ?>"
						href="<?= base_url('mahasiswa/dashboard') ?>"
						data-bs-toggle="tooltip" data-bs-placement="right" title="Halaman Dashboard">
						<i class="bi bi-house-door"></i> Dashboard
					</a>
				</li>

				<li class="nav-item">
					<a class="nav-link<?= uri_string() == 'mahasiswa/nilai' ? ' active' : '' ?>"
						href="<?= base_url('mahasiswa/nilai') ?>"
						data-bs-toggle="tooltip" data-bs-placement="right" title="Lihat nilai mata kuliah Anda">
						<i class="bi bi-file-earmark-bar-graph"></i> Nilai Saya
					</a>
				</li>

				<?php
				$laporanUris = [
					'mahasiswa/laporan-cpmk',
					'mahasiswa/laporan-cpl'
				];
				$isLaporanOpen = in_array(uri_string(), $laporanUris);
				?>
				<li class="nav-item sidebar-dropdown<?= $isLaporanOpen ? ' open has-active-child' : '' ?>">
					<a class="nav-link sidebar-dropdown-toggle d-flex justify-content-between align-items-center" href="#" tabindex="0"
						data-bs-toggle="tooltip" data-bs-placement="right" title="Lihat laporan CPL dan CPMK">
						<span><i class="bi bi-file-earmark-text"></i> Laporan</span>
						<span class="caret"></span>
					</a>
					<ul class="sidebar-dropdown-menu list-unstyled ps-2<?= $isLaporanOpen ? ' show' : '' ?>">
						<div>
							<li><a class="nav-link<?= uri_string() == 'mahasiswa/laporan-cpmk' ? ' active' : '' ?>"
									href="<?= base_url('mahasiswa/laporan-cpmk') ?>"
									data-bs-toggle="tooltip" data-bs-placement="right" title="Laporan Capaian CPMK">
									Laporan CPMK</a></li>
							<li><a class="nav-link<?= uri_string() == 'mahasiswa/laporan-cpl' ? ' active' : '' ?>"
									href="<?= base_url('mahasiswa/laporan-cpl') ?>"
									data-bs-toggle="tooltip" data-bs-placement="right" title="Laporan Capaian CPL">
									Laporan CPL</a></li>
						</div>
					</ul>
				</li>

				<!-- <li class="nav-item">
					<a class="nav-link<?= uri_string() == 'mahasiswa/jadwal' ? ' active' : '' ?>"
						href="<?= base_url('mahasiswa/jadwal') ?>"
						data-bs-toggle="tooltip" data-bs-placement="right" title="Lihat jadwal kuliah Anda">
						<i class="bi bi-calendar-event"></i> Jadwal Kuliah
					</a>
				</li> -->

				<li class="nav-item">
					<a class="nav-link<?= uri_string() == 'mahasiswa/profil-cpl' ? ' active' : '' ?>"
						href="<?= base_url('mahasiswa/profil-cpl') ?>"
						data-bs-toggle="tooltip" data-bs-placement="right" title="Lihat profil capaian pembelajaran lulusan Anda">
						<i class="bi bi-graph-up"></i> Profil CPL
					</a>
				</li>

				<!-- <li class="nav-item">
					<a class="nav-link<?= uri_string() == 'mahasiswa/mbkm' ? ' active' : '' ?>"
						href="<?= base_url('mahasiswa/mbkm') ?>"
						data-bs-toggle="tooltip" data-bs-placement="right" title="Lihat kegiatan MBKM Anda">
						<i class="bi bi-backpack"></i> Kegiatan MBKM
					</a>
				</li> -->

				<li class="nav-item">
					<a class="nav-link<?= uri_string() == 'mahasiswa/profil' ? ' active' : '' ?>"
						href="<?= base_url('mahasiswa/profil') ?>"
						data-bs-toggle="tooltip" data-bs-placement="right" title="Lihat dan kelola profil Anda">
						<i class="bi bi-person-circle"></i> Profil Saya
					</a>
				</li>
			</ul>
		</div>

		<main class="main-content">
			<div class="content-wrapper">
				<?= $this->renderSection('content') ?>
			</div>
		</main>
	</div>

	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

	<script>
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
		var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
			return new bootstrap.Tooltip(tooltipTriggerEl)
		})

		// Script untuk toggle dropdown menu
		document.querySelectorAll('.sidebar-dropdown-toggle').forEach(function(btn) {
			btn.addEventListener('click', function(e) {
				e.preventDefault();
				var parent = btn.closest('.sidebar-dropdown');
				var isOpen = parent.classList.contains('open');

				// Tutup semua dropdown lain
				document.querySelectorAll('.sidebar-dropdown').forEach(function(li) {
					if (li !== parent) {
						li.classList.remove('open');
						var menu = li.querySelector('.sidebar-dropdown-menu');
						if (menu) menu.classList.remove('show');
					}
				});

				// Toggle dropdown yang diklik
				if (isOpen) {
					parent.classList.remove('open');
					parent.querySelector('.sidebar-dropdown-menu').classList.remove('show');
				} else {
					parent.classList.add('open');
					parent.querySelector('.sidebar-dropdown-menu').classList.add('show');
				}
			});
		});

		// Tutup semua dropdown kalau klik di luar sidebar
		document.body.addEventListener('click', function(e) {
			if (!e.target.closest('.sidebar')) {
				document.querySelectorAll('.sidebar-dropdown').forEach(function(li) {
					li.classList.remove('open');
					var menu = li.querySelector('.sidebar-dropdown-menu');
					if (menu) menu.classList.remove('show');
				});
			}
		}, true);
	</script>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
	<?= $this->renderSection('js') ?>
</body>

</html>
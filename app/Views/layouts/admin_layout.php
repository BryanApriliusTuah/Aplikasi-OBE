<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<title>Dashboard OBE TI UPR</title>
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
		<span class="brand">Sistem Kurikulum OBE TI UPR</span>
		<div class="d-flex align-items-center gap-3">
			<div class="user-info text-end">
				<div class="user-name fw-bold"><?= session('nama_lengkap') ?? session('username') ?></div>
				<div class="user-role text-muted small">
					<?php
					$role = session('role');
					if ($role === 'admin') {
						echo 'Administrator';
					} elseif ($role === 'dosen') {
						echo 'Dosen' . (session('nip') ? ' (' . session('nip') . ')' : '');
					} elseif ($role === 'mahasiswa') {
						echo 'Mahasiswa' . (session('nim') ? ' (' . session('nim') . ')' : '');
					}
					?>
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
					<a class="nav-link<?= uri_string() == 'admin' ? ' active' : '' ?>" href="<?= base_url('admin') ?>"
						data-bs-toggle="tooltip" data-bs-placement="right" title="Halaman Dashboard">
						<i class="bi bi-house-door"></i> Dashboard
					</a>
				</li>

				<?php
				$masterDataUris = [
					'admin/profil-lulusan',
					'admin/cpl',
					'admin/bahan-kajian',
					'admin/mata-kuliah',
					'admin/cpmk',
					'admin/dosen',
					'admin/mahasiswa'
				];
				$isMasterDataOpen = in_array(uri_string(), $masterDataUris);
				?>
				<li class="nav-item sidebar-dropdown<?= $isMasterDataOpen ? ' open' : '' ?>">
					<a class="nav-link sidebar-dropdown-toggle d-flex justify-content-between align-items-center" href="#" tabindex="0"
						data-bs-toggle="tooltip" data-bs-placement="right" title="Kelola data-data utama kurikulum">
						<span><i class="bi bi-folder2-open"></i> Master Data</span>
						<span class="caret"></span>
					</a>
					<ul class="sidebar-dropdown-menu list-unstyled ps-2<?= $isMasterDataOpen ? ' show' : '' ?>">
						<li><a class="nav-link <?= uri_string() == 'admin/profil-lulusan' ? 'active' : '' ?>" href="<?= base_url('admin/profil-lulusan') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Kelola Profil Lulusan Program Studi">Profil Lulusan</a></li>
						<li><a class="nav-link <?= uri_string() == 'admin/cpl' ? 'active' : '' ?>" href="<?= base_url('admin/cpl') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Kelola Capaian Pembelajaran Lulusan (CPL)">CPL</a></li>
						<li><a class="nav-link <?= uri_string() == 'admin/bahan-kajian' ? 'active' : '' ?>" href="<?= base_url('admin/bahan-kajian') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Kelola Bahan Kajian Kurikulum">Bahan Kajian</a></li>
						<li><a class="nav-link <?= uri_string() == 'admin/mata-kuliah' ? 'active' : '' ?>" href="<?= base_url('admin/mata-kuliah') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Kelola Mata Kuliah yang Ditawarkan">Mata Kuliah</a></li>
						<li><a class="nav-link <?= uri_string() == 'admin/cpmk' ? 'active' : '' ?>" href="<?= base_url('admin/cpmk') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Kelola Capaian Pembelajaran Mata Kuliah (CPMK)">CPMK</a></li>
						<li><a class="nav-link <?= uri_string() == 'admin/dosen' ? 'active' : '' ?>" href="<?= base_url('admin/dosen') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Kelola Data Dosen">Data Dosen</a></li>
						<li><a class="nav-link <?= uri_string() == 'admin/mahasiswa' ? 'active' : '' ?>" href="<?= base_url('admin/mahasiswa') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Kelola Data Mahasiswa">Data mahasiswa</a></li>
					</ul>
				</li>

				<?php
				$pemetaanUris = [
					'admin/cpl-pl',
					'admin/cpl-bk',
					'admin/bkmk',
					'admin/cpl-mk',
					'admin/pemetaan-cpl-mk-cpmk',
					'admin/pemetaan-mk-cpmk-sub'
				];
				$isPemetaanOpen = in_array(uri_string(), $pemetaanUris);
				?>
				<li class="nav-item sidebar-dropdown<?= $isPemetaanOpen ? ' open' : '' ?>">
					<a class="nav-link sidebar-dropdown-toggle d-flex justify-content-between align-items-center" href="#" tabindex="0"
						data-bs-toggle="tooltip" data-bs-placement="right" title="Hubungkan antar komponen kurikulum">
						<span><i class="bi bi-diagram-3"></i> Pemetaan</span>
						<span class="caret"></span>
					</a>
					<ul class="sidebar-dropdown-menu list-unstyled ps-2<?= $isPemetaanOpen ? ' show' : '' ?>">
						<li><a class="nav-link <?= uri_string() == 'admin/cpl-pl' ? 'active' : '' ?>" href="<?= base_url('admin/cpl-pl') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Hubungkan CPL dengan Profil Lulusan">CPL ke PL</a></li>
						<li><a class="nav-link <?= uri_string() == 'admin/cpl-bk' ? 'active' : '' ?>" href="<?= base_url('admin/cpl-bk') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Hubungkan CPL dengan Bahan Kajian">CPL ke BK</a></li>
						<li><a class="nav-link <?= uri_string() == 'admin/bkmk' ? 'active' : '' ?>" href="<?= base_url('admin/bkmk') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Hubungkan Bahan Kajian dengan Mata Kuliah">BK ke MK</a></li>
						<li><a class="nav-link <?= uri_string() == 'admin/cpl-mk' ? 'active' : '' ?>" href="<?= base_url('admin/cpl-mk') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Hubungkan CPL dengan Mata Kuliah">CPL ke MK</a></li>
						<li><a class="nav-link <?= uri_string() == 'admin/pemetaan-cpl-mk-cpmk' ? 'active' : '' ?>" href="<?= base_url('admin/pemetaan-cpl-mk-cpmk') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Hubungkan CPL, CPMK, dan MK">CPL- CPMK - MK</a></li>
						<li><a class="nav-link <?= uri_string() == 'admin/pemetaan-mk-cpmk-sub' ? 'active' : '' ?>" href="<?= base_url('admin/pemetaan-mk-cpmk-sub') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Hubungkan MK, CPMK, dan SubCPMK">MK-CPMK-SubCPMK</a></li>
					</ul>
				</li>

				<li class="nav-item">
					<a class="nav-link<?= uri_string() == 'rps' ? ' active' : '' ?>" href="<?= base_url('rps') ?>"
						data-bs-toggle="tooltip" data-bs-placement="right" title="Kelola Rencana Pembelajaran Semester">
						<i class="bi bi-book"></i> RPS
					</a>
				</li>

				<?php
				$matriksUris = [
					'admin/organisasi-mk',
					'admin/peta-cpl',
					'admin/cpl-bk-mk',
					'admin/cpl-cpmk-mk-per-semester',
					'admin/mk-cpl-cpmk'
				];
				$isMatriksOpen = in_array(uri_string(), $matriksUris);
				?>
				<li class="nav-item sidebar-dropdown<?= $isMatriksOpen ? ' open' : '' ?>">
					<a class="nav-link sidebar-dropdown-toggle d-flex justify-content-between align-items-center" href="#" tabindex="0"
						data-bs-toggle="tooltip" data-bs-placement="right" title="Lihat rekapitulasi pemetaan dalam bentuk matriks">
						<span><i class="bi bi-table"></i> Matriks Pemetaan</span>
						<span class="caret"></span>
					</a>
					<ul class="sidebar-dropdown-menu list-unstyled ps-2<?= $isMatriksOpen ? ' show' : '' ?>">
						<li><a class="nav-link <?= uri_string() == 'admin/organisasi-mk' ? 'active' : '' ?>" href="<?= base_url('admin/organisasi-mk') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Lihat matriks organisasi mata kuliah per semester">Organisasi MK</a></li>
						<li><a class="nav-link <?= uri_string() == 'admin/peta-cpl' ? 'active' : '' ?>" href="<?= base_url('admin/peta-cpl') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Lihat matriks pemenuhan CPL per semester">Peta CPL</a></li>
						<li><a class="nav-link <?= uri_string() == 'admin/cpl-bk-mk' ? 'active' : '' ?>" href="<?= base_url('admin/cpl-bk-mk') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Lihat matriks hubungan CPL, BK, dan MK">CPL-BK-MK</a></li>
						<li><a class="nav-link <?= uri_string() == 'admin/cpl-cpmk-mk-per-semester' ? 'active' : '' ?>" href="<?= base_url('admin/cpl-cpmk-mk-per-semester') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Lihat pemenuhan CPL & CPMK oleh Mata Kuliah">Pemenuhan CPL-CPMK-MK</a></li>
						<li><a class="nav-link <?= uri_string() == 'admin/mk-cpl-cpmk' ? 'active' : '' ?>" href="<?= base_url('admin/mk-cpl-cpmk') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Lihat pemetaan antara CPL, MK, dan CPMK">Pemetaan CPL-MK-CPMK</a></li>
					</ul>
				</li>

				<?php
				$asesmenUris = [
					'teknik-penilaian-cpmk',
					'tahap-mekanisme-penilaian',
					'bobot-penilaian-cpl',
					'bobot-penilaian-mk',
					'rumusan-akhir-mk',
					'rumusan-akhir-cpl'
				];
				$isAsesmenOpen = in_array(uri_string(), $asesmenUris);
				?>
				<li class="nav-item sidebar-dropdown<?= $isAsesmenOpen ? ' open' : '' ?>">
					<a class="nav-link sidebar-dropdown-toggle d-flex justify-content-between align-items-center" href="#" tabindex="0"
						data-bs-toggle="tooltip" data-bs-placement="right" title="Lihat rekapitulasi penilaian dan asesmen">
						<span><i class="bi bi-file-earmark-text"></i> Asesmen Pembelajaran </span>
						<span class="caret"></span>
					</a>
					<ul class="sidebar-dropdown-menu list-unstyled ps-2<?= $isAsesmenOpen ? ' show' : '' ?>">
						<li><a class="nav-link <?= uri_string() == 'teknik-penilaian-cpmk' ? 'active' : '' ?>" href="<?= base_url('teknik-penilaian-cpmk') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Rekapitulasi teknik penilaian untuk setiap CPMK">Teknik Penilaian CPMK</a></li>
						<li><a class="nav-link <?= uri_string() == 'tahap-mekanisme-penilaian' ? 'active' : '' ?>" href="<?= base_url('tahap-mekanisme-penilaian') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Detail tahapan dan mekanisme penilaian">Tahap dan Mekanisme Penilaian</a></li>
						<li><a class="nav-link <?= uri_string() == 'bobot-penilaian-cpl' ? 'active' : '' ?>" href="<?= base_url('bobot-penilaian-cpl') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Rekapitulasi bobot nilai berdasarkan CPL">Bobot Penilaian Berdasarkan CPL</a></li>
						<li><a class="nav-link <?= uri_string() == 'bobot-penilaian-mk' ? 'active' : '' ?>" href="<?= base_url('bobot-penilaian-mk') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Rekapitulasi bobot nilai berdasarkan Mata Kuliah">Bobot Penilaian Berdasarkan MK</a></li>
						<li><a class="nav-link <?= uri_string() == 'rumusan-akhir-mk' ? 'active' : '' ?>" href="<?= base_url('rumusan-akhir-mk') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Rumusan perhitungan nilai akhir Mata Kuliah">Rumusan Nilai Akhir MK</a></li>
						<li><a class="nav-link <?= uri_string() == 'rumusan-akhir-cpl' ? 'active' : '' ?>" href="<?= base_url('rumusan-akhir-cpl') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Rumusan perhitungan nilai akhir CPL">Rumusan Nilai Akhir CPL</a></li>
					</ul>
				</li>

				<?php if (session('role') === 'admin'): ?>
					<li class="nav-item">
						<a class="nav-link" href="<?= base_url('admin/user') ?>"
							data-bs-toggle="tooltip" data-bs-placement="right" title="Kelola akun pengguna (Admin & Dosen)">
							<i class="bi bi-people"></i> Manajemen User
						</a>
					</li>
				<?php endif; ?>

				<li class="nav-item">
					<a class="nav-link" href="<?= base_url('admin/dosen') ?>"
						data-bs-toggle="tooltip" data-bs-placement="right" title="Lihat riwayat aktivitas pengguna dalam sistem">
						<i class="bi bi-journal-text"></i> Dosen
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?= base_url('admin/mengajar') ?>"
						data-bs-toggle="tooltip" data-bs-placement="right" title="Lihat riwayat aktivitas pengguna dalam sistem">
						<i class="bi bi-journal-text"></i> Mengajar
					</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?= base_url('admin/nilai') ?>"
						data-bs-toggle="tooltip" data-bs-placement="right" title="Lihat riwayat aktivitas pengguna dalam sistem">
						<i class="bi bi-journal-text"></i> Nilai
					</a>
				</li>
				<?php
				$mbkmUris = [
					'admin/mbkm',
					'admin/mbkm-jenis',
				];
				$isMbkmOpen = in_array(uri_string(), $mbkmUris);
				?>

				<li class="nav-item sidebar-dropdown<?= $isMbkmOpen ? ' open' : '' ?>">
					<a class="nav-link sidebar-dropdown-toggle d-flex justify-content-between align-items-center" href="#" tabindex="0"
						data-bs-toggle="tooltip" data-bs-placement="right" title="Kelola kegiatan MBKM">
						<span><i class="bi bi-backpack"></i> MBKM </span>
						<span class="caret"></span>
					</a>
					<ul class="sidebar-dropdown-menu list-unstyled ps-2<?= $isMbkmOpen ? ' show' : '' ?>">
						<li><a class="nav-link <?= uri_string() == 'admin/mbkm' ? 'active' : '' ?>"
								href="<?= base_url('admin/mbkm') ?>"
								data-bs-toggle="tooltip" data-bs-placement="right"
								title="Kelola kegiatan MBKM mahasiswa">
								Kegiatan MBKM</a></li>
						<li><a class="nav-link <?= uri_string() == 'admin/mbkm-jenis' ? 'active' : '' ?>"
								href="<?= base_url('admin/mbkm-jenis') ?>"
								data-bs-toggle="tooltip" data-bs-placement="right"
								title="Kelola jenis kegiatan MBKM">
								Jenis Kegiatan</a></li>
					</ul>
				</li>
				<?php
				$profilCplUris = [
					'admin/capaian-cpmk',
					'admin/capaian-cpl'
				];
				$isProfilCplOpen = in_array(uri_string(), $profilCplUris);
				?>

				<li class="nav-item sidebar-dropdown<?= $isProfilCplOpen ? ' open' : '' ?>">
					<a class="nav-link sidebar-dropdown-toggle d-flex justify-content-between align-items-center" href="#" tabindex="0"
						data-bs-toggle="tooltip" data-bs-placement="right" title="Lihat rekapitulasi penilaian dan asesmen">
						<span><i class="bi bi-file-earmark-text"></i> Profil Capaian </span>
						<span class="caret"></span>
					</a>
					<ul class="sidebar-dropdown-menu list-unstyled ps-2<?= $isProfilCplOpen ? ' show' : '' ?>">
						<li><a class="nav-link <?= uri_string() == 'admin/capaian-cpmk' ? 'active' : '' ?>" href="<?= base_url('admin/capaian-cpmk') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Rekapitulasi teknik penilaian untuk setiap CPMK">Capaian CPMK</a></li>
						<li><a class="nav-link <?= uri_string() == 'admin/capaian-cpl' ? 'active' : '' ?>" href="<?= base_url('admin/capaian-cpl') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Detail tahapan dan mekanisme penilaian">Capaian CPL</a></li>
					</ul>
				</li>

				<?php
				$laporanCapaianUris = [
					'admin/laporan-cpmk',
					'admin/laporan-cpmk/generate',
					'admin/laporan-cpl'
				];
				$isLaporanCapaianOpen = in_array(uri_string(), $laporanCapaianUris);
				?>

				<li class="nav-item sidebar-dropdown<?= $isLaporanCapaianOpen ? ' open' : '' ?>">
					<a class="nav-link sidebar-dropdown-toggle d-flex justify-content-between align-items-center" href="#" tabindex="0"
						data-bs-toggle="tooltip" data-bs-placement="right" title="Lihat laporan capaian pembelajaran">
						<span><i class="bi bi-file-earmark-bar-graph"></i> Portofolio </span>
						<span class="caret"></span>
					</a>
					<ul class="sidebar-dropdown-menu list-unstyled ps-2<?= $isLaporanCapaianOpen ? ' show' : '' ?>">
						<li><a class="nav-link <?= in_array(uri_string(), ['admin/laporan-cpmk', 'admin/laporan-cpmk/generate']) ? 'active' : '' ?>" href="<?= base_url('admin/laporan-cpmk') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Laporan Capaian Pembelajaran Mata Kuliah">Portofolio CPMK</a></li>
						<li><a class="nav-link <?= uri_string() == 'admin/laporan-cpl' ? 'active' : '' ?>" href="<?= base_url('admin/laporan-cpl') ?>" data-bs-toggle="tooltip" data-bs-placement="right" title="Laporan Capaian Pembelajaran Lulusan">Portofolio CPL</a></li>
					</ul>
				</li>

				<?php if (session('role') === 'admin'): ?>
					<li class="nav-item">
						<a class="nav-link<?= uri_string() == 'admin/settings' ? ' active' : '' ?>" href="<?= base_url('admin/settings') ?>"
							data-bs-toggle="tooltip" data-bs-placement="right" title="Kelola pengaturan sistem penilaian dan grade">
							<i class="bi bi-gear"></i> Settings
						</a>
					</li>
				<?php endif; ?>
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
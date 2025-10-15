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
		body {
			font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
			background-color: #f5f7fa;
		}

		.topbar {
			background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
			color: white;
			padding: 1rem 2rem;
			display: flex;
			justify-content: space-between;
			align-items: center;
			box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
		}

		.brand {
			font-size: 1.5rem;
			font-weight: 600;
		}

		.user-info {
			display: flex;
			align-items: center;
			gap: 1rem;
		}

		.user-avatar {
			width: 40px;
			height: 40px;
			border-radius: 50%;
			background: white;
			color: #667eea;
			display: flex;
			align-items: center;
			justify-content: center;
			font-weight: 600;
		}

		.logout-link {
			color: white;
			text-decoration: none;
			padding: 0.5rem 1rem;
			border-radius: 5px;
			transition: background 0.3s;
		}

		.logout-link:hover {
			background: rgba(255, 255, 255, 0.2);
			color: white;
		}

		.wrapper {
			display: flex;
			min-height: calc(100vh - 73px);
		}

		.sidebar {
			width: 260px;
			background: white;
			box-shadow: 2px 0 10px rgba(0, 0, 0, 0.05);
			padding: 1.5rem 0;
		}

		.nav-link {
			color: #4a5568;
			padding: 0.75rem 1.5rem;
			display: flex;
			align-items: center;
			gap: 0.75rem;
			transition: all 0.3s;
			border-left: 3px solid transparent;
		}

		.nav-link:hover {
			background: #f7fafc;
			color: #667eea;
			border-left-color: #667eea;
		}

		.nav-link.active {
			background: #eef2ff;
			color: #667eea;
			border-left-color: #667eea;
			font-weight: 600;
		}

		.nav-link i {
			font-size: 1.1rem;
		}

		.main-content {
			flex: 1;
			padding: 2rem;
			overflow-y: auto;
		}

		.content-wrapper {
			max-width: 1400px;
			margin: 0 auto;
		}

		.card {
			border: none;
			border-radius: 10px;
			box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
			margin-bottom: 1.5rem;
		}

		.card-header {
			background: white;
			border-bottom: 2px solid #e2e8f0;
			padding: 1.25rem 1.5rem;
			font-weight: 600;
			color: #2d3748;
		}

		.stat-card {
			border-left: 4px solid;
			transition: transform 0.3s;
		}

		.stat-card:hover {
			transform: translateY(-5px);
		}

		.stat-card.primary {
			border-left-color: #667eea;
		}

		.stat-card.success {
			border-left-color: #48bb78;
		}

		.stat-card.warning {
			border-left-color: #ed8936;
		}

		.stat-card.info {
			border-left-color: #4299e1;
		}
	</style>
</head>

<body>
	<div class="topbar">
		<span class="brand">
			<i class="bi bi-mortarboard-fill"></i> Portal Mahasiswa TI UPR
		</span>
		<div class="user-info">
			<div class="user-avatar">
				<?php
				$nama = session('nama_lengkap') ?? 'M';
				echo strtoupper(substr($nama, 0, 1));
				?>
			</div>
			<div>
				<div style="font-weight: 600;"><?= session('nama_lengkap') ?></div>
				<small><?= session('nim') ?></small>
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
						href="<?= base_url('mahasiswa/dashboard') ?>">
						<i class="bi bi-house-door"></i> Dashboard
					</a>
				</li>

				<li class="nav-item">
					<a class="nav-link<?= uri_string() == 'mahasiswa/nilai' ? ' active' : '' ?>"
						href="<?= base_url('mahasiswa/nilai') ?>">
						<i class="bi bi-file-earmark-bar-graph"></i> Nilai Saya
					</a>
				</li>

				<li class="nav-item">
					<a class="nav-link<?= uri_string() == 'mahasiswa/jadwal' ? ' active' : '' ?>"
						href="<?= base_url('mahasiswa/jadwal') ?>">
						<i class="bi bi-calendar-event"></i> Jadwal Kuliah
					</a>
				</li>

				<li class="nav-item">
					<a class="nav-link<?= uri_string() == 'mahasiswa/profil-cpl' ? ' active' : '' ?>"
						href="<?= base_url('mahasiswa/profil-cpl') ?>">
						<i class="bi bi-graph-up"></i> Profil CPL
					</a>
				</li>

				<li class="nav-item">
					<a class="nav-link<?= uri_string() == 'mahasiswa/mbkm' ? ' active' : '' ?>"
						href="<?= base_url('mahasiswa/mbkm') ?>">
						<i class="bi bi-backpack"></i> Kegiatan MBKM
					</a>
				</li>

				<li class="nav-item">
					<a class="nav-link<?= uri_string() == 'mahasiswa/profil' ? ' active' : '' ?>"
						href="<?= base_url('mahasiswa/profil') ?>">
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
		// Initialize tooltips
		var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
		var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
			return new bootstrap.Tooltip(tooltipTriggerEl)
		})
	</script>
	<?= $this->renderSection('js') ?>
</body>

</html>
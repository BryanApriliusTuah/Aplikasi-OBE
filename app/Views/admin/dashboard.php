<?= $this->extend('layouts/admin_layout') ?>

<?= $this->section('css') ?>
<style>
	.hero-banner {
		background: linear-gradient(135deg, #1e3a8a 0%, #2563eb 60%, #3b82f6 100%);
		border-radius: 1.25rem;
		padding: 2.25rem 2.5rem;
		color: #fff;
		position: relative;
		overflow: hidden;
		margin-bottom: 2rem;
	}
	.hero-banner::before {
		content: "";
		position: absolute;
		top: -40px; right: -40px;
		width: 220px; height: 220px;
		background: rgba(255,255,255,0.07);
		border-radius: 50%;
	}
	.hero-banner::after {
		content: "";
		position: absolute;
		bottom: -60px; right: 80px;
		width: 180px; height: 180px;
		background: rgba(255,255,255,0.05);
		border-radius: 50%;
	}
	.hero-banner .hero-title {
		font-size: 1.65rem;
		font-weight: 700;
		margin-bottom: 0.35rem;
	}
	.hero-banner .hero-sub {
		font-size: 0.95rem;
		opacity: 0.82;
	}
	.hero-badge {
		display: inline-flex;
		align-items: center;
		gap: 6px;
		background: rgba(255,255,255,0.18);
		border: 1px solid rgba(255,255,255,0.3);
		border-radius: 999px;
		padding: 4px 14px;
		font-size: 0.8rem;
		font-weight: 600;
		margin-bottom: 1rem;
		letter-spacing: 0.5px;
	}
	.stat-card {
		border-radius: 1.1rem;
		border: none;
		padding: 1.5rem 1.75rem;
		position: relative;
		overflow: hidden;
		transition: transform 0.22s cubic-bezier(.4,0,.2,1), box-shadow 0.22s;
		cursor: default;
	}
	.stat-card:hover {
		transform: translateY(-4px);
		box-shadow: 0 12px 28px rgba(0,0,0,0.13) !important;
	}
	.stat-card .stat-icon {
		width: 52px; height: 52px;
		border-radius: 0.85rem;
		display: flex; align-items: center; justify-content: center;
		font-size: 1.5rem;
		margin-bottom: 1rem;
	}
	.stat-card .stat-value {
		font-size: 2.2rem;
		font-weight: 800;
		line-height: 1;
		margin-bottom: 0.3rem;
	}
	.stat-card .stat-label {
		font-size: 0.875rem;
		font-weight: 500;
		opacity: 0.75;
	}
	.stat-card .stat-bg-icon {
		position: absolute;
		bottom: -12px; right: -10px;
		font-size: 5.5rem;
		opacity: 0.07;
		line-height: 1;
	}
	.stat-blue   { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #fff; }
	.stat-indigo { background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #fff; }
	.stat-cyan   { background: linear-gradient(135deg, #0891b2 0%, #0e7490 100%); color: #fff; }
	.stat-blue .stat-icon,
	.stat-indigo .stat-icon,
	.stat-cyan .stat-icon { background: rgba(255,255,255,0.2); color: #fff; }

	.quick-link-card {
		border-radius: 1rem;
		border: 1.5px solid #e8edf8;
		background: #fff;
		padding: 1.1rem 1.25rem;
		display: flex;
		align-items: center;
		gap: 1rem;
		text-decoration: none;
		color: #1e293b;
		transition: all 0.22s cubic-bezier(.4,0,.2,1);
		box-shadow: 0 1px 4px rgba(30,58,138,0.05);
	}
	.quick-link-card:hover {
		border-color: #2563eb;
		background: #f0f5ff;
		color: #1d4ed8;
		text-decoration: none;
		transform: translateY(-2px);
		box-shadow: 0 6px 18px rgba(37,99,235,0.12);
	}
	.quick-link-card .ql-icon {
		width: 42px; height: 42px;
		border-radius: 0.7rem;
		display: flex; align-items: center; justify-content: center;
		font-size: 1.25rem;
		flex-shrink: 0;
		background: #eff2ff;
		color: #2563eb;
		transition: background 0.2s;
	}
	.quick-link-card:hover .ql-icon { background: #dbeafe; }
	.quick-link-card .ql-label {
		font-size: 0.9rem;
		font-weight: 600;
		line-height: 1.3;
	}
	.quick-link-card .ql-desc {
		font-size: 0.78rem;
		color: #64748b;
		margin-top: 1px;
	}
	.section-heading {
		font-size: 1rem;
		font-weight: 700;
		color: #1e293b;
		letter-spacing: 0.2px;
		margin-bottom: 1rem;
		display: flex;
		align-items: center;
		gap: 8px;
	}
	.section-heading::after {
		content: "";
		flex: 1;
		height: 1px;
		background: linear-gradient(90deg, #e2e8f0 0%, transparent 100%);
		margin-left: 4px;
	}

	/* ── Guidelines / Timeline ── */
	.guide-timeline {
		position: relative;
		padding-left: 0;
		list-style: none;
		margin: 0;
	}
	.guide-timeline::before {
		content: "";
		position: absolute;
		left: 23px;
		top: 28px;
		bottom: 28px;
		width: 2px;
		background: linear-gradient(180deg, #dbeafe 0%, #e0e7ff 50%, #cffafe 100%);
		border-radius: 2px;
	}
	.guide-stage {
		position: relative;
		margin-bottom: 10px;
	}
	.guide-stage-header {
		display: flex;
		align-items: center;
		gap: 14px;
		padding: 0;
		cursor: pointer;
		user-select: none;
		background: none;
		border: none;
		width: 100%;
		text-align: left;
	}
	.guide-stage-num {
		width: 46px; height: 46px;
		border-radius: 50%;
		display: flex; align-items: center; justify-content: center;
		font-size: 1rem;
		font-weight: 800;
		flex-shrink: 0;
		position: relative;
		z-index: 1;
		transition: transform 0.2s;
		box-shadow: 0 2px 8px rgba(0,0,0,0.10);
	}
	.guide-stage-header:hover .guide-stage-num { transform: scale(1.08); }
	.guide-stage-title {
		font-size: 0.95rem;
		font-weight: 700;
		color: #1e293b;
		flex: 1;
	}
	.guide-stage-subtitle {
		font-size: 0.78rem;
		font-weight: 400;
		color: #64748b;
		margin-top: 1px;
	}
	.guide-stage-caret {
		font-size: 1.1rem;
		color: #94a3b8;
		transition: transform 0.25s cubic-bezier(.4,0,.2,1);
		margin-right: 2px;
	}
	.guide-stage.open .guide-stage-caret { transform: rotate(180deg); }

	.guide-stage-body {
		display: grid;
		grid-template-rows: 0fr;
		transition: grid-template-rows 0.3s cubic-bezier(.4,0,.2,1);
		padding-left: 60px;
	}
	.guide-stage-body > div { overflow: hidden; }
	.guide-stage.open .guide-stage-body { grid-template-rows: 1fr; }

	.guide-body-inner {
		padding: 14px 0 8px 0;
	}
	.guide-menu-list {
		list-style: none;
		padding: 0; margin: 0;
		display: flex;
		flex-direction: column;
		gap: 8px;
	}
	.guide-menu-item {
		display: flex;
		gap: 12px;
		align-items: flex-start;
		background: #fff;
		border: 1.5px solid #e8edf8;
		border-radius: 0.85rem;
		padding: 12px 14px;
		transition: border-color 0.18s, box-shadow 0.18s;
	}
	.guide-menu-item:hover {
		border-color: #bfdbfe;
		box-shadow: 0 2px 10px rgba(37,99,235,0.07);
	}
	.guide-menu-icon {
		width: 34px; height: 34px;
		border-radius: 0.6rem;
		display: flex; align-items: center; justify-content: center;
		font-size: 1rem;
		flex-shrink: 0;
		margin-top: 1px;
	}
	.guide-menu-name {
		font-size: 0.875rem;
		font-weight: 700;
		color: #1e293b;
		line-height: 1.3;
	}
	.guide-menu-desc {
		font-size: 0.8rem;
		color: #64748b;
		margin-top: 3px;
		line-height: 1.5;
	}
	.guide-menu-tip {
		display: inline-flex;
		align-items: center;
		gap: 4px;
		font-size: 0.72rem;
		font-weight: 600;
		color: #2563eb;
		background: #eff6ff;
		border-radius: 999px;
		padding: 2px 9px;
		margin-top: 5px;
	}
	.guide-menu-req {
		display: inline-flex;
		align-items: center;
		gap: 4px;
		font-size: 0.72rem;
		font-weight: 600;
		color: #92400e;
		background: #fef3c7;
		border: 1px solid #fde68a;
		border-radius: 999px;
		padding: 2px 9px;
		margin-top: 5px;
		margin-right: 4px;
	}
	.guide-menu-badges {
		display: flex;
		flex-wrap: wrap;
		gap: 4px;
		margin-top: 6px;
	}
	/* Stage color palettes */
	.sn-1 { background: #dbeafe; color: #1d4ed8; }
	.sb-1 { background: #eff6ff; }
	.si-1 { background: #dbeafe; color: #2563eb; }

	.sn-2 { background: #e0e7ff; color: #4338ca; }
	.sb-2 { background: #f5f3ff; }
	.si-2 { background: #e0e7ff; color: #4f46e5; }

	.sn-3 { background: #fce7f3; color: #be185d; }
	.sb-3 { background: #fdf2f8; }
	.si-3 { background: #fce7f3; color: #db2777; }

	.sn-4 { background: #d1fae5; color: #065f46; }
	.sb-4 { background: #f0fdf4; }
	.si-4 { background: #d1fae5; color: #059669; }

	.sn-5 { background: #ffedd5; color: #9a3412; }
	.sb-5 { background: #fff7ed; }
	.si-5 { background: #ffedd5; color: #ea580c; }

	.sn-6 { background: #cffafe; color: #164e63; }
	.sb-6 { background: #ecfeff; }
	.si-6 { background: #cffafe; color: #0e7490; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Hero Banner -->
<div class="hero-banner shadow-sm">
	<div class="hero-badge">
		<i class="bi bi-mortarboard-fill"></i> Sistem Kurikulum OBE TI UPR
	</div>
	<div class="hero-title">Selamat Datang, <?= esc(session('nama_lengkap') ?? session('username')) ?></div>
	<div class="hero-sub">Kelola kurikulum berbasis OBE Program Studi Teknik Informatika Universitas Palangka Raya.</div>
	<div class="d-flex gap-2 flex-wrap mt-3">
		<button id="btn-start-tour" onclick="startTour()" class="btn btn-light btn-sm" style="font-weight:600; border-radius:999px; padding:6px 18px; display:inline-flex; align-items:center; gap:6px;">
			<i class="bi bi-play-circle-fill text-primary"></i> Panduan Singkat
		</button>
		<button onclick="startFullTour()" class="btn btn-sm" style="font-weight:600; border-radius:999px; padding:6px 18px; display:inline-flex; align-items:center; gap:6px; background:rgba(255,255,255,0.18); color:#fff; border:1px solid rgba(255,255,255,0.4);">
			<i class="bi bi-map-fill"></i> Panduan Lengkap
		</button>
	</div>
</div>

<!-- Stat Cards -->
<div id="tour-stat-cards" class="row g-3 mb-4">
	<div class="col-sm-6 col-lg-4">
		<div class="stat-card stat-blue shadow-sm h-100">
			<div class="stat-icon"><i class="bi bi-people-fill"></i></div>
			<div class="stat-value"><?= $total_dosen ?></div>
			<div class="stat-label">Akun Dosen Terdaftar</div>
			<i class="bi bi-people-fill stat-bg-icon"></i>
		</div>
	</div>
	<div class="col-sm-6 col-lg-4">
		<div class="stat-card stat-indigo shadow-sm h-100">
			<div class="stat-icon"><i class="bi bi-journal-bookmark-fill"></i></div>
			<div class="stat-value"><?= $total_mk ?></div>
			<div class="stat-label">Mata Kuliah</div>
			<i class="bi bi-journal-bookmark-fill stat-bg-icon"></i>
		</div>
	</div>
	<div class="col-sm-6 col-lg-4">
		<div class="stat-card stat-cyan shadow-sm h-100">
			<div class="stat-icon"><i class="bi bi-book-fill"></i></div>
			<div class="stat-value"><?= $total_rps ?></div>
			<div class="stat-label">RPS Tersedia</div>
			<i class="bi bi-book-fill stat-bg-icon"></i>
		</div>
	</div>
</div>

<!-- Quick Links -->
<div id="tour-quick-links" class="section-heading"><i class="bi bi-lightning-charge-fill text-warning"></i> Akses Cepat</div>
<div class="row g-3">
	<div class="col-sm-6 col-lg-4">
		<a href="<?= base_url('admin/mata-kuliah') ?>" class="quick-link-card">
			<div class="ql-icon"><i class="bi bi-journal-bookmark"></i></div>
			<div>
				<div class="ql-label">Mata Kuliah</div>
				<div class="ql-desc">Kelola daftar mata kuliah</div>
			</div>
		</a>
	</div>
	<div class="col-sm-6 col-lg-4">
		<a href="<?= base_url('admin/cpl') ?>" class="quick-link-card">
			<div class="ql-icon"><i class="bi bi-bullseye"></i></div>
			<div>
				<div class="ql-label">CPL</div>
				<div class="ql-desc">Capaian Pembelajaran Lulusan</div>
			</div>
		</a>
	</div>
	<div class="col-sm-6 col-lg-4">
		<a href="<?= base_url('rps') ?>" class="quick-link-card">
			<div class="ql-icon"><i class="bi bi-book"></i></div>
			<div>
				<div class="ql-label">RPS</div>
				<div class="ql-desc">Rencana Pembelajaran Semester</div>
			</div>
		</a>
	</div>
	<div class="col-sm-6 col-lg-4">
		<a href="<?= base_url('admin/cpmk') ?>" class="quick-link-card">
			<div class="ql-icon"><i class="bi bi-check2-circle"></i></div>
			<div>
				<div class="ql-label">CPMK</div>
				<div class="ql-desc">Capaian Pembelajaran Mata Kuliah</div>
			</div>
		</a>
	</div>
	<div class="col-sm-6 col-lg-4">
		<a href="<?= base_url('admin/capaian-cpl') ?>" class="quick-link-card">
			<div class="ql-icon"><i class="bi bi-bar-chart-line"></i></div>
			<div>
				<div class="ql-label">Profil Capaian CPL</div>
				<div class="ql-desc">Lihat capaian CPL mahasiswa</div>
			</div>
		</a>
	</div>
	<div class="col-sm-6 col-lg-4">
		<a href="<?= base_url('admin/dosen') ?>" class="quick-link-card">
			<div class="ql-icon"><i class="bi bi-person-badge"></i></div>
			<div>
				<div class="ql-label">Data Dosen</div>
				<div class="ql-desc">Kelola data dosen</div>
			</div>
		</a>
	</div>
</div>

<!-- Guidelines -->
<div id="tour-guide-heading" class="section-heading mt-4"><i class="bi bi-map text-primary"></i> Panduan Penggunaan Sistem</div>
<p class="text-muted small mb-3">Ikuti tahapan berikut secara berurutan agar semua fitur sistem dapat berjalan dengan benar.</p>

<ul class="guide-timeline">

	<!-- Stage 1 -->
	<li class="guide-stage" id="gs1">
		<button class="guide-stage-header" onclick="toggleGuide('gs1')">
			<div class="guide-stage-num sn-1">1</div>
			<div class="flex-grow-1">
				<div class="guide-stage-title">Setup Awal</div>
				<div class="guide-stage-subtitle">Konfigurasi dasar sistem sebelum mengisi data kurikulum</div>
			</div>
			<i class="bi bi-chevron-down guide-stage-caret"></i>
		</button>
		<div class="guide-stage-body">
			<div><div class="guide-body-inner">
				<ul class="guide-menu-list">
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-1"><i class="bi bi-calendar3"></i></div>
						<div>
							<div class="guide-menu-name">Tahun Akademik</div>
							<div class="guide-menu-desc">Buat data tahun akademik (cth: 2024 Ganjil, 2024 Genap). Tahun akademik terbaru akan menjadi default filter di seluruh sistem.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-slash-circle"></i> Tidak ada syarat — langkah pertama</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Master Data → Tahun Akademik</span>
							</div>
						</div>
					</li>
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-1"><i class="bi bi-gear"></i></div>
						<div>
							<div class="guide-menu-name">Pengaturan</div>
							<div class="guide-menu-desc">Tentukan rentang nilai huruf (A–E), persentase ambang kelulusan CPMK, dan ambang kelulusan CPL. Konfigurasi ini digunakan dalam perhitungan capaian.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-slash-circle"></i> Tidak ada syarat — lakukan di awal</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Pengaturan</span>
							</div>
						</div>
					</li>
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-1"><i class="bi bi-people"></i></div>
						<div>
							<div class="guide-menu-name">Sinkronisasi Data Dosen & Mahasiswa</div>
							<div class="guide-menu-desc">Lakukan sinkronisasi data dosen dan mahasiswa dari API SIUBER kampus agar data selalu <i>up-to-date</i> sebelum membuat RPS ataupun jadwal mengajar.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-slash-circle"></i> Tidak ada syarat</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Master Data → Data Dosen / Data Mahasiswa → Sinkronisasi</span>
							</div>
						</div>
					</li>
				</ul>
			</div></div>
		</div>
	</li>

	<!-- Stage 2 -->
	<li class="guide-stage" id="gs2">
		<button class="guide-stage-header" onclick="toggleGuide('gs2')">
			<div class="guide-stage-num sn-2">2</div>
			<div class="flex-grow-1">
				<div class="guide-stage-title">Struktur Kurikulum</div>
				<div class="guide-stage-subtitle">Definisikan komponen-komponen utama kurikulum OBE</div>
			</div>
			<i class="bi bi-chevron-down guide-stage-caret"></i>
		</button>
		<div class="guide-stage-body">
			<div><div class="guide-body-inner">
				<ul class="guide-menu-list">
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-2"><i class="bi bi-award"></i></div>
						<div>
							<div class="guide-menu-name">Profil Lulusan (PL)</div>
							<div class="guide-menu-desc">Tentukan profil/peran yang diharapkan dari lulusan program studi (cth: PL01 – Pengembang Perangkat Lunak). Profil ini menjadi landasan penyusunan CPL.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-slash-circle"></i> Tidak ada syarat</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Master Data → Profil Lulusan</span>
							</div>
						</div>
					</li>
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-2"><i class="bi bi-bullseye"></i></div>
						<div>
							<div class="guide-menu-name">CPL – Capaian Pembelajaran Lulusan</div>
							<div class="guide-menu-desc">Buat rumusan kemampuan yang harus dikuasai lulusan (cth: CPL01 – Mampu menganalisis masalah komputasi). CPL merupakan acuan utama seluruh asesmen.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-slash-circle"></i> Tidak ada syarat</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Master Data → CPL</span>
							</div>
						</div>
					</li>
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-2"><i class="bi bi-collection"></i></div>
						<div>
							<div class="guide-menu-name">Bahan Kajian (BK)</div>
							<div class="guide-menu-desc">Definisikan topik/modul bahan kajian kurikulum (cth: BK01 – Algoritma dan Pemrograman). BK menjadi jembatan antara CPL dan Mata Kuliah.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-slash-circle"></i> Tidak ada syarat</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Master Data → Bahan Kajian</span>
							</div>
						</div>
					</li>
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-2"><i class="bi bi-journal-bookmark"></i></div>
						<div>
							<div class="guide-menu-name">Mata Kuliah (MK)</div>
							<div class="guide-menu-desc">Daftarkan mata kuliah beserta semester, jumlah SKS, dan prasyarat. Gunakan tombol Sinkronisasi untuk mengambil data MK dari API kampus.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-slash-circle"></i> Tidak ada syarat</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Master Data → Mata Kuliah</span>
							</div>
						</div>
					</li>
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-2"><i class="bi bi-check2-circle"></i></div>
						<div>
							<div class="guide-menu-name">CPMK – Capaian Pembelajaran Mata Kuliah</div>
							<div class="guide-menu-desc">Buat capaian yang lebih spesifik per mata kuliah (cth: CPMK01 – Mampu merancang algoritma). CPMK adalah turunan CPL pada level mata kuliah.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-slash-circle"></i> Tidak ada syarat</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Master Data → CPMK</span>
							</div>
						</div>
					</li>
				</ul>
			</div></div>
		</div>
	</li>

	<!-- Stage 3 -->
	<li class="guide-stage" id="gs3">
		<button class="guide-stage-header" onclick="toggleGuide('gs3')">
			<div class="guide-stage-num sn-3">3</div>
			<div class="flex-grow-1">
				<div class="guide-stage-title">Pemetaan Kurikulum</div>
				<div class="guide-stage-subtitle">Hubungkan setiap komponen kurikulum satu sama lain</div>
			</div>
			<i class="bi bi-chevron-down guide-stage-caret"></i>
		</button>
		<div class="guide-stage-body">
			<div><div class="guide-body-inner">
				<ul class="guide-menu-list">
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-3"><i class="bi bi-diagram-2"></i></div>
						<div>
							<div class="guide-menu-name">CPL ke Profil Lulusan</div>
							<div class="guide-menu-desc">Hubungkan setiap CPL dengan Profil Lulusan yang relevan agar terlihat kontribusi CPL terhadap kompetensi lulusan.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-link-45deg"></i> Syarat: CPL & Profil Lulusan sudah ada</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Pemetaan → CPL ke PL</span>
							</div>
						</div>
					</li>
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-3"><i class="bi bi-diagram-2"></i></div>
						<div>
							<div class="guide-menu-name">CPL ke Bahan Kajian</div>
							<div class="guide-menu-desc">Petakan CPL ke Bahan Kajian untuk memastikan setiap CPL didukung oleh materi yang memadai.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-link-45deg"></i> Syarat: CPL & Bahan Kajian sudah ada</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Pemetaan → CPL ke BK</span>
							</div>
						</div>
					</li>
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-3"><i class="bi bi-diagram-2"></i></div>
						<div>
							<div class="guide-menu-name">Bahan Kajian ke Mata Kuliah</div>
							<div class="guide-menu-desc">Tentukan Bahan Kajian mana yang diajarkan pada Mata Kuliah tertentu. Satu BK bisa digunakan di lebih dari satu MK.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-link-45deg"></i> Syarat: Bahan Kajian & Mata Kuliah sudah ada</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Pemetaan → BK ke MK</span>
							</div>
						</div>
					</li>
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-3"><i class="bi bi-diagram-3"></i></div>
						<div>
							<div class="guide-menu-name">CPL ke Mata Kuliah</div>
							<div class="guide-menu-desc">Hubungkan langsung CPL ke Mata Kuliah yang berkontribusi terhadap pencapaian CPL tersebut.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-link-45deg"></i> Syarat: CPL & Mata Kuliah sudah ada</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Pemetaan → CPL ke MK</span>
							</div>
						</div>
					</li>
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-3"><i class="bi bi-diagram-3-fill"></i></div>
						<div>
							<div class="guide-menu-name">Pemetaan CPL – MK – CPMK</div>
							<div class="guide-menu-desc">Pemetaan inti tiga arah: CPL mana yang dicapai oleh MK tertentu melalui CPMK spesifik.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-link-45deg"></i> Syarat: CPL→MK & CPMK sudah dipetakan</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Pemetaan → CPL–CPMK–MK</span>
							</div>
						</div>
					</li>
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-3"><i class="bi bi-node-plus"></i></div>
						<div>
							<div class="guide-menu-name">Pemetaan MK – CPMK – Sub-CPMK</div>
							<div class="guide-menu-desc">Pecah CPMK menjadi Sub-CPMK yang lebih granular per Mata Kuliah. Digunakan sebagai dasar teknik penilaian.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-link-45deg"></i> Syarat: Pemetaan CPL–MK–CPMK selesai</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Pemetaan → MK–CPMK–SubCPMK</span>
							</div>
						</div>
					</li>
				</ul>
			</div></div>
		</div>
	</li>

	<!-- Stage 4 -->
	<li class="guide-stage" id="gs4">
		<button class="guide-stage-header" onclick="toggleGuide('gs4')">
			<div class="guide-stage-num sn-4">4</div>
			<div class="flex-grow-1">
				<div class="guide-stage-title">RPS & Konfigurasi Asesmen</div>
				<div class="guide-stage-subtitle">Susun rencana pembelajaran dan bobot penilaian tiap mata kuliah</div>
			</div>
			<i class="bi bi-chevron-down guide-stage-caret"></i>
		</button>
		<div class="guide-stage-body">
			<div><div class="guide-body-inner">
				<ul class="guide-menu-list">
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-4"><i class="bi bi-book"></i></div>
						<div>
							<div class="guide-menu-name">RPS – Rencana Pembelajaran Semester</div>
							<div class="guide-menu-desc">Buat dokumen RPS per mata kuliah: isi rencana mingguan, referensi, dan detail pembelajaran. Ekspor ke PDF atau Word tersedia.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-link-45deg"></i> Syarat: Pemetaan CPL–MK–CPMK selesai</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> RPS</span>
							</div>
						</div>
					</li>
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-4"><i class="bi bi-clipboard-check"></i></div>
						<div>
							<div class="guide-menu-name">Teknik Penilaian CPMK</div>
							<div class="guide-menu-desc">Tentukan cara pengukuran setiap CPMK: ujian tulis, tugas, presentasi, praktikum, dsb. Dasar untuk input nilai nantinya.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-link-45deg"></i> Syarat: Pemetaan MK–CPMK–SubCPMK selesai</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Asesmen Pembelajaran → Teknik Penilaian CPMK</span>
							</div>
						</div>
					</li>
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-4"><i class="bi bi-list-ol"></i></div>
						<div>
							<div class="guide-menu-name">Tahap & Mekanisme Penilaian</div>
							<div class="guide-menu-desc">Atur urutan tahapan penilaian dalam satu semester (UTS, UAS, tugas harian, dll.) beserta persentase kontribusinya.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-link-45deg"></i> Syarat: Teknik Penilaian CPMK sudah diisi</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Asesmen Pembelajaran → Tahap dan Mekanisme Penilaian</span>
							</div>
						</div>
					</li>
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-4"><i class="bi bi-percent"></i></div>
						<div>
							<div class="guide-menu-name">Bobot Penilaian CPL & MK</div>
							<div class="guide-menu-desc">Tetapkan bobot persentase kontribusi setiap MK/CPMK terhadap CPL. Pastikan total bobot = 100% sebelum input nilai.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-link-45deg"></i> Syarat: Pemetaan CPL–MK & Teknik Penilaian selesai</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Asesmen Pembelajaran → Bobot Penilaian CPL / Bobot Penilaian MK</span>
							</div>
						</div>
					</li>
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-4"><i class="bi bi-calculator"></i></div>
						<div>
							<div class="guide-menu-name">Rumusan Nilai Akhir MK & CPL</div>
							<div class="guide-menu-desc">Definisikan formula kalkulasi nilai akhir Mata Kuliah dari CPMK, dan formula nilai CPL dari nilai-nilai MK yang berkontribusi.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-link-45deg"></i> Syarat: Bobot Penilaian CPL & MK sudah diisi</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Asesmen Pembelajaran → Rumusan Nilai Akhir MK / CPL</span>
							</div>
						</div>
					</li>
				</ul>
			</div></div>
		</div>
	</li>

	<!-- Stage 5 -->
	<li class="guide-stage" id="gs5">
		<button class="guide-stage-header" onclick="toggleGuide('gs5')">
			<div class="guide-stage-num sn-5">5</div>
			<div class="flex-grow-1">
				<div class="guide-stage-title">Operasional Akademik</div>
				<div class="guide-stage-subtitle">Kelola jadwal mengajar, input nilai, dan konversi MBKM</div>
			</div>
			<i class="bi bi-chevron-down guide-stage-caret"></i>
		</button>
		<div class="guide-stage-body">
			<div><div class="guide-body-inner">
				<ul class="guide-menu-list">
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-5"><i class="bi bi-journal-text"></i></div>
						<div>
							<div class="guide-menu-name">Mengajar – Jadwal & Kelas</div>
							<div class="guide-menu-desc">Buat jadwal mengajar per semester. Dilakukan dengan mensinkronisasi data dosen dan mahasiswa dari API SIUBER kampus.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-link-45deg"></i> Syarat: RPS sudah dibuat, Dosen & Mahasiswa sudah disinkronisasi</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Mengajar</span>
							</div>
						</div>
					</li>
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-5"><i class="bi bi-input-cursor-text"></i></div>
						<div>
							<div class="guide-menu-name">Nilai – Input Nilai Mahasiswa</div>
							<div class="guide-menu-desc">Input nilai per CPMK berdasarkan teknik penilaian untuk setiap mahasiswa pada jadwal yang sudah dibuat. Nilai dapat diisi melalui form input atau diimpor dari Excel menggunakan fitur unggah. Dosen hanya dapat menginput nilai untuk mahasiswa yang telah terdaftar dalam kelas mereka.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-link-45deg"></i> Syarat: Jadwal Mengajar & Mahasiswa di kelas sudah ada, Rumusan Nilai Akhir sudah diisi</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Nilai</span>
							</div>
						</div>
					</li>
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-5"><i class="bi bi-backpack"></i></div>
						<div>
							<div class="guide-menu-name">MBKM – Merdeka Belajar Kampus Merdeka</div>
							<div class="guide-menu-desc">Konversi kegiatan MBKM mahasiswa (magang, penelitian, pertukaran, dll.). User melakukan sinkronisasi data SIUBER untuk memperoleh detail MBKM (program, sub-program, tujuan) dan mata kuliah yang dikonversi mahasiswa. Setelah sinkronisasi selesai, nilai CPMK dapat diinput.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-link-45deg"></i> Syarat: RPS sudah dibuat</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> MBKM</span>
							</div>
						</div>
					</li>
				</ul>
			</div></div>
		</div>
	</li>

	<!-- Stage 6 -->
	<li class="guide-stage" id="gs6">
		<button class="guide-stage-header" onclick="toggleGuide('gs6')">
			<div class="guide-stage-num sn-6">6</div>
			<div class="flex-grow-1">
				<div class="guide-stage-title">Analisis & Pelaporan</div>
				<div class="guide-stage-subtitle">Evaluasi capaian dan hasilkan laporan portofolio kurikulum</div>
			</div>
			<i class="bi bi-chevron-down guide-stage-caret"></i>
		</button>
		<div class="guide-stage-body">
			<div><div class="guide-body-inner">
				<ul class="guide-menu-list">
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-6"><i class="bi bi-table"></i></div>
						<div>
							<div class="guide-menu-name">Matriks Pemetaan</div>
							<div class="guide-menu-desc">Lihat rekapitulasi pemetaan dalam bentuk matriks: Organisasi MK per semester, Peta CPL, hubungan CPL–BK–MK, dan pemenuhan CPL–CPMK–MK.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-link-45deg"></i> Syarat: Seluruh pemetaan (Tahap 3) selesai</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Matriks Pemetaan</span>
							</div>
						</div>
					</li>
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-6"><i class="bi bi-bar-chart"></i></div>
						<div>
							<div class="guide-menu-name">Capaian CPMK</div>
							<div class="guide-menu-desc">Analisis pemenuhan CPMK per mata kuliah dan per mahasiswa. Bandingkan capaian antar angkatan dan identifikasi CPMK yang belum memenuhi ambang batas.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-link-45deg"></i> Syarat: Nilai mahasiswa sudah diinput</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Profil Capaian → Capaian CPMK</span>
							</div>
						</div>
					</li>
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-6"><i class="bi bi-graph-up-arrow"></i></div>
						<div>
							<div class="guide-menu-name">Capaian CPL</div>
							<div class="guide-menu-desc">Evaluasi pemenuhan CPL tingkat program studi. Identifikasi mahasiswa dan CPL yang masih di bawah ambang, serta bandingkan tren antar semester.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-link-45deg"></i> Syarat: Capaian CPMK sudah tersedia (nilai sudah diinput)</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Profil Capaian → Capaian CPL</span>
							</div>
						</div>
					</li>
					<li class="guide-menu-item">
						<div class="guide-menu-icon si-6"><i class="bi bi-file-earmark-bar-graph"></i></div>
						<div>
							<div class="guide-menu-name">Portofolio CPMK & CPL</div>
							<div class="guide-menu-desc">Hasilkan laporan portofolio resmi per mata kuliah (CPMK) dan per program studi (CPL). Tambahkan catatan analisis, rencana CQI, unggah rubrik/notulen, lalu ekspor ke PDF atau ZIP.</div>
							<div class="guide-menu-badges">
								<span class="guide-menu-req"><i class="bi bi-link-45deg"></i> Syarat: Capaian CPMK & Capaian CPL sudah dapat dilihat</span>
								<span class="guide-menu-tip"><i class="bi bi-arrow-right-circle"></i> Portofolio → Portofolio CPMK / Portofolio CPL</span>
							</div>
						</div>
					</li>
				</ul>
			</div></div>
		</div>
	</li>

</ul>

<?= $this->endSection() ?>

<?= $this->section('js') ?>
<script>
	function toggleGuide(id) {
		var el = document.getElementById(id);
		el.classList.toggle('open');
	}

	function startTour() {
		// Open all guide stages so Driver.js can highlight them
		['gs1','gs2','gs3','gs4','gs5','gs6'].forEach(function(id) {
			var el = document.getElementById(id);
			if (el && !el.classList.contains('open')) el.classList.add('open');
		});

		var driver = window.driver.js.driver({
			showProgress: true,
			animate: true,
			overlayColor: '#000',
			overlayOpacity: 0.6,
			smoothScroll: true,
			allowClose: true,
			nextBtnText: 'Lanjut →',
			prevBtnText: '← Kembali',
			doneBtnText: 'Selesai',
			progressText: 'Langkah {{current}} dari {{total}}',
			popoverClass: 'driverjs-theme',
			steps: [
				{
					element: '#btn-start-tour',
					popover: {
						title: '<i class="bi bi-mortarboard-fill text-primary"></i> Selamat Datang di Tutorial!',
						description: 'Tutorial ini akan memandu Anda mengenal fitur-fitur utama sistem OBE TI UPR. Klik <b>Lanjut</b> untuk memulai.',
						side: 'bottom',
						align: 'start'
					}
				},
				{
					element: '#tour-stat-cards',
					popover: {
						title: '<i class="bi bi-bar-chart-fill text-info"></i> Statistik Sistem',
						description: 'Panel ini menampilkan ringkasan data sistem secara real-time: jumlah dosen terdaftar, mata kuliah, dan RPS yang tersedia.',
						side: 'bottom',
						align: 'start'
					}
				},
				{
					element: '#tour-quick-links',
					popover: {
						title: '<i class="bi bi-lightning-charge-fill text-warning"></i> Akses Cepat',
						description: 'Gunakan kartu pintasan ini untuk langsung menuju halaman yang paling sering digunakan: Mata Kuliah, CPL, RPS, CPMK, dan lainnya.',
						side: 'bottom',
						align: 'start'
					}
				},
				{
					element: '#tour-guide-heading',
					popover: {
						title: '<i class="bi bi-map text-primary"></i> Panduan Penggunaan Sistem',
						description: 'Bagian ini berisi panduan langkah demi langkah penggunaan sistem. Ada <b>6 tahapan</b> yang harus diikuti secara berurutan.',
						side: 'bottom',
						align: 'start'
					}
				},
				{
					element: '#gs1',
					popover: {
						title: '<i class="bi bi-1-circle-fill text-primary"></i> Tahap 1: Setup Awal',
						description: 'Mulai dari sini. Buat <b>Tahun Akademik</b>, konfigurasi pengaturan sistem, lalu sinkronisasi data dosen & mahasiswa dari API. Ini adalah fondasi sebelum mengisi data kurikulum.',
						side: 'bottom',
						align: 'start'
					}
				},
				{
					element: '#gs2',
					popover: {
						title: '<i class="bi bi-2-circle-fill text-primary"></i> Tahap 2: Struktur Kurikulum',
						description: 'Isi data kurikulum: <b>Profil Lulusan (PL)</b>, <b>CPL</b>, <b>Bahan Kajian</b>, <b>Mata Kuliah</b>, dan <b>CPMK</b>. Ikuti urutan ini agar relasi antar data terbentuk dengan benar.',
						side: 'bottom',
						align: 'start'
					}
				},
				{
					element: '#gs3',
					popover: {
						title: '<i class="bi bi-3-circle-fill text-primary"></i> Tahap 3: Pemetaan Kurikulum',
						description: 'Setelah struktur lengkap, petakan relasi antar komponen: <b>CPL → PL</b>, <b>CPL → BK</b>, <b>BK → MK</b>, hingga <b>CPL – MK – CPMK</b>. Pemetaan ini menjadi dasar analisis capaian.',
						side: 'bottom',
						align: 'start'
					}
				},
				{
					element: '#gs4',
					popover: {
						title: '<i class="bi bi-4-circle-fill text-primary"></i> Tahap 4: RPS & Asesmen',
						description: 'Buat <b>RPS</b> untuk setiap mata kuliah, tentukan teknik penilaian CPMK, atur tahap & mekanisme penilaian, dan tetapkan bobot nilai CPL & MK.',
						side: 'bottom',
						align: 'start'
					}
				},
				{
					element: '#gs5',
					popover: {
						title: '<i class="bi bi-5-circle-fill text-primary"></i> Tahap 5: Operasional Akademik',
						description: 'Kelola jadwal mengajar, input nilai mahasiswa, dan data MBKM. Tahap ini dilakukan setiap semester berjalan.',
						side: 'bottom',
						align: 'start'
					}
				},
				{
					element: '#gs6',
					popover: {
						title: '<i class="bi bi-6-circle-fill text-primary"></i> Tahap 6: Analisis & Pelaporan',
						description: 'Lihat hasil akhir berupa <b>Matriks Pemetaan</b>, <b>Capaian CPMK</b>, <b>Capaian CPL</b>, dan <b>Portofolio</b>. Data hanya tersedia jika semua tahap sebelumnya sudah lengkap.',
						side: 'top',
						align: 'start'
					}
				},
				{
					popover: {
						title: '<i class="bi bi-check-circle-fill text-success"></i> Tutorial Dashboard Selesai!',
						description: 'Anda sudah mengenal struktur utama sistem. Gunakan tombol <b>Tour Per Halaman</b> di hero banner untuk memulai tutorial interaktif yang masuk ke setiap menu secara berurutan, mulai dari Tahun Akademik.',
					}
				}
			]
		});

		driver.drive();
		localStorage.setItem('obe_tour_seen', '1');
	}

	function startFullTour() {
		<?php if (session('role') === 'admin'): ?>
		window.location.href = OBE_TOURS.resolveUrl('admin/tahun-akademik') + '?tour=1&chain=1';
		<?php else: ?>
		window.location.href = OBE_TOURS.resolveUrl('/rps') + '?tour=1&chain=1';
		<?php endif; ?>
	}

	// Auto-start tour for first-time visitors
	document.addEventListener('DOMContentLoaded', function() {
		if (!localStorage.getItem('obe_tour_seen')) {
			setTimeout(startTour, 800);
		}
	});
</script>
<?= $this->endSection() ?>

<!DOCTYPE html>
<html lang="id">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>ERD - Sistem OBE</title>
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
	<style>
		*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

		body {
			font-family: system-ui, -apple-system, sans-serif;
			background: #f0f2f5;
			height: 100vh;
			display: flex;
			flex-direction: column;
			overflow: hidden;
		}

		/* ── Top bar ─────────────────────────────────────────── */
		.erd-topbar {
			display: flex;
			align-items: center;
			justify-content: space-between;
			padding: 10px 20px;
			background: #fff;
			border-bottom: 1px solid #e5e7eb;
			flex-shrink: 0;
			gap: 12px;
			flex-wrap: wrap;
		}

		.erd-title h4 {
			font-size: 1rem;
			font-weight: 600;
			color: #111827;
			margin-bottom: 2px;
		}
		.erd-title p {
			font-size: 0.75rem;
			color: #6b7280;
		}

		/* ── Controls ────────────────────────────────────────── */
		.erd-controls {
			display: flex;
			align-items: center;
			gap: 8px;
			flex-wrap: wrap;
		}

		.erd-legend {
			display: flex;
			gap: 14px;
			align-items: center;
			font-size: 0.75rem;
			color: #6b7280;
		}
		.erd-legend-item { display: flex; align-items: center; gap: 5px; }
		.erd-legend-box {
			width: 13px; height: 13px;
			border-radius: 3px;
			border: 1px solid #ccc;
			flex-shrink: 0;
		}

		#erd-zoom-level {
			min-width: 46px;
			text-align: center;
			font-size: 0.8rem;
			color: #6b7280;
		}

		.btn {
			display: inline-flex;
			align-items: center;
			gap: 4px;
			padding: 5px 10px;
			border-radius: 6px;
			border: 1px solid #d1d5db;
			background: #fff;
			color: #374151;
			font-size: 0.8rem;
			cursor: pointer;
			line-height: 1;
			transition: background .15s, border-color .15s;
		}
		.btn:hover { background: #f9fafb; }
		.btn-primary { background: #3b82f6; border-color: #3b82f6; color: #fff; }
		.btn-primary:hover { background: #2563eb; border-color: #2563eb; }

		/* ── Diagram area ─────────────────────────────────────── */
		.erd-wrapper {
			flex: 1;
			overflow: auto;
			padding: 20px;
		}

		.mermaid {
			transform-origin: top left;
			transition: transform 0.2s ease;
			display: inline-block;
		}

	</style>
</head>
<body>

<!-- Top bar -->
<div class="erd-topbar">
	<div class="erd-title">
		<h4>Entity Relationship Diagram</h4>
		<p>Struktur basis data sistem OBE</p>
	</div>

	<div class="erd-controls">
		<div class="erd-legend">
			<div class="erd-legend-item">
				<div class="erd-legend-box" style="background:#ECECFF;border-color:#9370DB;"></div>
				Master Data / Konfigurasi
			</div>
			<div class="erd-legend-item">
				<div class="erd-legend-box" style="background:#ca8a04;border-color:#eab308;"></div>
				Data Operasional Mahasiswa
			</div>
		</div>

		<button class="btn" id="btn-zoom-out"><i class="bi bi-zoom-out"></i></button>
		<span id="erd-zoom-level">100%</span>
		<button class="btn" id="btn-zoom-in"><i class="bi bi-zoom-in"></i></button>
		<button class="btn" id="btn-zoom-reset">Reset</button>
		<button class="btn btn-primary" id="btn-print"><i class="bi bi-camera"></i> Screenshot</button>
	</div>
</div>

<!-- Diagram -->
<div class="erd-wrapper" id="erd-container">
	<div class="mermaid" id="erd-diagram">erDiagram

  users {
    int id PK
    varchar nama
    varchar email
    varchar password
    enum role "admin/dosen/mahasiswa/kaprodi"
    datetime created_at
    datetime updated_at
  }

  fakultas {
    int kode PK
    varchar nama_singkat
    varchar nama_resmi
    varchar nip_dekan
    varchar nama_dekan
  }

  program_studi {
    int kode PK
    varchar nama
    varchar nama_singkat
    varchar jenjang
    int fakultas_kode FK
    datetime created_at
    datetime updated_at
  }

  dosen {
    int id PK
    int user_id FK
    varchar nip
    varchar nama_lengkap
    varchar email
    varchar jabatan_fungsional
    enum status_keaktifan "Aktif/Tidak Aktif"
    varchar program_studi_kode FK
    datetime created_at
    datetime updated_at
  }

  profil_prodi {
    int id PK
    varchar program_studi
    text deskripsi
    datetime created_at
    datetime updated_at
  }

  kurikulum {
    int id PK
    varchar nama
    int tahun
    int revisi
    varchar status
    varchar program_studi_kode
  }

  tahun_akademik {
    int id PK
    varchar tahun
    varchar status
    datetime created_at
    datetime updated_at
  }

  mata_kuliah {
    int id PK
    varchar kode_mk
    varchar nama_mk
    text deskripsi_singkat
    enum tipe "wajib/pilihan"
    tinyint semester
    tinyint sks
    enum kategori "wajib_teori/praktikum/pilihan/mkwk"
    datetime created_at
    datetime updated_at
  }

  bahan_kajian {
    int id PK
    varchar kode_bk
    varchar nama_bk
    datetime created_at
  }

  bk_mk {
    int id PK
    int bahan_kajian_id FK
    int mata_kuliah_id FK
  }

  mk_prasyarat {
    int id PK
    int mata_kuliah_id FK
    int prasyarat_mk_id FK
  }

  profil_lulusan {
    int id PK
    varchar kode_profil
    text deskripsi
    datetime created_at
    datetime updated_at
  }

  grade_config {
    int id PK
    varchar grade_letter
    decimal min_score
    decimal max_score
    decimal grade_point
    varchar description
    tinyint is_passing
    int order_number
    tinyint is_active
  }

  cpl {
    int id PK
    varchar kode_cpl
    text deskripsi
    enum jenis_cpl "P/KK/S/KU"
    datetime created_at
  }

  cpmk {
    int id PK
    varchar kode_cpmk
    text deskripsi
    datetime created_at
    datetime updated_at
  }

  sub_cpmk {
    int id PK
    int cpmk_id FK
    varchar kode_sub_cpmk
    text deskripsi
    datetime created_at
    datetime updated_at
  }

  cpl_cpmk {
    int id PK
    int cpl_id FK
    int cpmk_id FK
    datetime created_at
    datetime updated_at
  }

  cpl_mk {
    int id PK
    int cpl_id FK
    int mata_kuliah_id FK
  }

  cpl_bk {
    int id PK
    int cpl_id FK
    int bk_id FK
  }

  cpl_pl {
    int id PK
    int cpl_id FK
    int pl_id FK
  }

  cpmk_mk {
    int id PK
    int cpmk_id FK
    int mata_kuliah_id FK
  }

  sub_cpmk_mk {
    int id PK
    int sub_cpmk_id FK
    int mata_kuliah_id FK
    int teknik_penilaian_id
    int bobot
  }

  kelas {
    bigint kelas_id PK
    varchar kelas_nama
    varchar matakuliah_kode
    varchar matakuliah_nama
    varchar program_studi_kode
    varchar fakultas_kode
  }

  rps {
    int id PK
    int mata_kuliah_id FK
    varchar tahun_akademik
    text deskripsi
    datetime created_at
    datetime updated_at
  }

  rps_mingguan {
    int id PK
    int rps_id FK
    int minggu
    varchar topik
    text deskripsi
    datetime created_at
    datetime updated_at
  }

  rps_pengampu {
    int id PK
    int rps_id FK
    int dosen_id FK
  }

  rps_referensi {
    int id PK
    int rps_id FK
    varchar referensi
  }

  penyebab_templates_cpl {
    int id PK
    varchar option_key
    varchar option_label
    text template_text
    tinyint is_active
  }

  mbkm_jadwal {
    int id PK
    int mbkm_id FK
    int jadwal_id FK
  }

  mahasiswa {
    int id PK
    int user_id FK
    varchar nim
    varchar nama_lengkap
    enum jenis_kelamin "L/P"
    varchar email
    varchar program_studi_kode FK
    varchar tahun_angkatan
    enum status_mahasiswa "Aktif/Cuti/Lulus/DO"
    datetime created_at
    datetime updated_at
  }

  jadwal {
    int id PK
    int mata_kuliah_id FK
    varchar program_studi_kode
    varchar tahun_akademik
    varchar kelas
    varchar ruang
    enum hari "Senin/Selasa/Rabu/Kamis/Jumat/Sabtu"
    time jam_mulai
    time jam_selesai
    enum status "active/inactive/completed"
    tinyint is_nilai_validated
    bigint kelas_id FK
    datetime created_at
    datetime updated_at
  }

  jadwal_dosen {
    int id PK
    int jadwal_id FK
    int dosen_id FK
    enum role "leader/member"
    datetime created_at
  }

  jadwal_mahasiswa {
    int id PK
    int jadwal_id FK
    varchar nim FK
    datetime created_at
  }

  nilai_mahasiswa {
    int id PK
    int jadwal_id FK
    varchar nim
    float nilai
    varchar status
    datetime created_at
    datetime updated_at
  }

  nilai_cpmk_mahasiswa {
    int id PK
    int jadwal_id FK
    int cpmk_id FK
    varchar nim
    float nilai
    varchar status
    tinyint is_mbkm_excluded
    datetime created_at
    datetime updated_at
  }

  nilai_teknik_penilaian {
    int id PK
    int jadwal_id FK
    int cpmk_id FK
    int teknik_penilaian_id
    varchar nim
    float nilai
    datetime created_at
    datetime updated_at
  }

  mbkm {
    int id PK
    varchar nim
    varchar program
    varchar sub_program
    varchar tujuan
    varchar status_kegiatan
    varchar semester
    timestamp created_at
    timestamp updated_at
  }

  analysis_templates {
    int id PK
    varchar option_key
    varchar option_label
    text template_tercapai
    text template_tidak_tercapai
    tinyint is_active
  }

  analisis_cpmk {
    int id PK
    int mata_kuliah_id FK
    varchar tahun_akademik
    varchar program_studi
    enum mode "auto/manual"
    text analisis_singkat
    datetime created_at
    datetime updated_at
  }

  analysis_templates_cpl {
    int id PK
    varchar option_key
    varchar option_label
    text template_tercapai
    text template_tidak_tercapai
    tinyint is_active
  }

  analisis_cpl {
    int id PK
    varchar program_studi
    varchar tahun_akademik
    varchar angkatan
    enum mode "auto/manual"
    text analisis_summary
    varchar bukti_dokumentasi_file
    varchar notulensi_rapat_file
    datetime created_at
    datetime updated_at
  }

  cqi {
    int id PK
    enum type "cpl/cpmk"
    varchar program_studi
    varchar tahun_akademik
    varchar angkatan
    int jadwal_id
    varchar kode_cpl
    varchar kode_cpmk
    text masalah
    text rencana_perbaikan
    varchar penanggung_jawab
    datetime created_at
    datetime updated_at
  }

  standar_minimal_cpmk {
    int id PK
    varchar program_studi
    int cpmk_id FK
    varchar tahun_akademik
    int standar_minimal
    datetime created_at
    datetime updated_at
  }

  standar_minimal_cpl {
    int id PK
    varchar program_studi
    varchar tahun_akademik
    int standar_minimal
    datetime created_at
    datetime updated_at
  }


  %% ── Struktur Institusi ────────────────────────────────
  fakultas ||--o{ program_studi : "memiliki"
  program_studi ||--o{ kurikulum : "memiliki kurikulum"

  %% ── Pengguna & Dosen ──────────────────────────────────
  users ||--o| dosen : "adalah"
  users ||--o| mahasiswa : "adalah"
  program_studi ||--o{ dosen : "bertugas di"
  program_studi ||--o{ mahasiswa : "terdaftar di"

  %% ── Mata Kuliah & Bahan Kajian ────────────────────────
  mata_kuliah ||--o{ bk_mk : "memiliki bahan kajian"
  bahan_kajian ||--o{ bk_mk : "digunakan di"
  mata_kuliah ||--o{ mk_prasyarat : "memiliki prasyarat"
  mata_kuliah ||--o{ mk_prasyarat : "menjadi prasyarat"

  %% ── CPL, CPMK, Sub-CPMK ──────────────────────────────
  cpl ||--o{ cpl_cpmk : "memiliki pemetaan CPMK"
  cpmk ||--o{ cpl_cpmk : "dipetakan ke CPL"
  cpl ||--o{ cpl_mk : "diterapkan di"
  mata_kuliah ||--o{ cpl_mk : "menerapkan CPL"
  cpl ||--o{ cpl_bk : "didukung oleh"
  bahan_kajian ||--o{ cpl_bk : "mendukung CPL"
  cpl ||--o{ cpl_pl : "mendukung profil"
  profil_lulusan ||--o{ cpl_pl : "didukung CPL"
  cpmk ||--o{ cpmk_mk : "diterapkan di"
  mata_kuliah ||--o{ cpmk_mk : "memiliki CPMK"
  cpmk ||--o{ sub_cpmk : "terdiri dari"
  sub_cpmk ||--o{ sub_cpmk_mk : "diterapkan di"
  mata_kuliah ||--o{ sub_cpmk_mk : "memiliki Sub-CPMK"

  %% ── RPS ───────────────────────────────────────────────
  mata_kuliah ||--o{ rps : "memiliki RPS"
  rps ||--o{ rps_mingguan : "terdiri dari pertemuan"
  rps ||--o{ rps_pengampu : "diampu oleh"
  dosen ||--o{ rps_pengampu : "mengampu"
  rps ||--o{ rps_referensi : "memiliki referensi"

  %% ── Jadwal & Kelas ────────────────────────────────────
  mata_kuliah ||--o{ jadwal : "dijadwalkan di"
  kelas ||--o{ jadwal : "menyelenggarakan"
  jadwal ||--o{ jadwal_dosen : "diajar oleh"
  dosen ||--o{ jadwal_dosen : "mengajar di"
  jadwal ||--o{ jadwal_mahasiswa : "diikuti"
  mahasiswa ||--o{ jadwal_mahasiswa : "mengikuti"

  %% ── Nilai ─────────────────────────────────────────────
  jadwal ||--o{ nilai_mahasiswa : "menghasilkan nilai akhir"
  mahasiswa ||--o{ nilai_mahasiswa : "memiliki nilai akhir"
  jadwal ||--o{ nilai_cpmk_mahasiswa : "menghasilkan nilai CPMK"
  cpmk ||--o{ nilai_cpmk_mahasiswa : "dinilai di"
  mahasiswa ||--o{ nilai_cpmk_mahasiswa : "memiliki nilai CPMK"
  jadwal ||--o{ nilai_teknik_penilaian : "menghasilkan nilai teknik"
  cpmk ||--o{ nilai_teknik_penilaian : "dinilai via teknik"
  mahasiswa ||--o{ nilai_teknik_penilaian : "memiliki nilai teknik"

  %% ── MBKM ──────────────────────────────────────────────
  mahasiswa ||--o{ mbkm : "mengikuti program MBKM"
  mbkm ||--o{ mbkm_jadwal : "terkait jadwal"
  jadwal ||--o{ mbkm_jadwal : "terkait MBKM"

  %% ── Analisis & Standar ────────────────────────────────
  mata_kuliah ||--o{ analisis_cpmk : "dianalisis"
  cpmk ||--o{ standar_minimal_cpmk : "memiliki standar minimal"
	</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/mermaid@10/dist/mermaid.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script>
mermaid.initialize({
	startOnLoad: false,
	theme: 'default',
	er: {
		diagramPadding: 40,
		layoutDirection: 'LR',
		minEntityWidth: 100,
		minEntityHeight: 75,
		entityPadding: 15,
		useMaxWidth: false,
		fontSize: 12
	}
});

const YELLOW_TABLES = new Set([
	'mahasiswa', 'jadwal', 'jadwal_dosen', 'jadwal_mahasiswa',
	'nilai_teknik_penilaian', 'nilai_mahasiswa', 'nilai_cpmk_mahasiswa',
	'mbkm', 'analysis_templates', 'analisis_cpmk', 'analysis_templates_cpl',
	'analisis_cpl', 'cqi', 'standar_minimal_cpmk', 'standar_minimal_cpl', 'grade_config'
]);

const YELLOW = {
	header: '#ca8a04', headerText: '#ffffff',
	attrEven: '#fef9c3', attrOdd: '#fef08a', stroke: '#eab308'
};

function applyEntityColors() {
	const svg = document.querySelector('#erd-diagram svg');
	if (!svg) { setTimeout(applyEntityColors, 300); return; }

	svg.querySelectorAll('g').forEach(g => {
		const directTexts = Array.from(g.children).filter(c => c.tagName.toLowerCase() === 'text');
		if (!directTexts.length) return;
		const name = directTexts[0].textContent.trim();
		if (!YELLOW_TABLES.has(name)) return;

		g.querySelectorAll('rect').forEach((rect, i) => {
			rect.style.fill   = i === 0 ? YELLOW.header : (i % 2 === 1 ? YELLOW.attrEven : YELLOW.attrOdd);
			rect.style.stroke = YELLOW.stroke;
		});
		directTexts[0].style.fill = YELLOW.headerText;
	});
}

const _observer = new MutationObserver(() => {
	if (document.querySelector('#erd-diagram svg')) {
		_observer.disconnect();
		setTimeout(applyEntityColors, 150);
	}
});
_observer.observe(document.getElementById('erd-diagram'), { childList: true, subtree: true });
mermaid.run({ querySelector: '#erd-diagram' });

// Zoom
let zoom = 1;
const diagram   = document.getElementById('erd-diagram');
const zoomLabel = document.getElementById('erd-zoom-level');

function applyZoom() {
	diagram.style.transform = `scale(${zoom})`;
	diagram.style.width = (100 / zoom) + '%';
	zoomLabel.textContent = Math.round(zoom * 100) + '%';
}
document.getElementById('btn-zoom-in').addEventListener('click',    () => { zoom = Math.min(zoom + 0.1, 3);   applyZoom(); });
document.getElementById('btn-zoom-out').addEventListener('click',   () => { zoom = Math.max(zoom - 0.1, 0.2); applyZoom(); });
document.getElementById('btn-zoom-reset').addEventListener('click', () => { zoom = 1; applyZoom(); });
document.getElementById('btn-print').addEventListener('click', async () => {
	const btn = document.getElementById('btn-print');
	const svg = document.querySelector('#erd-diagram svg');
	if (!svg) return;

	btn.disabled = true;
	btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';

	// Temporarily reset zoom so the full diagram is captured at 1:1
	const prevZoom = zoom;
	zoom = 1; applyZoom();

	// Give browser a tick to re-render at zoom 1
	await new Promise(r => setTimeout(r, 100));

	try {
		const canvas = await html2canvas(document.getElementById('erd-diagram'), {
			backgroundColor: '#ffffff',
			scale: 2,           // 2× for sharper output
			useCORS: true,
			logging: false,
		});

		const link = document.createElement('a');
		link.download = 'ERD-Sistem-OBE.png';
		link.href = canvas.toDataURL('image/png');
		link.click();
	} finally {
		zoom = prevZoom; applyZoom();
		btn.disabled = false;
		btn.innerHTML = '<i class="bi bi-camera"></i> Screenshot';
	}
});
</script>
</body>
</html>

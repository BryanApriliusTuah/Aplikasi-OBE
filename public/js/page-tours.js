/**
 * OBE TI UPR — Page-by-Page Interactive Tour
 * Uses Driver.js. Activated via URL param ?tour=1 or the floating tour button.
 * Each page entry defines: steps[] and optional nextUrl (with base path, no domain).
 */

window.OBE_TOURS = (function () {

	// Resolve base URL dynamically (works on any subdomain/path)
	function base(path) {
		var origin = window.location.origin;
		// Detect sub-directory installations (e.g. /obe-system/)
		var scripts = document.querySelectorAll('script[src]');
		var basePath = '';
		scripts.forEach(function (s) {
			var m = s.src.match(/^.+?(\/[^/]+\/)[^/]+\/js\/page-tours\.js$/);
			if (m) basePath = m[1].replace(/\/$/, '');
		});
		return origin + basePath + '/' + path.replace(/^\//, '');
	}

	// Tour chain ordered by the 6-stage guide
	var TOUR_CHAIN = [
		'tahun-akademik',
		'settings',
		'dosen',
		'profil-lulusan',
		'cpl',
		'bahan-kajian',
		'mata-kuliah',
		'cpmk',
		'cpl-pl',
		'cpl-bk',
		'bkmk',
		'cpl-mk',
		'pemetaan-cpl-mk-cpmk',
		'pemetaan-mk-cpmk-sub',
		'rps',
		'mengajar',
		'nilai',
		'input-nilai-teknik',
		'capaian-cpmk',
		'capaian-cpl',
		'laporan-cpmk',
		'laporan-cpl',
	];

	// Helper: find first existing element from a list of selectors
	function el() {
		for (var i = 0; i < arguments.length; i++) {
			var found = document.querySelector(arguments[i]);
			if (found) return arguments[i];
		}
		return undefined;
	}

	// Page tour definitions — keyed by a substring of the URL path
	var PAGES = {

		// ─── STAGE 1: SETUP AWAL ─────────────────────────────────────────────

		'tahun-akademik': {
			nextKey: 'settings',
			nextUrl: 'admin/settings',
			steps: [
				{
					element: el('h4.fw-semibold', 'h2.fw-bold', '.card-body h4'),
					popover: {
						title: '<i class="bi bi-calendar3 text-primary"></i> Tahun Akademik',
						description: 'Halaman ini mengelola daftar tahun akademik (misal: <b>2024 Ganjil</b>, <b>2024 Genap</b>). Tahun akademik terbaru yang diaktifkan akan menjadi filter default di seluruh sistem.',
						side: 'bottom'
					}
				},
				{
					element: el('a[href*="tahun-akademik/create"]', '.btn.btn-dark'),
					popover: {
						title: '<i class="bi bi-plus-circle text-success"></i> Tambah Tahun Akademik',
						description: 'Klik tombol <b>Tambah</b> untuk membuat tahun akademik baru. Isi tahun (misal: 2024) dan pilih semester (Ganjil/Genap). Hanya admin yang dapat mengelola tahun akademik.',
						side: 'left'
					}
				},
				{
					element: el('.modern-table-wrapper', '.modern-table', 'table'),
					popover: {
						title: '<i class="bi bi-table text-info"></i> Daftar Tahun Akademik',
						description: 'Tabel ini menampilkan semua tahun akademik. Kolom <b>Status</b> menunjukkan tahun akademik mana yang sedang aktif. Gunakan ikon toggle untuk mengaktifkan/menonaktifkan. Tahun Akademik dengan status tidak aktif tidak akan muncul sebagai opsi di filter tahun akademik pada halaman lain.',
						side: 'top'
					}
				}
			]
		},

		'settings': {
			nextKey: 'dosen',
			nextUrl: 'admin/dosen',
			steps: [
				{
					element: el('h4.fw-semibold', '.card-body h4'),
					popover: {
						title: '<i class="bi bi-gear-fill text-secondary"></i> Pengaturan Sistem',
						description: 'Halaman ini mengatur konfigurasi penilaian OBE: standar minimal capaian dan konversi huruf mutu. Pastikan diisi sebelum memasukkan nilai. Hanya admin yang dapat mengakses halaman ini.',
						side: 'bottom'
					}
				},
				{
					element: el('#persentase_cpmk'),
					popover: {
						title: '<i class="bi bi-percent text-warning"></i> Standar Minimal CPMK',
						description: 'Tetapkan persentase minimal yang harus dicapai mahasiswa pada setiap CPMK (misal: 60%). Nilai di bawah ini dianggap belum mencapai CPMK.',
						side: 'right'
					}
				},
				{
					element: el('#persentase_cpl'),
					popover: {
						title: '<i class="bi bi-percent text-danger"></i> Standar Minimal CPL',
						description: 'Tetapkan persentase minimal capaian CPL secara keseluruhan. Digunakan untuk menentukan apakah CPL program studi sudah tercapai.',
						side: 'right'
					}
				},
				{
					element: el('.modern-table-wrapper', 'table.modern-table'),
					popover: {
						title: '<i class="bi bi-list-ol text-primary"></i> Konfigurasi Huruf Mutu',
						description: 'Tabel ini berisi rentang nilai dan konversinya ke huruf mutu (A, B, C, D, E). Klik <b>Tambah</b> untuk menambah aturan baru atau edit data yang sudah ada.',
						side: 'top'
					}
				}
			]
		},

		'dosen': {
			nextKey: 'profil-lulusan',
			nextUrl: 'admin/profil-lulusan',
			steps: [
				{
					element: el('h2.fw-bold', 'h2', '.d-flex h2'),
					popover: {
						title: '<i class="bi bi-person-badge-fill text-primary"></i> Data Dosen',
						description: 'Halaman ini mengelola akun dosen yang dapat login ke sistem. Data dosen <b>disinkronisasi</b> langsung dari API SIUBER.',
						side: 'bottom'
					}
				},
				{
					element: el('button[form*="sync"], form[action*="sync"] button, .btn.btn-warning'),
					popover: {
						title: '<i class="bi bi-arrow-repeat text-warning"></i> Sinkronisasi dari API',
						description: 'Klik <b>Sinkronisasi</b> untuk mengambil data dosen terbaru dari sistem SIUBER secara otomatis. Pastikan koneksi API aktif sebelum sinkronisasi. Hanya admin yang dapat melakukan sinkronisasi data dosen.',
						side: 'left'
					}
				},
				{
					element: el('.modern-table-wrapper', 'table'),
					popover: {
						title: '<i class="bi bi-table text-info"></i> Daftar Dosen',
						description: 'Tabel dosen yang terdaftar di sistem. Setiap dosen memiliki akun yang dapat digunakan untuk login, mengelola RPS, dan menginput nilai.',
						side: 'top'
					}
				}
			]
		},

		// ─── STAGE 2: STRUKTUR KURIKULUM ─────────────────────────────────────

		'profil-lulusan': {
			nextKey: 'cpl',
			nextUrl: 'admin/cpl',
			steps: [
				{
					element: el('h2.fw-bold', 'h2'),
					popover: {
						title: '<i class="bi bi-person-check-fill text-success"></i> Profil Lulusan (PL)',
						description: 'Profil Lulusan menggambarkan karakter dan kemampuan yang diharapkan dari seorang lulusan program studi. PL menjadi dasar perumusan CPL.',
						side: 'bottom'
					}
				},
				{
					element: el('a[href*="profil-lulusan/create"]', '.btn.btn-primary'),
					popover: {
						title: '<i class="bi bi-plus-circle text-success"></i> Tambah Profil Lulusan',
						description: 'Klik <b>Tambah Profil</b> untuk mendefinisikan profil baru. Contoh: "Pengembang Perangkat Lunak", "Data Scientist", dll. Hanya admin yang dapat mengelola profil lulusan.',
						side: 'left'
					}
				},
				{
					element: el('.btn-group .dropdown-toggle', '.btn-outline-success'),
					popover: {
						title: '<i class="bi bi-download text-success"></i> Ekspor Data',
						description: 'Data Profil Lulusan dapat diunduh dalam format <b>PDF</b> atau <b>Excel</b> untuk keperluan dokumentasi dan akreditasi.',
						side: 'left'
					}
				},
				{
					element: el('.modern-table-wrapper', 'table'),
					popover: {
						title: '<i class="bi bi-table text-info"></i> Daftar Profil Lulusan',
						description: 'Semua profil lulusan yang telah didefinisikan akan ditampilkan di sini. Profil ini akan dipetakan ke CPL pada tahap berikutnya.',
						side: 'top'
					}
				}
			]
		},

		'cpl': {
			nextKey: 'bahan-kajian',
			nextUrl: 'admin/bahan-kajian',
			steps: [
				{
					element: el('h2.fw-bold', 'h2'),
					popover: {
						title: '<i class="bi bi-bullseye text-danger"></i> CPL – Capaian Pembelajaran Lulusan',
						description: 'CPL adalah kemampuan yang harus dimiliki lulusan, mencakup aspek <b>Sikap</b>, <b>Pengetahuan</b>, <b>Keterampilan Umum</b>, dan <b>Keterampilan Khusus</b>.',
						side: 'bottom'
					}
				},
				{
					element: el('a[href*="cpl/create"]', '.btn.btn-primary'),
					popover: {
						title: '<i class="bi bi-plus-circle text-primary"></i> Tambah CPL',
						description: 'Tambahkan setiap CPL dengan kode unik (misal: CPL-01) dan deskripsi lengkap kemampuan yang dituju. Hanya admin yang dapat mengelola CPL.',
						side: 'left'
					}
				},
				{
					element: el('.btn-group .dropdown-toggle', '.btn-outline-success'),
					popover: {
						title: '<i class="bi bi-download text-success"></i> Ekspor Data CPL',
						description: 'Unduh daftar CPL dalam format PDF atau Excel untuk dokumentasi kurikulum dan keperluan akreditasi.',
						side: 'left'
					}
				},
				{
					element: el('.modern-table-wrapper', '#cplTable', 'table'),
					popover: {
						title: '<i class="bi bi-table text-info"></i> Daftar CPL',
						description: 'Tabel berisi semua CPL program studi. Setiap CPL akan dipetakan ke Profil Lulusan, Bahan Kajian, dan Mata Kuliah pada tahap pemetaan.',
						side: 'top'
					}
				}
			]
		},

		'bahan-kajian': {
			nextKey: 'mata-kuliah',
			nextUrl: 'admin/mata-kuliah',
			steps: [
				{
					element: el('h2.fw-bold', 'h2'),
					popover: {
						title: '<i class="bi bi-book-half text-warning"></i> Bahan Kajian (BK)',
						description: 'Bahan Kajian adalah topik atau materi utama yang relevan dengan CPL. BK menjadi jembatan antara CPL dan Mata Kuliah.',
						side: 'bottom'
					}
				},
				{
					element: el('a[href*="bahan-kajian/create"]', '.btn.btn-primary'),
					popover: {
						title: '<i class="bi bi-plus-circle text-primary"></i> Tambah Bahan Kajian',
						description: 'Definisikan bahan kajian dengan nama dan deskripsi. Contoh: "Pemrograman Web", "Basis Data", "Kecerdasan Buatan". Hanya admin yang dapat mengelola bahan kajian.',
						side: 'left'
					}
				},
				{
					element: el('.modern-table-wrapper', 'table'),
					popover: {
						title: '<i class="bi bi-table text-info"></i> Daftar Bahan Kajian',
						description: 'Semua bahan kajian ditampilkan di sini. Selanjutnya BK akan dipetakan ke CPL dan Mata Kuliah di tahap pemetaan kurikulum.',
						side: 'top'
					}
				}
			]
		},

		'mata-kuliah': {
			nextKey: 'cpmk',
			nextUrl: 'admin/cpmk',
			steps: [
				{
					element: el('h2.fw-bold', 'h2'),
					popover: {
						title: '<i class="bi bi-journal-bookmark-fill text-primary"></i> Mata Kuliah (MK)',
						description: 'Daftar semua mata kuliah program studi. Data MK dapat disinkronisasi dari API SIUBER.',
						side: 'bottom'
					}
				},
				{
					element: el('form[action*="mata-kuliah/sync"] button', '.btn.btn-warning'),
					popover: {
						title: '<i class="bi bi-arrow-repeat text-warning"></i> Sinkronisasi Mata Kuliah',
						description: 'Klik <b>Sinkronisasi</b> untuk mengambil data mata kuliah terbaru dari API SIUBER secara otomatis. Data yang sudah ada akan diperbarui. Hanya admin yang dapat melakukan sinkronisasi MK.',
						side: 'left'
					}
				},
				{
					element: el('.modern-table-wrapper', 'table'),
					popover: {
						title: '<i class="bi bi-table text-info"></i> Daftar Mata Kuliah',
						description: 'Tabel ini menampilkan semua MK beserta kode, SKS, dan semester. Setiap MK akan memiliki CPMK dan dipetakan ke CPL.',
						side: 'top'
					}
				}
			]
		},

		'cpmk': {
			nextKey: 'cpl-pl',
			nextUrl: 'admin/cpl-pl',
			steps: [
				{
					element: el('h2.fw-bold', 'h2'),
					popover: {
						title: '<i class="bi bi-check2-circle text-success"></i> Pemetaan – Capaian Pembelajaran Mata Kuliah',
						description: 'CPMK adalah kemampuan spesifik yang harus dicapai mahasiswa setelah menyelesaikan satu mata kuliah. CPMK merupakan turunan dari CPL.',
						side: 'bottom'
					}
				},
				{
					element: el('a[href*="cpmk/create"]', '.btn.btn-primary'),
					popover: {
						title: '<i class="bi bi-plus-circle text-primary"></i> Tambah Pemetaan',
						description: 'Buat CPMK untuk setiap CPL. Satu CPL dapat memiliki beberapa CPMK. Gunakan kode yang konsisten (misal: CPMK011 adalah CPMK pertama untuk CPL01, CPMK012 adalah CPMK kedua untuk CPL01, dan seterusnya). Hanya admin yang dapat mengelola CPMK.',
						side: 'left'
					}
				},
				{
					element: el('.modern-table-wrapper', 'table'),
					popover: {
						title: '<i class="bi bi-table text-info"></i> Daftar CPMK',
						description: 'Semua CPMK dari seluruh mata kuliah ditampilkan di sini. CPMK akan dipetakan ke CPL dan digunakan sebagai dasar penilaian.',
						side: 'top'
					}
				}
			]
		},

		// ─── STAGE 3: PEMETAAN KURIKULUM ─────────────────────────────────────

		'cpl-pl': {
			nextKey: 'cpl-bk',
			nextUrl: 'admin/cpl-bk',
			steps: [
				{
					element: el('h2.fw-bold', 'h2'),
					popover: {
						title: '<i class="bi bi-diagram-3-fill text-primary"></i> Pemetaan CPL → Profil Lulusan',
						description: 'Petakan setiap CPL ke satu atau lebih Profil Lulusan yang relevan. Pemetaan ini menunjukkan CPL mana yang mendukung profil lulusan tertentu.',
						side: 'bottom'
					}
				},
				{
					element: el('a[href*="cpl-pl/create"]', '.btn.btn-primary'),
					popover: {
						title: '<i class="bi bi-plus-circle text-primary"></i> Tambah Pemetaan',
						description: 'Pilih CPL dan Profil Lulusan yang terkait, lalu simpan pemetaannya. Satu CPL dapat dipetakan ke lebih dari satu profil lulusan. Hanya admin yang dapat mengelola pemetaan CPL–PL.',
						side: 'left'
					}
				},
				{
					element: el('.modern-table-wrapper', 'table'),
					popover: {
						title: '<i class="bi bi-table text-info"></i> Matriks CPL – PL',
						description: 'Tabel menampilkan relasi antara CPL dan Profil Lulusan. Pastikan semua CPL sudah terpetakan minimal ke satu profil lulusan.',
						side: 'top'
					}
				}
			]
		},

		'cpl-bk': {
			nextKey: 'bkmk',
			nextUrl: 'admin/bkmk',
			steps: [
				{
					element: el('h2.fw-bold', 'h2'),
					popover: {
						title: '<i class="bi bi-diagram-3-fill text-warning"></i> Pemetaan CPL → Bahan Kajian',
						description: 'Petakan CPL ke Bahan Kajian yang mendukung pencapaiannya. Satu CPL bisa didukung oleh beberapa bahan kajian.',
						side: 'bottom'
					}
				},
				{
					element: el('a[href*="cpl-bk/create"]', '.btn.btn-primary'),
					popover: {
						title: '<i class="bi bi-plus-circle text-primary"></i> Tambah Pemetaan CPL–BK',
						description: 'Pilih CPL dan Bahan Kajian yang berkaitan, lalu simpan. Relasi ini digunakan untuk melacak cakupan kurikulum. Hanya admin yang dapat mengelola pemetaan CPL–BK.',
						side: 'left'
					}
				},
				{
					element: el('.modern-table-wrapper', 'table'),
					popover: {
						title: '<i class="bi bi-table text-info"></i> Matriks CPL – Bahan Kajian',
						description: 'Tabel pemetaan CPL ke Bahan Kajian. Data ini digunakan dalam matriks pemenuhan kurikulum.',
						side: 'top'
					}
				}
			]
		},

		'bkmk': {
			nextKey: 'cpl-mk',
			nextUrl: 'admin/cpl-mk',
			steps: [
				{
					element: el('h2.fw-bold', 'h2'),
					popover: {
						title: '<i class="bi bi-diagram-3-fill text-info"></i> Pemetaan Bahan Kajian → Mata Kuliah',
						description: 'Petakan setiap Bahan Kajian ke Mata Kuliah yang membahasnya. Ini menunjukkan distribusi bahan kajian dalam kurikulum.',
						side: 'bottom'
					}
				},
				{
					element: el('a[href*="bkmk/create"]', '.btn.btn-primary'),
					popover: {
						title: '<i class="bi bi-plus-circle text-primary"></i> Tambah Pemetaan BK–MK',
						description: 'Pilih Bahan Kajian dan Mata Kuliah yang membahasnya. Satu BK bisa diajarkan di beberapa MK, dan satu MK bisa mencakup beberapa BK. Hanya admin yang dapat mengelola pemetaan BK–MK.',
						side: 'left'
					}
				},
				{
					element: el('a[href*="bkmk/matriks"]', '.btn-outline-primary'),
					popover: {
						title: '<i class="bi bi-grid-3x3-gap text-primary"></i> Lihat Matriks',
						description: 'Klik untuk melihat matriks visual BK vs MK, menampilkan semua relasi dalam bentuk tabel silang.',
						side: 'left'
					}
				},
				{
					element: el('.modern-table-wrapper', 'table'),
					popover: {
						title: '<i class="bi bi-table text-info"></i> Daftar Pemetaan BK–MK',
						description: 'Tabel menampilkan semua relasi antara Bahan Kajian dan Mata Kuliah yang telah didefinisikan.',
						side: 'top'
					}
				}
			]
		},

		'cpl-mk': {
			nextKey: 'pemetaan-cpl-mk-cpmk',
			nextUrl: 'admin/pemetaan-cpl-mk-cpmk',
			steps: [
				{
					element: el('h2.fw-bold', 'h2'),
					popover: {
						title: '<i class="bi bi-diagram-3-fill text-danger"></i> Pemetaan CPL → Mata Kuliah',
						description: 'Petakan CPL ke Mata Kuliah yang bertanggung jawab mencapainya. Satu CPL bisa didukung oleh beberapa MK lintas semester.',
						side: 'bottom'
					}
				},
				{
					element: el('a[href*="cpl-mk/create"]', '.btn.btn-primary'),
					popover: {
						title: '<i class="bi bi-plus-circle text-primary"></i> Tambah Pemetaan CPL–MK',
						description: 'Pilih CPL dan Mata Kuliah yang mendukungnya. Relasi ini menentukan MK mana yang berkontribusi pada pencapaian tiap CPL. Hanya admin yang dapat mengelola pemetaan CPL–MK.',
						side: 'left'
					}
				},
				{
					element: el('.modern-table-wrapper', 'table'),
					popover: {
						title: '<i class="bi bi-table text-info"></i> Matriks CPL – MK',
						description: 'Tabel menampilkan semua relasi CPL dan MK. Pastikan setiap CPL minimal terpetakan ke satu mata kuliah.',
						side: 'top'
					}
				}
			]
		},

		'pemetaan-cpl-mk-cpmk': {
			nextKey: 'pemetaan-mk-cpmk-sub',
			nextUrl: 'admin/pemetaan-mk-cpmk-sub',
			steps: [
				{
					element: el('h2.fw-bold', 'h2'),
					popover: {
						title: '<i class="bi bi-bezier2 text-purple"></i> Pemetaan CPL – MK – CPMK',
						description: 'Halaman ini memetakan relasi tiga arah: CPL, Mata Kuliah, dan CPMK. Ini adalah inti dari struktur OBE — menunjukkan CPMK mana dari MK mana yang mendukung CPL.',
						side: 'bottom'
					}
				},
				{
					element: el('a[href*="pemetaan-cpl-mk-cpmk/create"]', '.btn.btn-primary'),
					popover: {
						title: '<i class="bi bi-plus-circle text-primary"></i> Tambah Pemetaan',
						description: 'Pilih CPL → pilih MK yang terkait → pilih CPMK dari MK tersebut. Relasi ini menentukan bagaimana setiap CPMK berkontribusi pada pencapaian CPL. Admin dan dosen dapat mengelola pemetaan ini untuk memastikan struktur kurikulum yang tepat.',
						side: 'left'
					}
				},
				{
					element: el('.modern-table-wrapper', 'table'),
					popover: {
						title: '<i class="bi bi-table text-info"></i> Daftar Pemetaan CPL–MK–CPMK',
						description: 'Tabel ini adalah peta kurikulum OBE yang paling krusial. Gunakan fitur ekspor untuk mendokumentasikan pemetaan ini.',
						side: 'top'
					}
				}
			]
		},

		'pemetaan-mk-cpmk-sub': {
			nextKey: 'rps',
			nextUrl: 'rps',
			steps: [
				{
					element: el('h2.fw-bold', 'h2'),
					popover: {
						title: '<i class="bi bi-bezier text-success"></i> Pemetaan MK – CPMK – Sub-CPMK',
						description: 'Halaman ini mendefinisikan Sub-CPMK, yaitu turunan lebih spesifik dari CPMK. Sub-CPMK digunakan sebagai indikator penilaian yang lebih terperinci.',
						side: 'bottom'
					}
				},
				{
					element: el('a[href*="pemetaan-mk-cpmk-sub/create"]', '.btn.btn-primary'),
					popover: {
						title: '<i class="bi bi-plus-circle text-primary"></i> Tambah Sub-CPMK',
						description: 'Pilih Mata Kuliah dan CPMK induknya, lalu definisikan Sub-CPMK. Sub-CPMK ini akan muncul sebagai komponen penilaian di RPS. Admin dan dosen dapat mengelola Sub-CPMK untuk memastikan pemetaan tepat sasaran.',
						side: 'left'
					}
				},
				{
					element: el('.modern-table-wrapper', 'table'),
					popover: {
						title: '<i class="bi bi-table text-info"></i> Daftar Sub-CPMK',
						description: 'Semua Sub-CPMK dari seluruh mata kuliah. Setelah tahap ini, struktur kurikulum sudah lengkap dan siap untuk pembuatan RPS.',
						side: 'top'
					}
				}
			]
		},

		// ─── STAGE 4: RPS & ASESMEN ──────────────────────────────────────────

		'rps': {
			nextKey: 'mengajar',
			nextUrl: 'admin/mengajar',
			steps: [
				{
					element: el('h2.fw-bold', 'h1', 'h2', '.d-flex h2'),
					popover: {
						title: '<i class="bi bi-book-fill text-primary"></i> RPS – Rencana Pembelajaran Semester',
						description: 'RPS adalah dokumen perencanaan pembelajaran per mata kuliah yang memuat tujuan, materi, metode, dan rencana penilaian setiap minggu.',
						side: 'bottom'
					}
				},
				{
					element: el('a[href*="rps/create"]', '.btn.btn-primary'),
					popover: {
						title: '<i class="bi bi-plus-circle text-primary"></i> Buat RPS Baru',
						description: 'Klik untuk membuat RPS baru. Pilih mata kuliah, dosen pengampu, koordinator mata kuliah, tahun ajaran, dan tanggal penyusunan. Kelola mingguan RPS hanya bisa dibuat jika pemetaan CPL–MK–CPMK sudah selesai.',
						side: 'left'
					}
				},
				{
					element: el('.modern-table-wrapper', 'table'),
					popover: {
						title: '<i class="bi bi-table text-info"></i> Daftar RPS',
						description: 'Semua RPS yang telah dibuat ditampilkan di sini. Klik RPS untuk mengisi detail mingguan, referensi, teknik penilaian, dan bobot nilai.',
						side: 'top'
					}
				}
			]
		},

		// ─── STAGE 5: OPERASIONAL AKADEMIK ───────────────────────────────────

		'mengajar': {
			nextKey: 'nilai',
			nextUrl: 'admin/nilai',
			steps: [
				{
					element: el('h2.fw-bold', 'h2'),
					popover: {
						title: '<i class="bi bi-person-video3 text-primary"></i> Jadwal Mengajar',
						description: 'Halaman ini menampilkan daftar kelas yang diajarkan. Data jadwal disinkronisasi dari API SIUBER dan digunakan sebagai dasar input nilai.',
						side: 'bottom'
					}
				},
				{
					element: el('button[onclick*="sync"], form[action*="syncFromApi"] button, .btn.btn-warning'),
					popover: {
						title: '<i class="bi bi-arrow-repeat text-warning"></i> Sinkronisasi Jadwal',
						description: 'Sinkronisasi data jadwal mengajar dari API SIUBER. Kelas yang muncul di sini akan tersedia di menu Input Nilai. Hanya admin yang dapat melakukan sinkronisasi jadwal.',
						side: 'left'
					}
				},
				{
					element: el('.modern-table-wrapper', 'table'),
					popover: {
						title: '<i class="bi bi-table text-info"></i> Daftar Kelas',
						description: 'Klik Ikon titik tiga di pojok kanan atas setiap kelas untuk melihat secara detail jadwal. Pastikan data jadwal sudah benar sebelum lanjut ke input nilai.',
						side: 'top'
					}
				}
			]
		},

		'nilai': {
			nextKey: 'capaian-cpmk',
			nextUrl: 'admin/capaian-cpmk',
			steps: [
				{
					element: el('h2.fw-bold', 'h2'),
					popover: {
						title: '<i class="bi bi-pencil-square text-danger"></i> Input Nilai Mahasiswa',
						description: 'Halaman ini digunakan untuk menginput dan mengelola nilai mahasiswa per kelas. Nilai yang diinput akan dihitung otomatis menjadi capaian CPMK dan CPL.',
						side: 'bottom'
					}
				},
				{
					element: el('.modern-table-wrapper', 'table'),
					popover: {
						title: '<i class="bi bi-table text-info"></i> Daftar Kelas – Input Nilai',
						description: 'Berisikan daftar kelas yang diajarkan. Setiap dosen hanya dapat menginput nilai untuk kelas yang diajarnya. Klik tombol <b>Input Nilai</b> untuk memasukkan nilai mahasiswa per CPMK. Setelah seluruh nilai diinput, tekan tombol <b>Validasi Nilai</b>, jika nilai sudah sesuai. Nilai yang sudah divalidasi tidak dapat diubah tanpa persetujuan admin.',
						side: 'top'
					}
				}
			]
		},

		'input-nilai-teknik': {
			nextKey: 'capaian-cpmk',
			nextUrl: 'admin/capaian-cpmk',
			steps: [
				{
					element: el('h2.fw-bold', 'h2'),
					popover: {
						title: '<i class="bi bi-pencil-square text-danger"></i> Halaman Input Penilaian',
						description: 'Halaman ini digunakan untuk memasukkan nilai setiap mahasiswa berdasarkan <b>teknik penilaian</b> yang telah didefinisikan di RPS Mingguan. Setiap kolom nilai merepresentasikan satu komponen penilaian (misalnya: Tugas Minggu 3, UTS Minggu 8) beserta bobotnya.',
						side: 'bottom'
					}
				},
				{
					element: el('.card.border-0.shadow-sm.mb-4 .card-body', '.bg-light.border-bottom ~ .card .card-body.bg-light', '.card-body.bg-light'),
					popover: {
						title: '<i class="bi bi-info-circle text-info"></i> Informasi Kelas',
						description: 'Bagian ini menampilkan informasi kelas yang sedang dinilai: <b>Mata Kuliah</b>, <b>Kelas</b>, <b>Tahun Akademik</b>, dan <b>Dosen Koordinator</b>. Informasi ini penting untuk memastikan Anda menginput nilai pada kelas yang benar.',
						side: 'bottom'
					}
				},
				{
					element: el('a[href*="rps/preview"]', 'a[title="Lihat RPS"]'),
					popover: {
						title: '<i class="bi bi-file-text text-secondary"></i> Tombol RPS',
						description: 'Klik tombol <b>RPS</b> untuk membuka dokumen Rencana Pembelajaran Semester pada tab baru. Berguna sebagai referensi saat memasukkan nilai agar sesuai dengan rencana asesmen yang telah ditetapkan.',
						side: 'left'
					}
				},
				{
					element: el('a[href*="rps/mingguan"]', 'a[title="Kelola RPS Mingguan"]'),
					popover: {
						title: '<i class="bi bi-calendar-week text-secondary"></i> Tombol RPS Mingguan',
						description: 'Klik tombol <b>Mingguan</b> untuk mengelola rencana pembelajaran per minggu, termasuk teknik penilaian dan bobot yang menjadi dasar kolom nilai di halaman ini.',
						side: 'left'
					}
				},
				{
					element: el('a[href*="unduh-dpna"]', 'a[title*="DPNA"]'),
					popover: {
						title: '<i class="bi bi-download text-success"></i> Unduh DPNA',
						description: 'Klik <b>Unduh DPNA</b> untuk mengunduh Daftar Penilaian Nilai Akhir dalam format Excel. File ini juga berfungsi sebagai <b>template</b> untuk fitur Unggah Nilai — pastikan menggunakannya agar format kolom sesuai.',
						side: 'left'
					}
				},
				{
					element: el('button[data-bs-target="#uploadNilaiModal"]', '.btn.btn-primary[data-bs-toggle="modal"]'),
					popover: {
						title: '<i class="bi bi-upload text-primary"></i> Unggah Nilai dari Excel',
						description: 'Klik <b>Unggah</b> untuk mengimpor nilai dari file Excel (.xlsx). Gunakan template DPNA sebagai acuan format. Nilai yang diimport akan <b>menggantikan</b> nilai yang sudah ada. Fitur ini hanya tersedia selama nilai belum divalidasi.',
						side: 'left'
					}
				},
				{
					element: el('button[onclick="fillAllValues()"]', '.btn.btn-success.btn-sm'),
					popover: {
						title: '<i class="bi bi-lightning-fill text-success"></i> Isi Semua (Testing)',
						description: 'Tombol <b>Isi Semua</b> akan mengisi seluruh kolom nilai dengan angka acak antara 1–100 untuk keperluan <b>pengujian</b>. Fitur ini hanya digunakan selama proses testing dan akan dihapus pada versi final. Jangan gunakan tombol ini untuk data asli karena akan mengubah seluruh nilai menjadi angka acak.',
						side: 'bottom'
					}
				},
				{
					element: el('button[onclick="clearAllValues()"]', '.btn.btn-outline-secondary.btn-sm'),
					popover: {
						title: '<i class="bi bi-eraser text-secondary"></i> Kosongkan Semua',
						description: 'Tombol <b>Kosongkan Semua</b> akan menghapus seluruh isian nilai sekaligus dan mereset tampilan Nilai Angka, Nilai Huruf, dan Keterangan ke kondisi kosong.',
						side: 'bottom'
					}
				},
				{
					element: el('button[onclick="validateNilai()"]', '.btn.btn-primary.btn-sm'),
					popover: {
						title: '<i class="bi bi-check-circle text-primary"></i> Validasi Nilai',
						description: 'Setelah seluruh nilai selesai diinput dan diperiksa, klik <b>Validasi Nilai</b> untuk mengunci nilai. Nilai yang sudah divalidasi <b>tidak dapat diubah</b> oleh dosen. Hanya admin yang dapat membatalkan validasi. Pastikan semua nilai sudah benar sebelum memvalidasi.',
						side: 'bottom'
					}
				},
				{
					element: el('#nilaiTable', '.modern-table', 'table'),
					popover: {
						title: '<i class="bi bi-table text-info"></i> Tabel Input Nilai',
						description: 'Tabel utama berisi daftar mahasiswa (baris) dan teknik penilaian (kolom). Setiap kolom teknik menampilkan <b>nama komponen</b>, <b>minggu</b>, <b>kode CPMK</b>, dan <b>bobot (%)</b>. Hover pada header kolom untuk melihat detail lengkap. Kolom <b>No, NIM, dan Nama</b> bersifat tetap (sticky) saat tabel discroll ke kanan.',
						side: 'top'
					}
				},
				{
					element: el('.nilai-input', 'input.form-control.nilai-input'),
					popover: {
						title: '<i class="bi bi-input-cursor-text text-primary"></i> Input Nilai per Komponen',
						description: 'Isi nilai mahasiswa pada setiap kolom teknik penilaian (rentang <b>0–100</b>). Sistem akan langsung memvalidasi input: <span style="color:#198754">hijau</span> = valid, <span style="color:#dc3545">merah</span> = tidak valid. Nilai akhir dihitung otomatis secara tertimbang berdasarkan bobot masing-masing komponen.',
						side: 'top'
					}
				},
				{
					element: el('.nilai-angka-display', 'span.nilai-angka-display'),
					popover: {
						title: '<i class="bi bi-calculator text-info"></i> Nilai Angka (Otomatis)',
						description: 'Kolom <b>Nilai Angka</b> menampilkan nilai akhir yang dihitung secara otomatis dari akumulasi nilai x bobot. Nilai ini diperbarui langsung saat Anda mengedit input nilai tanpa perlu menyimpan terlebih dahulu.',
						side: 'left'
					}
				},
				{
					element: el('.nilai-huruf-display', 'span.nilai-huruf-display'),
					popover: {
						title: '<i class="bi bi-alphabet text-success"></i> Nilai Huruf (Otomatis)',
						description: 'Kolom <b>Nilai Huruf</b> menampilkan konversi nilai angka ke huruf mutu (A, AB, B, BC, C, D, E) berdasarkan konfigurasi di menu <b>Pengaturan</b> (<i>Admin only</i>). Warna menunjukkan status: <span style="color:#198754">hijau</span> = lulus, <span style="color:#dc3545">merah</span> = tidak lulus.',
						side: 'left'
					}
				},
				{
					element: el('.keterangan-display', 'span.keterangan-display'),
					popover: {
						title: '<i class="bi bi-tag text-warning"></i> Keterangan (Lulus/Tidak Lulus)',
						description: 'Kolom <b>Keterangan</b> menampilkan status kelulusan mahasiswa (<b>Lulus</b> atau <b>Tidak Lulus</b>) berdasarkan nilai akhir dan konfigurasi huruf mutu sistem.',
						side: 'left'
					}
				},
				{
					element: el('button[type="submit"].btn-primary', 'button.btn-primary[type="submit"]'),
					popover: {
						title: '<i class="bi bi-save text-primary"></i> Simpan Perubahan',
						description: 'Klik <b>Simpan Perubahan</b> untuk menyimpan seluruh nilai ke database. Sistem akan memvalidasi semua input sebelum menyimpan. Tombol ini tidak tersedia jika nilai sudah divalidasi. Nilai yang terkunci hanya dapat diubah setelah admin membatalkan validasi.',
						side: 'left'
					}
				},
				{
					element: el('.card-footer .text-muted', '#saveStatus'),
					popover: {
						title: '<i class="bi bi-info-circle text-secondary"></i> Ringkasan Data',
						description: 'Bagian footer menampilkan ringkasan: jumlah mahasiswa dan jumlah teknik penilaian yang ada.',
						side: 'top'
					}
				}
			]
		},

		// ─── MBKM ────────────────────────────────────────────────────────────

		'mbkm/input-nilai': {
			nextKey: null,
			nextUrl: null,
			steps: [
				{
					element: el('h2.fw-bold', 'h2'),
					popover: {
						title: '<i class="bi bi-mortarboard-fill text-primary"></i> Input Penilaian MBKM',
						description: 'Halaman ini digunakan untuk menginput nilai mahasiswa yang mengikuti program <b>MBKM (Merdeka Belajar – Kampus Merdeka)</b>. Satu nilai dimasukkan per mata kuliah, kemudian sistem mendistribusikan nilai tersebut ke seluruh CPMK yang terkait secara proporsional berdasarkan bobot masing-masing.',
						side: 'bottom'
					}
				},
				{
					element: el('.card.border-0.shadow-sm.mb-4 .card-body', '.card-body.bg-light'),
					popover: {
						title: '<i class="bi bi-person-badge text-info"></i> Informasi Mahasiswa',
						description: 'Bagian ini menampilkan identitas mahasiswa MBKM yang sedang dinilai: <b>NIM</b>, <b>Nama Lengkap</b>, dan <b>Program Studi</b>. Pastikan data mahasiswa sudah benar sebelum memasukkan nilai.',
						side: 'bottom'
					}
				},
				{
					element: el('.bg-light.border-bottom .text-muted', '.bg-light.border-bottom small'),
					popover: {
						title: '<i class="bi bi-info-circle text-warning"></i> Cara Pengisian Nilai',
						description: 'Masukkan <b>satu nilai (0–100) per mata kuliah</b>. Sistem akan secara otomatis mendistribusikan nilai ini ke semua CPMK yang terkait sesuai persentase bobot masing-masing CPMK yang ditampilkan pada kolom <b>CPMK & Bobot</b>.',
						side: 'bottom'
					}
				},
				{
					element: el('#nilaiTable', '.modern-table', 'table'),
					popover: {
						title: '<i class="bi bi-table text-info"></i> Tabel Konversi Mata Kuliah MBKM',
						description: 'Tabel ini menampilkan seluruh mata kuliah yang dikonversi dari kegiatan MBKM mahasiswa. Setiap baris merupakan satu mata kuliah dengan informasi kode, nama, SKS, daftar CPMK beserta bobotnya, dan kolom input nilai.',
						side: 'top'
					}
				},
				{
					element: el('td code.fw-semibold', 'code.fw-semibold'),
					popover: {
						title: '<i class="bi bi-code-slash text-secondary"></i> Kode & Nama Mata Kuliah',
						description: 'Kolom <b>Kode MK</b> dan <b>Nama Mata Kuliah</b> menampilkan identitas MK yang dikonversi dari kegiatan MBKM. Kolom <b>SKS</b> menunjukkan jumlah kredit yang diakui.',
						side: 'right'
					}
				},
				{
					element: el('.badge.bg-secondary', 'span.badge.bg-secondary'),
					popover: {
						title: '<i class="bi bi-tags-fill text-secondary"></i> CPMK & Bobot Distribusi',
						description: 'Setiap badge menampilkan <b>kode CPMK</b> dan <b>bobot (%)</b>-nya. Hover pada badge untuk melihat deskripsi CPMK. Jika MK belum memiliki pemetaan CPL-CPMK atau belum ada RPS, kolom ini menampilkan peringatan dan kolom nilai tidak dapat diisi.',
						side: 'right'
					}
				},
				{
					element: el('input.nilai-input', '.nilai-input'),
					popover: {
						title: '<i class="bi bi-input-cursor-text text-primary"></i> Input Nilai (0–100)',
						description: 'Masukkan nilai akhir mahasiswa untuk setiap mata kuliah di kolom ini. Kolom ini hanya muncul jika MK sudah memiliki pemetaan CPMK dan RPS yang lengkap.',
						side: 'left'
					}
				},
				{
					element: el('button[type="submit"].btn-primary', 'button.btn-primary[type="submit"]'),
					popover: {
						title: '<i class="bi bi-save text-primary"></i> Simpan Perubahan',
						description: 'Klik <b>Simpan Perubahan</b> untuk menyimpan seluruh nilai ke database. Sistem akan memeriksa validitas semua input sebelum menyimpan. Nilai yang tersimpan akan langsung mempengaruhi perhitungan capaian CPMK dan CPL mahasiswa MBKM.',
						side: 'left'
					}
				},
				{
					element: null,
					popover: {
						title: '<i class="bi bi-calculator text-success"></i> Cara Hitung Nilai CPMK',
						description: 'Setelah nilai disimpan, sistem menghitung <b>nilai CPMK</b> untuk setiap CPMK yang terkait dengan mata kuliah tersebut menggunakan rumus:<br><br><code>Nilai CPMK = (Nilai MK × Bobot CPMK) ÷ 100</code><br><br><b>Contoh:</b> Nilai MK = <b>80</b>, Bobot CPMK-1 = <b>60%</b>, Bobot CPMK-2 = <b>40%</b><br>→ Nilai CPMK-1 = (80 × 60) ÷ 100 = <b>48</b><br>→ Nilai CPMK-2 = (80 × 40) ÷ 100 = <b>32</b><br><br>Bobot masing-masing CPMK diambil dari total bobot pertemuan yang tercatat di <b>RPS (Rencana Pembelajaran Semester)</b>.'
					}
				},
				{
					element: null,
					popover: {
						title: '<i class="bi bi-percent text-warning"></i> Cara Hitung Capaian CPMK & CPL',
						description: '<b>Capaian CPMK (%)</b> menunjukkan seberapa besar mahasiswa mencapai target CPMK:<br><br><code>Capaian CPMK (%) = (Nilai CPMK ÷ Bobot) × 100</code><br><br><b>Contoh:</b> Nilai CPMK-1 = <b>48</b>, Bobot = <b>60</b> → Capaian = <b>80%</b><br><br>Sedangkan <b>Capaian CPL (%)</b> dihitung dari seluruh CPMK yang terkait dengan satu CPL:<br><br><code>Capaian CPL (%) = (Σ Nilai CPMK) ÷ (Σ Bobot) × 100</code><br><br>Capaian kelas merupakan rata-rata capaian CPL seluruh mahasiswa di kelas tersebut.'
					}
				}
			]
		},

		// ─── STAGE 6: ANALISIS & PELAPORAN ───────────────────────────────────

		'capaian-cpmk': {
			nextKey: 'capaian-cpl',
			nextUrl: 'admin/capaian-cpl',
			steps: [
				{
					element: el('h2.mb-4', 'h2'),
					popover: {
						title: '<i class="bi bi-bar-chart-fill text-primary"></i> Capaian CPMK',
						description: 'Halaman analisis capaian CPMK. Lihat seberapa besar persentase mahasiswa yang berhasil mencapai setiap CPMK per mata kuliah.',
						side: 'bottom'
					}
				},
				{
					element: el('#cpmkTabs', '.modern-tab-nav'),
					popover: {
						title: '<i class="bi bi-tabs text-secondary"></i> Mode Tampilan',
						description: 'Pilih mode analisis: <b>Mahasiswa</b> (per individual), <b>Angkatan</b> (seluruh individual per angkatan), atau <b>Keseluruhan</b> (Seluruh angkatan).',
						side: 'bottom'
					}
				},
				{
					element: el('#filter1-card', '.filter-card'),
					popover: {
						title: '<i class="bi bi-funnel text-info"></i> Pilih Filter Analisis',
						description: 'Klik kartu filter untuk memilih jenis analisis yang diinginkan, lalu isi parameter filter (mata kuliah, semester, dll) untuk melihat hasilnya.',
						side: 'bottom'
					}
				}
			]
		},

		'capaian-cpl': {
			nextKey: 'laporan-cpmk',
			nextUrl: 'admin/laporan-cpmk',
			steps: [
				{
					element: el('h2.mb-4', 'h2'),
					popover: {
						title: '<i class="bi bi-graph-up-arrow text-success"></i> Capaian CPL',
						description: 'Halaman analisis capaian CPL program studi. Tampilkan grafik seberapa besar mahasiswa mencapai setiap CPL berdasarkan nilai yang telah diinput.',
						side: 'bottom'
					}
				},
				{
					element: el('#cplTabs', '.modern-tab-nav'),
					popover: {
						title: '<i class="bi bi-tabs text-secondary"></i> Mode Tampilan CPL',
						description: 'Pilih antara tampilan <b>Mahasiswa</b> (Per individual), <b>Angkatan</b> (Seluruh individual satu angkatan), atau <b>Keseluruhan</b> (Seluruh angkatan).',
						side: 'bottom'
					}
				},
				{
					element: el('#filter1-card', '.filter-card'),
					popover: {
						title: '<i class="bi bi-funnel text-info"></i> Filter & Analisis',
						description: 'Pilih filter Keseluruhan (Mahasiswa, Angkatan), per semester, atau per tahun akademik untuk melihat capaian CPL yang relevan. Hasil dapat diekspor ke Excel.',
						side: 'bottom'
					}
				}
			]
		},

		'laporan-cpmk': {
			nextKey: 'laporan-cpl',
			nextUrl: 'admin/laporan-cpl',
			steps: [
				{
					element: el('h2.fw-bold', 'h2', 'h1'),
					popover: {
						title: '<i class="bi bi-file-earmark-text-fill text-primary"></i> Portofolio CPMK',
						description: 'Halaman ini menghasilkan portofolio mata kuliah yang berisi capaian CPMK, analisis hasil belajar, dan dokumen pendukung untuk keperluan akreditasi.',
						side: 'bottom'
					}
				},
				{
					element: el('.btn.btn-primary', '.btn-primary'),
					popover: {
						title: '<i class="bi bi-file-earmark-pdf text-danger"></i> Generate Portofolio',
						description: 'Pilih mata kuliah dan semester, lalu klik Generate untuk membuat dokumen portofolio. Dokumen dapat diunduh dalam format PDF atau ZIP.',
						side: 'left'
					}
				}
			]
		},

		'laporan-cpmk/generate': {
			nextKey: null,
			nextUrl: null,
			steps: [
				{
					element: el('#portfolio-content', '.card.shadow-sm'),
					popover: {
						title: '<i class="bi bi-file-earmark-text-fill text-primary"></i> Portofolio Mata Kuliah',
						description: 'Halaman ini menampilkan <b>dokumen portofolio mata kuliah</b> yang digunakan untuk keperluan evaluasi dan akreditasi OBE. Portofolio terdiri dari 6 bagian: Identitas MK, CPMK, Rencana Penilaian, Analisis Pencapaian, CQI, dan Dokumen Pendukung.',
						side: 'bottom'
					}
				},
				{
					element: el('button[onclick="exportToPDF()"]', '.btn.btn-success'),
					popover: {
						title: '<i class="bi bi-file-earmark-zip text-success"></i> Download ZIP',
						description: 'Klik <b>Download ZIP</b> untuk mengunduh seluruh portofolio dalam satu paket ZIP. File ZIP berisi dokumen PDF portofolio beserta semua dokumen pendukung yang telah dipilih (RPS, daftar nilai, rekapitulasi, rubrik, contoh soal, dan notulen rapat).',
						side: 'left'
					}
				},
				{
					element: el('#portfolio-content .card-body > div:nth-child(2) h5', '.section h5'),
					popover: {
						title: '<i class="bi bi-card-list text-secondary"></i> Bagian 1 – Identitas Mata Kuliah',
						description: 'Bagian pertama menampilkan identitas lengkap mata kuliah: <b>nama MK</b>, <b>kode MK</b>, <b>program studi</b>, <b>semester</b>, <b>tahun akademik</b>, <b>SKS</b>, dan <b>dosen pengampu</b>. Data ini diambil otomatis dari RPS dan jadwal yang telah tersimpan di sistem.',
						side: 'bottom'
					}
				},
				{
					element: el('#portfolio-content .card-body > div:nth-child(3) h5'),
					popover: {
						title: '<i class="bi bi-check2-all text-success"></i> Bagian 2 – CPMK',
						description: 'Bagian kedua menampilkan daftar <b>Capaian Pembelajaran Mata Kuliah (CPMK)</b> beserta kode dan deskripsinya. CPMK merupakan kemampuan spesifik yang harus dicapai mahasiswa setelah menyelesaikan mata kuliah ini dan menjadi dasar penilaian seluruh komponen asesmen.',
						side: 'bottom'
					}
				},
				{
					element: el('#portfolio-content .card-body > div:nth-child(4) h5'),
					popover: {
						title: '<i class="bi bi-clipboard2-data text-info"></i> Bagian 3 – Rencana dan Realisasi Penilaian',
						description: 'Tabel ini membandingkan <b>rencana penilaian</b> (dari RPS) dengan <b>realisasi</b> aktual. Terdapat dua kolom kunci:<br><br><b>• Nilai Rata-rata Mahasiswa</b> — rata-rata nilai mentah (0–100) yang diperoleh seluruh mahasiswa di kelas untuk teknik penilaian CPMK tersebut. Dihitung dari: <code>Σ nilai_mahasiswa ÷ jumlah_mahasiswa</code>.<br><br><b>• Rata-rata Capaian (%)</b> — persentase ketercapaian CPMK, dihitung dengan: <code>(Nilai Rata-rata ÷ Nilai Maksimal) × 100</code>. Ditampilkan <span style="color:#198754">hijau</span> jika ≥ standar minimal, <span style="color:#dc3545">merah</span> jika di bawah standar.<br><br>Standar minimal capaian dikonfigurasi di menu <b>Pengaturan Sistem</b> (admin only).',
						side: 'bottom'
					}
				},
				{
					element: el('#analysis-display', '#analysis-edit'),
					popover: {
						title: '<i class="bi bi-bar-chart-fill text-warning"></i> Bagian 4 – Analisis Pencapaian CPMK',
						description: 'Bagian keempat berisi <b>analisis pencapaian CPMK</b> dalam bentuk narasi. Analisis ini menjelaskan seberapa besar mahasiswa berhasil mencapai setiap CPMK, faktor-faktor yang mempengaruhi, dan kesimpulan umum. Teks analisis dapat diisi secara <b>otomatis</b> (dipilih dari template yang tersedia) atau <b>manual</b> (ditulis sendiri).',
						side: 'top'
					}
				},
				{
					element: el('button[onclick="toggleEditAnalysis()"]', '.btn-outline-primary'),
					popover: {
						title: '<i class="bi bi-pencil-square text-primary"></i> Edit Analisis Pencapaian',
						description: 'Klik tombol <b>Edit</b> untuk mengubah teks analisis. Tersedia dua mode:<br><br><b>• Mode Otomatis:</b> Pilih satu dari beberapa template narasi yang sudah tersedia (dihasilkan dari data capaian aktual). Setiap template dapat diedit lebih lanjut.<br><b>• Mode Manual:</b> Tulis analisis secara bebas sesuai penilaian dosen.<br><br>Klik <b>Simpan Analisis</b> setelah selesai.',
						side: 'left'
					}
				},
				{
					element: el('#cqi-display', '#cqi-edit'),
					popover: {
						title: '<i class="bi bi-arrow-repeat text-danger"></i> Bagian 5 – Tindak Lanjut & CQI',
						description: '<b>CQI (Continuous Quality Improvement)</b> atau Tindak Lanjut adalah rencana perbaikan berdasarkan hasil analisis pencapaian CPMK. Bagian ini berisi tabel dengan kolom: <b>Masalah</b> (kendala yang ditemukan), <b>Akar Masalah</b> (penyebab utama), <b>Tindak Lanjut</b> (solusi yang direncanakan), dan <b>Target</b> (waktu pelaksanaan). Data CQI sangat penting untuk laporan akreditasi.',
						side: 'top'
					}
				},
				{
					element: el('button[onclick="toggleEditCqi()"]', '.btn.btn-sm.btn-outline-primary'),
					popover: {
						title: '<i class="bi bi-pencil-square text-primary"></i> Edit Tindak Lanjut CQI',
						description: 'Klik tombol <b>Edit</b> pada bagian CQI untuk mengisi atau mengubah rencana tindak lanjut. Isi setiap baris dengan <b>masalah</b> yang teridentifikasi, <b>akar penyebab</b>-nya, <b>tindakan perbaikan</b> yang akan dilakukan, serta <b>target waktu</b> pelaksanaan. Klik <b>Simpan CQI</b> setelah selesai.',
						side: 'left'
					}
				},
				{
					element: el('#doc_rps', '.document-checkbox'),
					popover: {
						title: '<i class="bi bi-folder2-open text-warning"></i> Bagian 6 – Dokumen Pendukung',
						description: 'Bagian terakhir berisi daftar <b>dokumen pendukung</b> yang akan disertakan dalam paket ZIP portofolio. Centang dokumen yang ingin dimasukkan:<br><br><b>• RPS</b> – Rencana Pembelajaran Semester (tersedia otomatis)<br><b>• Daftar Nilai</b> – Nilai mahasiswa per teknik penilaian<br><b>• Rekapitulasi</b> – Nilai per CPMK<br><b>• Rubrik Penilaian</b> – Upload file PDF/Word<br><b>• Contoh Soal</b> – Upload file PDF/Word<br><b>• Notulen Rapat</b> – Upload file PDF/Word (opsional)',
						side: 'top'
					}
				},
				{
					element: el('button[onclick="selectAllDocuments()"]', '.btn-outline-primary'),
					popover: {
						title: '<i class="bi bi-check2-square text-primary"></i> Pilih / Batal Pilih Semua Dokumen',
						description: 'Gunakan tombol <b>Pilih Semua</b> untuk mencentang seluruh dokumen yang tersedia sekaligus, atau <b>Batal Pilih Semua</b> untuk mengosongkan semua centang. Untuk dokumen yang memerlukan upload (Rubrik, Contoh Soal, Notulen), tombol centang hanya aktif setelah file berhasil diunggah.',
						side: 'bottom'
					}
				},
				{
					element: el('#rubrik_file_input ~ button', 'button[onclick*="uploadRubrik"], button[onclick*="uploadContohSoal"], button[onclick*="uploadNotulen"]', '.btn.btn-sm.btn-outline-primary[onclick*="click"]'),
					popover: {
						title: '<i class="bi bi-upload text-primary"></i> Upload Dokumen Pendukung',
						description: 'Klik tombol <b>Upload</b> di samping Rubrik Penilaian, Contoh Soal, atau Notulen untuk mengunggah file pendukung (format PDF, DOC, atau DOCX). Setelah berhasil diunggah, dokumen dapat dipreview, dihapus, dan akan otomatis tersedia untuk dimasukkan ke dalam paket ZIP portofolio.',
						side: 'left'
					}
				},
				{
					popover: {
						title: '<i class="bi bi-trophy-fill text-warning"></i> Portofolio Siap Diunduh!',
						description: 'Setelah semua bagian diisi — analisis pencapaian CPMK, tindak lanjut CQI, dan dokumen pendukung diunggah — klik tombol <b>Download ZIP</b> di bagian atas untuk mengunduh portofolio lengkap. File ZIP ini siap digunakan untuk keperluan <b>evaluasi internal</b> maupun <b>akreditasi program studi</b>.'
					}
				}
			]
		},

		'laporan-cpl': {
			nextKey: null,
			nextUrl: 'admin/dashboard',
			steps: [
				{
					element: el('h2.fw-bold', 'h2', 'h1'),
					popover: {
						title: '<i class="bi bi-file-earmark-check-fill text-success"></i> Portofolio CPL',
						description: 'Halaman akhir dari siklus OBE. Hasilkan laporan pemenuhan CPL program studi, lengkap dengan analisis CQI (Continuous Quality Improvement).',
						side: 'bottom'
					}
				},
				{
					element: el('.btn.btn-primary', '.btn-primary'),
					popover: {
						title: '<i class="bi bi-file-earmark-zip-fill text-warning"></i> Generate & Unduh Laporan',
						description: 'Generate laporan CPL per angkatan. Laporan dapat diunduh sebagai PDF individual atau paket ZIP untuk semua CPL sekaligus.',
						side: 'left'
					}
				},
				{
					popover: {
						title: '<i class="bi bi-trophy-fill text-warning"></i> Tour Selesai!',
						description: 'Anda telah menyelesaikan tour lengkap sistem OBE TI UPR. Ikuti urutan 6 tahapan dalam panduan dashboard untuk memulai pengisian data. Selamat menggunakan sistem! 🎉',
					}
				}
			]
		},

		'laporan-cpl/generate': {
			nextKey: null,
			nextUrl: null,
			steps: [
				{
					element: el('#portfolio-content', '.card.shadow-sm'),
					popover: {
						title: '<i class="bi bi-file-earmark-check-fill text-success"></i> Laporan Pemenuhan CPL',
						description: 'Halaman ini menampilkan <b>Laporan Pemenuhan Capaian Pembelajaran Lulusan (CPL)</b> — dokumen resmi yang merangkum seberapa besar seluruh CPL program studi telah dicapai oleh angkatan yang dipilih. Laporan ini terdiri dari 8 bagian dan digunakan untuk evaluasi kurikulum serta akreditasi.',
						side: 'bottom'
					}
				},
				{
					element: el('button[onclick="exportToZIP()"]', '.btn.btn-success'),
					popover: {
						title: '<i class="bi bi-file-earmark-zip text-success"></i> Download ZIP',
						description: 'Klik <b>Download ZIP</b> untuk mengunduh laporan lengkap dalam satu paket. File ZIP berisi dokumen PDF laporan beserta seluruh lampiran yang telah dipilih (rekap CPMK, matriks CPL–CPMK–MK, RPS MK kontributor, bukti dokumentasi, dan notulensi rapat).',
						side: 'left'
					}
				},
				{
					element: el('#portfolio-content .card-body > div:nth-child(2) h5', '.section h5'),
					popover: {
						title: '<i class="bi bi-building text-secondary"></i> Bagian 1 – Identitas Program Studi',
						description: 'Berisi identitas resmi program studi: <b>nama prodi</b>, <b>fakultas</b>, <b>perguruan tinggi</b>, <b>tahun akademik</b>, <b>angkatan</b>, dan <b>ketua prodi</b>. Data diambil otomatis dari pengaturan sistem.',
						side: 'bottom'
					}
				},
				{
					element: el('#portfolio-content .card-body > div:nth-child(3) h5'),
					popover: {
						title: '<i class="bi bi-bullseye text-danger"></i> Bagian 2 – Daftar CPL Program Studi',
						description: 'Menampilkan seluruh <b>Capaian Pembelajaran Lulusan (CPL)</b> program studi beserta kode, deskripsi lengkap, dan sumber turunannya. CPL adalah kemampuan akhir yang harus dimiliki setiap lulusan.',
						side: 'bottom'
					}
				},
				{
					element: el('#portfolio-content .card-body > div:nth-child(4) h5'),
					popover: {
						title: '<i class="bi bi-diagram-3-fill text-info"></i> Bagian 3 – Matriks CPMK terhadap CPL',
						description: 'Menampilkan tabel silang yang memetakan <b>CPMK dari setiap Mata Kuliah</b> ke <b>CPL program studi</b>. Matriks ini membuktikan bahwa setiap CPL didukung oleh minimal satu CPMK dari MK kontributor, sesuai dengan struktur kurikulum OBE yang telah dikonfigurasi.',
						side: 'bottom'
					}
				},
				{
					element: el('#portfolio-content .card-body > div:nth-child(5) h5'),
					popover: {
						title: '<i class="bi bi-bar-chart-fill text-warning"></i> Bagian 4 – Rekapitulasi Capaian CPL',
						description: 'Tabel kunci yang menampilkan <b>capaian aktual setiap CPL</b> untuk angkatan yang dipilih. Setiap baris CPL menampilkan CPMK-CPMK kontributor beserta nilai per kolom:<br><br><b>① Rata-rata CPMK</b> — rata-rata sederhana nilai CPMK seluruh mahasiswa: <code>Σ nilai CPMK ÷ jumlah mahasiswa angkatan terkait</code> pada <b>mata kuliah kontributor</b>. Bobot dalam tanda kurung adalah total bobot CPMK dari <b>mata kuliah kontributor</b>.<br><br><b>② CPL (Total Bobot)</b> — penjumlahan seluruh <code>nilai CPMK</code> dari semua mahasiswa dan semua CPMK kontributor CPL ini, dengan total bobot semua CPMK kontributor dalam tanda kurung.<br><br><b>③ Rata-rata Capaian CPL (%)</b> — rata-rata persentase capaian CPL per angkatan per tahun akademik: <code>(Σ Capaian CPL Mahasiswa) ÷ (Total Mahasiswa)</code>. Ditampilkan <span style="color:#198754">hijau</span> jika ≥ standar minimal, <span style="color:#dc3545">merah</span> jika tidak tercapai.',
						side: 'top'
					}
				},
				{
					element: el('#section-analisis-cpl'),
					popover: {
						title: '<i class="bi bi-search text-danger"></i> Bagian 5 – Analisis Pemenuhan CPL',
						description: 'Bagian analisis yang memuat:<br><br><b>• Standar Minimal Capaian</b> — ambang batas yang dikonfigurasi di Pengaturan Sistem.<br><b>• CPL Tercapai</b> — daftar CPL yang melampaui standar minimal.<br><b>• CPL Tidak Tercapai</b> — daftar CPL yang belum memenuhi standar beserta persentase capaiannya.<br><b>• Penyebab Ketidakcapaian</b> — narasi analisis yang bisa diisi otomatis (dari template) atau manual.<br><b>• Keterangan Tambahan</b> — catatan bebas untuk konteks evaluasi lebih lanjut.',
						side: 'top'
					}
				},
				{
					element: el('button[onclick="toggleEditPenyebab()"]', 'button[onclick="toggleEditAnalysis()"]', '.btn.btn-sm.btn-outline-primary'),
					popover: {
						title: '<i class="bi bi-pencil-square text-primary"></i> Edit Analisis Pemenuhan CPL',
						description: 'Klik tombol <b>Edit</b> untuk mengisi atau mengubah narasi analisis. Tersedia dua mode:<br><br><b>• Mode Otomatis (Penyebab):</b> Pilih dari template narasi yang dihasilkan berdasarkan data CPL tidak tercapai. Template dapat diedit secara inline sebelum disimpan.<br><b>• Mode Manual:</b> Tulis teks secara bebas.<br><br>Keterangan tambahan selalu diisi secara manual. Klik <b>Simpan</b> setelah selesai.',
						side: 'left'
					}
				},
				{
					element: el('#cqi-display', '#cqi-edit'),
					popover: {
						title: '<i class="bi bi-arrow-repeat text-danger"></i> Bagian 6 – Tindak Lanjut & CQI',
						description: '<b>CQI (Continuous Quality Improvement)</b> berisi rencana perbaikan untuk setiap <b>CPL yang tidak tercapai</b>. Baris otomatis dibuat untuk setiap CPL di bawah standar dengan kolom: <b>Kode CPL</b>, <b>Masalah</b>, <b>Rencana Perbaikan</b>, <b>Penanggung Jawab</b>, dan <b>Jadwal Pelaksanaan</b>. Jika semua CPL tercapai, bagian ini kosong.',
						side: 'top'
					}
				},
				{
					element: el('button[onclick="toggleEditCqi()"]', '.btn.btn-sm.btn-outline-primary'),
					popover: {
						title: '<i class="bi bi-pencil-square text-primary"></i> Edit CQI',
						description: 'Klik <b>Edit</b> untuk mengisi atau menyesuaikan rencana CQI per CPL. Setiap CPL tidak tercapai mendapat satu kartu form dengan field masalah, rencana perbaikan, penanggung jawab, dan jadwal. Default awal sudah terisi otomatis — edit sesuai kondisi aktual. Klik <b>Simpan CQI</b> setelah selesai.',
						side: 'left'
					}
				},
				{
					element: el('#portfolio-content .card-body > div:nth-child(8) h5'),
					popover: {
						title: '<i class="bi bi-card-text text-secondary"></i> Bagian 7 – Kesimpulan Umum',
						description: 'Bagian ini menyajikan <b>kesimpulan otomatis</b> dari hasil analisis pemenuhan CPL: jumlah CPL yang tercapai vs tidak tercapai, persentase pemenuhan keseluruhan, dan rekomendasi umum tindakan selanjutnya.',
						side: 'bottom'
					}
				},
				{
					element: el('#lampiran_rekap_cpmk', '.lampiran-checkbox'),
					popover: {
						title: '<i class="bi bi-paperclip text-warning"></i> Bagian 8 – Lampiran',
						description: 'Pilih dokumen yang akan disertakan dalam paket ZIP:<br><br><b>• Rekap Nilai CPMK</b> — Excel rekap nilai CPMK dari seluruh MK kontributor (unduh per MK)<br><b>• Matriks CPL–CPMK–MK</b> — Excel matriks pemetaan kurikulum<br><b>• RPS MK Kontributor</b> — dokumen Word RPS per MK<br><b>• Bukti Dokumentasi Asesmen</b> — upload PDF/Word<br><b>• Notulensi Rapat Evaluasi CPL</b> — upload PDF/Word',
						side: 'top'
					}
				},
				{
					element: el('button[onclick="selectAllLampiran()"]', '.btn-outline-primary'),
					popover: {
						title: '<i class="bi bi-check2-square text-primary"></i> Pilih / Batal Pilih Semua Lampiran',
						description: 'Gunakan tombol <b>Pilih Semua</b> untuk mencentang seluruh lampiran yang tersedia sekaligus. Lampiran yang memerlukan upload (Bukti Dokumentasi, Notulensi Rapat) hanya dapat dicentang setelah file berhasil diunggah.',
						side: 'bottom'
					}
				},
				{
					element: el('button[onclick*="bukti_dokumentasi_file_input"]', 'button[onclick*="notulensi_rapat_file_input"]'),
					popover: {
						title: '<i class="bi bi-upload text-primary"></i> Upload Lampiran',
						description: 'Klik <b>Upload</b> di samping Bukti Dokumentasi atau Notulensi Rapat untuk mengunggah file (PDF, DOC, DOCX). Setelah berhasil diunggah, file dapat dipreview, dihapus, dan otomatis tersedia untuk disertakan dalam ZIP. File ini tidak dapat diganti sebelum dihapus terlebih dahulu.',
						side: 'left'
					}
				},
				{
					popover: {
						title: '<i class="bi bi-trophy-fill text-warning"></i> Laporan Siap Diunduh!',
						description: 'Setelah semua bagian diisi — analisis pemenuhan CPL, tindak lanjut CQI, dan lampiran diunggah — klik tombol <b>Download ZIP</b> di bagian atas. File ZIP berisi laporan PDF lengkap beserta seluruh lampiran yang dipilih, siap untuk keperluan <b>evaluasi program studi</b> dan <b>akreditasi</b>.'
					}
				}
			]
		},

	};

	/**
	 * Detect which tour to run based on the current URL path.
	 * Returns the key or null.
	 * Also handles URLs with a trailing numeric ID (e.g. /input-nilai-teknik/5).
	 */
	function detectCurrentPage() {
		var path = window.location.pathname;
		// Strip optional trailing numeric segment (e.g. /123) so dynamic-ID pages match
		var pathNoId = path.replace(/\/\d+$/, '');
		// Check from most-specific to least-specific (sort by key length descending)
		var keys = Object.keys(PAGES).sort(function (a, b) { return b.length - a.length; });
		for (var i = 0; i < keys.length; i++) {
			var re = new RegExp('\\/' + keys[i] + '$');
			if (re.test(path) || re.test(pathNoId)) {
				return keys[i];
			}
		}
		return null;
	}

	/**
	 * Start the tour for a given page key.
	 * @param {string} pageKey
	 * @param {boolean} chainMode - if true, navigates to nextUrl on completion (used by auto-start via ?tour=1)
	 */
	function startPageTour(pageKey, chainMode) {
		var config = PAGES[pageKey];
		if (!config) return;

		var steps = config.steps.filter(function (step) {
			if (!step.element) return true; // centered popover
			return !!document.querySelector(step.element);
		});

		if (steps.length === 0) return;

		var completed = false;

		if (!window.driver || !window.driver.js) {
		console.error('OBE Tours: driver.js not loaded. CDN may be blocked.');
		return;
	}

	var driverInstance = window.driver.js.driver({
			showProgress: true,
			animate: true,
			smoothScroll: true,
			allowClose: true,
			overlayColor: '#000',
			overlayOpacity: 0.55,
			nextBtnText: 'Lanjut →',
			prevBtnText: '← Kembali',
			doneBtnText: (chainMode && config.nextUrl) ? 'Halaman Berikutnya ›' : 'Selesai',
			progressText: 'Langkah {{current}} dari {{total}}',
			popoverClass: 'driverjs-theme',
			onDestroyStarted: function () {
				// Check if user clicked "done" on last step
				if (!driverInstance.hasNextStep()) {
					completed = true;
				}
				driverInstance.destroy();
				if (chainMode && completed && config.nextUrl) {
					var sep = config.nextUrl.indexOf('?') === -1 ? '?' : '&';
					window.location.href = resolveUrl(config.nextUrl) + sep + 'tour=1&chain=1';
				} else {
					// Remove tour param from URL without reloading
					var url = new URL(window.location.href);
					url.searchParams.delete('tour');
					history.replaceState({}, '', url.toString());
				}
			},
			steps: steps
		});

		driverInstance.drive();
	}

	/**
	 * Auto-run tour if ?tour=1 is in the URL.
	 */
	function autoStart() {
		var params = new URLSearchParams(window.location.search);
		if (params.get('tour') !== '1') return;
		var pageKey = detectCurrentPage();
		if (pageKey) {
			var chainMode = params.get('chain') === '1';
			setTimeout(function () { startPageTour(pageKey, chainMode); }, 600);
		}
	}

	/**
	 * Resolve a relative URL using detected base path.
	 */
	function resolveUrl(relPath) {
		// Try to get base from existing links in the page
		var sample = document.querySelector('a[href*="/admin/"]');
		if (sample) {
			var m = sample.href.match(/^(https?:\/\/[^/]+(?:\/[^/]+)*)\/admin\//);
			if (m) return m[1] + '/' + relPath;
		}
		return window.location.origin + '/' + relPath;
	}

	// Public API
	return {
		start: startPageTour,
		detect: detectCurrentPage,
		autoStart: autoStart,
		resolveUrl: resolveUrl,
	};

})();

// Auto-start on page load
document.addEventListener('DOMContentLoaded', function () {
	window.OBE_TOURS.autoStart();
});

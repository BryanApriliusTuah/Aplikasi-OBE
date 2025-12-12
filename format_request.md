# Format Request

## Data Mahasiswa

API GET: /obe-mahasiswa

Response:

```json
"NIM" 			: "213030503137",
"Nama"			: "Mayrika Chinta",
"Program Studi"	: "Teknik Informatika",
"Fakultas" 		: "Teknik",
"Angkatan"		: 2021
```

## Data Dosen

API GET: /obe-dosen

Response:

```json
"NIP" 			: "197503151997031002",
"Nama"			: "Budi Santoso, S.Kom., M.T.",
"Program Studi"	: "Teknik Informatika",
"Fakultas" 		: "Teknik"
```

## Data Mata Kuliah

API GET: /obe-mata-kuliah
Response:

```json
"Kode MK"			: "1DCP101030",
"Nama Mata Kuliah"	: "Pengantar Teknologi Informasi",
"Tipe"				: ["Wajib","Umum", "..."],
"Semester"			: 1,
"Deskripsi Singkat" : "Pengenalan dasar teknologi informasi dan komputer"
```

## Data Jadwal Mengajar

API GET: /obe-jadwal
Response:

```json
{
	[
		"Kode MK"	: "1DCP101030",
		"Mahasiswa"	: {
			"NIM" : ["213030503137", "213030503138", "213030503139", "..."]
		}
	],
	[
		"Kode MK"	: "1DCP101031",
		"Mahasiswa"	: {
			"NIM" : ["213030503137", "213030503138", "213030503139", "..."]
		}
	]
}
```

## Data MBKM

API GET: /obe-mbkm
Response:

```json
{
	[
		"Jenis_MBKM"		: "Magang",
		"Kegiatan"			: "Dicoding Software Developer",
		"Tempat_MBKM"		: "Dicoding",
		"Tanggal Mulai"		: "01/01/2021",
		"Tanggal Selesai"	: "30/12/2021",
		"SKS Konversi"		: 20,
		"Dosen Pembimbing"	: "Budi Santoso, S.Kom., M.T.",
		"Mahasiswa"			: {
			"NIM" : ["213030503137", "213030503138", "213030503139", "..."]
		}
	],
}
```

## Data KRS

API GET: /obe-krs-mahasiswa
Response:

```json
{
	[
		"NIM"				: "2130305137",
		"Nama"				: "Mayrika",
		"Program Studi"		: "Teknik Informatika",
		"KRS": {
			[
				"Semester"			: "2021/2022 Ganjil",
				"Tahun Akademik"	: "2021/2022",
				"Mata Kuliah"		: {
					[
						"Kode MK"			: "1DCP101030",
						"Mata Kuliah"		: "Basis Data I",
						"Kelas"				: "A",
						"Dosen Koordinator"	: "Owi",
						"NIP"				: "12345"
					],
					[
						"Kode MK"			: "1DCP101031",
						"Mata Kuliah"		: "Basis Data II",
						"Kelas"				: "A",
						"Dosen Koordinator"	: "Owo",
						"NIP"				: "1234"
					],
				}
			],
			[
				"Semester"			: "2021/2022 Genap",
				"Tahun Akademik"	: "2021/2022",
				"Mata Kuliah"		: {
					[
						"Kode MK"			: "1DCP1010334",
						"Mata Kuliah"		: "Basis Data III",
						"Kelas"				: "A",
						"Dosen Koordinator"	: "Owi",
						"NIP"				: "12345"
					],
					[
						"Kode MK"			: "1DCP10103141",
						"Mata Kuliah"		: "Basis Data IV",
						"Kelas"				: "A",
						"Dosen Koordinator"	: "Owo",
						"NIP"				: "1234"
					],
				}
			], [...]
		}
	],
	[
		"NIM"				: "2130305138",
		"Nama"				: "Mayrika Chinta",
		"Program Studi"		: "Teknik Informatika",
		"KRS": {
			[
				"Semester"			: "2021/2022 Ganjil",
				"Tahun Akademik"	: "2021/2022",
				"Mata Kuliah"		: {
					[
						"Kode MK"			: "1DCP101030",
						"Mata Kuliah"		: "Basis Data I",
						"Kelas"				: "A",
						"Dosen Koordinator"	: "Owi",
						"NIP"				: "12345"
					],
					[
						"Kode MK"			: "1DCP101031",
						"Mata Kuliah"		: "Basis Data II",
						"Kelas"				: "A",
						"Dosen Koordinator"	: "Owo",
						"NIP"				: "1234"
					],
				}
			],
			[
				"Semester"			: "2021/2022 Genap",
				"Tahun Akademik"	: "2021/2022",
				"Mata Kuliah"		: {
					[
						"Kode MK"			: "1DCP1010334",
						"Mata Kuliah"		: "Basis Data III",
						"Kelas"				: "A",
						"Dosen Koordinator"	: "Owi",
						"NIP"				: "12345"
					],
					[
						"Kode MK"			: "1DCP10103141",
						"Mata Kuliah"		: "Basis Data IV",
						"Kelas"				: "A",
						"Dosen Koordinator"	: "Owo",
						"NIP"				: "1234"
					],
				}
			], [...]
		}
	], [...]
}
```

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
		"Peserta"	: {
			"NIM" : ["213030503137", "213030503138", "213030503139", "..."]
		}
	],
	[
		"Kode MK"	: "1DCP101031",
		"Peserta"	: {
			"NIM" : ["213030503137", "213030503138", "213030503139", "..."]
		}
	]
}
```

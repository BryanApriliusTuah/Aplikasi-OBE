<?php
namespace App\Services;

class RpsPreviewService
{
    public static function getData($id)
    {
        $db = \Config\Database::connect();

        $rps = $db->table('rps')
            ->select('rps.*, mata_kuliah.nama_mk, mata_kuliah.kode_mk, mata_kuliah.sks, mata_kuliah.kategori, mata_kuliah.deskripsi_singkat')
            ->join('mata_kuliah', 'mata_kuliah.id = rps.mata_kuliah_id')
            ->where('rps.id', $id)
            ->get()->getRowArray();

        $relasi = $db->table('rps_pengampu')
            ->select('dosen.id, dosen.nama_lengkap, dosen.nip, rps_pengampu.peran')
            ->join('dosen', 'dosen.id = rps_pengampu.dosen_id')
            ->where('rps_pengampu.rps_id', $id)
            ->get()->getResultArray();

        $koordinator = '';
        $koordinator_nip = '';
        $pengampu = [];
        foreach ($relasi as $r) {
            if ($r['peran'] == 'koordinator') {
                $koordinator = $r['nama_lengkap'];
                $koordinator_nip = $r['nip'];
            } else {
                $pengampu[] = $r['nama_lengkap'];
            }
        }
        
        $cpl = $db->table('cpl')
            ->select('cpl.id, cpl.kode_cpl, cpl.deskripsi, cpl.jenis_cpl')
            ->join('cpl_mk', 'cpl.id = cpl_mk.cpl_id')
            ->where('cpl_mk.mata_kuliah_id', $rps['mata_kuliah_id'])
            ->get()->getResultArray();

        $cpmk = $db->table('cpmk')
            ->select('cpmk.*')
            ->join('cpmk_mk', 'cpmk.id = cpmk_mk.cpmk_id')
            ->where('cpmk_mk.mata_kuliah_id', $rps['mata_kuliah_id'])
            ->get()->getResultArray();

        $subcpmk_rows = $db->table('sub_cpmk')
            ->select('sub_cpmk.id, sub_cpmk.cpmk_id, sub_cpmk.kode_sub_cpmk, sub_cpmk.deskripsi')
            ->join('sub_cpmk_mk', 'sub_cpmk.id = sub_cpmk_mk.sub_cpmk_id')
            ->where('sub_cpmk_mk.mata_kuliah_id', $rps['mata_kuliah_id'])
            ->get()->getResultArray();
        
        $subcpmk_map = [];
        foreach($subcpmk_rows as $row) {
            $subcpmk_map[$row['id']] = $row;
        }

        usort($subcpmk_rows, function($a, $b) {
            return strnatcasecmp($a['kode_sub_cpmk'] ?? '', $b['kode_sub_cpmk'] ?? '');
        });

        $mingguan_rows = $db->table('rps_mingguan')
            ->where('rps_id', $id)
            ->orderBy('minggu', 'asc')
            ->get()->getResultArray();

        $mingguan_processed = [];
        foreach($mingguan_rows as $m) {
            
            $subcpmk_id = $m['sub_cpmk_id'];
            if (isset($subcpmk_map[$subcpmk_id])) {
                $m['sub_cpmk_formatted'] = esc($subcpmk_map[$subcpmk_id]['kode_sub_cpmk']);
            } else {
                $m['sub_cpmk_formatted'] = '-';
            }
            
            $jsonFormatter = function ($jsonString) {
                if (empty($jsonString) || $jsonString == 'null' || $jsonString == '[]') return '-';
                $data = json_decode($jsonString, true);
                if (is_array($data) && !empty($data)) {
                    return esc(implode(', ', $data));
                }
                return esc($jsonString); 
            };

            $m['indikator_formatted'] = $jsonFormatter($m['indikator']);
            $m['kriteria_penilaian_formatted'] = $jsonFormatter($m['kriteria_penilaian']);
            $m['metode_pembelajaran_formatted'] = $jsonFormatter($m['metode']);
            
            $teknik_formatted = [];
            $teknik_list = json_decode($m['teknik_penilaian'], true);
            $teknik_labels = [
                'partisipasi' => 'Partisipasi', 'observasi' => 'Observasi', 'unjuk_kerja' => 'Unjuk Kerja',
                'proyek' => 'Proyek', 'tes_tulis_uts' => 'Tes Tulis (UTS)', 'tes_tulis_uas' => 'Tes Tulis (UAS)', 'tes_lisan' => 'Tes Lisan'
            ];
            if (is_array($teknik_list)) {
                foreach ($teknik_list as $key => $value) {
                    if ($value > 0) {
                        $label = $teknik_labels[$key] ?? ucfirst($key);
                        $teknik_formatted[] = esc($label . ' (' . $value . ')');
                    }
                }
            }
            $m['teknik_penilaian_formatted'] = !empty($teknik_formatted) ? implode(', ', $teknik_formatted) : '-';
            
            $mingguan_processed[] = $m;
        }

        $materi_pembelajaran = [];
        foreach($mingguan_rows as $m) {
            if (!empty($m['materi_pembelajaran'])) {
                foreach (explode("\n", $m['materi_pembelajaran']) as $item) {
                    $item = trim($item);
                    if ($item && !in_array($item, $materi_pembelajaran)) {
                        $materi_pembelajaran[] = $item;
                    }
                }
            }
        }

        $referensi = $db->table('rps_referensi')->where('rps_id', $id)->get()->getResultArray();
        $referensi_utama = [];
        $referensi_pendukung = [];
        foreach($referensi as $ref) {
            if ($ref['tipe'] == 'utama') $referensi_utama[] = $ref;
            else $referensi_pendukung[] = $ref;
        }

        $mk_prasyarat = [];
        $prasyarat_rows = $db->table('mk_prasyarat')->where('mata_kuliah_id', $rps['mata_kuliah_id'])->get()->getResultArray();
        if ($prasyarat_rows) {
            $ids = array_column($prasyarat_rows, 'prasyarat_mk_id');
            if ($ids) {
                $mk_prasyarat = $db->table('mata_kuliah')->select('kode_mk, nama_mk')->whereIn('id', $ids)->get()->getResultArray();
            }
        }

        $profil = $db->table('profil_prodi')->get()->getRowArray();

        $jenisCPL = [
            'S'  => 'Sikap (S)', 'P'  => 'Pengetahuan (P)',
            'KU' => 'Keterampilan Umum (KU)', 'KK' => 'Keterampilan Khusus (KK)',
        ];

        return [
            'rps' => $rps, 'profil' => $profil,
            'koordinator' => $koordinator, 'koordinator_nip' => $koordinator_nip,
            'pengampu' => $pengampu, 'cpl' => $cpl, 'cpmk' => $cpmk,
            'subcpmk' => $subcpmk_rows, 
            'mingguan' => $mingguan_processed,
            'materi_pembelajaran' => $materi_pembelajaran,
            'referensi_utama' => $referensi_utama,
            'referensi_pendukung' => $referensi_pendukung,
            'mk_prasyarat' => $mk_prasyarat,
            'jenisCPL' => $jenisCPL,
        ];
    }
}
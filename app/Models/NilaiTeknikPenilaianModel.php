<?php

namespace App\Models;

use CodeIgniter\Model;

class NilaiTeknikPenilaianModel extends Model
{
    protected $table            = 'nilai_teknik_penilaian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields    = [
        'mahasiswa_id',
        'jadwal_id',
        'rps_mingguan_id',
        'teknik_penilaian_key',
        'nilai'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all teknik_penilaian items for a specific jadwal with their weights
     * Groups by rps_mingguan and extracts teknik from the JSON field
     *
     * @param int $jadwal_id
     * @return array Array of teknik_penilaian items with their details
     */
    public function getTeknikPenilaianByJadwal(int $jadwal_id): array
    {
        $db = \Config\Database::connect();

        // Get mata_kuliah_id from jadwal
        $jadwal = $db->table('jadwal')
            ->select('mata_kuliah_id')
            ->where('id', $jadwal_id)
            ->get()
            ->getRowArray();

        if (!$jadwal) {
            return [];
        }

        // Get RPS for this mata kuliah
        $rps = $db->table('rps')
            ->select('id')
            ->where('mata_kuliah_id', $jadwal['mata_kuliah_id'])
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getRowArray();

        if (!$rps) {
            return [];
        }

        // Get all rps_mingguan entries with their CPMK info and tahap_penilaian
        $rps_mingguan = $db->table('rps_mingguan')
            ->select('rps_mingguan.id, rps_mingguan.minggu, rps_mingguan.cpmk_id,
                      rps_mingguan.teknik_penilaian, rps_mingguan.tahap_penilaian,
                      cpmk.kode_cpmk, cpmk.deskripsi as cpmk_deskripsi')
            ->join('cpmk', 'cpmk.id = rps_mingguan.cpmk_id', 'left')
            ->where('rps_mingguan.rps_id', $rps['id'])
            ->orderBy('rps_mingguan.minggu', 'ASC')
            ->get()
            ->getResultArray();

        // Process and flatten the results
        $result = [];
        $teknik_labels = [
            'partisipasi'   => 'Partisipasi (Kehadiran / Quiz)',
            'observasi'     => 'Observasi (Praktek / Tugas)',
            'unjuk_kerja'   => 'Unjuk Kerja (Presentasi)',
            'proyek'        => 'Proyek (Case Method/Project Based)',
            'tes_tulis_uts' => 'Tes Tulis (UTS)',
            'tes_tulis_uas' => 'Tes Tulis (UAS)',
            'tes_lisan'     => 'Tes Lisan (Tugas Kelompok)'
        ];

        foreach ($rps_mingguan as $row) {
            $teknik_data = json_decode($row['teknik_penilaian'], true);
            $tahap_data = json_decode($row['tahap_penilaian'], true);

            // Get tahap_penilaian (use first one if multiple, or default)
            $tahap = 'Perkuliahan'; // Default
            if (is_array($tahap_data) && !empty($tahap_data)) {
                $tahap = $tahap_data[0]; // Use first tahap
            }

            if (is_array($teknik_data)) {
                foreach ($teknik_data as $key => $bobot) {
                    if ($bobot > 0) {
                        $result[] = [
                            'rps_mingguan_id' => $row['id'],
                            'minggu' => $row['minggu'],
                            'cpmk_id' => $row['cpmk_id'],
                            'kode_cpmk' => $row['kode_cpmk'],
                            'cpmk_deskripsi' => $row['cpmk_deskripsi'],
                            'tahap_penilaian' => $tahap,
                            'teknik_key' => $key,
                            'teknik_label' => $teknik_labels[$key] ?? ucfirst(str_replace('_', ' ', $key)),
                            'bobot' => (float) $bobot,
                        ];
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Get combined teknik_penilaian grouped by type (not by week)
     * This combines the same techniques across all weeks
     *
     * @param int $jadwal_id
     * @return array Array of combined teknik_penilaian with total weights
     */
    public function getCombinedTeknikPenilaianByJadwal(int $jadwal_id): array
    {
        // Get all individual teknik items first
        $all_teknik = $this->getTeknikPenilaianByJadwal($jadwal_id);

        // Group by tahap_penilaian and teknik_key
        $grouped = [];
        $teknik_details = []; // Store all rps_mingguan_ids for each combined teknik

        foreach ($all_teknik as $item) {
            $tahap = $item['tahap_penilaian'];
            $key = $item['teknik_key'];
            $combined_key = $tahap . '|' . $key;

            if (!isset($grouped[$combined_key])) {
                $grouped[$combined_key] = [
                    'tahap_penilaian' => $tahap,
                    'teknik_key' => $key,
                    'teknik_label' => $item['teknik_label'],
                    'total_bobot' => 0,
                    'rps_mingguan_ids' => [],
                    'cpmk_info' => [] // Track which CPMKs use this technique
                ];
            }

            $grouped[$combined_key]['total_bobot'] += $item['bobot'];
            $grouped[$combined_key]['rps_mingguan_ids'][] = [
                'rps_mingguan_id' => $item['rps_mingguan_id'],
                'minggu' => $item['minggu'],
                'cpmk_id' => $item['cpmk_id'],
                'kode_cpmk' => $item['kode_cpmk'],
                'bobot' => $item['bobot']
            ];

            // Track CPMK usage
            $cpmk_key = $item['kode_cpmk'];
            if (!in_array($cpmk_key, $grouped[$combined_key]['cpmk_info'])) {
                $grouped[$combined_key]['cpmk_info'][] = $cpmk_key;
            }
        }

        // Convert to indexed array and sort by tahap
        $result = array_values($grouped);

        // Sort by tahap order
        $tahap_order = ['Perkuliahan' => 1, 'Tengah Semester' => 2, 'Akhir Semester' => 3];
        usort($result, function($a, $b) use ($tahap_order) {
            $order_a = $tahap_order[$a['tahap_penilaian']] ?? 999;
            $order_b = $tahap_order[$b['tahap_penilaian']] ?? 999;
            if ($order_a === $order_b) {
                return strcmp($a['teknik_key'], $b['teknik_key']);
            }
            return $order_a - $order_b;
        });

        // Group by tahap for display
        $by_tahap = [];
        foreach ($result as $item) {
            $tahap = $item['tahap_penilaian'];
            if (!isset($by_tahap[$tahap])) {
                $by_tahap[$tahap] = [
                    'tahap_name' => $tahap,
                    'items' => []
                ];
            }
            $by_tahap[$tahap]['items'][] = $item;
        }

        return [
            'combined_list' => $result,
            'by_tahap' => $by_tahap
        ];
    }

    /**
     * Get existing scores for input form (combined view)
     * Returns scores grouped by teknik_key only (not by rps_mingguan_id)
     *
     * @param int $jadwal_id
     * @return array [mahasiswa_id][teknik_key] = nilai
     */
    public function getCombinedScoresForInput(int $jadwal_id): array
    {
        $results = $this->where('jadwal_id', $jadwal_id)->findAll();

        $scores = [];
        foreach ($results as $row) {
            // For combined view, we just pick the first value found for each teknik_key
            // (they should all be the same if entered through combined interface)
            $mhs_id = $row['mahasiswa_id'];
            $teknik_key = $row['teknik_penilaian_key'];

            if (!isset($scores[$mhs_id][$teknik_key])) {
                $scores[$mhs_id][$teknik_key] = $row['nilai'];
            }
        }

        return $scores;
    }

    /**
     * Get existing scores for input form
     *
     * @param int $jadwal_id
     * @return array [mahasiswa_id][rps_mingguan_id][teknik_key] = nilai
     */
    public function getScoresByJadwalForInput(int $jadwal_id): array
    {
        $results = $this->where('jadwal_id', $jadwal_id)->findAll();

        $scores = [];
        foreach ($results as $row) {
            $scores[$row['mahasiswa_id']][$row['rps_mingguan_id']][$row['teknik_penilaian_key']] = $row['nilai'];
        }

        return $scores;
    }

    /**
     * Save or update a score
     *
     * @param array $data
     * @return bool
     */
    public function saveOrUpdate(array $data): bool
    {
        $existing = $this->where([
            'mahasiswa_id' => $data['mahasiswa_id'],
            'jadwal_id' => $data['jadwal_id'],
            'rps_mingguan_id' => $data['rps_mingguan_id'],
            'teknik_penilaian_key' => $data['teknik_penilaian_key']
        ])->first();

        if ($existing) {
            return $this->update($existing['id'], ['nilai' => $data['nilai']]);
        } else {
            return $this->insert($data);
        }
    }

    /**
     * Calculate CPMK scores based on teknik_penilaian scores
     * Formula: CPMK Score = Σ(Bobot% × Nilai) = Σ(Bobot × Nilai) / 100
     * Returns array of [mahasiswa_id][cpmk_id] = calculated_score
     *
     * @param int $jadwal_id
     * @return array
     */
    public function calculateCpmkScores(int $jadwal_id): array
    {
        // Get all scores
        $scores = $this->select('nilai_teknik_penilaian.*, rps_mingguan.cpmk_id, rps_mingguan.teknik_penilaian')
            ->join('rps_mingguan', 'rps_mingguan.id = nilai_teknik_penilaian.rps_mingguan_id')
            ->where('nilai_teknik_penilaian.jadwal_id', $jadwal_id)
            ->findAll();

        // Group by mahasiswa and cpmk
        $cpmk_scores = [];

        foreach ($scores as $score) {
            $mahasiswa_id = $score['mahasiswa_id'];
            $cpmk_id = $score['cpmk_id'];

            if (!isset($cpmk_scores[$mahasiswa_id][$cpmk_id])) {
                $cpmk_scores[$mahasiswa_id][$cpmk_id] = 0;
            }

            // Get the weight for this teknik from rps_mingguan
            $teknik_data = json_decode($score['teknik_penilaian'], true);
            $bobot = $teknik_data[$score['teknik_penilaian_key']] ?? 0;

            // Calculate: Σ(Bobot% × Nilai)
            if ($score['nilai'] !== null && $bobot > 0) {
                $cpmk_scores[$mahasiswa_id][$cpmk_id] += ($score['nilai'] * $bobot / 100);
            }
        }

        // Round the final scores
        $result = [];
        foreach ($cpmk_scores as $mahasiswa_id => $cpmks) {
            foreach ($cpmks as $cpmk_id => $score) {
                $result[$mahasiswa_id][$cpmk_id] = round($score, 2);
            }
        }

        return $result;
    }
}

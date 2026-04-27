<?php

namespace App\Libraries;

class TeknikPenilaian
{
    // Full labels used in forms/checkboxes (RPS mingguan create/edit)
    public const VERBOSE_LABELS = [
        'partisipasi'   => 'Partisipasi (Kehadiran / Quiz)',
        'observasi'     => 'Observasi (Tugas)',
        'unjuk_kerja'   => 'Unjuk Kerja (Presentasi)',
        'proyek'        => 'Proyek (Case Method/Project Based)',
        'tes_tulis_uts' => 'Tes Tulis (UTS)',
        'tes_tulis_uas' => 'Tes Tulis (UAS)',
        'tes_lisan'     => 'Praktikum',
    ];

    // Short labels used in tables, reports, and exports
    public const LABELS = [
        'partisipasi'   => 'Partisipasi',
        'observasi'     => 'Observasi',
        'unjuk_kerja'   => 'Unjuk Kerja',
        'proyek'        => 'Proyek',
        'tes_tulis_uts' => 'UTS',
        'tes_tulis_uas' => 'UAS',
        'tes_lisan'     => 'Praktikum',
    ];

    // Activity type labels used in laporan (describes what students do)
    public const ACTIVITY_LABELS = [
        'partisipasi'   => 'Kehadiran/Quiz',
        'observasi'     => 'Tugas',
        'unjuk_kerja'   => 'Presentasi',
        'proyek'        => 'Case Method/Project Based',
        'tes_tulis_uts' => 'UTS',
        'tes_tulis_uas' => 'UAS',
        'tes_lisan'     => 'Praktikum',
    ];
}

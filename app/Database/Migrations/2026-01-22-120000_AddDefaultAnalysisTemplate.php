<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDefaultAnalysisTemplate extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        // Check if default template already exists
        $existing = $db->table('analysis_templates')
            ->where('option_key', 'default')
            ->countAllResults();

        if ($existing === 0) {
            // Insert default template
            $db->table('analysis_templates')->insert([
                'option_key' => 'default',
                'option_label' => 'Template Otomatis Utama (Default)',
                'template_tercapai' => 'Dari {total_cpmk} CPMK yang ditetapkan, seluruh CPMK telah tercapai dengan baik ({persentase_tercapai}%). Seluruh CPMK telah melampaui standar minimal capaian yang ditetapkan sebesar {standar_minimal}%. Mahasiswa menunjukkan pemahaman yang memadai terhadap seluruh materi pembelajaran. Meskipun seluruh CPMK tercapai, perlu dipertahankan dan ditingkatkan kualitas pembelajaran melalui inovasi metode pengajaran dan pengembangan materi yang lebih relevan dengan perkembangan terkini. Secara keseluruhan, capaian pembelajaran mata kuliah ini sangat baik.',
                'template_tidak_tercapai' => 'Dari {total_cpmk} CPMK yang ditetapkan, sebanyak {jumlah_tercapai} CPMK telah tercapai ({persentase_tercapai}%) dan {jumlah_tidak_tercapai} CPMK belum mencapai target minimal. CPMK yang belum mencapai standar minimal {standar_minimal}% adalah: {cpmk_tidak_tercapai_list}. Mahasiswa mengalami kesulitan dalam memahami dan menerapkan konsep-konsep tersebut. Diperlukan evaluasi lebih lanjut terhadap metode pengajaran dan materi pembelajaran untuk CPMK yang belum tercapai. Disarankan untuk meningkatkan intensitas latihan, menggunakan pendekatan pembelajaran yang lebih interaktif, dan memberikan umpan balik yang lebih konstruktif kepada mahasiswa.',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->table('analysis_templates')
            ->where('option_key', 'default')
            ->delete();
    }
}

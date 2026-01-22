<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnalysisTemplatesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'option_key' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'option_label' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'template_tercapai' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'template_tidak_tercapai' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('option_key');
        $this->forge->createTable('analysis_templates');

        // Insert default templates
        $this->insertDefaultTemplates();
    }

    public function down()
    {
        $this->forge->dropTable('analysis_templates');
    }

    private function insertDefaultTemplates()
    {
        $db = \Config\Database::connect();

        $templates = [
            [
                'option_key' => 'default',
                'option_label' => 'Template Otomatis Utama (Default)',
                'template_tercapai' => 'Dari {total_cpmk} CPMK yang ditetapkan, seluruh CPMK telah tercapai dengan baik ({persentase_tercapai}%). Seluruh CPMK telah melampaui standar minimal capaian yang ditetapkan sebesar {standar_minimal}%. Mahasiswa menunjukkan pemahaman yang memadai terhadap seluruh materi pembelajaran. Meskipun seluruh CPMK tercapai, perlu dipertahankan dan ditingkatkan kualitas pembelajaran melalui inovasi metode pengajaran dan pengembangan materi yang lebih relevan dengan perkembangan terkini. Secara keseluruhan, capaian pembelajaran mata kuliah ini sangat baik.',
                'template_tidak_tercapai' => 'Dari {total_cpmk} CPMK yang ditetapkan, sebanyak {jumlah_tercapai} CPMK telah tercapai ({persentase_tercapai}%) dan {jumlah_tidak_tercapai} CPMK belum mencapai target minimal. CPMK yang belum mencapai standar minimal {standar_minimal}% adalah: {cpmk_tidak_tercapai_list}. Mahasiswa mengalami kesulitan dalam memahami dan menerapkan konsep-konsep tersebut. Diperlukan evaluasi lebih lanjut terhadap metode pengajaran dan materi pembelajaran untuk CPMK yang belum tercapai. Disarankan untuk meningkatkan intensitas latihan, menggunakan pendekatan pembelajaran yang lebih interaktif, dan memberikan umpan balik yang lebih konstruktif kepada mahasiswa.',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'option_key' => 'capaian_keseluruhan',
                'option_label' => 'Ringkasan capaian keseluruhan CPMK',
                'template_tercapai' => 'Dari {total_cpmk} CPMK yang ditetapkan, seluruh CPMK telah tercapai dengan baik ({persentase_tercapai}%). Mahasiswa menunjukkan pemahaman yang memadai terhadap seluruh materi pembelajaran.',
                'template_tidak_tercapai' => 'Dari {total_cpmk} CPMK yang ditetapkan, sebanyak {jumlah_tercapai} CPMK telah tercapai ({persentase_tercapai}%) dan {jumlah_tidak_tercapai} CPMK belum mencapai target minimal.',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'option_key' => 'perbandingan_target',
                'option_label' => 'Perbandingan dengan target minimal',
                'template_tercapai' => 'Seluruh CPMK telah melampaui standar minimal capaian yang ditetapkan sebesar {standar_minimal}%.',
                'template_tidak_tercapai' => 'CPMK yang belum mencapai standar minimal {standar_minimal}% adalah: {cpmk_tidak_tercapai_list}.',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'option_key' => 'analisis_cpmk_rendah',
                'option_label' => 'Analisis CPMK dengan capaian rendah',
                'template_tercapai' => '',
                'template_tidak_tercapai' => 'Terdapat {jumlah_tidak_tercapai} CPMK yang belum tercapai ({cpmk_tidak_tercapai_list}). Mahasiswa mengalami kesulitan dalam memahami dan menerapkan konsep-konsep tersebut. Hal ini dapat disebabkan oleh kompleksitas materi, metode pengajaran yang kurang efektif, atau kurangnya latihan praktis.',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'option_key' => 'rekomendasi_perbaikan',
                'option_label' => 'Rekomendasi perbaikan pembelajaran',
                'template_tercapai' => 'Meskipun seluruh CPMK tercapai, perlu dipertahankan dan ditingkatkan kualitas pembelajaran melalui inovasi metode pengajaran dan pengembangan materi yang lebih relevan dengan perkembangan terkini.',
                'template_tidak_tercapai' => 'Diperlukan evaluasi lebih lanjut terhadap metode pengajaran dan materi pembelajaran untuk CPMK yang belum tercapai. Disarankan untuk meningkatkan intensitas latihan, menggunakan pendekatan pembelajaran yang lebih interaktif, dan memberikan umpan balik yang lebih konstruktif kepada mahasiswa.',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'option_key' => 'kesimpulan_umum',
                'option_label' => 'Kesimpulan umum dan tindak lanjut',
                'template_tercapai' => 'Secara keseluruhan, capaian pembelajaran mata kuliah ini sangat baik. Tindak lanjut yang diperlukan adalah mempertahankan kualitas pembelajaran dan terus melakukan perbaikan berkelanjutan (continuous improvement).',
                'template_tidak_tercapai' => 'Tindak lanjut yang perlu dilakukan meliputi: revisi metode pengajaran, pengembangan bahan ajar yang lebih komprehensif, dan peningkatan asesmen formatif untuk memantau perkembangan pemahaman mahasiswa secara berkala.',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $db->table('analysis_templates')->insertBatch($templates);
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateAnalysisTemplatesCplLabels extends Migration
{
    public function up()
    {
        // Update existing template labels
        $this->db->table('analysis_templates_cpl')
            ->where('option_key', 'default')
            ->update(['option_label' => 'Template 1']);

        $this->db->table('analysis_templates_cpl')
            ->where('option_key', 'formal')
            ->update(['option_label' => 'Template 2']);

        $this->db->table('analysis_templates_cpl')
            ->where('option_key', 'singkat')
            ->update(['option_label' => 'Template 3']);

        // Insert new templates (only if they don't exist)
        $newTemplates = [
            [
                'option_key' => 'template_4',
                'option_label' => 'Template 4',
                'template_tercapai' => 'Analisis capaian pembelajaran menunjukkan bahwa semua CPL telah tercapai dengan persentase keberhasilan 100%. Hasil ini mencerminkan efektivitas strategi pembelajaran yang diterapkan.',
                'template_tidak_tercapai' => 'Hasil analisis menunjukkan bahwa dari {total_cpl} CPL, sebanyak {jumlah_tercapai} CPL telah tercapai ({persentase_tercapai}%), namun {jumlah_tidak_tercapai} CPL masih perlu perbaikan: {cpl_tidak_tercapai_list}. Rekomendasi tindak lanjut perlu segera dilakukan.',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'option_key' => 'template_5',
                'option_label' => 'Template 5',
                'template_tercapai' => 'Evaluasi terhadap {total_cpl} CPL menunjukkan pencapaian yang sangat baik dengan seluruh indikator terpenuhi. Mahasiswa telah mendemonstrasikan kompetensi sesuai dengan standar yang ditetapkan sebesar {standar_minimal}%.',
                'template_tidak_tercapai' => 'Pencapaian CPL periode ini menunjukkan {persentase_tercapai}% CPL telah memenuhi standar ({cpl_tercapai_list}). Adapun CPL yang masih di bawah standar minimal {standar_minimal}% adalah: {cpl_tidak_tercapai_list}. Diperlukan intervensi pada mata kuliah pendukung CPL tersebut.',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'option_key' => 'template_6',
                'option_label' => 'Template 6',
                'template_tercapai' => 'Capaian pembelajaran lulusan untuk periode ini sangat memuaskan dengan tingkat pencapaian 100% untuk semua CPL.',
                'template_tidak_tercapai' => 'CPL tercapai: {cpl_tercapai_list} ({persentase_tercapai}%). CPL perlu perbaikan: {cpl_tidak_tercapai_list}. Evaluasi dan perbaikan pembelajaran diperlukan untuk CPL yang belum tercapai.',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        foreach ($newTemplates as $template) {
            // Check if template already exists
            $existing = $this->db->table('analysis_templates_cpl')
                ->where('option_key', $template['option_key'])
                ->get()
                ->getRow();

            // Insert only if it doesn't exist
            if (!$existing) {
                $this->db->table('analysis_templates_cpl')->insert($template);
            }
        }
    }

    public function down()
    {
        // Revert label changes
        $this->db->table('analysis_templates_cpl')
            ->where('option_key', 'default')
            ->update(['option_label' => 'Template Default']);

        $this->db->table('analysis_templates_cpl')
            ->where('option_key', 'formal')
            ->update(['option_label' => 'Template Formal']);

        $this->db->table('analysis_templates_cpl')
            ->where('option_key', 'singkat')
            ->update(['option_label' => 'Template Singkat']);

        // Delete the new templates
        $this->db->table('analysis_templates_cpl')
            ->whereIn('option_key', ['template_4', 'template_5', 'template_6'])
            ->delete();
    }
}

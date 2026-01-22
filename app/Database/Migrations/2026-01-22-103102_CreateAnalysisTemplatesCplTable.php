<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnalysisTemplatesCplTable extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'id' => [
				'type' => 'INT',
				'constraint' => 11,
				'unsigned' => true,
				'auto_increment' => true,
			],
			'option_key' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
				'unique' => true,
			],
			'option_label' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
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
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 1,
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
		$this->forge->createTable('analysis_templates_cpl');

		// Insert default templates
		$data = [
			[
				'option_key' => 'default',
				'option_label' => 'Template 1',
				'template_tercapai' => 'Semua CPL tercapai dengan baik. Dari total {total_cpl} CPL yang diukur, seluruhnya ({jumlah_tercapai} CPL) telah mencapai standar minimal {standar_minimal}%. Mahasiswa menunjukkan kompetensi yang memadai sesuai dengan profil lulusan yang diharapkan.',
				'template_tidak_tercapai' => 'Dari {total_cpl} CPL yang diukur, terdapat {jumlah_tidak_tercapai} CPL yang belum mencapai standar minimal {standar_minimal}%, yaitu: {cpl_tidak_tercapai_list}. Sementara itu, {jumlah_tercapai} CPL lainnya ({cpl_tercapai_list}) telah tercapai dengan baik. Diperlukan evaluasi lebih lanjut terhadap mata kuliah kontributor dan metode pembelajaran untuk meningkatkan capaian CPL yang belum tercapai.',
				'is_active' => 1,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			],
			[
				'option_key' => 'template_2',
				'option_label' => 'Template 2',
				'template_tercapai' => 'Berdasarkan hasil evaluasi capaian pembelajaran lulusan untuk periode ini, dapat dilaporkan bahwa seluruh CPL (total {total_cpl} CPL) telah mencapai standar minimal yang ditetapkan yaitu {standar_minimal}%. Pencapaian ini menunjukkan bahwa proses pembelajaran telah berjalan efektif dan mahasiswa telah menguasai kompetensi yang diharapkan sesuai dengan standar KKNI.',
				'template_tidak_tercapai' => 'Berdasarkan hasil evaluasi capaian pembelajaran lulusan, dari {total_cpl} CPL yang diukur, sebanyak {jumlah_tercapai} CPL ({persentase_tercapai}%) telah mencapai standar minimal {standar_minimal}%, yaitu: {cpl_tercapai_list}. Namun demikian, masih terdapat {jumlah_tidak_tercapai} CPL yang belum mencapai standar, yaitu: {cpl_tidak_tercapai_list}. Untuk CPL yang belum tercapai, diperlukan tindakan perbaikan berkelanjutan (Continuous Quality Improvement) yang mencakup evaluasi mata kuliah kontributor, perbaikan metode pembelajaran, serta penyesuaian strategi asesmen.',
				'is_active' => 1,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			],
			[
				'option_key' => 'template_3',
				'option_label' => 'Template 3',
				'template_tercapai' => 'Seluruh CPL tercapai (100%).',
				'template_tidak_tercapai' => '{jumlah_tercapai} dari {total_cpl} CPL tercapai ({persentase_tercapai}%). CPL yang belum tercapai: {cpl_tidak_tercapai_list}.',
				'is_active' => 1,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			],
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

		$this->db->table('analysis_templates_cpl')->insertBatch($data);
	}

	public function down()
	{
		$this->forge->dropTable('analysis_templates_cpl');
	}
}

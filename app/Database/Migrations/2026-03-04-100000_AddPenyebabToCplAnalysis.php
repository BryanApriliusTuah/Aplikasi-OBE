<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPenyebabToCplAnalysis extends Migration
{
	public function up()
	{
		// Add penyebab columns to analisis_cpl
		$this->forge->addColumn('analisis_cpl', [
			'penyebab_mode' => [
				'type'       => 'ENUM',
				'constraint' => ['auto', 'manual'],
				'default'    => 'auto',
				'null'       => true,
				'after'      => 'notulensi_rapat_file',
			],
			'penyebab_auto_option' => [
				'type'       => 'VARCHAR',
				'constraint' => 50,
				'null'       => true,
				'after'      => 'penyebab_mode',
			],
			'penyebab_text' => [
				'type' => 'TEXT',
				'null' => true,
				'after' => 'penyebab_auto_option',
			],
		]);

		// Create penyebab_templates_cpl table
		$this->forge->addField([
			'id' => [
				'type'           => 'INT',
				'constraint'     => 10,
				'unsigned'       => true,
				'auto_increment' => true,
			],
			'option_key' => [
				'type'       => 'VARCHAR',
				'constraint' => 50,
				'null'       => false,
			],
			'option_label' => [
				'type'       => 'VARCHAR',
				'constraint' => 255,
				'null'       => false,
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
		$this->forge->createTable('penyebab_templates_cpl');
	}

	public function down()
	{
		$this->forge->dropColumn('analisis_cpl', ['penyebab_mode', 'penyebab_auto_option', 'penyebab_text']);
		$this->forge->dropTable('penyebab_templates_cpl', true);
	}
}

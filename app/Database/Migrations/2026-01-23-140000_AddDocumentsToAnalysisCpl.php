<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDocumentsToAnalysisCpl extends Migration
{
	public function up()
	{
		$fields = [
			'bukti_dokumentasi_file' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
				'comment' => 'Path to uploaded bukti dokumentasi asesmen file'
			],
			'notulensi_rapat_file' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
				'comment' => 'Path to uploaded notulensi rapat evaluasi CPL file'
			]
		];

		$this->forge->addColumn('analisis_cpl', $fields);
	}

	public function down()
	{
		$this->forge->dropColumn('analisis_cpl', ['bukti_dokumentasi_file', 'notulensi_rapat_file']);
	}
}

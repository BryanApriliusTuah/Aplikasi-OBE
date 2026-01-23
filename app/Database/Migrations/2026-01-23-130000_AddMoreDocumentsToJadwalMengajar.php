<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMoreDocumentsToJadwalMengajar extends Migration
{
	public function up()
	{
		$fields = [
			'contoh_soal_file' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
				'comment' => 'Path to uploaded contoh soal dan jawaban file'
			],
			'notulen_rapat_file' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
				'comment' => 'Path to uploaded notulen rapat evaluasi file'
			]
		];

		$this->forge->addColumn('jadwal_mengajar', $fields);
	}

	public function down()
	{
		$this->forge->dropColumn('jadwal_mengajar', ['contoh_soal_file', 'notulen_rapat_file']);
	}
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRubrikPenilaianToJadwalMengajar extends Migration
{
	public function up()
	{
		$fields = [
			'rubrik_penilaian_file' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
				'null' => true,
				'comment' => 'Path to uploaded rubrik penilaian file'
			]
		];

		$this->forge->addColumn('jadwal_mengajar', $fields);
	}

	public function down()
	{
		$this->forge->dropColumn('jadwal_mengajar', ['rubrik_penilaian_file']);
	}
}

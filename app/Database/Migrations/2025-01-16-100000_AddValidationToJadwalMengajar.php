<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddValidationToJadwalMengajar extends Migration
{
	public function up()
	{
		$fields = [
			'is_nilai_validated' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 0,
				'comment' => '0 = Not validated, 1 = Validated by admin'
			],
			'validated_at' => [
				'type' => 'DATETIME',
				'null' => true,
				'comment' => 'Timestamp when nilai was validated'
			],
			'validated_by' => [
				'type' => 'INT',
				'constraint' => 11,
				'null' => true,
				'comment' => 'User ID who validated the nilai'
			]
		];

		$this->forge->addColumn('jadwal_mengajar', $fields);
	}

	public function down()
	{
		$this->forge->dropColumn('jadwal_mengajar', ['is_nilai_validated', 'validated_at', 'validated_by']);
	}
}

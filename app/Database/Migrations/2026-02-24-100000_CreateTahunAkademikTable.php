<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTahunAkademikTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'tahun' => [
                'type'       => 'VARCHAR',
                'constraint' => '9',
                'comment'    => 'Format YYYY/YYYY, e.g. 2024/2025',
            ],
            'semester' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'comment'    => 'Ganjil or Genap',
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
        $this->forge->addUniqueKey(['tahun', 'semester']);

        $this->forge->createTable('tahun_akademik');
    }

    public function down()
    {
        $this->forge->dropTable('tahun_akademik');
    }
}

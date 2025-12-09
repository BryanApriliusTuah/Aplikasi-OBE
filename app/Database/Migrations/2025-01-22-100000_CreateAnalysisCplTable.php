<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnalysisCplTable extends Migration
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
            'program_studi' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'tahun_akademik' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'angkatan' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
            ],
            'mode' => [
                'type'       => 'ENUM',
                'constraint' => ['auto', 'manual'],
                'default'    => 'auto',
            ],
            'analisis_summary' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->createTable('analisis_cpl');
    }

    public function down()
    {
        $this->forge->dropTable('analisis_cpl');
    }
}

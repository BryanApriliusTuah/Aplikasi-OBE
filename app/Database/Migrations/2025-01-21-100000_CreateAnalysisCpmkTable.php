<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnalysisCpmkTable extends Migration
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
            'mata_kuliah_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'tahun_akademik' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'program_studi' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'mode' => [
                'type'       => 'ENUM',
                'constraint' => ['auto', 'manual'],
                'default'    => 'auto',
            ],
            'analisis_singkat' => [
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
        $this->forge->addForeignKey('mata_kuliah_id', 'mata_kuliah', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('analisis_cpmk');
    }

    public function down()
    {
        $this->forge->dropTable('analisis_cpmk');
    }
}

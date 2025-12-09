<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCqiTable extends Migration
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
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['cpl', 'cpmk'],
                'default'    => 'cpl',
            ],
            'program_studi' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'tahun_akademik' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => true,
            ],
            'angkatan' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'null'       => true,
            ],
            'jadwal_mengajar_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'kode_cpl' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'kode_cpmk' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'masalah' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'rencana_perbaikan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'penanggung_jawab' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'jadwal_pelaksanaan' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
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
        $this->forge->addKey('type');
        $this->forge->addKey(['program_studi', 'tahun_akademik', 'angkatan']);
        $this->forge->addKey('jadwal_mengajar_id');
        $this->forge->createTable('cqi');
    }

    public function down()
    {
        $this->forge->dropTable('cqi');
    }
}

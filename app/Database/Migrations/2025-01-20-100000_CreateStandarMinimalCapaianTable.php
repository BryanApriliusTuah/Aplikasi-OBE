<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStandarMinimalCapaianTable extends Migration
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
            'persentase' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'default'    => '75.00',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('standar_minimal_capaian');

        // Insert default value
        $this->db->table('standar_minimal_capaian')->insert([
            'persentase' => 75.00
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('standar_minimal_capaian');
    }
}

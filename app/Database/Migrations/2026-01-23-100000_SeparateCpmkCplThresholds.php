<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SeparateCpmkCplThresholds extends Migration
{
    public function up()
    {
        // Get existing threshold value before dropping table
        $builder = $this->db->table('standar_minimal_capaian');
        $existingData = $builder->get()->getRow();
        $existingPersentase = $existingData ? $existingData->persentase : 75.00;

        // Create standar_minimal_cpmk table
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
        $this->forge->createTable('standar_minimal_cpmk');

        // Create standar_minimal_cpl table
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
        $this->forge->createTable('standar_minimal_cpl');

        // Insert existing data into both new tables
        $this->db->table('standar_minimal_cpmk')->insert([
            'persentase'  => $existingPersentase,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        $this->db->table('standar_minimal_cpl')->insert([
            'persentase'  => $existingPersentase,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);

        // Drop old table
        $this->forge->dropTable('standar_minimal_capaian', true);
    }

    public function down()
    {
        // Get existing threshold values from new tables
        $builderCpmk = $this->db->table('standar_minimal_cpmk');
        $cpmkData = $builderCpmk->get()->getRow();
        $cpmkPersentase = $cpmkData ? $cpmkData->persentase : 75.00;

        // Recreate old table
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

        // Insert CPMK threshold value (prioritize CPMK when rolling back)
        $this->db->table('standar_minimal_capaian')->insert([
            'persentase' => $cpmkPersentase,
        ]);

        // Drop new tables
        $this->forge->dropTable('standar_minimal_cpmk', true);
        $this->forge->dropTable('standar_minimal_cpl', true);
    }
}

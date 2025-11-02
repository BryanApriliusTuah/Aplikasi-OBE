<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNilaiTeknikPenilaianTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'mahasiswa_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'jadwal_mengajar_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'rps_mingguan_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'comment'    => 'Reference to rps_mingguan where teknik_penilaian is defined'
            ],
            'teknik_penilaian_key' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'comment'    => 'e.g., partisipasi, observasi, tes_tulis_uts, tes_tulis_uas, etc.'
            ],
            'nilai' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
                'comment'    => 'Score for this teknik_penilaian (0-100)'
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

        // Add foreign keys
        $this->forge->addForeignKey('mahasiswa_id', 'mahasiswa', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('jadwal_mengajar_id', 'jadwal_mengajar', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('rps_mingguan_id', 'rps_mingguan', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('nilai_teknik_penilaian');

        // Add composite index with custom shorter name
        $this->db->query('ALTER TABLE `nilai_teknik_penilaian` ADD INDEX `idx_nilai_teknik` (`mahasiswa_id`, `jadwal_mengajar_id`, `rps_mingguan_id`, `teknik_penilaian_key`)');
    }

    public function down()
    {
        $this->forge->dropTable('nilai_teknik_penilaian');
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMbkmJadwalTable extends Migration
{
    public function up()
    {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS `mbkm_jadwal` (
                `mbkm_id`   INT NOT NULL,
                `jadwal_id` INT NOT NULL,
                PRIMARY KEY (`mbkm_id`, `jadwal_id`),
                KEY `idx_mbkm_jadwal_jadwal` (`jadwal_id`),
                CONSTRAINT `fk_mbkm_jadwal_mbkm`  FOREIGN KEY (`mbkm_id`)   REFERENCES `mbkm`   (`id`) ON DELETE CASCADE,
                CONSTRAINT `fk_mbkm_jadwal_jadwal` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwal` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci
        ");
    }

    public function down()
    {
        $this->db->query('DROP TABLE IF EXISTS `mbkm_jadwal`');
    }
}

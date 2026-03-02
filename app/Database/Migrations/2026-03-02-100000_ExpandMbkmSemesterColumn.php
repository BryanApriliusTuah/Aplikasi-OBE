<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ExpandMbkmSemesterColumn extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE `mbkm` MODIFY `semester` VARCHAR(20) NULL DEFAULT NULL");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `mbkm` MODIFY `semester` VARCHAR(10) NULL DEFAULT NULL");
    }
}

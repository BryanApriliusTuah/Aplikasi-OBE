<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAutoOptionsToAnalysisCpmk extends Migration
{
    public function up()
    {
        $this->forge->addColumn('analisis_cpmk', [
            'auto_options' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'analisis_singkat',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('analisis_cpmk', 'auto_options');
    }
}

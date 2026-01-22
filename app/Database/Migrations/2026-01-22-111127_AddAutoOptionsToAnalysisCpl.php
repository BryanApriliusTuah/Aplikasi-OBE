<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAutoOptionsToAnalysisCpl extends Migration
{
    public function up()
    {
        // Add auto_options column to analisis_cpl table
        $fields = [
            'auto_options' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'mode',
            ],
        ];

        $this->forge->addColumn('analisis_cpl', $fields);
    }

    public function down()
    {
        // Remove auto_options column
        $this->forge->dropColumn('analisis_cpl', 'auto_options');
    }
}

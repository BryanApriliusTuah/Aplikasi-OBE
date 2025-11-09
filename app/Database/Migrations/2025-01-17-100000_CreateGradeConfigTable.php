<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGradeConfigTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'grade_letter' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'comment'    => 'Grade letter (A, AB, B, BC, C, D, E)',
            ],
            'min_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'comment'    => 'Minimum score for this grade (0-100)',
            ],
            'max_score' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'comment'    => 'Maximum score for this grade (0-100)',
            ],
            'grade_point' => [
                'type'       => 'DECIMAL',
                'constraint' => '3,2',
                'null'       => true,
                'comment'    => 'Grade point value (e.g., 4.0 for A)',
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'comment'    => 'Grade description (e.g., Istimewa, Baik Sekali)',
            ],
            'is_passing' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1 = passing grade, 0 = failing grade',
            ],
            'order_number' => [
                'type'       => 'INT',
                'constraint' => 11,
                'comment'    => 'Order for sorting grades',
            ],
            'is_active' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'comment'    => '1 = active, 0 = inactive',
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
        $this->forge->addKey('grade_letter');
        $this->forge->addKey('order_number');

        $this->forge->createTable('grade_config');

        // Insert default grade configuration based on current system
        $data = [
            [
                'grade_letter' => 'A',
                'min_score' => 80.01,
                'max_score' => 100.00,
                'grade_point' => 4.00,
                'description' => 'Istimewa',
                'is_passing' => 1,
                'order_number' => 1,
                'is_active' => 1,
            ],
            [
                'grade_letter' => 'AB',
                'min_score' => 70.01,
                'max_score' => 80.00,
                'grade_point' => 3.50,
                'description' => 'Baik Sekali',
                'is_passing' => 1,
                'order_number' => 2,
                'is_active' => 1,
            ],
            [
                'grade_letter' => 'B',
                'min_score' => 65.01,
                'max_score' => 70.00,
                'grade_point' => 3.00,
                'description' => 'Baik',
                'is_passing' => 1,
                'order_number' => 3,
                'is_active' => 1,
            ],
            [
                'grade_letter' => 'BC',
                'min_score' => 60.01,
                'max_score' => 65.00,
                'grade_point' => 2.50,
                'description' => 'Cukup Baik',
                'is_passing' => 1,
                'order_number' => 4,
                'is_active' => 1,
            ],
            [
                'grade_letter' => 'C',
                'min_score' => 50.01,
                'max_score' => 60.00,
                'grade_point' => 2.00,
                'description' => 'Cukup',
                'is_passing' => 1,
                'order_number' => 5,
                'is_active' => 1,
            ],
            [
                'grade_letter' => 'D',
                'min_score' => 40.01,
                'max_score' => 50.00,
                'grade_point' => 1.00,
                'description' => 'Kurang',
                'is_passing' => 0,
                'order_number' => 6,
                'is_active' => 1,
            ],
            [
                'grade_letter' => 'E',
                'min_score' => 0.00,
                'max_score' => 40.00,
                'grade_point' => 0.00,
                'description' => 'Sangat Kurang',
                'is_passing' => 0,
                'order_number' => 7,
                'is_active' => 1,
            ],
        ];

        $builder = $this->db->table('grade_config');
        $builder->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('grade_config');
    }
}

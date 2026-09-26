<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSalaryIncrementHistoryTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                  => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'employee_id'         => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'increment_amount'    => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'previous_salary'     => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'new_salary'          => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'effective_from_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'updated_by'          => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'created_at'          => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        
        $this->forge->addKey('id', true);
        $this->forge->createTable('salary_increment_history');
    }

    public function down()
    {
        $this->forge->dropTable('salary_increment_history');
    }
}

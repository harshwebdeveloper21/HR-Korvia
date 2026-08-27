<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExprienceTable extends Migration
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
            'employee_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'from_date' => [
                'type' => 'DATE',
            ],
            'to_date' => [
                'type' => 'DATE',
            ],
            'template_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'generated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('exprience', true);
    }

    public function down()
    {
        $this->forge->dropTable('exprience', true);
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLeaveTypeTable extends Migration
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
            'leave_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'number_of_leaves' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
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
        $this->forge->createTable('leave_type', true);
    }

    public function down()
    {
        $this->forge->dropTable('leave_type', true);
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubtasksTable extends Migration
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
            'task_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'subtask_title' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'subtask_status' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'pending',
            ],
            'subtask_assigned_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'subtask_due_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'files' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
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
        $this->forge->createTable('subtasks', true);
    }

    public function down()
    {
        $this->forge->dropTable('subtasks', true);
    }
}

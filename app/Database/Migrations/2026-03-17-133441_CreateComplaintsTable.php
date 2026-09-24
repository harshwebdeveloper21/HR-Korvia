<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateComplaintsTable extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'mobile' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'type' => [
                'type'       => 'ENUM',
                'constraint' => ['Complaint', 'Feedback'],
            ],
            'subject' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'message' => [
                'type' => 'TEXT',
            ],
            'file' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['Pending', 'In Progress', 'Resolved'],
                'default'    => 'Pending',
            ],
            'admin_remark' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->createTable('complaints', true);
    }

    public function down()
    {
        $this->forge->dropTable('complaints', true);
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStaffTransfersTable extends Migration
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
            ],
            'from_branch_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'to_branch_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'transferred_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'effective_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('from_branch_id');
        $this->forge->addKey('to_branch_id');
        $this->forge->createTable('staff_transfers', true);
    }

    public function down()
    {
        $this->forge->dropTable('staff_transfers', true);
    }
}

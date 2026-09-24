<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBranchColumnsToUsers extends Migration
{
    public function up()
    {
        // Add branch_id nullable FK
        $this->forge->addColumn('users', [
            'branch_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'role',
            ],
        ]);

        // Add can_transfer_staff permission flag for HR
        $this->forge->addColumn('users', [
            'can_transfer_staff' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'after'      => 'branch_id',
            ],
        ]);

        // Add index for branch_id lookups
        $this->db->query('ALTER TABLE users ADD INDEX idx_branch_id (branch_id)');
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'branch_id');
        $this->forge->dropColumn('users', 'can_transfer_staff');
    }
}

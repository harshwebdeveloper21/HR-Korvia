<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSickLeavesToPayroll extends Migration
{
    public function up() { if ($this->db->fieldExists('used_sick_leaves', 'payroll')) { return; } 
        $this->forge->addColumn('payroll', [
            'used_sick_leaves' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'after' => 'used_paid_leaves'
            ],
            'remaining_sick_leaves' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
                'after' => 'used_sick_leaves'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('payroll', ['used_sick_leaves', 'remaining_sick_leaves']);
    }
}

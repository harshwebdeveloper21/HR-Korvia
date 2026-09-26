<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSaturdayWorkingHoursToCompanyRules extends Migration
{
    public function up()
    {
        $this->forge->addColumn('company_rules', [
            'saturday_working_hours' => [
                'type' => 'FLOAT',
                'default' => 4,
                'null' => true
            ],
            'saturday_full_day_override' => [
                'type' => 'BOOLEAN',
                'default' => 0,
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('company_rules', ['saturday_working_hours', 'saturday_full_day_override']);
    }
}

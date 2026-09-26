<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEnableGeofencingToCompanyRules extends Migration
{
    public function up()
    {
        $this->forge->addColumn('company_rules', [
            'enable_geofencing' => [
                'type' => 'BOOLEAN',
                'default' => 0,
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('company_rules', 'enable_geofencing');
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobLocationAddressesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'address_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'locations_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'address' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'city_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'state' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'country_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'postal_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
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
        $this->forge->addKey('address_id', true);
        $this->forge->createTable('job_location_addresses', true);
    }

    public function down()
    {
        $this->forge->dropTable('job_location_addresses', true);
    }
}

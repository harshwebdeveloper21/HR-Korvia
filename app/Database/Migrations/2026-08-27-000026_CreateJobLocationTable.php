<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobLocationTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'location_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'job_location' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
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
        $this->forge->addKey('location_id', true);
        $this->forge->createTable('job_location', true);
    }

    public function down()
    {
        $this->forge->dropTable('job_location', true);
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOnboardingTable extends Migration
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
            'candidate_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'department_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'job_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'start_date' => [
                'type' => 'DATE',
            ],
            'onboarding_status' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'docu_submitted' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'offer_later_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
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
        $this->forge->createTable('onboarding', true);
    }

    public function down()
    {
        $this->forge->dropTable('onboarding', true);
    }
}

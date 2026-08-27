<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCandidateTable extends Migration
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
            'candidate_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
            ],
            'job_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'job_date' => [
                'type' => 'DATE',
            ],
            'phone_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'default'    => 'applied',
            ],
            'resume' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'notes' => [
                'type' => 'TEXT',
                'null' => true,
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
        $this->forge->createTable('candidate', true);
    }

    public function down()
    {
        $this->forge->dropTable('candidate', true);
    }
}

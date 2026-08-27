<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePerformanceTable extends Migration
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
            'review_date' => [
                'type' => 'DATE',
            ],
            'reviewer_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'designation_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
            ],
            'goals_achieved' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
            ],
            'team_work' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
            ],
            'management' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
            ],
            'presentation_skill' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
            ],
            'behaviour' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
            ],
            'rating' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'notes' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
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
        $this->forge->createTable('performance', true);
    }

    public function down()
    {
        $this->forge->dropTable('performance', true);
    }
}

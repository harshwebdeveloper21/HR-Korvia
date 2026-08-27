<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJobsTable extends Migration
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
            'job_title' => [
                'type'       => 'VARCHAR',
                'constraint' => '200',
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'department_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['open', 'close'],
                'default'    => 'open',
            ],
            'locations_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'addresses_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'age' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'gender' => [
                'type'       => 'ENUM',
                'constraint' => ['male', 'female', 'other'],
            ],
            'job_type' => [
                'type'       => 'ENUM',
                'constraint' => ['full', 'part'],
            ],
            'experience' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'salary_range' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
            ],
            'post_date' => [
                'type' => 'DATE',
            ],
            'close_date' => [
                'type' => 'DATE',
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
        $this->forge->createTable('jobs', true);
    }

    public function down()
    {
        $this->forge->dropTable('jobs', true);
    }
}

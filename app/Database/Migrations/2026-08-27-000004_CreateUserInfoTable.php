<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUserInfoTable extends Migration
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
            'firstname' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'lastname' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'gender' => [
                'type'       => 'ENUM',
                'constraint' => ['male', 'female', 'other'],
                'null'       => true,
            ],
            'date_of_birth' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'address_1' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'address_2' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'state' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'state_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'postcode' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'city_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'country_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'contact_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'employee_id' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'designation_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'department_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'joining_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'working_location' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'role' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'salary' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'default'    => 0.00,
            ],
            'status' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'resume' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'job_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'profile_image' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'face_photo' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
            ],
            'last_increment_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'last_increment_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
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
        $this->forge->createTable('user_info', true);
    }

    public function down()
    {
        $this->forge->dropTable('user_info', true);
    }
}

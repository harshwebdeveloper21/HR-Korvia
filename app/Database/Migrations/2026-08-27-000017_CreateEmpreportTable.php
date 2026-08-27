<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmpreportTable extends Migration
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
            'department_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'joining_date' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'designation_id' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('empreport', true);
    }

    public function down()
    {
        $this->forge->dropTable('empreport', true);
    }
}

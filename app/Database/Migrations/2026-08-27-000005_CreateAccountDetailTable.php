<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAccountDetailTable extends Migration
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
            'acc_number' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'bank_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'ifsc_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'acc_in_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'branch_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'branch_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
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
        $this->forge->createTable('account_detail', true);
    }

    public function down()
    {
        $this->forge->dropTable('account_detail', true);
    }
}

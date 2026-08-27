<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSmtpSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'smtp_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'smtp_protocol' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'smtp',
            ],
            'smtp_host' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'smtp_port' => [
                'type'       => 'INT',
                'constraint' => 11,
            ],
            'smtp_username' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'smtp_password' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'smtp_encryption' => [
                'type'       => 'ENUM',
                'constraint' => ['ssl', 'tls', 'none'],
                'default'    => 'tls',
            ],
            'smtp_from_email' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'smtp_from_name' => [
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
        $this->forge->addKey('smtp_id', true);
        $this->forge->createTable('smtp_settings', true);
    }

    public function down()
    {
        $this->forge->dropTable('smtp_settings', true);
    }
}

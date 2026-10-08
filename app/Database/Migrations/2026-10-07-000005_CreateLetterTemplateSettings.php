<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLetterTemplateSettings extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('letter_template_settings')) {
            return;
        }

        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'template_key' => ['type' => 'VARCHAR', 'constraint' => 50],
            'content'      => ['type' => 'LONGTEXT', 'null' => true],
            'updated_by'   => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('template_key');
        $this->forge->createTable('letter_template_settings');
    }

    public function down()
    {
        $this->forge->dropTable('letter_template_settings', true);
    }
}

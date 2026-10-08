<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmployeeJoiningForms extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('employee_joining_forms')) {
            return;
        }

        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'form_data'  => ['type' => 'LONGTEXT', 'null' => true],
            'checklist'  => ['type' => 'TEXT', 'null' => true],
            'updated_by' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('user_id');
        $this->forge->createTable('employee_joining_forms');
    }

    public function down()
    {
        $this->forge->dropTable('employee_joining_forms', true);
    }
}

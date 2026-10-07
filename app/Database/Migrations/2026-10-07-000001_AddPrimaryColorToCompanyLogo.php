<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPrimaryColorToCompanyLogo extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('company_logo')) {
            return;
        }

        if (!in_array('primary_color', $this->db->getFieldNames('company_logo'), true)) {
            $this->forge->addColumn('company_logo', [
                'primary_color' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 7,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'company_email',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('company_logo') && in_array('primary_color', $this->db->getFieldNames('company_logo'), true)) {
            $this->forge->dropColumn('company_logo', 'primary_color');
        }
    }
}

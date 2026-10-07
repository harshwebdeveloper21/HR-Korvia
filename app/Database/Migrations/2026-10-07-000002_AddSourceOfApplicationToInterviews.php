<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSourceOfApplicationToInterviews extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('interviews')) {
            return;
        }

        if (!in_array('source_of_application', $this->db->getFieldNames('interviews'), true)) {
            $this->forge->addColumn('interviews', [
                'source_of_application' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'interview_round',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('interviews') && in_array('source_of_application', $this->db->getFieldNames('interviews'), true)) {
            $this->forge->dropColumn('interviews', 'source_of_application');
        }
    }
}

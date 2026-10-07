<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddContactAndManagerToInterviewAssessments extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('interview_assessments')) {
            return;
        }

        $fields = $this->db->getFieldNames('interview_assessments');

        if (!in_array('contact_number', $fields, true)) {
            $this->forge->addColumn('interview_assessments', [
                'contact_number' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'interview_mode',
                ],
            ]);
        }

        if (!in_array('reporting_manager', $fields, true)) {
            $this->forge->addColumn('interview_assessments', [
                'reporting_manager' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'contact_number',
                ],
            ]);
        }
    }

    public function down()
    {
        if (!$this->db->tableExists('interview_assessments')) {
            return;
        }
        $fields = $this->db->getFieldNames('interview_assessments');
        foreach (['reporting_manager', 'contact_number'] as $field) {
            if (in_array($field, $fields, true)) {
                $this->forge->dropColumn('interview_assessments', $field);
            }
        }
    }
}

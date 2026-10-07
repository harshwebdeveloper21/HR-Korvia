<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAnnualCtcToOnboarding extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('onboarding')) {
            return;
        }

        if (!in_array('annual_ctc', $this->db->getFieldNames('onboarding'), true)) {
            $this->forge->addColumn('onboarding', [
                'annual_ctc' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '12,2',
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'offer_later_id',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('onboarding') && in_array('annual_ctc', $this->db->getFieldNames('onboarding'), true)) {
            $this->forge->dropColumn('onboarding', 'annual_ctc');
        }
    }
}

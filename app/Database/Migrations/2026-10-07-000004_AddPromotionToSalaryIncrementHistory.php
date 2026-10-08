<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPromotionToSalaryIncrementHistory extends Migration
{
    private const COLUMNS = [
        'previous_designation_id' => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'default' => null],
        'new_designation_id'      => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'default' => null],
        'previous_department_id'  => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'default' => null],
        'new_department_id'       => ['type' => 'INT', 'constraint' => 11, 'null' => true, 'default' => null],
        'reporting_manager'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'default' => null],
    ];

    public function up()
    {
        if (!$this->db->tableExists('salary_increment_history')) {
            return;
        }
        $existing = $this->db->getFieldNames('salary_increment_history');
        $add = array_diff_key(self::COLUMNS, array_flip($existing));
        if ($add) {
            $this->forge->addColumn('salary_increment_history', $add);
        }
    }

    public function down()
    {
        if (!$this->db->tableExists('salary_increment_history')) {
            return;
        }
        $existing = $this->db->getFieldNames('salary_increment_history');
        foreach (array_keys(self::COLUMNS) as $col) {
            if (in_array($col, $existing, true)) {
                $this->forge->dropColumn('salary_increment_history', $col);
            }
        }
    }
}

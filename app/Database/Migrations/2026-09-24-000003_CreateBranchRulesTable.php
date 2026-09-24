<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBranchRulesTable extends Migration
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
            'branch_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            // -- Payroll ----------------------------------------------
            'enable_payroll' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'payroll_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
                'default'    => 'monthly',
            ],
            'working_hours_per_day' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],
            'include_holidays_in_working_days' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'half_day_hours' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],
            // -- Sunday -----------------------------------------------
            'sunday_off' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            'sunday_pay_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            // -- Saturday ---------------------------------------------
            'saturday_off_enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'saturday_off_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'saturday_off_pattern' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'saturday_half_day_enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'saturday_half_day_pattern' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'saturday_pay_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'saturday_working_hours' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
                'default'    => 4,
            ],
            'saturday_full_day_override' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
            ],
            // -- Tax --------------------------------------------------
            'yearly_holidays' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'enable_tax' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'tax_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'tax' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            'salary_above_tax' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
            ],
            // -- Attendance -------------------------------------------
            'lunch_break' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'start_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'half_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'end_time' => [
                'type' => 'TIME',
                'null' => true,
            ],
            'grace_period' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0,
            ],
            'grace_minutes' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 10,
            ],
            // -- Overtime ---------------------------------------------
            'enable_overtime' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'overtime_multiplier' => [
                'type'       => 'DECIMAL',
                'constraint' => '5,2',
                'null'       => true,
            ],
            'overtime_rate_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'min_overtime_count_in_minutes' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            // -- Misc -------------------------------------------------
            'sandwich_leave' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'enable_geofencing' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
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
        $this->forge->addUniqueKey('branch_id');
        $this->forge->createTable('branch_rules', true);
    }

    public function down()
    {
        $this->forge->dropTable('branch_rules', true);
    }
}

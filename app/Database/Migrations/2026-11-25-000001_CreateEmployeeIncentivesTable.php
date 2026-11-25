<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmployeeIncentivesTable extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('employee_incentives')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'employee_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'department_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'incentive_type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                ],
                'percentage' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,2',
                ],
                'base_salary' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'default'    => 0.00,
                ],
                'incentive_amount' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'default'    => 0.00,
                ],
                'month' => [
                    'type'       => 'TINYINT',
                    'constraint' => 2,
                    'unsigned'   => true,
                ],
                'year' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 4,
                    'unsigned'   => true,
                ],
                'remarks' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'created_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
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
            $this->forge->addKey(['employee_id', 'month', 'year']);
            $this->forge->addKey('department_id');
            $this->forge->addForeignKey('employee_id', 'users', 'id', 'CASCADE', 'CASCADE');
            $this->forge->createTable('employee_incentives');
        }

        if ($this->db->tableExists('payroll') && !$this->db->fieldExists('incentive_amount', 'payroll')) {
            $this->forge->addColumn('payroll', [
                'incentive_amount' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'default'    => 0.00,
                    'after'      => 'bonuses',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('employee_incentives')) {
            $this->forge->dropTable('employee_incentives', true);
        }

        if ($this->db->tableExists('payroll') && $this->db->fieldExists('incentive_amount', 'payroll')) {
            $this->forge->dropColumn('payroll', 'incentive_amount');
        }
    }
}

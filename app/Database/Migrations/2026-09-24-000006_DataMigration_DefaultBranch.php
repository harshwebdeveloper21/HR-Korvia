<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Data Migration: Create the default "Head Office" branch and assign all
 * existing HR and employee users to it. Also copies the current global
 * company_rules into branch_rules for the default branch.
 *
 * This migration is safe to run on a live database — it never deletes
 * existing data, only inserts defaults and updates NULLs.
 */
class DataMigration_DefaultBranch extends Migration
{
    public function up()
    {
        $now = date('Y-m-d H:i:s');

        // -- 1. Create the default "Head Office" branch ------------------------
        $existingDefault = $this->db->table('branches')
            ->where('code', 'HO')
            ->get()->getRowArray();

        if (!$existingDefault) {
            $this->db->table('branches')->insert([
                'name'       => 'Head Office',
                'code'       => 'HO',
                'address'    => 'Default Office',
                'city'       => 'Head Office',
                'phone'      => '',
                'status'     => 'active',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        $branch = $this->db->table('branches')
            ->where('code', 'HO')
            ->get()->getRowArray();

        $branchId = $branch['id'];

        // -- 2. Assign all HR and employee users that have no branch -----------
        $this->db->table('users')
            ->whereIn('role', ['hr', 'employee'])
            ->where('branch_id IS NULL')
            ->update(['branch_id' => $branchId]);

        // -- 3. Copy global company_rules ? branch_rules for Head Office -------
        $existingBranchRule = $this->db->table('branch_rules')
            ->where('branch_id', $branchId)
            ->get()->getRowArray();

        if (!$existingBranchRule) {
            $globalRule = $this->db->table('company_rules')
                ->orderBy('id', 'DESC')
                ->get()->getRowArray();

            if ($globalRule) {
                unset($globalRule['id']);
                $globalRule['branch_id']   = $branchId;
                $globalRule['grace_minutes'] = $globalRule['grace_period'] ?? 10;
                $globalRule['created_at']  = $now;
                $globalRule['updated_at']  = $now;

                // Remove any columns that don't exist in branch_rules
                $branchRulesCols = array_column(
                    $this->db->getFieldData('branch_rules'),
                    'name'
                );
                foreach (array_keys($globalRule) as $key) {
                    if (!in_array($key, $branchRulesCols)) {
                        unset($globalRule[$key]);
                    }
                }

                $this->db->table('branch_rules')->insert($globalRule);
            } else {
                // No global rule exists yet — insert a safe default
                $this->db->table('branch_rules')->insert([
                    'branch_id'              => $branchId,
                    'payroll_type'           => 'monthly',
                    'working_hours_per_day'  => 8,
                    'half_day_hours'         => 4,
                    'sunday_off'             => 1,
                    'grace_period'           => 10,
                    'grace_minutes'          => 10,
                    'start_time'             => '09:00:00',
                    'end_time'               => '18:00:00',
                    'lunch_break'            => '01:00:00',
                    'created_at'             => $now,
                    'updated_at'             => $now,
                ]);
            }
        }
    }

    public function down()
    {
        // Remove the default branch rule
        $branch = $this->db->table('branches')
            ->where('code', 'HO')
            ->get()->getRowArray();

        if ($branch) {
            $this->db->table('branch_rules')
                ->where('branch_id', $branch['id'])
                ->delete();

            // Reset users to NULL
            $this->db->table('users')
                ->where('branch_id', $branch['id'])
                ->update(['branch_id' => null]);

            // Soft-delete the default branch (don't hard delete)
            $this->db->table('branches')
                ->where('id', $branch['id'])
                ->update(['deleted_at' => date('Y-m-d H:i:s')]);
        }
    }
}

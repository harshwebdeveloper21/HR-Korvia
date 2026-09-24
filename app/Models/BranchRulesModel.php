<?php

namespace App\Models;

use CodeIgniter\Model;

class BranchRulesModel extends Model
{
    protected $table      = 'branch_rules';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'branch_id',
        'enable_payroll', 'payroll_type', 'working_hours_per_day',
        'include_holidays_in_working_days', 'half_day_hours',
        'sunday_off', 'sunday_pay_type',
        'saturday_off_enabled', 'saturday_off_type', 'saturday_off_pattern',
        'saturday_half_day_enabled', 'saturday_half_day_pattern',
        'saturday_pay_type', 'saturday_working_hours', 'saturday_full_day_override',
        'yearly_holidays', 'enable_tax', 'tax_type', 'tax', 'salary_above_tax',
        'lunch_break', 'start_time', 'half_time', 'end_time',
        'grace_period', 'grace_minutes',
        'enable_overtime', 'overtime_multiplier', 'overtime_rate_type',
        'min_overtime_count_in_minutes', 'sandwich_leave', 'enable_geofencing',
        'created_at', 'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $returnType    = 'array';

    /**
     * Get rules for a given branch. Falls back to global company_rules if none set.
     */
    public function getRulesForBranch(int $branchId): ?array
    {
        $rules = $this->where('branch_id', $branchId)->first();

        if (!$rules) {
            // Fallback: copy from global company_rules
            $global = $this->db->table('company_rules')->orderBy('id', 'DESC')->get()->getRowArray();
            return $global ?: null;
        }

        return $rules;
    }

    /**
     * Get branch_id for a user then return that branch's rules.
     */
    public function getRulesForUser(int $userId): ?array
    {
        $user = $this->db->table('users')
            ->select('branch_id')
            ->where('id', $userId)
            ->get()->getRowArray();

        if (!$user || empty($user['branch_id'])) {
            // Admin or unassigned ? global rules
            $global = $this->db->table('company_rules')->orderBy('id', 'DESC')->get()->getRowArray();
            return $global ?: null;
        }

        return $this->getRulesForBranch((int)$user['branch_id']);
    }

    /**
     * Create a branch rules row by copying the current global defaults.
     */
    public function createDefaultForBranch(int $branchId): void
    {
        $existing = $this->where('branch_id', $branchId)->first();
        if ($existing) {
            return; // already exists
        }

        $global = $this->db->table('company_rules')->orderBy('id', 'DESC')->get()->getRowArray();
        $now    = date('Y-m-d H:i:s');

        if ($global) {
            unset($global['id']);
            $data = array_intersect_key($global, array_flip($this->allowedFields));
        } else {
            $data = [
                'payroll_type'          => 'monthly',
                'working_hours_per_day' => 8,
                'half_day_hours'        => 4,
                'sunday_off'            => 1,
                'grace_period'          => 10,
                'grace_minutes'         => 10,
                'start_time'            => '09:00:00',
                'end_time'              => '18:00:00',
                'lunch_break'           => '01:00:00',
            ];
        }

        $data['branch_id']   = $branchId;
        $data['created_at']  = $now;
        $data['updated_at']  = $now;
        $data['grace_minutes'] = $data['grace_minutes'] ?? $data['grace_period'] ?? 10;

        $this->insert($data);
    }
}

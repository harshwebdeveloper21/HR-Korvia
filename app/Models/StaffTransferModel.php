<?php

namespace App\Models;

use CodeIgniter\Model;

class StaffTransferModel extends Model
{
    protected $table      = 'staff_transfers';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id', 'from_branch_id', 'to_branch_id',
        'transferred_by', 'reason', 'effective_date', 'created_at',
    ];

    protected $useTimestamps = false;
    protected $returnType    = 'array';

    /**
     * Get full transfer history for a user, joining branch and transferrer names.
     */
    public function getHistoryForUser(int $userId): array
    {
        return $this->db->table('staff_transfers st')
            ->select('st.*, 
                fb.name AS from_branch_name, 
                tb.name AS to_branch_name,
                ui.firstname, ui.lastname,
                tui.firstname AS transferred_by_firstname,
                tui.lastname  AS transferred_by_lastname')
            ->join('branches fb', 'fb.id = st.from_branch_id', 'left')
            ->join('branches tb', 'tb.id = st.to_branch_id', 'left')
            ->join('user_info ui', 'ui.user_id = st.user_id', 'left')
            ->join('user_info tui', 'tui.user_id = st.transferred_by', 'left')
            ->where('st.user_id', $userId)
            ->orderBy('st.created_at', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Get all transfer records (Admin view), optionally filtered by branch.
     */
    public function getAllHistory(?int $branchId = null): array
    {
        $builder = $this->db->table('staff_transfers st')
            ->select('st.*, 
                fb.name AS from_branch_name, 
                tb.name AS to_branch_name,
                ui.firstname, ui.lastname,
                tui.firstname AS transferred_by_firstname,
                tui.lastname  AS transferred_by_lastname')
            ->join('branches fb', 'fb.id = st.from_branch_id', 'left')
            ->join('branches tb', 'tb.id = st.to_branch_id', 'left')
            ->join('user_info ui', 'ui.user_id = st.user_id', 'left')
            ->join('user_info tui', 'tui.user_id = st.transferred_by', 'left');

        if ($branchId) {
            $builder->groupStart()
                    ->where('st.from_branch_id', $branchId)
                    ->orWhere('st.to_branch_id', $branchId)
                    ->groupEnd();
        }

        return $builder->orderBy('st.created_at', 'DESC')->get()->getResultArray();
    }
}

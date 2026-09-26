<?php

namespace App\Models;

use CodeIgniter\Model;

class BranchModel extends Model
{
    protected $table      = 'branches';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name', 'code', 'address', 'city', 'phone', 'status',
        'latitude', 'longitude', 'radius',
        'created_at', 'updated_at', 'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $returnType    = 'array';

    // -- Soft-delete support ---------------------------------------------------
    protected $useSoftDeletes  = true;
    protected $deletedField    = 'deleted_at';

    /**
     * Get all active branches (for dropdowns).
     */
    public function getActiveBranches(): array
    {
        return $this->where('status', 'active')
                    ->orderBy('name', 'ASC')
                    ->findAll();
    }

    /**
     * Get branches with their HR count and staff count.
     */
    public function getBranchesWithStats(string $search = '', int $perPage = 10): array
    {
        $builder = $this->db->table('branches b')
            ->select('b.*, 
                (SELECT COUNT(*) FROM users WHERE branch_id = b.id AND role = "hr"   AND is_deleted = 0) AS hr_count,
                (SELECT COUNT(*) FROM users WHERE branch_id = b.id AND role = "employee" AND is_deleted = 0) AS staff_count')
            ->where('b.deleted_at IS NULL');

        if ($search) {
            $builder->groupStart()
                    ->like('b.name', $search)
                    ->orLike('b.code', $search)
                    ->orLike('b.city', $search)
                    ->groupEnd();
        }

        return $builder->orderBy('b.created_at', 'DESC')->get()->getResultArray();
    }

    /**
     * Check if a branch has active HR or staff users.
     * Used before soft-deleting to block deletion.
     */
    public function hasActiveUsers(int $branchId): bool
    {
        $count = $this->db->table('users')
            ->where('branch_id', $branchId)
            ->whereIn('role', ['hr', 'employee'])
            ->where('is_deleted', 0)
            ->countAllResults();

        return $count > 0;
    }
}

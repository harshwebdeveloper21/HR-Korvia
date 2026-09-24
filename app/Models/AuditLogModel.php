<?php

namespace App\Models;

use CodeIgniter\Model;

class AuditLogModel extends Model
{
    protected $table      = 'audit_logs';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id', 'action', 'model', 'record_id',
        'old_value', 'new_value', 'ip_address', 'created_at',
    ];

    protected $useTimestamps = false;
    protected $returnType    = 'array';

    /**
     * Convenience method to write an audit entry from a controller.
     *
     * @param int|null $actorId  The user performing the action
     * @param string   $action   e.g. 'branch.create', 'branch.delete', 'transfer'
     * @param string   $model    e.g. 'Branch', 'StaffTransfer'
     * @param int|null $recordId Primary key of affected record
     * @param mixed    $oldValue Previous state (will be JSON encoded)
     * @param mixed    $newValue New state (will be JSON encoded)
     */
    public function log(
        ?int $actorId,
        string $action,
        string $model,
        ?int $recordId = null,
        $oldValue = null,
        $newValue = null
    ): void {
        try {
            $this->insert([
                'user_id'    => $actorId,
                'action'     => $action,
                'model'      => $model,
                'record_id'  => $recordId,
                'old_value'  => $oldValue !== null ? json_encode($oldValue) : null,
                'new_value'  => $newValue !== null ? json_encode($newValue) : null,
                'ip_address' => \Config\Services::request()->getIPAddress(),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'AuditLog::log failed: ' . $e->getMessage());
        }
    }
}

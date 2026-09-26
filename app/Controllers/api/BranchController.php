<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;
use App\Services\AuthService;
use App\Models\BranchModel;
use App\Models\BranchRulesModel;
use App\Models\UserModel;
use App\Models\UserInfoModel;
use App\Models\AuditLogModel;

/**
 * BranchController — Admin-only CRUD for branches.
 * All methods check for admin role server-side (AdminOnlyFilter also applied
 * at route level, but defence-in-depth here too).
 */
class BranchController extends ResourceController
{
    protected $authService;
    protected $branchModel;
    protected $branchRulesModel;
    protected $userModel;
    protected $userInfoModel;
    protected $auditLog;
    protected $db;

    public function __construct()
    {
        $this->db               = \Config\Database::connect();
        $this->authService      = new AuthService(service('request'));
        $this->branchModel      = new BranchModel();
        $this->branchRulesModel = new BranchRulesModel();
        $this->userModel        = new UserModel();
        $this->userInfoModel    = new UserInfoModel();
        $this->auditLog         = new AuditLogModel();
    }

    // -- Helper ----------------------------------------------------------------

    private function requireAdmin(): ?object
    {
        $user = $this->authService->check();
        if (!$user || $user->role !== 'admin') {
            return null;
        }
        return $user;
    }

    // -- Page Views ------------------------------------------------------------

    public function index()
    {
        if (!$this->requireAdmin()) {
            return redirect()->to('/dashboard')->with('error', 'Admin access required.');
        }
        return view('branches/index');
    }

    public function create()
    {
        if (!$this->requireAdmin()) {
            return redirect()->to('/dashboard');
        }
        return view('branches/form');
    }

    public function edit($id = null)
    {
        if (!$this->requireAdmin()) {
            return redirect()->to('/dashboard');
        }
        $branch = $this->branchModel->find($id);
        if (!$branch) {
            return redirect()->to('/branches')->with('error', 'Branch not found.');
        }
        return view('branches/form', ['branch' => $branch]);
    }

    public function assignHrPage($id = null)
    {
        if (!$this->requireAdmin()) {
            return redirect()->to('/dashboard');
        }
        $branch = $this->branchModel->find($id);
        if (!$branch) {
            return redirect()->to('/branches')->with('error', 'Branch not found.');
        }
        $hrUsers = $this->db->table('users u')
            ->select('u.id, ui.firstname, ui.lastname, u.email, u.branch_id, u.can_transfer_staff')
            ->join('user_info ui', 'ui.user_id = u.id', 'left')
            ->where('u.role', 'hr')
            ->where('u.is_deleted', 0)
            ->orderBy('ui.firstname')
            ->get()->getResultArray();

        return view('branches/assign_hr', [
            'branch'  => $branch,
            'hrUsers' => $hrUsers,
        ]);
    }

    // -- API Endpoints ---------------------------------------------------------

    /**
     * GET api/branches — list all branches (search + pagination)
     */
    public function list()
    {
        if (!$this->requireAdmin()) {
            return $this->respond(['status' => 'error', 'message' => 'Admin access required.'], 403);
        }

        $search  = $this->request->getGet('search') ?? '';
        $perPage = (int)($this->request->getGet('per_page') ?? 10);
        $page    = (int)($this->request->getGet('page') ?? 1);
        $offset  = ($page - 1) * $perPage;

        // Total count
        $totalBuilder = $this->db->table('branches b')->where('b.deleted_at IS NULL');
        if ($search) {
            $totalBuilder->groupStart()->like('b.name', $search)->orLike('b.code', $search)->groupEnd();
        }
        $total = $totalBuilder->countAllResults();

        // Fetch with stats
        $builder = $this->db->table('branches b')
            ->select('b.*, 
                (SELECT COUNT(*) FROM users WHERE branch_id = b.id AND role = "hr"       AND is_deleted = 0) AS hr_count,
                (SELECT COUNT(*) FROM users WHERE branch_id = b.id AND role = "employee" AND is_deleted = 0) AS staff_count')
            ->where('b.deleted_at IS NULL');

        if ($search) {
            $builder->groupStart()->like('b.name', $search)->orLike('b.code', $search)->groupEnd();
        }

        $branches = $builder->orderBy('b.created_at', 'DESC')
                            ->limit($perPage, $offset)
                            ->get()->getResultArray();

        return $this->respond([
            'status' => 'success',
            'data'   => $branches,
            'total'  => $total,
            'page'   => $page,
            'per_page' => $perPage,
        ]);
    }

    /**
     * POST api/branches/set-active
     * Sets the active branch context for admin users
     */
    public function setActiveBranch()
    {
        $user = $this->authService->check();
        if (!$user || $user->role !== 'admin') {
            return $this->respond(['status' => 'error', 'message' => 'Admin access required.'], 403);
        }

        $json = $this->request->getJSON();
        $branchId = isset($json->branch_id) ? $json->branch_id : '';
        
        session()->set('admin_active_branch', $branchId);
        
        return $this->respond(['status' => 'success', 'message' => 'Branch filter updated.']);
    }

    /**
     * GET api/branches/(:num) — single branch
     */
    public function show($id = null)
    {
        if (!$this->requireAdmin()) {
            return $this->respond(['status' => 'error', 'message' => 'Admin access required.'], 403);
        }

        $branch = $this->branchModel->find($id);
        if (!$branch) {
            return $this->respond(['status' => 'error', 'message' => 'Branch not found.'], 404);
        }

        $rules = $this->branchRulesModel->where('branch_id', $id)->first();

        // HR assigned to this branch
        $hrList = $this->db->table('users u')
            ->select('u.id, ui.firstname, ui.lastname, u.email, u.can_transfer_staff')
            ->join('user_info ui', 'ui.user_id = u.id', 'left')
            ->where('u.branch_id', $id)
            ->where('u.role', 'hr')
            ->where('u.is_deleted', 0)
            ->get()->getResultArray();

        return $this->respond([
            'status' => 'success',
            'data'   => $branch,
            'rules'  => $rules,
            'hr'     => $hrList,
        ]);
    }

    /**
     * POST api/branches — create branch
     */
    public function store()
    {
        $admin = $this->requireAdmin();
        if (!$admin) {
            return $this->respond(['status' => 'error', 'message' => 'Admin access required.'], 403);
        }

        $data = $this->request->getJSON(true) ?? $this->request->getPost();

        // Validation
        $rules = [
            'name' => 'required|min_length[2]|max_length[255]',
            'code' => 'required|min_length[2]|max_length[50]|is_unique[branches.code]',
        ];
        if (!$this->validate($rules)) {
            return $this->respond([
                'status' => 'error',
                'errors' => $this->validator->getErrors(),
            ], 422);
        }

        $insertData = [
            'name'    => trim($data['name']),
            'code'    => strtoupper(trim($data['code'])),
            'address' => $data['address'] ?? null,
            'city'      => $data['city'] ?? null,
            'phone'     => $data['phone'] ?? null,
            'status'    => $data['status'] ?? 'active',
            'latitude'  => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'radius'    => isset($data['radius']) ? (int)$data['radius'] : 100,
        ];

        $branchService = new \App\Services\BranchService();
        $branchId = $branchService->createBranch($insertData, $admin->sub);

        return $this->respond([
            'status'    => 'success',
            'message'   => 'Branch created successfully.',
            'branch_id' => $branchId,
        ]);
    }

    /**
     * PUT api/branches/(:num) — update branch
     */
    public function update($id = null)
    {
        $admin = $this->requireAdmin();
        if (!$admin) {
            return $this->respond(['status' => 'error', 'message' => 'Admin access required.'], 403);
        }

        $branch = $this->branchModel->find($id);
        if (!$branch) {
            return $this->respond(['status' => 'error', 'message' => 'Branch not found.'], 404);
        }

        $data = $this->request->getJSON(true) ?? $this->request->getRawInput();

        $rules = [
            'name' => 'required|min_length[2]|max_length[255]',
            'code' => "required|min_length[2]|max_length[50]|is_unique[branches.code,id,{$id}]",
        ];
        if (!$this->validate($rules)) {
            return $this->respond([
                'status' => 'error',
                'errors' => $this->validator->getErrors(),
            ], 422);
        }

        $updateData = [
            'name'    => trim($data['name']),
            'code'    => strtoupper(trim($data['code'])),
            'address' => $data['address'] ?? $branch['address'],
            'city'      => $data['city'] ?? $branch['city'],
            'phone'     => $data['phone'] ?? $branch['phone'],
            'status'    => $data['status'] ?? $branch['status'],
            'latitude'  => $data['latitude'] ?? $branch['latitude'],
            'longitude' => $data['longitude'] ?? $branch['longitude'],
            'radius'    => isset($data['radius']) ? (int)$data['radius'] : ($branch['radius'] ?? 100),
        ];

        $this->branchModel->update($id, $updateData);
        $this->auditLog->log($admin->sub, 'branch.update', 'Branch', (int)$id, $branch, $updateData);

        return $this->respond(['status' => 'success', 'message' => 'Branch updated successfully.']);
    }

    /**
     * DELETE api/branches/(:num) — soft-delete a branch
     */
    public function delete($id = null)
    {
        $admin = $this->requireAdmin();
        if (!$admin) {
            return $this->respond(['status' => 'error', 'message' => 'Admin access required.'], 403);
        }

        $branch = $this->branchModel->find($id);
        if (!$branch) {
            return $this->respond(['status' => 'error', 'message' => 'Branch not found.'], 404);
        }

        $branchService = new \App\Services\BranchService();
        try {
            $branchService->deleteBranch((int)$id, $admin->sub);
            return $this->respond(['status' => 'success', 'message' => 'Branch deleted successfully.']);
        } catch (\Exception $e) {
            return $this->respond([
                'status'  => 'error',
                'message' => $e->getMessage(),
            ], 409);
        }
    }

    /**
     * POST api/branches/assign-hr — assign an HR user to a branch
     */
    public function assignHr()
    {
        $admin = $this->requireAdmin();
        if (!$admin) {
            return $this->respond(['status' => 'error', 'message' => 'Admin access required.'], 403);
        }

        $data     = $this->request->getJSON(true) ?? $this->request->getPost();
        $hrUserId = (int)($data['user_id'] ?? 0);
        $branchId = (int)($data['branch_id'] ?? 0);

        if (!$hrUserId || !$branchId) {
            return $this->respond(['status' => 'error', 'message' => 'user_id and branch_id are required.'], 422);
        }

        $hrUser = $this->userModel->where('id', $hrUserId)->where('role', 'hr')->first();
        if (!$hrUser) {
            return $this->respond(['status' => 'error', 'message' => 'HR user not found.'], 404);
        }

        $branch = $this->branchModel->find($branchId);
        if (!$branch) {
            return $this->respond(['status' => 'error', 'message' => 'Branch not found.'], 404);
        }

        $oldBranchId = $hrUser['branch_id'];
        $this->userModel->update($hrUserId, ['branch_id' => $branchId]);
        $this->auditLog->log($admin->sub, 'hr.assign_branch', 'User', $hrUserId, 
            ['branch_id' => $oldBranchId], ['branch_id' => $branchId]);

        return $this->respond(['status' => 'success', 'message' => 'HR assigned to branch successfully.']);
    }

    /**
     * POST api/branches/toggle-transfer-permission — toggle can_transfer_staff for an HR
     */
    public function toggleTransferPermission()
    {
        $admin = $this->requireAdmin();
        if (!$admin) {
            return $this->respond(['status' => 'error', 'message' => 'Admin access required.'], 403);
        }

        $data     = $this->request->getJSON(true) ?? $this->request->getPost();
        $hrUserId = (int)($data['user_id'] ?? 0);

        if (!$hrUserId) {
            return $this->respond(['status' => 'error', 'message' => 'user_id is required.'], 422);
        }

        $hrUser = $this->userModel->where('id', $hrUserId)->where('role', 'hr')->first();
        if (!$hrUser) {
            return $this->respond(['status' => 'error', 'message' => 'HR user not found.'], 404);
        }

        $newValue = $hrUser['can_transfer_staff'] ? 0 : 1;
        $this->userModel->update($hrUserId, ['can_transfer_staff' => $newValue]);
        $this->auditLog->log($admin->sub, 'hr.toggle_transfer_permission', 'User', $hrUserId,
            ['can_transfer_staff' => $hrUser['can_transfer_staff']],
            ['can_transfer_staff' => $newValue]);

        return $this->respond([
            'status'              => 'success',
            'can_transfer_staff'  => $newValue,
            'message'             => $newValue ? 'Transfer permission granted.' : 'Transfer permission revoked.',
        ]);
    }

    /**
     * GET api/branches/list-all — dropdown data (name + id) for all active branches
     */
    public function listAll()
    {
        $user = $this->authService->check();
        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'Unauthorized'], 401);
        }

        $branches = $this->branchModel->getActiveBranches();

        return $this->respond(['status' => 'success', 'data' => $branches]);
    }
}


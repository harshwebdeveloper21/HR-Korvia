<?php

namespace App\Controllers;

use App\Services\AuthService;

class OrgTreeController extends BaseController
{
    private const ROLE_LABELS = [
        'admin'              => 'Super Admin',
        'hr'                 => 'HR',
        'branch_admin'       => 'Branch Manager',
        'department_manager' => 'Department Manager',
        'employee'           => 'Employee',
    ];

    public function index()
    {
        $authUser = (new AuthService($this->request))->check();
        if (!$authUser) {
            return redirect()->to('/login');
        }

        $db     = \Config\Database::connect();
        $meId   = (int) ($authUser->sub ?? $authUser->id ?? 0);
        $meRole = (string) ($authUser->role ?? '');

        $rows = $db->table('users u')
            ->select('u.id, u.username, u.email, u.role, u.branch_id, u.department_id,
                      ui.id AS info_id, ui.firstname, ui.lastname, ui.employee_id, ui.profile_image, ds.designation_name')
            ->join('user_info ui', 'ui.user_id = u.id', 'left')
            ->join('designation ds', 'ds.id = ui.designation_id', 'left')
            ->where('u.is_deleted', 0)
            ->whereIn('u.role', array_keys(self::ROLE_LABELS))
            ->orderBy('ui.firstname', 'ASC')
            ->get()->getResultArray();

        $people = [];
        foreach ($rows as $r) {
            $name = trim(preg_replace('/\s+/', ' ', ($r['firstname'] ?? '') . ' ' . ($r['lastname'] ?? '')));
            $people[(int) $r['id']] = [
                'id'            => (int) $r['id'],
                'info_id'       => $r['info_id'] ? (int) $r['info_id'] : null,
                'name'          => ucwords($name !== '' ? $name : trim($r['username'] ?? $r['email'])),
                'role'          => $r['role'],
                'role_label'    => self::ROLE_LABELS[$r['role']],
                'designation'   => $r['designation_name'] ?? '',
                'employee_code' => $r['employee_id'] ?? '',
                'avatar'        => getUserProfileImage($r['profile_image'] ?? null),
                'branch_id'     => $r['branch_id'] !== null ? (int) $r['branch_id'] : 0,
                'department_id' => $r['department_id'] !== null ? (int) $r['department_id'] : 0,
                'is_me'         => (int) $r['id'] === $meId,
            ];
        }

        $me = $people[$meId] ?? null;

        $branches = $db->table('branches')->select('id, name, city')
            ->where('deleted_at', null)->orderBy('name', 'ASC')
            ->get()->getResultArray();
        $branchNames = [];
        foreach ($branches as $b) {
            $branchNames[(int) $b['id']] = $b['name'];
        }

        $departments = [];
        foreach ($db->table('department')->select('id, department_name')->get()->getResultArray() as $d) {
            $departments[(int) $d['id']] = $d['department_name'];
        }

        $byRole = fn(string $role) => array_values(array_filter($people, fn($p) => $p['role'] === $role));

        // Which slice of the organisation this user may see.
        $scopeBranch = null;
        $scopeDept   = null;
        if (!in_array($meRole, ['admin', 'hr'], true)) {
            $scopeBranch = $me['branch_id'] ?? -1;
            if (in_array($meRole, ['department_manager', 'employee'], true)) {
                $scopeDept = $me['department_id'] ?? -1;
            }
        }

        $branchNodes = [];
        $branchIds   = array_keys($branchNames);
        foreach ($people as $p) {
            if ($p['branch_id'] && !isset($branchNames[$p['branch_id']]) && in_array($p['role'], ['branch_admin', 'department_manager', 'employee'], true)) {
                $branchIds[] = $p['branch_id'];
            }
        }
        $branchIds = array_unique($branchIds);
        $branchIds[] = 0;

        foreach ($branchIds as $bid) {
            if ($scopeBranch !== null && $bid !== $scopeBranch) {
                continue;
            }

            $inBranch = array_filter($people, fn($p) => $p['branch_id'] === $bid);
            $managers = array_values(array_filter($inBranch, fn($p) => $p['role'] === 'branch_admin'));
            $staff    = array_filter($inBranch, fn($p) => in_array($p['role'], ['department_manager', 'employee'], true));

            $deptIds = array_unique(array_map(fn($p) => $p['department_id'], $staff));
            usort($deptIds, fn($a, $b) => strcasecmp($departments[$a] ?? 'zzz', $departments[$b] ?? 'zzz'));

            $deptNodes = [];
            foreach ($deptIds as $did) {
                if ($scopeDept !== null && $did !== $scopeDept) {
                    continue;
                }
                $inDept = array_filter($staff, fn($p) => $p['department_id'] === $did);
                $deptNodes[] = [
                    'title'     => $departments[$did] ?? 'No Department',
                    'level'     => 'department',
                    'managers'  => array_values(array_filter($inDept, fn($p) => $p['role'] === 'department_manager')),
                    'employees' => array_values(array_filter($inDept, fn($p) => $p['role'] === 'employee')),
                ];
            }

            if (!$managers && !$deptNodes) {
                continue;
            }

            $branchNodes[] = [
                'title'       => $bid ? ($branchNames[$bid] ?? ('Branch #' . $bid)) : 'No Branch',
                'level'       => 'branch',
                'managers'    => $managers,
                'departments' => $deptNodes,
            ];
        }

        $totals = [
            'branches'    => count(array_filter($branchNodes, fn($b) => $b['title'] !== 'No Branch')),
            'departments' => array_sum(array_map(fn($b) => count($b['departments']), $branchNodes)),
            'people'      => 0,
        ];
        foreach ($branchNodes as $b) {
            $totals['people'] += count($b['managers']);
            foreach ($b['departments'] as $d) {
                $totals['people'] += count($d['managers']) + count($d['employees']);
            }
        }

        return view('org_tree/index', [
            'admins'      => $byRole('admin'),
            'hrs'         => $byRole('hr'),
            'branchNodes' => $branchNodes,
            'totals'      => $totals,
            'me'          => $me,
            'meRole'      => $meRole,
            'isFullView'  => $scopeBranch === null,
        ]);
    }
}

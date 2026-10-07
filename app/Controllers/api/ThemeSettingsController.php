<?php

namespace App\Controllers\Api;

use App\Models\CompanyLogoModel;
use App\Services\AuthService;
use CodeIgniter\RESTful\ResourceController;

class ThemeSettingsController extends ResourceController
{
    protected $companyModel;
    protected $authService;

    public function __construct()
    {
        helper('company');
        $this->companyModel = new CompanyLogoModel();
        $this->authService = new AuthService(service('request'));
    }

    private function canManage($user): bool
    {
        return $user && in_array($user->role, ['admin', 'hr'], true);
    }

    public function view()
    {
        $user = $this->authService->user();
        if (!$this->canManage($user)) {
            return redirect()->to('/dashboard')->with('error', 'Access denied. Admin and HR only.');
        }

        return view('settings/theme_settings', [
            'theme'        => getPrimaryTheme(),
            'defaultColor' => DEFAULT_PRIMARY_COLOR,
        ]);
    }

    public function getSettings()
    {
        $user = $this->authService->check();
        if (!$this->canManage($user)) {
            return $this->respond(['status' => 'error', 'message' => 'Access denied'], $user ? 403 : 401);
        }

        return $this->respond(['status' => 'success', 'data' => getPrimaryTheme()]);
    }

    public function updateSettings()
    {
        $user = $this->authService->check();
        if (!$this->canManage($user)) {
            return $this->respond(['status' => 'error', 'message' => 'Only Admin and HR can change the theme color'], $user ? 403 : 401);
        }

        $json = $this->request->getJSON(true) ?? [];
        $input = $json['primary_color'] ?? $this->request->getPost('primary_color');
        $reset = !empty($json['reset']) || $this->request->getPost('reset');

        $color = $reset ? DEFAULT_PRIMARY_COLOR : normalizeHexColor($input);
        if (!$color) {
            return $this->respond(['status' => 'error', 'message' => 'Please choose a valid HEX color like #e66136'], 400);
        }

        $this->ensureColumn();

        $company = $this->companyModel->first();
        $value = $color === DEFAULT_PRIMARY_COLOR ? null : $color;

        if ($company) {
            $this->companyModel->update($company['id'], ['primary_color' => $value]);
        } else {
            $this->companyModel->insert(['primary_color' => $value]);
        }

        return $this->respond([
            'status'  => 'success',
            'message' => 'Primary color updated for all branches.',
            'data'    => ['primary_color' => $color, 'contrast' => getContrastTextColor($color)],
        ]);
    }

    private function ensureColumn(): void
    {
        $db = db_connect();
        if (!in_array('primary_color', $db->getFieldNames('company_logo'), true)) {
            \Config\Database::forge()->addColumn('company_logo', [
                'primary_color' => ['type' => 'VARCHAR', 'constraint' => 7, 'null' => true, 'default' => null],
            ]);
        }
    }
}

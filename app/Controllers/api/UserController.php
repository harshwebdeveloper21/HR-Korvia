<?php

namespace App\Controllers\Api;

use App\Models\UserModel;
use CodeIgniter\RESTful\ResourceController;

class UserController extends ResourceController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // Register User
    public function register()
    {
        $data = $this->request->getPost();

        if (!$this->validate([
            'username' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[6]',
        ])) {
            return $this->respond(['status' => 'error', 'message' => $this->validator->getErrors()], 400);
        }

        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        $this->userModel->insert($data);

        return $this->respond(['status' => 'success', 'message' => 'User registered successfully']);
    }

    // Login User
    public function login()
    {
        $data = $this->request->getPost();
        $user = $this->userModel->where('email', $data['email'])->first();

        if ($user && password_verify($data['password'], $user['password'])) {
            $this->userModel->update($user['id'], ['status' => 'online']);
            return $this->respond(['status' => 'success', 'message' => 'Login successful', 'data' => $user]);
        }

        return $this->respond(['status' => 'error', 'message' => 'Invalid credentials'], 401);
    }

    // Get All Users
    public function index()
    {
        $users = $this->userModel->findAll();
        return $this->respond(['status' => 'success', 'data' => $users]);
    }

    // Get User by ID
    public function show($id = null)
    {
        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->respond(['status' => 'error', 'message' => 'User not found'], 404);
        }

        return $this->respond(['status' => 'success', 'data' => $user]);
    }

    // Update User
    public function update($id = null)
    {
        $data = $this->request->getRawInput();

        if ($this->userModel->update($id, $data)) {
            return $this->respond(['status' => 'success', 'message' => 'User updated successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to update user'], 500);
    }

    // Delete User
    public function delete($id = null)
    {
        if ($this->userModel->delete($id)) {
            return $this->respond(['status' => 'success', 'message' => 'User deleted successfully']);
        }

        return $this->respond(['status' => 'error', 'message' => 'Failed to delete user'], 500);
    }
    public function updateStatus()
{
    $userModel = new UserModel();
    $userId = $this->request->getVar('user_id');

    if ($userId) {
        // Update last activity time
        $userModel->update($userId, ['status' => 'online']);
        return $this->response->setJSON(['status' => 'success', 'message' => 'User is online']);
    }

    return $this->response->setJSON(['status' => 'error', 'message' => 'User not found']);
}

}

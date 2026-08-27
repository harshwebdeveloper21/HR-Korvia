<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        $email = 'admin@gmail.com';
        $username = 'admin';
        $password = 'admin123';
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

        // Check if admin user already exists
        $userBuilder = $db->table('users');
        $existingUser = $userBuilder->where('email', $email)->get()->getRowArray();

        if ($existingUser) {
            // Update existing admin password and role
            $userBuilder->where('id', $existingUser['id'])->update([
                'username'      => $username,
                'password'      => $hashedPassword,
                'role'          => 'admin',
                'is_deleted'    => 0,
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
            $userId = $existingUser['id'];
        } else {
            // Insert new admin user
            $userBuilder->insert([
                'username'      => $username,
                'email'         => $email,
                'password'      => $hashedPassword,
                'role'          => 'admin',
                'chat_status'   => 'offline',
                'is_deleted'    => 0,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
            $userId = $db->insertID();
        }

        // Check user_info
        $userInfoBuilder = $db->table('user_info');
        $existingInfo = $userInfoBuilder->where('user_id', $userId)->get()->getRowArray();

        if ($existingInfo) {
            $userInfoBuilder->where('user_id', $userId)->update([
                'firstname' => 'Admin',
                'lastname'  => 'User',
                'email'     => $email,
                'role'      => 'admin',
                'updated_at'=> date('Y-m-d H:i:s'),
            ]);
        } else {
            $userInfoBuilder->insert([
                'user_id'       => $userId,
                'firstname'     => 'Admin',
                'lastname'      => 'User',
                'email'         => $email,
                'role'          => 'admin',
                'employee_id'   => 'EMP001',
                'salary'        => 50000.00,
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        // Ensure company_logo has at least one default record
        $logoBuilder = $db->table('company_logo');
        if ($logoBuilder->countAllResults() === 0) {
            $logoBuilder->insert([
                'company_name' => 'Fablead Developer',
                'logo_img'     => 'fab_logo.jpg',
                'created_by'   => $userId,
                'created_at'   => date('Y-m-d H:i:s'),
                'updated_at'   => date('Y-m-d H:i:s'),
            ]);
        }
    }
}

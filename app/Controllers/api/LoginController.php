<?php

namespace App\Controllers\Api;

use CodeIgniter\Controller;

class LoginController extends Controller
{
    public function index()
    {
        return view('dashboard/login');
    }
}

<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthFilter implements FilterInterface
{
    private const DEFAULT_JWT_SECRET = 'YourSecureJWTSecretKeyForHRSystem2024!';

    public function before(RequestInterface $request, $arguments = null)
    {
        $authService = new \App\Services\AuthService($request);

        if (!$authService->check()) {
            return redirect()->to('/login')->with('error', 'Session expired or login required.');
        }

        return;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    // No action needed after the controller's execution
    }
}

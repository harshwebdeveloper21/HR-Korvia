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
        $configuredKey = trim((string) (env('JWT_SECRET') ?? getenv('JWT_SECRET') ?? ''));
        $key = strlen($configuredKey) >= 32 ? $configuredKey : self::DEFAULT_JWT_SECRET;

        $authHeader = $request->getHeaderLine('Authorization');
        $token = null;
        // print_r($token);
        // die;
        if ($authHeader) {
            $arr = explode(' ', $authHeader);
            if (count($arr) === 2) {
                $token = $arr[1];
            }
        } else {
            $session = session();
            $token = $session->get('user_token');
        }

        if ($token) {
            try {
                $decoded = JWT::decode($token, new Key($key, 'HS256'));
                // Token is valid; proceed with the request
                return;
            } catch (\Exception $e) {
                // Token is invalid or expired
                return redirect()->to('/login')->with('error', 'Session expired. Please log in again.');
            }
        }

        // No token found; redirect to login
        return redirect()->to('/login')->with('error', 'You must be logged in to access this page.');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after the controller's execution
    }
}

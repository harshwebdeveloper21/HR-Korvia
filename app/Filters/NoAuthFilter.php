<?php

// namespace App\Filters;

// use CodeIgniter\HTTP\RequestInterface;
// use CodeIgniter\HTTP\ResponseInterface;
// use CodeIgniter\Filters\FilterInterface;
// use Firebase\JWT\JWT;
// use Firebase\JWT\Key;

// class NoAuthFilter implements FilterInterface
// {
//     public function before(RequestInterface $request, $arguments = null)
//     {
//         $session = session();
//         $token = $session->get('token');
//         // dd($token);

//         // Check if request is an API request
//         $isApiRequest = strpos($request->getUri()->getPath(), 'api/') !== false;

//         if ($token) {
//             try {
//                 // Decode JWT token
//                 $decoded = JWT::decode($token, new Key(getenv('JWT_SECRET'), 'HS256'));

//                 if ($isApiRequest) {
//                     // For API, return JSON response instead of redirecting
//                     return service('response')->setJSON([
//                         'status'  => false,
//                         'message' => 'User already logged in',
//                     ])->setStatusCode(403);
//                 } else {
//                     // For web-based routes, redirect to dashboard
//                     return redirect()->to('/dashboard');
//                 }
//             } catch (\Exception $e) {
//                 return; // Allow access if token is invalid or expired
//             }
//         }

//         return; // Allow access if no token exists
//     }

//     public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
//     {
//         // No action needed
//     }
// }

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class NoAuthFilter implements FilterInterface
{
    private const DEFAULT_JWT_SECRET = 'YourSecureJWTSecretKeyForHRSystem2024!';

    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $token = $session->get('token');
        $configuredKey = trim((string) (env('JWT_SECRET') ?? getenv('JWT_SECRET') ?? ''));
        $jwtKey = strlen($configuredKey) >= 32 ? $configuredKey : self::DEFAULT_JWT_SECRET;

        // Check if request is an API request
        $isApiRequest = strpos($request->getUri()->getPath(), 'api/') !== false;

        if ($token) {
            try {
                // Decode JWT token
                $decoded = JWT::decode($token, new Key($jwtKey, 'HS256'));

                if ($isApiRequest) {
                    // For API, return a fail response if the user is already logged in
                    return $this->sendFailRes('User already logged in', 403);
                    
                } else {
                    // For web-based routes, redirect to dashboard
                    return redirect()->to('/dashboard');
                }
            } catch (\Exception $e) {
                return; // Allow access if token is invalid or expired
            }
        }

        return; // Allow access if no token exists
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // No action needed after the request is processed
    }

    // Send failure response (using centralized response method)
    private function sendFailRes($message, $statusCode = 400, $errors = [])
    {
        return service('response')->setJSON([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ])->setStatusCode($statusCode);
    }
}


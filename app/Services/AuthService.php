<?php

namespace App\Services;

use Firebase\JWT\JWT;
use CodeIgniter\HTTP\RequestInterface;
use App\Models\UserModel;
use Firebase\JWT\Key;

class AuthService
{
    private const DEFAULT_JWT_SECRET = "YourSecureJWTSecretKeyForHRSystem2024!";
    private $key;
    private $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
        $configuredKey = trim((string) (env("JWT_SECRET") ?? getenv("JWT_SECRET") ?? ""));
        $this->key = strlen($configuredKey) >= 32 ? $configuredKey : self::DEFAULT_JWT_SECRET;

        if ($configuredKey !== "" && strlen($configuredKey) < 32) {
            log_message("error", "JWT_SECRET is too short. It must be at least 32 characters.");
        }
    }

    public function attempt($email, $password)
    {
        $userModel = new UserModel();
        $user = $userModel->where("email", $email)->first();

        if (!$user || !password_verify($password, $user["password"])) {
            return false;
        }

        $issuedAt = time();
        $expirationTime = $issuedAt + 86400; // Token valid for 24 hours
        $payload = [
            "iat" => $issuedAt,
            "exp" => $expirationTime,
            "sub" => $user["id"],
            "email" => $user["email"],
            "role" => $user["role"],
        ];

        $token = JWT::encode($payload, $this->key, "HS256");

        session()->set("user_token", $token);

        return $token;
    }

    public function check()
    {
        log_message("debug", "AuthService::check() called");

        // First try to get token from Authorization header or session
        $token = $this->getBearerToken();

        log_message(
            "debug",
            "AuthService::check() - Token found: " .
                ($token ? "yes (length: " . strlen($token) . ")" : "no"),
        );

        if ($token) {
            try {
                $decoded = JWT::decode($token, new Key($this->key, "HS256"));
                // Store user_id in session
                session()->set("user_id", $decoded->sub);
                log_message(
                    "debug",
                    "AuthService::check() - JWT valid for user: " .
                        $decoded->sub,
                );
                return (object) $decoded;
            } catch (\Firebase\JWT\ExpiredException $e) {
                log_message("error", "JWT Expired: " . $e->getMessage());
                session()->remove("user_token");
                // Don't return yet - try remember-me fallback
            } catch (\Exception $e) {
                log_message("error", "JWT Error: " . $e->getMessage());
                session()->remove("user_token");
                // Don't return yet - try remember-me fallback
            }
        }

        // Fallback: Try remember-me token to restore session
        log_message(
            "debug",
            "AuthService::check() - Trying remember-me fallback",
        );
        $result = $this->tryRememberMeLogin();
        if ($result) {
            log_message(
                "debug",
                "AuthService::check() - Remember-me login successful",
            );
            return $result;
        }

        log_message(
            "debug",
            "AuthService::check() - No valid authentication found",
        );
        return false;
    }

    public function user()
    {
        // First try to get token from Authorization header or session
        $token = $this->getBearerToken();

        if ($token) {
            try {
                $decoded = JWT::decode($token, new Key($this->key, "HS256"));
                return (object) $decoded;
            } catch (\Firebase\JWT\ExpiredException $e) {
                log_message("error", "JWT Expired: " . $e->getMessage());
                session()->remove("user_token");
                // Don't return yet - try remember-me fallback
            } catch (\Exception $e) {
                log_message("error", "JWT Error: " . $e->getMessage());
                session()->remove("user_token");
                // Don't return yet - try remember-me fallback
            }
        }

        // Fallback: Try remember-me token to restore session
        $result = $this->tryRememberMeLogin();
        if ($result) {
            return $result;
        }

        return null;
    }

    public function logout()
    {
        session()->remove("user_token");
        return true; // Logout successful
    }

    private function getBearerToken()
    {
        // Check Authorization header first
        $authorizationHeader = $this->request->getHeaderLine("Authorization");
        if ($authorizationHeader) {
            $arr = explode(" ", $authorizationHeader);
            if (count($arr) == 2 && strtolower($arr[0]) === "bearer" && $arr[1] !== 'null' && $arr[1] !== 'undefined') {
                return $arr[1];
            }
        }

        // Then check session - get fresh session value each time
        $session = session();
        return $session->get("user_token");
    }

    /**
     * Try to authenticate using remember-me cookie
     * This is called as a fallback when JWT is missing or expired
     *
     * @return object|false Returns decoded user object or false
     */
    private function tryRememberMeLogin()
    {
        log_message(
            "debug",
            "tryRememberMeLogin() - Checking for remember_me_token cookie",
        );
        log_message(
            "debug",
            "tryRememberMeLogin() - Available cookies: " .
                print_r(array_keys($_COOKIE), true),
        );

        $rememberToken = $_COOKIE["remember_me_token"] ?? null;

        if (!$rememberToken) {
            log_message(
                "debug",
                "tryRememberMeLogin() - No remember_me_token cookie found",
            );
            return false;
        }

        log_message(
            "debug",
            "tryRememberMeLogin() - Found remember_me_token cookie (length: " .
                strlen($rememberToken) .
                ")",
        );

        $db = db_connect();
        $rememberHash = hash("sha256", $rememberToken);

        $record = $db
            ->table("remember_tokens")
            ->where("token_hash", $rememberHash)
            ->where("expires_at >=", date("Y-m-d H:i:s"))
            ->get()
            ->getRow();

        if (!$record) {
            // Invalid or expired remember token - cleanup cookie
            log_message(
                "debug",
                "tryRememberMeLogin() - Token not found in DB or expired",
            );
            $secure = isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] !== "off";
            setcookie(
                "remember_me_token",
                "",
                time() - 3600,
                "/",
                "",
                $secure,
                true,
            );
            return false;
        }

        log_message(
            "debug",
            "tryRememberMeLogin() - Found valid token record for user_id: " .
                $record->user_id,
        );

        // Get user data
        $userModel = new UserModel();
        $user = $userModel->find($record->user_id);

        if (!$user) {
            log_message(
                "debug",
                "tryRememberMeLogin() - User not found in database",
            );
            return false;
        }

        // Generate new JWT token
        $newJwt = $this->generateTokenByUser($user);

        // Restore full session with JWT
        $session = session();
        $session->set([
            "user_id" => $user["id"],
            "logged_in" => true,
            "user_token" => $newJwt,
        ]);

        log_message(
            "debug",
            "tryRememberMeLogin() - Auto-login successful for user: " .
                $user["id"] .
                ", new JWT stored in session",
        );

        // Return decoded token data as object
        return (object) [
            "sub" => $user["id"],
            "email" => $user["email"],
            "role" => $user["role"],
        ];
    }

    public function generateTokenByUser(array $user)
    {
        $issuedAt = time();
        $expirationTime = $issuedAt + 86400; // 24 hours

        $payload = [
            "iat" => $issuedAt,
            "exp" => $expirationTime,
            "sub" => $user["id"],
            "email" => $user["email"],
            "role" => $user["role"],
        ];

        return JWT::encode($payload, $this->key, "HS256");
    }
}

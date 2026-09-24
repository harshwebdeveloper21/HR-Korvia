<?php
require "vendor/autoload.php";
$db = \Config\Database::connect();
$user_id = 2; // admin user id
$token = (new \App\Services\AuthService(service("request")))->login(["id" => 2, "email" => "admin@example.com", "role" => "admin"]);
echo "Token: $token\n";

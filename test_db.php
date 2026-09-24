<?php
$userData = ["id" => 2, "email" => "admin@example.com", "username" => ""];
$userInfo = ["id" => 1, "user_id" => 2, "firstname" => "Super", "lastname" => "Admin", "email" => "", "gender" => null, "date_of_birth" => null];
$company = ["company_name" => "Fablead"];
$profileData = array_merge($userData, $userInfo, [
    "company_name" => $company ? $company["company_name"] : "N/A"
]);
echo json_encode(["status" => "success", "data" => $profileData]);

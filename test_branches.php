<?php
require "vendor/autoload.php";
$db = \Config\Database::connect();
$user_id = 2;
// Mock request object for auth check
$_SERVER["HTTP_AUTHORIZATION"] = "Bearer MOCK";
echo "DB Connected.\n";

$perPage = 10;
$offset = 0;
$search = "";
$builder = $db->table("branches b")
              ->select("b.*, 
                  (SELECT COUNT(*) FROM users WHERE branch_id = b.id AND role = 'hr'       AND is_deleted = 0) AS hr_count,
                  (SELECT COUNT(*) FROM users WHERE branch_id = b.id AND role = 'employee' AND is_deleted = 0) AS staff_count")
              ->where("b.deleted_at IS NULL");
$branches = $builder->orderBy("b.created_at", "DESC")
                    ->limit($perPage, $offset)
                    ->get()->getResultArray();
echo json_encode(["status" => "success", "data" => $branches]);

<?php
$db = new PDO('mysql:host=localhost;dbname=hr_protal_08', 'root', '');
$stmt = $db->query('SHOW TABLES');
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));

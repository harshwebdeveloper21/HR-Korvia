<?php
$mysqli = new mysqli("localhost", "root", "", "hrprotaldemo_new");
$result = $mysqli->query("SHOW COLUMNS FROM leaves LIKE 'no_of_day'");
$row = $result->fetch_assoc();
echo trim($row['Type']);
?>

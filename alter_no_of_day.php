<?php
$mysqli = new mysqli("localhost", "root", "", "hrprotaldemo_new");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Modify no_of_day column
if ($mysqli->query("ALTER TABLE leaves MODIFY COLUMN no_of_day VARCHAR(50)")) {
    echo "Modified no_of_day to VARCHAR(50).\n";
} else {
    echo "Error modifying no_of_day: " . $mysqli->error . "\n";
}

$mysqli->close();
?>

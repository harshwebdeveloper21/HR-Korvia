<?php
$mysqli = new mysqli("localhost", "root", "", "hr_protal_new_db");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
}
$query = "ALTER TABLE branches 
    ADD COLUMN latitude VARCHAR(50) DEFAULT NULL, 
    ADD COLUMN longitude VARCHAR(50) DEFAULT NULL, 
    ADD COLUMN radius INT DEFAULT NULL";
if ($mysqli->query($query) === TRUE) {
    echo "Columns added successfully";
} else {
    echo "Error adding columns: " . $mysqli->error;
}
$mysqli->close();
?>

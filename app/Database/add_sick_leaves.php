<?php
$db = mysqli_connect('localhost', 'root', '', 'hrprotaldemo_new');
if (!$db) die("Connection failed: " . mysqli_connect_error());

$sql = "ALTER TABLE payroll ADD COLUMN used_sick_leaves DECIMAL(10,2) DEFAULT 0.00 AFTER used_paid_leaves, ADD COLUMN remaining_sick_leaves DECIMAL(10,2) DEFAULT 0.00 AFTER used_sick_leaves";
if (mysqli_query($db, $sql)) {
    echo "Columns added successfully";
} else {
    echo "Error adding columns: " . mysqli_error($db);
}
mysqli_close($db);

<?php
$mysqli = new mysqli("localhost", "root", "", "hrprotaldemo_new");

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Add leave_duration
$result = $mysqli->query("SHOW COLUMNS FROM leaves LIKE 'leave_duration'");
if($result->num_rows == 0) {
    if ($mysqli->query("ALTER TABLE leaves ADD COLUMN leave_duration VARCHAR(50) DEFAULT 'full_day'")) {
        echo "Added leave_duration.\n";
    } else {
        echo "Error adding leave_duration: " . $mysqli->error . "\n";
    }
} else {
    echo "leave_duration already exists.\n";
}

// Add half_day_type
$result = $mysqli->query("SHOW COLUMNS FROM leaves LIKE 'half_day_type'");
if($result->num_rows == 0) {
    if ($mysqli->query("ALTER TABLE leaves ADD COLUMN half_day_type VARCHAR(50) DEFAULT NULL")) {
        echo "Added half_day_type.\n";
    } else {
        echo "Error adding half_day_type: " . $mysqli->error . "\n";
    }
} else {
    echo "half_day_type already exists.\n";
}

$mysqli->close();
?>

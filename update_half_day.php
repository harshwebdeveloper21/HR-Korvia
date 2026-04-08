<?php
$mysqli = new mysqli("localhost", "root", "", "hrprotaldemo_new");
$mysqli->query("UPDATE leaves SET no_of_day = '0.5' WHERE leave_duration = 'half_day'");
echo "Updated " . $mysqli->affected_rows . " rows.\n";
$mysqli->close();
?>

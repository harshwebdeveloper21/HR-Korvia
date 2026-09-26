<?php
$db = mysqli_connect('localhost', 'root', '', 'hr_protal_new_db');
echo json_encode(mysqli_fetch_all(mysqli_query($db, 'DESCRIBE users'), MYSQLI_ASSOC));

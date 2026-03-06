<?php
include('/Applications/XAMPP/xamppfiles/htdocs/Ascend/config/db.php');

$users = $conn->query("SELECT id, username FROM users WHERE role = 'student'");
while ($row = $users->fetch_assoc()) {
    echo $row['id'] . " - " . $row['username'] . "\n";
}
?>
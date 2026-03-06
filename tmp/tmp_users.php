<?php
include('/Applications/XAMPP/xamppfiles/htdocs/Ascend/config/db.php');

// Let's check the users table too
$users = $conn->query("SELECT id, username, email FROM users WHERE role='student' OR role='parent'");
while ($row = $users->fetch_assoc()) {
    print_r($row);
}
?>
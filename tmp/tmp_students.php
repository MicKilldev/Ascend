<?php
include('/Applications/XAMPP/xamppfiles/htdocs/Ascend/config/db.php');

$students = $conn->query("SELECT * FROM students");
echo "Students table contents:\n";
while ($row = $students->fetch_assoc()) {
    print_r($row);
}
?>
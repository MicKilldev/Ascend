<?php
include('/Applications/XAMPP/xamppfiles/htdocs/Ascend/config/db.php');

$parents = $conn->query("SELECT * FROM parents");
echo "Parents table contents:\n";
while ($row = $parents->fetch_assoc()) {
    print_r($row);
}
?>
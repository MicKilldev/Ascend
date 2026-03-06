<?php
include('/Applications/XAMPP/xamppfiles/htdocs/Ascend/config/db.php');

function get_columns($conn, $table)
{
    $res = $conn->query("DESCRIBE $table");
    if (!$res)
        return "Table $table not found.\n";
    $cols = [];
    while ($row = $res->fetch_assoc()) {
        $cols[] = $row['Field'] . " (" . $row['Type'] . ")";
    }
    return "$table columns:\n" . implode("\n", $cols) . "\n\n";
}

echo get_columns($conn, 'grades');
echo get_columns($conn, 'students');
echo get_columns($conn, 'parents');
?>
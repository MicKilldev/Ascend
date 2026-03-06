<?php
include('/Applications/XAMPP/xamppfiles/htdocs/Ascend/config/db.php');

$parents_updates = [
    [27, 1], // Gina -> Mickael
    [28, 3], // Melvin Parent -> Melvin Zabala
    [29, 2], // Marco Parent -> Marco Jaime
    [30, 5], // Glaiza Parent -> Glaiza Rubin
    [31, 4], // Carl Parent -> Carl Arana
];

foreach ($parents_updates as $update) {
    $parent_user_id = $update[0];
    $student_id = $update[1];

    // In our database 'parents' table, 'student_id' refers to the user_id of the student right now based on how it's mapped, OR it just has bad data (1, 2, 3 instead of 11, 12, 13).
    $conn->query("UPDATE parents SET student_id = $student_id WHERE user_id = $parent_user_id");
}

echo "Assigned correctly.\n";
?>
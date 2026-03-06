<?php
include('/Applications/XAMPP/xamppfiles/htdocs/Ascend/config/db.php');

$students = [11, 12, 13, 14, 23];
$subjects = [
    ['code' => 'IT101', 'name' => 'Web Development 2', 'type' => 'Major', 'credits' => 3],
    ['code' => 'CS201', 'name' => 'Database Management', 'type' => 'Major', 'credits' => 3],
    ['code' => 'GE101', 'name' => 'Understanding the Self', 'type' => 'GE', 'credits' => 3]
];

foreach ($students as $student_id) {
    foreach ($subjects as $sub) {
        $prelim = rand(100, 300) / 100; // 1.0 to 3.0
        $midterm = rand(100, 300) / 100;
        $final = rand(100, 300) / 100;
        $average = ($prelim + $midterm + $final) / 3;
        $status = ($average <= 3.0) ? 'Passed' : 'Failed';

        $sql = "INSERT INTO student_grades (student_id, subject_code, subject_name, subject_type, credits, prelim_grade, midterm_grade, final_grade, average_grade, status, semester, year_level) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '2nd Semester 2025-2026', '2nd Year')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isssidddds", $student_id, $sub['code'], $sub['name'], $sub['type'], $sub['credits'], $prelim, $midterm, $final, $average, $status);
        $stmt->execute();
    }
}
echo "Dummy grades inserted.";
?>
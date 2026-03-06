<?php
include('/Applications/XAMPP/xamppfiles/htdocs/Ascend/config/db.php');

$sql = "CREATE TABLE IF NOT EXISTS student_grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    subject_code VARCHAR(50),
    subject_name VARCHAR(255),
    subject_type VARCHAR(50),
    credits INT,
    prelim_grade DECIMAL(5,2),
    midterm_grade DECIMAL(5,2),
    final_grade DECIMAL(5,2),
    average_grade DECIMAL(5,2),
    status VARCHAR(50),
    semester VARCHAR(100),
    year_level VARCHAR(50)
)";

if ($conn->query($sql)) {
    echo "Table created or already exists.\n";
} else {
    echo "Error: " . $conn->error . "\n";
}
?>
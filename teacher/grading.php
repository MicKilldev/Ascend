<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: ../login.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_grade'])) {
    $student_id = $_POST['student_id'];
    $subject_code = htmlspecialchars($_POST['subject_code']);
    $subject_name = htmlspecialchars($_POST['subject_name']);
    $credits = (int)$_POST['credits'];
    $prelim = (float)$_POST['prelim'];
    $midterm = (float)$_POST['midterm'];
    $final = (float)$_POST['final'];
    $semester = htmlspecialchars($_POST['semester']);

    // Calculation (Adjust the formula if your school weights them differently)
    $average = ($prelim + $midterm + $final) / 3;
    
    // Status Logic (Assuming 3.0 is the passing threshold for PH grading systems)
    $status = ($average <= 3.0) ? 'Passed' : 'Failed';

    
    $sql = "INSERT INTO student_grades (student_id, subject_code, subject_name, subject_type, credits, prelim_grade, midterm_grade, final_grade, average_grade, status, semester) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
  $stmt = $conn->prepare($sql);
    // "issiddddss" -> i=int, s=string, d=double/float
   $stmt->bind_param("isssiddddss", $student_id, $subject_code, $subject_name, $subject_type, $credits, $prelim, $midterm, $final, $average, $status, $semester);

    if ($stmt->execute()) {
        $formatted_avg = number_format($average, 2);
        $message = "<div class='alert success'>Grade posted successfully! Average: $formatted_avg</div>";
    } else {
        $message = "<div class='alert error'>Error: " . $conn->error . "</div>";
    }
}

$students = $conn->query("SELECT id, username FROM users WHERE role = 'student'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher | Grading Portal</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; background: #e8ecf0; padding: 40px; color: #1a1a2e; display: flex; flex-direction: column; align-items: center; }
        .container { width: 100%; max-width: 600px; background: white; padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        h2 { display: flex; align-items: center; gap: 10px; margin-top: 0; color: #0f1624; }
        .form-group { margin-bottom: 15px; }
        label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 5px; opacity: 0.8; }
        input, select { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: inherit; box-sizing: border-box; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px; }
        .grid-3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; margin-bottom: 15px; }
        .button { width: 100%; background: #00d2ff; border: none; padding: 15px; border-radius: 8px; color: #0f1624; font-weight: 700; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .button:hover { background: #00b8e6; }
        .alert { padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; }
        .success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
        .error { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
        .back-link { margin-top: 20px; color: #6e45e2; text-decoration: none; font-size: 0.9rem; font-weight: 600; }
    </style>
</head>
<body>

<div class="container">
    <h2><i class="ph-bold ph-pencil-line"></i> Grade Computation</h2>
    <?php echo $message; ?>

    <form method="POST">
        <div class="form-group">
            <label>Select Student</label>
            <select name="student_id" required>
                <option value="">-- Select Student --</option>
                <?php while($row = $students->fetch_assoc()): ?>
                    <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['username']); ?></option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label>Subject Code</label>
                <input type="text" name="subject_code" placeholder="e.g. IT101" required>
            </div>
            <div class="form-group">
                <label>Units / Credits</label>
                <input type="number" name="credits" value="3" required>
            </div>
        </div>

        <div class="form-group">
            <label>Subject Name</label>
            <input type="text" name="subject_name" placeholder="e.g. Web Development 2" required>
        </div>

        <div class="form-group">
            <label>Semester</label>
            <select name="semester" required>
                <option value="1st Semester 2025-2026">1st Semester 2025-2026</option>
                <option value="2nd Semester 2025-2026">2nd Semester 2025-2026</option>
            </select>
        </div>

        <div class="grid-2">
    <div class="form-group">
        <label>Subject Type</label>
        <select name="subject_type" required>
            <option value="Major">Major Subject</option>
            <option value="GE">General Education (GE)</option>
        </select>
    </div>
    <div class="form-group">
        <label>Units / Credits</label>
        <input type="number" name="credits" value="3" required>
    </div>
</div>

        <div class="grid-3">
            <div class="form-group">
                <label>Prelim</label>
                <input type="number" step="0.01" name="prelim" placeholder="1.0-5.0" required>
            </div>
            <div class="form-group">
                <label>Midterm</label>
                <input type="number" step="0.01" name="midterm" placeholder="1.0-5.0" required>
            </div>
            <div class="form-group">
                <label>Final</label>
                <input type="number" step="0.01" name="final" placeholder="1.0-5.0" required>
            </div>
        </div>

        <button type="submit" name="submit_grade" class="button">
            Compute & Post Grade
        </button>
    </form>
</div>

<a href="dashboard.php" class="back-link">
    ← Back to Teacher Dashboard
</a>

</body>
</html>
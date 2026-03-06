<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php?role=student");
    exit();
}
$username = htmlspecialchars($_SESSION['username'] ?? 'Student');
$course = htmlspecialchars($_SESSION['course'] ?? 'BSIT');
$student_id = $_SESSION['id'];

// Calculate actual GPA
$gpa_query = $conn->prepare("SELECT average_grade FROM student_grades WHERE student_id = ?");
$gpa_query->bind_param("i", $student_id);
$gpa_query->execute();
$gpa_res = $gpa_query->get_result();
$gpa_sum = 0;
$gpa_count = 0;
while ($row = $gpa_res->fetch_assoc()) {
    $gpa_sum += $row['average_grade'];
    $gpa_count++;
}
$student_gpa = $gpa_count > 0 ? number_format($gpa_sum / $gpa_count, 2) : "N/A";


$clearance = [
    "Tuition fully paid" => true,
    "All quizzes have been taken" => true,
    "All exams have been taken" => true,
    "Complete notes" => false
];

$is_cleared = !in_array(false, $clearance);
?>

<?php include("../includes/header.php"); ?>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>

    <div class="main-content">
        <h1>Student Dashboard</h1>
        <p>Welcome, <?php echo $username; ?> 👋</p>

        <div class="card-container">
            <div class="card" style="background: linear-gradient(135deg, #45b6e8 0%, #1a7bc4 100%); color: white;">
                <h3 style="color: rgba(255,255,255,0.7);">Current GPA</h3>
                <h1 style="color: white;">
                    <?php echo $student_gpa; ?>
                </h1>
                <div class="card-footer" style="color: rgba(255,255,255,0.9); font-weight: 600;">94% Excellent Standing
                </div>
            </div>

            <div class="card">
                <h3>Academic Progress</h3>
                <div style="margin-top: 10px;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 5px;">
                        <span>Major Subjects</span>
                        <span style="font-weight: 700;">80%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width: 80%;"></div>
                    </div>
                </div>
                <div style="margin-top: 15px;">
                    <div style="display: flex; justify-content: space-between; font-size: 0.8rem; margin-bottom: 5px;">
                        <span>GE Subjects</span>
                        <span style="font-weight: 700;">49%</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar" style="width: 49%; background: #a855f7;"></div>
                    </div>
                </div>
            </div>

            <div class="card">
                <h3>Dean's List Status</h3>
                <div style="display: flex; align-items: center; gap: 15px; margin-top: 10px;">
                    <span style="font-size: 2.5rem;">⭐️</span>
                    <div>
                        <div style="font-size: 1.5rem; font-weight: 800;">1.50</div>
                        <div style="font-size: 0.8rem; color: #64748b; font-weight: 600;">Target Qualification</div>
                    </div>
                </div>
            </div>
        </div>

        <h2
            style="margin-top: 40px; margin-bottom: 20px; font-weight: 800; font-size: 1.2rem; color: #0f172a; display: flex; align-items: center; gap: 10px;">
            <i class="ph ph-medal" style="color: #6e45e2;"></i> Earned Badges
        </h2>

        <div class="card-container">
            <div class="card" style="padding: 20px; text-align: center;">
                <img src="../assets/images/badge_web_dev.png" style="width: 80px; margin-bottom: 10px;" alt="Badge">
                <div style="font-weight: 700; font-size: 0.9rem;">Web Development</div>
            </div>
            <div class="card" style="padding: 20px; text-align: center;">
                <img src="../assets/images/badge_attendance.png" style="width: 80px; margin-bottom: 10px;" alt="Badge">
                <div style="font-weight: 700; font-size: 0.9rem;">Perfect Attendance</div>
            </div>
            <div class="card" style="padding: 20px; text-align: center;">
                <img src="../assets/images/badge_uiux.png" style="width: 80px; margin-bottom: 10px;" alt="Badge">
                <div style="font-weight: 700; font-size: 0.9rem;">UI/UX Fundamentals</div>
            </div>
        </div>

        <h2
            style="margin-top: 40px; margin-bottom: 20px; font-weight: 800; font-size: 1.2rem; color: #0f172a; display: flex; align-items: center; gap: 10px;">
            <i class="ph ph-lock-key" style="color: <?php echo $is_cleared ? '#10b981' : '#f59e0b'; ?>;"></i> Enrollment
            Clearance
        </h2>

        <div class="card"
            style="width: 100%; padding: 25px; border-top: 4px solid <?php echo $is_cleared ? '#10b981' : '#f59e0b'; ?>;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 20px;">
                <div>
                    <h3 style="margin: 0; font-size: 1.1rem;">Academic Year Clearance</h3>
                    <p style="color: #64748b; font-size: 0.85rem; margin-top: 5px;">
                        <?php echo $is_cleared ? "Congratulations! You have met all requirements." : "You must complete all requirements below before you can enroll."; ?>
                    </p>
                </div>
                <div
                    style="padding: 5px 12px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; background: <?php echo $is_cleared ? '#dcfce7' : '#fef3c7'; ?>; color: <?php echo $is_cleared ? '#166534' : '#92400e'; ?>;">
                    <?php echo $is_cleared ? "READY TO ENROLL" : "ACTION REQUIRED"; ?>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 15px;">
                <?php foreach ($clearance as $task => $status): ?>
                    <div
                        style="display: flex; align-items: center; gap: 12px; padding: 10px; background: #f8fafc; border-radius: 8px;">
                        <span style="font-size: 1.2rem;"><?php echo $status ? '✅' : '❌'; ?></span>
                        <span
                            style="font-size: 0.9rem; font-weight: 500; color: <?php echo $status ? '#1e293b' : '#be123c'; ?>;">
                            <?php echo $task; ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div style="margin-top: 25px; text-align: right;">
                <button <?php echo !$is_cleared ? 'disabled' : ''; ?> style="padding: 12px 24px; border-radius: 8px; border: none; font-weight: 700; transition: 0.3s;
                        background-color: <?php echo $is_cleared ? '#6e45e2' : '#cbd5e1'; ?>; 
                        color: <?php echo $is_cleared ? 'white' : '#94a3b8'; ?>;
                        cursor: <?php echo $is_cleared ? 'pointer' : 'not-allowed'; ?>;">
                    Proceed to Enrollment
                </button>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
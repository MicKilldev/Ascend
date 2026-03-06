<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'parent') {
    header("Location: ../login.php?role=parent");
    exit();
}

$parent_user_id = $_SESSION['id'];

// Initializing the connected student
$stmt = $conn->prepare("SELECT u.id as u_id, s.user_id as target_student_id, u.username as student_name, u.course 
                        FROM parents p
                        JOIN users u ON p.student_id = u.id
                        LEFT JOIN students s ON u.id = s.user_id
                        WHERE p.user_id = ?");
$stmt->bind_param("i", $parent_user_id);
$stmt->execute();
$res = $stmt->get_result();
$student = $res->fetch_assoc();

if (!$student) {
    // Fallback for demo
    $student_name = "Mickael Garde";
    $student_id = 1;
    $course = "Information Technology";
} else {
    $student_name = $student['student_name'];
    $student_id = $student['target_student_id'] ?? $student['u_id']; // Prefer student user_id, fallback to internal id
    $course = $student['course'];
}

// Balance data (Mocked but connected to student)
$total_assessed = 52000;
$total_paid = 30000;
$outstanding = $total_assessed - $total_paid;

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
$parent_child_gpa = $gpa_count > 0 ? number_format($gpa_sum / $gpa_count, 2) : "Enrolled";
?>

<?php include("../includes/header.php"); ?>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>

    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
            <div>
                <h1>Parent Dashboard</h1>
                <p>Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> 👋 tracking
                    <strong><?php echo $student_name; ?>'s</strong> progress.
                </p>
            </div>
        </div>

        <h3 style="margin-bottom: 15px; font-size: 1.1rem; color: #0f172a;"><i class="ph ph-graduation-cap"
                style="color: var(--primary);"></i> Academic Overview</h3>
        <div class="card-container" style="margin-bottom: 30px;">
            <div class="card">
                <h3>Child's GPA</h3>
                <h1><?php echo $parent_child_gpa; ?></h1>
                <div class="card-footer" style="color: #10b981; font-weight: 600;">✓ Good Standing</div>
            </div>
            <div class="card">
                <h3>Attendance Rate</h3>
                <h1>98.5%</h1>
                <div class="card-footer">Perfect record this month</div>
            </div>
            <div class="card" style="border-left: 4px solid #f59e0b;">
                <h3>Program</h3>
                <h1 style="font-size: 1.5rem; margin-top: 10px;"><?php echo $course; ?></h1>
                <div class="card-footer">Student ID: 24-1133-954</div>
            </div>
        </div>

        <h3 style="margin-bottom: 15px; font-size: 1.1rem; color: #0f172a;"><i class="ph ph-wallet"
                style="color: #ef4444;"></i> Financial Oversight</h3>
        <div class="card-container" style="margin-bottom: 30px;">
            <a href="ledger.php" class="card"
                style="border-left: 4px solid #ef4444; position: relative; text-decoration: none;">
                <div style="position: absolute; top: 15px; right: 15px; color: #ef4444; font-size: 1.5rem;"><i
                        class="ph ph-arrow-circle-right"></i></div>
                <h3 style="color: #64748b;">Outstanding Balance</h3>
                <h1 style="color: #ef4444; margin-bottom: 5px;">₱<?php echo number_format($outstanding, 2); ?></h1>
                <div style="font-size: 0.8rem; font-weight: 600; color: #94a3b8;">Click to view ledger</div>
            </a>

            <div class="card" style="border-left: 4px solid #d97706; position: relative;">
                <h3 style="color: #64748b;">Next Milestone</h3>
                <h1 style="color: #0f172a; margin-bottom: 5px;">Final Exams</h1>
                <div style="font-size: 0.8rem; font-weight: 600; color: #ef4444;">Settlement required before May 20
                </div>
            </div>
        </div>

        <h3 style="margin-bottom: 15px; font-size: 1.1rem; color: #0f172a;"><i class="ph ph-calendar-check"
                style="color: #8b5cf6;"></i> School Calendar & Events</h3>
        <div class="card-container">
            <div class="card" style="border-left: 4px solid #8b5cf6;">
                <h3 style="color: #64748b;">Career Guidance Day</h3>
                <h1 style="font-size: 1.3rem; margin-top: 10px; color: #1e293b;">March 15, 2026</h1>
                <div class="card-footer">All graduating students</div>
            </div>

            <div class="card" style="border-left: 4px solid #06b6d4;">
                <h3 style="color: #64748b;">PE Day / Sports Fest</h3>
                <h1 style="font-size: 1.3rem; margin-top: 10px; color: #1e293b;">March 22, 2026</h1>
                <div class="card-footer">Wear assigned team colors</div>
            </div>
        </div>

        <div
            style="margin-top: 25px; padding: 15px; background-color: #fef2f2; border: 1px solid #fee2e2; border-radius: 12px; display: flex; align-items: center; gap: 15px;">
            <div style="background: #fee2e2; padding: 10px; border-radius: 50%;">
                <i class="ph ph-megaphone" style="color: #ef4444; font-size: 1.2rem; display: block;"></i>
            </div>
            <p style="margin: 0; color: #991b1b; font-size: 0.85rem; line-height: 1.4;">
                <strong>Notice:</strong> Sudden class suspensions (weather/emergencies) are announced directly in the
                <strong>students' official Group Chats</strong>. Please coordinate with your child for real-time
                updates.
            </p>
        </div>

    </div>
</div>

<?php include("../includes/footer.php"); ?>
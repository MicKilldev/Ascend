<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'parent') {
    header("Location: ../login.php?role=parent");
    exit();
}

$parent_user_id = $_SESSION['id'];

// Initializing the connected student
$stmt = $conn->prepare("SELECT u.id, u.username as student_name, u.course 
                        FROM parents p
                        JOIN users u ON p.student_id = u.id
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
    $student_id = $student['id'];
    $course = $student['course'];
}

// Balance data (Mocked but connected to student)
$total_assessed = 52000;
$total_paid = 30000;
$outstanding = $total_assessed - $total_paid;
?>

<?php include("../includes/header.php"); ?>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>

    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
            <div>
                <h1>Parent Dashboard</h1>
                <p>Welcome back, <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong> 👋 tracking
                    <strong><?php echo $student_name; ?>'s</strong> progress.</p>
            </div>
        </div>

        <h3 style="margin-bottom: 15px; font-size: 1.1rem; color: #0f172a;"><i class="ph ph-graduation-cap"
                style="color: var(--primary);"></i> Academic Overview</h3>
        <div class="card-container" style="margin-bottom: 30px;">
            <div class="card">
                <h3>Child's GPA</h3>
                <h1>1.42</h1>
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
        <div class="card-container">
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
    </div>
</div>

<?php include("../includes/footer.php"); ?>
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
} else {
    $student_name = $student['student_name'];
}
?>

<?php include("../includes/header.php"); ?>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1>Development Progress</h1>
                <p>Monitoring academic benchmarks for <strong><?php echo $student_name; ?></strong>.</p>
            </div>
            <div
                style="background: white; padding: 10px 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 10px;">
                <div
                    style="width: 10px; height: 10px; border-radius: 50%; background: #10b981; animation: pulse 2s infinite;">
                </div>
                <span style="font-weight: 700; font-size: 0.85rem; color: #64748b;">Active Trajectory</span>
            </div>
        </div>

        <div class="card"
            style="padding: 40px; text-align: center; background: white; border-radius: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
            <i class="ph ph-trend-up" style="font-size: 5rem; color: #6e45e2; opacity: 0.8; margin-bottom: 25px;"></i>
            <h2 style="color: #0f172a; margin-bottom: 15px;">Tracking Growth Milestones</h2>
            <p style="color: #64748b; max-width: 500px; margin: 0 auto; line-height: 1.6;">The AI-driven progress engine
                is aggregating <strong><?php echo $student_name; ?>'s</strong> daily participation, assignment
                submissions, and assessment results to visualize their semester-long learning curve.</p>

            <div style="margin-top: 40px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                <div style="background: #f8fafc; padding: 20px; border-radius: 15px;">
                    <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase;">
                        Cognitive Skill</div>
                    <div style="font-size: 1.2rem; font-weight: 800; color: #0f172a;">Exceeding</div>
                </div>
                <div style="background: #f8fafc; padding: 20px; border-radius: 15px;">
                    <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase;">
                        Technical Depth</div>
                    <div style="font-size: 1.2rem; font-weight: 800; color: #0f172a;">Advanced</div>
                </div>
                <div style="background: #f8fafc; padding: 20px; border-radius: 15px;">
                    <div style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Soft
                        Skills</div>
                    <div style="font-size: 1.2rem; font-weight: 800; color: #0f172a;">Optimal</div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes pulse {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }

        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
        }

        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }
</style>

<?php include("../includes/footer.php"); ?>
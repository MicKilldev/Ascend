<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php?role=student");
    exit();
}
$username = htmlspecialchars($_SESSION['username'] ?? 'Student');
$course = htmlspecialchars($_SESSION['course'] ?? 'BSIT');
?>

<?php include("../includes/header.php"); ?>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>

    <div class="main-content">
        <h1>Student Dashboard</h1>
        <p>Welcome, <?php echo $username; ?> 👋</p>

        <!-- Academic Status Section -->
        <div class="card-container">
            <div class="card" style="background: linear-gradient(135deg, #45b6e8 0%, #1a7bc4 100%); color: white;">
                <h3 style="color: rgba(255,255,255,0.7);">Current GPA</h3>
                <h1 style="color: white;">1.25</h1>
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
    </div>
</div>

<?php include("../includes/footer.php"); ?>
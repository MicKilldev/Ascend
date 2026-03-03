<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'parent') {
    header("Location: ../login.php?role=parent");
    exit();
}
?>

<?php include("../includes/header.php"); ?>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>

    <div class="main-content">
        <h1>Parent Dashboard</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</p>

        <div class="card-container">
            <div class="card">
                <h3>Child's GPA</h3>
                <h1>1.50</h1>
                <div class="card-footer" style="color: #10b981; font-weight: 600;">✓ Good Standing</div>
            </div>
            <div class="card">
                <h3>Attendance Rate</h3>
                <h1>94%</h1>
                <div class="card-footer">This semester</div>
            </div>
            <div class="card">
                <h3>Recent Achievement</h3>
                <div class="badge-item">
                    <span style="font-size: 1.5rem;">🏅</span>
                    <div>
                        <div style="font-weight: 700; font-size: 0.9rem;">Academic Excellence</div>
                        <div style="font-size: 0.75rem; color: #94a3b8;">Awarded Feb 15, 2026</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
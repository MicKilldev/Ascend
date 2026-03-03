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
                <p>Good Standing</p>
            </div>
            <div class="card">
                <h3>Attendance Rate</h3>
                <h1>94%</h1>
                <p>This semester</p>
            </div>
            <div class="card">
                <h3>Recent Achievement</h3>
                <p>🏅 Academic Excellence Badge</p>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
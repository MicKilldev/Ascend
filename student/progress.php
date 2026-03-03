<?php
session_start();
include('../config/db.php');
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php?role=student");
    exit();
}
?>
<?php include("../includes/header.php"); ?>
<div class="container">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <h1>Progress Tracker</h1>
        <p>Visualizing your journey towards graduation and industry readiness.</p>
        <div class="card"
            style="margin-top: 30px; border-style: dashed; text-align: center; color: #94a3b8; padding: 80px;">
            <i class="ph ph-trend-up" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;"></i>
            <h3 style="color: #64748b;">Mapping Curriculum Path...</h3>
            <p style="font-size: 0.85rem; margin-top: 10px;">The predictive graduation engine is recalculating your path
                based on recent GE subject credits.</p>
        </div>
    </div>
</div>
<?php include("../includes/footer.php"); ?>
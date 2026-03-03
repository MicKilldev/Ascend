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
        <h1>Academic Grades</h1>
        <p>Your performance record is being synchronized with the registrar's portal.</p>
        <div class="card"
            style="margin-top: 30px; border-style: dashed; text-align: center; color: #94a3b8; padding: 80px;">
            <i class="ph ph-chart-bar" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;"></i>
            <h3 style="color: #64748b;">Gradebook Syncing...</h3>
            <p style="font-size: 0.85rem; margin-top: 10px;">The official academic transcript will be available once the
                term evaluation is finalized.</p>
        </div>
    </div>
</div>
<?php include("../includes/footer.php"); ?>
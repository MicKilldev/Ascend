<?php
session_start();
include('../config/db.php');
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['teacher', 'faculty', 'school_admin'])) {
    header("Location: ../login.php?role=faculty");
    exit();
}
?>
<?php include("../includes/header.php"); ?>
<div class="container">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <h1>Institutional Gradebook</h1>
        <p>Advanced evaluation and grade distribution management system.</p>
        <div class="card"
            style="margin-top: 30px; border-style: dashed; text-align: center; color: #94a3b8; padding: 80px;">
            <i class="ph ph-chart-bar" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;"></i>
            <h3 style="color: #64748b;">Processing Academic Records</h3>
            <p style="font-size: 0.85rem; margin-top: 10px;">The evaluation module is synchronizing with student
                semester results for department-wide validation.</p>
        </div>
    </div>
</div>
<?php include("../includes/footer.php"); ?>
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
        <h1>Student Portfolios</h1>
        <p>Review and validate digital achievement collections from your assigned sections.</p>
        <div class="card"
            style="margin-top: 30px; border-style: dashed; text-align: center; color: #94a3b8; padding: 80px;">
            <i class="ph ph-folder-open" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;"></i>
            <h3 style="color: #64748b;">Curating Project Submissions</h3>
            <p style="font-size: 0.85rem; margin-top: 10px;">Portfolio validations are currently being categorized by
                faculty discipline focus.</p>
        </div>
    </div>
</div>
<?php include("../includes/footer.php"); ?>
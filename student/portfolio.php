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
        <h1>Student Portfolio</h1>
        <p>Curate and showcase your institutional achievements and projects.</p>
        <div class="card"
            style="margin-top: 30px; border-style: dashed; text-align: center; color: #94a3b8; padding: 80px;">
            <i class="ph ph-briefcase" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;"></i>
            <h3 style="color: #64748b;">Portfolio Engine Initializing</h3>
            <p style="font-size: 0.85rem; margin-top: 10px;">Select your best projects to showcase to verified hiring
                partners in the Ascend ecosystem.</p>
        </div>
    </div>
</div>
<?php include("../includes/footer.php"); ?>
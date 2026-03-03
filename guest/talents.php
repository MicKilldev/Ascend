<?php
session_start();
include('../config/db.php');
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guest') {
    header("Location: ../login.php?role=guest");
    exit();
}
?>
<?php include("../includes/header.php"); ?>
<div class="container">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <h1>Talent Scout Directory</h1>
        <p>Discover top-performing students verified by faculty evaluations.</p>
        <div class="card"
            style="margin-top: 30px; border-style: dashed; text-align: center; color: #94a3b8; padding: 80px;">
            <i class="ph ph-users-three" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;"></i>
            <h3 style="color: #64748b;">Curating Elite Talent Pool</h3>
            <p style="font-size: 0.85rem; margin-top: 10px;">The Ascend talent engine is filtering graduates with
                industry-specific micro-credentials and high GPA stability.</p>
        </div>
    </div>
</div>
<?php include("../includes/footer.php"); ?>
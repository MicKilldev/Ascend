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
        <h1>Development Progress</h1>
        <p>Monitoring academic benchmarks and behavioral success indicators.</p>
        <div class="card"
            style="margin-top: 30px; border-style: dashed; text-align: center; color: #94a3b8; padding: 80px;">
            <i class="ph ph-trend-up" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;"></i>
            <h3 style="color: #64748b;">Mapping Learning Trajectory...</h3>
            <p style="font-size: 0.85rem; margin-top: 10px;">The Ascend predictive engine is currently visualizing the
                semester-over-semester growth metrics.</p>
        </div>
    </div>
</div>
<?php include("../includes/footer.php"); ?>
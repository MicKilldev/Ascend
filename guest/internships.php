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
        <h1>Internship Placements</h1>
        <p>Manage and track industry-academic immersion programs.</p>
        <div class="card"
            style="margin-top: 30px; border-style: dashed; text-align: center; color: #94a3b8; padding: 80px;">
            <i class="ph ph-briefcase" style="font-size: 4rem; margin-bottom: 20px; opacity: 0.3;"></i>
            <h3 style="color: #64748b;">Mapping Placement Slots</h3>
            <p style="font-size: 0.85rem; margin-top: 10px;">Institutional partnership modules are currently being
                synchronized with the academic calendar.</p>
        </div>
    </div>
</div>
<?php include("../includes/footer.php"); ?>
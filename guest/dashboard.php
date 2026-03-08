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
        <h1>Hiring & Internship Portal</h1>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</p>

        <div class="card-container">
            <div class="card">
                <h3>Verified Graduates</h3>
                <h1>348</h1>
                <div class="card-footer" style="color: #00d2ff; font-weight: 600;">Available for hire</div>
            </div>
            <div class="card">
                <h3>Internship Slots</h3>
                <h1>12</h1>
                <div class="card-footer">Open positions this term</div>
            </div>
            <div class="card">
                <h3>Skills Match</h3>
                <h1>94%</h1>
                <div class="card-footer" style="color: #a855f7; font-weight: 600;">Industry alignment rate</div>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>

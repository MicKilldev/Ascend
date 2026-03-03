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
        <p>Welcome,
            <?php echo htmlspecialchars($_SESSION['username']); ?> 👋
        </p>

        <div class="card-container">
            <div class="card">
                <h3>Verified Graduates</h3>
                <h1>348</h1>
                <p>Available for hire</p>
            </div>
            <div class="card">
                <h3>Internship Slots</h3>
                <h1>12</h1>
                <p>Open positions this term</p>
            </div>
            <div class="card">
                <h3>Skills Match</h3>
                <h1>94%</h1>
                <p>Industry alignment rate</p>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
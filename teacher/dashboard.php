<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || ($_SESSION['role'] !== 'teacher' && $_SESSION['role'] !== 'faculty' && $_SESSION['role'] !== 'school_admin')) {
    header("Location: ../login.php?role=faculty");
    exit();
}
?>

<?php include("../includes/header.php"); ?>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>

    <div class="main-content">
        <h1>Teacher / Admin Dashboard</h1>
        <p>Welcome,
            <?php echo htmlspecialchars($_SESSION['username']); ?> 👋
        </p>

        <div class="card-container">
            <div class="card">
                <h3>Total Students</h3>
                <h1>142</h1>
                <p>Enrolled this semester</p>
            </div>
            <div class="card">
                <h3>At-Risk Students</h3>
                <h1>7</h1>
                <p>Flagged by EWS</p>
            </div>
            <div class="card">
                <h3>Dean's List</h3>
                <h1>23</h1>
                <p>Students this term</p>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
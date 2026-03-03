<?php
session_start();
include("../config/db.php");

if ($_SESSION['role'] != "student") {
    header("Location: ../login.php");
}
?>

<?php include("../includes/header.php"); ?>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>

    <div class="main-content">
        <h1>Dashboard</h1>
        <p>Welcome, Student 👋</p>

        <div class="card-container">

            <div class="card">
                <h3>GPA</h3>
                <h1>1.25</h1>
                <p>Excellent</p>
            </div>

            <div class="card">
                <h3>Academic Progress</h3>
                <p>GE Subjects: 49%</p>
                <div class="progress">
                    <div class="progress-bar" style="width:49%"></div>
                </div>

                <p>Major Subjects: 80%</p>
                <div class="progress">
                    <div class="progress-bar" style="width:80%"></div>
                </div>
            </div>

            <div class="card">
                <h3>Dean's List ⭐</h3>
                <p>Congratulations!</p>
            </div>

        </div>

    </div>
</div>

<?php include("../includes/footer.php"); ?>
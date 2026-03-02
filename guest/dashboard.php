if ($_SESSION['role'] != "guest") {
    header("Location: ../login.php");
}
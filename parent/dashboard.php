if ($_SESSION['role'] != "parent") {
    header("Location: ../login.php");
}
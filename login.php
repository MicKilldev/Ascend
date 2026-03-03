<?php
session_start();
include('config/db.php');

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];

        // Role-Based Routing Logic
        if ($user['role'] == 'school_admin') {
            header("Location: admin/school_dashboard.php");
        } elseif ($user['role'] == 'student_admin') {
            header("Location: admin/student_parent_dashboard.php");
        } elseif ($user['role'] == 'guest_admin') {
            header("Location: admin/business_dashboard.php");
        } else {
            header("Location: " . $user['role'] . "/dashboard.php");
        }
    } else {
        echo "Invalid Login Credentials";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ascend - Login</title>
    <style>
        body { font-family: sans-serif; display: flex; justify-content: center; padding-top: 50px; background: #f4f4f4; }
        .login-box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px #ccc; }
        input { display: block; width: 100%; margin-bottom: 10px; padding: 8px; }
        button { width: 100%; background: #2ecc71; color: white; border: none; padding: 10px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Ascend Login</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Enter Launchpad</button>
        </form>
    </div>
</body>
</html>
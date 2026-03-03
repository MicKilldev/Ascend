<?php
session_start();
include('config/db.php');

// Get selected role for visual feedback
$selectedRole = isset($_GET['role']) ? $_GET['role'] : 'general';
$portalTitles = [
    'faculty' => 'Teachers & Admin',
    'student' => 'Student Portal',
    'parent' => 'Parent Portal',
    'guest' => 'Hiring & Internships',
    'general' => 'Student Success Launchpad'
];
$displayTitle = isset($portalTitles[$selectedRole]) ? $portalTitles[$selectedRole] : 'Student Success Launchpad';

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Note: In production use password_verify() and prepared statements!
    $query = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['role'] = $user['role'];
        $_SESSION['username'] = $user['username'];

        // Role-Based Routing
        if ($user['role'] == 'school_admin' || $user['role'] == 'faculty') {
            header("Location: teacher/dashboard.php");
        } elseif ($user['role'] == 'student') {
            header("Location: student/dashboard.php");
        } elseif ($user['role'] == 'parent') {
            header("Location: parent/dashboard.php");
        } elseif ($user['role'] == 'guest') {
            header("Location: guest/dashboard.php");
        } else {
            header("Location: index.php"); // Fallback
        }
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ascend | <?php echo $displayTitle; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        :root {
            --primary: #00d2ff;
            --secondary: #92fe9d;
            --accent: #6e45e2;
            --dark: #0f172a;
            --light: #f8fafc;
            --glass: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.15);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--dark);
            background-image: linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.7)), url('assets/images/login-bg.png');
            background-size: cover;
            background-position: center;
            overflow: hidden;
            color: var(--light);
        }

        .container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card {
            background: var(--glass);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid var(--glass-border);
            border-radius: 32px;
            padding: 45px 35px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            text-align: center;
            position: relative;
        }

        .back-btn {
            position: absolute;
            top: 25px;
            left: 25px;
            color: rgba(255, 255, 255, 0.5);
            text-decoration: none;
            font-size: 1.2rem;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .back-btn:hover {
            color: var(--primary);
            transform: translateX(-3px);
        }

        .logo-section h1 {
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -1px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .logo-section p {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: 35px;
            font-weight: 400;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .portal-badge {
            display: inline-block;
            padding: 6px 16px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 25px;
            border: 1px solid rgba(0, 210, 255, 0.2);
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
            text-align: left;
        }

        .input-group label {
            display: block;
            font-size: 0.85rem;
            margin-bottom: 8px;
            margin-left: 5px;
            color: rgba(255, 255, 255, 0.7);
        }

        .input-group input {
            width: 100%;
            padding: 14px 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            color: white;
            font-size: 1rem;
            transition: all 0.3s ease;
            outline: none;
        }

        .input-group input:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(0, 210, 255, 0.15);
        }

        .login-btn {
            width: 100%;
            padding: 16px;
            margin-top: 10px;
            border: none;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary), #00a8ff);
            color: var(--dark);
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 20px -5px rgba(0, 210, 255, 0.4);
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(0, 210, 255, 0.5);
            filter: brightness(1.1);
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: #f87171;
            padding: 14px;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .footer-links {
            margin-top: 30px;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.4);
        }

        .footer-links a {
            color: var(--primary);
            text-decoration: none;
        }

        @media (max-width: 480px) {
            .login-card { padding: 35px 25px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card">
            <a href="index.php" class="back-btn" title="Back to selection">
                <i class="ph ph-arrow-left"></i>
            </a>

            <div class="logo-section">
                <h1>ASCEND</h1>
                <div class="portal-badge"><?php echo $displayTitle; ?></div>
            </div>

            <?php if (isset($_POST['login']) && mysqli_num_rows($result) == 0): ?>
                <div class="error-msg">
                    <i class="ph ph-warning-circle"></i>
                    Invalid credentials. Please try again.
                </div>
            <?php endif; ?>

            <form method="POST">
                <div class="input-group">
                    <label>Username</label>
                    <input type="text" name="username" placeholder="Enter your username" required>
                </div>
                
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <button type="submit" name="login" class="login-btn">Secure Login</button>
            </form>

            <div class="footer-links">
                <p>Authentication required for ecosystem access.</p>
                <p style="margin-top: 10px; font-size: 0.75rem;">&copy; 2026 Ascend Intelligence</p>
            </div>
        </div>
    </div>
</body>
</html>
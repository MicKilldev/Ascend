<?php
session_start();
include('config/db.php');

// Get selected role for visual feedback
$selectedRole = isset($_GET['role']) ? $_GET['role'] : 'general';
$portalTitles = [
    'faculty' => 'Teachers & Admin Portal',
    'student' => 'Student Portal',
    'parent' => 'Parent Portal',
    'general' => 'Student Success Launchpad'
];
$displayTitle = $portalTitles[$selectedRole] ?? 'Ascend Login';

$loginError = false;

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Secure: use prepared statement with email-based login
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Define allowed role mappings for each portal
        $allowedRoles = [
            'faculty' => ['teacher', 'faculty', 'school_admin'],
            'student' => ['student'],
            'parent' => ['parent'],
            'general' => [] // Should redirect to specific portal
        ];

        // Check if user is allowed in the selected portal
        $isAllowed = false;
        if ($selectedRole === 'general') {
            $isAllowed = true; // General login can route to any
        } elseif (isset($allowedRoles[$selectedRole]) && in_array($user['role'], $allowedRoles[$selectedRole])) {
            $isAllowed = true;
        }

        if ($isAllowed) {
            $_SESSION['role'] = $user['role'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['username'] = $user['username'] ?? $user['email'];
            $_SESSION['id'] = $user['id'];
            $_SESSION['flash_success'] = "Welcome back, " . ($_SESSION['username']) . "! High-performance session initiated.";

            // Role-Based Routing
            switch ($user['role']) {
                case 'faculty':
                case 'school_admin':
                case 'teacher':
                    header("Location: teacher/dashboard.php");
                    break;
                case 'student':
                    header("Location: student/dashboard.php");
                    break;
                case 'parent':
                    header("Location: parent/dashboard.php");
                    break;
                default:
                    header("Location: index.php");
            }
            exit();
        } else {
            $loginError = true;
            $errorMessage = "Access denied. Your account does not have permission to access the " . htmlspecialchars($displayTitle) . ".";
        }
    } else {
        $loginError = true;
        $errorMessage = "Invalid email or password. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ascend | <?php echo htmlspecialchars($displayTitle); ?></title>
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
            background-image:
                linear-gradient(rgba(15, 23, 42, 0.72), rgba(15, 23, 42, 0.72)),
                url('assets/images/login-bg.png');
            background-size: cover;
            background-position: center;
            overflow: hidden;
            color: var(--light);
        }

        .container {
            width: 100%;
            max-width: 430px;
            padding: 20px;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
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
            color: rgba(255, 255, 255, 0.45);
            text-decoration: none;
            font-size: 1.3rem;
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
            margin-bottom: 6px;
        }

        .portal-badge {
            display: inline-block;
            padding: 6px 16px;
            background: rgba(255, 255, 255, 0.08);
            border-radius: 50px;
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 28px;
            border: 1px solid rgba(0, 210, 255, 0.2);
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .input-group {
            margin-bottom: 18px;
            text-align: left;
        }

        .input-group label {
            display: block;
            font-size: 0.82rem;
            margin-bottom: 8px;
            margin-left: 4px;
            color: rgba(255, 255, 255, 0.7);
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            top: 50%;
            left: 15px;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.3);
            font-size: 1.1rem;
            pointer-events: none;
        }

        .input-group input {
            width: 100%;
            padding: 14px 20px 14px 44px;
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
            box-shadow: 0 0 0 4px rgba(0, 210, 255, 0.12);
        }

        .input-group input::placeholder {
            color: rgba(255, 255, 255, 0.25);
        }

        .login-btn {
            width: 100%;
            padding: 16px;
            margin-top: 12px;
            border: none;
            border-radius: 16px;
            background: linear-gradient(135deg, var(--primary), #00a8ff);
            color: var(--dark);
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: 0 10px 20px -5px rgba(0, 210, 255, 0.4);
            letter-spacing: 0.3px;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 25px -5px rgba(0, 210, 255, 0.55);
            filter: brightness(1.08);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .error-msg {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.25);
            color: #f87171;
            padding: 13px 16px;
            border-radius: 12px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            text-align: left;
        }

        .footer-links {
            margin-top: 28px;
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.35);
        }

        .footer-links a {
            color: var(--primary);
            text-decoration: none;
        }

        @media (max-width: 480px) {
            .login-card {
                padding: 35px 22px;
            }

            .logo-section h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="login-card">

            <a href="index.php" class="back-btn" title="Back to Portal Selection">
                <i class="ph ph-arrow-left"></i>
            </a>

            <div class="logo-section">
                <h1>ASCEND</h1>
                <div class="portal-badge"><?php echo htmlspecialchars($displayTitle); ?></div>
            </div>

            <?php if ($loginError): ?>
                <div class="error-msg">
                    <i class="ph ph-warning-circle"></i>
                    <?php echo $errorMessage ?? "Invalid email or password. Please try again."; ?>
                </div>
            <?php endif; ?>

            <form method="POST" id="loginForm">
                <div class="input-group">
                    <label for="email">Email Address</label>
                    <div class="input-wrapper">
                        <i class="ph ph-envelope input-icon"></i>
                        <input type="email" id="email" name="email" placeholder="you@institution.edu"
                            value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                            required autocomplete="email">
                    </div>
                </div>

                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="input-wrapper">
                        <i class="ph ph-lock input-icon"></i>
                        <input type="password" id="password" name="password" placeholder="••••••••" required
                            autocomplete="current-password">
                    </div>
                </div>

                <button type="submit" name="login" class="login-btn">
                    Secure Login &nbsp;<i class="ph ph-arrow-right" style="vertical-align:middle;"></i>
                </button>
            </form>

            <div class="footer-links">
                <p>Forgot password? <a href="#">Contact your Administrator</a></p>
                <p style="margin-top: 10px; font-size: 0.72rem;">&copy; 2026 Ascend Educational Ecosystem</p>
            </div>

        </div>
    </div>
</body>

</html>
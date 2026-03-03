<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ascend | Integrated Career Lifecycle Platform</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        :root {
            --primary: #00d2ff;
            --secondary: #92fe9d;
            --accent: #6e45e2;
            --dark: #070b14;
            --light: #f8fafc;
            --glass: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Outfit', sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: var(--dark);
            background-image: radial-gradient(circle at 50% 50%, rgba(0, 210, 255, 0.05) 0%, transparent 50%),
                linear-gradient(rgba(7, 11, 20, 0.8), rgba(7, 11, 20, 0.8)),
                url('assets/images/portal-bg.png');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: var(--light);
            overflow-x: hidden;
        }

        .header {
            padding: 40px 20px;
            text-align: center;
            animation: fadeInDown 0.8s ease-out;
        }

        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .header h1 {
            font-size: 3.5rem;
            font-weight: 800;
            letter-spacing: -2px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .header p {
            font-size: 1.1rem;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 300;
            max-width: 600px;
            margin: 0 auto;
        }

        .main-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
        }

        .portal-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            width: 100%;
            max-width: 1200px;
            animation: fadeInUp 1s ease-out 0.2s both;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .portal-card {
            background: var(--glass);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 40px 30px;
            text-align: center;
            text-decoration: none;
            color: var(--light);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .portal-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            opacity: 0;
            transition: opacity 0.4s;
            z-index: -1;
        }

        .portal-card:hover {
            transform: translateY(-10px) scale(1.02);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .portal-card:hover::before {
            opacity: 0.1;
        }

        .icon-box {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 25px;
            transition: all 0.4s;
            border: 1px solid var(--glass-border);
        }

        .portal-card:hover .icon-box {
            background: var(--primary);
            color: var(--dark);
            transform: rotate(10deg);
        }

        .icon-box i {
            font-size: 2.5rem;
        }

        .portal-card h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .portal-card p {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.5);
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .btn-arrow {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid var(--glass-border);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .portal-card:hover .btn-arrow {
            background: var(--light);
            color: var(--dark);
            transform: translateX(5px);
        }

        .footer {
            padding: 40px 20px;
            text-align: center;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.4);
        }

        /* Colors for different roles */
        .card-admin .icon-box {
            border-color: #6e45e2;
            color: #6e45e2;
        }

        .card-student .icon-box {
            border-color: #00d2ff;
            color: #00d2ff;
        }

        .card-parent .icon-box {
            border-color: #92fe9d;
            color: #92fe9d;
        }

        .card-guest .icon-box {
            border-color: #ff9a9e;
            color: #ff9a9e;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2.5rem;
            }

            .portal-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <h1>ASCEND</h1>
        <p>Your Gateway to the Academic Lifecycle & Career Success. Choose your portal to begin your journey.</p>
    </header>

    <main class="main-container">
        <div class="portal-grid">
            <!-- Teachers/Admin -->
            <a href="login.php?role=faculty" class="portal-card card-admin">
                <div class="icon-box">
                    <i class="ph-duotone ph-shield-check"></i>
                </div>
                <h2>Teachers & Admin</h2>
                <p>Manage curriculum, track student analytics, and oversee institutional growth.</p>
                <div class="btn-arrow">
                    <i class="ph ph-arrow-right"></i>
                </div>
            </a>

            <!-- Student -->
            <a href="login.php?role=student" class="portal-card card-student">
                <div class="icon-box">
                    <i class="ph-duotone ph-student"></i>
                </div>
                <h2>Student Portal</h2>
                <p>Access success maps, build your digital portfolio, and track your career badges.</p>
                <div class="btn-arrow">
                    <i class="ph ph-arrow-right"></i>
                </div>
            </a>

            <!-- Parents -->
            <a href="login.php?role=parent" class="portal-card card-parent">
                <div class="icon-box">
                    <i class="ph-duotone ph-users-three"></i>
                </div>
                <h2>Parent Portal</h2>
                <p>Monitor academic progress, view achievement milestones, and stay connected.</p>
                <div class="btn-arrow">
                    <i class="ph ph-arrow-right"></i>
                </div>
            </a>

            <!-- Guest/Hiring -->
            <a href="login.php?role=guest" class="portal-card card-guest">
                <div class="icon-box">
                    <i class="ph-duotone ph-briefcase"></i>
                </div>
                <h2>Hiring & Internships</h2>
                <p>Discover verified talent, explore student resumes, and bridge the industry skill gap.</p>
                <div class="btn-arrow">
                    <i class="ph ph-arrow-right"></i>
                </div>
            </a>
        </div>
    </main>

    <footer class="footer">
        <p>&copy; 2026 Ascend Educational Ecosystem. All rights reserved.</p>
        </header>
</body>

</html>
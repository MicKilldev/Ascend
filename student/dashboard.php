<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php?role=student");
    exit();
}
$username = htmlspecialchars($_SESSION['username'] ?? 'Student');
$course = htmlspecialchars($_SESSION['course'] ?? 'BSIT');

// Flash toast helper
$flash = '';
if (!empty($_SESSION['flash_success'])) {
    $flash = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ascend | Student Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        /* ===== RESET & BASE ===== */
        *,
        *::before,
        *::after {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: #e8ecf0;
            display: flex;
            min-height: 100vh;
            color: #1a1a2e;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            width: 240px;
            min-height: 100vh;
            background: #0f1624;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
        }

        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 28px 20px 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        .user-avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00d2ff, #6e45e2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .user-info {
            flex: 1;
        }

        .user-name {
            font-size: 0.9rem;
            font-weight: 600;
            color: #fff;
            line-height: 1.2;
        }

        .user-course {
            font-size: 0.72rem;
            color: rgba(255, 255, 255, 0.4);
        }

        .bell-icon {
            color: rgba(255, 255, 255, 0.4);
            font-size: 1.1rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        .bell-icon:hover {
            color: #00d2ff;
        }

        /* NAV */
        .sidebar-nav {
            flex: 1;
            padding: 18px 0;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 13px 20px;
            color: rgba(255, 255, 255, 0.45);
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            transition: all 0.2s;
            position: relative;
            border-left: 3px solid transparent;
        }

        .nav-item:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.04);
        }

        .nav-item.active {
            color: #fff;
            background: rgba(0, 210, 255, 0.08);
            border-left-color: #00d2ff;
        }

        .nav-item.active i {
            color: #00d2ff;
        }

        .nav-item i {
            font-size: 1.15rem;
        }

        /* SIDEBAR BOTTOM */
        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
        }

        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, 0.4);
            text-decoration: none;
            font-size: 0.85rem;
            padding: 8px 0;
            transition: color 0.2s;
        }

        .sidebar-footer a:hover {
            color: #fff;
        }

        .sidebar-footer a.logout:hover {
            color: #f87171;
        }

        /* ===== MAIN AREA ===== */
        .main-area {
            margin-left: 240px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* TOP BAR */
        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 24px 36px;
            background: #e8ecf0;
        }

        .topbar-title {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .topbar-accent {
            width: 4px;
            height: 40px;
            background: #00d2ff;
            border-radius: 4px;
        }

        .topbar-title h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: #0f1624;
            line-height: 1;
        }

        .topbar-title span {
            display: block;
            font-size: 0.72rem;
            font-weight: 700;
            color: #00d2ff;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .topbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 1.3rem;
            font-weight: 800;
            color: #0f1624;
            letter-spacing: -0.5px;
        }

        .topbar-brand svg {
            width: 30px;
            height: 30px;
        }

        /* PAGE CONTENT */
        .page-content {
            padding: 0 36px 36px;
        }

        /* ===== SECTION LABEL ===== */
        .section-label {
            display: inline-flex;
            align-items: center;
            background: #00d2ff;
            color: #0f1624;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 7px 20px;
            border-radius: 6px 6px 0 0;
            margin-bottom: 0;
        }

        /* ===== STATUS CARD ===== */
        .status-card {
            background: linear-gradient(135deg, #45b6e8 0%, #1a7bc4 40%, #0d5a9e 100%);
            border-radius: 0 16px 16px 16px;
            padding: 28px;
            display: flex;
            gap: 24px;
            align-items: stretch;
            margin-bottom: 36px;
            box-shadow: 0 10px 30px rgba(0, 130, 200, 0.25);
        }

        /* Academic Progress */
        .progress-section {
            flex: 1.2;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            padding: 20px 22px;
        }

        .progress-label {
            display: inline-block;
            background: rgba(255, 255, 255, 0.25);
            color: #fff;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 4px 12px;
            border-radius: 30px;
            margin-bottom: 18px;
        }

        .subject-row {
            margin-bottom: 14px;
        }

        .subject-row:last-child {
            margin-bottom: 0;
        }

        .subject-name {
            font-size: 0.72rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.75);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 6px;
        }

        .progress-track {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 30px;
            height: 10px;
            position: relative;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 30px;
            background: #1a1a2e;
            transition: width 1.2s ease;
        }

        .progress-pct {
            font-size: 0.8rem;
            font-weight: 600;
            color: #fff;
            position: absolute;
            right: 0;
            top: 50%;
            transform: translate(calc(100% + 8px), -50%);
        }

        /* GPA Section */
        .gpa-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 20px;
            text-align: center;
            border-right: 1px solid rgba(255, 255, 255, 0.2);
        }

        .gpa-label {
            font-size: 0.82rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.8);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .gpa-value {
            font-size: 3.6rem;
            font-weight: 800;
            color: #fff;
            line-height: 1;
            margin-bottom: 6px;
        }

        .gpa-sub {
            font-size: 0.82rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.7);
        }

        /* Dean's List */
        .deans-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 16px;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .deans-badge {
            text-align: center;
        }

        .deans-badge .star {
            font-size: 2rem;
        }

        .deans-number {
            font-size: 2.4rem;
            font-weight: 800;
            color: #fff;
            line-height: 1;
        }

        .deans-title {
            font-size: 1rem;
            font-weight: 800;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* At-Risk Alert */
        .at-risk-alert {
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            padding: 10px 14px;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            width: 100%;
        }

        .at-risk-icon {
            font-size: 1rem;
            color: #f59e0b;
            flex-shrink: 0;
        }

        .at-risk-title {
            font-size: 0.68rem;
            font-weight: 800;
            color: #92400e;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .at-risk-desc {
            font-size: 0.65rem;
            font-weight: 500;
            color: #78350f;
            line-height: 1.3;
        }

        /* ===== BADGE SECTION ===== */
        .badge-label {
            display: inline-flex;
            align-items: center;
            background: #0f1624;
            color: #fff;
            font-size: 0.78rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 9px 22px;
            border-radius: 30px;
            margin-bottom: 20px;
        }

        .badge-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 18px;
        }

        .badge-card {
            background: #fff;
            border-radius: 18px;
            padding: 28px 18px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 14px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }

        .badge-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, 0.12);
        }

        .badge-img {
            width: 110px;
            height: 110px;
            object-fit: contain;
        }

        .badge-name {
            font-size: 0.78rem;
            font-weight: 700;
            color: #1a1a2e;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            text-align: center;
        }

        /* ===== FLASH TOAST ===== */
        #ascend-toast {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 9999;
            background: #0f1624;
            border: 1px solid rgba(0, 210, 255, 0.3);
            border-left: 4px solid #00d2ff;
            color: #f8fafc;
            padding: 18px 20px 18px 18px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            font-size: 0.9rem;
            min-width: 300px;
            max-width: 380px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            animation: slideIn 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(30px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }

            to {
                opacity: 0;
                transform: translateX(30px);
            }
        }

        .toast-close {
            background: none;
            border: none;
            color: rgba(255, 255, 255, 0.3);
            cursor: pointer;
            font-size: 1rem;
            margin-top: 1px;
            transition: color 0.2s;
        }

        .toast-close:hover {
            color: #f87171;
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 900px) {
            .status-card {
                flex-direction: column;
            }

            .gpa-section {
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-area {
                margin-left: 0;
            }
        }
    </style>
</head>

<body>

    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar">
        <div class="sidebar-user">
            <div class="user-avatar"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
            <div class="user-info">
                <div class="user-name"><?php echo $username; ?></div>
                <div class="user-course"><?php echo $course; ?></div>
            </div>
            <i class="ph ph-bell bell-icon"></i>
        </div>

        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item active">
                <i class="ph-fill ph-squares-four"></i> Dashboard
            </a>
            <a href="grades.php" class="nav-item">
                <i class="ph ph-chart-bar"></i> Grades
            </a>
            <a href="portfolio.php" class="nav-item">
                <i class="ph ph-user-circle"></i> Portfolio
            </a>
            <a href="badges.php" class="nav-item">
                <i class="ph ph-trophy"></i> Badge
            </a>
            <a href="progress.php" class="nav-item">
                <i class="ph ph-squares-four"></i> Progress Tracker
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="settings.php">
                <i class="ph ph-gear"></i> Setting
            </a>
            <a href="../logout.php" class="logout">
                <i class="ph ph-sign-out"></i> Log Out
            </a>
        </div>
    </aside>

    <!-- ===== MAIN ===== -->
    <div class="main-area">
        <!-- Topbar -->
        <div class="topbar">
            <div class="topbar-title">
                <div class="topbar-accent"></div>
                <div>
                    <h1>Dashboard</h1>
                    <span>Student</span>
                </div>
            </div>
            <div class="topbar-brand">
                <!-- Shield Icon -->
                <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M20 3L5 9v10c0 9.4 6.4 18.2 15 20.4C29.6 37.2 36 28.4 36 19V9L20 3Z" fill="#0f1624"
                        stroke="#00d2ff" stroke-width="2" />
                    <path d="M14 20l4 4 8-8" stroke="#00d2ff" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
                ASCEND
            </div>
        </div>

        <!-- Page Content -->
        <div class="page-content">

            <!-- STATUS SECTION -->
            <div class="section-label">Status</div>
            <div class="status-card">

                <!-- Academic Progress -->
                <div class="progress-section">
                    <div class="progress-label">Academic Progress</div>

                    <div class="subject-row">
                        <div class="subject-name">GE Subjects</div>
                        <div style="display:flex; align-items:center; gap:16px;">
                            <div class="progress-track" style="flex:1;">
                                <div class="progress-fill" style="width:49%;"></div>
                            </div>
                            <span style="font-size:0.85rem; font-weight:700; color:#fff; width:36px;">49%</span>
                        </div>
                    </div>

                    <div class="subject-row">
                        <div class="subject-name">Major Subjects</div>
                        <div style="display:flex; align-items:center; gap:16px;">
                            <div class="progress-track" style="flex:1;">
                                <div class="progress-fill" style="width:80%;"></div>
                            </div>
                            <span style="font-size:0.85rem; font-weight:700; color:#fff; width:36px;">80%</span>
                        </div>
                    </div>
                </div>

                <!-- GPA -->
                <div class="gpa-section">
                    <div class="gpa-label">GPA</div>
                    <div class="gpa-value">1.25</div>
                    <div class="gpa-sub">94% Excellent</div>
                </div>

                <!-- Dean's List + Alert -->
                <div class="deans-section">
                    <div class="deans-badge">
                        <div class="star">⭐</div>
                        <div class="deans-number">1.5</div>
                        <div class="deans-title">Dean's List</div>
                    </div>
                    <div class="at-risk-alert">
                        <i class="ph ph-warning at-risk-icon"></i>
                        <div>
                            <div class="at-risk-title">⚠ At-Risk Alert</div>
                            <div class="at-risk-desc">Failure grades or academic<br>progress not improving</div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- BADGE SECTION -->
            <div class="badge-label">Badge</div>
            <div class="badge-grid">
                <div class="badge-card">
                    <img src="../assets/images/badge_web_dev.png" alt="Web Development Badge" class="badge-img">
                    <div class="badge-name">Web Development</div>
                </div>
                <div class="badge-card">
                    <img src="../assets/images/badge_attendance.png" alt="Attendance Badge" class="badge-img">
                    <div class="badge-name">Attendance</div>
                </div>
                <div class="badge-card">
                    <img src="../assets/images/badge_uiux.png" alt="UI/UX Basic Badge" class="badge-img">
                    <div class="badge-name">UI/UX Basic</div>
                </div>
            </div>

        </div><!-- /page-content -->
    </div><!-- /main-area -->

    <!-- ===== FLASH TOAST ===== -->
    <?php if ($flash): ?>
        <div id="ascend-toast">
            <span style="font-size:1.5rem;">✅</span>
            <div style="flex:1;">
                <div
                    style="font-weight:700; font-size:0.78rem; color:#00d2ff; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">
                    Login Successful</div>
                <div style="color:rgba(248,250,252,0.85); line-height:1.5; font-size:0.88rem;"><?php echo $flash; ?></div>
            </div>
            <button class="toast-close" onclick="document.getElementById('ascend-toast').remove()">✕</button>
        </div>
        <script>
            setTimeout(function () {
                var t = document.getElementById('ascend-toast');
                if (t) {
                    t.style.animation = 'slideOut 0.4s ease-in forwards';
                    setTimeout(function () { if (t) t.remove(); }, 400);
                }
            }, 5000);
        </script>
    <?php endif; ?>

</body>

</html>
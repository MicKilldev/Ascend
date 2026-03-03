<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['teacher', 'faculty', 'school_admin'])) {
    header("Location: ../login.php?role=faculty");
    exit();
}

$username = htmlspecialchars($_SESSION['username'] ?? 'Admin');
$role = htmlspecialchars($_SESSION['role']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ascend | Academic Analytics</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
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
            background: linear-gradient(135deg, #6e45e2, #00d2ff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 700;
            color: #fff;
        }

        .user-name {
            font-size: .9rem;
            font-weight: 600;
            color: #fff;
            line-height: 1.2;
        }

        .user-role {
            font-size: .72rem;
            color: rgba(255, 255, 255, .4);
        }

        .sidebar-nav {
            flex: 1;
            padding: 18px 0;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 13px 20px;
            color: rgba(255, 255, 255, .45);
            text-decoration: none;
            font-size: .9rem;
            font-weight: 500;
            transition: all .2s;
            border-left: 3px solid transparent;
        }

        .nav-item:hover {
            color: #fff;
            background: rgba(255, 255, 255, .04);
        }

        .nav-item.active {
            color: #fff;
            background: rgba(110, 69, 226, .1);
            border-left-color: #6e45e2;
        }

        .nav-item.active i {
            color: #6e45e2;
        }

        .main-area {
            margin-left: 240px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

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
            background: #a855f7;
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
            font-size: .72rem;
            font-weight: 700;
            color: #a855f7;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .page-content {
            padding: 0 36px 36px;
        }

        .placeholder-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
            margin-top: 10px;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        }

        .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f1624;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .chart-placeholder {
            height: 300px;
            background: #f8fafc;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #94a3b8;
            border: 2px dashed #e2e8f0;
            font-size: .9rem;
        }

        .stat-rows {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .stat-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 16px;
            background: #f8fafc;
            border-radius: 10px;
        }

        .stat-row-label {
            font-size: .85rem;
            font-weight: 600;
            color: #64748b;
        }

        .stat-row-val {
            font-size: 1.2rem;
            font-weight: 800;
            color: #0f1624;
        }
    </style>
</head>

<body>

    <aside class="sidebar">
        <div class="sidebar-user">
            <div class="user-avatar">
                <?php echo strtoupper(substr($username, 0, 1)); ?>
            </div>
            <div style="flex:1;">
                <div class="user-name">
                    <?php echo $username; ?>
                </div>
                <div class="user-role">
                    <?php echo ucfirst($role); ?>
                </div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item"><i class="ph ph-squares-four"></i> Dashboard</a>
            <a href="users.php" class="nav-item"><i class="ph ph-users"></i> All Users</a>
            <a href="students.php" class="nav-item"><i class="ph ph-student"></i> Students</a>
            <a href="analytics.php" class="nav-item active"><i class="ph-fill ph-chart-line-up"></i> Analytics</a>
        </nav>
    </aside>

    <div class="main-area">
        <div class="topbar">
            <div class="topbar-title">
                <div class="topbar-accent"></div>
                <div>
                    <h1>Predictive Analytics</h1><span>Teacher & Admin</span>
                </div>
            </div>
            <div style="font-size: 1.3rem; font-weight: 800; color: #0f1624;">ASCEND</div>
        </div>

        <div class="page-content">
            <div class="placeholder-grid">
                <div class="card">
                    <div class="card-title"><i class="ph ph-chart-bar"></i> Performance Trends</div>
                    <div class="chart-placeholder">
                        Visual Data Analytics Visualization Area
                    </div>
                </div>
                <div class="card">
                    <div class="card-title"><i class="ph ph-trend-up"></i> Quick Stats</div>
                    <div class="stat-rows">
                        <div class="stat-row">
                            <div class="stat-row-label">Avg. Attendance</div>
                            <div class="stat-row-val">92%</div>
                        </div>
                        <div class="stat-row">
                            <div class="stat-row-label">Batch Pass Rate</div>
                            <div class="stat-row-val">88.4%</div>
                        </div>
                        <div class="stat-row">
                            <div class="stat-row-label">At Risk Identified</div>
                            <div class="stat-row-val">12</div>
                        </div>
                        <div class="stat-row">
                            <div class="stat-row-label">Course Satisfaction</div>
                            <div class="stat-row-val">4.8/5</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
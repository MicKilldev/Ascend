<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['teacher', 'faculty', 'school_admin'])) {
    header("Location: ../login.php?role=faculty");
    exit();
}

$username = htmlspecialchars($_SESSION['username'] ?? 'Admin');
$course = htmlspecialchars($_SESSION['course'] ?? 'Faculty');

// Fetch only Students
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql = "SELECT id, username, email, course, created_at FROM users WHERE role='student'";
$params = [];
$types = '';

if ($search !== '') {
    $sql .= " AND (username LIKE ? OR email LIKE ? OR course LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $params[] = $like;
    $types .= 'sss';
}
$sql .= " ORDER BY course ASC, username ASC";

$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$students = $stmt->get_result();

// Mock analytics for the student view
$total_students = $students->num_rows;
$at_risk = floor($total_students * 0.12); // Mock 12% at risk
$deans_list = floor($total_students * 0.18); // Mock 18% deans list
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ascend | Student Directory</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        /* Shared Styles */
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
            flex-shrink: 0;
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
            background: rgba(0, 210, 255, 0.08);
            border-left-color: #00d2ff;
        }

        .nav-item.active i {
            color: #00d2ff;
        }

        .sidebar-footer {
            padding: 20px;
            border-top: 1px solid rgba(255, 255, 255, .06);
        }

        .sidebar-footer a {
            display: flex;
            align-items: center;
            gap: 10px;
            color: rgba(255, 255, 255, .4);
            text-decoration: none;
            font-size: .85rem;
            padding: 8px 0;
            transition: color .2s;
        }

        .sidebar-footer a:hover {
            color: #fff;
        }

        .sidebar-footer a.logout:hover {
            color: #f87171;
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
            font-size: .72rem;
            font-weight: 700;
            color: #00d2ff;
            letter-spacing: 1.5px;
            text-transform: uppercase;
        }

        .page-content {
            padding: 0 36px 36px;
        }

        /* Student Specific Stats */
        .student-stats {
            display: flex;
            gap: 18px;
            margin-bottom: 24px;
            margin-top: 10px;
        }

        .stat-small {
            flex: 1;
            background: #fff;
            border-radius: 16px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .stat-small-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.3rem;
        }

        .stat-small-val {
            font-size: 1.6rem;
            font-weight: 800;
            color: #0f1624;
            line-height: 1;
        }

        .stat-small-label {
            font-size: .78rem;
            font-weight: 600;
            color: #64748b;
            margin-top: 2px;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .section-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #00d2ff;
            color: #0f1624;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 9px 22px;
            border-radius: 30px;
        }

        .search-box {
            display: flex;
            gap: 10px;
        }

        .search-input {
            padding: 11px 18px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: .9rem;
            width: 320px;
            outline: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .btn-search {
            padding: 11px 22px;
            background: #0f1624;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: .9rem;
            cursor: pointer;
        }

        .student-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .student-card {
            background: #fff;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
            display: flex;
            flex-direction: column;
            gap: 16px;
            border: 1px solid transparent;
            transition: all .3s;
            cursor: pointer;
            position: relative;
        }

        .student-card:hover {
            transform: translateY(-5px);
            border-color: #00d2ff;
            box-shadow: 0 12px 30px rgba(0, 210, 255, 0.12);
        }

        .student-header {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .student-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            font-weight: 700;
            color: #00d2ff;
            border: 2px solid #e2e8f0;
        }

        .student-name {
            font-size: 1rem;
            font-weight: 700;
            color: #0f1624;
        }

        .student-id {
            font-size: .75rem;
            color: #94a3b8;
            font-weight: 600;
            margin-top: 2px;
            text-transform: uppercase;
        }

        .student-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            border-top: 1px solid #f1f5f9;
            padding-top: 16px;
        }

        .info-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .info-label {
            font-size: .65rem;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .info-val {
            font-size: .85rem;
            font-weight: 600;
            color: #334155;
        }

        .risk-badge {
            position: absolute;
            top: 18px;
            right: 18px;
            font-size: .68rem;
            font-weight: 800;
            padding: 4px 10px;
            border-radius: 30px;
            text-transform: uppercase;
        }

        .risk-med {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .risk-high {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0.6;
            }

            100% {
                opacity: 1;
            }
        }

        .empty-state {
            grid-column: 1 / -1;
            padding: 80px 20px;
            text-align: center;
            color: #94a3b8;
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
                    <?php echo ucfirst($_SESSION['role']); ?>
                </div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item"><i class="ph ph-squares-four"></i> Dashboard</a>
            <a href="users.php" class="nav-item"><i class="ph ph-users"></i> All Users</a>
            <a href="students.php" class="nav-item active"><i class="ph-fill ph-student"></i> Students</a>
        </nav>
    </aside>

    <div class="main-area">
        <div class="topbar">
            <div class="topbar-title">
                <div class="topbar-accent"></div>
                <div>
                    <h1>Student Directory</h1><span>Teacher & Admin</span>
                </div>
            </div>
            <div style="font-size: 1.3rem; font-weight: 800; color: #0f1624;">ASCEND</div>
        </div>

        <div class="page-content">
            <div class="student-stats">
                <div class="stat-small">
                    <div class="stat-small-icon" style="background: rgba(0,210,255,0.1); color: #00d2ff;"><i
                            class="ph-fill ph-student"></i></div>
                    <div>
                        <div class="stat-small-val">
                            <?php echo $total_students; ?>
                        </div>
                        <div class="stat-small-label">Total Registered</div>
                    </div>
                </div>
                <div class="stat-small">
                    <div class="stat-small-icon" style="background: rgba(16,185,129,0.1); color: #10b981;"><i
                            class="ph-fill ph-medal"></i></div>
                    <div>
                        <div class="stat-small-val">
                            <?php echo $deans_list; ?>
                        </div>
                        <div class="stat-small-label">Potential Dean's List</div>
                    </div>
                </div>
                <div class="stat-small">
                    <div class="stat-small-icon" style="background: rgba(239,68,68,0.1); color: #ef4444;"><i
                            class="ph-fill ph-warning-circle"></i></div>
                    <div>
                        <div class="stat-small-val">
                            <?php echo $at_risk; ?>
                        </div>
                        <div class="stat-small-label">At-Risk Students</div>
                    </div>
                </div>
            </div>

            <div class="section-header">
                <div class="section-label"><i class="ph ph-graduation-cap"></i> Student Masterlist</div>
                <form onclick="this.form.submit()" class="search-box">
                    <input class="search-input" type="text" name="search"
                        placeholder="Search by name, course, or email..." value="<?php echo $search; ?>">
                    <button class="btn-search" type="submit">Search</button>
                </form>
            </div>

            <div class="student-grid">
                <?php if ($students->num_rows === 0): ?>
                    <div class="empty-state"><i class="ph ph-magnifying-glass" style="font-size: 3rem;"></i><br><br>No
                        students matched your criteria.</div>
                <?php else: ?>
                    <?php while ($s = $students->fetch_assoc()): ?>
                        <div class="student-card">
                            <div class="student-header">
                                <div class="student-avatar">
                                    <?php echo strtoupper(substr($s['username'], 0, 1)); ?>
                                </div>
                                <div>
                                    <div class="student-name">
                                        <?php echo htmlspecialchars($s['username']); ?>
                                    </div>
                                    <div class="student-id">
                                        <?php echo htmlspecialchars($s['email']); ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $is_high_risk = (crc32($s['id']) % 10 === 0); // Mock check for demo
                            ?>
                            <div class="risk-badge <?php echo $is_high_risk ? 'risk-high' : 'risk-med'; ?>">
                                <?php echo $is_high_risk ? '⚠ High Risk' : '✓ Normal'; ?>
                            </div>
                            <div class="student-info">
                                <div class="info-item">
                                    <div class="info-label">Course</div>
                                    <div class="info-val">
                                        <?php echo htmlspecialchars($s['course'] ?? 'N/A'); ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">GPA</div>
                                    <div class="info-val">
                                        <?php echo number_format(1 + (crc32($s['id']) % 200) / 100, 2); ?>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Attendance</div>
                                    <div class="info-val">
                                        <?php echo 80 + (crc32($s['id']) % 20); ?>%
                                    </div>
                                </div>
                                <div class="info-item">
                                    <div class="info-label">Status</div>
                                    <div class="info-val">
                                        <?php echo $is_high_risk ? 'Falling' : 'Passing'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>
<?php
session_start();
include('../config/db.php');

// Guard: only teacher/faculty/school_admin allowed
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['teacher', 'faculty', 'school_admin'])) {
    header("Location: ../login.php?role=faculty");
    exit();
}

$username = htmlspecialchars($_SESSION['username'] ?? 'Admin');
$course = htmlspecialchars($_SESSION['course'] ?? 'Faculty');

// ---------- FLASH ----------
$flash = '';
$flashType = 'success';
if (!empty($_SESSION['flash_success'])) {
    $flash = $_SESSION['flash_success'];
    unset($_SESSION['flash_success']);
}

/* ===================================================
   ACTION: CREATE NEW USER ACCOUNT
   =================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $new_username = trim($_POST['new_username']);
    $new_email = trim($_POST['new_email']);
    $new_password = trim($_POST['new_password']);
    $new_role = trim($_POST['new_role']);
    $new_course = trim($_POST['new_course'] ?? '');

    $allowed_roles = ['student', 'teacher', 'parent', 'guest', 'faculty', 'school_admin'];
    if (!in_array($new_role, $allowed_roles)) {
        $flash = "Invalid role selected.";
        $flashType = 'error';
    } elseif (empty($new_username) || empty($new_email) || empty($new_password)) {
        $flash = "All required fields must be filled.";
        $flashType = 'error';
    } else {
        // Check duplicate email
        $chk = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $chk->bind_param("s", $new_email);
        $chk->execute();
        $chk->store_result();

        if ($chk->num_rows > 0) {
            $flash = "An account with that email already exists.";
            $flashType = 'error';
        } else {
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, course) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $new_username, $new_email, $new_password, $new_role, $new_course);
            if ($stmt->execute()) {
                $flash = "Account for <strong>" . htmlspecialchars($new_username) . "</strong> created successfully as <strong>" . ucfirst($new_role) . "</strong>.";
            } else {
                $flash = "Database error: " . $conn->error;
                $flashType = 'error';
            }
        }
    }
}

/* ===================================================
   ACTION: DELETE USER
   =================================================== */
if (isset($_GET['delete_user']) && is_numeric($_GET['delete_user'])) {
    $del_id = intval($_GET['delete_user']);
    // prevent self-delete
    if ($del_id !== intval($_SESSION['user_id'] ?? 0)) {
        $del = $conn->prepare("DELETE FROM users WHERE id = ?");
        $del->bind_param("i", $del_id);
        $del->execute();
        $flash = "User account deleted.";
    } else {
        $flash = "You cannot delete your own account.";
        $flashType = 'error';
    }
    header("Location: dashboard.php");
    exit();
}

/* ===================================================
   FETCH STATS
   =================================================== */
function countRole($conn, $role)
{
    $r = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='$role'");
    return $r ? $r->fetch_assoc()['c'] : 0;
}
$total_students = countRole($conn, 'student');
$total_teachers = countRole($conn, 'teacher') + countRole($conn, 'faculty') + countRole($conn, 'school_admin');
$total_parents = countRole($conn, 'parent');
$total_guests = countRole($conn, 'guest');
$total_all = $total_students + $total_teachers + $total_parents + $total_guests;

/* ===================================================
   FETCH ALL USERS
   =================================================== */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'all';
$search_sql = '';
$params = [];
$types = '';

$sql = "SELECT id, username, email, role, course, created_at FROM users WHERE 1=1";

if ($filter !== 'all') {
    $sql .= " AND role = ?";
    $params[] = $filter;
    $types .= 's';
}
if ($search !== '') {
    $sql .= " AND (username LIKE ? OR email LIKE ?)";
    $like = "%$search%";
    $params[] = $like;
    $params[] = $like;
    $types .= 'ss';
}
$sql .= " ORDER BY created_at DESC";

$usersStmt = $conn->prepare($sql);
if (!empty($params)) {
    $usersStmt->bind_param($types, ...$params);
}
$usersStmt->execute();
$usersResult = $usersStmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ascend | Teacher & Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        /* ===== RESET ===== */
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

        .bell-icon {
            color: rgba(255, 255, 255, .4);
            font-size: 1.1rem;
            cursor: pointer;
            margin-left: auto;
            transition: color .2s;
        }

        .bell-icon:hover {
            color: #6e45e2;
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

        .nav-item i {
            font-size: 1.1rem;
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

        /* ===== MAIN ===== */
        .main-area {
            margin-left: 240px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* TOPBAR */
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
            background: #6e45e2;
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
            color: #6e45e2;
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
        }

        .topbar-brand svg {
            width: 30px;
            height: 30px;
        }

        /* ADD USER BUTTON */
        .btn-add-user {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 22px;
            background: linear-gradient(135deg, #6e45e2, #a855f7);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: .9rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 8px 20px -4px rgba(110, 69, 226, .45);
            transition: all .3s;
        }

        .btn-add-user:hover {
            transform: translateY(-2px);
            filter: brightness(1.1);
        }

        /* PAGE CONTENT */
        .page-content {
            padding: 0 36px 36px;
        }

        /* ===== STATS GRID ===== */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 18px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: #fff;
            border-radius: 18px;
            padding: 24px 22px;
            display: flex;
            align-items: center;
            gap: 16px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, .06);
            transition: transform .3s, box-shadow .3s;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--accent);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 28px rgba(0, 0, 0, .1);
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            background: var(--accent-light);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: var(--accent);
            flex-shrink: 0;
        }

        .stat-num {
            font-size: 2rem;
            font-weight: 800;
            color: #0f1624;
            line-height: 1;
        }

        .stat-label {
            font-size: .8rem;
            color: #64748b;
            font-weight: 500;
            margin-top: 3px;
        }

        /* ===== SECTION LABEL ===== */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 16px;
        }

        .section-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #0f1624;
            color: #fff;
            font-size: .78rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 9px 22px;
            border-radius: 30px;
        }

        /* FILTER BAR */
        .filter-bar {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .filter-bar form {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-input {
            padding: 9px 16px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-family: 'Outfit', sans-serif;
            font-size: .88rem;
            color: #1a1a2e;
            outline: none;
            transition: border-color .2s;
            width: 220px;
        }

        .search-input:focus {
            border-color: #6e45e2;
        }

        .filter-select {
            padding: 9px 14px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-family: 'Outfit', sans-serif;
            font-size: .88rem;
            color: #1a1a2e;
            outline: none;
            cursor: pointer;
        }

        .btn-filter {
            padding: 9px 18px;
            background: #0f1624;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: 'Outfit', sans-serif;
            font-size: .85rem;
            cursor: pointer;
            transition: all .2s;
        }

        .btn-filter:hover {
            background: #1e293b;
        }

        /* ===== USER TABLE ===== */
        .table-wrapper {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0, 0, 0, .07);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #0f1624;
        }

        thead th {
            padding: 16px 20px;
            text-align: left;
            font-size: .75rem;
            font-weight: 700;
            color: rgba(255, 255, 255, .6);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background .15s;
        }

        tbody tr:last-child {
            border-bottom: none;
        }

        tbody tr:hover {
            background: #f8faff;
        }

        td {
            padding: 15px 20px;
            font-size: .88rem;
            color: #334155;
            vertical-align: middle;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-cell-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6e45e2, #00d2ff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .85rem;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .user-cell-name {
            font-weight: 600;
            color: #0f1624;
        }

        .user-cell-email {
            font-size: .78rem;
            color: #94a3b8;
        }

        /* ROLE BADGES */
        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .role-student {
            background: rgba(0, 210, 255, .1);
            color: #0369a1;
        }

        .role-teacher,
        .role-faculty,
        .role-school_admin {
            background: rgba(110, 69, 226, .1);
            color: #6e45e2;
        }

        .role-parent {
            background: rgba(16, 185, 129, .1);
            color: #059669;
        }

        .role-guest {
            background: rgba(245, 158, 11, .1);
            color: #d97706;
        }

        .btn-delete {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 14px;
            background: rgba(239, 68, 68, .08);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, .2);
            border-radius: 8px;
            font-family: 'Outfit', sans-serif;
            font-size: .78rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            transition: all .2s;
        }

        .btn-delete:hover {
            background: #ef4444;
            color: #fff;
        }

        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 10px;
            display: block;
        }

        /* ===== MODAL ===== */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(7, 11, 20, .6);
            backdrop-filter: blur(6px);
            z-index: 999;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.open {
            display: flex;
        }

        .modal {
            background: #fff;
            border-radius: 24px;
            padding: 38px 36px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 30px 60px rgba(0, 0, 0, .3);
            animation: modalIn .35s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes modalIn {
            from {
                opacity: 0;
                transform: scale(.93) translateY(20px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }

        .modal-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: #0f1624;
        }

        .modal-close {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #f1f5f9;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            color: #64748b;
            transition: all .2s;
        }

        .modal-close:hover {
            background: #fee2e2;
            color: #ef4444;
        }

        .form-row {
            margin-bottom: 18px;
        }

        .form-row label {
            display: block;
            font-size: .82rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 7px;
        }

        .form-row span.req {
            color: #ef4444;
        }

        .form-row input,
        .form-row select {
            width: 100%;
            padding: 12px 16px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: .92rem;
            color: #0f1624;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }

        .form-row input:focus,
        .form-row select:focus {
            border-color: #6e45e2;
            box-shadow: 0 0 0 3px rgba(110, 69, 226, .12);
        }

        .form-row-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #6e45e2, #a855f7);
            color: #fff;
            border: none;
            border-radius: 14px;
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 8px 20px -4px rgba(110, 69, 226, .4);
            transition: all .3s;
            margin-top: 8px;
        }

        .btn-submit:hover {
            filter: brightness(1.08);
            transform: translateY(-1px);
        }

        /* ===== TOAST ===== */
        #ascend-toast {
            position: fixed;
            top: 24px;
            right: 24px;
            z-index: 9999;
            background: #0f1624;
            border: 1px solid rgba(110, 69, 226, .3);
            border-left: 4px solid #6e45e2;
            color: #f8fafc;
            padding: 18px 20px 18px 18px;
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, .3);
            min-width: 300px;
            max-width: 400px;
            display: flex;
            align-items: flex-start;
            gap: 14px;
            animation: slideIn .5s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        #ascend-toast.toast-error {
            border-left-color: #ef4444;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(30px)
            }

            to {
                opacity: 1;
                transform: translateX(0)
            }
        }

        @keyframes slideOut {
            from {
                opacity: 1;
                transform: translateX(0)
            }

            to {
                opacity: 0;
                transform: translateX(30px)
            }
        }

        .toast-close {
            background: none;
            border: none;
            color: rgba(255, 255, 255, .3);
            cursor: pointer;
            font-size: 1rem;
            transition: color .2s;
        }

        .toast-close:hover {
            color: #f87171;
        }

        /* RESPONSIVE */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .main-area {
                margin-left: 0;
            }

            .form-row-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <!-- ===== SIDEBAR ===== -->
    <aside class="sidebar">
        <div class="sidebar-user">
            <div class="user-avatar"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
            <div style="flex:1;">
                <div class="user-name"><?php echo $username; ?></div>
                <div class="user-role"><?php echo ucfirst($_SESSION['role']); ?></div>
            </div>
            <i class="ph ph-bell bell-icon"></i>
        </div>

        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item active">
                <i class="ph-fill ph-squares-four"></i> Dashboard
            </a>
            <a href="users.php" class="nav-item">
                <i class="ph ph-users"></i> All Users
            </a>
            <a href="students.php" class="nav-item">
                <i class="ph ph-student"></i> Students
            </a>
            <a href="analytics.php" class="nav-item">
                <i class="ph ph-chart-line-up"></i> Analytics
            </a>
            <a href="grades.php" class="nav-item">
                <i class="ph ph-chart-bar"></i> Grades
            </a>
            <a href="portfolio.php" class="nav-item">
                <i class="ph ph-folder-open"></i> Portfolios
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="settings.php"><i class="ph ph-gear"></i> Settings</a>
            <a href="../logout.php" class="logout"><i class="ph ph-sign-out"></i> Log Out</a>
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
                    <span>Teacher &amp; Admin</span>
                </div>
            </div>
            <div style="display:flex; align-items:center; gap:20px;">
                <button class="btn-add-user" onclick="openModal()">
                    <i class="ph ph-plus"></i> Create Account
                </button>
                <div class="topbar-brand">
                    <svg viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 3L5 9v10c0 9.4 6.4 18.2 15 20.4C29.6 37.2 36 28.4 36 19V9L20 3Z" fill="#0f1624"
                            stroke="#6e45e2" stroke-width="2" />
                        <path d="M14 20l4 4 8-8" stroke="#6e45e2" stroke-width="2.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                    ASCEND
                </div>
            </div>
        </div>

        <div class="page-content">

            <!-- ===== STATS ===== -->
            <div class="stats-grid">
                <div class="stat-card" style="--accent:#00d2ff; --accent-light:rgba(0,210,255,.1);">
                    <div class="stat-icon"><i class="ph-fill ph-student"></i></div>
                    <div>
                        <div class="stat-num"><?php echo $total_students; ?></div>
                        <div class="stat-label">Total Students</div>
                    </div>
                </div>
                <div class="stat-card" style="--accent:#6e45e2; --accent-light:rgba(110,69,226,.1);">
                    <div class="stat-icon"><i class="ph-fill ph-chalkboard-teacher"></i></div>
                    <div>
                        <div class="stat-num"><?php echo $total_teachers; ?></div>
                        <div class="stat-label">Teachers / Admins</div>
                    </div>
                </div>
                <div class="stat-card" style="--accent:#10b981; --accent-light:rgba(16,185,129,.1);">
                    <div class="stat-icon"><i class="ph-fill ph-users-three"></i></div>
                    <div>
                        <div class="stat-num"><?php echo $total_parents; ?></div>
                        <div class="stat-label">Parents</div>
                    </div>
                </div>
                <div class="stat-card" style="--accent:#f59e0b; --accent-light:rgba(245,158,11,.1);">
                    <div class="stat-icon"><i class="ph-fill ph-briefcase"></i></div>
                    <div>
                        <div class="stat-num"><?php echo $total_guests; ?></div>
                        <div class="stat-label">Hiring / Guests</div>
                    </div>
                </div>
                <div class="stat-card" style="--accent:#0f1624; --accent-light:rgba(15,22,36,.07);">
                    <div class="stat-icon"><i class="ph-fill ph-globe"></i></div>
                    <div>
                        <div class="stat-num"><?php echo $total_all; ?></div>
                        <div class="stat-label">Total Accounts</div>
                    </div>
                </div>
            </div>

            <!-- ===== USER TABLE ===== -->
            <div class="section-header">
                <div class="section-label"><i class="ph ph-users"></i> All User Accounts</div>
                <div class="filter-bar">
                    <form method="GET">
                        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                        <input class="search-input" type="text" name="search" placeholder="Search name / email..."
                            value="<?php echo htmlspecialchars($search); ?>">
                        <select class="filter-select" name="filter" onchange="this.form.submit()">
                            <option value="all" <?php echo $filter === 'all' ? 'selected' : ''; ?>>All Roles</option>
                            <option value="student" <?php echo $filter === 'student' ? 'selected' : ''; ?>>Student
                            </option>
                            <option value="teacher" <?php echo $filter === 'teacher' ? 'selected' : ''; ?>>Teacher
                            </option>
                            <option value="parent" <?php echo $filter === 'parent' ? 'selected' : ''; ?>>Parent</option>
                            <option value="guest" <?php echo $filter === 'guest' ? 'selected' : ''; ?>>Guest</option>
                        </select>
                        <button class="btn-filter" type="submit"><i class="ph ph-magnifying-glass"></i></button>
                    </form>
                </div>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User</th>
                            <th>Role</th>
                            <th>Course / Dept</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($usersResult->num_rows === 0): ?>
                            <tr>
                                <td colspan="6">
                                    <div class="empty-state">
                                        <i class="ph ph-users-three"></i>
                                        No users found. Try changing your search or filter.
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $i = 1;
                            while ($u = $usersResult->fetch_assoc()): ?>
                                <tr>
                                    <td style="color:#94a3b8; font-size:.8rem;"><?php echo $i++; ?></td>
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-cell-avatar">
                                                <?php echo strtoupper(substr($u['username'], 0, 1)); ?>
                                            </div>
                                            <div>
                                                <div class="user-cell-name"><?php echo htmlspecialchars($u['username']); ?>
                                                </div>
                                                <div class="user-cell-email"><?php echo htmlspecialchars($u['email']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php $r = htmlspecialchars($u['role']); ?>
                                        <span class="role-badge role-<?php echo $r; ?>"><?php echo ucfirst($r); ?></span>
                                    </td>
                                    <td><?php echo htmlspecialchars($u['course'] ?? '—'); ?></td>
                                    <td style="font-size:.8rem; color:#94a3b8;">
                                        <?php echo isset($u['created_at']) ? date('M d, Y', strtotime($u['created_at'])) : '—'; ?>
                                    </td>
                                    <td>
                                        <a class="btn-delete" href="dashboard.php?delete_user=<?php echo $u['id']; ?>"
                                            onclick="return confirm('Delete <?php echo htmlspecialchars($u['username']); ?>? This cannot be undone.');">
                                            <i class="ph ph-trash"></i> Delete
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div><!-- /page-content -->
    </div><!-- /main-area -->


    <!-- ===== MODAL: CREATE USER ===== -->
    <div class="modal-overlay" id="createModal" onclick="handleOverlayClick(event)">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title">✚ Create New Account</div>
                <button class="modal-close" onclick="closeModal()"><i class="ph ph-x"></i></button>
            </div>
            <form method="POST">
                <div class="form-row-grid">
                    <div class="form-row">
                        <label>Full Name <span class="req">*</span></label>
                        <input type="text" name="new_username" placeholder="e.g. Juan Dela Cruz" required>
                    </div>
                    <div class="form-row">
                        <label>Role <span class="req">*</span></label>
                        <select name="new_role" id="roleSelect" required>
                            <option value="">— Select Role —</option>
                            <option value="student">Student Account</option>
                            <option value="teacher">Teacher Account</option>
                            <option value="faculty">Faculty Member</option>
                            <option value="school_admin">System Administrator</option>
                            <option value="parent">Parent Account</option>
                            <option value="guest">Guest / Hiring Partner</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <label>Email Address <span class="req">*</span></label>
                    <input type="email" name="new_email" placeholder="user@institution.edu" required>
                </div>

                <div class="form-row-grid">
                    <div class="form-row">
                        <label>Password <span class="req">*</span></label>
                        <input type="password" name="new_password" placeholder="Min. 6 characters" required
                            minlength="6">
                    </div>
                    <div class="form-row">
                        <label>Course / Department</label>
                        <input type="text" name="new_course" placeholder="e.g. BSIT, IT Dept">
                    </div>
                </div>

                <button type="submit" name="create_user" class="btn-submit">
                    <i class="ph ph-user-plus"></i> &nbsp; Create Account
                </button>
            </form>
        </div>
    </div>


    <!-- ===== FLASH TOAST ===== -->
    <?php if ($flash): ?>
        <div id="ascend-toast" class="<?php echo $flashType === 'error' ? 'toast-error' : ''; ?>">
            <span style="font-size:1.4rem;"><?php echo $flashType === 'error' ? '❌' : '✅'; ?></span>
            <div style="flex:1;">
                <div
                    style="font-weight:700; font-size:.78rem; color:<?php echo $flashType === 'error' ? '#f87171' : '#6e45e2'; ?>; text-transform:uppercase; letter-spacing:.5px; margin-bottom:4px;">
                    <?php echo $flashType === 'error' ? 'Error' : 'Success'; ?>
                </div>
                <div style="color:rgba(248,250,252,.85); line-height:1.5; font-size:.88rem;"><?php echo $flash; ?></div>
            </div>
            <button class="toast-close" onclick="document.getElementById('ascend-toast').remove()">✕</button>
        </div>
        <script>
            setTimeout(function () {
                var t = document.getElementById('ascend-toast');
                if (t) {
                    t.style.animation = 'slideOut .4s ease-in forwards';
                    setTimeout(function () {
                        if (t) t.remove();
                    }, 400);
                }
            }, 5000);
        </script>
    <?php endif; ?>

    <!-- Open modal auto if form failed -->
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user']) && $flashType === 'error'): ?>
        <script>document.addEventListener('DOMContentLoaded', openModal);</script>
    <?php endif; ?>

    <script>
        function openModal() {
            document.getElementById('createModal').classList.add('open');
        }

        function closeModal() {
            document.getElementById('createModal').classList.remove('open');
        }

        function handleOverlayClick(e) {
            if (e.target === document.getElementById('createModal')) closeModal();
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
        });
    </script>

</body>

</html>
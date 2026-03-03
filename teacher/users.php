<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['teacher', 'faculty', 'school_admin'])) {
    header("Location: ../login.php?role=faculty");
    exit();
}

$username = htmlspecialchars($_SESSION['username'] ?? 'Admin');
$course = htmlspecialchars($_SESSION['course'] ?? 'Faculty');

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'all';

$sql = "SELECT id, username, email, role, course, created_at FROM users WHERE 1=1";
$params = [];
$types = '';

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
    <title>Ascend | All Users Management</title>
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

        .page-content {
            padding: 0 36px 36px;
        }

        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            margin-top: 10px;
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

        .search-input {
            padding: 11px 18px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: .9rem;
            width: 300px;
            outline: none;
            transition: border-color .2s;
        }

        .search-input:focus {
            border-color: #6e45e2;
        }

        .filter-select {
            padding: 11px 16px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: .9rem;
            outline: none;
            cursor: pointer;
        }

        .btn-filter {
            padding: 11px 22px;
            background: #0f1624;
            color: #fff;
            border: none;
            border-radius: 12px;
            font-family: 'Outfit', sans-serif;
            font-size: .9rem;
            cursor: pointer;
        }

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
            padding: 18px 20px;
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

        tbody tr:hover {
            background: #f8faff;
        }

        td {
            padding: 18px 20px;
            font-size: .9rem;
            color: #334155;
            vertical-align: middle;
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-cell-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6e45e2, #00d2ff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .9rem;
            font-weight: 700;
            color: #fff;
        }

        .user-cell-name {
            font-weight: 600;
            color: #0f1624;
        }

        .user-cell-email {
            font-size: .8rem;
            color: #94a3b8;
        }

        .role-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 5px 14px;
            border-radius: 30px;
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .role-student {
            background: rgba(0, 210, 255, 0.1);
            color: #0369a1;
        }

        .role-teacher,
        .role-faculty,
        .role-school_admin {
            background: rgba(110, 69, 226, 0.1);
            color: #6e45e2;
        }

        .role-parent {
            background: rgba(16, 185, 129, 0.1);
            color: #059669;
        }

        .role-guest {
            background: rgba(245, 158, 11, 0.1);
            color: #d97706;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            border-radius: 8px;
            font-size: .8rem;
            font-weight: 600;
            text-decoration: none;
            transition: all .2s;
        }

        .btn-view {
            background: rgba(110, 69, 226, .08);
            color: #6e45e2;
            border: 1px solid rgba(110, 69, 226, .2);
        }

        .btn-view:hover {
            background: #6e45e2;
            color: #fff;
        }
    </style>
</head>

<body>

    <aside class="sidebar">
        <div class="sidebar-user">
            <div class="user-avatar"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
            <div style="flex:1;">
                <div class="user-name"><?php echo $username; ?></div>
                <div class="user-role"><?php echo ucfirst($_SESSION['role']); ?></div>
            </div>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item"><i class="ph ph-squares-four"></i> Dashboard</a>
            <a href="users.php" class="nav-item active"><i class="ph-fill ph-users"></i> All Users</a>
            <a href="students.php" class="nav-item"><i class="ph ph-student"></i> Students</a>
        </nav>
    </aside>

    <div class="main-area">
        <div class="topbar">
            <div class="topbar-title">
                <div class="topbar-accent"></div>
                <div>
                    <h1>User Management</h1><span>Teacher & Admin</span>
                </div>
            </div>
            <div style="font-size: 1.3rem; font-weight: 800; color: #0f1624;">ASCEND</div>
        </div>

        <div class="page-content">
            <div class="section-header">
                <div class="section-label"><i class="ph ph-users"></i> Directory</div>
                <form method="GET" style="display:flex; gap:10px;">
                    <input class="search-input" type="text" name="search" placeholder="Search name or email..."
                        value="<?php echo $search; ?>">
                    <select class="filter-select" name="filter" onchange="this.form.submit()">
                        <option value="all" <?php echo $filter == 'all' ? 'selected' : ''; ?>>All Roles</option>
                        <option value="student" <?php echo $filter == 'student' ? 'selected' : ''; ?>>Students</option>
                        <option value="teacher" <?php echo $filter == 'teacher' ? 'selected' : ''; ?>>Teachers</option>
                        <option value="faculty" <?php echo $filter == 'faculty' ? 'selected' : ''; ?>>Faculty</option>
                        <option value="school_admin" <?php echo $filter == 'school_admin' ? 'selected' : ''; ?>>System Admin
                        </option>
                        <option value="parent" <?php echo $filter == 'parent' ? 'selected' : ''; ?>>Parents</option>
                        <option value="guest" <?php echo $filter == 'guest' ? 'selected' : ''; ?>>Guests</option>
                    </select>
                    <button class="btn-filter" type="submit">Filter</button>
                </form>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>User Account</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Member Since</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($usersResult->num_rows === 0): ?>
                            <tr>
                                <td colspan="6" style="padding:40px; text-align:center; color:#94a3b8;">No accounts found.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $i = 1;
                            while ($u = $usersResult->fetch_assoc()): ?>
                                <tr>
                                    <td style="color:#94a3b8; font-size:.85rem;"><?php echo $i++; ?></td>
                                    <td>
                                        <div class="user-cell">
                                            <div class="user-cell-avatar"><?php echo strtoupper(substr($u['username'], 0, 1)); ?>
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
                                    <td><?php echo htmlspecialchars($u['course'] ?? 'General'); ?></td>
                                    <td style="font-size:.85rem; color:#94a3b8;">
                                        <?php echo date('M Y', strtotime($u['created_at'])); ?>
                                    </td>
                                    <td>
                                        <a href="#" class="btn-action btn-view"><i class="ph ph-eye"></i> View Profile</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>

</html>
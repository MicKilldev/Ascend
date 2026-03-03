<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['teacher', 'faculty', 'school_admin'])) {
    header("Location: ../login.php?role=faculty");
    exit();
}

$username = htmlspecialchars($_SESSION['username'] ?? 'Admin');

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

<?php include("../includes/header.php"); ?>

<style>
    .table-wrapper {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
        margin-top: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #0f1624;
    }

    thead th {
        padding: 18px 24px;
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
        padding: 18px 24px;
        font-size: .9rem;
        color: #334155;
        vertical-align: middle;
    }

    .user-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar-mini {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .85rem;
        font-weight: 800;
        color: var(--primary);
    }

    .role-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 14px;
        border-radius: 30px;
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
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

    .btn-view-profile {
        padding: 8px 15px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: white;
        color: #64748b;
        font-size: 0.8rem;
        font-weight: 700;
        text-decoration: none;
        transition: 0.2s;
    }

    .btn-view-profile:hover {
        background: #f8fafc;
        color: var(--primary);
        border-color: var(--primary);
    }

    .filter-group {
        display: flex;
        gap: 10px;
        align-items: center;
        background: white;
        padding: 8px;
        border-radius: 14px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
    }

    .filter-group input,
    .filter-group select {
        border: none;
        padding: 8px 12px;
        font-family: inherit;
        font-size: 0.9rem;
        outline: none;
        border-radius: 8px;
    }

    .filter-group input {
        border-right: 1px solid #f1f5f9;
        width: 250px;
    }
</style>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>

    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;">
            <div>
                <h1>Global Directory</h1>
                <p>Advanced user management and role distribution monitoring.</p>
            </div>

            <form method="GET" class="filter-group">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search name or email...">
                <select name="filter" onchange="this.form.submit()">
                    <option value="all" <?php echo $filter == 'all' ? 'selected' : ''; ?>>All Roles</option>
                    <option value="student" <?php echo $filter == 'student' ? 'selected' : ''; ?>>Students</option>
                    <option value="teacher" <?php echo $filter == 'teacher' ? 'selected' : ''; ?>>Teachers</option>
                    <option value="faculty" <?php echo $filter == 'faculty' ? 'selected' : ''; ?>>Faculty</option>
                    <option value="school_admin" <?php echo $filter == 'school_admin' ? 'selected' : ''; ?>>System Admin
                    </option>
                    <option value="parent" <?php echo $filter == 'parent' ? 'selected' : ''; ?>>Parents</option>
                    <option value="guest" <?php echo $filter == 'guest' ? 'selected' : ''; ?>>Guests</option>
                </select>
                <button type="submit"
                    style="background: var(--primary); color: white; border: none; padding: 8px 15px; border-radius: 8px; cursor: pointer; font-weight: 700;">
                    Apply
                </button>
            </form>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Account Details</th>
                        <th>Role Identification</th>
                        <th>Course / Dept</th>
                        <th>Joined</th>
                        <th>Management</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    while ($u = $usersResult->fetch_assoc()):
                        ?>
                        <tr>
                            <td style="color: #94a3b8; font-size: 0.8rem; font-weight: 700;"><?php echo $i++; ?></td>
                            <td>
                                <div class="user-cell">
                                    <div class="user-avatar-mini"><?php echo strtoupper(substr($u['username'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; color: #0f172a;">
                                            <?php echo htmlspecialchars($u['username']); ?></div>
                                        <div style="font-size: 0.8rem; color: #64748b;">
                                            <?php echo htmlspecialchars($u['email']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span
                                    class="role-badge role-<?php echo $u['role']; ?>"><?php echo ucfirst($u['role']); ?></span>
                            </td>
                            <td><?php echo htmlspecialchars($u['course'] ?? 'General'); ?></td>
                            <td style="font-size: 0.85rem; color: #64748b; font-weight: 500;">
                                <?php echo date('M d, Y', strtotime($u['created_at'])); ?>
                            </td>
                            <td>
                                <a href="#" class="btn-view-profile">
                                    <i class="ph ph-user-focus"></i> Profile
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
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
    if ($del_id !== intval($_SESSION['id'] ?? 0)) {
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
    /* Table specific overrides */
    .table-wrapper { background: #fff; border-radius: 20px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,.05); margin-top: 20px; }
    table { width: 100%; border-collapse: collapse; }
    thead { background: #0f1624; }
    thead th { padding: 18px 24px; text-align: left; font-size: .75rem; font-weight: 700; color: rgba(255,255,255,.6); text-transform: uppercase; letter-spacing: 1px; }
    tbody tr { border-bottom: 1px solid #f1f5f9; transition: background .15s; }
    tbody tr:hover { background: #f8faff; }
    td { padding: 18px 24px; font-size: .9rem; color: #334155; vertical-align: middle; }
    .user-cell { display: flex; align-items: center; gap: 12px; }
    .user-avatar-mini { width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #6e45e2, #00d2ff); display: flex; align-items: center; justify-content: center; font-size: .85rem; font-weight: 700; color: #fff; }
    .role-badge { display: inline-flex; align-items: center; padding: 5px 14px; border-radius: 30px; font-size: .75rem; font-weight: 700; text-transform: uppercase; }
    .role-student { background: rgba(0,210,255,0.1); color: #0369a1; }
    .role-teacher, .role-faculty, .role-school_admin { background: rgba(110,69,226,0.1); color: #6e45e2; }
    .role-parent { background: rgba(16,185,129,0.1); color: #059669; }
    .role-guest { background: rgba(245,158,11,0.1); color: #d97706; }
    .btn-delete { color: #f87171; text-decoration: none; font-size: 0.85rem; font-weight: 600; transition: 0.2s; display: flex; align-items: center; gap: 5px; }
    .btn-delete:hover { color: #ef4444; }

    /* Modal Styling */
    .modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.5); backdrop-filter: blur(5px); z-index: 2000; display: none; align-items: center; justify-content: center; padding: 20px; }
    .modal-overlay.open { display: flex; }
    .modal-card { background: white; border-radius: 24px; width: 100%; max-width: 550px; padding: 40px; box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25); animation: zoomIn 0.3s ease-out; }
    @keyframes zoomIn { from { transform: scale(0.9); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .modal-header h2 { font-weight: 800; font-size: 1.5rem; color: #0f172a; }
    .close-btn { background: #f1f5f9; border: none; width: 36px; height: 36px; border-radius: 50%; cursor: pointer; color: #64748b; font-size: 1.2rem; display: flex; align-items: center; justify-content: center; transition: 0.2s; }
    .close-btn:hover { background: #fee2e2; color: #f87171; }
    
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; font-size: 0.85rem; font-weight: 600; color: #475569; margin-bottom: 8px; }
    .form-group input, .form-group select { width: 100%; padding: 12px 18px; border: 1px solid #e2e8f0; border-radius: 12px; font-family: inherit; font-size: 0.95rem; outline: none; transition: 0.2s; }
    .form-group input:focus, .form-group select:focus { border-color: #6e45e2; box-shadow: 0 0 0 4px rgba(110,69,226,0.1); }
    .btn-save { width: 100%; padding: 14px; background: linear-gradient(135deg, #6e45e2, #a855f7); color: white; border: none; border-radius: 12px; font-size: 1rem; font-weight: 700; cursor: pointer; transition: 0.3s; box-shadow: 0 10px 15px -3px rgba(110,69,226,0.3); }
    .btn-save:hover { transform: translateY(-2px); filter: brightness(1.1); }
</style>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>

    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1>Admin Dashboard</h1>
                <p>Manage the Ascend ecosystem and institutional users.</p>
            </div>
            <button class="btn-save" style="width: auto; padding: 12px 24px;" onclick="openModal()">
                <i class="ph ph-plus-circle"></i> Create Account
            </button>
        </div>

        <div class="card-container">
            <div class="card" style="border-left: 4px solid #00d2ff;">
                <h3>Students</h3>
                <h1><?php echo $total_students; ?></h1>
                <div class="card-footer">Active enrollment</div>
            </div>
            <div class="card" style="border-left: 4px solid #6e45e2;">
                <h3>Admins / Faculty</h3>
                <h1><?php echo $total_teachers; ?></h1>
                <div class="card-footer">System oversight</div>
            </div>
            <div class="card" style="border-left: 4px solid #10b981;">
                <h3>Total Accounts</h3>
                <h1><?php echo $total_all; ?></h1>
                <div class="card-footer">Global users</div>
            </div>
        </div>

        <div style="margin-top: 50px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-weight: 800; color: #0f172a; font-size: 1.25rem;">User Directory</h2>
                <form method="GET" style="display: flex; gap: 10px;">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search name or email..." style="padding: 8px 15px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 0.9rem;">
                    <select name="filter" onchange="this.form.submit()" style="padding: 8px 15px; border-radius: 10px; border: 1px solid #e2e8f0; font-size: 0.9rem;">
                        <option value="all" <?php echo $filter == 'all' ? 'selected' : ''; ?>>All Roles</option>
                        <option value="student" <?php echo $filter == 'student' ? 'selected' : ''; ?>>Student</option>
                        <option value="teacher" <?php echo $filter == 'teacher' ? 'selected' : ''; ?>>Teacher</option>
                        <option value="parent" <?php echo $filter == 'parent' ? 'selected' : ''; ?>>Parent</option>
                        <option value="guest" <?php echo $filter == 'guest' ? 'selected' : ''; ?>>Guest</option>
                    </select>
                </form>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>User Profile</th>
                            <th>Role</th>
                            <th>Course / Department</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($u = $usersResult->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar-mini"><?php echo strtoupper(substr($u['username'], 0, 1)); ?></div>
                                        <div>
                                            <div style="font-weight: 700; color: #0f172a;"><?php echo htmlspecialchars($u['username']); ?></div>
                                            <div style="font-size: 0.8rem; color: #64748b;"><?php echo htmlspecialchars($u['email']); ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="role-badge role-<?php echo $u['role']; ?>"><?php echo ucfirst($u['role']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($u['course'] ?? 'General'); ?></td>
                                <td>
                                    <a href="dashboard.php?delete_user=<?php echo $u['id']; ?>" class="btn-delete" onclick="return confirm('Delete this account permanently?')">
                                        <i class="ph ph-trash"></i> Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal-overlay" id="createModal">
    <div class="modal-card">
        <div class="modal-header">
            <h2>Create Account</h2>
            <button class="close-btn" onclick="closeModal()">✕</button>
        </div>
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="new_username" placeholder="Full name" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div class="form-group">
                    <label>Role</label>
                    <select name="new_role" required>
                        <option value="student">Student Account</option>
                        <option value="teacher">Teacher Account</option>
                        <option value="faculty">Faculty Member</option>
                        <option value="school_admin">System Administrator</option>
                        <option value="parent">Parent Account</option>
                        <option value="guest">Guest / Hiring</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Department/Course</label>
                    <input type="text" name="new_course" placeholder="e.g. BSIT">
                </div>
            </div>
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="new_email" placeholder="user@institution.edu" required>
            </div>
            <div class="form-group">
                <label>Default Password</label>
                <input type="password" name="new_password" value="password123" required>
            </div>
            <button type="submit" name="create_user" class="btn-save">Create Account</button>
        </form>
    </div>
</div>

<script>
    function openModal() { document.getElementById('createModal').classList.add('open'); }
    function closeModal() { document.getElementById('createModal').classList.remove('open'); }
    window.onclick = function(event) {
        let modal = document.getElementById('createModal');
        if (event.target == modal) closeModal();
    }
</script>

<?php include("../includes/footer.php"); ?>
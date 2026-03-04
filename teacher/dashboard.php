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
    $new_username = trim($_POST['new_username'] ?? '');
    $new_email = trim($_POST['new_email'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $new_role = trim($_POST['new_role'] ?? '');
    $new_course = trim($_POST['new_course'] ?? 'General');

    $allowed_roles = ['student', 'teacher', 'parent', 'faculty', 'school_admin', 'registrar', 'cashier', 'librarian'];
    if ($_SESSION['role'] !== 'school_admin') {
        $flash = "Access Denied: Only School Administrators can create accounts.";
        $flashType = 'error';
    } elseif (!in_array($new_role, $allowed_roles)) {
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
            $conn->begin_transaction();
            try {
                $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, course) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $new_username, $new_email, $new_password, $new_role, $new_course);
                $stmt->execute();
                $last_user_id = $conn->insert_id;

                if ($new_role === 'student') {
                    $st_stmt = $conn->prepare("INSERT INTO students (user_id, course) VALUES (?, ?)");
                    $st_stmt->bind_param("is", $last_user_id, $new_course);
                    $st_stmt->execute();
                }

                // Handle Parent-Student Linking
                if ($new_role === 'parent' && isset($_POST['linked_student_id'])) {
                    $student_record_id = intval($_POST['linked_student_id']);
                    $p_stmt = $conn->prepare("INSERT INTO parents (user_id, student_id, parent_name) VALUES (?, ?, ?)");
                    $p_stmt->bind_param("iis", $last_user_id, $student_record_id, $new_username);
                    $p_stmt->execute();
                }

                $conn->commit();
                $flash = "Account for <strong>" . htmlspecialchars($new_username) . "</strong> created successfully as <strong>" . ucfirst($new_role) . "</strong>.";
                if ($new_role == 'teacher' || $new_role == 'student') {
                    $flash .= " Program: <strong>" . htmlspecialchars($new_course) . "</strong>.";
                }
            } catch (Exception $e) {
                $conn->rollback();
                $flash = "Database error: " . $e->getMessage();
                $flashType = 'error';
            }
        }
    }
}

// Fetch Student Profile Records for Parent Linking
$students_query = $conn->query("SELECT s.id as student_record_id, u.username FROM students s JOIN users u ON s.user_id = u.id ORDER BY u.username ASC");
$all_students = [];
while ($row = $students_query->fetch_assoc()) {
    $all_students[] = $row;
}

/* ===================================================
   ACTION: DELETE USER
   =================================================== */
if (isset($_GET['delete_user']) && is_numeric($_GET['delete_user'])) {
    $del_id = intval($_GET['delete_user']);
    if ($_SESSION['role'] !== 'school_admin') {
        $flash = "Access Denied: Only School Administrators can prune accounts.";
        $flashType = 'error';
    } elseif ($del_id !== intval($_SESSION['id'] ?? 0)) {
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
   FETCH STATS & PROGRAMS
   =================================================== */
$teacher_course = ($_SESSION['role'] === 'teacher') ? $_SESSION['course'] : '';

function countRole($conn, $role, $course = '')
{
    $where = "role='$role'";
    if (!empty($course))
        $where .= " AND course='$course'";

    if ($role == 'admin_staff') {
        $where = "role IN ('registrar', 'cashier', 'librarian', 'school_admin')";
        if (!empty($course))
            $where .= " AND course='$course'";
    }

    $r = $conn->query("SELECT COUNT(*) as c FROM users WHERE $where");
    return $r ? $r->fetch_assoc()['c'] : 0;
}

$total_students = countRole($conn, 'student', $teacher_course);
$total_teachers = countRole($conn, 'teacher', $teacher_course) + countRole($conn, 'faculty', $teacher_course);
$total_staff = countRole($conn, 'admin_staff', $teacher_course);

$programs = [
    ['name' => 'Information Technology', 'color' => '#00d2ff', 'icon' => 'ph-globe'],
    ['name' => 'Computer Science', 'color' => '#6e45e2', 'icon' => 'ph-cpu'],
    ['name' => 'Information System', 'color' => '#f59e0b', 'icon' => 'ph-database'],
    ['name' => 'Software Engineer', 'color' => '#10b981', 'icon' => 'ph-code'],
    ['name' => 'Computer Engineer', 'color' => '#ef4444', 'icon' => 'ph-circuit-board']
];

/* ===================================================
   FETCH ALL USERS
   =================================================== */
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? trim($_GET['filter']) : 'all';

$sql = "SELECT id, username, email, role, course, created_at FROM users WHERE 1=1";
$params = [];
$types = '';

// Teacher restricts directory to their program
if ($_SESSION['role'] === 'teacher' && !empty($_SESSION['course'])) {
    $sql .= " AND course = ?";
    $params[] = $_SESSION['course'];
    $types .= 's';
}

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

    .user-avatar-mini {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: .85rem;
        font-weight: 700;
        color: #fff;
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
    .role-faculty {
        background: rgba(110, 69, 226, 0.1);
        color: #6e45e2;
    }

    .role-registrar,
    .role-cashier,
    .role-librarian,
    .role-school_admin {
        background: #f1f5f9;
        color: #475569;
    }

    .prog-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 30px;
    }

    .prog-card {
        background: white;
        padding: 20px;
        border-radius: 16px;
        border: 1px solid #f1f5f9;
        transition: 0.3s;
    }

    .prog-card:hover {
        border-color: var(--primary);
        transform: translateY(-3px);
    }

    .prog-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        font-size: 1.2rem;
    }

    .prog-count {
        font-size: 1.25rem;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 2px;
    }

    .prog-name {
        font-size: 0.7rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(5px);
        z-index: 2000;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-overlay.open {
        display: flex;
    }

    .modal-card {
        background: white;
        border-radius: 24px;
        width: 100%;
        max-width: 600px;
        padding: 40px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        max-height: 90vh;
        overflow-y: auto;
    }
</style>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <?php if ($flash): ?>
            <div
                style="background: <?php echo $flashType == 'error' ? '#fee2e2' : '#dcfce7'; ?>; color: <?php echo $flashType == 'error' ? '#ef4444' : '#16a34a'; ?>; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; font-size: 0.9rem; font-weight: 600;">
                <?php echo $flash; ?>
            </div>
        <?php endif; ?>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1>Institutional Control</h1>
                <p><?php echo $_SESSION['role'] === 'school_admin' ? "Global system management and onboarding." : "Program oversight and student tracking."; ?>
                </p>
            </div>
            <?php if ($_SESSION['role'] === 'school_admin'): ?>
                <button
                    style="background: var(--primary); color: white; border: none; padding: 14px 28px; border-radius: 12px; font-weight: 700; cursor: pointer; display: flex; align-items: center; gap: 10px; box-shadow: 0 10px 15px -3px rgba(110,69,226,0.3);"
                    onclick="document.getElementById('createModal').classList.add('open')">
                    <i class="ph ph-plus-circle" style="font-size: 1.2rem;"></i> Create Account
                </button>
            <?php else: ?>
                <div
                    style="background: #f1f5f9; color: #64748b; padding: 12px 24px; border-radius: 12px; font-size: 0.85rem; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                    <i class="ph ph-shield-check"></i> Faculty Overview (Program Restricted)
                </div>
            <?php endif; ?>
        </div>

        <h3
            style="margin-bottom: 20px; font-size: 0.9rem; color: #64748b; text-transform: uppercase; letter-spacing: 1px; font-weight: 800;">
            Programs</h3>
        <div class="prog-grid">
            <?php foreach ($programs as $p):
                $p_name = $p['name'];
                $ts = $conn->query("SELECT COUNT(*) as c FROM users WHERE course='$p_name' AND role='teacher'")->fetch_assoc()['c'];
                $ss = $conn->query("SELECT COUNT(*) as c FROM users WHERE course='$p_name' AND role='student'")->fetch_assoc()['c'];
                ?>
                <div class="prog-card">
                    <div class="prog-icon"
                        style="background: <?php echo $p['color']; ?>15; color: <?php echo $p['color']; ?>;">
                        <i class="ph <?php echo $p['icon']; ?>"></i>
                    </div>
                    <div class="prog-count"><?php echo $ss; ?> <span
                            style="font-size: 0.8rem; font-weight: 400; color: #94a3b8;">/ <?php echo $ts; ?></span></div>
                    <div class="prog-name"><?php echo $p['name']; ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card-container" style="margin-bottom: 50px;">
            <div class="card" style="border-left: 4px solid #00d2ff;">
                <h3>Total Students</h3>
                <h1><?php echo $total_students; ?></h1>
                <div class="card-footer">
                    <?php echo !empty($teacher_course) ? "In $teacher_course" : "Across institution"; ?>
                </div>
            </div>
            <div class="card" style="border-left: 4px solid #6e45e2;">
                <h3>Faculty</h3>
                <h1><?php echo $total_teachers; ?></h1>
                <div class="card-footer">
                    <?php echo !empty($teacher_course) ? "In $teacher_course" : "Verified members"; ?>
                </div>
            </div>
            <div class="card" style="border-left: 4px solid #475569;">
                <h3>Institutional Staff</h3>
                <h1><?php echo $total_staff; ?></h1>
                <div class="card-footer">
                    <?php echo !empty($teacher_course) ? "In $teacher_course" : "Global offices"; ?>
                </div>
            </div>
        </div>

        <div class="table-wrapper">
            <div
                style="padding: 24px; border-bottom: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="font-size: 1.1rem; font-weight: 800; color: #0f172a;">Institutional Directory</h2>
                <form method="GET" style="display: flex; gap: 10px;">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                        placeholder="Search name..."
                        style="padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.85rem;">
                    <select name="filter" onchange="this.form.submit()"
                        style="padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0; font-size: 0.85rem;">
                        <option value="all">All Roles</option>
                        <?php foreach ($allowed_roles as $r): ?>
                            <option value="<?php echo $r; ?>" <?php echo $filter == $r ? 'selected' : ''; ?>>
                                <?php echo ucfirst($r); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Institutional Role</th>
                        <th>Program / Assignment</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($u = $usersResult->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div class="user-avatar-mini"
                                        style="background: <?php echo in_array($u['role'], ['teacher', 'faculty']) ? 'linear-gradient(135deg, #6e45e2, #a855f7)' : 'linear-gradient(135deg, #00d2ff, #3a7bd5)'; ?>;">
                                        <?php echo strtoupper(substr($u['username'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; color: #0f172a;">
                                            <?php echo htmlspecialchars($u['username']); ?>
                                        </div>
                                        <div style="font-size: 0.8rem; color: #64748b;">
                                            <?php echo htmlspecialchars($u['email']); ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td><span
                                    class="role-badge role-<?php echo $u['role']; ?>"><?php echo ucfirst(str_replace('_', ' ', $u['role'])); ?></span>
                            </td>
                            <td>
                                <div style="font-weight: 600; color: #334155;">
                                    <?php echo htmlspecialchars($u['course'] ?? 'General'); ?>
                                </div>
                            </td>
                            <td style="text-align: right;">
                                <?php if ($_SESSION['role'] === 'school_admin'): ?>
                                    <a href="dashboard.php?delete_user=<?php echo $u['id']; ?>"
                                        style="color: #ef4444; font-weight: 700; text-decoration: none; font-size: 0.85rem;"
                                        onclick="return confirm('Pruning account: continue?')">Prune Access</a>
                                <?php else: ?>
                                    <span style="color: #94a3b8; font-size: 0.85rem; font-weight: 600;">System Locked</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal-overlay" id="createModal">
    <div class="modal-card">
        <h2 style="font-weight: 800; font-size: 1.5rem; color: #0f172a; margin-bottom: 30px;">Institutional Onboarding
        </h2>
        <form method="POST">
            <div style="margin-bottom: 20px;">
                <label
                    style="display: block; font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 8px;">Assign
                    Institutional Role</label>
                <select name="new_role" id="roleSelector" required onchange="handleRoleChange()"
                    style="width: 100%; padding: 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-weight: 600;">
                    <option value="" disabled selected>Identify internal role...</option>
                    <option value="teacher">Teacher</option>
                    <option value="student">Active Student</option>
                    <option value="registrar">Registrar Office</option>
                    <option value="cashier">Treasury / Cashier</option>
                    <option value="librarian">Library Admin</option>
                    <option value="school_admin">System Administrator</option>
                    <option value="parent">Parent Portal</option>
                </select>
            </div>

            <div id="parentCont" style="display:none; margin-bottom: 20px;">
                <label
                    style="display: block; font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 8px;">Select
                    Student (Authorized Child)</label>
                <select name="linked_student_id"
                    style="width: 100%; padding: 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-weight: 600;">
                    <option value="" disabled selected>Select child account...</option>
                    <?php foreach ($all_students as $st): ?>
                        <option value="<?php echo $st['student_record_id']; ?>">
                            <?php echo htmlspecialchars($st['username']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div id="progCont" style="display:none; margin-bottom: 20px;">
                <label
                    style="display: block; font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 8px;">Program
                    Assignment</label>
                <select name="new_course"
                    style="width: 100%; padding: 14px; border: 1px solid #e2e8f0; border-radius: 12px; font-weight: 600;">
                    <option value="Information Technology">Information Technology</option>
                    <option value="Computer Science">Computer Science</option>
                    <option value="Information System">Information System</option>
                    <option value="Software Engineer">Software Engineer</option>
                    <option value="Computer Engineer">Computer Engineer</option>
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label
                    style="display: block; font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 8px;">Legal
                    Full Name</label>
                <input type="text" name="new_username" placeholder="Full name (e.g. Juan De La Cruz)" required
                    style="width: 100%; padding: 14px; border: 1px solid #e2e8f0; border-radius: 12px;">
            </div>

            <div style="margin-bottom: 20px;">
                <label
                    style="display: block; font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 8px;">Institutional
                    Email</label>
                <input type="email" name="new_email" placeholder="admin@ascend.edu" required
                    style="width: 100%; padding: 14px; border: 1px solid #e2e8f0; border-radius: 12px;">
            </div>

            <div style="margin-bottom: 40px;">
                <label
                    style="display: block; font-size: 0.85rem; font-weight: 700; color: #475569; margin-bottom: 8px;">Access
                    Key</label>
                <input type="password" name="new_password" value="ASCEND2026" required
                    style="width: 100%; padding: 14px; border: 1px solid #e2e8f0; border-radius: 12px;">
            </div>

            <div style="display: flex; gap: 15px;">
                <button type="button" onclick="document.getElementById('createModal').classList.remove('open')"
                    style="flex: 1; padding: 14px; background: #f1f5f9; border: none; border-radius: 12px; font-weight: 700; color: #64748b; cursor: pointer;">Abort</button>
                <button type="submit" name="create_user"
                    style="flex: 2; padding: 14px; background: var(--primary); border: none; border-radius: 12px; font-weight: 700; color: white; cursor: pointer;">Initialize
                    Access</button>
            </div>
        </form>
    </div>
</div>

<script>
    function handleRoleChange() {
        const role = document.getElementById('roleSelector').value;
        const progCont = document.getElementById('progCont');
        const parentCont = document.getElementById('parentCont');

        progCont.style.display = (role === 'teacher' || role === 'student') ? 'block' : 'none';
        parentCont.style.display = (role === 'parent') ? 'block' : 'none';
    }
</script>

<?php include("../includes/footer.php"); ?>
<?php
session_start();
include('../config/db.php');

// Guard: teacher/faculty/school_admin
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['teacher', 'faculty', 'school_admin'])) {
    header("Location: ../login.php?role=faculty");
    exit();
}

$role = $_SESSION['role'];
$teacher_course = $_SESSION['course'] ?? '';
$username = htmlspecialchars($_SESSION['username'] ?? 'Admin');

/* ===================================================
   ACTION: UPDATE GRADE (AJAX)
   =================================================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_grade'])) {
    if ($role === 'school_admin') {
        echo json_encode(['status' => 'error', 'message' => 'Admins cannot modify grades.']);
        exit();
    }

    $sid = intval($_POST['student_id']);
    $subj = trim($_POST['subject']);
    $val = floatval($_POST['grade']);

    // Upsert logic
    $check = $conn->prepare("SELECT id FROM grades WHERE student_id = ? AND subject = ?");
    $check->bind_param("is", $sid, $subj);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $stmt = $conn->prepare("UPDATE grades SET grade = ? WHERE student_id = ? AND subject = ?");
        $stmt->bind_param("dis", $val, $sid, $subj);
    } else {
        $stmt = $conn->prepare("INSERT INTO grades (student_id, subject, grade) VALUES (?, ?, ?)");
        $stmt->bind_param("isd", $sid, $subj, $val);
    }

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    exit();
}

// Fetch students for grading
$sql = "SELECT id, username, email, course FROM users WHERE role='student'";
$params = [];
$types = '';

if ($role === 'teacher' && !empty($teacher_course)) {
    $sql .= " AND course = ?";
    $params[] = $teacher_course;
    $types .= 's';
}
$sql .= " ORDER BY username ASC";

$stmt = $conn->prepare($sql);
if (!empty($params))
    $stmt->bind_param($types, ...$params);
$stmt->execute();
$studentsResult = $stmt->get_result();

// Mock subjects for the gradebook
$subjects = ['Web Development', 'Database Systems', 'Data Structures', 'Network Security'];

// Helper to get grade
function getGrade($conn, $sid, $subject)
{
    $q = $conn->prepare("SELECT grade FROM grades WHERE student_id = ? AND subject = ?");
    $q->bind_param("is", $sid, $subject);
    $q->execute();
    $r = $q->get_result();
    if ($r->num_rows > 0) {
        return $r->fetch_assoc()['grade'];
    }
    // Default mock if none exists
    return 1.0 + (crc32($sid . $subject) % 20) / 10;
}
?>

<?php include("../includes/header.php"); ?>

<style>
    .grade-table-wrapper {
        background: #fff;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, .05);
        margin-top: 30px;
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

    .grade-input {
        width: 60px;
        padding: 8px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        text-align: center;
        font-weight: 700;
        color: var(--primary);
        outline: none;
        transition: 0.2s;
    }

    .grade-input:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(110, 69, 226, 0.1);
    }

    .grade-input.saving {
        opacity: 0.5;
        pointer-events: none;
    }

    .save-btn {
        background: #0f172a;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 700;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: 0.3s;
    }

    .save-btn:hover {
        background: var(--primary);
        transform: translateY(-2px);
    }

    /* Success highlight */
    .save-success {
        background: #dcfce7 !important;
        transition: 1s;
    }
</style>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1>Institutional Gradebook</h1>
                <p><?php echo ($role === 'school_admin') ? "Institutional performance oversight." : "Managing academic records for $teacher_course."; ?>
                </p>
            </div>
            <?php if ($role !== 'school_admin'): ?>
                <button class="save-btn" onclick="location.reload()"><i class="ph ph-arrows-clockwise"></i> Refresh
                    Records</button>
            <?php else: ?>
                <div
                    style="background: #f1f5f9; color: #64748b; padding: 12px 24px; border-radius: 12px; font-size: 0.85rem; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                    <i class="ph ph-shield-check"></i> Audit Mode (View Only)
                </div>
            <?php endif; ?>
        </div>

        <div class="grade-table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 250px;">Student Profile</th>
                        <?php foreach ($subjects as $sub): ?>
                            <th style="text-align: center;"><?php echo $sub; ?></th>
                        <?php endforeach; ?>
                        <th style="text-align: center;">GPA</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($s = $studentsResult->fetch_assoc()): ?>
                        <tr data-sid="<?php echo $s['id']; ?>">
                            <td>
                                <div style="display: flex; align-items: center; gap: 12px;">
                                    <div
                                        style="width: 32px; height: 32px; border-radius: 8px; background: #f1f5f9; color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 0.75rem;">
                                        <?php echo strtoupper(substr($s['username'], 0, 1)); ?>
                                    </div>
                                    <div>
                                        <div style="font-weight: 700; color: #0f172a; font-size: 0.85rem;">
                                            <?php echo htmlspecialchars($s['username']); ?></div>
                                        <div style="font-size: 0.7rem; color: #94a3b8;">
                                            <?php echo htmlspecialchars($s['course']); ?></div>
                                    </div>
                                </div>
                            </td>
                            <?php
                            $sum = 0;
                            foreach ($subjects as $sub):
                                $val = getGrade($conn, $s['id'], $sub);
                                $sum += $val;
                                ?>
                                <td style="text-align: center;">
                                    <?php if ($role !== 'school_admin'): ?>
                                        <input type="number" step="0.1" min="1.0" max="5.0" class="grade-input"
                                            data-subject="<?php echo $sub; ?>" value="<?php echo number_format($val, 1); ?>"
                                            onchange="saveGrade(this)">
                                    <?php else: ?>
                                        <span style="font-weight: 800; color: #475569;"><?php echo number_format($val, 1); ?></span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                            <td style="text-align: center;">
                                <span class="gpa-badge"
                                    style="background: rgba(16, 185, 129, 0.1); color: #059669; padding: 4px 10px; border-radius: 20px; font-weight: 800; font-size: 0.85rem;">
                                    <?php echo number_format($sum / count($subjects), 2); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <?php if ($role !== 'school_admin'): ?>
            <div style="margin-top: 20px; font-size: 0.85rem; color: #64748b; font-style: italic;">
                <i class="ph ph-info"></i> Grades are automatically saved upon change.
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function saveGrade(input) {
        const row = input.closest('tr');
        const sid = row.getAttribute('data-sid');
        const subject = input.getAttribute('data-subject');
        const val = input.value;

        input.classList.add('saving');

        const formData = new FormData();
        formData.append('update_grade', '1');
        formData.append('student_id', sid);
        formData.append('subject', subject);
        formData.append('grade', val);

        fetch('grades.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                input.classList.remove('saving');
                if (data.status === 'success') {
                    input.classList.add('save-success');
                    setTimeout(() => input.classList.remove('save-success'), 1000);
                    updateGPA(row);
                } else {
                    alert('Error: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(err => {
                input.classList.remove('saving');
                console.error(err);
            });
    }

    function updateGPA(row) {
        const inputs = row.querySelectorAll('.grade-input');
        let sum = 0;
        inputs.forEach(i => sum += parseFloat(i.value) || 0);
        const gpa = (sum / inputs.length).toFixed(2);
        row.querySelector('.gpa-badge').textContent = gpa;
    }
</script>

<?php include("../includes/footer.php"); ?>
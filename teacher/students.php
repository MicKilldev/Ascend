<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['teacher', 'faculty', 'school_admin'])) {
    header("Location: ../login.php?role=faculty");
    exit();
}

$username = htmlspecialchars($_SESSION['username'] ?? 'Admin');

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

// Stats calculation
$total_count = $students->num_rows;
?>

<?php include("../includes/header.php"); ?>

<style>
    .student-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 24px;
        margin-top: 30px;
    }

    .student-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.02);
        transition: 0.3s;
        position: relative;
        overflow: hidden;
    }

    .student-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
        border-color: var(--primary);
    }

    .risk-indicator {
        position: absolute;
        top: 0;
        right: 0;
        padding: 6px 15px;
        border-radius: 0 0 0 15px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .risk-high {
        background: #fee2e2;
        color: #ef4444;
    }

    .risk-normal {
        background: #dcfce7;
        color: #16a34a;
    }

    .student-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .avatar-circle {
        width: 48px;
        height: 48px;
        border-radius: 15px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: var(--primary);
        border: 2px solid #e2e8f0;
    }

    .student-meta h4 {
        font-size: 1rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.2;
    }

    .student-meta p {
        font-size: 0.8rem;
        color: #64748b;
        margin: 0;
    }

    .data-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        background: #f8fafc;
        padding: 15px;
        border-radius: 12px;
    }

    .data-item label {
        display: block;
        font-size: 0.65rem;
        font-weight: 700;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }

    .data-item span {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1e293b;
    }
</style>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>

    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1>Student Directory</h1>
                <p>Monitoring academic excellence across all disciplines.</p>
            </div>
            <div
                style="background: white; padding: 8px 18px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); font-weight: 700;">
                <span style="color: #64748b; font-size: 0.8rem; text-transform: uppercase;">Total Students:</span>
                <span style="color: var(--primary); margin-left: 5px;"><?php echo $total_count; ?></span>
            </div>
        </div>

        <form method="GET" style="margin-bottom: 30px;">
            <div style="position: relative; max-width: 400px;">
                <i class="ph ph-magnifying-glass"
                    style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search by name, course, or ID..."
                    style="width: 100%; padding: 12px 15px 12px 45px; border-radius: 12px; border: 1px solid #e2e8f0; outline: none; font-family: inherit;">
            </div>
        </form>

        <div class="student-grid">
            <?php while ($s = $students->fetch_assoc()):
                $mockGPA = number_format(1 + (crc32($s['id']) % 200) / 100, 2);
                $isAtRisk = ($mockGPA > 2.5); // Mock logic
                ?>
                <div class="student-card">
                    <div class="risk-indicator <?php echo $isAtRisk ? 'risk-high' : 'risk-normal'; ?>">
                        <?php echo $isAtRisk ? 'At Risk' : 'Excellent'; ?>
                    </div>

                    <div class="student-header">
                        <div class="avatar-circle"><?php echo strtoupper(substr($s['username'], 0, 1)); ?></div>
                        <div class="student-meta">
                            <h4><?php echo htmlspecialchars($s['username']); ?></h4>
                            <p><?php echo htmlspecialchars($s['email']); ?></p>
                        </div>
                    </div>

                    <div class="data-grid">
                        <div class="data-item">
                            <label>Course</label>
                            <span><?php echo htmlspecialchars($s['course'] ?? 'BSIT'); ?></span>
                        </div>
                        <div class="data-item">
                            <label>GPA</label>
                            <span
                                style="color: <?php echo $isAtRisk ? '#ef4444' : '#16a34a'; ?>;"><?php echo $mockGPA; ?></span>
                        </div>
                        <div class="data-item">
                            <label>Attendance</label>
                            <span><?php echo 85 + (crc32($s['id']) % 15); ?>%</span>
                        </div>
                        <div class="data-item">
                            <label>Stability</label>
                            <span><?php echo $isAtRisk ? 'Declining' : 'Stable'; ?></span>
                        </div>
                    </div>

                    <button
                        style="width: 100%; margin-top: 20px; padding: 10px; border: 1px solid #e2e8f0; border-radius: 10px; background: white; font-weight: 700; color: #64748b; cursor: pointer; transition: 0.2s;"
                        onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='white'">
                        View Analytics →
                    </button>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
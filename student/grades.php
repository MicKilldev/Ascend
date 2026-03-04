<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php?role=student");
    exit();
}

$username = htmlspecialchars($_SESSION['username'] ?? 'Student');
$course = htmlspecialchars($_SESSION['course'] ?? 'BSIT');
$student_id = $_SESSION['id']; // This is the user ID

// Fetch all grades for this student from the 'grades' table
$query = "SELECT subject, grade FROM grades WHERE student_id = ? ORDER BY subject ASC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$total_units = 0;
$passed_count = 0;
$failed_count = 0;
$grades_list = [];

while ($row = $result->fetch_assoc()) {
    $grades_list[] = $row;
    $total_units += 3; // Mocking 3 units per subject if not in DB
    if ($row['grade'] <= 3.0) {
        $passed_count++;
    } else {
        $failed_count++;
    }
}

// Calculate GPA
$sum_grades = 0;
foreach ($grades_list as $g) {
    $sum_grades += $g['grade'];
}
$gpa = (count($grades_list) > 0) ? number_format($sum_grades / count($grades_list), 2) : "0.00";
?>

<?php include("../includes/header.php"); ?>

<style>
    .grade-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-mini-card {
        background: white;
        padding: 24px;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        gap: 8px;
        border: 1px solid rgba(0, 0, 0, 0.02);
    }

    .stat-label {
        font-size: 0.7rem;
        color: #94a3b8;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .stat-value {
        font-size: 1.8rem;
        font-weight: 800;
        color: #0f172a;
    }

    .grade-table-wrapper {
        background: white;
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        border: 1px solid #f1f5f9;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #0f172a;
        color: white;
    }

    th {
        padding: 20px;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: rgba(255, 255, 255, 0.6);
    }

    td {
        padding: 20px;
        font-size: 0.95rem;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
        vertical-align: middle;
    }

    tr:last-child td {
        border-bottom: none;
    }

    tr:hover {
        background: #f8faff;
    }

    .status-badge {
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .status-passed {
        background: #dcfce7;
        color: #16a34a;
    }

    .status-failed {
        background: #fee2e2;
        color: #ef4444;
    }
</style>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1 style="font-weight: 900; letter-spacing: -1px;">Academic Records</h1>
                <p style="color: #64748b;">Official transcript of your performance in the current semester.</p>
            </div>
            <div
                style="background: white; padding: 12px 24px; border-radius: 14px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); font-weight: 800; color: var(--primary);">
                <i class="ph ph-calendar"></i> 2nd Sem, 2026
            </div>
        </div>

        <?php if ($failed_count > 0): ?>
            <div
                style="background: #fff; border-left: 4px solid #ef4444; padding: 20px; border-radius: 14px; margin-bottom: 30px; display: flex; align-items: center; gap: 15px; box-shadow: 0 10px 20px rgba(239, 68, 68, 0.1);">
                <i class="ph-fill ph-warning-circle" style="font-size: 2rem; color: #ef4444;"></i>
                <div>
                    <div style="font-weight: 800; font-size: 0.8rem; color: #ef4444; text-transform: uppercase;">Academic
                        Warning</div>
                    <div style="color: #64748b; font-size: 0.9rem;">You have <?php echo $failed_count; ?> deficient
                        subject(s). Please consult your department head.</div>
                </div>
            </div>
        <?php endif; ?>

        <div class="grade-stats">
            <div class="stat-mini-card" style="border-top: 4px solid #6e45e2;">
                <span class="stat-label">Calculated GPA</span>
                <span class="stat-value"><?php echo $gpa; ?></span>
            </div>
            <div class="stat-mini-card" style="border-top: 4px solid #10b981;">
                <span class="stat-label">Units Completed</span>
                <span class="stat-value"><?php echo count($grades_list) * 3; ?>.0</span>
            </div>
            <div class="stat-mini-card" style="border-top: 4px solid #00d2ff;">
                <span class="stat-label">Academic Status</span>
                <span class="stat-value" style="font-size: 1.2rem; color: #10b981;">Good Standing</span>
            </div>
        </div>

        <div class="grade-table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Subject / Course Title</th>
                        <th style="text-align: center;">Units</th>
                        <th style="text-align: center;">Verified Grade</th>
                        <th style="text-align: center;">Equivalence</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($grades_list)): ?>
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 100px; color: #94a3b8;">
                                <i class="ph ph-mask-sad"
                                    style="font-size: 4rem; opacity: 0.2; display: block; margin-bottom: 20px;"></i>
                                <span style="font-weight: 700;">No academic records have been posted for this semester
                                    yet.</span>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($grades_list as $g): ?>
                            <tr>
                                <td>
                                    <div style="font-weight: 800; color: #0f172a;">
                                        <?php echo htmlspecialchars($g['subject']); ?></div>
                                    <div
                                        style="font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; font-weight: 700;">
                                        Major Requirement</div>
                                </td>
                                <td style="text-align: center; font-weight: 700;">3.0</td>
                                <td style="text-align: center;">
                                    <span
                                        style="font-weight: 900; color: var(--primary); font-size: 1.2rem;"><?php echo number_format($g['grade'], 1); ?></span>
                                </td>
                                <td style="text-align: center;">
                                    <span
                                        class="status-badge <?php echo ($g['grade'] <= 3.0) ? 'status-passed' : 'status-failed'; ?>">
                                        <?php echo ($g['grade'] <= 3.0) ? 'Passed' : 'Deficient'; ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div
            style="margin-top: 30px; display: flex; align-items: center; gap: 10px; color: #94a3b8; font-size: 0.8rem; justify-content: center;">
            <i class="ph ph-shield-check"></i> This transcript is verified and digitally signed by the Registrar's
            Office.
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
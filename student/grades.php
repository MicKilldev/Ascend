<?php
session_start();
include('../config/db.php');

$username = htmlspecialchars($_SESSION['username'] ?? 'Student');
$course = htmlspecialchars($_SESSION['course'] ?? 'BSIT');
$student_id = $_SESSION['user_id'];

// Fetch all grades for this student
$query = "SELECT * FROM student_grades WHERE student_id = ? ORDER BY id DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$total_credits = 0;
$passed_credits = 0;
$failed_count = 0;
$grades_list = [];

while ($row = $result->fetch_assoc()) {
    $grades_list[] = $row;
    $total_credits += $row['credits'];
    if ($row['status'] === 'Passed') {
        $passed_credits += $row['credits'];
    } else {
        $failed_count++;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ascend | My Grades</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@phosphor-icons/web"></script>
    <style>
        /* ===== BASE STYLES (MATCHING DASHBOARD) ===== */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background: #e8ecf0; display: flex; min-height: 100vh; color: #1a1a2e; }

        /* ===== SIDEBAR & TOPBAR (EXACT MATCH) ===== */
        .sidebar { width: 240px; min-height: 100vh; background: #0f1624; display: flex; flex-direction: column; position: fixed; top: 0; left: 0; z-index: 100; }
        .sidebar-user { display: flex; align-items: center; gap: 12px; padding: 28px 20px 24px; border-bottom: 1px solid rgba(255, 255, 255, 0.06); }
        .user-avatar { width: 42px; height: 42px; border-radius: 50%; background: linear-gradient(135deg, #00d2ff, #6e45e2); display: flex; align-items: center; justify-content: center; font-size: 1rem; font-weight: 700; color: #fff; }
        .user-info { flex: 1; }
        .user-name { font-size: 0.9rem; font-weight: 600; color: #fff; line-height: 1.2; }
        .user-course { font-size: 0.72rem; color: rgba(255, 255, 255, 0.4); }
        .sidebar-nav { flex: 1; padding: 18px 0; }
        .nav-item { display: flex; align-items: center; gap: 13px; padding: 13px 20px; color: rgba(255, 255, 255, 0.45); text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: all 0.2s; border-left: 3px solid transparent; }
        .nav-item:hover { color: #fff; background: rgba(255, 255, 255, 0.04); }
        .nav-item.active { color: #fff; background: rgba(0, 210, 255, 0.08); border-left-color: #00d2ff; }
        .nav-item.active i { color: #00d2ff; }
        .sidebar-footer { padding: 20px; border-top: 1px solid rgba(255, 255, 255, 0.06); }
        .sidebar-footer a { display: flex; align-items: center; gap: 10px; color: rgba(255, 255, 255, 0.4); text-decoration: none; font-size: 0.85rem; padding: 8px 0; }

        .main-area { margin-left: 240px; flex: 1; display: flex; flex-direction: column; }
        .topbar { display: flex; align-items: center; justify-content: space-between; padding: 24px 36px; background: #e8ecf0; }
        .topbar-title { display: flex; align-items: center; gap: 14px; }
        .topbar-accent { width: 4px; height: 40px; background: #00d2ff; border-radius: 4px; }
        .topbar-title h1 { font-size: 1.8rem; font-weight: 800; color: #0f1624; line-height: 1; }
        .topbar-title span { display: block; font-size: 0.72rem; font-weight: 700; color: #00d2ff; letter-spacing: 1.5px; text-transform: uppercase; }

        /* ===== GRADES PAGE CONTENT ===== */
        .page-content { padding: 0 36px 36px; }
        .section-label { display: inline-flex; align-items: center; background: #00d2ff; color: #0f1624; font-size: 0.78rem; font-weight: 700; letter-spacing: 1.5px; text-transform: uppercase; padding: 7px 20px; border-radius: 6px 6px 0 0; }
        
        /* Summary Header using Dashboard Blue Gradient */
        .status-card { 
            background: linear-gradient(135deg, #45b6e8 0%, #1a7bc4 40%, #0d5a9e 100%); 
            border-radius: 0 16px 16px 16px; 
            padding: 28px; 
            display: grid; 
            grid-template-columns: repeat(3, 1fr); 
            gap: 24px; 
            margin-bottom: 36px; 
            box-shadow: 0 10px 30px rgba(0, 130, 200, 0.25); 
        }

        .stat-item { background: rgba(255, 255, 255, 0.12); padding: 20px; border-radius: 12px; text-align: center; color: white; }
        .stat-val { font-size: 2rem; font-weight: 800; display: block; }
        .stat-lbl { font-size: 0.7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; }

        /* Table Card */
        .grade-table-card { background: white; border-radius: 18px; overflow: hidden; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06); }
        table { width: 100%; border-collapse: collapse; }
        th { background: #0f1624; color: white; padding: 18px 20px; text-align: left; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; }
        td { padding: 18px 20px; border-bottom: 1px solid #f1f5f9; font-size: 0.9rem; color: #1a1a2e; }
        tr:last-child td { border-bottom: none; }

        /* Badges/Pills */
        .status-pill { padding: 5px 14px; border-radius: 30px; font-weight: 700; font-size: 0.7rem; text-transform: uppercase; }
        .status-passed { background: #dcfce7; color: #166534; }
        .status-failed { background: #fee2e2; color: #991b1b; }

        /* Warning (Dashboard style) */
        .at-risk-box { background: #fff; border-left: 4px solid #f59e0b; padding: 15px 20px; border-radius: 12px; margin-bottom: 25px; display: flex; align-items: center; gap: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
        .at-risk-box i { font-size: 1.5rem; color: #f59e0b; }
    </style>
</head>

<body>
    <aside class="sidebar">
        <div class="sidebar-user">
            <div class="user-avatar"><?php echo strtoupper(substr($username, 0, 1)); ?></div>
            <div class="user-info">
                <div class="user-name"><?php echo $username; ?></div>
                <div class="user-course"><?php echo $course; ?></div>
            </div>
            <i class="ph ph-bell bell-icon"></i>
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="nav-item"><i class="ph-fill ph-squares-four"></i> Dashboard</a>
            <a href="grades.php" class="nav-item active"><i class="ph-fill ph-chart-bar"></i> Grades</a>
            <a href="portfolio.php" class="nav-item"><i class="ph ph-user-circle"></i> Portfolio</a>
            <a href="badges.php" class="nav-item"><i class="ph ph-trophy"></i> Badge</a>
            <a href="progress.php" class="nav-item"><i class="ph ph-squares-four"></i> Progress Tracker</a>
        </nav>
        <div class="sidebar-footer">
            <a href="settings.php"><i class="ph ph-gear"></i> Setting</a>
            <a href="../logout.php" class="logout" style="color:#f87171;"><i class="ph ph-sign-out"></i> Log Out</a>
        </div>
    </aside>

    <div class="main-area">
        <div class="topbar">
            <div class="topbar-title">
                <div class="topbar-accent"></div>
                <div>
                    <h1>Academic Records</h1>
                    <span>Student Grades</span>
                </div>
            </div>
            <div class="topbar-brand">ASCEND</div>
        </div>

        <div class="page-content">
            
            <?php if ($failed_count > 0): ?>
            <div class="at-risk-box">
                <i class="ph-fill ph-warning-circle"></i>
                <div>
                    <div style="font-weight:800; font-size:0.75rem; color:#92400e; text-transform:uppercase;">Academic Warning</div>
                    <div style="font-size:0.85rem; color:#78350f;">You have <?php echo $failed_count; ?> failed subject(s). Please check with your advisor.</div>
                </div>
            </div>
            <?php endif; ?>

            <div class="section-label">Summary</div>
            <div class="status-card">
                <div class="stat-item">
                    <span class="stat-val"><?php echo $total_credits; ?></span>
                    <span class="stat-lbl">Units Taken</span>
                </div>
                <div class="stat-item">
                    <span class="stat-val"><?php echo $passed_credits; ?></span>
                    <span class="stat-lbl">Units Passed</span>
                </div>
                <div class="stat-item">
                    <span class="stat-val"><?php echo ($total_credits > 0) ? round(($passed_credits/$total_credits)*100) : 0; ?>%</span>
                    <span class="stat-lbl">Completion</span>
                </div>
            </div>

            <div class="section-label">Transcript</div>
            <div class="grade-table-card">
                <table>
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Subject</th>
                            <th>Units</th>
                            <th>Average</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($grades_list)): ?>
                            <tr><td colspan="6" style="text-align:center; padding: 40px; color:#64748b;">No grades posted yet.</td></tr>
                        <?php else: ?>
                            <?php foreach ($grades_list as $g): ?>
                            <tr>
                                <td style="font-weight: 700; color: #0f1624;"><?php echo htmlspecialchars($g['subject_code']); ?></td>
                                <td style="font-weight: 700; color: #0f1624;"><?php echo htmlspecialchars($g['subject_name']); ?></td>
                                <td><?php echo $g['credits']; ?></td>
                                <td style="font-weight: 800; color: #00d2ff;"><?php echo number_format($g['average_grade'], 2); ?></td>
                                <td>
                                    <span class="status-pill <?php echo ($g['status'] == 'Passed') ? 'status-passed' : 'status-failed'; ?>">
                                        <?php echo $g['status']; ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
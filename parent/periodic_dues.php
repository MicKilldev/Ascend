<?php
session_start();
include('../config/db.php');
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'parent') {
    header("Location: ../login.php?role=parent");
    exit();
}

$parent_user_id = $_SESSION['id'];
$stmt = $conn->prepare("
    SELECT u.username as student_name, u.course, s.student_number, s.year_level 
    FROM parents p
    JOIN users u ON p.student_id = u.id
    LEFT JOIN students s ON u.id = s.user_id
    WHERE p.user_id = ?
");
$stmt->bind_param("i", $parent_user_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();

$student_name = $student ? $student['student_name'] : "Mickael Garde";
$student_number = ($student && $student['student_number']) ? $student['student_number'] : "24-1133-954";
$course = ($student && $student['course']) ? $student['course'] : "Information Technology";
$year_level = ($student && $student['year_level']) ? $student['year_level'] : "2";
$course_yr = $course . ' - ' . $year_level;
?>
<?php include("../includes/header.php"); ?>

<style>
    .schedule-table-wrapper {
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

    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 5px 14px;
        border-radius: 30px;
        font-size: .75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .status-paid {
        background: rgba(16, 185, 129, 0.1);
        color: #059669;
    }

    .status-overdue {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }

    .status-upcoming {
        background: rgba(245, 158, 11, 0.1);
        color: #d97706;
    }

    .header-info-box {
        background: white;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.02);
        margin-top: 20px;
    }

    .header-info-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .info-label {
        font-size: 0.75rem;
        color: #64748b;
        text-transform: uppercase;
        font-weight: 700;
    }

    .info-val {
        font-size: 1rem;
        color: #0f172a;
        font-weight: 800;
    }

    .payment-action-btn {
        background: var(--primary);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 700;
        transition: transform 0.2s;
    }

    .payment-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 210, 255, 0.3);
    }
</style>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 10px;">
            <div>
                <h1>Payment Schedule</h1>
                <p>Track mandatory periodic dues and cumulative balances for the current semester.</p>
            </div>

            <button
                style="background: #0f172a; color: white; border: none; padding: 10px 20px; border-radius: 12px; cursor: pointer; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="ph ph-printer"></i> Print Schedule
            </button>
        </div>

        <div class="header-info-box">
            <h3
                style="margin-bottom: 15px; font-size: 1rem; color: #0f172a; border-bottom: 2px dashed #f1f5f9; padding-bottom: 15px;">
                Second Term, 2025 - 26 <span style="float: right; font-weight: 800; color: #10b981;">Status:
                    Enrolled</span>
            </h3>
            <div class="header-info-grid">
                <div class="info-item">
                    <span class="info-label">Student ID</span>
                    <span class="info-val"><?php echo htmlspecialchars($student_number); ?></span>
                </div>
                <div class="info-item" style="grid-column: span 3;">
                    <span class="info-label">Student Name</span>
                    <span class="info-val"><?php echo strtoupper(htmlspecialchars($student_name)); ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label">Last Enrolled SY-Term</span>
                    <span class="info-val">Second Term, 2025 - 26</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Last Enrolled Course-Yr</span>
                    <span class="info-val"><?php echo strtoupper(htmlspecialchars($course_yr)); ?></span>
                </div>
            </div>
        </div>

        <div class="schedule-table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th style="width: 25%;">Payment Schedule</th>
                        <th style="text-align: right; width: 25%;">Required Amount</th>
                        <th style="text-align: right; width: 25%;">Cumulative Balance</th>
                        <th style="text-align: center; width: 25%;">Status & Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="font-weight: 700; color: #0f172a;">Prelim</td>
                        <td style="text-align: right; font-variant-numeric: tabular-nums;">12,661.79</td>
                        <td style="text-align: right; font-variant-numeric: tabular-nums;">12,661.79</td>
                        <td style="text-align: center;">
                            <span class="status-badge status-paid">Paid</span>
                        </td>
                    </tr>
                    <tr style="background: rgba(239, 68, 68, 0.03);">
                        <td style="font-weight: 700; color: #0f172a;">Midterm</td>
                        <td style="text-align: right; font-variant-numeric: tabular-nums;">12,661.79</td>
                        <td style="text-align: right; font-variant-numeric: tabular-nums;">25,323.59</td>
                        <td style="text-align: center;">
                            <div style="display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                <span class="status-badge status-overdue" style="animation: pulse 2s infinite;">Past
                                    Due</span>
                                <button class="payment-action-btn" style="font-size: 0.75rem; padding: 6px 12px;">Pay
                                    Now</button>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700; color: #0f172a;">Finals</td>
                        <td style="text-align: right; font-variant-numeric: tabular-nums;">12,661.79</td>
                        <td style="text-align: right; font-variant-numeric: tabular-nums;">37,985.38</td>
                        <td style="text-align: center;">
                            <span class="status-badge status-upcoming">Upcoming</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            style="margin-top: 15px; font-size: 0.8rem; color: #64748b; background: white; padding: 15px; border-radius: 12px; border-left: 4px solid var(--primary);">
            <i class="ph ph-info"></i> <strong>Note:</strong> The required amount per term is automatically calculated
            by dividing the total assessed fees minus the baseline downpayment. Balances accumulate periodically. Ensure
            on-time payments to avoid examination delays.
        </div>
    </div>
</div>

<style>
    @keyframes pulse {
        0% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.4);
        }

        70% {
            transform: scale(1.05);
            box-shadow: 0 0 0 6px rgba(239, 68, 68, 0);
        }

        100% {
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(239, 68, 68, 0);
        }
    }
</style>

<?php include("../includes/footer.php"); ?>
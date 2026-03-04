<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'parent') {
    header("Location: ../login.php?role=parent");
    exit();
}

$parent_user_id = $_SESSION['id'];

// Initializing the connected student
$stmt = $conn->prepare("SELECT u.id, u.username as student_name, u.course 
                        FROM parents p
                        JOIN users u ON p.student_id = u.id
                        WHERE p.user_id = ?");
$stmt->bind_param("i", $parent_user_id);
$stmt->execute();
$res = $stmt->get_result();
$student = $res->fetch_assoc();

if (!$student) {
    // Fallback for demo
    $student_name = "Mickael Garde";
    $student_id = 1;
    $course = "Information Technology";
} else {
    $student_name = $student['student_name'];
    $student_id = $student['id'];
    $course = $student['course'];
}

// Balance data (Mocked but connected to student)
$total_assessed = 52000;
$total_paid = 30000;
$outstanding = $total_assessed - $total_paid;
?>

<?php include("../includes/header.php"); ?>

<style>
    .ledger-table-wrapper {
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

    .financial-card {
        background: white;
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.02);
        display: flex;
        flex-direction: column;
        gap: 10px;
        position: relative;
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

    .status-pending {
        background: rgba(245, 158, 11, 0.1);
        color: #d97706;
    }
</style>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;">
            <div>
                <h1>Student Ledger</h1>
                <p>Consolidated financial records for <strong><?php echo $student_name; ?></strong>
                    (<?php echo $course; ?>).</p>
            </div>
            <button
                style="background: var(--primary); color: white; border: none; padding: 12px 24px; border-radius: 12px; cursor: pointer; font-weight: 700;">Statement
                of Account</button>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
            <div class="financial-card">
                <h3>Total Assessed Fees</h3>
                <h1>₱<?php echo number_format($total_assessed, 2); ?></h1>
                <div style="font-size: 0.75rem; font-weight: 600; color: #94a3b8; margin-top: auto;">Current Semester
                    Portfolio</div>
            </div>
            <div class="financial-card">
                <h3>Total Payments</h3>
                <h1 style="color: #10b981;">₱<?php echo number_format($total_paid, 2); ?></h1>
                <div style="font-size: 0.75rem; font-weight: 600; color: #94a3b8; margin-top: auto;">Institutional
                    Receipts Verified</div>
            </div>
            <div class="financial-card" style="border-bottom: 4px solid #ef4444;">
                <h3>Outstanding Balance</h3>
                <h1 style="color: #ef4444;">₱<?php echo number_format($outstanding, 2); ?></h1>
                <div style="font-size: 0.75rem; font-weight: 600; color: #94a3b8; margin-top: auto;">Required Before
                    Finals</div>
            </div>
        </div>

        <div class="ledger-table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description / Details</th>
                        <th>Debit (Charges)</th>
                        <th>Credit (Payments)</th>
                        <th>Balance</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="color: #64748b; font-size: 0.8rem;">Jan 10, 2026</td>
                        <td>
                            <div style="font-weight: 700; color: #0f172a;">Enrollment Assessment - Term 1</div>
                            <div style="font-size: 0.8rem; color: #94a3b8;">ASM-2026-T1-092</div>
                        </td>
                        <td style="font-weight: 700; color: #ef4444;">₱26,000.00</td>
                        <td>-</td>
                        <td style="font-weight: 700;">₱26,000.00</td>
                        <td><span class="status-badge status-pending">Billed</span></td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; font-size: 0.8rem;">Jan 15, 2026</td>
                        <td>
                            <div style="font-weight: 700; color: #0f172a;">Term 1 Downpayment</div>
                            <div style="font-size: 0.8rem; color: #94a3b8;">OR-2026-1184 Verified</div>
                        </td>
                        <td>-</td>
                        <td style="font-weight: 700; color: #10b981;">₱15,000.00</td>
                        <td style="font-weight: 700;">₱11,000.00</td>
                        <td><span class="status-badge status-paid">Cleared</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
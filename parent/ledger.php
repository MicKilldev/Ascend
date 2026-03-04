<?php
session_start();
include('../config/db.php');
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'parent') {
    header("Location: ../login.php?role=parent");
    exit();
}
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

    .financial-card h3 {
        font-size: 0.85rem;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    .financial-card h1 {
        font-size: 2rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
    }

    .term-breakdown {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }

    .term-box {
        background: #f8fafc;
        border-radius: 16px;
        padding: 20px;
        border: 1px solid #e2e8f0;
    }

    .term-box h4 {
        font-size: 1rem;
        color: #0f172a;
        font-weight: 800;
        display: flex;
        justify-content: space-between;
        border-bottom: 2px dashed #cbd5e1;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .fee-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
        margin-bottom: 8px;
        color: #334155;
    }

    .fee-label {
        font-weight: 600;
        color: #64748b;
    }
</style>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;">
            <div>
                <h1>Student Ledger</h1>
                <p>2nd Semester, Academic Year 2025-2026 (Consolidated Terms)</p>
            </div>

            <button
                style="background: var(--primary); color: white; border: none; padding: 10px 20px; border-radius: 12px; cursor: pointer; font-weight: 700; display: flex; align-items: center; gap: 8px;">
                <i class="ph ph-download-simple"></i> Statement of Account
            </button>
        </div>

        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 30px;">
            <div class="financial-card">
                <h3>Total Assessed Fees</h3>
                <h1>₱52,000.00</h1>
                <div style="font-size: 0.75rem; font-weight: 600; color: #94a3b8; margin-top: auto;">Term 1 + Term 2
                </div>
            </div>
            <div class="financial-card">
                <h3>Total Payments</h3>
                <h1 style="color: #10b981;">₱30,000.00</h1>
                <div style="font-size: 0.75rem; font-weight: 600; color: #94a3b8; margin-top: auto;">Applied to 2nd
                    Semester</div>
            </div>
            <div class="financial-card" style="border-bottom: 4px solid #ef4444;">
                <h3>Outstanding Balance</h3>
                <h1 style="color: #ef4444;">₱22,000.00</h1>
                <div style="font-size: 0.75rem; font-weight: 600; color: #94a3b8; margin-top: auto;">To be settled
                    before Final Exams</div>
            </div>
        </div>

        <h3 style="margin-bottom: 15px; font-size: 1.1rem; color: #0f172a;"><i class="ph ph-calculator"
                style="color: #a855f7;"></i> Semester Assessment Breakdown</h3>
        <p style="font-size: 0.85rem; color: #64748b; margin-bottom: 15px;">Note: Your school operates on a two-term
            system per semester. The ledger balance reflects the total for the entire semester, regardless of the term
            subjects.</p>

        <div class="term-breakdown">
            <div class="term-box">
                <h4><span>Term 1 Assessment</span> <span style="color: #6e45e2;">₱26,000.00</span></h4>
                <div class="fee-row"><span class="fee-label">Tuition Fee (12 units)</span> <span>₱18,000.00</span></div>
                <div class="fee-row"><span class="fee-label">Laboratory Fee (ComProg 2)</span> <span>₱3,500.00</span>
                </div>
                <div class="fee-row"><span class="fee-label">Miscellaneous Fees</span> <span>₱4,500.00</span></div>
            </div>
            <div class="term-box">
                <h4><span>Term 2 Assessment</span> <span style="color: #6e45e2;">₱26,000.00</span></h4>
                <div class="fee-row"><span class="fee-label">Tuition Fee (12 units)</span> <span>₱18,000.00</span></div>
                <div class="fee-row"><span class="fee-label">Laboratory Fee (Web Dev)</span> <span>₱3,500.00</span>
                </div>
                <div class="fee-row"><span class="fee-label">Miscellaneous Fees</span> <span>₱4,500.00</span></div>
            </div>
        </div>

        <h3 style="margin-bottom: 15px; font-size: 1.1rem; color: #0f172a;"><i class="ph ph-receipt"
                style="color: var(--primary);"></i> Consolidated Transaction History</h3>

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
                            <div style="font-size: 0.8rem; color: #94a3b8;">Ref: ASM-2026-T1-092</div>
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
                            <div style="font-size: 0.8rem; color: #94a3b8;">Ref: OR-2026-1184 Online Transfer</div>
                        </td>
                        <td>-</td>
                        <td style="font-weight: 700; color: #10b981;">₱15,000.00</td>
                        <td style="font-weight: 700;">₱11,000.00</td>
                        <td><span class="status-badge status-paid">Cleared</span></td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; font-size: 0.8rem;">Mar 01, 2026</td>
                        <td>
                            <div style="font-weight: 700; color: #0f172a;">Enrollment Assessment - Term 2</div>
                            <div style="font-size: 0.8rem; color: #94a3b8;">Ref: ASM-2026-T2-092</div>
                        </td>
                        <td style="font-weight: 700; color: #ef4444;">₱26,000.00</td>
                        <td>-</td>
                        <td style="font-weight: 700;">₱37,000.00</td>
                        <td><span class="status-badge status-pending">Billed</span></td>
                    </tr>
                    <tr>
                        <td style="color: #64748b; font-size: 0.8rem;">Mar 05, 2026</td>
                        <td>
                            <div style="font-weight: 700; color: #0f172a;">Term 2 Downpayment</div>
                            <div style="font-size: 0.8rem; color: #94a3b8;">Ref: OR-2026-2241 Over-the-counter</div>
                        </td>
                        <td>-</td>
                        <td style="font-weight: 700; color: #10b981;">₱15,000.00</td>
                        <td style="font-weight: 700;">₱22,000.00</td>
                        <td><span class="status-badge status-paid">Cleared</span></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php include("../includes/footer.php"); ?>
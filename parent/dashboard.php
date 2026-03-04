<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'parent') {
    header("Location: ../login.php?role=parent");
    exit();
}
?>

<?php include("../includes/header.php"); ?>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>

    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
            <div>
                <h1>Parent Dashboard</h1>
                <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?> 👋</p>
            </div>
        </div>

        <h3 style="margin-bottom: 15px; font-size: 1.1rem; color: #0f172a;"><i class="ph ph-graduation-cap"
                style="color: var(--primary);"></i> Academic Overview</h3>
        <div class="card-container" style="margin-bottom: 30px;">
            <div class="card">
                <h3>Child's GPA</h3>
                <h1>1.50</h1>
                <div class="card-footer" style="color: #10b981; font-weight: 600;">✓ Good Standing</div>
            </div>
            <div class="card">
                <h3>Attendance Rate</h3>
                <h1>94%</h1>
                <div class="card-footer">This semester</div>
            </div>
            <div class="card">
                <h3>Recent Achievement</h3>
                <div class="badge-item" style="display: flex; gap: 10px; align-items: center; margin-top: 10px;">
                    <span style="font-size: 2rem;">🏅</span>
                    <div>
                        <div style="font-weight: 700; font-size: 0.9rem;">Academic Excellence</div>
                        <div style="font-size: 0.75rem; color: #94a3b8;">Awarded Feb 15</div>
                    </div>
                </div>
            </div>
        </div>

        <h3 style="margin-bottom: 15px; font-size: 1.1rem; color: #0f172a;"><i class="ph ph-wallet"
                style="color: #ef4444;"></i> Financial Oversight</h3>
        <div class="card-container">
            <div class="card" style="border-left: 4px solid #ef4444; position: relative;">
                <a href="ledger.php"
                    style="position: absolute; top: 15px; right: 15px; color: #ef4444; font-size: 1.5rem;"><i
                        class="ph ph-arrow-circle-right"></i></a>
                <h3 style="color: #64748b;">Outstanding Balance</h3>
                <h1 style="color: #ef4444; margin-bottom: 5px;">₱20,500.00</h1>
                <div style="font-size: 0.8rem; font-weight: 600; color: #94a3b8;">As of 1st Semester, 2026</div>
            </div>

            <div class="card" style="border-left: 4px solid #d97706; position: relative;">
                <a href="periodic_dues.php"
                    style="position: absolute; top: 15px; right: 15px; color: #d97706; font-size: 1.5rem;"><i
                        class="ph ph-arrow-circle-right"></i></a>
                <h3 style="color: #64748b;">Next Payment Due</h3>
                <h1 style="color: #0f172a; margin-bottom: 5px;">Midterms</h1>
                <div style="font-size: 0.8rem; font-weight: 600; color: #ef4444;">Past Due (Oct 20) — ₱10,500.00</div>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
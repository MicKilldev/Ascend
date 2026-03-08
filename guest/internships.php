<?php
session_start();
include('../config/db.php');
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'guest') {
    header("Location: ../login.php?role=guest");
    exit();
}
?>
<?php include("../includes/header.php"); ?>

<style>
    .internship-banner {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        border-radius: 24px;
        padding: 40px;
        color: white;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }

    .banner-content {
        position: relative;
        z-index: 2;
    }

    .banner-bg-icon {
        position: absolute;
        right: -20px;
        bottom: -20px;
        font-size: 10rem;
        opacity: 0.05;
        z-index: 1;
    }

    .slots-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
    }

    .slot-card {
        background: white;
        padding: 24px;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .slot-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .company-logo {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: var(--primary);
        border: 2px solid #e2e8f0;
    }

    .status-tag {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .open {
        background: #dcfce7;
        color: #16a34a;
    }

    .filled {
        background: #f1f5f9;
        color: #64748b;
    }

    .action-row {
        display: flex;
        gap: 10px;
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px dashed #e2e8f0;
    }

    .btn-secondary {
        flex: 1;
        padding: 10px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.85rem;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
    }
</style>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <div class="internship-banner">
            <div class="banner-content">
                <h1>Internship Program Dashboard</h1>
                <p style="opacity: 0.8; max-width: 500px;">Streamline your industry-academic immersion. Manage slots,
                    track student applications, and finalize MOU requirements.</p>
                <div style="margin-top: 25px; display: flex; gap: 20px;">
                    <div><span style="font-size: 1.5rem; font-weight: 800;">12</span>
                        <p style="font-size: 0.75rem; opacity: 0.7;">Active Slots</p>
                    </div>
                    <div style="width: 1px; background: rgba(255,255,255,0.1);"></div>
                    <div><span style="font-size: 1.5rem; font-weight: 800;">45</span>
                        <p style="font-size: 0.75rem; opacity: 0.7;">Total Applicants</p>
                    </div>
                </div>
            </div>
            <i class="ph ph-briefcase banner-bg-icon"></i>
        </div>

        <h3 style="margin-bottom: 20px; color: #0f172a;">Your Posting Slots</h3>
        <div class="slots-grid">
            <div class="slot-card">
                <div class="slot-header">
                    <div class="company-logo">テック</div>
                    <span class="status-tag open">4 Slots Open</span>
                </div>
                <div>
                    <h3 style="margin-bottom: 5px;">Junior Web Engineer</h3>
                    <p style="font-size: 0.8rem; color: #64748b;">Primary Tech: PHP, Laravel, MySQL</p>
                </div>
                <div style="font-size: 0.85rem; color: #334155;">
                    <i class="ph ph-users" style="color: var(--primary);"></i> <strong>8 Applicants</strong> waiting for
                    review
                </div>
                <div class="action-row">
                    <button class="btn-secondary">View Details</button>
                    <button
                        style="flex: 1.5; padding: 10px; background: var(--primary); color: white; border: none; border-radius: 10px; font-size: 0.85rem; font-weight: 700; cursor: pointer;">Review
                        Applicants</button>
                </div>
            </div>

            <div class="slot-card">
                <div class="slot-header">
                    <div class="company-logo" style="background: #fdf2f2;">アイ</div>
                    <span class="status-tag open">2 Slots Open</span>
                </div>
                <div>
                    <h3 style="margin-bottom: 5px;">UI/UX Interaction Intern</h3>
                    <p style="font-size: 0.8rem; color: #64748b;">Primary Tech: Figma, Framer, CSS3</p>
                </div>
                <div style="font-size: 0.85rem; color: #334155;">
                    <i class="ph ph-users" style="color: var(--primary);"></i> <strong>12 Applicants</strong> waiting
                    for review
                </div>
                <div class="action-row">
                    <button class="btn-secondary">View Details</button>
                    <button
                        style="flex: 1.5; padding: 10px; background: var(--primary); color: white; border: none; border-radius: 10px; font-size: 0.85rem; font-weight: 700; cursor: pointer;">Review
                        Applicants</button>
                </div>
            </div>

            <div class="slot-card" style="opacity: 0.7;">
                <div class="slot-header">
                    <div class="company-logo" style="background: #f1f5f9; color: #94a3b8;">セキ</div>
                    <span class="status-tag filled">Filled</span>
                </div>
                <div>
                    <h3 style="margin-bottom: 5px;">Cybersecurity Analyst</h3>
                    <p style="font-size: 0.8rem; color: #64748b;">Closed Feb 2026</p>
                </div>
                <button class="btn-secondary">View Archives</button>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>

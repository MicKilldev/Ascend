<?php
session_start();
include('../config/db.php');
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php?role=student");
    exit();
}
?>
<?php include("../includes/header.php"); ?>

<style>
    .badge-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
        margin-top: 30px;
    }

    .badge-card {
        background: white;
        padding: 30px 24px;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        text-align: center;
        transition: 0.3s;
        border: 1px solid rgba(0, 0, 0, 0.02);
        position: relative;
        overflow: hidden;
    }

    .badge-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
        border-color: var(--primary);
    }

    .badge-icon {
        width: 80px;
        height: 80px;
        background: #f8fafc;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 2.5rem;
        border: 4px solid #fff;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }

    .badge-unlocked {
        background: linear-gradient(135deg, #00d2ff 0%, #3a7bd5 100%);
        color: white;
    }

    .badge-locked {
        filter: grayscale(1);
        opacity: 0.6;
    }

    .badge-title {
        font-weight: 800;
        font-size: 1.1rem;
        color: #0f172a;
        margin-bottom: 8px;
    }

    .badge-desc {
        font-size: 0.85rem;
        color: #64748b;
        margin-bottom: 20px;
        line-height: 1.5;
    }

    .unlocked-date {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        color: #10b981;
        background: #dcfce7;
        padding: 4px 12px;
        border-radius: 30px;
    }
</style>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: flex-end;">
            <div>
                <h1>Badges & Achievements</h1>
                <p>Recognizing your technical excellence and institutional contributions.</p>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Points
                    Earned</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--primary);">1,450 XP</div>
            </div>
        </div>

        <div class="badge-grid">
            <!-- Unlocked Badge -->
            <div class="badge-card">
                <div class="badge-icon badge-unlocked">
                    <i class="ph ph-code"></i>
                </div>
                <h3 class="badge-title">Clean Coder</h3>
                <p class="badge-desc">Awarded for consistently submitting projects with zero linting errors and optimal
                    documentation.</p>
                <span class="unlocked-date">Unlocked Dec 2024</span>
            </div>

            <!-- Unlocked Badge -->
            <div class="badge-card">
                <div class="badge-icon badge-unlocked"
                    style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                    <i class="ph ph-lightning"></i>
                </div>
                <h3 class="badge-title">Fast Responder</h3>
                <p class="badge-desc">Recognized for rapid problem-solving during institutional hackathons and coding
                    sprints.</p>
                <span class="unlocked-date">Unlocked Jan 2025</span>
            </div>

            <!-- Unlocked Badge -->
            <div class="badge-card">
                <div class="badge-icon badge-unlocked"
                    style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                    <i class="ph ph-heart-break"></i>
                </div>
                <h3 class="badge-title">Team Leader</h3>
                <p class="badge-desc">Successfully spearheaded a multi-disciplinary group for the Capstone project
                    phase.</p>
                <span class="unlocked-date">Unlocked Feb 2025</span>
            </div>

            <!-- Locked Badge -->
            <div class="badge-card badge-locked">
                <div class="badge-icon">
                    <i class="ph ph-shield-check"></i>
                </div>
                <h3 class="badge-title">Security Guru</h3>
                <p class="badge-desc">Requires completion of Advanced Cyber Security and Penetration Testing modules.
                </p>
                <span
                    style="font-size: 0.7rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Locked</span>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
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
    .overall-progress-card {
        background: #0f172a;
        color: white;
        border-radius: 24px;
        padding: 40px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
    }

    .progress-track {
        height: 12px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        margin: 20px 0;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        background: linear-gradient(90deg, #00d2ff, #3a7bd5);
        border-radius: 10px;
        width: 65%;
        /* Current progress */
        position: relative;
    }

    .progress-marker {
        position: absolute;
        top: -25px;
        right: 0;
        background: var(--primary);
        color: white;
        padding: 2px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .curriculum-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
    }

    .curriculum-year-card {
        background: white;
        padding: 24px;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }

    .year-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f5f9;
    }

    .year-title {
        font-weight: 800;
        color: #0f172a;
    }

    .percentage-tag {
        font-size: 0.8rem;
        font-weight: 700;
        color: var(--primary);
    }

    .subject-pill {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 15px;
        background: #f8fafc;
        border-radius: 10px;
        margin-bottom: 10px;
        font-size: 0.85rem;
    }

    .subject-pill.completed {
        border-left: 3px solid #10b981;
    }

    .subject-pill.current {
        border-left: 3px solid #f59e0b;
        background: rgba(245, 158, 11, 0.05);
    }

    .check-icon {
        color: #10b981;
    }
</style>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <h1>Degree Progress</h1>
        <p>Your journey towards BS Information Technology completion.</p>

        <div class="overall-progress-card">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h3 style="opacity: 0.8;">Course Completion</h3>
                    <h1 style="font-size: 3rem; margin-top: 5px;">65%</h1>
                </div>
                <div style="text-align: right;">
                    <div style="font-weight: 700; color: var(--primary);">Estimated Graduation</div>
                    <div style="font-size: 1.2rem; font-weight: 800; opacity: 0.9;">June 2026</div>
                </div>
            </div>

            <div class="progress-track">
                <div class="progress-bar">
                    <span class="progress-marker">You are here</span>
                </div>
            </div>

            <div style="display: flex; gap: 30px; margin-top: 10px;">
                <div style="font-size: 0.85rem; opacity: 0.8;"><i class="ph ph-check-circle"></i> 92 Units Completed
                </div>
                <div style="font-size: 0.85rem; opacity: 0.8;"><i class="ph ph-circle"></i> 48 Units Remaining</div>
            </div>
        </div>

        <div class="curriculum-grid">
            <div class="curriculum-year-card">
                <div class="year-header">
                    <span class="year-title">First Year</span>
                    <span class="percentage-tag">100% Complete</span>
                </div>
                <div class="subject-pill completed"><span>ComProg 1</span> <i class="ph ph-check-circle check-icon"></i>
                </div>
                <div class="subject-pill completed"><span>ComProg 2</span> <i class="ph ph-check-circle check-icon"></i>
                </div>
                <div class="subject-pill completed"><span>Intro to Computing</span> <i
                        class="ph ph-check-circle check-icon"></i></div>
            </div>

            <div class="curriculum-year-card">
                <div class="year-header">
                    <span class="year-title">Second Year</span>
                    <span class="percentage-tag">75% Complete</span>
                </div>
                <div class="subject-pill completed"><span>Data Structures</span> <i
                        class="ph ph-check-circle check-icon"></i></div>
                <div class="subject-pill current"><span>Web Development</span> <span
                        style="font-size: 0.7rem; color: #f59e0b; font-weight: 700;">ENROLLED</span></div>
                <div class="subject-pill completed"><span>Discrete math</span> <i
                        class="ph ph-check-circle check-icon"></i></div>
            </div>

            <div class="curriculum-year-card" style="opacity: 0.6;">
                <div class="year-header">
                    <span class="year-title">Third Year</span>
                    <span class="percentage-tag">0% Complete</span>
                </div>
                <div class="subject-pill"><span>Capstone 1</span> <i class="ph ph-lock-key"></i></div>
                <div class="subject-pill"><span>Internship 1</span> <i class="ph ph-lock-key"></i></div>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
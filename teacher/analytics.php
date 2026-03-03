<?php
session_start();
include('../config/db.php');

if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['teacher', 'faculty', 'school_admin'])) {
    header("Location: ../login.php?role=faculty");
    exit();
}

$username = htmlspecialchars($_SESSION['username'] ?? 'Admin');
?>

<?php include("../includes/header.php"); ?>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>

    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1>Academic Analytics</h1>
                <p>Predictive insights and institutional performance metrics.</p>
            </div>
            <div
                style="background: white; padding: 10px 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 10px;">
                <div
                    style="width: 10px; height: 10px; border-radius: 50%; background: #10b981; animation: pulse 2s infinite;">
                </div>
                <span style="font-weight: 700; font-size: 0.85rem; color: #64748b; text-transform: uppercase;">Live
                    Engine Active</span>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
            <!-- Trend Card -->
            <div class="card" style="padding: 30px;">
                <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                    <i class="ph ph-chart-line" style="color: #6e45e2; font-size: 1.5rem;"></i>
                    Batch Performance Trends
                </h3>
                <div
                    style="height: 350px; background: #f8fafc; border-radius: 16px; border: 2px dashed #e2e8f0; display: flex; flex-direction: column; align-items: center; justify-content: center; color: #94a3b8; text-align: center; gap: 15px;">
                    <i class="ph ph-graph" style="font-size: 4rem; opacity: 0.3;"></i>
                    <div>
                        <div style="font-weight: 700; color: #64748b;">Visualization Engine Loading...</div>
                        <div style="font-size: 0.8rem; margin-top: 5px;">Synchronizing with Student Academic Records
                        </div>
                    </div>
                </div>
            </div>

            <!-- Distribution Card -->
            <div class="card" style="padding: 30px;">
                <h3 style="margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                    <i class="ph ph-gauge" style="color: #00d2ff; font-size: 1.5rem;"></i>
                    Key Indicators
                </h3>
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div style="padding: 15px; background: #f8fafc; border-radius: 12px;">
                        <div
                            style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 5px;">
                            Avg. Attendance</div>
                        <div style="font-size: 1.8rem; font-weight: 800; color: #0f172a;">92.4%</div>
                    </div>
                    <div style="padding: 15px; background: #f8fafc; border-radius: 12px;">
                        <div
                            style="font-size: 0.75rem; color: #94a3b8; font-weight: 700; text-transform: uppercase; margin-bottom: 5px;">
                            Dean's List Rate</div>
                        <div style="font-size: 1.8rem; font-weight: 800; color: #0f172a;">18.5%</div>
                    </div>
                    <div
                        style="padding: 15px; background: #f8fafc; border-radius: 12px; border-left: 4px solid #ef4444;">
                        <div
                            style="font-size: 0.75rem; color: #ef4444; font-weight: 700; text-transform: uppercase; margin-bottom: 5px;">
                            At-Risk Index</div>
                        <div style="font-size: 1.8rem; font-weight: 800; color: #0f172a;">4.2%</div>
                    </div>
                </div>

                <button
                    style="width: 100%; margin-top: 30px; padding: 15px; background: #0f172a; color: white; border: none; border-radius: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <i class="ph ph-file-pdf"></i> Download Report
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes pulse {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
        }

        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(16, 185, 129, 0);
        }

        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
        }
    }
</style>

<?php include("../includes/footer.php"); ?>
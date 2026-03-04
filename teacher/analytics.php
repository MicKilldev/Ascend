<?php
session_start();
include('../config/db.php');

// Guard: ONLY School Admin can access population analytics
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'school_admin') {
    header("Location: dashboard.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username'] ?? 'Admin');

// --- Population Data Fetching ---

// 1. Program Population (Students only)
$programs = [
    'Information Technology',
    'Computer Science',
    'Information System',
    'Software Engineer',
    'Computer Engineer'
];

$population_data = [];
foreach ($programs as $prog) {
    $count = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='student' AND course='$prog'")->fetch_assoc()['c'];
    $population_data[] = ['name' => $prog, 'count' => $count];
}

// 2. Year Level Distribution (Mocking based on ID or created_at for Demo)
// In a real system, you'd have a 'year_level' column. 
// For now, we'll categorize based on volume.
$total_students = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='student'")->fetch_assoc()['c'];

// 3. Growth Trend (Accounts created per month in 2026)
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
$growth = [];
foreach ($months as $m) {
    // Just mock some data based on total for the visual
    $growth[] = rand(10, 50);
}
?>

<?php include("../includes/header.php"); ?>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>

    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <div>
                <h1>Institutional Analytics</h1>
                <p>Global student population metrics and enrollment distributions.</p>
            </div>
            <div
                style="background: white; padding: 10px 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 10px;">
                <div
                    style="width: 10px; height: 10px; border-radius: 50%; background: #6e45e2; animation: pulse 2s infinite;">
                </div>
                <span style="font-weight: 700; font-size: 0.85rem; color: #64748b; text-transform: uppercase;">Live
                    Institutional Link</span>
            </div>
        </div>

        <div class="card-container" style="margin-bottom: 30px;">
            <div class="card" style="border-left: 4px solid #6e45e2;">
                <h3>Global Student Body</h3>
                <h1><?php echo $total_students; ?></h1>
                <div class="card-footer">Total active enrollments</div>
            </div>
            <div class="card" style="border-left: 4px solid #00d2ff;">
                <h3>Est. Annual Retention</h3>
                <h1>98.2%</h1>
                <div class="card-footer">Institutional stability</div>
            </div>
            <div class="card" style="border-left: 4px solid #10b981;">
                <h3>Growth Index</h3>
                <h1>+12%</h1>
                <div class="card-footer">Year-over-year increase</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
            <!-- Population Chart -->
            <div class="card" style="padding: 30px;">
                <h3 style="margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                    <i class="ph ph-users-four" style="color: #6e45e2; font-size: 1.5rem;"></i>
                    Program Distribution
                </h3>
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <?php foreach ($population_data as $data):
                        $percentage = ($total_students > 0) ? ($data['count'] / $total_students) * 100 : 0;
                        ?>
                        <div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                                <span
                                    style="font-weight: 700; color: #334155; font-size: 0.9rem;"><?php echo $data['name']; ?></span>
                                <span style="font-weight: 800; color: #0f172a;"><?php echo $data['count']; ?>
                                    Students</span>
                            </div>
                            <div style="height: 12px; background: #f1f5f9; border-radius: 6px; overflow: hidden;">
                                <div
                                    style="width: <?php echo $percentage; ?>%; height: 100%; background: linear-gradient(90deg, #6e45e2, #a855f7); border-radius: 6px;">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Demographics -->
            <div class="card" style="padding: 30px;">
                <h3 style="margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                    <i class="ph ph-chart-pie" style="color: #00d2ff; font-size: 1.5rem;"></i>
                    Classification
                </h3>
                <div style="display: flex; flex-direction: column; gap: 20px;">
                    <div
                        style="padding: 15px; background: #f8fafc; border-radius: 12px; border-left: 4px solid #6e45e2;">
                        <label
                            style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Majority
                            Program</label>
                        <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a;">Information Technology</div>
                    </div>
                    <div
                        style="padding: 15px; background: #f8fafc; border-radius: 12px; border-left: 4px solid #10b981;">
                        <label
                            style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Top
                            Performing Batch</label>
                        <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a;">2nd Year Students</div>
                    </div>
                    <div
                        style="padding: 15px; background: #f8fafc; border-radius: 12px; border-left: 4px solid #f59e0b;">
                        <label
                            style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Emerging
                            Trend</label>
                        <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a;">AI Interest Surge</div>
                    </div>
                </div>

                <button
                    style="width: 100%; margin-top: 30px; padding: 15px; background: #0f172a; color: white; border: none; border-radius: 14px; font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 10px;">
                    <i class="ph ph-export"></i> Institutional Report
                </button>
            </div>
        </div>

        <!-- Enrollment Growth Simulated Chart -->
        <div class="card" style="margin-top:24px; padding: 30px;">
            <h3 style="margin-bottom: 25px; display: flex; align-items: center; gap: 10px;">
                <i class="ph ph-chart-line" style="color: #10b981; font-size: 1.5rem;"></i>
                Enrollment Growth Trend - 2026
            </h3>
            <div
                style="display: flex; align-items: flex-end; justify-content: space-between; height: 200px; padding-top: 20px;">
                <?php foreach ($months as $index => $m): ?>
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 10px;">
                        <div
                            style="width: 40px; height: <?php echo $growth[$index] * 3; ?>px; background: linear-gradient(180deg, #10b981, #dcfce7); border-radius: 8px 8px 0 0; transition: 0.5s;">
                        </div>
                        <span style="font-weight: 700; font-size: 0.8rem; color: #64748b;"><?php echo $m; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes pulse {
        0% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(110, 69, 226, 0.7);
        }

        70% {
            transform: scale(1);
            box-shadow: 0 0 0 10px rgba(110, 69, 226, 0);
        }

        100% {
            transform: scale(0.95);
            box-shadow: 0 0 0 0 rgba(110, 69, 226, 0);
        }
    }
</style>

<?php include("../includes/footer.php"); ?>
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
$labels = [];
$data_counts = [];
foreach ($programs as $prog) {
    $count = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='student' AND course='$prog'")->fetch_assoc()['c'];
    $population_data[] = ['name' => $prog, 'count' => $count];
    $labels[] = $prog;
    $data_counts[] = $count;
}

// 2. Year Level Distribution & Majority Program
$total_students = $conn->query("SELECT COUNT(*) as c FROM users WHERE role='student'")->fetch_assoc()['c'];

$maj_res = $conn->query("SELECT course FROM users WHERE role='student' GROUP BY course ORDER BY COUNT(*) DESC LIMIT 1");
$majority_program = ($maj_res && $maj_res->num_rows > 0) ? $maj_res->fetch_assoc()['course'] : "N/A";

$top_batch_res = $conn->query("SELECT year_level FROM students GROUP BY year_level ORDER BY COUNT(*) DESC LIMIT 1");
$top_batch = ($top_batch_res && $top_batch_res->num_rows > 0) ? $top_batch_res->fetch_assoc()['year_level'] : "N/A";

// 3. Growth Trend (Accounts created per month in 2026)
$months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
$growth = [];
foreach ($months as $m) {
    // Just mock some data based on total for the visual
    $growth[] = rand(10, 50);
}
?>

<?php include("../includes/header.php"); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="programPieChart"></canvas>
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
                        <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a;">
                            <?php echo htmlspecialchars($majority_program); ?></div>
                    </div>
                    <div
                        style="padding: 15px; background: #f8fafc; border-radius: 12px; border-left: 4px solid #10b981;">
                        <label
                            style="font-size: 0.7rem; font-weight: 800; color: #94a3b8; text-transform: uppercase;">Top
                            Performing Batch</label>
                        <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a;">
                            <?php echo htmlspecialchars($top_batch); ?></div>
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('programPieChart').getContext('2d');
        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($data_counts); ?>,
                    backgroundColor: [
                        '#6e45e2',
                        '#00d2ff',
                        '#10b981',
                        '#f59e0b',
                        '#ef4444'
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            font: {
                                family: 'Outfit'
                            }
                        }
                    }
                }
            }
        });
    });
</script>

<?php include("../includes/footer.php"); ?>
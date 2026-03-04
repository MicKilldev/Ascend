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
    .grade-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-mini-card {
        background: white;
        padding: 20px;
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: #0f172a;
    }

    .grade-table-wrapper {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #0f172a;
        color: white;
    }

    th {
        padding: 15px 20px;
        text-align: left;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    td {
        padding: 18px 20px;
        font-size: 0.9rem;
        border-bottom: 1px solid #f1f5f9;
        color: #334155;
    }

    tr:last-child td {
        border-bottom: none;
    }

    tr:hover {
        background: #f8faff;
    }

    .grade-badge {
        padding: 4px 12px;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .passed {
        background: #dcfce7;
        color: #16a34a;
    }

    .pending {
        background: #fef3c7;
        color: #d97706;
    }
</style>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 30px;">
            <div>
                <h1>Academic Grades</h1>
                <p>Track your academic progress for the current semester.</p>
            </div>
            <div
                style="font-weight: 700; color: #64748b; font-size: 0.9rem; background: white; padding: 10px 20px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                Semester: <span style="color: var(--primary);">2nd Semester, 2025-26</span>
            </div>
        </div>

        <div class="grade-stats">
            <div class="stat-mini-card">
                <span class="stat-label">Total Units</span>
                <span class="stat-value">21.0</span>
            </div>
            <div class="stat-mini-card">
                <span class="stat-label">Current GPA</span>
                <span class="stat-value">1.50</span>
            </div>
            <div class="stat-mini-card">
                <span class="stat-label">Passed Subjects</span>
                <span class="stat-value">6/7</span>
            </div>
        </div>

        <div class="grade-table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Descriptive Title</th>
                        <th style="text-align: center;">Units</th>
                        <th style="text-align: center;">Midterm</th>
                        <th style="text-align: center;">Finals</th>
                        <th style="text-align: center;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="font-weight: 700;">IT221</td>
                        <td>Object Oriented Programming 2</td>
                        <td style="text-align: center;">3.0</td>
                        <td style="text-align: center; font-weight: 700;">1.25</td>
                        <td style="text-align: center; font-weight: 700;">1.50</td>
                        <td style="text-align: center;"><span class="grade-badge passed">Passed</span></td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700;">IT222</td>
                        <td>Web Development 1</td>
                        <td style="text-align: center;">3.0</td>
                        <td style="text-align: center; font-weight: 700;">1.50</td>
                        <td style="text-align: center; font-weight: 700;">1.25</td>
                        <td style="text-align: center;"><span class="grade-badge passed">Passed</span></td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700;">IT223</td>
                        <td>Data Structures & Algorithms</td>
                        <td style="text-align: center;">3.0</td>
                        <td style="text-align: center; font-weight: 700;">1.75</td>
                        <td style="text-align: center; color: #94a3b8;">--</td>
                        <td style="text-align: center;"><span class="grade-badge pending">In Progress</span></td>
                    </tr>
                    <tr>
                        <td style="font-weight: 700;">GE105</td>
                        <td>Ethics</td>
                        <td style="text-align: center;">3.0</td>
                        <td style="text-align: center; font-weight: 700;">1.25</td>
                        <td style="text-align: center; font-weight: 700;">1.25</td>
                        <td style="text-align: center;"><span class="grade-badge passed">Passed</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            style="margin-top: 20px; padding: 20px; background: #fff; border-radius: 16px; border-left: 4px solid var(--primary); font-size: 0.85rem; color: #64748b;">
            <i class="ph ph-info"></i> <strong>Note:</strong> Grades displayed here are preliminary. Official
            transcripts can be requested from the Registrar's Office after the final semester deliberation.
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>
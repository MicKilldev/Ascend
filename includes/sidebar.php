<?php
$role = $_SESSION['role'] ?? 'portal';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <h2 class="logo">ASCEND</h2>

    <ul>
        <!-- Common Dashboard -->
        <li>
            <a href="dashboard.php" class="<?php echo $current_page == 'dashboard.php' ? 'active' : ''; ?>">
                <i class="ph ph-squares-four"></i> Dashboard
            </a>
        </li>

        <?php if ($role == 'teacher' || $role == 'faculty' || $role == 'school_admin'): ?>
            <?php if ($role === 'school_admin'): ?>
                <li>
                    <a href="users.php" class="<?php echo $current_page == 'users.php' ? 'active' : ''; ?>">
                        <i class="ph ph-users"></i> Global Registry
                    </a>
                </li>
            <?php endif; ?>
            <li>
                <a href="students.php" class="<?php echo $current_page == 'students.php' ? 'active' : ''; ?>">
                    <i class="ph ph-student"></i> Students
                </a>
            </li>
            <?php if ($role === 'school_admin'): ?>
                <li>
                    <a href="analytics.php" class="<?php echo $current_page == 'analytics.php' ? 'active' : ''; ?>">
                        <i class="ph ph-chart-line-up"></i> Population Analytics
                    </a>
                </li>
            <?php endif; ?>
            <li>
                <a href="grades.php" class="<?php echo $current_page == 'grades.php' ? 'active' : ''; ?>">
                    <i class="ph ph-chart-bar"></i> Grades
                </a>
            </li>

        <?php elseif ($role == 'student'): ?>
            <!-- Student Specific -->
            <li>
                <a href="grades.php" class="<?php echo $current_page == 'grades.php' ? 'active' : ''; ?>">
                    <i class="ph ph-chart-bar"></i> My Grades
                </a>
            </li>

            <li>
                <a href="badges.php" class="<?php echo $current_page == 'badges.php' ? 'active' : ''; ?>">
                    <i class="ph ph-trophy"></i> My Badges
                </a>
            </li>
            <li>
                <a href="progress.php" class="<?php echo $current_page == 'progress.php' ? 'active' : ''; ?>">
                    <i class="ph ph-trend-up"></i> Progress Tracker
                </a>
            </li>

        <?php elseif ($role == 'parent'): ?>
            <!-- Parent Specific -->
            <li>
                <a href="child_grades.php" class="<?php echo $current_page == 'child_grades.php' ? 'active' : ''; ?>">
                    <i class="ph ph-chart-bar"></i> Child's Grades
                </a>
            </li>
            <li>
                <a href="child_progress.php" class="<?php echo $current_page == 'child_progress.php' ? 'active' : ''; ?>">
                    <i class="ph ph-trend-up"></i> Progress
                </a>
            </li>
            <li>
                <a href="ledger.php" class="<?php echo $current_page == 'ledger.php' ? 'active' : ''; ?>">
                    <i class="ph ph-receipt"></i> Student Ledger
                </a>
            </li>
            <li>
                <a href="periodic_dues.php" class="<?php echo $current_page == 'periodic_dues.php' ? 'active' : ''; ?>">
                    <i class="ph ph-calendar-check"></i> Periodic Dues
                </a>
            </li>

        <?php endif; ?>

        <!-- Common Settings & Logout -->
        <li>
            <a href="settings.php" class="<?php echo $current_page == 'settings.php' ? 'active' : ''; ?>">
                <i class="ph ph-gear"></i> Settings
            </a>
        </li>
        <li>
            <a href="../logout.php" style="color: #f87171;">
                <i class="ph ph-sign-out"></i> Logout
            </a>
        </li>
    </ul>
</div>
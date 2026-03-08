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
    .talent-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .search-filter-box {
        background: white;
        padding: 20px;
        border-radius: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        display: flex;
        gap: 15px;
        margin-bottom: 30px;
    }

    .filter-input {
        padding: 12px 15px;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.9rem;
        flex: 1;
    }

    .talent-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 24px;
    }

    .talent-card {
        background: white;
        border-radius: 24px;
        padding: 24px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        border: 1px solid rgba(0, 0, 0, 0.02);
        transition: 0.3s;
    }

    .talent-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
    }

    .talent-badge {
        width: 64px;
        height: 64px;
        border-radius: 20px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 15px;
    }

    .skill-tag {
        display: inline-block;
        padding: 4px 10px;
        background: #f8fafc;
        color: #64748b;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 700;
        margin-right: 5px;
        margin-bottom: 5px;
    }

    .view-portfolio-btn {
        width: 100%;
        margin-top: 20px;
        padding: 12px;
        background: #0f172a;
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        cursor: pointer;
        transition: 0.2s;
    }

    .view-portfolio-btn:hover {
        background: var(--primary);
    }
</style>

<div class="container">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <div class="talent-header">
            <div>
                <h1>Talent Scout Directory</h1>
                <p>Browse top-performing graduates verified through institutional analytics.</p>
            </div>
            <button
                style="background: var(--primary); color: white; border: none; padding: 12px 24px; border-radius: 12px; font-weight: 700; cursor: pointer;">
                <i class="ph ph-envelope-simple"></i> Contact Placement Office
            </button>
        </div>

        <div class="search-filter-box">
            <input type="text" class="filter-input" placeholder="Search by skills (e.g. PHP, Python, UI Design)...">
            <select class="filter-input" style="flex: 0.4;">
                <option>All Specializations</option>
                <option>Web Development</option>
                <option>Cyber Security</option>
                <option>Network Admin</option>
            </select>
        </div>

        <div class="talent-grid">
            <div class="talent-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div class="talent-badge">👨‍💻</div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.7rem; color: #10b981; font-weight: 800;">GPA 1.25</div>
                        <div style="font-size: 0.7rem; font-weight: 700; color: #94a3b8;">Top 3%</div>
                    </div>
                </div>
                <h3 style="margin-bottom: 5px;">Mickael D. Garde</h3>
                <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 15px;">Full-Stack Developer Specialization
                </p>
                <div class="skills-container">
                    <span class="skill-tag">PHP/MySQL</span>
                    <span class="skill-tag">React</span>
                    <span class="skill-tag">UI/UX</span>
                    <span class="skill-tag">Laravel</span>
                </div>
                <button class="view-portfolio-btn">View Verified Portfolio</button>
            </div>

            <div class="talent-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div class="talent-badge" style="background: #e0f2fe;">🛡️</div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.7rem; color: #10b981; font-weight: 800;">GPA 1.40</div>
                        <div style="font-size: 0.7rem; font-weight: 700; color: #94a3b8;">Top 10%</div>
                    </div>
                </div>
                <h3 style="margin-bottom: 5px;">Sarah J. Wilson</h3>
                <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 15px;">Cybersecurity & Network Defense</p>
                <div class="skills-container">
                    <span class="skill-tag">Cloud Security</span>
                    <span class="skill-tag">Python</span>
                    <span class="skill-tag">Ethical Hacking</span>
                </div>
                <button class="view-portfolio-btn">View Verified Portfolio</button>
            </div>

            <div class="talent-card">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div class="talent-badge" style="background: #fef2f2;">🎨</div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.7rem; color: #10b981; font-weight: 800;">GPA 1.50</div>
                        <div style="font-size: 0.7rem; font-weight: 700; color: #94a3b8;">Dean's List</div>
                    </div>
                </div>
                <h3 style="margin-bottom: 5px;">James K. Rodriguez</h3>
                <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 15px;">Multimedia Arts & Design</p>
                <div class="skills-container">
                    <span class="skill-tag">Figma</span>
                    <span class="skill-tag">Blender</span>
                    <span class="skill-tag">Motion Graphics</span>
                </div>
                <button class="view-portfolio-btn">View Verified Portfolio</button>
            </div>
        </div>
    </div>
</div>

<?php include("../includes/footer.php"); ?>

<?php
session_start();
include('../config/db.php');
if (!isset($_SESSION['role'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['id'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['old_password']) && isset($_POST['new_password']) && isset($_POST['confirm_password'])) {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row && $old_password == $row['password']) {
        if ($new_password === $confirm_password) {
            $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_stmt->bind_param("si", $new_password, $user_id);
            if ($update_stmt->execute()) {
                $msg = "<p style='color: green;'>Password updated successfully.</p>";
            } else {
                $msg = "<p style='color: red;'>Database error. Please try again.</p>";
            }
        } else {
            $msg = "<p style='color: red;'>New passwords do not match.</p>";
        }
    } else {
        $msg = "<p style='color: red;'>Incorrect old password.</p>";
    }
}
?>
<?php include("../includes/header.php"); ?>
<div class="container">
    <?php include("../includes/sidebar.php"); ?>
    <div class="main-content">
        <h1>Account Settings</h1>
        <p>Manage your institutional identity and security preferences.</p>

        <?php if (isset($msg)) {
            echo $msg;
        } ?>

        <div class="card" style="margin-top: 30px; border: 1px solid #e2e8f0; padding: 30px;">
            <div
                style="display: flex; align-items: center; gap: 20px; margin-bottom: 30px; border-bottom: 1px solid #f1f5f9; padding-bottom: 20px;">
                <div
                    style="width: 80px; height: 80px; border-radius: 20px; background: #0f172a; color: white; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800;">
                    <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                </div>
                <div>
                    <h2 style="font-weight: 800; color: #0f172a;">
                        <?php echo htmlspecialchars($_SESSION['username']); ?>
                    </h2>
                    <p style="margin: 0; color: #64748b;">
                        <?php echo htmlspecialchars($_SESSION['email']); ?>
                    </p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                <div>
                    <h3 style="font-size: 0.85rem; text-transform: uppercase; color: #94a3b8; margin-bottom: 15px;">
                        Profile Details</h3>
                    <div style="display: flex; flex-direction: column; gap: 15px;">
                        <div style="padding: 12px; background: #f8fafc; border-radius: 10px;">
                            <label
                                style="display: block; font-size: 0.65rem; color: #94a3b8; font-weight: 700; text-transform: uppercase;">Public
                                Display Name</label>
                            <span style="font-weight: 700; color: #1e293b;">
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 style="font-size: 0.85rem; text-transform: uppercase; color: #94a3b8; margin-bottom: 15px;">
                        Security</h3>
                    <form method="POST" style="display: flex; flex-direction: column; gap: 10px;">
                        <input type="password" name="old_password" placeholder="Current Password" required
                            style="padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <input type="password" name="new_password" placeholder="New Password" required
                            style="padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <input type="password" name="confirm_password" placeholder="Confirm New Password" required
                            style="padding: 10px; border-radius: 8px; border: 1px solid #e2e8f0;">
                        <button type="submit"
                            style="width: 100%; padding: 12px; background: var(--primary); border: none; border-radius: 10px; font-weight: 700; color: white; cursor: pointer;">
                            Change Access Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include("../includes/footer.php"); ?>
<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];
$active = 'settings';
$page_title = 'Settings';

$profileError = $profileSuccess = $passwordError = $passwordSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---- Update profile ----
    if (isset($_POST['update_profile'])) {
        $first_name = trim($_POST['first_name']);
        $last_name  = trim($_POST['last_name']);

        if ($first_name === '' || $last_name === '') {
            $profileError = "Both fields are required.";
        } else {
            $stmt = mysqli_prepare($conn, "UPDATE users SET first_name=?, last_name=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, "ssi", $first_name, $last_name, $user_id);
            mysqli_stmt_execute($stmt);
            $_SESSION['first_name'] = $first_name;
            $profileSuccess = "Profile updated successfully.";
        }
    }

    // ---- Change password ----
    if (isset($_POST['change_password'])) {
        $current = $_POST['current_password'];
        $new     = $_POST['new_password'];
        $confirm = $_POST['confirm_password'];

        $stmt = mysqli_prepare($conn, "SELECT password FROM users WHERE id=?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!password_verify($current, $row['password'])) {
            $passwordError = "Current password is incorrect.";
        } elseif (strlen($new) < 6) {
            $passwordError = "New password must be at least 6 characters.";
        } elseif ($new !== $confirm) {
            $passwordError = "New password and confirmation do not match.";
        } else {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $upd = mysqli_prepare($conn, "UPDATE users SET password=? WHERE id=?");
            mysqli_stmt_bind_param($upd, "si", $hashed, $user_id);
            mysqli_stmt_execute($upd);

            // Force re-login with the new password for security
            session_unset();
            session_destroy();
            header("Location: login.php?password_changed=1");
            exit;
        }
    }
}

$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Settings - kharchaPatra</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app-layout">
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include 'includes/topbar.php'; ?>
        <div class="page-body">

            <div class="table-card" style="max-width:520px; margin-bottom:24px;">
                <h2>Update Profile</h2>
                <?php if ($profileError): ?><div class="alert alert-error"><?= htmlspecialchars($profileError) ?></div><?php endif; ?>
                <?php if ($profileSuccess): ?><div class="alert alert-success"><?= htmlspecialchars($profileSuccess) ?></div><?php endif; ?>

                <form method="POST" action="settings.php">
                    <div class="form-row">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                        </div>
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary">Save Changes</button>
                </form>
            </div>

            <div class="table-card" style="max-width:520px;">
                <h2>Change Password</h2>
                <?php if ($passwordError): ?><div class="alert alert-error"><?= htmlspecialchars($passwordError) ?></div><?php endif; ?>
                <?php if ($passwordSuccess): ?><div class="alert alert-success"><?= htmlspecialchars($passwordSuccess) ?></div><?php endif; ?>

                <form method="POST" action="settings.php">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" minlength="6" required>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" minlength="6" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary">Change Password</button>
                </form>
            </div>

        </div>
    </div>
</div>
<script src="assets/js/script.js"></script>
</body>
</html>
<?php
session_start();
require_once 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$mode = ($_GET['mode'] ?? '') === 'forgot' ? 'forgot' : 'login';
$error = "";

// ---- Handle login submit ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_login'])) {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if ($email === '' || $password === '') {
        $error = "Please fill in both fields.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, first_name, password FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['first_name'] = $user['first_name'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Invalid email or password.";
        }
    }
}

// ---- Handle forgot-password submit (no OTP — direct reset) ----
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['do_reset'])) {
    $mode = 'forgot';
    $email    = trim($_POST['email']);
    $new      = $_POST['new_password'];
    $confirm  = $_POST['confirm_password'];

    if ($email === '' || $new === '' || $confirm === '') {
        $error = "Please fill in all fields.";
    } elseif (strlen($new) < 6) {
        $error = "New password must be at least 6 characters.";
    } elseif ($new !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

        if (!$user) {
            $error = "No account found with that email address.";
        } else {
            $hashed = password_hash($new, PASSWORD_DEFAULT);
            $upd = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE email = ?");
            mysqli_stmt_bind_param($upd, "ss", $hashed, $email);
            mysqli_stmt_execute($upd);
            header("Location: login.php?password_reset=1");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title><?= $mode === 'login' ? 'Login' : 'Forgot Password' ?> - kharchaPatra</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card-outer">
    <div class="auth-visual">
        <div class="auth-visual-content">
            <img src="assets/images/expense img.jpg" alt="Savings illustration">
            <h2>Track Today. Save Tomorrow.</h2>
            <p>Log back in to pick up right where you left off — every rupee still accounted for.</p>
        </div>
    </div>
    <div class="auth-form-side">
    <div class="auth-card">

        <?php if ($mode === 'login'): ?>

            <h1>Welcome Back!</h1>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if (isset($_GET['registered'])): ?>
                <div class="alert alert-success">Account created successfully. Please log in.</div>
            <?php endif; ?>
            <?php if (isset($_GET['password_changed'])): ?>
                <div class="alert alert-success">Password changed successfully. Please log in again.</div>
            <?php endif; ?>
            <?php if (isset($_GET['password_reset'])): ?>
                <div class="alert alert-success">Password reset successfully. Please log in.</div>
            <?php endif; ?>

            <form method="POST" action="login.php">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="Enter your email address" required>
                </div>
                <div class="form-group">
                    <label>Password <span class="required">*</span></label>
                    <input type="password" name="password" placeholder="Enter your password" required>
                    <div style="text-align:right; margin-top:8px;">
                        <a href="login.php?mode=forgot" style="font-size:12px; color:var(--primary-hover); font-weight:600;">Forgot Password?</a>
                    </div>
                </div>
                <button type="submit" name="do_login" class="btn btn-primary btn-full">LOG IN</button>
            </form>

            <p class="auth-footer-text">Don't have an account? <a href="signup.php">Sign Up</a></p>

        <?php else: ?>

            <h1>Forgot Password</h1>
            <p style="color:var(--text-muted); font-size:13px; margin-top:-20px; margin-bottom:24px;">
                Enter your account email and choose a new password.
            </p>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php?mode=forgot">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="Enter your account email" required>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" placeholder="At least 6 characters" minlength="6" required>
                </div>
                <div class="form-group">
                    <label>Confirm New Password</label>
                    <input type="password" name="confirm_password" placeholder="Re-enter new password" minlength="6" required>
                </div>
                <button type="submit" name="do_reset" class="btn btn-primary btn-full">RESET PASSWORD</button>
            </form>

            <p class="auth-footer-text"><a href="login.php">Back to Login</a></p>

        <?php endif; ?>

    </div>
    </div>
    </div>
</div>
</body>
</html>
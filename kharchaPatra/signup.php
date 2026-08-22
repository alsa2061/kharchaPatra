<?php
session_start();
require_once 'includes/db.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name  = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $password   = $_POST['password'];

    if ($first_name === '' || $last_name === '' || $email === '' || $password === '') {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = "An account with this email already exists.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt2 = mysqli_prepare($conn, "INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt2, "ssss", $first_name, $last_name, $email, $hashed);

            if (mysqli_stmt_execute($stmt2)) {
                $new_user_id = mysqli_insert_id($conn);

                // seed a few default categories for the new user (plain name column, no type)
                $defaults = ['Food', 'Transport', 'Rent', 'Utilities', 'Entertainment', 'Other', 'Salary', 'Freelance'];
                $catStmt = mysqli_prepare($conn, "INSERT INTO categories (user_id, name) VALUES (?, ?)");
                foreach ($defaults as $cat) {
                    mysqli_stmt_bind_param($catStmt, "is", $new_user_id, $cat);
                    mysqli_stmt_execute($catStmt);
                }

                header("Location: login.php?registered=1");
                exit;
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sign Up - kharchaPatra</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-card-outer">
    <div class="auth-visual">
        <div class="auth-visual-content">
            <img src="assets/images/paisa.jpg" alt="People managing their finances">
            <h2>Join kharchaPatra</h2>
            <p>Create your free account and start tracking income, expenses, and savings today.</p>
        </div>
    </div>
    <div class="auth-form-side">
    <div class="auth-card">
        <h1>Create an Account</h1>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="signup.php">
            <div class="form-row">
                <div class="form-group">
                    <label>First Name</label>
                    <input type="text" name="first_name" placeholder="Enter your first name" required
                           value="<?= isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Last Name</label>
                    <input type="text" name="last_name" placeholder="Enter your last name" required
                           value="<?= isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label>Email Address <span class="required">*</span></label>
                <input type="email" name="email" placeholder="Enter your email address" required
                       value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your password" required minlength="6">
            </div>
            <button type="submit" class="btn btn-primary btn-full">SUBMIT</button>
        </form>

        <p class="auth-footer-text">Already have an account? <a href="login.php">Login</a></p>
    </div>
    </div>
    </div>
</div>
</body>
</html>
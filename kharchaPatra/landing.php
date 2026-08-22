<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>kharchaPatra - Track Today. Save Tomorrow.</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="landing-page">

    <div class="landing-nav">
        <div class="landing-logo">
            <div class="landing-logo-circle">kp</div>
            <span>kharchaPatra</span>
        </div>
        <div class="landing-nav-actions">
            <a href="login.php" class="btn btn-outline btn-small">Log In</a>
            <a href="signup.php" class="btn btn-primary btn-small">Sign Up</a>
        </div>
    </div>

    <div class="landing-hero">
        <div class="landing-hero-text">
            <h1>Take control of your money, one rupee at a time.</h1>
            <p class="lead">Track income, manage expenses, and grow your savings —
               free, simple, and built for everyone.</p>
            <div class="landing-cta">
                <a href="signup.php" class="btn btn-primary">Get Started</a>
                <a href="login.php" class="btn btn-outline">I already have an account</a>
            </div>
        </div>
        <div class="landing-hero-image">
            <img src="assets/images/dashboard.png" alt="kharchaPatra dashboard preview">
        </div>
    </div>

</div>
</body>
</html>
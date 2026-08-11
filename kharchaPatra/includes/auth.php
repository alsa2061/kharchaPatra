<?php
/**
 * Session guard — include this at the top of every protected page.
 * Redirects to login.php if no user is logged in.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
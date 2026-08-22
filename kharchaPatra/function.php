<?php
/**
 * functions.php
 * -------------
 * Small reusable helper functions.
 * Include this AFTER config.php:
 *      require_once 'config.php';
 *      require_once 'functions.php';
 */

// Clean up user input to help prevent XSS when we print it back out
function clean($conn, $value) {
    $value = trim($value);
    $value = htmlspecialchars($value, ENT_QUOTES);
    return mysqli_real_escape_string($conn, $value);
}

// Redirect helper
function redirect($page) {
    header("Location: " . $page);
    exit();
}

// Stop the page if the user is not logged in (used on every protected page)
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        redirect('login.php');
    }
}

// Store a one-time message to show after a redirect (e.g. "Saved successfully")
function set_flash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

// Print + clear the flash message (call this near the top of the page body)
function show_flash() {
    if (isset($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type']; // 'success' or 'error'
        $msg  = htmlspecialchars($_SESSION['flash']['message']);
        echo "<div class='alert alert-{$type}'>{$msg}</div>";
        unset($_SESSION['flash']);
    }
}

// Format a number as Nepali rupees, e.g. 100000 -> Rs. 100,000.00
function rs($amount) {
    return "Rs. " . number_format((float)$amount, 2);
}
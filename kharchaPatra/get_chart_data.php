<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';
header('Content-Type: application/json');

$user_id = $_SESSION['user_id'];
$labels = [];
$balance = [];
$expenses = [];

for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $labels[] = date('D', strtotime($date));

    // income for that day
    $stmt = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount),0) FROM income WHERE user_id = ? AND income_date = ?");
    mysqli_stmt_bind_param($stmt, "is", $user_id, $date);
    mysqli_stmt_execute($stmt);
    $inc = mysqli_fetch_row(mysqli_stmt_get_result($stmt))[0];

    // expense for that day
    $stmt2 = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount),0) FROM expenses WHERE user_id = ? AND expense_date = ?");
    mysqli_stmt_bind_param($stmt2, "is", $user_id, $date);
    mysqli_stmt_execute($stmt2);
    $exp = mysqli_fetch_row(mysqli_stmt_get_result($stmt2))[0];

    $balance[] = floatval($inc) - floatval($exp);
    $expenses[] = floatval($exp);
}

echo json_encode([
    'labels' => $labels,
    'balance' => $balance,
    'expenses' => $expenses
]);
<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];

$filter_type = $_GET['filter_type'] ?? 'month';
$from        = $_GET['from'] ?? date('Y-m-01');
$to          = $_GET['to'] ?? date('Y-m-d');
$month       = $_GET['month'] ?? date('Y-m');
$year        = $_GET['year'] ?? date('Y');

if ($filter_type === 'date') {
    $startDate = $from;
    $endDate   = $to;
} elseif ($filter_type === 'month') {
    $startDate = $month . '-01';
    $endDate   = date('Y-m-t', strtotime($startDate));
} else {
    $startDate = $year . '-01-01';
    $endDate   = $year . '-12-31';
}

$stmt = mysqli_prepare($conn, "SELECT * FROM income WHERE user_id=? AND income_date BETWEEN ? AND ? ORDER BY income_date");
mysqli_stmt_bind_param($stmt, "iss", $user_id, $startDate, $endDate);
mysqli_stmt_execute($stmt);
$incomeRows = mysqli_stmt_get_result($stmt);

$stmt2 = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount),0) FROM income WHERE user_id=? AND income_date BETWEEN ? AND ?");
mysqli_stmt_bind_param($stmt2, "iss", $user_id, $startDate, $endDate);
mysqli_stmt_execute($stmt2);
$totalIncome = mysqli_fetch_row(mysqli_stmt_get_result($stmt2))[0];

$stmt3 = mysqli_prepare($conn, "SELECT e.*, c.name AS category_name FROM expenses e
                                 JOIN categories c ON e.category_id=c.id
                                 WHERE e.user_id=? AND e.expense_date BETWEEN ? AND ?
                                 ORDER BY e.expense_date");
mysqli_stmt_bind_param($stmt3, "iss", $user_id, $startDate, $endDate);
mysqli_stmt_execute($stmt3);
$expenseRows = mysqli_stmt_get_result($stmt3);

$stmt4 = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount),0) FROM expenses WHERE user_id=? AND expense_date BETWEEN ? AND ?");
mysqli_stmt_bind_param($stmt4, "iss", $user_id, $startDate, $endDate);
mysqli_stmt_execute($stmt4);
$totalExpense = mysqli_fetch_row(mysqli_stmt_get_result($stmt4))[0];

$netSavings = $totalIncome - $totalExpense;

$userStmt = mysqli_prepare($conn, "SELECT first_name, last_name, email FROM users WHERE id=?");
mysqli_stmt_bind_param($userStmt, "i", $user_id);
mysqli_stmt_execute($userStmt);
$user = mysqli_fetch_assoc(mysqli_stmt_get_result($userStmt));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Report <?= htmlspecialchars($startDate) ?> to <?= htmlspecialchars($endDate) ?> - kharchaPatra</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="bill-wrapper">
    <div class="bill-card" style="max-width:620px;">
        <div class="bill-header">
            <div class="bill-logo">kp</div>
            <div class="bill-brand">
                <strong>kharchaPatra</strong>
                <span>Track Today. Save Tomorrow.</span>
            </div>
        </div>

        <h2 class="bill-title">Financial Report</h2>

        <div class="bill-meta">
            <div><span>Period</span><strong><?= htmlspecialchars($startDate) ?> to <?= htmlspecialchars($endDate) ?></strong></div>
            <div><span>Generated For</span><strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong></div>
        </div>

        <div class="card-grid" style="grid-template-columns: repeat(3, 1fr); margin: 20px 0 30px;">
            <div class="stat-card income" style="box-shadow:none; border:1px solid var(--cream);">
                <div class="stat-label">Total Income</div>
                <div class="stat-value" style="font-size:18px;">Rs. <?= number_format($totalIncome, 2) ?></div>
            </div>
            <div class="stat-card expense" style="box-shadow:none; border:1px solid var(--cream);">
                <div class="stat-label">Total Expense</div>
                <div class="stat-value" style="font-size:18px;">Rs. <?= number_format($totalExpense, 2) ?></div>
            </div>
            <div class="stat-card balance" style="box-shadow:none; border:1px solid var(--cream);">
                <div class="stat-label">Net Savings</div>
                <div class="stat-value" style="font-size:18px;">Rs. <?= number_format($netSavings, 2) ?></div>
            </div>
        </div>

        <h3 style="font-size:14px; margin-bottom:10px;">Income</h3>
        <table class="bill-table">
            <thead><tr><th>Date</th><th>Source</th><th style="text-align:right;">Amount</th></tr></thead>
            <tbody>
                <?php if (mysqli_num_rows($incomeRows) === 0): ?>
                    <tr><td colspan="3">No income in this period.</td></tr>
                <?php endif; ?>
                <?php while ($r = mysqli_fetch_assoc($incomeRows)): ?>
                <tr>
                    <td><?= htmlspecialchars($r['income_date']) ?></td>
                    <td><?= htmlspecialchars($r['source']) ?></td>
                    <td style="text-align:right;">Rs. <?= number_format($r['amount'], 2) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h3 style="font-size:14px; margin:24px 0 10px;">Expenses</h3>
        <table class="bill-table">
            <thead><tr><th>Date</th><th>Category</th><th>Note</th><th style="text-align:right;">Amount</th></tr></thead>
            <tbody>
                <?php if (mysqli_num_rows($expenseRows) === 0): ?>
                    <tr><td colspan="4">No expenses in this period.</td></tr>
                <?php endif; ?>
                <?php while ($r = mysqli_fetch_assoc($expenseRows)): ?>
                <tr>
                    <td><?= htmlspecialchars($r['expense_date']) ?></td>
                    <td><?= htmlspecialchars($r['category_name']) ?></td>
                    <td><?= htmlspecialchars($r['note']) ?></td>
                    <td style="text-align:right;">Rs. <?= number_format($r['amount'], 2) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="bill-footer">
            Generated by kharchaPatra &middot; This is a system-generated report.
        </div>

        <div class="bill-actions no-print">
            <button onclick="window.print()" class="btn btn-primary">🖨 Print / Save as PDF</button>
            <a href="reports.php" class="btn btn-outline">Back to Reports</a>
        </div>
    </div>
</div>
</body>
</html>
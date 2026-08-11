<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];
$active = 'reports';
$page_title = 'Reports';

// ---- Filters ----
$filter_type = $_GET['filter_type'] ?? 'month';   // date | month | year
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
} else { // year
    $startDate = $year . '-01-01';
    $endDate   = $year . '-12-31';
}

// ---- Income in range ----
$stmt = mysqli_prepare($conn, "SELECT * FROM income WHERE user_id=? AND income_date BETWEEN ? AND ? ORDER BY income_date DESC");
mysqli_stmt_bind_param($stmt, "iss", $user_id, $startDate, $endDate);
mysqli_stmt_execute($stmt);
$incomeRows = mysqli_stmt_get_result($stmt);

$stmt2 = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount),0) FROM income WHERE user_id=? AND income_date BETWEEN ? AND ?");
mysqli_stmt_bind_param($stmt2, "iss", $user_id, $startDate, $endDate);
mysqli_stmt_execute($stmt2);
$totalIncome = mysqli_fetch_row(mysqli_stmt_get_result($stmt2))[0];

// ---- Expenses in range ----
$stmt3 = mysqli_prepare($conn, "SELECT e.*, c.name AS category_name FROM expenses e
                                 JOIN categories c ON e.category_id=c.id
                                 WHERE e.user_id=? AND e.expense_date BETWEEN ? AND ?
                                 ORDER BY e.expense_date DESC");
mysqli_stmt_bind_param($stmt3, "iss", $user_id, $startDate, $endDate);
mysqli_stmt_execute($stmt3);
$expenseRows = mysqli_stmt_get_result($stmt3);

$stmt4 = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount),0) FROM expenses WHERE user_id=? AND expense_date BETWEEN ? AND ?");
mysqli_stmt_bind_param($stmt4, "iss", $user_id, $startDate, $endDate);
mysqli_stmt_execute($stmt4);
$totalExpense = mysqli_fetch_row(mysqli_stmt_get_result($stmt4))[0];

$netSavings = $totalIncome - $totalExpense;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Reports - kharchaPatra</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app-layout">
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include 'includes/topbar.php'; ?>
        <div class="page-body">

            <form class="filter-bar" method="GET" action="reports.php">
                <div class="form-group">
                    <label>Filter By</label>
                    <select name="filter_type" onchange="this.form.submit()">
                        <option value="date"  <?= $filter_type === 'date'  ? 'selected' : '' ?>>Date Range</option>
                        <option value="month" <?= $filter_type === 'month' ? 'selected' : '' ?>>Month</option>
                        <option value="year"  <?= $filter_type === 'year'  ? 'selected' : '' ?>>Year</option>
                    </select>
                </div>

                <?php if ($filter_type === 'date'): ?>
                    <div class="form-group"><label>From</label><input type="date" name="from" value="<?= $from ?>"></div>
                    <div class="form-group"><label>To</label><input type="date" name="to" value="<?= $to ?>"></div>
                <?php elseif ($filter_type === 'month'): ?>
                    <div class="form-group"><label>Month</label><input type="month" name="month" value="<?= $month ?>"></div>
                <?php else: ?>
                    <div class="form-group"><label>Year</label><input type="number" name="year" value="<?= $year ?>" min="2000" max="2100"></div>
                <?php endif; ?>

                <button type="submit" class="btn btn-primary">Generate Report</button>
            </form>

            <div class="card-grid" style="grid-template-columns: repeat(3, 1fr);">
                <div class="stat-card income">
                    <div class="stat-label">Total Income</div>
                    <div class="stat-value">Rs. <?= number_format($totalIncome, 2) ?></div>
                </div>
                <div class="stat-card expense">
                    <div class="stat-label">Total Expense</div>
                    <div class="stat-value">Rs. <?= number_format($totalExpense, 2) ?></div>
                </div>
                <div class="stat-card balance">
                    <div class="stat-label">Net Savings</div>
                    <div class="stat-value">Rs. <?= number_format($netSavings, 2) ?></div>
                </div>
            </div>

            <div class="table-card" style="margin-bottom:20px;">
                <div class="table-header"><h2>Income (<?= htmlspecialchars($startDate) ?> to <?= htmlspecialchars($endDate) ?>)</h2></div>
                <table class="data-table">
                    <thead><tr><th>Date</th><th>Source</th><th>Amount</th></tr></thead>
                    <tbody>
                        <?php if (mysqli_num_rows($incomeRows) === 0): ?>
                            <tr><td colspan="3">No income in this period.</td></tr>
                        <?php endif; ?>
                        <?php while ($r = mysqli_fetch_assoc($incomeRows)): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['income_date']) ?></td>
                            <td><?= htmlspecialchars($r['source']) ?></td>
                            <td><span class="tag tag-income">Rs. <?= number_format($r['amount'], 2) ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-card">
                <div class="table-header"><h2>Expenses (<?= htmlspecialchars($startDate) ?> to <?= htmlspecialchars($endDate) ?>)</h2></div>
                <table class="data-table">
                    <thead><tr><th>Date</th><th>Category</th><th>Note</th><th>Amount</th></tr></thead>
                    <tbody>
                        <?php if (mysqli_num_rows($expenseRows) === 0): ?>
                            <tr><td colspan="4">No expenses in this period.</td></tr>
                        <?php endif; ?>
                        <?php while ($r = mysqli_fetch_assoc($expenseRows)): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['expense_date']) ?></td>
                            <td><?= htmlspecialchars($r['category_name']) ?></td>
                            <td><?= htmlspecialchars($r['note']) ?></td>
                            <td><span class="tag tag-expense">Rs. <?= number_format($r['amount'], 2) ?></span></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="assets/js/script.js"></script>
</body>
</html>
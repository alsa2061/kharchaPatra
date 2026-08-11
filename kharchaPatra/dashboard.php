<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];
$active = 'dashboard';
$page_title = 'Dashboard';

// ---- Handle quick-add forms ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_income'])) {
        $source = trim($_POST['source']);
        $amount = floatval($_POST['income_amount']);
        if ($source !== '' && $amount > 0) {
            $stmt = mysqli_prepare($conn, "INSERT INTO income (user_id, source, amount, income_date) VALUES (?, ?, ?, CURDATE())");
            mysqli_stmt_bind_param($stmt, "isd", $user_id, $source, $amount);
            mysqli_stmt_execute($stmt);
        }
    } elseif (isset($_POST['add_expense'])) {
        $category_id = intval($_POST['category_id']);
        $amount = floatval($_POST['expense_amount']);
        if ($category_id > 0 && $amount > 0) {
            $stmt = mysqli_prepare($conn, "INSERT INTO expenses (user_id, category_id, amount, expense_date) VALUES (?, ?, ?, CURDATE())");
            mysqli_stmt_bind_param($stmt, "iid", $user_id, $category_id, $amount);
            mysqli_stmt_execute($stmt);
        }
    }
    header("Location: dashboard.php");
    exit;
}

// ---- Compute stats ----
function scalar($conn, $sql, $user_id) {
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_row($res);
    return $row[0] ? floatval($row[0]) : 0;
}

$totalIncome  = scalar($conn, "SELECT SUM(amount) FROM income WHERE user_id = ?", $user_id);
$totalExpense = scalar($conn, "SELECT SUM(amount) FROM expenses WHERE user_id = ?", $user_id);
$totalSaving  = scalar($conn, "SELECT SUM(amount) FROM savings WHERE user_id = ?", $user_id);
$balance      = $totalIncome - $totalExpense;

// categories for the quick-add expense dropdown
$catStmt = mysqli_prepare($conn, "SELECT id, name FROM categories WHERE user_id = ? ORDER BY name");
mysqli_stmt_bind_param($catStmt, "i", $user_id);
mysqli_stmt_execute($catStmt);
$categories = mysqli_stmt_get_result($catStmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard - kharchaPatra</title>
<link rel="stylesheet" href="assets/css/style.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
</head>
<body>
<div class="app-layout">
    <?php include 'includes/sidebar.php'; ?>

    <div class="main-content">
        <?php include 'includes/topbar.php'; ?>

        <div class="page-body">
            <div class="greeting">
                Hello, <?= htmlspecialchars($_SESSION['first_name']) ?> 👋
                <span>Welcome back</span>
            </div>

            <div class="card-grid">
                <div class="stat-card balance">
                    <div class="stat-label">Balance</div>
                    <div class="stat-value">Rs. <?= number_format($balance, 2) ?></div>
                </div>
                <div class="stat-card income">
                    <div class="stat-label">Income</div>
                    <div class="stat-value">Rs. <?= number_format($totalIncome, 2) ?></div>
                </div>
                <div class="stat-card expense">
                    <div class="stat-label">Expense</div>
                    <div class="stat-value">Rs. <?= number_format($totalExpense, 2) ?></div>
                </div>
                <div class="stat-card saving">
                    <div class="stat-label">Saving</div>
                    <div class="stat-value">Rs. <?= number_format($totalSaving, 2) ?></div>
                </div>
            </div>

            <div class="chart-grid">
                <div class="chart-card">
                    <h3>Balance Trend (Last 7 Days)</h3>
                    <canvas id="lineChart" height="180"></canvas>
                </div>
                <div class="chart-card">
                    <h3>Expenses (Last 7 Days)</h3>
                    <canvas id="barChart" height="180"></canvas>
                </div>
            </div>

            <div class="quick-add-grid">
                <div class="quick-add-card">
                    <form method="POST" action="dashboard.php">
                        <div class="form-group">
                            <label>Source</label>
                            <input type="text" name="source" placeholder="e.g. Salary, Freelance" required>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" step="0.01" name="income_amount" placeholder="0.00" required>
                        </div>
                        <button type="submit" name="add_income" class="btn btn-income btn-full">+ Add income</button>
                    </form>
                </div>
                <div class="quick-add-card">
                    <form method="POST" action="dashboard.php">
                        <div class="form-group">
                            <label>Category</label>
                            <select name="category_id" required>
                                <option value="">Select category</option>
                                <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Amount</label>
                            <input type="number" step="0.01" name="expense_amount" placeholder="0.00" required>
                        </div>
                        <button type="submit" name="add_expense" class="btn btn-expense btn-full">+ Add expenses</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="assets/js/script.js"></script>
<script>
fetch('get_chart_data.php')
  .then(r => r.json())
  .then(data => {
    new Chart(document.getElementById('lineChart'), {
      type: 'line',
      data: {
        labels: data.labels,
        datasets: [{
          label: 'Balance',
          data: data.balance,
          borderColor: '#8FAD8A',
          backgroundColor: 'rgba(143,173,138,0.15)',
          tension: 0.4,
          fill: true,
          pointBackgroundColor: '#8FAD8A'
        }]
      },
      options: { plugins: { legend: { display: false } } }
    });

    new Chart(document.getElementById('barChart'), {
      type: 'bar',
      data: {
        labels: data.labels,
        datasets: [{
          label: 'Expenses',
          data: data.expenses,
          backgroundColor: '#D97B7B',
          borderRadius: 6
        }]
      },
      options: { plugins: { legend: { display: false } } }
    });
  });
</script>
</body>
</html>
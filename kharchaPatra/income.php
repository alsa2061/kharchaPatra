<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];
$active = 'income';
$page_title = 'Income';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_income'])) {
        $id     = intval($_POST['income_id']);
        $source = trim($_POST['source']);
        $amount = floatval($_POST['amount']);
        $date   = $_POST['income_date'];

        if ($id > 0) {
            $stmt = mysqli_prepare($conn, "UPDATE income SET source=?, amount=?, income_date=? WHERE id=? AND user_id=?");
            mysqli_stmt_bind_param($stmt, "sdsii", $source, $amount, $date, $id, $user_id);
            mysqli_stmt_execute($stmt);
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO income (user_id, source, amount, income_date) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "isds", $user_id, $source, $amount, $date);
            mysqli_stmt_execute($stmt);
        }
    } elseif (isset($_POST['delete_income'])) {
        $id = intval($_POST['income_id']);
        $stmt = mysqli_prepare($conn, "DELETE FROM income WHERE id=? AND user_id=?");
        mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
        mysqli_stmt_execute($stmt);
    }
    header("Location: income.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM income WHERE user_id = ? ORDER BY income_date DESC, id DESC");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$rows = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Income - kharchaPatra</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app-layout">
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include 'includes/topbar.php'; ?>
        <div class="page-body">
            <div class="table-card">
                <div class="table-header">
                    <h2>Income List</h2>
                    <button class="btn btn-income" onclick="openIncomeModal()">+ Add income</button>
                </div>

                <table class="data-table">
                    <thead>
                        <tr><th>Date</th><th>Source</th><th>Amount</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($rows) === 0): ?>
                            <tr><td colspan="4">No income entries yet. Add your first one above.</td></tr>
                        <?php endif; ?>
                        <?php while ($r = mysqli_fetch_assoc($rows)): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['income_date']) ?></td>
                            <td><?= htmlspecialchars($r['source']) ?></td>
                            <td><span class="tag tag-income">Rs. <?= number_format($r['amount'], 2) ?></span></td>
                            <td>
                               
                                <span class="action-link edit"
                                      onclick='openIncomeModal(<?= json_encode($r) ?>)'>Edit</span>
                                <form method="POST" style="display:inline" onsubmit="return confirm('Delete this income entry?');">
                                    <input type="hidden" name="income_id" value="<?= $r['id'] ?>">
                                    <span class="action-link delete" onclick="this.parentNode.submit()">Delete</span>
                                    <input type="hidden" name="delete_income" value="1">
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="incomeModal">
    <div class="modal-box">
        <h3 id="incomeModalTitle">Add Income</h3>
        <form method="POST" action="income.php">
            <input type="hidden" name="income_id" id="income_id" value="0">
            <div class="form-group">
                <label>Source</label>
                <input type="text" name="source" id="income_source" placeholder="e.g. Salary" required>
            </div>
            <div class="form-group">
                <label>Amount</label>
                <input type="number" step="0.01" name="amount" id="income_amount" placeholder="0.00" required>
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="income_date" id="income_date" required>
            </div>
            <div class="modal-actions">
                <button type="submit" name="save_income" class="btn btn-income btn-full">Save</button>
                <button type="button" class="btn btn-outline btn-full" onclick="closeIncomeModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/script.js"></script>
<script>
function openIncomeModal(data) {
    document.getElementById('incomeModal').classList.add('open');
    if (data) {
        document.getElementById('incomeModalTitle').innerText = 'Edit Income';
        document.getElementById('income_id').value = data.id;
        document.getElementById('income_source').value = data.source;
        document.getElementById('income_amount').value = data.amount;
        document.getElementById('income_date').value = data.income_date;
    } else {
        document.getElementById('incomeModalTitle').innerText = 'Add Income';
        document.getElementById('income_id').value = 0;
        document.getElementById('income_source').value = '';
        document.getElementById('income_amount').value = '';
        document.getElementById('income_date').valueAsDate = new Date();
    }
}
function closeIncomeModal() {
    document.getElementById('incomeModal').classList.remove('open');
}
</script>
</body>
</html>
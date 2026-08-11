<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];
$active = 'expenses';
$page_title = 'Expenses';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_expense'])) {
        $id          = intval($_POST['expense_id']);
        $category_id = intval($_POST['category_id']);
        $amount      = floatval($_POST['amount']);
        $note        = trim($_POST['note']);
        $date        = $_POST['expense_date'];

        if ($id > 0) {
            $stmt = mysqli_prepare($conn, "UPDATE expenses SET category_id=?, amount=?, note=?, expense_date=? WHERE id=? AND user_id=?");
            mysqli_stmt_bind_param($stmt, "idssii", $category_id, $amount, $note, $date, $id, $user_id);
            mysqli_stmt_execute($stmt);
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO expenses (user_id, category_id, amount, note, expense_date) VALUES (?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iidss", $user_id, $category_id, $amount, $note, $date);
            mysqli_stmt_execute($stmt);
        }
    } elseif (isset($_POST['delete_expense'])) {
        $id = intval($_POST['expense_id']);
        $stmt = mysqli_prepare($conn, "DELETE FROM expenses WHERE id=? AND user_id=?");
        mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
        mysqli_stmt_execute($stmt);
    }
    header("Location: expenses.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT e.*, c.name AS category_name FROM expenses e
                                JOIN categories c ON e.category_id = c.id
                                WHERE e.user_id = ? ORDER BY e.expense_date DESC, e.id DESC");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$rows = mysqli_stmt_get_result($stmt);

$catStmt = mysqli_prepare($conn, "SELECT id, name FROM categories WHERE user_id = ? ORDER BY name");
mysqli_stmt_bind_param($catStmt, "i", $user_id);
mysqli_stmt_execute($catStmt);
$categoriesResult = mysqli_stmt_get_result($catStmt);
$categories = [];
while ($c = mysqli_fetch_assoc($categoriesResult)) { $categories[] = $c; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Expenses - kharchaPatra</title>
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
                    <h2>Expense List</h2>
                    <button class="btn btn-expense" onclick="openExpenseModal()">+ Add expenses</button>
                </div>

                <table class="data-table">
                    <thead>
                        <tr><th>Date</th><th>Category</th><th>Note</th><th>Amount</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($rows) === 0): ?>
                            <tr><td colspan="5">No expense entries yet. Add your first one above.</td></tr>
                        <?php endif; ?>
                        <?php while ($r = mysqli_fetch_assoc($rows)): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['expense_date']) ?></td>
                            <td><?= htmlspecialchars($r['category_name']) ?></td>
                            <td><?= htmlspecialchars($r['note']) ?></td>
                            <td><span class="tag tag-expense">Rs. <?= number_format($r['amount'], 2) ?></span></td>
                            <td>
                                <span class="action-link edit"
                                      onclick='openExpenseModal(<?= json_encode($r) ?>)'>Edit</span>
                                <form method="POST" style="display:inline" onsubmit="return confirm('Delete this expense entry?');">
                                    <input type="hidden" name="expense_id" value="<?= $r['id'] ?>">
                                    <span class="action-link delete" onclick="this.parentNode.submit()">Delete</span>
                                    <input type="hidden" name="delete_expense" value="1">
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

<!-- Add/Edit Modal -->
<div class="modal-overlay" id="expenseModal">
    <div class="modal-box">
        <h3 id="expenseModalTitle">Add Expense</h3>
        <form method="POST" action="expenses.php">
            <input type="hidden" name="expense_id" id="expense_id" value="0">
            <div class="form-group">
                <label>Category</label>
                <select name="category_id" id="expense_category" required>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Amount</label>
                <input type="number" step="0.01" name="amount" id="expense_amount" placeholder="0.00" required>
            </div>
            <div class="form-group">
                <label>Note</label>
                <input type="text" name="note" id="expense_note" placeholder="Optional note">
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="expense_date" id="expense_date" required>
            </div>
            <div class="modal-actions">
                <button type="submit" name="save_expense" class="btn btn-expense btn-full">Save</button>
                <button type="button" class="btn btn-outline btn-full" onclick="closeExpenseModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/script.js"></script>
<script>
function openExpenseModal(data) {
    document.getElementById('expenseModal').classList.add('open');
    if (data) {
        document.getElementById('expenseModalTitle').innerText = 'Edit Expense';
        document.getElementById('expense_id').value = data.id;
        document.getElementById('expense_category').value = data.category_id;
        document.getElementById('expense_amount').value = data.amount;
        document.getElementById('expense_note').value = data.note;
        document.getElementById('expense_date').value = data.expense_date;
    } else {
        document.getElementById('expenseModalTitle').innerText = 'Add Expense';
        document.getElementById('expense_id').value = 0;
        document.getElementById('expense_amount').value = '';
        document.getElementById('expense_note').value = '';
        document.getElementById('expense_date').valueAsDate = new Date();
    }
}
function closeExpenseModal() {
    document.getElementById('expenseModal').classList.remove('open');
}
</script>
</body>
</html>
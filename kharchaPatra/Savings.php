<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];
$active = 'savings';
$page_title = 'Savings';

// ---- Handle add / edit / delete ----
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_saving'])) {
        $id     = intval($_POST['saving_id']);
        $note   = trim($_POST['note']);
        $amount = floatval($_POST['amount']);
        $date   = $_POST['saving_date'];

        if ($id > 0) { // update
            $stmt = mysqli_prepare($conn, "UPDATE savings SET note=?, amount=?, saving_date=? WHERE id=? AND user_id=?");
            mysqli_stmt_bind_param($stmt, "sdsii", $note, $amount, $date, $id, $user_id);
            mysqli_stmt_execute($stmt);
        } else { // insert
            $stmt = mysqli_prepare($conn, "INSERT INTO savings (user_id, note, amount, saving_date) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "isds", $user_id, $note, $amount, $date);
            mysqli_stmt_execute($stmt);
        }
    } elseif (isset($_POST['delete_saving'])) {
        $id = intval($_POST['saving_id']);
        $stmt = mysqli_prepare($conn, "DELETE FROM savings WHERE id=? AND user_id=?");
        mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
        mysqli_stmt_execute($stmt);
    }
    header("Location: savings.php");
    exit;
}

$stmt = mysqli_prepare($conn, "SELECT * FROM savings WHERE user_id = ? ORDER BY saving_date DESC, id DESC");
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$rows = mysqli_stmt_get_result($stmt);

$totalStmt = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount),0) FROM savings WHERE user_id = ?");
mysqli_stmt_bind_param($totalStmt, "i", $user_id);
mysqli_stmt_execute($totalStmt);
$totalSaving = mysqli_fetch_row(mysqli_stmt_get_result($totalStmt))[0];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Savings - kharchaPatra</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app-layout">
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include 'includes/topbar.php'; ?>
        <div class="page-body">

            <div class="card-grid" style="grid-template-columns: 1fr; max-width:280px; margin-bottom:24px;">
                <div class="stat-card saving">
                    <div class="stat-icon">🏦</div>
                    <div class="stat-label">Total Saved</div>
                    <div class="stat-value">Rs. <?= number_format($totalSaving, 2) ?></div>
                </div>
            </div>

            <div class="table-card">
                <div class="table-header">
                    <h2>Savings List</h2>
                    <button class="btn btn-saving" onclick="openSavingModal()">+ Add saving</button>
                </div>

                <table class="data-table">
                    <thead>
                        <tr><th>Date</th><th>Note</th><th>Amount</th><th>Actions</th></tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($rows) === 0): ?>
                            <tr><td colspan="4">No savings entries yet. Add your first one above.</td></tr>
                        <?php endif; ?>
                        <?php while ($r = mysqli_fetch_assoc($rows)): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['saving_date']) ?></td>
                            <td><?= htmlspecialchars($r['note']) ?></td>
                            <td><span class="tag" style="background:#FBF0DA; color:var(--warning-color);">Rs. <?= number_format($r['amount'], 2) ?></span></td>
                            <td>
                                <span class="action-link edit"
                                      onclick='openSavingModal(<?= json_encode($r) ?>)'>Edit</span>
                                <form method="POST" style="display:inline" onsubmit="return confirm('Delete this saving entry?');">
                                    <input type="hidden" name="saving_id" value="<?= $r['id'] ?>">
                                    <span class="action-link delete" onclick="this.parentNode.submit()">Delete</span>
                                    <input type="hidden" name="delete_saving" value="1">
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
<div class="modal-overlay" id="savingModal">
    <div class="modal-box">
        <h3 id="savingModalTitle">Add Saving</h3>
        <form method="POST" action="savings.php">
            <input type="hidden" name="saving_id" id="saving_id" value="0">
            <div class="form-group">
                <label>Note</label>
                <input type="text" name="note" id="saving_note" placeholder="e.g. Emergency fund">
            </div>
            <div class="form-group">
                <label>Amount</label>
                <input type="number" step="0.01" name="amount" id="saving_amount" placeholder="0.00" required>
            </div>
            <div class="form-group">
                <label>Date</label>
                <input type="date" name="saving_date" id="saving_date" required>
            </div>
            <div class="modal-actions">
                <button type="submit" name="save_saving" class="btn btn-saving btn-full">Save</button>
                <button type="button" class="btn btn-outline btn-full" onclick="closeSavingModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/script.js"></script>
<script>
function openSavingModal(data) {
    document.getElementById('savingModal').classList.add('open');
    if (data) {
        document.getElementById('savingModalTitle').innerText = 'Edit Saving';
        document.getElementById('saving_id').value = data.id;
        document.getElementById('saving_note').value = data.note || '';
        document.getElementById('saving_amount').value = data.amount;
        document.getElementById('saving_date').value = data.saving_date;
    } else {
        document.getElementById('savingModalTitle').innerText = 'Add Saving';
        document.getElementById('saving_id').value = 0;
        document.getElementById('saving_note').value = '';
        document.getElementById('saving_amount').value = '';
        document.getElementById('saving_date').valueAsDate = new Date();
    }
}
function closeSavingModal() {
    document.getElementById('savingModal').classList.remove('open');
}
</script>
</body>
</html>
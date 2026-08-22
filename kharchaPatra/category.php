<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$user_id = $_SESSION['user_id'];
$active = 'category';
$page_title = 'Category';
$error = '';

$type = ($_GET['type'] ?? 'expense') === 'income' ? 'income' : 'expense';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['save_category'])) {
        $id       = intval($_POST['category_id']);
        $name     = trim($_POST['name']);
        $catType  = ($_POST['type'] ?? 'expense') === 'income' ? 'income' : 'expense';

        if ($name !== '') {
            if ($id > 0) {
                $stmt = mysqli_prepare($conn, "UPDATE categories SET name=?, type=? WHERE id=? AND user_id=?");
                mysqli_stmt_bind_param($stmt, "ssii", $name, $catType, $id, $user_id);
                mysqli_stmt_execute($stmt);
            } else {
                $stmt = mysqli_prepare($conn, "INSERT INTO categories (user_id, name, type) VALUES (?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "iss", $user_id, $name, $catType);
                mysqli_stmt_execute($stmt);
            }
        }
        header("Location: category.php?type=" . $catType);
        exit;
    } elseif (isset($_POST['delete_category'])) {
        $id = intval($_POST['category_id']);
        $delType = ($_POST['del_type'] ?? 'expense') === 'income' ? 'income' : 'expense';

        if ($delType === 'expense') {
            $check = mysqli_prepare($conn, "SELECT COUNT(*) FROM expenses WHERE category_id=?");
            mysqli_stmt_bind_param($check, "i", $id);
            mysqli_stmt_execute($check);
            $inUse = mysqli_fetch_row(mysqli_stmt_get_result($check))[0];

            if ($inUse > 0) {
                $error = "Cannot delete — this category is used by existing expenses.";
            } else {
                $stmt = mysqli_prepare($conn, "DELETE FROM categories WHERE id=? AND user_id=?");
                mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
                mysqli_stmt_execute($stmt);
            }
        } else {
            $stmt = mysqli_prepare($conn, "DELETE FROM categories WHERE id=? AND user_id=?");
            mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
            mysqli_stmt_execute($stmt);
        }
        $type = $delType;
    }
}

$stmt = mysqli_prepare($conn, "SELECT * FROM categories WHERE user_id = ? AND type = ? ORDER BY name");
mysqli_stmt_bind_param($stmt, "is", $user_id, $type);
mysqli_stmt_execute($stmt);
$rows = mysqli_stmt_get_result($stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Category - kharchaPatra</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="app-layout">
    <?php include 'includes/sidebar.php'; ?>
    <div class="main-content">
        <?php include 'includes/topbar.php'; ?>
        <div class="page-body">

            <div class="table-header">
                <h2><?= $type === 'income' ? 'Income Categories' : 'Expense Categories' ?></h2>
                <button class="btn <?= $type === 'income' ? 'btn-income' : 'btn-primary' ?>" onclick="openCatModal()">+ Add category</button>
            </div>

            <div class="category-tabs">
                <a href="category.php?type=expense" class="category-tab <?= $type === 'expense' ? 'active' : '' ?>">Expense Categories</a>
                <a href="category.php?type=income" class="category-tab <?= $type === 'income' ? 'active' : '' ?>">Income Categories</a>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="category-grid">
                <?php if (mysqli_num_rows($rows) === 0): ?>
                    <p style="color:var(--text-muted); font-size:13px;">
                        No <?= $type ?> categories yet. Add your first one above.
                    </p>
                <?php endif; ?>
                <?php while ($c = mysqli_fetch_assoc($rows)): ?>
                <div class="category-chip">
                    <span><?= htmlspecialchars($c['name']) ?></span>
                    <div class="chip-actions">
                        <span class="action-link edit" onclick='openCatModal(<?= json_encode($c) ?>)'>Edit</span>
                        <form method="POST" style="display:inline" onsubmit="return confirm('Delete this category?');">
                            <input type="hidden" name="category_id" value="<?= $c['id'] ?>">
                            <input type="hidden" name="del_type" value="<?= $c['type'] ?>">
                            <span class="action-link delete" onclick="this.parentNode.submit()">Delete</span>
                            <input type="hidden" name="delete_category" value="1">
                        </form>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<div class="modal-overlay" id="catModal">
    <div class="modal-box">
        <h3 id="catModalTitle">Add Category</h3>
        <form method="POST" action="category.php">
            <input type="hidden" name="category_id" id="category_id" value="0">
            <div class="form-group">
                <label>Category Type</label>
                <select name="type" id="category_type">
                    <option value="expense" <?= $type === 'expense' ? 'selected' : '' ?>>Expense</option>
                    <option value="income" <?= $type === 'income' ? 'selected' : '' ?>>Income</option>
                </select>
            </div>
            <div class="form-group">
                <label>Category Name</label>
                <input type="text" name="name" id="category_name" placeholder="e.g. Groceries" required>
            </div>
            <div class="modal-actions">
                <button type="submit" name="save_category" class="btn btn-primary btn-full">Save</button>
                <button type="button" class="btn btn-outline btn-full" onclick="closeCatModal()">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script src="assets/js/script.js"></script>
<script>
function openCatModal(data) {
    document.getElementById('catModal').classList.add('open');
    if (data) {
        document.getElementById('catModalTitle').innerText = 'Edit Category';
        document.getElementById('category_id').value = data.id;
        document.getElementById('category_name').value = data.name;
        document.getElementById('category_type').value = data.type;
    } else {
        document.getElementById('catModalTitle').innerText = 'Add Category';
        document.getElementById('category_id').value = 0;
        document.getElementById('category_name').value = '';
        document.getElementById('category_type').value = '<?= $type ?>';
    }
}
function closeCatModal() {
    document.getElementById('catModal').classList.remove('open');
}
</script>
</body>
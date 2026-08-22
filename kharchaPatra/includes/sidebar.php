<?php
$active = $active ?? '';
?>
<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-circle">kp</div>
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="nav-item <?= $active === 'dashboard' ? 'active' : '' ?>">
            <span class="nav-icon">▦</span> Dashboard
        </a>
        <a href="income.php" class="nav-item <?= $active === 'income' ? 'active' : '' ?>">
            <span class="nav-icon">↑</span> Income
        </a>
        <a href="expenses.php" class="nav-item <?= $active === 'expenses' ? 'active' : '' ?>">
            <span class="nav-icon">↓</span> Expenses
        </a>
        <a href="savings.php" class="nav-item <?= $active === 'savings' ? 'active' : '' ?>">
            <span class="nav-icon">🏦</span> Savings
        </a>
        <a href="category.php" class="nav-item <?= $active === 'category' ? 'active' : '' ?>">
            <span class="nav-icon">▤</span> Category
        </a>
        <a href="reports.php" class="nav-item <?= $active === 'reports' ? 'active' : '' ?>">
            <span class="nav-icon">▥</span> Reports
        </a>
        <a href="settings.php" class="nav-item <?= $active === 'settings' ? 'active' : '' ?>">
            <span class="nav-icon">⚙</span> Settings
        </a>
    </nav>
</aside>
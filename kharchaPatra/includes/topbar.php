<?php
// $page_title should be set by the including page
$page_title = $page_title ?? 'Page';
?>
<header class="topbar">
    <span class="topbar-title"><?= htmlspecialchars($page_title) ?></span>
    <div class="topbar-right">
        <div class="profile-icon">👤</div>
        <a href="logout.php" class="btn btn-primary btn-small">LOG OUT</a>
    </div>
</header>
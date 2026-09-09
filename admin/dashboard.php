<?php
require __DIR__ . '/includes/auth.php';
requireLogin();
require __DIR__ . '/../config/connect.php';
$sections = require __DIR__ . '/includes/sections.php';

$counts = [];
foreach (array_keys($sections) as $key) {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM content_items WHERE section = ?');
    $stmt->execute([$key]);
    $counts[$key] = (int) $stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<link rel="stylesheet" href="../css/color.css">
<link rel="stylesheet" href="../css/color-dark.css">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/components.css">
<link rel="stylesheet" href="../css/responsive.css">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<div class="bg-layers"><div class="orb orb-1"></div><div class="orb orb-2"></div><div class="orb orb-3"></div><div class="orb orb-4"></div></div>

<div class="glass admin-topbar">
  <div style="font-weight:700;">Portfolio Dashboard</div>
  <div style="display:flex; align-items:center; gap:14px; font-size:13px; color:var(--text-secondary);">
    <span>Logged in as <?= htmlspecialchars(currentAdminUsername()) ?></span>
    <a href="change_password.php" class="btn btn-ghost" style="padding:8px 16px; font-size:12.5px;">Change Password</a>
    <a href="logout.php" class="btn btn-ghost" style="padding:8px 16px; font-size:12.5px;">Log Out</a>
  </div>
</div>

<div class="admin-wrap">
  <div class="section-heading" style="text-align:left; margin-bottom:24px;">
    <div class="eyebrow">Manage</div>
    <h2>Website Sections</h2>
    <p>Click a section to add, edit, or remove items shown on the live site.</p>
  </div>

  <a href="settings.php" class="glass section-tile" style="display:block; margin-bottom:22px;">
    <div class="label" style="font-size:16px; font-weight:600; color:var(--text-primary);">⚙ Site Settings</div>
    <div class="label" style="margin-top:4px;">Name, contact, social links, hero text, and stats shown on every page</div>
  </a>

  <div class="section-grid">
    <?php foreach ($sections as $key => $s): ?>
      <a href="manage.php?section=<?= urlencode($key) ?>" class="glass section-tile">
        <div class="count"><?= $counts[$key] ?></div>
        <div class="label"><?= htmlspecialchars($s['label']) ?></div>
      </a>
    <?php endforeach; ?>
  </div>
</div>

</body>
</html>

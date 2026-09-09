<?php
require __DIR__ . '/includes/auth.php';
requireLogin();
require __DIR__ . '/../config/connect.php';
$sections = require __DIR__ . '/includes/sections.php';

$section = $_GET['section'] ?? '';
if (!isset($sections[$section])) {
    header('Location: dashboard.php');
    exit;
}
$config = $sections[$section];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $stmt = $pdo->prepare('DELETE FROM content_items WHERE id = ? AND section = ?');
    $stmt->execute([(int) $_POST['id'], $section]);
    header('Location: manage.php?section=' . urlencode($section) . '&deleted=1');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM content_items WHERE section = ? ORDER BY sort_order ASC, id DESC');
$stmt->execute([$section]);
$items = $stmt->fetchAll();

$titleField = array_key_first($config['fields']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manage <?= htmlspecialchars($config['label']) ?></title>
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
  <div style="font-weight:700;"><a href="dashboard.php" style="color:var(--text-primary); text-decoration:none;">← Dashboard</a></div>
  <div style="font-size:13px; color:var(--text-secondary);">Logged in as <?= htmlspecialchars(currentAdminUsername()) ?></div>
</div>

<div class="admin-wrap">
  <div class="section-heading" style="text-align:left; margin-bottom:22px;">
    <div class="eyebrow">Section</div>
    <h2><?= htmlspecialchars($config['label']) ?></h2>
  </div>

  <?php if (!empty($_GET['deleted'])): ?><div class="flash success">Item deleted.</div><?php endif; ?>
  <?php if (!empty($_GET['saved'])): ?><div class="flash success">Saved successfully.</div><?php endif; ?>

  <div style="margin-bottom:22px;">
    <a href="edit_item.php?section=<?= urlencode($section) ?>" class="btn btn-primary">+ Add New Item</a>
  </div>

  <?php if (!$items): ?>
    <p style="color:var(--text-muted);">No items yet. Add the first one above.</p>
  <?php endif; ?>

  <?php foreach ($items as $item): $data = json_decode($item['data'], true); ?>
    <div class="glass item-row">
      <div>
        <div class="item-title"><?= htmlspecialchars($data[$titleField] ?? '(untitled)') ?></div>
        <div class="item-sub">ID #<?= $item['id'] ?></div>
      </div>
      <div class="item-actions">
        <a href="edit_item.php?section=<?= urlencode($section) ?>&id=<?= $item['id'] ?>" class="btn btn-ghost" style="padding:8px 16px; font-size:12.5px;">Edit</a>
        <form method="post" onsubmit="return confirm('Delete this item?');" style="display:inline;">
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= $item['id'] ?>">
          <button type="submit" class="btn btn-ghost" style="padding:8px 16px; font-size:12.5px; color:#dc2626;">Delete</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
</div>

</body>
</html>

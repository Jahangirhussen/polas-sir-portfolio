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
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

$data = [];
if ($id) {
    $stmt = $pdo->prepare('SELECT * FROM content_items WHERE id = ? AND section = ?');
    $stmt->execute([$id, $section]);
    $row = $stmt->fetch();
    if (!$row) { header('Location: manage.php?section=' . urlencode($section)); exit; }
    $data = json_decode($row['data'], true);
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newData = [];
    foreach ($config['fields'] as $key => $field) {
        if ($field['type'] === 'checkbox') {
            $newData[$key] = isset($_POST[$key]);
        } else {
            $newData[$key] = trim($_POST[$key] ?? '');
        }
        if (!empty($field['required']) && $newData[$key] === '') {
            $error = htmlspecialchars($field['label']) . ' is required.';
        }
    }

    if (!$error) {
        $json = json_encode($newData, JSON_UNESCAPED_UNICODE);
        if ($id) {
            $stmt = $pdo->prepare('UPDATE content_items SET data = ? WHERE id = ? AND section = ?');
            $stmt->execute([$json, $id, $section]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO content_items (section, data) VALUES (?, ?)');
            $stmt->execute([$section, $json]);
        }
        header('Location: manage.php?section=' . urlencode($section) . '&saved=1');
        exit;
    }
    $data = $newData;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= $id ? 'Edit' : 'Add' ?> <?= htmlspecialchars($config['label']) ?></title>
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
  <div style="font-weight:700;"><a href="manage.php?section=<?= urlencode($section) ?>" style="color:var(--text-primary); text-decoration:none;">← <?= htmlspecialchars($config['label']) ?></a></div>
</div>

<div class="admin-wrap" style="max-width:640px;">
  <div class="section-heading" style="text-align:left; margin-bottom:22px;">
    <h2><?= $id ? 'Edit' : 'Add New' ?> — <?= htmlspecialchars($config['label']) ?></h2>
  </div>

  <?php if ($error): ?><div class="flash error"><?= $error ?></div><?php endif; ?>

  <form method="post" class="glass glass-card admin-form-grid" style="padding:32px;">
    <?php foreach ($config['fields'] as $key => $field):
        $val = $data[$key] ?? '';
    ?>
      <?php if ($field['type'] === 'checkbox'): ?>
        <label class="checkbox-row">
          <input type="checkbox" name="<?= $key ?>" <?= $val ? 'checked' : '' ?>>
          <?= htmlspecialchars($field['label']) ?>
        </label>
      <?php elseif ($field['type'] === 'select'): ?>
        <label class="form-label"><?= htmlspecialchars($field['label']) ?></label>
        <select name="<?= $key ?>" class="form-field">
          <?php foreach ($field['options'] as $opt): ?>
            <option value="<?= htmlspecialchars($opt) ?>" <?= $val === $opt ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option>
          <?php endforeach; ?>
        </select>
      <?php elseif ($field['type'] === 'textarea'): ?>
        <label class="form-label"><?= htmlspecialchars($field['label']) ?></label>
        <textarea name="<?= $key ?>" class="form-field" rows="4"><?= htmlspecialchars($val) ?></textarea>
      <?php elseif ($field['type'] === 'image'): ?>
        <label class="form-label"><?= htmlspecialchars($field['label']) ?></label>
        <div class="image-upload-zone" data-target="<?= $key ?>" <?= $val ? "style=\"background-image:url('" . htmlspecialchars($val) . "')\"" : '' ?>>
          <div class="image-upload-preview" <?= $val ? '' : 'hidden' ?> <?= $val ? "style=\"background-image:url('" . htmlspecialchars($val) . "')\"" : '' ?>></div>
          <div class="image-upload-prompt" <?= $val ? 'hidden' : '' ?>>
            <span>Drag & drop a photo, or click to browse</span>
            <input type="file" accept="image/png,image/jpeg,image/webp,image/gif" class="image-upload-input" hidden>
          </div>
        </div>
        <input type="text" name="<?= $key ?>" class="form-field image-url-field" placeholder="or paste an image URL" value="<?= htmlspecialchars($val) ?>">
      <?php else: ?>
        <label class="form-label"><?= htmlspecialchars($field['label']) ?><?= !empty($field['required']) ? ' *' : '' ?></label>
        <input type="<?= $field['type'] === 'number' ? 'number' : 'text' ?>" name="<?= $key ?>" class="form-field" value="<?= htmlspecialchars((string) $val) ?>">
      <?php endif; ?>
    <?php endforeach; ?>

    <button type="submit" class="btn btn-primary" style="margin-top:8px;">Save</button>
  </form>
</div>

<script src="../js/utils/dataApi.js"></script>
<script>wireImageFields(document);</script>
</body>
</html>

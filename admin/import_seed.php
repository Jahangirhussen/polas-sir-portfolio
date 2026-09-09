<?php
// One-time helper: migrates data/publications.json and data/media.json into
// content_items so the dashboard starts populated. Run once from your browser,
// then delete this file (or leave it — it's protected by login and is safe to
// re-run since it skips a section that already has rows).

require __DIR__ . '/includes/auth.php';
requireLogin();
require __DIR__ . '/../config/connect.php';

function alreadyImported(PDO $pdo, string $section): bool {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM content_items WHERE section = ?');
    $stmt->execute([$section]);
    return (int) $stmt->fetchColumn() > 0;
}

function importPublications(PDO $pdo): int {
    if (alreadyImported($pdo, 'publications')) return 0;
    $file = __DIR__ . '/../data/publications.json';
    if (!file_exists($file)) return 0;
    $json = json_decode(file_get_contents($file), true);
    $pubs = $json['publications'] ?? [];
    $stmt = $pdo->prepare('INSERT INTO content_items (section, sort_order, data) VALUES ("publications", ?, ?)');
    $i = 0;
    foreach ($pubs as $pub) {
        $stmt->execute([$i++, json_encode($pub, JSON_UNESCAPED_UNICODE)]);
    }
    return $i;
}

function importMedia(PDO $pdo): int {
    if (alreadyImported($pdo, 'media')) return 0;
    $file = __DIR__ . '/../data/media.json';
    if (!file_exists($file)) return 0;
    $items = json_decode(file_get_contents($file), true) ?: [];
    $stmt = $pdo->prepare('INSERT INTO content_items (section, sort_order, data) VALUES ("media", ?, ?)');
    $i = 0;
    foreach ($items as $item) {
        $stmt->execute([$i++, json_encode($item, JSON_UNESCAPED_UNICODE)]);
    }
    return $i;
}

$pubCount = importPublications($pdo);
$mediaCount = importMedia($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><title>Import Seed Data</title>
<link rel="stylesheet" href="../css/color.css">
<link rel="stylesheet" href="../css/color-dark.css">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/components.css">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<div class="admin-wrap" style="max-width:560px; padding-top:60px;">
  <div class="glass glass-card" style="padding:32px;">
    <h2>Import Complete</h2>
    <p class="card-desc" style="margin-top:14px;">Publications imported: <?= $pubCount ?><br>Media items imported: <?= $mediaCount ?></p>
    <p class="card-desc" style="margin-top:10px; color:var(--text-muted);">(0 means that section already had data, so it was skipped.)</p>
    <a href="dashboard.php" class="btn btn-primary" style="margin-top:20px;">Go to Dashboard</a>
  </div>
</div>
</body>
</html>

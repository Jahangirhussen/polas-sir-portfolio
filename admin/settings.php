<?php
require __DIR__ . '/includes/auth.php';
requireLogin();
require __DIR__ . '/../config/connect.php';

$fields = [
    'site_name'       => 'Full Name',
    'tagline'         => 'Title / Role (e.g. Professor & Researcher)',
    'university'      => 'University / Department',
    'location'        => 'Location (City, Country)',
    'email'           => 'Public Email',
    'statement'       => 'Short Academic Statement (footer)',
    'hero_heading'    => 'Home Hero Heading',
    'hero_subtitle'   => 'Home Hero Subtitle',
    'stat_citations'  => 'Stat — Citations',
    'stat_hindex'     => 'Stat — h-index',
    'stat_i10index'   => 'Stat — i10-index',
    'stat_publications' => 'Stat — Publications',
    'scholar_url'     => 'Google Scholar URL',
    'orcid_url'       => 'ORCID URL',
    'researchgate_url' => 'ResearchGate URL',
    'scopus_url'      => 'Scopus URL',
    'wos_url'         => 'Web of Science URL',
    'linkedin_url'    => 'LinkedIn URL',
    'github_url'      => 'GitHub URL',
    'university_profile_url' => 'University Profile URL',
];

$stmt = $pdo->prepare('SELECT * FROM content_items WHERE section = "settings" LIMIT 1');
$stmt->execute();
$row = $stmt->fetch();
$data = $row ? json_decode($row['data'], true) : [];

$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newData = [];
    foreach ($fields as $key => $label) {
        $newData[$key] = trim($_POST[$key] ?? '');
    }
    $json = json_encode($newData, JSON_UNESCAPED_UNICODE);

    if ($row) {
        $stmt = $pdo->prepare('UPDATE content_items SET data = ? WHERE id = ?');
        $stmt->execute([$json, $row['id']]);
    } else {
        $stmt = $pdo->prepare('INSERT INTO content_items (section, sort_order, data) VALUES ("settings", 0, ?)');
        $stmt->execute([$json]);
    }
    $data = $newData;
    $success = 'Settings saved. Changes are live on the site immediately.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Site Settings</title>
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
</div>

<div class="admin-wrap" style="max-width:680px;">
  <div class="section-heading" style="text-align:left; margin-bottom:22px;">
    <div class="eyebrow">Sitewide</div>
    <h2>Site Settings</h2>
    <p>Name, contact, social links, hero text, and stats shown across every page.</p>
  </div>

  <?php if ($success): ?><div class="flash success"><?= htmlspecialchars($success) ?></div><?php endif; ?>

  <form method="post" class="glass glass-card admin-form-grid" style="padding:32px;">
    <?php foreach ($fields as $key => $label): $val = $data[$key] ?? ''; ?>
      <label class="form-label"><?= htmlspecialchars($label) ?></label>
      <?php if (in_array($key, ['statement', 'hero_subtitle'])): ?>
        <textarea name="<?= $key ?>" class="form-field" rows="2"><?= htmlspecialchars($val) ?></textarea>
      <?php else: ?>
        <input type="text" name="<?= $key ?>" class="form-field" value="<?= htmlspecialchars($val) ?>">
      <?php endif; ?>
    <?php endforeach; ?>
    <button type="submit" class="btn btn-primary" style="margin-top:8px;">Save Settings</button>
  </form>
</div>

</body>
</html>

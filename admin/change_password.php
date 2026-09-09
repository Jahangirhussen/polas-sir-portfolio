<?php
require __DIR__ . '/includes/auth.php';
requireLogin();
require __DIR__ . '/../config/connect.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $stmt = $pdo->prepare('SELECT password_hash FROM admin_users WHERE id = ?');
    $stmt->execute([$_SESSION['admin_id']]);
    $hash = $stmt->fetchColumn();

    if (!password_verify($current, $hash)) {
        $error = 'Current password is incorrect.';
    } elseif (strlen($new) < 8) {
        $error = 'New password must be at least 8 characters.';
    } elseif ($new !== $confirm) {
        $error = 'New password and confirmation do not match.';
    } else {
        $newHash = password_hash($new, PASSWORD_BCRYPT);
        $stmt = $pdo->prepare('UPDATE admin_users SET password_hash = ? WHERE id = ?');
        $stmt->execute([$newHash, $_SESSION['admin_id']]);
        $success = 'Password updated successfully.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Change Password</title>
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

<div class="admin-wrap" style="max-width:480px;">
  <div class="section-heading" style="text-align:left; margin-bottom:22px;"><h2>Change Password</h2></div>
  <?php if ($error): ?><div class="flash error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
  <?php if ($success): ?><div class="flash success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
  <form method="post" class="glass glass-card" style="padding:32px;">
    <input type="password" name="current_password" class="form-field" placeholder="Current Password" required>
    <input type="password" name="new_password" class="form-field" placeholder="New Password (min 8 chars)" required>
    <input type="password" name="confirm_password" class="form-field" placeholder="Confirm New Password" required>
    <button type="submit" class="btn btn-primary">Update Password</button>
  </form>
</div>

</body>
</html>

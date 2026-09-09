<?php
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/../config/connect.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT id, username, password_hash FROM admin_users WHERE username = ?');
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        header('Location: dashboard.php');
        exit;
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login</title>
<link rel="stylesheet" href="../css/color.css">
<link rel="stylesheet" href="../css/color-dark.css">
<link rel="stylesheet" href="../css/style.css">
<link rel="stylesheet" href="../css/components.css">
<link rel="stylesheet" href="../css/responsive.css">
<link rel="stylesheet" href="assets/admin.css">
</head>
<body>
<div class="bg-layers"><div class="orb orb-1"></div><div class="orb orb-2"></div><div class="orb orb-3"></div><div class="orb orb-4"></div></div>

<div class="admin-login-wrap">
  <div class="glass glass-card" style="max-width:380px; width:100%; padding:36px;">
    <h2 style="text-align:center; margin-bottom:6px;">Dashboard Login</h2>
    <p style="text-align:center; color:var(--text-muted); font-size:13px; margin-bottom:24px;">Portfolio Content Manager</p>
    <?php if ($error): ?>
      <p style="color:#e0525f; font-size:13px; margin-bottom:14px; text-align:center;"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <form method="post">
      <input class="form-field" type="text" name="username" placeholder="Username" required autofocus>
      <input class="form-field" type="password" name="password" placeholder="Password" required>
      <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Log In</button>
    </form>
  </div>
</div>

</body>
</html>

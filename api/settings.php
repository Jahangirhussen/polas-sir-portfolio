<?php
// Public read-only endpoint: /api/settings.php
// Returns the sitewide settings object (name, contact, social links, hero text, stats).

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require __DIR__ . '/../config/connect.php';

$stmt = $pdo->query('SELECT data FROM content_items WHERE section = "settings" LIMIT 1');
$row = $stmt->fetch();

echo $row ? $row['data'] : '{}';

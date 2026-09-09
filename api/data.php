<?php
// Public read-only endpoint: /api/data.php?section=publications
// Returns items for one section as JSON, in the same shape the front-end already expects.

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require __DIR__ . '/../config/connect.php';
$sections = require __DIR__ . '/../admin/includes/sections.php';

$section = $_GET['section'] ?? '';
if (!isset($sections[$section])) {
    http_response_code(400);
    echo json_encode(['error' => 'Unknown section']);
    exit;
}

$stmt = $pdo->prepare('SELECT data FROM content_items WHERE section = ? ORDER BY sort_order ASC, id ASC');
$stmt->execute([$section]);
$rows = $stmt->fetchAll(PDO::FETCH_COLUMN);

$items = array_map(fn($json) => json_decode($json, true), $rows);

echo json_encode($items, JSON_UNESCAPED_UNICODE);

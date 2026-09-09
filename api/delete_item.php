<?php
// Admin-only: deletes one item from a live page's inline controls.
// POST JSON: { section, id }

session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

require __DIR__ . '/../config/connect.php';

$body = json_decode(file_get_contents('php://input'), true);
$section = $body['section'] ?? '';
$id = isset($body['id']) ? (int) $body['id'] : 0;

$stmt = $pdo->prepare('DELETE FROM content_items WHERE id = ? AND section = ?');
$stmt->execute([$id, $section]);

echo json_encode(['ok' => true]);

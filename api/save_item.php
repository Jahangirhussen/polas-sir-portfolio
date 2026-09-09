<?php
// Admin-only: saves one item from the inline "add new" box on a live page.
// POST JSON: { section, id (optional, for edit), data: {...} }

session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Not authorized. Please log in at /admin/login.php']);
    exit;
}

require __DIR__ . '/../config/connect.php';
$sections = require __DIR__ . '/../admin/includes/sections.php';

$body = json_decode(file_get_contents('php://input'), true);
$section = $body['section'] ?? '';
$id = isset($body['id']) ? (int) $body['id'] : null;
$input = $body['data'] ?? [];

if (!isset($sections[$section])) {
    http_response_code(400);
    echo json_encode(['error' => 'Unknown section']);
    exit;
}

$fields = $sections[$section]['fields'];
$data = [];
foreach ($fields as $key => $field) {
    if ($field['type'] === 'checkbox') {
        $data[$key] = !empty($input[$key]);
    } else {
        $data[$key] = trim((string) ($input[$key] ?? ''));
    }
    if (!empty($field['required']) && $data[$key] === '') {
        http_response_code(422);
        echo json_encode(['error' => $field['label'] . ' is required.']);
        exit;
    }
}

$json = json_encode($data, JSON_UNESCAPED_UNICODE);

if ($id) {
    $stmt = $pdo->prepare('UPDATE content_items SET data = ? WHERE id = ? AND section = ?');
    $stmt->execute([$json, $id, $section]);
} else {
    $stmt = $pdo->prepare('INSERT INTO content_items (section, data) VALUES (?, ?)');
    $stmt->execute([$section, $json]);
    $id = $pdo->lastInsertId();
}

echo json_encode(['ok' => true, 'id' => $id, 'data' => $data]);

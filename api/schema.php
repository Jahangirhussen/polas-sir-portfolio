<?php
// Public: returns the field schema for one section so the front-end can
// build the inline "add new" form without duplicating field definitions.

session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

$sections = require __DIR__ . '/../admin/includes/sections.php';
$section = $_GET['section'] ?? '';

if (!isset($sections[$section])) {
    http_response_code(400);
    echo json_encode(['error' => 'Unknown section']);
    exit;
}

echo json_encode($sections[$section]['fields'], JSON_UNESCAPED_UNICODE);

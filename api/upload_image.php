<?php
// Admin-only: receives one uploaded image file, saves it into image/,
// returns its public path so it can be used as an "Image URL" value.

session_start();
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Not authorized']);
    exit;
}

if (empty($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => 'No valid file received']);
    exit;
}

$file = $_FILES['image'];
$maxBytes = 8 * 1024 * 1024; // 8 MB
if ($file['size'] > $maxBytes) {
    http_response_code(413);
    echo json_encode(['error' => 'File too large (max 8MB)']);
    exit;
}

$allowed = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp', 'gif' => 'image/gif'];
$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$mime = mime_content_type($file['tmp_name']);

if (!isset($allowed[$ext]) || $mime !== $allowed[$ext]) {
    http_response_code(415);
    echo json_encode(['error' => 'Only JPG, PNG, WEBP, or GIF images are allowed']);
    exit;
}

$imageDir = __DIR__ . '/../image';
if (!is_dir($imageDir)) mkdir($imageDir, 0755, true);

$safeName = preg_replace('/[^a-z0-9-]+/', '-', strtolower(pathinfo($file['name'], PATHINFO_FILENAME)));
$filename = $safeName . '-' . substr(bin2hex(random_bytes(4)), 0, 8) . '.' . $ext;
$destination = $imageDir . '/' . $filename;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    http_response_code(500);
    echo json_encode(['error' => 'Could not save the uploaded file']);
    exit;
}

echo json_encode(['ok' => true, 'url' => 'image/' . $filename]);

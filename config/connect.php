<?php
// Shared PDO connection. Include this from any api/*.php or admin/*.php.

$configFile = __DIR__ . '/db.php';
if (!file_exists($configFile)) {
    http_response_code(500);
    die('Database not configured. Copy config/db.sample.php to config/db.php and fill in your Hostinger MySQL credentials.');
}

$config = require $configFile;

$dsn = "mysql:host={$config['host']};dbname={$config['dbname']};charset={$config['charset']}";

try {
    $pdo = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    die('Database connection failed. Check config/db.php credentials.');
}

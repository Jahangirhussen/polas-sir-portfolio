<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['admin' => !empty($_SESSION['admin_id'])]);

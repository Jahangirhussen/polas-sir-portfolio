<?php
session_start();

function requireLogin(): void {
    if (empty($_SESSION['admin_id'])) {
        header('Location: login.php');
        exit;
    }
}

function currentAdminUsername(): string {
    return $_SESSION['admin_username'] ?? '';
}

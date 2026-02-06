<?php
declare(strict_types=1);

function ensure_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function is_admin(): bool
{
    ensure_session();
    return isset($_SESSION['admin_id']);
}

function require_admin(): void
{
    if (!is_admin()) {
        header('Location: /admin/login.php');
        exit;
    }
}

function current_customer_id(): string
{
    ensure_session();
    if (!isset($_SESSION['customer_id'])) {
        $_SESSION['customer_id'] = bin2hex(random_bytes(16));
    }

    return $_SESSION['customer_id'];
}

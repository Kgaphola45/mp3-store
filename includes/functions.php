<?php
declare(strict_types=1);

/**
 * Format price for display
 */
function format_price(float $price): string
{
    return '$' . number_format($price, 2);
}

/**
 * Generate a secure random token
 */
function generate_token(int $length = 32): string
{
    return bin2hex(random_bytes($length / 2));
}

/**
 * Redirect and exit
 */
function redirect(string $url): void
{
    header("Location: $url");
    exit;
}

/**
 * Get current admin user ID (mock/simplified)
 */
function get_current_admin_user_id(): int 
{
    // In a real app, this would come from session
    return $_SESSION['admin_id'] ?? 0;
}

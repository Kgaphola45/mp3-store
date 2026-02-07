<?php
declare(strict_types=1);

/**
 * Format price for display
 */
function format_price(float $price): string
{
    return 'R' . number_format($price, 2);
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

/**
 * Generate dynamic cover art data based on title and artist
 * Returns ['color' => hex_string, 'text' => initials_string]
 */
function get_cover_art_data(string $title, string $artist): array
{
    // Generate a consistent HSL color based on the hash of the title + artist
    // Use md5 to get a consistent hash, then take the first few chars to seed the hue
    $hash = md5($title . $artist);
    $hue = hexdec(substr($hash, 0, 2)) * 360 / 255;
    
    // Saturation and Lightness fixed for a nice look (simulated with RGB conversion or just HSL if CSS supports it)
    // CSS supports HSL, so we can return an HSL string or Hex. Let's return a CSS gradient string.
    
    $color1 = "hsl({$hue}, 60%, 40%)";
    $color2 = "hsl({$hue}, 60%, 20%)";
    
    // Generate initials (max 2 chars)
    $initials = '';
    if ($title) $initials .= strtoupper(substr($title, 0, 1));
    if ($artist) $initials .= strtoupper(substr($artist, 0, 1));
    
    return [
        'background' => "linear-gradient(135deg, $color1, $color2)",
        'text' => $initials
    ];
}

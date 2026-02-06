<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$songId = (int)($_GET['song_id'] ?? 0);
if ($songId <= 0) {
    http_response_code(400);
    echo 'Invalid song.';
    exit;
}

$stmt = db()->prepare('SELECT id, title, artist, price FROM songs WHERE id = ?');
$stmt->execute([$songId]);
$song = $stmt->fetch();

if (!$song) {
    http_response_code(404);
    echo 'Song not found.';
    exit;
}

$customerId = current_customer_id();
$token = bin2hex(random_bytes(24));
$expiresAt = (new DateTimeImmutable('+24 hours'))->format('Y-m-d H:i:s');

$stmt = db()->prepare(
    'INSERT INTO purchases (song_id, customer_id, download_token, token_expires_at, max_downloads, downloads_count)
     VALUES (?, ?, ?, ?, ?, 0)'
);
$stmt->execute([$songId, $customerId, $token, $expiresAt, 3]);
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Purchase Complete - MP3 Store</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <main class="container narrow">
        <h1>Purchase Complete</h1>
        <div class="card">
            <h2><?= htmlspecialchars($song['title']) ?></h2>
            <p class="muted"><?= htmlspecialchars($song['artist']) ?></p>
            <p class="price">$<?= number_format((float)$song['price'], 2) ?></p>
            <p class="muted">Your download link is valid for 24 hours and up to 3 downloads.</p>
            <a class="btn" href="/public/download.php?token=<?= htmlspecialchars($token) ?>">Download MP3</a>
        </div>
        <p><a class="btn ghost" href="/public/index.php">Back to Store</a></p>
    </main>
</body>
</html>

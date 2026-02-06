<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';

$token = $_GET['token'] ?? '';
if ($token === '') {
    http_response_code(400);
    echo 'Missing token.';
    exit;
}

$stmt = db()->prepare(
    'SELECT p.id, p.downloads_count, p.max_downloads, p.token_expires_at, s.file_path, s.title
     FROM purchases p
     JOIN songs s ON s.id = p.song_id
     WHERE p.download_token = ? LIMIT 1'
);
$stmt->execute([$token]);
$purchase = $stmt->fetch();

if (!$purchase) {
    http_response_code(404);
    echo 'Invalid token.';
    exit;
}

$now = new DateTimeImmutable('now');
$expiresAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $purchase['token_expires_at']);
if (!$expiresAt || $expiresAt < $now) {
    http_response_code(403);
    echo 'Download link expired.';
    exit;
}

if ((int)$purchase['downloads_count'] >= (int)$purchase['max_downloads']) {
    http_response_code(403);
    echo 'Download limit reached.';
    exit;
}

$filePath = __DIR__ . '/../songs_private/' . $purchase['file_path'];
if (!is_file($filePath)) {
    http_response_code(404);
    echo 'File missing.';
    exit;
}

db()->prepare('UPDATE purchases SET downloads_count = downloads_count + 1 WHERE id = ?')
    ->execute([(int)$purchase['id']]);

header('Content-Type: audio/mpeg');
header('Content-Disposition: attachment; filename="' . basename($purchase['title']) . '.mp3"');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;

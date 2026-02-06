<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';

$songId = (int)($_GET['song_id'] ?? 0);
if ($songId <= 0) {
    http_response_code(400);
    echo 'Invalid song.';
    exit;
}

$stmt = db()->prepare('SELECT preview_path FROM songs WHERE id = ?');
$stmt->execute([$songId]);
$song = $stmt->fetch();

if (!$song) {
    http_response_code(404);
    echo 'Preview not found.';
    exit;
}

$filePath = __DIR__ . '/../previews/' . $song['preview_path'];
if (!is_file($filePath)) {
    http_response_code(404);
    echo 'Preview file missing.';
    exit;
}

header('Content-Type: audio/mpeg');
header('Content-Length: ' . filesize($filePath));
readfile($filePath);
exit;

<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $artist = trim($_POST['artist'] ?? '');
    $price = (float)($_POST['price'] ?? 0);

    if ($title === '' || $artist === '' || $price <= 0) {
        $error = 'Title, artist, and price are required.';
    } elseif (!isset($_FILES['song']) || $_FILES['song']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please upload a valid MP3 file.';
    } elseif (!isset($_FILES['preview']) || $_FILES['preview']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please upload a 30-second preview MP3.';
    } else {
        $songFile = $_FILES['song'];
        $previewFile = $_FILES['preview'];

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $songMime = $finfo->file($songFile['tmp_name']);
        $previewMime = $finfo->file($previewFile['tmp_name']);

        if ($songMime !== 'audio/mpeg' || $previewMime !== 'audio/mpeg') {
            $error = 'Files must be MP3 (audio/mpeg).';
        } else {
            $songName = 'song_' . bin2hex(random_bytes(8)) . '.mp3';
            $previewName = 'preview_' . bin2hex(random_bytes(8)) . '.mp3';

            $songDest = __DIR__ . '/../songs_private/' . $songName;
            $previewDest = __DIR__ . '/../previews/' . $previewName;

            if (!move_uploaded_file($songFile['tmp_name'], $songDest)) {
                $error = 'Failed to store full MP3.';
            } elseif (!move_uploaded_file($previewFile['tmp_name'], $previewDest)) {
                $error = 'Failed to store preview MP3.';
            } else {
                $stmt = db()->prepare(
                    'INSERT INTO songs (title, artist, price, file_path, preview_path) VALUES (?, ?, ?, ?, ?)'
                );
                $stmt->execute([$title, $artist, $price, $songName, $previewName]);
                $success = 'Upload complete.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Upload Track - MP3 Store</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <main class="container narrow">
        <h1>Upload Track</h1>
        <?php if ($error): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="alert success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="card">
            <label>
                Title
                <input type="text" name="title" required>
            </label>
            <label>
                Artist
                <input type="text" name="artist" required>
            </label>
            <label>
                Price (USD)
                <input type="number" name="price" step="0.01" min="0.01" required>
            </label>
            <label>
                Full MP3 File
                <input type="file" name="song" accept=".mp3,audio/mpeg" required>
            </label>
            <label>
                30-second Preview MP3
                <input type="file" name="preview" accept=".mp3,audio/mpeg" required>
            </label>
            <button class="btn" type="submit">Upload</button>
        </form>
        <p class="muted">Previews are served separately and should be exactly 30 seconds long.</p>
    </main>
</body>
</html>

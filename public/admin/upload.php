<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

require_admin();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $artist = trim($_POST['artist'] ?? '');
    $price = (float)($_POST['price'] ?? 0);

    if ($title === '' || $artist === '' || $price < 185) {
        $error = 'Title and artist are required. Price must be at least R185.';
    } elseif (!isset($_FILES['song']) || $_FILES['song']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please upload a valid MP3 file.';
    } elseif (!isset($_FILES['preview']) || $_FILES['preview']['error'] !== UPLOAD_ERR_OK) {
        $error = 'Please upload a 30-second preview MP3.';
    } else {
        $songFile = $_FILES['song'];
        $previewFile = $_FILES['preview'];
        $coverFile = $_FILES['cover'] ?? null;

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $songMime = $finfo->file($songFile['tmp_name']);
        $previewMime = $finfo->file($previewFile['tmp_name']);
        
        $hasCover = isset($coverFile) && $coverFile['error'] === UPLOAD_ERR_OK;
        $coverMime = $hasCover ? $finfo->file($coverFile['tmp_name']) : null;

        if ($songMime !== 'audio/mpeg' && $songMime !== 'audio/mp3') { // loosen check slightly if needed, but strict mime is better
             $error = 'Song must be MP3 (audio/mpeg). Detected: ' . $songMime;
        } elseif ($previewMime !== 'audio/mpeg' && $previewMime !== 'audio/mp3') {
             $error = 'Preview must be MP3 (audio/mpeg).';
        } elseif ($hasCover && !in_array($coverMime, ['image/jpeg', 'image/png', 'image/webp'])) {
             $error = 'Cover must be JPG, PNG, or WebP.';
        } else {
            $songName = 'song_' . bin2hex(random_bytes(8)) . '.mp3';
            $previewName = 'preview_' . bin2hex(random_bytes(8)) . '.mp3';
            $coverName = null;

            if ($hasCover) {
                $ext = match($coverMime) {
                    'image/jpeg' => '.jpg',
                    'image/png' => '.png',
                    'image/webp' => '.webp',
                };
                $coverName = 'cover_' . bin2hex(random_bytes(8)) . $ext;
            }

            $songDest = __DIR__ . '/../../songs_private/' . $songName;
            $previewDest = __DIR__ . '/../../previews/' . $previewName;
            $coverDest = $hasCover ? __DIR__ . '/../../assets/covers/' . $coverName : null;

            // Ensure covers directory exists
            if ($hasCover && !is_dir(dirname($coverDest))) {
                mkdir(dirname($coverDest), 0755, true);
            }

            if (!move_uploaded_file($songFile['tmp_name'], $songDest)) {
                $error = 'Failed to store full MP3.';
            } elseif (!move_uploaded_file($previewFile['tmp_name'], $previewDest)) {
                $error = 'Failed to store preview MP3.';
            } elseif ($hasCover && !move_uploaded_file($coverFile['tmp_name'], $coverDest)) {
                 $error = 'Failed to store cover image.';
            } else {
                $stmt = db()->prepare(
                    'INSERT INTO songs (title, artist, price, file_path, preview_path, cover_path) VALUES (?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute([$title, $artist, $price, $songName, $previewName, $coverName]);
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
    <link rel="stylesheet" href="../../assets/css/style.css">
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
                Price (ZAR)
                <input type="number" name="price" step="0.01" min="185" required>
            </label>
            <label>
                Full MP3 File
                <input type="file" name="song" accept=".mp3,audio/mpeg" required>
            </label>
            <label>
                Cover Image (Optional)
                <input type="file" name="cover" accept="image/jpeg,image/png,image/webp">
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

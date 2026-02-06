<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

$id = (int)($_GET['id'] ?? 0);
$song = null;

if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM songs WHERE id = ?');
    $stmt->execute([$id]);
    $song = $stmt->fetch();
}

if (!$song) {
    header('Location: /admin/dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $artist = trim($_POST['artist'] ?? '');
    $price = (float)($_POST['price'] ?? 0);

    if ($title === '' || $artist === '' || $price <= 0) {
        $error = 'Title, artist, and price are required.';
    } else {
        $stmt = db()->prepare('UPDATE songs SET title = ?, artist = ?, price = ? WHERE id = ?');
        $stmt->execute([$title, $artist, $price, $id]);
        $success = 'Song updated successfully.';
        
        // Refresh data
        $song['title'] = $title;
        $song['artist'] = $artist;
        $song['price'] = $price;
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Edit Song - MP3 Store</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <main class="container narrow">
        <header class="page-header">
            <h1>Edit Song</h1>
            <a href="/admin/dashboard.php">Back to Dashboard</a>
        </header>

        <?php if ($error): ?>
            <div class="alert"><?= htmlspecialchars($error) ?></div>
        <?php elseif ($success): ?>
            <div class="alert success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form method="post" class="card">
            <label>
                Title
                <input type="text" name="title" value="<?= htmlspecialchars($song['title']) ?>" required>
            </label>
            <label>
                Artist
                <input type="text" name="artist" value="<?= htmlspecialchars($song['artist']) ?>" required>
            </label>
            <label>
                Price (USD)
                <input type="number" name="price" step="0.01" min="0.01" value="<?= number_format((float)$song['price'], 2) ?>" required>
            </label>
            <button class="btn" type="submit">Update Song</button>
        </form>
    </main>
</body>
</html>

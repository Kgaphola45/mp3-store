<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$id = (int)($_GET['id'] ?? 0);
$song = null;

if ($id > 0) {
    $stmt = db()->prepare('SELECT * FROM songs WHERE id = ?');
    $stmt->execute([$id]);
    $song = $stmt->fetch();
}

if (!$song) {
    header('Location: index.php');
    exit;
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title><?= htmlspecialchars($song['title']) ?> - MP3 Store</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <main class="container">
        <header class="page-header">
            <a href="index.php">&larr; Back to Catalog</a>
        </header>

        <div class="card" style="display: flex; gap: 2rem; align-items: flex-start; padding: 2rem;">
            <div style="flex: 0 0 300px;">
                <?php if ($song['cover_path']): ?>
                    <img src="/assets/covers/<?= htmlspecialchars($song['cover_path']) ?>" alt="Cover Art" style="width:100%; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <?php else: ?>
                    <div style="width:100%; height:300px; background:#eee; display:flex; align-items:center; justify-content:center; border-radius: 8px;">
                        <span class="muted">No Cover</span>
                    </div>
                <?php endif; ?>
            </div>
            
            <div style="flex: 1;">
                <h1><?= htmlspecialchars($song['title']) ?></h1>
                <h2 class="muted"><?= htmlspecialchars($song['artist']) ?></h2>
                <p class="price" style="font-size: 1.5rem; margin: 1rem 0;">$<?= number_format((float)$song['price'], 2) ?></p>
                
                <div style="margin: 2rem 0;">
                    <p><strong>Preview:</strong></p>
                    <audio controls style="width: 100%;">
                        <source src="preview.php?song_id=<?= (int)$song['id'] ?>" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>

                <a class="btn primary large" href="buy.php?song_id=<?= (int)$song['id'] ?>">Buy Now & Download</a>
            </div>
        </div>
    </main>
</body>
</html>

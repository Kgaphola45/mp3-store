<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$songs = db()->query('SELECT id, title, artist, price, preview_path FROM songs ORDER BY created_at DESC')->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>MP3 Store</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <main class="container">
        <header class="page-header">
            <div>
                <h1>MP3 Store</h1>
                <p class="muted">Real-world digital sales system with secure previews and downloads.</p>
            </div>
            <a class="btn ghost" href="/admin/login.php">Admin</a>
        </header>

        <?php if (!$songs): ?>
            <div class="card">
                <p class="muted">No tracks available yet.</p>
            </div>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($songs as $song): ?>
                    <article class="card">
                        <h2><?= htmlspecialchars($song['title']) ?></h2>
                        <p class="muted"><?= htmlspecialchars($song['artist']) ?></p>
                        <audio controls preload="none">
                            <source src="/public/preview.php?song_id=<?= (int)$song['id'] ?>" type="audio/mpeg">
                            Your browser does not support the audio element.
                        </audio>
                        <div class="card-footer">
                            <span class="price">$<?= number_format((float)$song['price'], 2) ?></span>
                            <a class="btn" href="/public/buy.php?song_id=<?= (int)$song['id'] ?>">Buy & Download</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>

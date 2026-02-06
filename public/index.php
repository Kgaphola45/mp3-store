<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

$songs = db()->query('SELECT id, title, artist, price, preview_path, cover_path FROM songs ORDER BY created_at DESC')->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>MP3 Store</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <main class="container">
        <div class="hero">
            <h1>Sonic Vault.</h1>
            <p>Curated high-fidelity audio for the discerning collector. Secure, instant, premium.</p>
            <div style="position: relative; z-index: 2;">
                <a class="btn ghost" href="admin/login.php">Admin Access</a>
            </div>
        </div>
        
        <header class="page-header">
            <h2>Latest Drops</h2>
        </header>

        <?php if (!$songs): ?>
            <div class="card">
                <p class="muted">No tracks available yet.</p>
            </div>
        <?php else: ?>
            <div class="grid">
                <?php foreach ($songs as $song): ?>
                    <article class="card">
                        <?php if ($song['cover_path']): ?>
                            <a href="song.php?id=<?= $song['id'] ?>">
                                <img src="/assets/covers/<?= htmlspecialchars($song['cover_path']) ?>" alt="Cover Art" style="width:100%; height:auto; display:block; border-radius: 4px 4px 0 0;">
                            </a>
                        <?php else: ?>
                            <div style="width:100%; height:200px; background:#eee; display:flex; align-items:center; justify-content:center; border-radius: 4px 4px 0 0;">
                                <span class="muted">No Cover</span>
                            </div>
                        <?php endif; ?>
                        <div style="padding: 1rem;">
                            <h2><a href="song.php?id=<?= $song['id'] ?>"><?= htmlspecialchars($song['title']) ?></a></h2>
                            <p class="muted"><?= htmlspecialchars($song['artist']) ?></p>
                            <div class="card-footer">
                                <span class="price">R<?= number_format((float)$song['price'], 2) ?></span>
                                <a class="btn small" href="song.php?id=<?= $song['id'] ?>">View</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>

<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();

$songs = db()->query('SELECT id, title, artist, price, created_at FROM songs ORDER BY created_at DESC')->fetchAll();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard - MP3 Store</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <main class="container">
        <header class="page-header">
            <div>
                <h1>Dashboard</h1>
                <p class="muted">Manage uploads and track catalog.</p>
            </div>
            <a class="btn" href="/admin/upload.php">Upload New Track</a>
        </header>

        <section class="card">
            <h2>Catalog</h2>
            <?php if (!$songs): ?>
                <p class="muted">No songs uploaded yet.</p>
            <?php else: ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Artist</th>
                            <th>Price</th>
                            <th>Uploaded</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($songs as $song): ?>
                            <tr>
                                <td><?= htmlspecialchars($song['title']) ?></td>
                                <td><?= htmlspecialchars($song['artist']) ?></td>
                                <td>$<?= number_format((float)$song['price'], 2) ?></td>
                                <td><?= htmlspecialchars($song['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>

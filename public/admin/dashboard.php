<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

require_admin();

$songs = db()->query('SELECT id, title, artist, price, created_at FROM songs ORDER BY created_at DESC')->fetchAll();

// Analytics
$totalSongs = count($songs);
$totalSalesCount = db()->query('SELECT COUNT(*) FROM purchases')->fetchColumn();
$totalRevenue = db()->query('SELECT SUM(s.price) FROM purchases p JOIN songs s ON p.song_id = s.id')->fetchColumn() ?? 0;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard - MP3 Store</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <main class="container">
        <header class="page-header">
            <div>
                <h1>Dashboard</h1>
                <p class="muted">Manage uploads and track catalog.</p>
            </div>
            <a class="btn" href="upload.php">Upload New Track</a>
        </header>

        <section class="grid" style="grid-template-columns: repeat(3, 1fr); margin-bottom: 2rem;">
            <div class="card" style="text-align: center;">
                <h3 class="muted" style="font-size: 0.9rem; margin-bottom: 0.5rem;">CATALOG SIZE</h3>
                <p style="font-size: 2.5rem; font-weight: 700; margin: 0;"><?= $totalSongs ?></p>
            </div>
            <div class="card" style="text-align: center;">
                <h3 class="muted" style="font-size: 0.9rem; margin-bottom: 0.5rem;">TOTAL SALES</h3>
                <p style="font-size: 2.5rem; font-weight: 700; margin: 0;"><?= $totalSalesCount ?></p>
            </div>
            <div class="card" style="text-align: center;">
                <h3 class="muted" style="font-size: 0.9rem; margin-bottom: 0.5rem;">REVENUE</h3>
                <p style="font-size: 2.5rem; font-weight: 700; margin: 0; color: var(--success);">R<?= number_format((float)$totalRevenue, 2) ?></p>
            </div>
        </section>

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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($songs as $song): ?>
                            <tr>
                                <td><?= htmlspecialchars($song['title']) ?></td>
                                <td><?= htmlspecialchars($song['artist']) ?></td>
                                <td>R<?= number_format((float)$song['price'], 2) ?></td>
                                <td class="actions">
                                    <a href="edit.php?id=<?= $song['id'] ?>" class="btn small">Edit</a>
                                    <form method="post" action="delete.php" style="display:inline;" onsubmit="return confirm('Are you sure?');">
                                        <input type="hidden" name="id" value="<?= $song['id'] ?>">
                                        <button type="submit" class="btn small danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>

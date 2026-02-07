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
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <main class="container">
        <a href="index.php" class="btn ghost small" style="margin-bottom: 2rem;">&larr; Back to Store</a>
        
        <div class="card" style="display: flex; flex-wrap: wrap; gap: 2rem; align-items: flex-start;">
            <div style="flex: 1; min-width: 300px;">
                <?php if ($song['cover_path']): ?>
                    <img src="../assets/covers/<?= htmlspecialchars($song['cover_path']) ?>" alt="Cover Art" style="width:100%; border-radius: 16px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                <?php else: ?>
                    <div style="width:100%; aspect-ratio: 1; background: linear-gradient(135deg, #334155, #1e293b); display:flex; align-items:center; justify-content:center; border-radius: 16px;">
                        <span class="muted">No Cover</span>
                    </div>
                <?php endif; ?>
            </div>
            
            <div style="flex: 1.5; min-width: 300px;">
                <h1 style="margin-bottom: 0.5rem; line-height: 1.1;"><?= htmlspecialchars($song['title']) ?></h1>
                <h2 style="font-size: 1.5rem; color: var(--accent); margin-top: 0;"><?= htmlspecialchars($song['artist']) ?></h2>
                
                <div style="display: flex; align-items: center; gap: 1rem; margin: 2rem 0;">
                    <p class="price" style="font-size: 2.5rem; margin: 0;">R<?= number_format((float)$song['price'], 2) ?></p>
                </div>
                
                <div style="background: rgba(255,255,255,0.03); padding: 1.5rem; border-radius: 16px; margin-bottom: 2rem;">
                    <p style="margin-top: 0; margin-bottom: 0.5rem; font-weight: 500; color: var(--text-secondary);">PREVIEW TRACK</p>
                    <audio controls controlsList="nodownload">
                        <source src="preview.php?song_id=<?= (int)$song['id'] ?>" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>

                <div style="border-top: 1px solid var(--glass-border); padding-top: 2rem;">
                     <a class="btn large" href="buy.php?song_id=<?= (int)$song['id'] ?>">Buy Now & Download</a>
                     <p class="muted" style="margin-top: 1rem; font-size: 0.9rem; text-align: center;">Secure payment via MockPay. Instant download link provided.</p>
                </div>
            </div>
        </div>
    </main>
</body>
</html>

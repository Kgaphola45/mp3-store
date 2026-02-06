<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/auth.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    
    if ($id > 0) {
        $stmt = db()->prepare('SELECT file_path, preview_path, cover_path FROM songs WHERE id = ?');
        $stmt->execute([$id]);
        $song = $stmt->fetch();

        if ($song) {
            // Delete files
            $files = [
                __DIR__ . '/../../songs_private/' . $song['file_path'],
                __DIR__ . '/../../previews/' . $song['preview_path']
            ];
            
            if ($song['cover_path']) {
                $files[] = __DIR__ . '/../../assets/covers/' . $song['cover_path'];
            }

            foreach ($files as $file) {
                if (file_exists($file)) {
                    unlink($file);
                }
            }

            // Delete DB record
            $stmt = db()->prepare('DELETE FROM songs WHERE id = ?');
            $stmt->execute([$id]);
        }
    }
}

header('Location: dashboard.php');
exit;

<?php
require_once __DIR__ . '/includes/db.php';

try {
    $pdo = db();
    echo "Connected to database.\n";
    
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM songs LIKE 'cover_path'");
    if ($stmt->fetch()) {
        echo "Column 'cover_path' already exists.\n";
    } else {
        $pdo->exec("ALTER TABLE songs ADD COLUMN cover_path VARCHAR(255) DEFAULT NULL AFTER artist");
        echo "Added 'cover_path' column to 'songs' table.\n";
    }

    // Ensure songs_private directory exists
    $privateDir = __DIR__ . '/songs_private';
    if (!is_dir($privateDir)) {
        mkdir($privateDir, 0755, true);
        echo "Created directory: $privateDir\n";
    } else {
        echo "Directory exists: $privateDir\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

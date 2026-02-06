<?php
require_once __DIR__ . '/includes/db.php';

$previewDir = __DIR__ . '/previews';
$privateDir = __DIR__ . '/songs_private';
$files = glob($previewDir . '/*.mp3');

$pdo = db();
$imported = 0;

echo "Found " . count($files) . " files in previews.\n";

foreach ($files as $sourcePath) {
    $filename = basename($sourcePath);
    $destPath = $privateDir . '/' . $filename;
    
    // 1. Copy to songs_private if missing
    if (!file_exists($destPath)) {
        if (copy($sourcePath, $destPath)) {
            echo "Copied $filename to songs_private.\n";
        } else {
            echo "Failed to copy $filename.\n";
            continue;
        }
    } else {
        echo "$filename already exists in songs_private.\n";
    }

    // 2. Insert into DB if missing
    $stmt = $pdo->prepare("SELECT id FROM songs WHERE file_path = ?");
    $stmt->execute([$filename]);
    if ($stmt->fetch()) {
        echo " - Record already exists in DB.\n";
        continue;
    }

    $nameParts = pathinfo($filename, PATHINFO_FILENAME);
    $title = $nameParts;
    $artist = 'Unknown Artist';
    
    $stmt = $pdo->prepare("INSERT INTO songs (title, artist, price, file_path, preview_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $artist, 0.99, $filename, $filename]);
    $imported++;
    echo " - Imported into DB as '$title'.\n";
}

echo "Done. Imported $imported songs.\n";

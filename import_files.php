<?php
require_once __DIR__ . '/includes/db.php';

// Scan for MP3s in songs_private
$privateDir = __DIR__ . '/songs_private';
$privateFiles = glob($privateDir . '/*.mp3');

$pdo = db();
$imported = 0;

foreach ($privateFiles as $filePath) {
    $filename = basename($filePath);
    
    // Check if already in DB
    $stmt = $pdo->prepare("SELECT id FROM songs WHERE file_path = ?");
    $stmt->execute([$filename]);
    if ($stmt->fetch()) {
        continue;
    }
    
    // Attempt to guess title/artist from filename
    // Format assumption: Artist - Title.mp3 or just Title.mp3
    $nameParts = pathinfo($filename, PATHINFO_FILENAME);
    $parts = explode('-', $nameParts);
    
    if (count($parts) >= 2) {
        $artist = trim($parts[0]);
        $title = trim($parts[1]);
    } else {
        $artist = 'Unknown Artist';
        $title = trim($nameParts);
    }
    
    // Look for a matching preview
    // Simplistic check: look for a file that contains the title in previews/
    // OR create a dummy entry if you just want it to work.
    // For now, let's assume we need to generate a "missing" preview or reuse one.
    // Ideally, the admin should upload properly. 
    // BUT, since the user says "trans in preview folder", maybe there is a matching file.
    
    // Let's list previews to see if we can match
    // Strategy: Just insert it with a placeholder preview path if exact match not found
    
    $previewPath = 'demo_preview.mp3'; // Default fallback
    
    // Insert
    $price = 0.99; // Default price
    
    echo "Importing: $title by $artist ($filename)...\n";
    
    $stmt = $pdo->prepare("INSERT INTO songs (title, artist, price, file_path, preview_path) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$title, $artist, $price, $filename, $previewPath]);
    $imported++;
}

echo "Imported $imported songs.\n";
if ($imported > 0) {
    echo "NOTE: Default preview 'demo_preview.mp3' assigned. You might want to update this in Admin.\n";
}

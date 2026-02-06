<?php
require_once __DIR__ . '/includes/db.php';

echo "--- Database Songs ---\n";
try {
    $stmt = db()->query("SELECT id, title, file_path, preview_path FROM songs");
    $songs = $stmt->fetchAll();
    if (count($songs) === 0) {
        echo "No songs found in database table 'songs'.\n";
    } else {
        foreach ($songs as $song) {
            echo "ID: {$song['id']}, Title: {$song['title']}, File: {$song['file_path']}, Preview: {$song['preview_path']}\n";
        }
    }
} catch (Exception $e) {
    echo "DB Error: " . $e->getMessage() . "\n";
}

echo "\n--- Preview Files (d:/xampp/htdocs/mp3-store/previews) ---\n";
$previews = glob(__DIR__ . '/previews/*');
foreach ($previews as $file) {
    echo basename($file) . "\n";
}

echo "\n--- Private Files (d:/xampp/htdocs/mp3-store/songs_private) ---\n";
$private = glob(__DIR__ . '/songs_private/*');
foreach ($private as $file) {
    echo basename($file) . "\n";
}

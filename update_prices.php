<?php
require_once __DIR__ . '/includes/db.php';

try {
    $pdo = db();
    
    // Update all songs with price < 185 to 185
    $stmt = $pdo->query("UPDATE songs SET price = 185 WHERE price < 185");
    $count = $stmt->rowCount();
    
    echo "Updated $count songs to the minimum price of R185.\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

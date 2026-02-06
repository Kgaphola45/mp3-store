<?php
require_once __DIR__ . '/includes/db.php';

try {
    $pdo = db();
    
    $username = 'admin';
    $password = 'password123';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Update existing user
        $stmt = $pdo->prepare("UPDATE admins SET password_hash = ? WHERE username = ?");
        $stmt->execute([$hash, $username]);
        echo "Updated password for existing user '$username'.\n";
    } else {
        // Create new user
        $stmt = $pdo->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
        $stmt->execute([$username, $hash]);
        echo "Created new user '$username'.\n";
    }
    
    echo "Password has been set to: $password\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

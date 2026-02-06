<?php
require_once __DIR__ . '/includes/db.php';

try {
    $pdo = db();
    
    $username = 'admin';
    $password = 'password123';
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT id FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->fetch()) {
        echo "Admin user '$username' already exists.\n";
    } else {
        $stmt = $pdo->prepare("INSERT INTO admins (username, password_hash) VALUES (?, ?)");
        $stmt->execute([$username, $hash]);
        echo "Created admin user.\nUsername: $username\nPassword: $password\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

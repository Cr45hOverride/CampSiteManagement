<?php
require 'db_config.php';

try {
    // Create table if not exists
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            role ENUM('admin', 'staff') DEFAULT 'admin',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ");
    echo "Table verified/created.\n";
} catch (Exception $e) {
    echo "Table creation error: " . $e->getMessage() . "\n";
}

$username = 'azian@kamalharmoni.com';
$password = 'azian780808';
$hash = password_hash($password, PASSWORD_DEFAULT);
try {
    $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, 'admin')")->execute([$username, $hash]);
    echo "User created successfully.\n";
} catch (Exception $e) {
    echo "User already exists or error: " . $e->getMessage() . "\n";
}

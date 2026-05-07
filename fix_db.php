<?php
require 'db_config.php';
try {
    $pdo->exec("ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP");
    echo "Added created_at column successfully.";
} catch(Exception $e) {
    echo "Error or already exists: " . $e->getMessage();
}

<?php
require 'db_config.php';
try {
    $pdo->exec("ALTER TABLE bookings ADD COLUMN payment_reference VARCHAR(100) DEFAULT NULL");
    $pdo->exec("ALTER TABLE bookings ADD COLUMN payment_proof VARCHAR(255) DEFAULT NULL");
    echo "Proof columns added successfully.";
} catch(Exception $e) {
    echo "Error or already exists: " . $e->getMessage();
}

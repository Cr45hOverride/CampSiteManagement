<?php
require 'db_config.php';
try {
    $pdo->exec("ALTER TABLE bookings ADD COLUMN payment_amount DECIMAL(10,2) DEFAULT 0.00");
    $pdo->exec("ALTER TABLE bookings ADD COLUMN payment_status ENUM('unpaid', 'partial', 'paid') DEFAULT 'unpaid'");
    echo "Payment columns added successfully.";
} catch(Exception $e) {
    echo "Error or already exists: " . $e->getMessage();
}

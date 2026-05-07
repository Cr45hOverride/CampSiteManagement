<?php
require 'db_config.php';
try {
    $pdo->exec("ALTER TABLE sites ADD COLUMN remark TEXT NULL AFTER status");
    echo "Column 'remark' added successfully.";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column 'remark' already exists.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}

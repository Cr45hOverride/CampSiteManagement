<?php
// seed_sites.php
// Helper file to auto-generate Tapak 1 to 20
require 'db_config.php';

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM sites");
    $count = $stmt->fetchColumn();

    if ($count == 0) {
        $insertStmt = $pdo->prepare("INSERT INTO sites (tapak_name, status) VALUES (?, 'active')");
        for ($i = 1; $i <= 20; $i++) {
            $insertStmt->execute(["Tapak $i"]);
        }
        echo "Successfully inserted Tapak 1 to Tapak 20 into the database.";
    } else {
        echo "Database already contains $count sites. No action taken to avoid duplicates.";
    }
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage();
}
?>

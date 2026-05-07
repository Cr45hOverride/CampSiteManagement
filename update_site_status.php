<?php
// update_site_status.php
session_start();
require 'db_config.php';

header('Content-Type: application/json');

// In a real application with a full login system, you would verify the admin role here:
// if (($_SESSION['user_role'] ?? 'staff') !== 'admin') {
//     echo json_encode(['success' => false, 'message' => 'Unauthorized. Only admins can manage sites.']);
//     exit;
// }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $site_id = filter_input(INPUT_POST, 'site_id', FILTER_VALIDATE_INT);
    $status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

    if (!$site_id || !in_array($status, ['active', 'maintenance'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid data provided.']);
        exit;
    }

    try {
        $stmt = $pdo->prepare("UPDATE sites SET status = ? WHERE id = ?");
        $stmt->execute([$status, $site_id]);

        echo json_encode(['success' => true, 'message' => 'Site status updated successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>

<?php
// api_sites.php
require 'auth.php';
require 'db_config.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($action === 'create') {
            $tapak_name = trim($_POST['tapak_name'] ?? '');
            $status = $_POST['status'] ?? 'active';
            $remark = trim($_POST['remark'] ?? '');

            if (!$tapak_name) throw new Exception("Site name is required.");

            $stmt = $pdo->prepare("INSERT INTO sites (tapak_name, status, remark) VALUES (?, ?, ?)");
            $stmt->execute([$tapak_name, $status, $remark]);
            echo json_encode(['success' => true, 'message' => 'Site created successfully.']);

        } elseif ($action === 'update') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $tapak_name = trim($_POST['tapak_name'] ?? '');
            $status = $_POST['status'] ?? 'active';
            $remark = trim($_POST['remark'] ?? '');

            if (!$id || !$tapak_name) throw new Exception("Invalid input data.");

            $stmt = $pdo->prepare("UPDATE sites SET tapak_name = ?, status = ?, remark = ? WHERE id = ?");
            $stmt->execute([$tapak_name, $status, $remark, $id]);
            echo json_encode(['success' => true, 'message' => 'Site updated successfully.']);

        } elseif ($action === 'delete') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) throw new Exception("Invalid Site ID.");

            $stmt = $pdo->prepare("DELETE FROM sites WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Site deleted successfully.']);

        } else {
            throw new Exception("Invalid action specified.");
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

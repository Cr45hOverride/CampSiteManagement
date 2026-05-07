<?php
require 'auth.php';
if ($_SESSION['user_role'] !== 'admin') {
    die(json_encode(['success' => false, 'message' => 'Admins only.']));
}
require 'db_config.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if ($action === 'create') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? 'staff';

            if (!$username || !$password) throw new Exception("All fields required.");
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (username, password_hash, role) VALUES (?, ?, ?)");
            $stmt->execute([$username, $hash, $role]);
            echo json_encode(['success' => true]);

        } elseif ($action === 'reset_password') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            $password = $_POST['password'] ?? '';

            if (!$id || !$password) throw new Exception("All fields required.");
            $hash = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $stmt->execute([$hash, $id]);
            echo json_encode(['success' => true]);

        } elseif ($action === 'delete') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if ($id == $_SESSION['user_id']) throw new Exception("Cannot delete yourself.");
            
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true]);
        }
    } catch (Exception $e) {
        if ($e->getCode() == 23000) {
            echo json_encode(['success' => false, 'message' => 'Username already exists.']);
        } else {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

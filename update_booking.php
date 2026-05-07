<?php
// update_booking.php
require 'auth.php';
require 'db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate inputs
    $booking_id = filter_input(INPUT_POST, 'booking_id', FILTER_VALIDATE_INT);
    $site_id = filter_input(INPUT_POST, 'site_id', FILTER_VALIDATE_INT);
    $booking_name = htmlspecialchars(trim($_POST['booking_name'] ?? ''));
    $booking_contact = htmlspecialchars(trim($_POST['booking_contact'] ?? ''));
    $check_in = filter_input(INPUT_POST, 'check_in', FILTER_SANITIZE_STRING);
    $check_out = filter_input(INPUT_POST, 'check_out', FILTER_SANITIZE_STRING);
    $booking_remark = htmlspecialchars(trim($_POST['booking_remark'] ?? ''));
    $payment_amount = filter_input(INPUT_POST, 'payment_amount', FILTER_VALIDATE_FLOAT) ?: 0.00;
    $payment_status = $_POST['payment_status'] ?? 'unpaid';
    $payment_reference = htmlspecialchars(trim($_POST['payment_reference'] ?? ''));

    // Logic Fix: If there is evidence, it should not remain 'unpaid'
    $hasFile = isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK;
    if (($payment_reference !== '' || $hasFile) && $payment_status === 'unpaid') {
        $payment_status = 'partial'; // Auto-upgrade to partial if evidence is provided
    }

    if (!$booking_id || !$site_id || !$booking_name || !$booking_contact || !$check_in || !$check_out) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit;
    }

    if (strtotime($check_in) >= strtotime($check_out)) {
        echo json_encode(['success' => false, 'message' => 'Check-out date must be after check-in date.']);
        exit;
    }

    try {
        // Validate Availability: Ensure no overlapping bookings for the same site (excluding THIS booking)
        // Overlap condition: existing.check_in < new.check_out AND existing.check_out > new.check_in
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM bookings 
            WHERE site_id = ? 
            AND check_in < ? 
            AND check_out > ?
            AND id != ?
        ");
        $stmt->execute([$site_id, $check_out, $check_in, $booking_id]);
        $conflict_count = $stmt->fetchColumn();

        if ($conflict_count > 0) {
            echo json_encode(['success' => false, 'message' => 'The selected Site is not available for these dates.']);
            exit;
        }

        // Handle file upload if present
        $proof_query_part = "";
        $queryParams = [
            $site_id, $booking_name, $booking_contact, $check_in, $check_out, 
            $booking_remark, $payment_amount, $payment_status, $payment_reference
        ];

        if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            $fileExt = strtolower(pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION));
            $allowedExts = ['jpg', 'jpeg', 'png', 'pdf'];
            
            if (in_array($fileExt, $allowedExts)) {
                $newFileName = 'proof_' . $booking_id . '_' . time() . '.' . $fileExt;
                if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $uploadDir . $newFileName)) {
                    $proof_query_part = ", payment_proof = ?";
                    $queryParams[] = $newFileName;
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid file format. Only JPG, PNG, and PDF allowed.']);
                exit;
            }
        }

        $queryParams[] = $booking_id; // Add ID for the WHERE clause

        // Proceed to update the booking
        $updateStmt = $pdo->prepare("
            UPDATE bookings 
            SET site_id = ?, booking_name = ?, booking_contact = ?, check_in = ?, check_out = ?, booking_remark = ?, payment_amount = ?, payment_status = ?, payment_reference = ? $proof_query_part
            WHERE id = ?
        ");
        
        $updateStmt->execute($queryParams);

        echo json_encode([
            'success' => true, 
            'message' => 'Booking updated successfully!'
        ]);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>

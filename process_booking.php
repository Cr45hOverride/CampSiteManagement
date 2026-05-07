<?php
// process_booking.php
require 'auth.php';
require 'db_config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Sanitize and validate inputs
    $site_id = filter_input(INPUT_POST, 'site_id', FILTER_VALIDATE_INT);
    $booking_name = htmlspecialchars(trim($_POST['booking_name'] ?? ''));
    $booking_contact = htmlspecialchars(trim($_POST['booking_contact'] ?? ''));
    $check_in = filter_input(INPUT_POST, 'check_in', FILTER_SANITIZE_STRING);
    $check_out = filter_input(INPUT_POST, 'check_out', FILTER_SANITIZE_STRING);
    $booking_remark = htmlspecialchars(trim($_POST['booking_remark'] ?? ''));

    // Check for missing mandatory fields
    if (!$site_id || !$booking_name || !$booking_contact || !$check_in || !$check_out) {
        echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
        exit;
    }

    // Logical date check
    if (strtotime($check_in) >= strtotime($check_out)) {
        echo json_encode(['success' => false, 'message' => 'Check-out date must be after check-in date.']);
        exit;
    }

    try {
        // 2. Validate Availability (Prevent Double Bookings)
        // Overlap Condition: existing booking check_in < new check_out AND existing check_out > new check_in
        $stmt = $pdo->prepare("
            SELECT COUNT(*) FROM bookings 
            WHERE site_id = ? 
            AND check_in < ? 
            AND check_out > ?
        ");
        $stmt->execute([$site_id, $check_out, $check_in]);
        $conflict_count = $stmt->fetchColumn();

        if ($conflict_count > 0) {
            echo json_encode(['success' => false, 'message' => 'The selected Site is not available for these dates.']);
            exit;
        }

        // 3. Insert new booking
        $insertStmt = $pdo->prepare("
            INSERT INTO bookings (site_id, booking_name, booking_contact, check_in, check_out, booking_remark)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        
        $insertStmt->execute([
            $site_id, 
            $booking_name, 
            $booking_contact, 
            $check_in, 
            $check_out, 
            $booking_remark
        ]);

        $last_id = $pdo->lastInsertId();
        
        // 4. Generate unique Booking ID string as requested (e.g. SIA-YYYYMMDD-ID)
        $booking_id_string = "SIA-" . date('Ymd', strtotime($check_in)) . "-" . $last_id;

        echo json_encode([
            'success' => true, 
            'message' => 'Booking successful!',
            'booking_ref' => $booking_id_string
        ]);

    } catch (PDOException $e) {
        // Log the actual error internally but return generic msg
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>

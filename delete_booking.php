<?php
// delete_booking.php
require 'auth.php';
require 'db_config.php';

header('Content-Type: application/json');

// NOTE: For demonstration purposes, if session role isn't set, we assume 'staff'.
// In production, users would log in and session role would be populated.
$user_role = $_SESSION['user_role'] ?? 'staff';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = filter_input(INPUT_POST, 'booking_id', FILTER_VALIDATE_INT);

    if (!$booking_id) {
        echo json_encode(['success' => false, 'message' => 'Invalid booking ID.']);
        exit;
    }

    try {
        // 1. Fetch the booking check-in date
        $stmt = $pdo->prepare("SELECT check_in FROM bookings WHERE id = ?");
        $stmt->execute([$booking_id]);
        $booking = $stmt->fetch();

        if (!$booking) {
            echo json_encode(['success' => false, 'message' => 'Booking not found.']);
            exit;
        }

        // 2. The 7-Day Rule logic
        $check_in_date = new DateTime($booking['check_in']);
        $today = new DateTime('today'); // Sets to 00:00:00 today
        
        $interval = $today->diff($check_in_date);
        $days_difference = (int)$interval->format('%R%a'); // Positive if check_in is in the future

        // 3. Block cancellation if less than 7 days, unless admin
        if ($days_difference < 7) {
            if ($user_role !== 'admin') {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Cancellation Denied: Bookings within 7 days of check-in can only be deleted by an Admin.'
                ]);
                exit;
            }
        }

        // 4. Proceed to delete
        $deleteStmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
        $deleteStmt->execute([$booking_id]);

        echo json_encode(['success' => true, 'message' => 'Booking deleted successfully.']);

    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>

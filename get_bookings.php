<?php
// get_bookings.php
// Helper file to fetch bookings for AJAX dashboard
require 'auth.php';
require 'db_config.php';

header('Content-Type: application/json');

$site_filter = $_GET['site_id'] ?? 'all';
$date_start = $_GET['date_start'] ?? '';
$date_end = $_GET['date_end'] ?? '';

// Build dynamic query
$query = "SELECT b.*, s.tapak_name FROM bookings b JOIN sites s ON b.site_id = s.id WHERE 1=1";
$params = [];

if ($site_filter !== 'all' && is_numeric($site_filter)) {
    $query .= " AND b.site_id = ?";
    $params[] = $site_filter;
}

if (!empty($date_start)) {
    $query .= " AND b.check_in >= ?";
    $params[] = $date_start;
}

if (!empty($date_end)) {
    $query .= " AND b.check_in <= ?";
    $params[] = $date_end;
}

$query .= " ORDER BY b.check_in ASC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $bookings = $stmt->fetchAll();
    
    echo json_encode(['success' => true, 'data' => $bookings]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
?>

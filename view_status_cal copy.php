<?php
// view_status_cal.php
require 'db_config.php';

// Determine month and year to view
$month = isset($_GET['m']) ? (int)$_GET['m'] : (int)date('m');
$year = isset($_GET['y']) ? (int)$_GET['y'] : (int)date('Y');

// Adjust for previous/next year overflow
if ($month < 1) { $month = 12; $year--; }
if ($month > 12) { $month = 1; $year++; }

// Calculate start and end dates for the query
$firstDayOfMonth = sprintf('%04d-%02d-01', $year, $month);
$lastDayOfMonth = date('Y-m-t', strtotime($firstDayOfMonth));

// Fetch bookings that overlap with this month
$bookings = [];
try {
    $stmt = $pdo->prepare("
        SELECT b.*, s.tapak_name 
        FROM bookings b 
        JOIN sites s ON b.site_id = s.id 
        WHERE b.check_in <= ? AND b.check_out > ?
    ");
    $stmt->execute([$lastDayOfMonth, $firstDayOfMonth]);
    $bookings = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Map bookings to specific days in the calendar
$calendarData = [];
foreach ($bookings as $b) {
    $in = new DateTime($b['check_in']);
    $out = new DateTime($b['check_out']);
    
    // Iterate through each day of the booking (until check_out - 1 day)
    $current = clone $in;
    while ($current < $out) {
        $dateStr = $current->format('Y-m-d');
        if (!isset($calendarData[$dateStr])) {
            $calendarData[$dateStr] = [];
        }
        // Save detailed data for the modal
        $calendarData[$dateStr][] = [
            'id' => $b['id'],
            'tapak_name' => $b['tapak_name'],
            'guest' => $b['booking_name'],
            'contact' => $b['booking_contact'],
            'check_in' => $b['check_in'],
            'check_out' => $b['check_out'],
            'remark' => $b['booking_remark']
        ];
        $current->modify('+1 day');
    }
}

// Build Calendar Grid Info
$firstDayTimestamp = strtotime($firstDayOfMonth);
$daysInMonth = date('t', $firstDayTimestamp);
$firstDayOfWeek = date('N', $firstDayTimestamp); 

$prevMonth = $month - 1;
$prevYear = $year;
$nextMonth = $month + 1;
$nextYear = $year;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sia Campsite - Calendar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Inter', 'Segoe UI', sans-serif; }
        .navbar-brand { font-weight: bold; color: #2c3e50 !important; }
        .calendar-card { border-radius: 12px; border: none; }
        .calendar-table th { text-align: center; background-color: #f8f9fa; width: 14.28%; }
        .calendar-table td { height: 120px; vertical-align: top; padding: 8px; border: 1px solid #dee2e6; }
        .calendar-table td.empty { background-color: #fcfcfc; }
        .date-num { font-weight: bold; color: #6c757d; margin-bottom: 5px; display: block; }
        .today .date-num { color: white; background-color: #0d6efd; border-radius: 50%; width: 25px; height: 25px; text-align: center; line-height: 25px; }
        .booking-badge { 
            display: block; font-size: 0.75rem; font-weight: 600; margin-bottom: 4px; 
            padding: 4px 6px; border-radius: 4px; white-space: nowrap; 
            overflow: hidden; text-overflow: ellipsis; cursor: pointer; 
            box-shadow: 0 1px 2px rgba(0,0,0,0.1); transition: transform 0.1s; 
        }
        .booking-badge:hover { transform: scale(1.02); filter: brightness(1.1); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php"><i class="fas fa-campground text-success me-2"></i>Sia Campsite</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">New Booking</a></li>
                <li class="nav-item"><a class="nav-link" href="booking_status.php">Booking Status</a></li>
                <li class="nav-item"><a class="nav-link active" href="view_status_cal.php">Calendar</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_sites.php">Manage Site</a></li>
                <li class="nav-item"><a class="nav-link" href="info.php">Info Setup</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid pb-5 px-lg-4">
    <div class="card shadow-sm calendar-card">
        <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center rounded-top-3">
            <a href="?m=<?= $prevMonth ?>&y=<?= $prevYear ?>" class="btn btn-sm btn-light"><i class="fas fa-chevron-left"></i> Prev</a>
            <h5 class="mb-0 fw-bold"><?= date('F Y', $firstDayTimestamp) ?></h5>
            <a href="?m=<?= $nextMonth ?>&y=<?= $nextYear ?>" class="btn btn-sm btn-light">Next <i class="fas fa-chevron-right"></i></a>
        </div>
        <div class="card-body p-0 table-responsive">
            <table class="table table-bordered calendar-table mb-0" style="min-width: 800px;">
                <thead>
                    <tr>
                        <th>Monday</th>
                        <th>Tuesday</th>
                        <th>Wednesday</th>
                        <th>Thursday</th>
                        <th>Friday</th>
                        <th>Saturday</th>
                        <th>Sunday</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                    <?php
                    $dayCount = 1;
                    $currentDayOfWeek = 1;

                    // Empty cells for days before the 1st of the month
                    for ($i = 1; $i < $firstDayOfWeek; $i++) {
                        echo "<td class='empty'></td>";
                        $currentDayOfWeek++;
                    }

                    // Days of the month
                    while ($dayCount <= $daysInMonth) {
                        $currentDate = sprintf('%04d-%02d-%02d', $year, $month, $dayCount);
                        $isToday = ($currentDate === date('Y-m-d')) ? 'today' : '';
                        
                        echo "<td class='$isToday'>";
                        echo "<span class='date-num'>$dayCount</span>";
                        
                        if (isset($calendarData[$currentDate])) {
                            foreach ($calendarData[$currentDate] as $booking) {
                                // Assign distinct colors based on Tapak Name
                                $colors = ['bg-success', 'bg-danger', 'bg-warning text-dark', 'bg-info text-dark', 'bg-primary', 'bg-secondary'];
                                $cIdx = crc32($booking['tapak_name']) % count($colors);
                                $colorClass = $colors[$cIdx];
                                
                                // Embed data attributes for the modal
                                echo "<div class='booking-badge $colorClass text-white' 
                                        data-tapak='" . htmlspecialchars($booking['tapak_name']) . "'
                                        data-guest='" . htmlspecialchars($booking['guest']) . "'
                                        data-contact='" . htmlspecialchars($booking['contact']) . "'
                                        data-checkin='" . htmlspecialchars($booking['check_in']) . "'
                                        data-checkout='" . htmlspecialchars($booking['check_out']) . "'
                                        data-remark='" . htmlspecialchars($booking['remark'] ?? '-') . "'
                                        title='Click for details: {$booking['guest']}'>";
                                echo htmlspecialchars($booking['tapak_name']);
                                echo "</div>";
                            }
                        }
                        echo "</td>";

                        if ($currentDayOfWeek == 7) {
                            echo "</tr>";
                            if ($dayCount < $daysInMonth) {
                                echo "<tr>";
                            }
                            $currentDayOfWeek = 0;
                        }

                        $dayCount++;
                        $currentDayOfWeek++;
                    }

                    // Empty cells to finish the last row
                    if ($currentDayOfWeek > 1) {
                        for ($i = $currentDayOfWeek; $i <= 7; $i++) {
                            echo "<td class='empty'></td>";
                        }
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Booking Detail Modal -->
<div class="modal fade" id="bookingDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold"><i class="fas fa-campground me-2"></i>Booking Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4">
        <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
            <div>
                <span class="text-muted small text-uppercase fw-bold">Site Name</span>
                <h5 id="modal-tapak" class="mb-0 text-primary fw-bold"></h5>
            </div>
            <div class="text-end">
                <span class="text-muted small text-uppercase fw-bold">Guest</span>
                <h6 id="modal-guest" class="mb-0 fw-bold"></h6>
            </div>
        </div>
        
        <div class="row mb-3">
            <div class="col-6">
                <span class="text-muted small text-uppercase fw-bold"><i class="fas fa-sign-in-alt me-1"></i>Check-in</span>
                <p id="modal-checkin" class="mb-0 fw-bold"></p>
            </div>
            <div class="col-6 text-end">
                <span class="text-muted small text-uppercase fw-bold"><i class="fas fa-sign-out-alt me-1"></i>Check-out</span>
                <p id="modal-checkout" class="mb-0 fw-bold"></p>
            </div>
        </div>

        <div class="mb-3">
            <span class="text-muted small text-uppercase fw-bold"><i class="fas fa-phone-alt me-1"></i>Contact Number</span>
            <p id="modal-contact" class="mb-0"></p>
        </div>

        <div class="bg-light p-3 rounded">
            <span class="text-muted small text-uppercase fw-bold"><i class="fas fa-sticky-note me-1"></i>Guest Remarks</span>
            <p id="modal-remark" class="mb-0 small"></p>
        </div>
      </div>
      <div class="modal-footer border-top-0">
        <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Handle click on booking badges in the calendar
    $('.booking-badge').click(function() {
        
        // Extract data attributes embedded in the HTML element
        let tapak = $(this).data('tapak');
        let guest = $(this).data('guest');
        let contact = $(this).data('contact');
        let checkin = $(this).data('checkin');
        let checkout = $(this).data('checkout');
        let remark = $(this).data('remark');

        // Populate the modal fields
        $('#modal-tapak').text(tapak);
        $('#modal-guest').text(guest);
        $('#modal-contact').text(contact);
        $('#modal-checkin').text(checkin);
        $('#modal-checkout').text(checkout);
        $('#modal-remark').text(remark && remark !== '-' ? remark : 'No remarks provided.');

        // Initialize and show the Bootstrap Modal
        var detailModal = new bootstrap.Modal(document.getElementById('bookingDetailModal'));
        detailModal.show();
    });
});
</script>
</body>
</html>

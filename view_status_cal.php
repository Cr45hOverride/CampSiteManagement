<?php
// view_status_cal.php
require 'auth.php';
require 'db_config.php';

// Fetch all sites for the edit dropdown
$sitesList = [];
try {
    $stmt = $pdo->query("SELECT id, tapak_name FROM sites ORDER BY id ASC");
    $sitesList = $stmt->fetchAll();
} catch (PDOException $e) {}

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
            'site_id' => $b['site_id'],
            'tapak_name' => $b['tapak_name'],
            'guest' => $b['booking_name'],
            'contact' => $b['booking_contact'],
            'check_in' => $b['check_in'],
            'check_out' => $b['check_out'],
            'remark' => $b['booking_remark'],
            'payment_amount' => $b['payment_amount'] ?? 0.00,
            'payment_status' => $b['payment_status'] ?? 'unpaid',
            'payment_reference' => $b['payment_reference'],
            'payment_proof' => $b['payment_proof']
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
                <li class="nav-item"><a class="nav-link" href="manage_users.php">Users</a></li>
                <li class="nav-item ms-lg-3"><a class="nav-link text-danger fw-bold" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
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
                                        data-id='" . htmlspecialchars($booking['id']) . "'
                                        data-site-id='" . htmlspecialchars($booking['site_id']) . "'
                                        data-tapak='" . htmlspecialchars($booking['tapak_name']) . "'
                                        data-guest='" . htmlspecialchars($booking['guest']) . "'
                                        data-contact='" . htmlspecialchars($booking['contact']) . "'
                                        data-checkin='" . htmlspecialchars($booking['check_in']) . "'
                                        data-checkout='" . htmlspecialchars($booking['check_out']) . "'
                                        data-remark='" . htmlspecialchars($booking['remark'] ?? '-') . "'
                                        data-payment-amount='" . htmlspecialchars($booking['payment_amount']) . "'
                                        data-payment-status='" . htmlspecialchars($booking['payment_status']) . "'
                                        data-payment-reference='" . htmlspecialchars($booking['payment_reference'] ?? '') . "'
                                        data-payment-proof='" . htmlspecialchars($booking['payment_proof'] ?? '') . "'
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

        <div id="modal-payment-box" class="p-3 rounded mb-3 border">
            <div class="row">
                <div class="col-6">
                    <span class="text-muted small text-uppercase fw-bold"><i class="fas fa-money-bill-wave me-1"></i>Amount (RM)</span>
                    <p id="modal-payment-amount" class="mb-0 fw-bold"></p>
                </div>
                <div class="col-6 text-end">
                    <span class="text-muted small text-uppercase fw-bold"><i class="fas fa-check-circle me-1"></i>Status</span>
                    <p id="modal-payment-status" class="mb-0"></p>
                </div>
            </div>
            <div id="modal-payment-ref-container" class="mt-2 text-muted small d-none">
                <strong>Ref:</strong> <span id="modal-payment-reference"></span>
            </div>
            <div id="modal-payment-proof-container" class="mt-3 text-center d-none">
                <a href="#" id="modal-payment-proof-link" target="_blank" class="btn btn-sm btn-outline-success w-100"><i class="fas fa-file-invoice me-1"></i>View Payment Proof</a>
            </div>
        </div>

        <div class="bg-light p-3 rounded">
            <span class="text-muted small text-uppercase fw-bold"><i class="fas fa-sticky-note me-1"></i>Guest Remarks</span>
            <p id="modal-remark" class="mb-0 small"></p>
        </div>
      </div>
      <div class="modal-footer border-top-0 d-flex justify-content-between">
        <button type="button" class="btn btn-primary" id="btn-open-edit"><i class="fas fa-edit me-1"></i> Edit Booking</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Booking Modal -->
<div class="modal fade" id="modalEditBooking" tabindex="-1">
    <div class="modal-dialog">
        <form id="form-edit-booking" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Booking</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="edit-form-alert" class="alert d-none"></div>
                <input type="hidden" name="booking_id" id="edit-booking-id">
                
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Select Site</label>
                    <select name="site_id" id="edit-site-id" class="form-select" required>
                        <?php foreach ($sitesList as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['tapak_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Guest Name</label>
                    <input type="text" name="booking_name" id="edit-guest-name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Contact Number</label>
                    <input type="tel" name="booking_contact" id="edit-contact" class="form-control" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label text-muted small fw-bold">Check-In</label>
                        <input type="date" name="check_in" id="edit-checkin" class="form-control" required>
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small fw-bold">Check-Out</label>
                        <input type="date" name="check_out" id="edit-checkout" class="form-control" required>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label text-muted small fw-bold">Payment Amount (RM)</label>
                        <input type="number" step="0.01" name="payment_amount" id="edit-payment-amount" class="form-control" value="0.00">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small fw-bold">Payment Status</label>
                        <select name="payment_status" id="edit-payment-status" class="form-select">
                            <option value="unpaid">Unpaid</option>
                            <option value="partial">Partial</option>
                            <option value="paid">Paid</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-sm-6">
                        <label class="form-label text-muted small fw-bold">Ref No / Receipt No</label>
                        <input type="text" name="payment_reference" id="edit-payment-reference" class="form-control" placeholder="e.g. TRX123456">
                    </div>
                    <div class="col-sm-6">
                        <label class="form-label text-muted small fw-bold">Upload Proof</label>
                        <input type="file" name="payment_proof" id="edit-payment-proof" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small fw-bold">Remarks (Optional)</label>
                    <textarea name="booking_remark" id="edit-remark" class="form-control" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="btn-update-booking">Update Booking</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    
    // Handle click on booking badges in the calendar
    $('.booking-badge').click(function() {
        // Extract data attributes
        let id = $(this).data('id');
        let siteId = $(this).data('site-id');
        let tapak = $(this).data('tapak');
        let guest = $(this).data('guest');
        let contact = $(this).data('contact');
        let checkin = $(this).data('checkin');
        let checkout = $(this).data('checkout');
        let remark = $(this).data('remark');
        let paymentAmount = $(this).data('payment-amount');
        let paymentStatus = $(this).data('payment-status');
        let paymentReference = $(this).data('payment-reference');
        let paymentProof = $(this).data('payment-proof');

        // Populate the Detail modal fields
        $('#modal-tapak').text(tapak);
        $('#modal-guest').text(guest);
        $('#modal-contact').text(contact);
        $('#modal-checkin').text(checkin);
        $('#modal-checkout').text(checkout);
        $('#modal-remark').text(remark && remark !== '-' ? remark : 'No remarks provided.');
        $('#modal-payment-amount').text(parseFloat(paymentAmount).toFixed(2));
        
        let statusBadge = 'bg-danger';
        let boxClass = 'bg-light text-dark';
        if (paymentStatus === 'paid') {
            statusBadge = 'bg-success';
            boxClass = 'bg-success bg-opacity-10 border-success';
        } else if (paymentStatus === 'partial') {
            statusBadge = 'bg-warning text-dark';
        }
        
        if ((paymentReference || paymentProof) && paymentStatus !== 'paid') {
            boxClass = 'bg-warning bg-opacity-25 border-warning';
        }

        $('#modal-payment-box').removeClass('bg-light bg-success bg-warning bg-opacity-10 bg-opacity-25 border-success border-warning text-dark').addClass(boxClass);
        $('#modal-payment-status').html(`<span class="badge ${statusBadge}">${paymentStatus.toUpperCase()}</span>`);

        if (paymentReference) {
            $('#modal-payment-reference').text(paymentReference);
            $('#modal-payment-ref-container').removeClass('d-none');
        } else {
            $('#modal-payment-ref-container').addClass('d-none');
        }

        if (paymentProof) {
            $('#modal-payment-proof-link').attr('href', 'uploads/' + paymentProof);
            $('#modal-payment-proof-container').removeClass('d-none');
        } else {
            $('#modal-payment-proof-container').addClass('d-none');
        }

        // Pass data to Edit button
        let btnEdit = $('#btn-open-edit');
        btnEdit.data('id', id);
        btnEdit.data('site-id', siteId);
        btnEdit.data('guest', guest);
        btnEdit.data('contact', contact);
        btnEdit.data('checkin', checkin);
        btnEdit.data('checkout', checkout);
        btnEdit.data('remark', remark);
        btnEdit.data('payment-amount', paymentAmount);
        btnEdit.data('payment-status', paymentStatus);
        btnEdit.data('payment-reference', paymentReference);

        // Show Detail Modal
        var detailModal = new bootstrap.Modal(document.getElementById('bookingDetailModal'));
        detailModal.show();
    });

    // Handle open Edit form from Detail modal
    $('#btn-open-edit').click(function() {
        $('#bookingDetailModal').modal('hide');
        
        $('#edit-booking-id').val($(this).data('id'));
        $('#edit-site-id').val($(this).data('site-id'));
        $('#edit-guest-name').val($(this).data('guest'));
        $('#edit-contact').val($(this).data('contact'));
        $('#edit-checkin').val($(this).data('checkin'));
        $('#edit-checkout').val($(this).data('checkout'));
        
        let remark = $(this).data('remark');
        $('#edit-remark').val(remark === '-' ? '' : remark);
        
        $('#edit-payment-amount').val($(this).data('payment-amount'));
        $('#edit-payment-status').val($(this).data('payment-status'));
        $('#edit-payment-reference').val($(this).data('payment-reference') || '');
        $('#edit-payment-proof').val(''); // Reset file input

        setTimeout(() => {
            new bootstrap.Modal(document.getElementById('modalEditBooking')).show();
        }, 400);
    });

    // Submit Edit Form via AJAX
    $('#form-edit-booking').submit(function(e) {
        e.preventDefault();
        
        let submitBtn = $('#btn-update-booking');
        let alertBox = $('#edit-form-alert');
        let formData = new FormData(this);
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Saving...');
        
        $.ajax({
            url: 'update_booking.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                alertBox.removeClass('d-none alert-success alert-danger');
                if (response.success) {
                    alertBox.addClass('alert-success').html("<strong>Success!</strong> " + response.message);
                    setTimeout(() => {
                        location.reload(); // Reload calendar to reflect changes
                    }, 1000);
                } else {
                    alertBox.addClass('alert-danger').html("<strong>Error:</strong> " + response.message);
                }
            },
            error: function() {
                alertBox.removeClass('d-none alert-success').addClass('alert-danger').text('A network or server error occurred.');
            },
            complete: function() {
                submitBtn.prop('disabled', false).text('Update Booking');
            }
        });
    });

});
</script>
</body>
</html>

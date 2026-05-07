<?php
// booking_status.php
require 'auth.php';
require 'db_config.php';

$sites = [];
try {
    $stmt = $pdo->query("SELECT id, tapak_name FROM sites ORDER BY id ASC");
    $sites = $stmt->fetchAll();
} catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sia Campsite - Booking Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Inter', 'Segoe UI', sans-serif; }
        .btn-touch, .form-control, .form-select { min-height: 48px; border-radius: 8px; }
        .btn-touch { font-weight: 600; font-size: 1rem; }
        .card-booking { transition: transform 0.2s, box-shadow 0.2s; border-radius: 12px; border: none; }
        .card-booking:hover { transform: translateY(-2px); box-shadow: 0 8px 15px rgba(0,0,0,0.05); }
        .navbar-brand { font-weight: bold; color: #2c3e50 !important; }
        @media (max-width: 768px) {
            .mobile-stack { display: flex; flex-direction: column; gap: 0.5rem; }
            .mobile-stack > * { width: 100%; }
        }
    </style>
    <link rel="manifest" href="manifest.json">
    <meta name="theme-color" content="#198754">
    <link rel="apple-touch-icon" href="https://cdn-icons-png.flaticon.com/512/3206/3206014.png">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('sw.js');
            });
        }
    </script>
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
                <li class="nav-item"><a class="nav-link active" href="booking_status.php">Booking Status</a></li>
                <li class="nav-item"><a class="nav-link" href="view_status_cal.php">Calendar</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_sites.php">Manage Site</a></li>
                <li class="nav-item"><a class="nav-link" href="info.php">Info Setup</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_users.php">Users</a></li>
                <li class="nav-item ms-lg-3"><a class="nav-link text-danger fw-bold" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="card shadow-sm border-0 rounded-3 h-100">
        <div class="card-body p-4">
            
            <div class="d-flex flex-column flex-xl-row justify-content-between align-items-xl-center mb-4 gap-3">
                <h5 class="card-title mb-0 text-dark fw-bold"><i class="fas fa-list-alt me-2 text-primary"></i>Dashboard Overview</h5>
                
                <div class="d-flex mobile-stack gap-2">
                    <select id="filter-site" class="form-select form-select-sm" style="min-height:48px;">
                        <option value="all">All Sites</option>
                        <?php foreach ($sites as $site): ?>
                            <option value="<?= $site['id'] ?>"><?= htmlspecialchars($site['tapak_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <input type="date" id="filter-start" class="form-control form-control-sm" style="min-height:48px;" aria-label="Start Date" value="<?= date('Y-m-d') ?>">
                    <input type="date" id="filter-end" class="form-control form-control-sm" style="min-height:48px;" aria-label="End Date" value="<?= date('Y-m-d', strtotime('+100 days')) ?>">
                    <button id="btn-filter" class="btn btn-primary btn-touch px-4"><i class="fas fa-search"></i></button>
                </div>
            </div>

            <div id="bookings-container" class="row g-3">
                <div class="text-center text-muted py-5">
                    <div class="spinner-border text-success" role="status"></div>
                    <p class="mt-2">Loading...</p>
                </div>
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
                        <?php foreach ($sites as $site): ?>
                            <option value="<?= $site['id'] ?>"><?= htmlspecialchars($site['tapak_name']) ?></option>
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
    loadBookings();

    $('#btn-filter').click(function() {
        loadBookings();
    });

    // Delete Booking Logic
    $(document).on('click', '.btn-delete', function() {
        if (!confirm('Are you sure you want to cancel this booking?')) return;
        
        let bookingId = $(this).data('id');
        let btn = $(this);
        let originalHtml = btn.html();
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: 'delete_booking.php',
            type: 'POST',
            data: { booking_id: bookingId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    loadBookings(); 
                } else {
                    alert(response.message); 
                    btn.prop('disabled', false).html(originalHtml);
                }
            },
            error: function() {
                alert('Server error processing deletion.');
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });

    // Open Edit Modal and pre-fill data
    $(document).on('click', '.btn-edit', function() {
        $('#edit-booking-id').val($(this).data('id'));
        $('#edit-site-id').val($(this).data('site-id'));
        $('#edit-guest-name').val($(this).data('guest'));
        $('#edit-contact').val($(this).data('contact'));
        $('#edit-checkin').val($(this).data('checkin'));
        $('#edit-checkout').val($(this).data('checkout'));
        $('#edit-remark').val($(this).data('remark'));
        $('#edit-payment-amount').val($(this).data('payment-amount'));
        $('#edit-payment-status').val($(this).data('payment-status'));
        $('#edit-payment-reference').val($(this).data('payment-reference') || '');
        $('#edit-payment-proof').val(''); // Reset file input

        let editModal = new bootstrap.Modal(document.getElementById('modalEditBooking'));
        editModal.show();
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
                    loadBookings(); // Refresh the list instantly
                    setTimeout(() => {
                        $('#modalEditBooking').modal('hide');
                        alertBox.addClass('d-none');
                    }, 1200);
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

function loadBookings() {
    let siteId = $('#filter-site').val();
    let dateStart = $('#filter-start').val();
    let dateEnd = $('#filter-end').val();
    let container = $('#bookings-container');

    container.html('<div class="col-12 text-center text-muted py-5"><div class="spinner-border text-success"></div></div>');

    $.ajax({
        url: 'get_bookings.php',
        type: 'GET',
        data: { site_id: siteId, date_start: dateStart, date_end: dateEnd },
        dataType: 'json',
        success: function(response) {
            container.empty();

            if (response.success && response.data.length > 0) {
                $.each(response.data, function(index, booking) {
                    // Update JS template to include data attributes and an Edit button
                    let card = `
                        <div class="col-md-6 col-xl-4 col-xxl-3">
                            <div class="card card-booking h-100 shadow-sm border">
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="badge bg-primary px-3 py-2 fs-6 rounded-pill">${booking.tapak_name}</span>
                                        <span class="text-muted small">Ref: SIA-${booking.id}</span>
                                    </div>
                                    <h6 class="fw-bold mb-1"><i class="fas fa-user-circle text-secondary me-2"></i>${booking.booking_name}</h6>
                                    <p class="text-muted small mb-3"><i class="fas fa-phone-alt text-secondary me-2"></i>${booking.booking_contact}</p>
                                    
                                    ${(() => {
                                        let boxClass = 'bg-light';
                                        let textClass = 'text-dark';
                                        if (booking.payment_reference || booking.payment_proof) {
                                            if (booking.payment_status !== 'paid') {
                                                boxClass = 'bg-warning bg-opacity-25 border-warning';
                                            } else {
                                                boxClass = 'bg-success bg-opacity-10 border-success';
                                            }
                                        }
                                        return `
                                        <div class="${boxClass} p-2 rounded small mb-3 border">
                                            <div class="d-flex justify-content-between ${textClass} mb-1">
                                                <span><strong>In:</strong> ${booking.check_in}</span>
                                                <span><strong>Out:</strong> ${booking.check_out}</span>
                                            </div>
                                            <div class="d-flex justify-content-between ${textClass} border-top pt-1 mt-1 border-secondary border-opacity-25">
                                                <span><strong>Payment:</strong> RM ${parseFloat(booking.payment_amount || 0).toFixed(2)}</span>
                                                <span class="badge ${booking.payment_status === 'paid' ? 'bg-success' : (booking.payment_status === 'partial' ? 'bg-warning text-dark' : 'bg-danger')}">${booking.payment_status ? booking.payment_status.toUpperCase() : 'UNPAID'}</span>
                                            </div>
                                            ${booking.payment_reference ? `<div class="${textClass} small mt-1">Ref: ${booking.payment_reference}</div>` : ''}
                                        </div>
                                        `;
                                    })()}
                                    
                                    ${booking.booking_remark && booking.booking_remark !== '-' ? `
                                    <div class="bg-light p-2 rounded small mb-3 border">
                                        <div class="text-muted small text-uppercase fw-bold mb-1"><i class="fas fa-sticky-note me-1"></i>Remarks</div>
                                        <div class="text-dark">${booking.booking_remark}</div>
                                    </div>
                                    ` : ''}
                                    
                                    ${booking.payment_proof ? `
                                    <div class="mb-3 text-center">
                                        <a href="uploads/${booking.payment_proof}" target="_blank" class="btn btn-sm btn-outline-success w-100"><i class="fas fa-file-invoice me-1"></i>View Payment Proof</a>
                                    </div>
                                    ` : ''}

                                    <div class="mt-auto pt-2 border-top d-flex gap-2">
                                        <button class="btn btn-outline-primary btn-touch w-50 btn-edit" 
                                            data-id="${booking.id}"
                                            data-site-id="${booking.site_id}"
                                            data-guest="${booking.booking_name}"
                                            data-contact="${booking.booking_contact}"
                                            data-checkin="${booking.check_in}"
                                            data-checkout="${booking.check_out}"
                                            data-remark="${booking.booking_remark || ''}"
                                            data-payment-amount="${booking.payment_amount || '0.00'}"
                                            data-payment-status="${booking.payment_status || 'unpaid'}"
                                            data-payment-reference="${booking.payment_reference || ''}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-outline-danger btn-touch w-50 btn-delete" data-id="${booking.id}">
                                            <i class="fas fa-times-circle"></i> Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.append(card);
                });
            } else {
                container.html('<div class="col-12 text-center text-muted py-5"><i class="fas fa-inbox fa-3x mb-3 text-light"></i><br>No bookings found.</div>');
            }
        },
        error: function() {
            container.html('<div class="col-12 text-center text-danger py-4">Failed to load bookings from server.</div>');
        }
    });
}
</script>
</body>
</html>

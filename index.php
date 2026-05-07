<?php
// index.php
require 'auth.php';
require 'db_config.php';

$sites = [];
try {
    $stmt = $pdo->query("SELECT id, tapak_name FROM sites WHERE status = 'active' ORDER BY id ASC");
    $sites = $stmt->fetchAll();
} catch (PDOException $e) {}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sia Campsite - New Booking</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body { background-color: #f4f6f9; font-family: 'Inter', 'Segoe UI', sans-serif; }
        .btn-touch, .form-control, .form-select { min-height: 48px; border-radius: 8px; }
        .btn-touch { font-weight: 600; font-size: 1rem; }
        .navbar-brand { font-weight: bold; color: #2c3e50 !important; }
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

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-5">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php"><i class="fas fa-campground text-success me-2"></i>Sia Campsite</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="index.php">New Booking</a></li>
                <li class="nav-item"><a class="nav-link" href="booking_status.php">Booking Status</a></li>
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
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-success text-white rounded-top-3 py-3 text-center">
                    <h5 class="card-title mb-0"><i class="fas fa-plus-circle me-2"></i>Create New Booking</h5>
                </div>
                <div class="card-body p-4">
                    <div id="form-alert" class="alert d-none"></div>
                    
                    <form id="booking-form">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Select Site</label>
                            <select name="site_id" class="form-select" required>
                                <option value="" disabled selected>Choose a site...</option>
                                <?php foreach ($sites as $site): ?>
                                    <option value="<?= $site['id'] ?>"><?= htmlspecialchars($site['tapak_name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Guest Name</label>
                            <input type="text" name="booking_name" class="form-control" required placeholder="Full Name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Contact Number</label>
                            <input type="tel" name="booking_contact" class="form-control" required placeholder="+60123456789">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-sm-6">
                                <label class="form-label text-muted small fw-bold">Check-In</label>
                                <input type="date" name="check_in" class="form-control" required>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small fw-bold">Check-Out</label>
                                <input type="date" name="check_out" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Remarks (Optional)</label>
                            <textarea name="booking_remark" class="form-control" rows="2" placeholder="Any special requests?"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100 btn-touch shadow-sm" id="btn-submit">
                            <i class="fas fa-check me-2"></i>Save Booking
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    $('#booking-form').submit(function(e) {
        e.preventDefault();
        
        let submitBtn = $('#btn-submit');
        let alertBox = $('#form-alert');
        
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Processing...');
        
        $.ajax({
            url: 'process_booking.php',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            success: function(response) {
                alertBox.removeClass('d-none alert-success alert-danger');
                if (response.success) {
                    alertBox.addClass('alert-success').html("<strong>Success!</strong> " + response.message + "<br>Ref: " + response.booking_ref);
                    $('#booking-form')[0].reset();
                } else {
                    alertBox.addClass('alert-danger').html("<strong>Error:</strong> " + response.message);
                }
            },
            error: function() {
                alertBox.removeClass('d-none alert-success').addClass('alert-danger').text('A network or server error occurred.');
            },
            complete: function() {
                submitBtn.prop('disabled', false).html('<i class="fas fa-check me-2"></i>Save Booking');
                setTimeout(() => alertBox.addClass('d-none'), 6000);
            }
        });
    });
});
</script>
</body>
</html>

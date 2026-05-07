<?php require 'auth.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sia Campsite - Information Setup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Inter', 'Segoe UI', sans-serif; }
        .info-card { border-radius: 12px; border: none; transition: transform 0.2s; }
        .info-card:hover { transform: translateY(-3px); box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
        .icon-box { 
            width: 60px; height: 60px; border-radius: 12px; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 1.5rem; color: white; margin-right: 1.5rem;
        }
        .navbar-brand { font-weight: bold; color: #2c3e50 !important; }
        
        /* Clean Utility Classes for easy content edits */
        .content-title { font-weight: bold; color: #34495e; font-size: 1.25rem; margin-bottom: 0.5rem; }
        .content-text { color: #7f8c8d; font-size: 1rem; line-height: 1.5; margin: 0; }
        .content-label { font-weight: 600; color: #2c3e50; }
    </style>
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
                <li class="nav-item"><a class="nav-link" href="index.php">New Booking</a></li>
                <li class="nav-item"><a class="nav-link" href="booking_status.php">Booking Status</a></li>
                <li class="nav-item"><a class="nav-link" href="view_status_cal.php">Calendar</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_sites.php">Manage Site</a></li>
                <li class="nav-item"><a class="nav-link active" href="info.php">Info Setup</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_users.php">Users</a></li>
                <li class="nav-item ms-lg-3"><a class="nav-link text-danger fw-bold" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="text-center mb-5">
                <h2 class="fw-bold text-dark">Campsite Information</h2>
                <p class="text-muted">Easily manage and view the static contact and location information.</p>
            </div>
            
            <!-- Location Details -->
            <div class="card shadow-sm info-card mb-4">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-primary shadow-sm flex-shrink-0">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <div>
                        <h5 class="content-title">Location Details</h5>
                        <p class="content-text">
                            <span class="content-label">Address:</span><br>
                            Jalan Sungai Lembing, Kampung Kenangan<br>
                            26200 Kuantan, Pahang, Malaysia
                        </p>
                    </div>
                </div>
            </div>

            <!-- Ownership Details -->
            <div class="card shadow-sm info-card mb-4">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-success shadow-sm flex-shrink-0">
                        <i class="fas fa-id-badge"></i>
                    </div>
                    <div>
                        <h5 class="content-title">Ownership & Management</h5>
                        <p class="content-text">
                            <span class="content-label">Owner:</span> Mr. Sia<br>
                            <span class="content-label">Manager:</span> Siti Nurhaliza
                        </p>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card shadow-sm info-card">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="icon-box bg-info shadow-sm flex-shrink-0">
                        <i class="fas fa-headset"></i>
                    </div>
                    <div>
                        <h5 class="content-title">Contact Information</h5>
                        <p class="content-text">
                            <span class="content-label">Phone / WhatsApp:</span> +60 12-345 6789<br>
                            <span class="content-label">Email:</span> hello@siacampsite.com
                        </p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

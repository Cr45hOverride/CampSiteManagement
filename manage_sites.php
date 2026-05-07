<?php
// manage_sites.php
require 'auth.php';
require 'db_config.php';

try {
    $stmt = $pdo->query("SELECT * FROM sites ORDER BY id ASC");
    $sites = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sia Campsite - Manage Sites</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Inter', 'Segoe UI', sans-serif; }
        .navbar-brand { font-weight: bold; color: #2c3e50 !important; }
        .card { border-radius: 12px; border: none; }
        .btn-touch { min-height: 38px; font-weight: 500; }
        .table-responsive { border-radius: 8px; }
        .remark-cell { max-width: 250px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
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
                <li class="nav-item"><a class="nav-link" href="view_status_cal.php">Calendar</a></li>
                <li class="nav-item"><a class="nav-link active" href="manage_sites.php">Manage Site</a></li>
                <li class="nav-item"><a class="nav-link" href="info.php">Info Setup</a></li>
                <li class="nav-item"><a class="nav-link" href="manage_users.php">Users</a></li>
                <li class="nav-item ms-lg-3"><a class="nav-link text-danger fw-bold" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white py-3 rounded-top-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-cogs me-2"></i>Manage Sites</h5>
                    <div>
                        <button class="btn btn-success btn-sm fw-bold shadow-sm me-2" data-bs-toggle="modal" data-bs-target="#modalAddTapak"><i class="fas fa-plus me-1"></i> Add Site</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>Site Name</th>
                                    <th>Remark</th>
                                    <th>Status</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($sites)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-5">
                                            <i class="fas fa-exclamation-circle fa-2x mb-2 text-warning"></i><br>
                                            No site found.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($sites as $site): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-muted">#<?= $site['id'] ?></td>
                                        <td class="fw-bold"><?= htmlspecialchars($site['tapak_name']) ?></td>
                                        <td class="text-muted small remark-cell" title="<?= htmlspecialchars($site['remark'] ?? '') ?>">
                                            <?= htmlspecialchars($site['remark'] ?? '-') ?>
                                        </td>
                                        <td>
                                            <?php if ($site['status'] === 'active'): ?>
                                                <span class="badge bg-success px-2 py-1"><i class="fas fa-check me-1"></i>Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger px-2 py-1"><i class="fas fa-wrench me-1"></i>Maintenance</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-outline-primary btn-sm btn-touch btn-edit" 
                                                    data-id="<?= $site['id'] ?>" 
                                                    data-name="<?= htmlspecialchars($site['tapak_name']) ?>" 
                                                    data-remark="<?= htmlspecialchars($site['remark'] ?? '') ?>" 
                                                    data-status="<?= $site['status'] ?>">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm btn-touch btn-delete ms-1" data-id="<?= $site['id'] ?>">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Add Tapak -->
<div class="modal fade" id="modalAddTapak" tabindex="-1">
    <div class="modal-dialog">
        <form id="form-add" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Site</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="action" value="create">
                <div class="mb-3">
                    <label class="form-label fw-bold">Site Name</label>
                    <input type="text" name="tapak_name" class="form-control" required placeholder="e.g. Site 21">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" class="form-select">
                        <option value="active">Active</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Remark</label>
                    <textarea name="remark" class="form-control" rows="3" placeholder="Notes about this site..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Save Site</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Tapak -->
<div class="modal fade" id="modalEditTapak" tabindex="-1">
    <div class="modal-dialog">
        <form id="form-edit" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Site</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit-id">
                <div class="mb-3">
                    <label class="form-label fw-bold">Site Name</label>
                    <input type="text" name="tapak_name" id="edit-name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Status</label>
                    <select name="status" id="edit-status" class="form-select">
                        <option value="active">Active</option>
                        <option value="maintenance">Maintenance</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Remark</label>
                    <textarea name="remark" id="edit-remark" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update Site</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    
    // Create Tapak
    $('#form-add').submit(function(e) {
        e.preventDefault();
        let btn = $(this).find('button[type=submit]');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Saving...');
        
        $.post('api_sites.php', $(this).serialize(), function(res) {
            if (res.success) location.reload();
            else { alert('Error: ' + res.message); btn.prop('disabled', false).text('Save Site'); }
        }, 'json').fail(function() {
            alert('A server error occurred.'); btn.prop('disabled', false).text('Save Site');
        });
    });

    // Populate and Show Edit Modal
    $('.btn-edit').click(function() {
        $('#edit-id').val($(this).data('id'));
        $('#edit-name').val($(this).data('name'));
        $('#edit-remark').val($(this).data('remark'));
        $('#edit-status').val($(this).data('status'));
        
        let editModal = new bootstrap.Modal(document.getElementById('modalEditTapak'));
        editModal.show();
    });

    // Update Tapak
    $('#form-edit').submit(function(e) {
        e.preventDefault();
        let btn = $(this).find('button[type=submit]');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');
        
        $.post('api_sites.php', $(this).serialize(), function(res) {
            if (res.success) location.reload();
            else { alert('Error: ' + res.message); btn.prop('disabled', false).text('Update Site'); }
        }, 'json').fail(function() {
            alert('A server error occurred.'); btn.prop('disabled', false).text('Update Site');
        });
    });

    // Delete Tapak
    $('.btn-delete').click(function() {
        if (!confirm('Are you sure you want to delete this site? \nWARNING: All bookings associated with this site will also be permanently deleted!')) return;
        
        let btn = $(this);
        let originalHtml = btn.html();
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.post('api_sites.php', { action: 'delete', id: $(this).data('id') }, function(res) {
            if (res.success) location.reload();
            else { alert('Error: ' + res.message); btn.prop('disabled', false).html(originalHtml); }
        }, 'json').fail(function() {
            alert('A server error occurred.'); btn.prop('disabled', false).html(originalHtml);
        });
    });
});
</script>
</body>
</html>

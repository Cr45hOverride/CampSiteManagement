<?php
// manage_users.php
require 'auth.php';
if ($_SESSION['user_role'] !== 'admin') {
    die("Access denied. Admins only.");
}
require 'db_config.php';

try {
    $stmt = $pdo->query("SELECT id, username, role, created_at FROM users ORDER BY id ASC");
    $users = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sia Campsite - Manage Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Inter', 'Segoe UI', sans-serif; }
        .navbar-brand { font-weight: bold; color: #2c3e50 !important; }
        .card { border-radius: 12px; border: none; }
        .btn-touch { min-height: 38px; font-weight: 500; }
        .table-responsive { border-radius: 8px; }
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
                <li class="nav-item"><a class="nav-link" href="manage_sites.php">Manage Site</a></li>
                <li class="nav-item"><a class="nav-link active" href="manage_users.php">Users</a></li>
                <li class="nav-item"><a class="nav-link" href="info.php">Info Setup</a></li>
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
                    <h5 class="mb-0"><i class="fas fa-users me-2"></i>Manage Users</h5>
                    <button class="btn btn-success btn-sm fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAddUser"><i class="fas fa-user-plus me-1"></i> Add User</button>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">ID</th>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Created</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr><td colspan="5" class="text-center text-muted py-4">No users found.</td></tr>
                                <?php else: ?>
                                    <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td class="ps-4 fw-bold text-muted">#<?= $user['id'] ?></td>
                                        <td class="fw-bold"><?= htmlspecialchars($user['username']) ?></td>
                                        <td><span class="badge bg-secondary"><?= htmlspecialchars($user['role']) ?></span></td>
                                        <td class="text-muted small"><?= $user['created_at'] ?></td>
                                        <td class="text-end pe-4">
                                            <button class="btn btn-outline-warning btn-sm btn-touch btn-reset" data-id="<?= $user['id'] ?>" data-username="<?= htmlspecialchars($user['username']) ?>">
                                                <i class="fas fa-key"></i> Reset Password
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm btn-touch btn-delete ms-1" data-id="<?= $user['id'] ?>" <?= $user['id'] == $_SESSION['user_id'] ? 'disabled' : '' ?>>
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

<!-- Modal Add User -->
<div class="modal fade" id="modalAddUser" tabindex="-1">
    <div class="modal-dialog">
        <form id="form-add-user" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="action" value="create">
                <div class="mb-3">
                    <label class="form-label fw-bold">Username / Email</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Password</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Role</label>
                    <select name="role" class="form-select">
                        <option value="admin">Admin</option>
                        <option value="staff">Staff</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Save User</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reset Password -->
<div class="modal fade" id="modalResetPass" tabindex="-1">
    <div class="modal-dialog">
        <form id="form-reset-pass" class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="action" value="reset_password">
                <input type="hidden" name="id" id="reset-id">
                <p>Set a new password for <strong id="reset-username" class="text-primary"></strong>:</p>
                <div class="mb-3">
                    <label class="form-label fw-bold">New Password</label>
                    <input type="password" name="password" class="form-control" required minlength="6">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-warning">Update Password</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $('#form-add-user').submit(function(e) {
        e.preventDefault();
        let btn = $(this).find('button[type=submit]');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        $.post('api_users.php', $(this).serialize(), function(res) {
            if(res.success) location.reload(); else { alert(res.message); btn.prop('disabled', false).text('Save User'); }
        }, 'json').fail(function() { alert('Server error'); btn.prop('disabled', false).text('Save User'); });
    });

    $('.btn-reset').click(function() {
        $('#reset-id').val($(this).data('id'));
        $('#reset-username').text($(this).data('username'));
        new bootstrap.Modal(document.getElementById('modalResetPass')).show();
    });

    $('#form-reset-pass').submit(function(e) {
        e.preventDefault();
        let btn = $(this).find('button[type=submit]');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        $.post('api_users.php', $(this).serialize(), function(res) {
            if(res.success) { alert('Password updated successfully'); location.reload(); }
            else { alert(res.message); btn.prop('disabled', false).text('Update Password'); }
        }, 'json').fail(function() { alert('Server error'); btn.prop('disabled', false).text('Update Password'); });
    });

    $('.btn-delete').click(function() {
        if(!confirm('Delete this user?')) return;
        $.post('api_users.php', {action: 'delete', id: $(this).data('id')}, function(res) {
            if(res.success) location.reload(); else alert(res.message);
        }, 'json');
    });
});
</script>
</body>
</html>

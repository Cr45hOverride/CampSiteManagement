<?php
// login.php
session_start();
require 'db_config.php';

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT id, username, password_hash, role FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_role'] = $user['role'];
            header("Location: index.php");
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sia Campsite - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Inter', 'Segoe UI', sans-serif; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .card-login { border-radius: 12px; border: none; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); width: 100%; max-width: 900px; }
        .bg-login { background: linear-gradient(135deg, #198754 0%, #20c997 100%); padding: 40px; color: white; text-align: center; display: flex; flex-direction: column; justify-content: center; }
        .btn-login { min-height: 48px; font-weight: 600; border-radius: 8px; }
        .form-control { min-height: 48px; border-radius: 8px; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card card-login flex-row flex-wrap">
                <div class="col-md-5 bg-login d-none d-md-flex">
                    <i class="fas fa-campground fa-4x mb-3"></i>
                    <h3 class="fw-bold">Sia Campsite</h3>
                    <p class="mb-0 text-white-50">Booking Management System</p>
                </div>
                <div class="col-md-7 p-5">
                    <div class="text-center d-md-none mb-4">
                        <i class="fas fa-campground fa-3x text-success mb-2"></i>
                        <h4 class="fw-bold">Sia Campsite</h4>
                    </div>
                    <h4 class="fw-bold mb-4 text-dark">Welcome Back</h4>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger p-2 small"><i class="fas fa-exclamation-circle me-1"></i><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold">Username / Email</label>
                            <input type="text" name="username" class="form-control" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-success w-100 btn-login shadow-sm">
                            Login <i class="fas fa-sign-in-alt ms-2"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

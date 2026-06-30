<?php
require_once 'includes/config.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['admin_id'])) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $query = mysqli_query($conn, "SELECT * FROM admin WHERE username = '$username'");
    $admin = mysqli_fetch_assoc($query);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id']   = $admin['id'];
        $_SESSION['admin_nama'] = $admin['nama'];
        redirect('dashboard.php');
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Rekomendasi Jurusan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #1e293b 0%, #1a56db 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
            font-family: 'Segoe UI', sans-serif;
        }
        .login-card {
            background: #fff; border-radius: 20px; padding: 40px;
            width: 100%; max-width: 420px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        .login-logo {
            width: 64px; height: 64px; background: #1a56db;
            border-radius: 16px; display: flex; align-items: center;
            justify-content: center; margin: 0 auto 20px;
        }
        .form-control { padding: 12px 16px; border-radius: 10px; border: 1.5px solid #e2e8f0; }
        .form-control:focus { border-color: #1a56db; box-shadow: 0 0 0 3px rgba(26,86,219,0.1); }
        .btn-login {
            background: #1a56db; color: #fff; border: none;
            padding: 12px; border-radius: 10px; font-weight: 600;
            width: 100%; font-size: 1rem; transition: all 0.2s;
        }
        .btn-login:hover { background: #1649c0; transform: translateY(-1px); }
        .info-box {
            background: #f0f7ff; border: 1px solid #bfdbfe;
            border-radius: 10px; padding: 12px 16px; font-size: 0.85rem; color: #1e40af;
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="login-logo">
        <i class="bi bi-mortarboard-fill text-white fs-3"></i>
    </div>
    <h4 class="text-center fw-bold mb-1">Selamat Datang</h4>
    <p class="text-center text-muted small mb-4">Sistem Rekomendasi Pemilihan Jurusan<br>Metode Profile Matching</p>

    <?php if ($error): ?>
    <div class="alert alert-danger small py-2"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label fw-semibold small">Username</label>
            <div class="input-group">
                <span class="input-group-text" style="border-radius:10px 0 0 10px; border:1.5px solid #e2e8f0;">
                    <i class="bi bi-person text-muted"></i>
                </span>
                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required
                    style="border-radius:0 10px 10px 0;">
            </div>
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold small">Password</label>
            <div class="input-group">
                <span class="input-group-text" style="border-radius:10px 0 0 10px; border:1.5px solid #e2e8f0;">
                    <i class="bi bi-lock text-muted"></i>
                </span>
                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required
                    style="border-radius:0 10px 10px 0;">
            </div>
        </div>
        <button type="submit" class="btn-login">
            <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
        </button>
    </form>

    <div class="info-box mt-4">
        <i class="bi bi-info-circle me-1"></i>
        <strong>Default Login:</strong><br>
        Username: <code>admin</code> | Password: <code>admin123</code>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

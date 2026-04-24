<?php
session_start();
require 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Validasi input
    if (empty($email) || empty($password)) {
        $error = 'Email dan password wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } else {
        $email_safe = mysqli_real_escape_string($conn, $email);
        $result = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email_safe' LIMIT 1");
        $user   = mysqli_fetch_assoc($result);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['email']   = $user['email'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = 'Email atau password salah.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SmartFlood Sensor</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="auth-wrapper">
    <div class="auth-box">
        <div class="auth-logo">
            <span class="logo-icon">🌊</span>
            <h2>SmartFlood Sensor</h2>
            <p>Sistem Monitoring Banjir Smart City</p>
        </div>
        <h3>Masuk ke Akun</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if (!empty($_GET['registered'])): ?>
            <div class="alert alert-success">✅ Registrasi berhasil! Silakan login.</div>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">📧 Email</label>
                <input type="email" id="email" name="email" class="form-control"
                       placeholder="Masukkan email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="password">🔒 Password</label>
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="Masukkan password (min. 6 karakter)" required minlength="6">
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;margin-top:4px;">
                🚀 Login
            </button>
        </form>

        <div class="auth-footer">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </div>
    </div>
</div>
</body>
</html>
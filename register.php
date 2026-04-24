<?php
session_start();
require 'koneksi.php';

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $konfirmasi = $_POST['konfirmasi'] ?? '';

    // Validasi
    if (empty($nama) || empty($email) || empty($password) || empty($konfirmasi)) {
        $error = 'Semua field wajib diisi.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
    } elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
    } elseif ($password !== $konfirmasi) {
        $error = 'Konfirmasi password tidak cocok.';
    } else {
        $email_safe = mysqli_real_escape_string($conn, $email);
        $cek = mysqli_query($conn, "SELECT id FROM users WHERE email = '$email_safe' LIMIT 1");

        if (mysqli_num_rows($cek) > 0) {
            $error = 'Email sudah terdaftar.';
        } else {
            $nama_safe = mysqli_real_escape_string($conn, $nama);
            $hash      = password_hash($password, PASSWORD_DEFAULT);
            $query = "INSERT INTO users (nama, email, password) VALUES ('$nama_safe', '$email_safe', '$hash')";

            if (mysqli_query($conn, $query)) {
                header("Location: login.php?registered=1");
                exit;
            } else {
                $error = 'Registrasi gagal. Silakan coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - SmartFlood Sensor</title>
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
        <h3>Buat Akun Baru</h3>

        <?php if ($error): ?>
            <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="nama">👤 Nama Lengkap</label>
                <input type="text" id="nama" name="nama" class="form-control"
                       placeholder="Masukkan nama lengkap"
                       value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="email">📧 Email</label>
                <input type="email" id="email" name="email" class="form-control"
                       placeholder="Masukkan email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="password">🔒 Password</label>
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="Minimal 6 karakter" required minlength="6">
            </div>
            <div class="form-group">
                <label for="konfirmasi">🔒 Konfirmasi Password</label>
                <input type="password" id="konfirmasi" name="konfirmasi" class="form-control"
                       placeholder="Ulangi password" required minlength="6">
            </div>
            <button type="submit" class="btn btn-success" style="width:100%;justify-content:center;margin-top:4px;">
                ✅ Daftar Sekarang
            </button>
        </form>

        <div class="auth-footer">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </div>
    </div>
</div>
</body>
</html>
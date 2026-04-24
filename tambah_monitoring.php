<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'koneksi.php';

$user_id = $_SESSION['user_id'];
$error   = '';

// Helper: tentukan status otomatis berdasarkan tinggi air
function tentukanStatus($tinggi_air) {
    if ($tinggi_air <= 50) {
        return 'aman';
    } elseif ($tinggi_air <= 100) {
        return 'waspada';
    } else {
        return 'bahaya';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lokasi         = trim($_POST['lokasi_sungai'] ?? '');
    $waktu          = trim($_POST['waktu_pengukuran'] ?? '');
    $tinggi_air     = trim($_POST['tinggi_air'] ?? '');
    $deskripsi      = trim($_POST['deskripsi'] ?? '');
    $foto_bukti_nama = '';

    // Validasi
    if (empty($lokasi) || empty($waktu) || $tinggi_air === '' || empty($deskripsi)) {
        $error = 'Semua field wajib diisi.';
    } elseif (!is_numeric($tinggi_air) || $tinggi_air < 0) {
        $error = 'Tinggi air harus berupa angka positif.';
    } else {
        // Upload file
        if (!empty($_FILES['foto_bukti']['name'])) {
            $file     = $_FILES['foto_bukti'];
            $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed  = ['jpg', 'jpeg', 'png'];

            if (!in_array($ext, $allowed)) {
                $error = 'Format file tidak valid. Gunakan JPG, JPEG, atau PNG.';
            } elseif ($file['size'] > 5 * 1024 * 1024) {
                $error = 'Ukuran file maksimal 5 MB.';
            } else {
                $foto_bukti_nama = uniqid('foto_') . '.' . $ext;
                if (!move_uploaded_file($file['tmp_name'], 'uploads/' . $foto_bukti_nama)) {
                    $error = 'Gagal mengupload file.';
                    $foto_bukti_nama = '';
                }
            }
        }

        if (empty($error)) {
            $status         = tentukanStatus((float)$tinggi_air);
            $lokasi_safe    = mysqli_real_escape_string($conn, $lokasi);
            $waktu_safe     = mysqli_real_escape_string($conn, $waktu);
            $tinggi_safe    = (float)$tinggi_air;
            $deskripsi_safe = mysqli_real_escape_string($conn, $deskripsi);
            $foto_safe      = mysqli_real_escape_string($conn, $foto_bukti_nama);

            $query = "INSERT INTO monitoring 
                      (user_id, lokasi_sungai, waktu_pengukuran, tinggi_air, status_banjir, deskripsi, foto_bukti)
                      VALUES ($user_id, '$lokasi_safe', '$waktu_safe', $tinggi_safe, '$status', '$deskripsi_safe', '$foto_safe')";

            if (mysqli_query($conn, $query)) {
                header("Location: monitoring.php?success=tambah");
                exit;
            } else {
                $error = 'Gagal menyimpan data: ' . mysqli_error($conn);
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
    <title>Tambah Monitoring - SmartFlood Sensor</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php require 'navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h2>➕ Tambah Data Monitoring</h2>
        <a href="monitoring.php" class="btn btn-secondary">← Kembali</a>
    </div>

    <div class="card" style="max-width:680px;">
        <div class="card-title">📝 Form Input Monitoring</div>

        <?php if ($error): ?>
            <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="tambah_monitoring.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="lokasi_sungai">📍 Lokasi Sungai</label>
                <input type="text" id="lokasi_sungai" name="lokasi_sungai" class="form-control"
                       placeholder="Contoh: Sungai Citarum, Sungai Cisangkuy"
                       value="<?= htmlspecialchars($_POST['lokasi_sungai'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="waktu_pengukuran">🕐 Waktu Pengukuran</label>
                <input type="datetime-local" id="waktu_pengukuran" name="waktu_pengukuran" class="form-control"
                       value="<?= htmlspecialchars($_POST['waktu_pengukuran'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label for="tinggi_air">💧 Tinggi Air (cm)</label>
                <input type="number" id="tinggi_air" name="tinggi_air" class="form-control"
                       placeholder="Masukkan tinggi air dalam cm" step="0.01" min="0"
                       value="<?= htmlspecialchars($_POST['tinggi_air'] ?? '') ?>" required>
                <small class="text-muted">0–50 cm: Aman | 51–100 cm: Waspada | &gt;100 cm: Bahaya (status ditentukan otomatis)</small>
            </div>

            <div class="form-group">
                <label for="deskripsi">📝 Deskripsi Kondisi</label>
                <textarea id="deskripsi" name="deskripsi" class="form-control"
                          placeholder="Deskripsikan kondisi lapangan saat ini..."
                          rows="4" required><?= htmlspecialchars($_POST['deskripsi'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="foto_bukti">📷 Foto Bukti (Opsional)</label>
                <input type="file" id="foto_bukti" name="foto_bukti" class="form-control"
                       accept=".jpg,.jpeg,.png">
                <small class="text-muted">Format: JPG, JPEG, PNG. Maks: 5 MB.</small>
            </div>

            <div style="display:flex;gap:10px;margin-top:6px;">
                <button type="submit" class="btn btn-success">💾 Simpan Data</button>
                <a href="monitoring.php" class="btn btn-secondary">❌ Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require 'footer.php'; ?>
</body>
</html>
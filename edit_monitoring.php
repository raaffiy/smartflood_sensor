<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'koneksi.php';

$user_id = $_SESSION['user_id'];
$error   = '';

// Ambil ID dari GET
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: monitoring.php");
    exit;
}

// Ambil data existing — pastikan milik user ini
$result = mysqli_query($conn, "SELECT * FROM monitoring WHERE id = $id AND user_id = $user_id LIMIT 1");
if (mysqli_num_rows($result) === 0) {
    header("Location: monitoring.php");
    exit;
}
$data = mysqli_fetch_assoc($result);

// Helper status
function tentukanStatus($tinggi_air) {
    if ($tinggi_air <= 50) return 'aman';
    elseif ($tinggi_air <= 100) return 'waspada';
    else return 'bahaya';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lokasi         = trim($_POST['lokasi_sungai'] ?? '');
    $waktu          = trim($_POST['waktu_pengukuran'] ?? '');
    $tinggi_air     = trim($_POST['tinggi_air'] ?? '');
    $deskripsi      = trim($_POST['deskripsi'] ?? '');
    $foto_lama      = $data['foto_bukti'];
    $foto_baru      = $foto_lama;

    // Validasi
    if (empty($lokasi) || empty($waktu) || $tinggi_air === '' || empty($deskripsi)) {
        $error = 'Semua field wajib diisi.';
    } elseif (!is_numeric($tinggi_air) || $tinggi_air < 0) {
        $error = 'Tinggi air harus berupa angka positif.';
    } else {
        // Cek upload baru
        if (!empty($_FILES['foto_bukti']['name'])) {
            $file    = $_FILES['foto_bukti'];
            $ext     = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png'];

            if (!in_array($ext, $allowed)) {
                $error = 'Format file tidak valid. Gunakan JPG, JPEG, atau PNG.';
            } elseif ($file['size'] > 5 * 1024 * 1024) {
                $error = 'Ukuran file maksimal 5 MB.';
            } else {
                $nama_baru = uniqid('foto_') . '.' . $ext;
                if (move_uploaded_file($file['tmp_name'], 'uploads/' . $nama_baru)) {
                    // Hapus foto lama
                    if ($foto_lama && file_exists('uploads/' . $foto_lama)) {
                        unlink('uploads/' . $foto_lama);
                    }
                    $foto_baru = $nama_baru;
                } else {
                    $error = 'Gagal mengupload file baru.';
                }
            }
        }

        if (empty($error)) {
            $status         = tentukanStatus((float)$tinggi_air);
            $lokasi_safe    = mysqli_real_escape_string($conn, $lokasi);
            $waktu_safe     = mysqli_real_escape_string($conn, $waktu);
            $tinggi_safe    = (float)$tinggi_air;
            $deskripsi_safe = mysqli_real_escape_string($conn, $deskripsi);
            $foto_safe      = mysqli_real_escape_string($conn, $foto_baru);

            $query = "UPDATE monitoring SET
                        lokasi_sungai = '$lokasi_safe',
                        waktu_pengukuran = '$waktu_safe',
                        tinggi_air = $tinggi_safe,
                        status_banjir = '$status',
                        deskripsi = '$deskripsi_safe',
                        foto_bukti = '$foto_safe'
                      WHERE id = $id AND user_id = $user_id";

            if (mysqli_query($conn, $query)) {
                header("Location: monitoring.php?success=edit");
                exit;
            } else {
                $error = 'Gagal memperbarui data: ' . mysqli_error($conn);
            }
        }
    }

    // Refresh data setelah POST gagal
    $data['lokasi_sungai']     = $_POST['lokasi_sungai'] ?? $data['lokasi_sungai'];
    $data['waktu_pengukuran']  = $_POST['waktu_pengukuran'] ?? $data['waktu_pengukuran'];
    $data['tinggi_air']        = $_POST['tinggi_air'] ?? $data['tinggi_air'];
    $data['deskripsi']         = $_POST['deskripsi'] ?? $data['deskripsi'];
}

// Format waktu untuk input datetime-local
$waktu_input = date('Y-m-d\TH:i', strtotime($data['waktu_pengukuran']));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Monitoring - SmartFlood Sensor</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php require 'navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h2>✏️ Edit Data Monitoring</h2>
        <a href="monitoring.php" class="btn btn-secondary">← Kembali</a>
    </div>

    <div class="card" style="max-width:680px;">
        <div class="card-title">📝 Form Edit Monitoring</div>

        <?php if ($error): ?>
            <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="edit_monitoring.php?id=<?= $id ?>" enctype="multipart/form-data">
            <div class="form-group">
                <label for="lokasi_sungai">📍 Lokasi Sungai</label>
                <input type="text" id="lokasi_sungai" name="lokasi_sungai" class="form-control"
                       placeholder="Contoh: Sungai Citarum"
                       value="<?= htmlspecialchars($data['lokasi_sungai']) ?>" required>
            </div>

            <div class="form-group">
                <label for="waktu_pengukuran">🕐 Waktu Pengukuran</label>
                <input type="datetime-local" id="waktu_pengukuran" name="waktu_pengukuran" class="form-control"
                       value="<?= $waktu_input ?>" required>
            </div>

            <div class="form-group">
                <label for="tinggi_air">💧 Tinggi Air (cm)</label>
                <input type="number" id="tinggi_air" name="tinggi_air" class="form-control"
                       step="0.01" min="0"
                       value="<?= htmlspecialchars($data['tinggi_air']) ?>" required>
                <small class="text-muted">0–50 cm: Aman | 51–100 cm: Waspada | &gt;100 cm: Bahaya (otomatis)</small>
            </div>

            <div class="form-group">
                <label for="deskripsi">📝 Deskripsi Kondisi</label>
                <textarea id="deskripsi" name="deskripsi" class="form-control" rows="4" required><?= htmlspecialchars($data['deskripsi']) ?></textarea>
            </div>

            <div class="form-group">
                <label for="foto_bukti">📷 Ganti Foto Bukti (Opsional)</label>
                <?php if ($data['foto_bukti'] && file_exists('uploads/' . $data['foto_bukti'])): ?>
                    <div class="current-foto">
                        <p>Foto saat ini:</p>
                        <img height="200px" src="uploads/<?= htmlspecialchars($data['foto_bukti']) ?>" alt="Foto saat ini">
                    </div>
                <?php endif; ?>
                <input type="file" id="foto_bukti" name="foto_bukti" class="form-control"
                       accept=".jpg,.jpeg,.png" style="margin-top:8px;">
                <small class="text-muted">Kosongkan jika tidak ingin mengganti foto. Format: JPG, JPEG, PNG. Maks: 5 MB.</small>
            </div>

            <div style="display:flex;gap:10px;margin-top:6px;">
                <button type="submit" class="btn btn-warning">💾 Perbarui Data</button>
                <a href="monitoring.php" class="btn btn-secondary">❌ Batal</a>
            </div>
        </form>
    </div>
</div>

<?php require 'footer.php'; ?>
</body>
</html>
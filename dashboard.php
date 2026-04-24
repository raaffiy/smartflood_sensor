<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'koneksi.php';

$user_id = $_SESSION['user_id'];

// Statistik monitoring milik user ini
$total   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM monitoring WHERE user_id = $user_id"))['n'];
$aman    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM monitoring WHERE user_id = $user_id AND status_banjir = 'aman'"))['n'];
$waspada = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM monitoring WHERE user_id = $user_id AND status_banjir = 'waspada'"))['n'];
$bahaya  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS n FROM monitoring WHERE user_id = $user_id AND status_banjir = 'bahaya'"))['n'];

// 5 data terbaru
$terbaru = mysqli_query($conn, "SELECT * FROM monitoring WHERE user_id = $user_id ORDER BY waktu_pengukuran DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SmartFlood Sensor</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php require 'navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h2>🏠 Dashboard Monitoring</h2>
        <span class="text-muted">Selamat datang, <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong>!</span>
    </div>

    <!-- Statistik -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="stat-header">
                <div class="stat-label">Total Monitoring</div>
                <div class="stat-icon">📋</div>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $total ?></div>
            </div>
        </div>
        <div class="stat-card aman">
            <div class="stat-header">
                <div class="stat-label">Status Aman</div>
                <div class="stat-icon">✅</div>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $aman ?></div>
            </div>
        </div>
        <div class="stat-card waspada">
            <div class="stat-header">
                <div class="stat-label">Status Waspada</div>
                <div class="stat-icon">⚠️</div>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $waspada ?></div>
            </div>
        </div>
        <div class="stat-card bahaya">
            <div class="stat-header">
                <div class="stat-label">Status Bahaya</div>
                <div class="stat-icon">🚨</div>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?= $bahaya ?></div>
            </div>
        </div>
    </div>

    <!-- Data Terbaru -->
    <div class="card">
        <div class="card-title">🕐 Data Monitoring Terbaru</div>

        <?php if (mysqli_num_rows($terbaru) === 0): ?>
            <div class="alert alert-warning">
                ℹ️ Belum ada data monitoring. <a href="tambah_monitoring.php">Tambah data pertama</a>.
            </div>
        <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Lokasi Sungai</th>
                        <th>Waktu Pengukuran</th>
                        <th>Tinggi Air</th>
                        <th>Status</th>
                        <th>Deskripsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($terbaru)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>📍 <?= htmlspecialchars($row['lokasi_sungai']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['waktu_pengukuran'])) ?></td>
                        <td><strong><?= $row['tinggi_air'] ?> cm</strong></td>
                        <td><span class="badge badge-<?= $row['status_banjir'] ?>"><?= strtoupper($row['status_banjir']) ?></span></td>
                        <td><?= htmlspecialchars(mb_substr($row['deskripsi'], 0, 60)) ?><?= strlen($row['deskripsi']) > 60 ? '...' : '' ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <div style="margin-top:14px;">
            <a href="monitoring.php" class="btn btn-primary btn-sm">📊 Lihat Semua Data</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require 'footer.php'; ?>
</body>
</html>
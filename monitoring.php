<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
require 'koneksi.php';

$user_id = $_SESSION['user_id'];
$success = $_GET['success'] ?? '';

$data = mysqli_query($conn, "SELECT * FROM monitoring WHERE user_id = $user_id ORDER BY waktu_pengukuran DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Monitoring - SmartFlood Sensor</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

<?php require 'navbar.php'; ?>

<div class="container">
    <div class="page-header">
        <h2>📊 Data Monitoring Ketinggian Air</h2>
        <a href="tambah_monitoring.php" class="btn btn-success">➕ Tambah Data</a>
    </div>

    <?php if ($success === 'tambah'): ?>
        <div class="alert alert-success">✅ Data monitoring berhasil ditambahkan.</div>
    <?php elseif ($success === 'edit'): ?>
        <div class="alert alert-success">✅ Data monitoring berhasil diperbarui.</div>
    <?php elseif ($success === 'hapus'): ?>
        <div class="alert alert-success">✅ Data monitoring berhasil dihapus.</div>
    <?php endif; ?>

    <div class="card">
        <div class="card-title">📋 Daftar Monitoring</div>

        <?php if (mysqli_num_rows($data) === 0): ?>
            <div class="alert alert-warning">ℹ️ Belum ada data monitoring. <a href="tambah_monitoring.php">Tambah data sekarang</a>.</div>
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
                        <th>Foto Bukti</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; while ($row = mysqli_fetch_assoc($data)): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>📍 <?= htmlspecialchars($row['lokasi_sungai']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($row['waktu_pengukuran'])) ?></td>
                        <td><strong><?= $row['tinggi_air'] ?> cm</strong></td>
                        <td><span class="badge badge-<?= $row['status_banjir'] ?>"><?= strtoupper($row['status_banjir']) ?></span></td>
                        <td style="max-width:180px;"><?= htmlspecialchars(mb_substr($row['deskripsi'], 0, 70)) ?><?= strlen($row['deskripsi']) > 70 ? '...' : '' ?></td>
                        <td>
                            <?php if ($row['foto_bukti'] && file_exists('uploads/' . $row['foto_bukti'])): ?>
                                <a href="uploads/<?= htmlspecialchars($row['foto_bukti']) ?>" target="_blank">
                                    <img src="uploads/<?= htmlspecialchars($row['foto_bukti']) ?>" class="foto-thumb" alt="Foto">
                                </a>
                            <?php else: ?>
                                <span class="no-foto">— Tidak ada —</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="edit_monitoring.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <br> <br>
                            <a href="hapus_monitoring.php?id=<?= $row['id'] ?>"
                               class="btn btn-danger btn-sm"
                               onclick="return confirm('Yakin hapus data ini? File foto juga akan terhapus.')">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require 'footer.php'; ?>
</body>
</html>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar">
    <div class="navbar-container">
        <a href="dashboard.php" class="brand">
            <span class="icon">🌊</span> SmartFlood
        </a>
        
        <nav>
            <a href="dashboard.php" class="<?= $current === 'dashboard.php' ? 'active' : '' ?>">
                🏠 Dashboard
            </a>
            <a href="monitoring.php" class="<?= $current === 'monitoring.php' ? 'active' : '' ?>">
                📊 Monitoring
            </a>
            <a href="tambah_monitoring.php" class="<?= $current === 'tambah_monitoring.php' ? 'active' : '' ?>">
                ➕ Tambah
            </a>
        </nav>
        
        <div style="display:flex; align-items:center; gap:20px;">
            <div class="user-info" style="display:flex; align-items:center; gap:8px;">
                <span style="font-size:1.2rem;">👤</span>
                <span><?= htmlspecialchars($_SESSION['nama'] ?? 'Guest') ?></span>
            </div>
            <a href="logout.php" class="btn btn-danger btn-sm" style="padding: 6px 14px;">
                🚪 Logout
            </a>
        </div>
    </div>
</nav>
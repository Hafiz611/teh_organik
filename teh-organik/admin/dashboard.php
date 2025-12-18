<?php
require_once '../config/koneksi.php';

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php?error=Anda tidak memiliki akses ke halaman ini!");
    exit();
}

// Ambil data statistik
$total_produk = $conn->query("SELECT COUNT(*) as total FROM produk")->fetch_assoc()['total'];
$total_pesanan = $conn->query("SELECT COUNT(*) as total FROM pesanan")->fetch_assoc()['total'];
$total_pelanggan = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'pelanggan'")->fetch_assoc()['total'];
$total_pendapatan = $conn->query("SELECT SUM(total_harga) as total FROM pesanan WHERE status_pesanan = 'selesai'")->fetch_assoc()['total'];

// Pesanan terbaru
$pesanan_terbaru = $conn->query("SELECT p.*, u.nama_lengkap, u.email 
                                FROM pesanan p 
                                JOIN users u ON p.id_user = u.id 
                                ORDER BY p.created_at DESC 
                                LIMIT 5");

// Produk terlaris
$produk_terlaris = $conn->query("SELECT pr.nama_produk, SUM(dp.jumlah) as total_terjual 
                                 FROM detail_pesanan dp 
                                 JOIN produk pr ON dp.id_produk = pr.id 
                                 GROUP BY dp.id_produk 
                                 ORDER BY total_terjual DESC 
                                 LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Teh Organik</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-green: #2d5016;
            --light-green: #4a7c28;
            --accent-gold: #d4af37;
            --cream: #f8f5f0;
            --white: #ffffff;
        }

        body {
            background: var(--cream);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            min-height: 100vh;
            color: var(--white);
            position: fixed;
            width: 250px;
            z-index: 1000;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .sidebar-header h3 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .sidebar-menu {
            padding: 20px 0;
        }

        .sidebar-menu .nav-link {
            color: var(--white);
            padding: 12px 20px;
            border-radius: 0;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
        }

        .sidebar-menu .nav-link:hover, .sidebar-menu .nav-link.active {
            background: rgba(255,255,255,0.1);
            color: var(--accent-gold);
        }

        .sidebar-menu .nav-link i {
            width: 20px;
            margin-right: 10px;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .top-header {
            background: var(--white);
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-card {
            background: var(--white);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border-left: 4px solid var(--light-green);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .stat-card.products .stat-icon {
            background: rgba(74, 124, 40, 0.1);
            color: var(--light-green);
        }

        .stat-card.orders .stat-icon {
            background: rgba(212, 175, 55, 0.1);
            color: var(--accent-gold);
        }

        .stat-card.customers .stat-icon {
            background: rgba(45, 80, 22, 0.1);
            color: var(--primary-green);
        }

        .stat-card.revenue .stat-icon {
            background: rgba(74, 124, 40, 0.1);
            color: var(--light-green);
        }

        .stat-card h3 {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-green);
            margin: 0;
        }

        .stat-card p {
            color: #6c757d;
            margin: 5px 0 0 0;
            font-weight: 500;
        }

        .table-container {
            background: var(--white);
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .table-container h4 {
            color: var(--primary-green);
            margin-bottom: 20px;
            font-weight: 600;
        }

        .table {
            border-radius: 10px;
            overflow: hidden;
        }

        .table thead th {
            background: var(--cream);
            border: none;
            color: var(--primary-green);
            font-weight: 600;
        }

        .table tbody td {
            vertical-align: middle;
            border-color: #f0f0f0;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 500;
        }

        .badge-pending { background: #fff3cd; color: #856404; }
        .badge-diproses { background: #cce5ff; color: #004085; }
        .badge-dikirim { background: #d1ecf1; color: #0c5460; }
        .badge-selesai { background: #d4edda; color: #155724; }
        .badge-dibatalkan { background: #f8d7da; color: #721c24; }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: var(--light-green);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-header">
            <i class="fas fa-leaf fa-2x mb-2" style="color: var(--accent-gold);"></i>
            <h3>Teh Organik</h3>
            <small>Panel Admin</small>
        </div>
        <div class="sidebar-menu">
            <a href="dashboard.php" class="nav-link active">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="kelola_produk.php" class="nav-link">
                <i class="fas fa-box"></i> Kelola Produk
            </a>
            <a href="kelola_pesanan.php" class="nav-link">
                <i class="fas fa-shopping-cart"></i> Kelola Pesanan
            </a>
            <a href="laporan.php" class="nav-link">
                <i class="fas fa-chart-bar"></i> Laporan
            </a>
            <a href="../logout.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Top Header -->
        <div class="top-header">
            <div>
                <h4 class="mb-0">Selamat datang, <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>!</h4>
                <small class="text-muted">Dashboard Admin Sistem Teh Organik</small>
            </div>
            <div class="user-info">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)); ?>
                </div>
                <div>
                    <strong><?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?></strong><br>
                    <small class="text-muted">Administrator</small>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="stat-card products">
                    <div class="stat-icon">
                        <i class="fas fa-box"></i>
                    </div>
                    <h3><?php echo number_format($total_produk); ?></h3>
                    <p>Total Produk</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card orders">
                    <div class="stat-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3><?php echo number_format($total_pesanan); ?></h3>
                    <p>Total Pesanan</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card customers">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3><?php echo number_format($total_pelanggan); ?></h3>
                    <p>Total Pelanggan</p>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="stat-card revenue">
                    <div class="stat-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                    <h3>Rp <?php echo number_format($total_pendapatan, 0, ',', '.'); ?></h3>
                    <p>Total Pendapatan</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Pesanan Terbaru -->
            <div class="col-md-7 mb-4">
                <div class="table-container">
                    <h4><i class="fas fa-shopping-cart me-2"></i>Pesanan Terbaru</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>No. Pesanan</th>
                                    <th>Pelanggan</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($pesanan = $pesanan_terbaru->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($pesanan['nomor_pesanan']); ?></strong></td>
                                    <td>
                                        <small><?php echo htmlspecialchars($pesanan['nama_lengkap']); ?></small><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($pesanan['email']); ?></small>
                                    </td>
                                    <td>Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $pesanan['status_pesanan']; ?>">
                                            <?php echo ucfirst($pesanan['status_pesanan']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Produk Terlaris -->
            <div class="col-md-5 mb-4">
                <div class="table-container">
                    <h4><i class="fas fa-fire me-2"></i>Produk Terlaris</h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Terjual</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($produk = $produk_terlaris->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($produk['nama_produk']); ?></td>
                                    <td><strong><?php echo number_format($produk['total_terjual']); ?></strong></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
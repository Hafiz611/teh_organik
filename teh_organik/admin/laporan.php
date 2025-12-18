<?php
require_once '../config/koneksi.php';

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php?error=Anda tidak memiliki akses ke halaman ini!");
    exit();
}

// Filter untuk laporan
$tanggal_awal = isset($_GET['tanggal_awal']) ? $_GET['tanggal_awal'] : date('Y-m-01');
$tanggal_akhir = isset($_GET['tanggal_akhir']) ? $_GET['tanggal_akhir'] : date('Y-m-t');
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Query untuk data laporan
$where_clause = "WHERE DATE(p.created_at) BETWEEN '$tanggal_awal' AND '$tanggal_akhir'";
if ($status_filter) {
    $where_clause .= " AND p.status_pesanan = '$status_filter'";
}

// Total pendapatan
$pendapatan_query = "SELECT SUM(total_harga) as total_pendapatan, COUNT(*) as total_pesanan 
                    FROM pesanan p $where_clause AND p.status_pesanan = 'selesai'";
$pendapatan_result = $conn->query($pendapatan_query);
$pendapatan_data = $pendapatan_result->fetch_assoc();

// Data pesanan
$pesanan_query = "SELECT p.*, u.nama_lengkap, u.email 
                 FROM pesanan p 
                 JOIN users u ON p.id_user = u.id 
                 $where_clause 
                 ORDER BY p.created_at DESC";
$pesanan_result = $conn->query($pesanan_query);

// Data produk terlaris
$produk_terlaris_query = "SELECT pr.nama_produk, SUM(dp.jumlah) as total_terjual, SUM(dp.subtotal) as total_pendapatan
                         FROM detail_pesanan dp 
                         JOIN produk pr ON dp.id_produk = pr.id 
                         JOIN pesanan p ON dp.id_pesanan = p.id 
                         $where_clause 
                         GROUP BY dp.id_produk 
                         ORDER BY total_terjual DESC 
                         LIMIT 10";
$produk_terlaris_result = $conn->query($produk_terlaris_query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan - Teh Organik</title>
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

        .content-card {
            background: var(--white);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            color: var(--white);
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
        }

        .stat-card p {
            margin: 0;
            opacity: 0.9;
        }

        .filter-section {
            background: var(--cream);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 20px;
            color: var(--white);
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(74, 124, 40, 0.3);
        }

        .table thead th {
            background: var(--cream);
            border: none;
            color: var(--primary-green);
            font-weight: 600;
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

        .form-control, .form-select {
            border: 2px solid #e8f0e3;
            border-radius: 8px;
            padding: 10px 15px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--light-green);
            box-shadow: 0 0 0 0.2rem rgba(74, 124, 40, 0.1);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }
            
            .main-content {
                margin-left: 0;
            }
        }

        @media print {
            .sidebar, .top-header, .no-print {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0 !important;
                padding: 0 !important;
            }
            
            .content-card {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
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
            <a href="dashboard.php" class="nav-link">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="kelola_produk.php" class="nav-link">
                <i class="fas fa-box"></i> Kelola Produk
            </a>
            <a href="kelola_pesanan.php" class="nav-link">
                <i class="fas fa-shopping-cart"></i> Kelola Pesanan
            </a>
            <a href="laporan.php" class="nav-link active">
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
                <h4 class="mb-0">Laporan Penjualan</h4>
                <small class="text-muted">Rekapitulasi Data Penjualan Teh Organik</small>
            </div>
            <div class="no-print">
                <button class="btn btn-primary-custom me-2" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Cetak Laporan
                </button>
                <button class="btn btn-success" onclick="exportPDF()">
                    <i class="fas fa-file-pdf me-2"></i>Export PDF
                </button>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section no-print">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Tanggal Awal</label>
                    <input type="date" class="form-control" name="tanggal_awal" value="<?php echo $tanggal_awal; ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal Akhir</label>
                    <input type="date" class="form-control" name="tanggal_akhir" value="<?php echo $tanggal_akhir; ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status Pesanan</label>
                    <select class="form-select" name="status">
                        <option value="">Semua Status</option>
                        <option value="pending" <?php echo $status_filter == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="diproses" <?php echo $status_filter == 'diproses' ? 'selected' : ''; ?>>Diproses</option>
                        <option value="dikirim" <?php echo $status_filter == 'dikirim' ? 'selected' : ''; ?>>Dikirim</option>
                        <option value="selesai" <?php echo $status_filter == 'selesai' ? 'selected' : ''; ?>>Selesai</option>
                        <option value="dibatalkan" <?php echo $status_filter == 'dibatalkan' ? 'selected' : ''; ?>>Dibatalkan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <div>
                        <button type="submit" class="btn btn-primary-custom w-100">
                            <i class="fas fa-filter me-2"></i>Filter
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="stat-card">
                    <i class="fas fa-money-bill-wave fa-3x mb-3"></i>
                    <h3>Rp <?php echo number_format($pendapatan_data['total_pendapatan'], 0, ',', '.'); ?></h3>
                    <p>Total Pendapatan</p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card">
                    <i class="fas fa-shopping-cart fa-3x mb-3"></i>
                    <h3><?php echo number_format($pendapatan_data['total_pesanan']); ?></h3>
                    <p>Pesanan Selesai</p>
                </div>
            </div>
        </div>

        <!-- Detail Pesanan -->
        <div class="content-card">
            <h5 class="mb-4"><i class="fas fa-list me-2"></i>Detail Pesanan</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($pesanan = $pesanan_result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($pesanan['nomor_pesanan']); ?></strong></td>
                            <td><?php echo date('d/m/Y', strtotime($pesanan['created_at'])); ?></td>
                            <td>
                                <div><?php echo htmlspecialchars($pesanan['nama_lengkap']); ?></div>
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

        <!-- Produk Terlaris -->
        <div class="content-card">
            <h5 class="mb-4"><i class="fas fa-fire me-2"></i>Produk Terlaris</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Nama Produk</th>
                            <th>Total Terjual</th>
                            <th>Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rank = 1; while ($produk = $produk_terlaris_result->fetch_assoc()): ?>
                        <tr>
                            <td><strong>#<?php echo $rank++; ?></strong></td>
                            <td><?php echo htmlspecialchars($produk['nama_produk']); ?></td>
                            <td><strong><?php echo number_format($produk['total_terjual']); ?></strong> pcs</td>
                            <td>Rp <?php echo number_format($produk['total_pendapatan'], 0, ',', '.'); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="text-center mt-4 text-muted">
            <p>Laporan ini dicetak pada: <?php echo date('d/m/Y H:i:s'); ?></p>
            <p>Periode: <?php echo date('d/m/Y', strtotime($tanggal_awal)); ?> - <?php echo date('d/m/Y', strtotime($tanggal_akhir)); ?></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function exportPDF() {
            // Implementasi export PDF menggunakan library seperti jsPDF atau DOMPDF
            alert('Fitur export PDF akan diimplementasikan dengan library PDF generator');
        }
    </script>
</body>
</html>
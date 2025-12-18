<?php
require_once '../config/koneksi.php';

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php?error=Anda tidak memiliki akses ke halaman ini!");
    exit();
}

// Proses update status pesanan
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $id_pesanan = clean_input($_POST['id_pesanan']);
    $status = clean_input($_POST['status']);
    
    $sql = "UPDATE pesanan SET status_pesanan = '$status' WHERE id = '$id_pesanan'";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: kelola_pesanan.php?success=Status pesanan berhasil diperbarui!");
        exit();
    } else {
        header("Location: kelola_pesanan.php?error=Gagal memperbarui status pesanan!");
        exit();
    }
}

// Ambil data pesanan
$pesanan_list = $conn->query("SELECT p.*, u.nama_lengkap, u.email, u.no_telepon 
                             FROM pesanan p 
                             JOIN users u ON p.id_user = u.id 
                             ORDER BY p.created_at DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pesanan - Teh Organik</title>
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

        .order-details {
            background: var(--cream);
            border-radius: 10px;
            padding: 15px;
            margin-top: 10px;
        }

        .btn-status {
            padding: 5px 10px;
            margin: 2px;
            border-radius: 5px;
            border: none;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-status:hover {
            transform: translateY(-2px);
        }

        .btn-pending { background: #ffc107; color: #000; }
        .btn-diproses { background: #007bff; color: #fff; }
        .btn-dikirim { background: #17a2b8; color: #fff; }
        .btn-selesai { background: #28a745; color: #fff; }
        .btn-dibatalkan { background: #dc3545; color: #fff; }

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
            <a href="dashboard.php" class="nav-link">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="kelola_produk.php" class="nav-link">
                <i class="fas fa-box"></i> Kelola Produk
            </a>
            <a href="kelola_pesanan.php" class="nav-link active">
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
                <h4 class="mb-0">Kelola Pesanan</h4>
                <small class="text-muted">Manajemen Pesanan Pelanggan</small>
            </div>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo htmlspecialchars($_GET['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($_GET['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Pesanan Table -->
        <div class="content-card">
            <h5 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Daftar Pesanan</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Tanggal</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($pesanan = $pesanan_list->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($pesanan['nomor_pesanan']); ?></strong></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($pesanan['created_at'])); ?></td>
                            <td>
                                <div><?php echo htmlspecialchars($pesanan['nama_lengkap']); ?></div>
                                <small class="text-muted"><?php echo htmlspecialchars($pesanan['email']); ?></small><br>
                                <small class="text-muted"><?php echo htmlspecialchars($pesanan['no_telepon']); ?></small>
                            </td>
                            <td>Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></td>
                            <td>
                                <span class="badge badge-<?php echo $pesanan['status_pesanan']; ?>">
                                    <?php echo ucfirst($pesanan['status_pesanan']); ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" onclick="toggleOrderDetails(<?php echo $pesanan['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        
                        <!-- Order Details (Hidden by default) -->
                        <tr id="details-<?php echo $pesanan['id']; ?>" style="display: none;">
                            <td colspan="6">
                                <div class="order-details">
                                    <h6>Detail Pesanan</h6>
                                    <p><strong>Alamat Pengiriman:</strong><br><?php echo nl2br(htmlspecialchars($pesanan['alamat_pengiriman'])); ?></p>
                                    <?php if ($pesanan['catatan']): ?>
                                        <p><strong>Catatan:</strong><br><?php echo nl2br(htmlspecialchars($pesanan['catatan'])); ?></p>
                                    <?php endif; ?>
                                    
                                    <h6 class="mt-3">Produk yang Dipesan</h6>
                                    <?php
                                    $detail_query = "SELECT dp.*, p.nama_produk 
                                                   FROM detail_pesanan dp 
                                                   JOIN produk p ON dp.id_produk = p.id 
                                                   WHERE dp.id_pesanan = '" . $pesanan['id'] . "'";
                                    $detail_result = $conn->query($detail_query);
                                    ?>
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Produk</th>
                                                <th>Jumlah</th>
                                                <th>Harga</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($detail = $detail_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($detail['nama_produk']); ?></td>
                                                <td><?php echo $detail['jumlah']; ?></td>
                                                <td>Rp <?php echo number_format($detail['harga_satuan'], 0, ',', '.'); ?></td>
                                                <td>Rp <?php echo number_format($detail['subtotal'], 0, ',', '.'); ?></td>
                                            </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                    
                                    <div class="mt-3">
                                        <h6>Update Status Pesanan</h6>
                                        <form method="POST" class="d-inline">
                                            <input type="hidden" name="id_pesanan" value="<?php echo $pesanan['id']; ?>">
                                            <div class="btn-group" role="group">
                                                <button type="submit" name="update_status" value="pending" class="btn-status btn-pending">Pending</button>
                                                <button type="submit" name="update_status" value="diproses" class="btn-status btn-diproses">Diproses</button>
                                                <button type="submit" name="update_status" value="dikirim" class="btn-status btn-dikirim">Dikirim</button>
                                                <button type="submit" name="update_status" value="selesai" class="btn-status btn-selesai">Selesai</button>
                                                <button type="submit" name="update_status" value="dibatalkan" class="btn-status btn-dibatalkan">Dibatalkan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleOrderDetails(orderId) {
            const detailsRow = document.getElementById('details-' + orderId);
            if (detailsRow.style.display === 'none') {
                detailsRow.style.display = 'table-row';
            } else {
                detailsRow.style.display = 'none';
            }
        }
    </script>
</body>
</html>
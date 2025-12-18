<?php
require_once '../config/koneksi.php';

// Cek apakah user sudah login dan role-nya pelanggan
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'pelanggan') {
    header("Location: ../login.php?error=Anda tidak memiliki akses ke halaman ini!");
    exit();
}

// Ambil data produk untuk ditampilkan di katalog
$produk_list = $conn->query("SELECT p.*, k.nama_kategori 
                            FROM produk p 
                            LEFT JOIN kategori k ON p.id_kategori = k.id 
                            WHERE p.status = 'tersedia' AND p.stok > 0
                            ORDER BY p.created_at DESC");

// Ambil data pesanan pelanggan
$pesanan_list = $conn->query("SELECT * FROM pesanan 
                             WHERE id_user = '" . $_SESSION['user_id'] . "' 
                             ORDER BY created_at DESC 
                             LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelanggan - Teh Organik</title>
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

        .navbar {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .navbar-brand {
            color: var(--white) !important;
            font-weight: 600;
            font-size: 1.3rem;
        }

        .navbar-brand i {
            color: var(--accent-gold);
        }

        .main-content {
            padding: 80px 20px 20px;
        }

        .welcome-card {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            color: var(--white);
            border-radius: 20px;
            padding: 40px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .welcome-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(212, 175, 55, 0.1);
            border-radius: 50%;
        }

        .welcome-card h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .product-card {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid rgba(74, 124, 40, 0.1);
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .product-image {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .product-body {
            padding: 20px;
        }

        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-green);
            margin-bottom: 10px;
            height: 50px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .product-category {
            background: var(--cream);
            color: var(--light-green);
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 10px;
        }

        .product-description {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 15px;
            height: 60px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
        }

        .product-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-green);
            margin-bottom: 15px;
        }

        .product-stock {
            background: #d4edda;
            color: #155724;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
            margin-bottom: 15px;
        }

        .btn-order {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            color: var(--white);
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-order:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(74, 124, 40, 0.3);
        }

        .orders-card {
            background: var(--white);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 30px;
        }

        .orders-card h4 {
            color: var(--primary-green);
            margin-bottom: 20px;
            font-weight: 600;
        }

        .order-item {
            background: var(--cream);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid var(--light-green);
        }

        .order-number {
            font-weight: 600;
            color: var(--primary-green);
            margin-bottom: 5px;
        }

        .order-date {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }

        .order-total {
            font-weight: 600;
            color: var(--primary-green);
            font-size: 1.1rem;
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

        .section-title {
            color: var(--primary-green);
            font-weight: 600;
            margin-bottom: 25px;
            font-size: 1.8rem;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--accent-gold);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }

        .floating-tea {
            position: fixed;
            font-size: 2rem;
            color: rgba(212, 175, 55, 0.1);
            animation: float 6s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }

        .tea-1 { top: 10%; left: 5%; animation-delay: 0s; }
        .tea-2 { top: 20%; right: 5%; animation-delay: 2s; }
        .tea-3 { bottom: 20%; left: 5%; animation-delay: 4s; }
        .tea-4 { bottom: 10%; right: 5%; animation-delay: 1s; }

        @media (max-width: 768px) {
            .welcome-card h2 {
                font-size: 1.8rem;
            }
            
            .welcome-card {
                padding: 25px;
            }
        }
    </style>
</head>
<body>
    <!-- Floating Tea Icons -->
    <i class="fas fa-mug-hot floating-tea tea-1"></i>
    <i class="fas fa-leaf floating-tea tea-2"></i>
    <i class="fas fa-mug-hot floating-tea tea-3"></i>
    <i class="fas fa-leaf floating-tea tea-4"></i>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-leaf me-2"></i>Teh Organik
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">
                            <i class="fas fa-home me-1"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">
                            <i class="fas fa-th-large me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pesan_produk.php">
                            <i class="fas fa-shopping-cart me-1"></i> Pesan Produk
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-1"></i> <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user-circle me-2"></i>Profil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container">
            <!-- Welcome Card -->
            <div class="welcome-card">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2>Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?>! 👋</h2>
                        <p class="mb-0">Nikmati berbagai pilihan teh celup organik berkualitas tinggi kami. Temukan rasa alami dan manfaat kesehatan dalam setiap cangkir teh.</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="user-info justify-content-md-end">
                            <div class="user-avatar">
                                <?php echo strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)); ?>
                            </div>
                            <div class="text-white">
                                <strong><?php echo htmlspecialchars($_SESSION['nama_lengkap']); ?></strong><br>
                                <small>Pelanggan</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="orders-card">
                <h4><i class="fas fa-shopping-bag me-2"></i>Pesanan Terbaru Anda</h4>
                <?php if ($pesanan_list && $pesanan_list->num_rows > 0): ?>
                    <?php while ($pesanan = $pesanan_list->fetch_assoc()): ?>
                    <div class="order-item">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="order-number"><?php echo htmlspecialchars($pesanan['nomor_pesanan']); ?></div>
                                <div class="order-date"><?php echo date('d/m/Y H:i', strtotime($pesanan['created_at'])); ?></div>
                            </div>
                            <div class="col-md-3">
                                <div class="order-total">Rp <?php echo number_format($pesanan['total_harga'], 0, ',', '.'); ?></div>
                            </div>
                            <div class="col-md-3 text-md-end">
                                <span class="badge badge-<?php echo $pesanan['status_pesanan']; ?>">
                                    <?php echo ucfirst($pesanan['status_pesanan']); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Anda belum memiliki pesanan. Mulai berbelanja sekarang!</p>
                        <a href="pesan_produk.php" class="btn btn-primary-custom">
                            <i class="fas fa-shopping-cart me-2"></i>Belanja Sekarang
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Products Section -->
            <h3 class="section-title"><i class="fas fa-fire me-2"></i>Produk Terbaru</h3>
            
            <div class="row">
                <?php if ($produk_list && $produk_list->num_rows > 0): ?>
                    <?php while ($produk = $produk_list->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="product-card">
                            <?php if ($produk['gambar']): ?>
                                <img src="../assets/img/<?php echo htmlspecialchars($produk['gambar']); ?>" 
                                     class="product-image" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>">
                            <?php else: ?>
                                <div class="product-image d-flex align-items-center justify-content-center bg-light">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            
                            <div class="product-body">
                                <div class="product-category"><?php echo htmlspecialchars($produk['nama_kategori'] ?? 'Teh Organik'); ?></div>
                                <h5 class="product-title"><?php echo htmlspecialchars($produk['nama_produk']); ?></h5>
                                <p class="product-description"><?php echo htmlspecialchars($produk['deskripsi']); ?></p>
                                <div class="product-price">Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></div>
                                <div class="product-stock">Stok: <?php echo number_format($produk['stok']); ?></div>
                                <button class="btn btn-order" onclick="pesanProduk(<?php echo $produk['id']; ?>)">
                                    <i class="fas fa-shopping-cart me-2"></i>Pesan Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">Belum ada produk tersedia</h4>
                            <p class="text-muted">Mohon maaf, saat ini belum ada produk yang tersedia. Silakan cek kembali nanti.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function pesanProduk(produkId) {
            window.location.href = 'pesan_produk.php?produk=' + produkId;
        }
    </script>
</body>
</html>
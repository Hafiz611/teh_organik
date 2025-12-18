<?php
require_once 'config/koneksi.php';

// Ambil data produk untuk ditampilkan di katalog
$produk_list = $conn->query("SELECT p.*, k.nama_kategori 
                            FROM produk p 
                            LEFT JOIN kategori k ON p.id_kategori = k.id 
                            WHERE p.status = 'tersedia' AND p.stok > 0
                            ORDER BY p.created_at DESC");

// Ambil data kategori untuk filter
$kategori_list = $conn->query("SELECT * FROM kategori ORDER BY nama_kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teh Organik - Produk Teh Celup Organik Berkualitas</title>
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--cream);
            overflow-x: hidden;
        }

        /* Hero Section */
        .hero {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            color: var(--white);
            padding: 100px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: rgba(212, 175, 55, 0.1);
            border-radius: 50%;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: -5%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .hero .lead {
            font-size: 1.3rem;
            margin-bottom: 30px;
            opacity: 0.95;
        }

        .hero-cta {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn-hero {
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 50px;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .btn-hero-primary {
            background: var(--accent-gold);
            color: var(--primary-green);
        }

        .btn-hero-primary:hover {
            background: var(--white);
            color: var(--primary-green);
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .btn-hero-secondary {
            background: transparent;
            color: var(--white);
            border-color: var(--white);
        }

        .btn-hero-secondary:hover {
            background: var(--white);
            color: var(--primary-green);
            transform: translateY(-3px);
        }

        /* Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .navbar-brand {
            color: var(--primary-green) !important;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .navbar-brand i {
            color: var(--accent-gold);
        }

        .navbar-nav .nav-link {
            color: var(--primary-green) !important;
            font-weight: 500;
            margin: 0 10px;
            transition: all 0.3s ease;
            border-radius: 25px;
            padding: 8px 15px !important;
        }

        .navbar-nav .nav-link:hover {
            background: var(--cream);
            color: var(--light-green) !important;
        }

        /* Features Section */
        .features {
            padding: 80px 0;
            background: var(--white);
        }

        .feature-card {
            text-align: center;
            padding: 30px;
            border-radius: 15px;
            transition: all 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            color: var(--white);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 2rem;
        }

        .feature-card h3 {
            color: var(--primary-green);
            margin-bottom: 15px;
            font-weight: 600;
        }

        /* Products Section */
        .products {
            padding: 80px 0;
            background: var(--cream);
        }

        .section-title {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-title h2 {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-green);
            margin-bottom: 15px;
        }

        .section-title p {
            font-size: 1.1rem;
            color: #6c757d;
        }

        .filter-section {
            background: var(--white);
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 40px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .product-card {
            background: var(--white);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            height: 100%;
            border: 1px solid rgba(74, 124, 40, 0.1);
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .product-image {
            height: 250px;
            object-fit: cover;
            width: 100%;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-image {
            transform: scale(1.05);
        }

        .product-body {
            padding: 25px;
        }

        .product-category {
            background: var(--cream);
            color: var(--light-green);
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
        }

        .product-title {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-green);
            margin-bottom: 15px;
            height: 60px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .product-description {
            color: #6c757d;
            font-size: 0.95rem;
            margin-bottom: 20px;
            height: 90px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 4;
            -webkit-box-orient: vertical;
        }

        .product-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-green);
            margin-bottom: 15px;
        }

        .product-stock {
            background: #d4edda;
            color: #155724;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 20px;
        }

        .btn-order {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            border: none;
            border-radius: 50px;
            padding: 15px 25px;
            color: var(--white);
            font-weight: 600;
            width: 100%;
            transition: all 0.3s ease;
            font-size: 1rem;
        }

        .btn-order:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(74, 124, 40, 0.3);
        }

        /* CTA Section */
        .cta {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            color: var(--white);
            padding: 80px 0;
            text-align: center;
        }

        .cta h2 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .cta p {
            font-size: 1.2rem;
            margin-bottom: 30px;
            opacity: 0.95;
        }

        /* Footer */
        footer {
            background: var(--primary-green);
            color: var(--white);
            padding: 50px 0 30px;
        }

        footer h5 {
            color: var(--accent-gold);
            margin-bottom: 20px;
            font-weight: 600;
        }

        footer ul {
            list-style: none;
            padding: 0;
        }

        footer ul li {
            margin-bottom: 10px;
        }

        footer ul li a {
            color: var(--white);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        footer ul li a:hover {
            color: var(--accent-gold);
        }

        .social-icons a {
            color: var(--white);
            font-size: 1.5rem;
            margin: 0 10px;
            transition: all 0.3s ease;
        }

        .social-icons a:hover {
            color: var(--accent-gold);
            transform: translateY(-3px);
        }

        /* Floating Elements */
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

        /* Responsive */
        @media (max-width: 768px) {
            .hero h1 {
                font-size: 2.5rem;
            }
            
            .hero .lead {
                font-size: 1.1rem;
            }
            
            .section-title h2 {
                font-size: 2rem;
            }
            
            .cta h2 {
                font-size: 2rem;
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
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-leaf me-2"></i>Teh Organik
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-home me-1"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#produk">
                            <i class="fas fa-box me-1"></i> Produk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang">
                            <i class="fas fa-info-circle me-1"></i> Tentang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fas fa-sign-in-alt me-1"></i> Masuk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">
                            <i class="fas fa-user-plus me-1"></i> Daftar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center min-vh-100">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1>Teh Celup Organik Terbaik</h1>
                        <p class="lead">Nikmati kesegaran dan manfaat kesehatan dari teh celup organik pilihan, dipetik langsung dari perkebunan terbaik.</p>
                        <div class="hero-cta">
                            <a href="#produk" class="btn-hero btn-hero-primary">
                                <i class="fas fa-shopping-cart me-2"></i>Belanja Sekarang
                            </a>
                            <a href="#tentang" class="btn-hero btn-hero-secondary">
                                <i class="fas fa-play-circle me-2"></i>Pelajari Lebih Lanjut
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features" id="tentang">
        <div class="container">
            <div class="section-title">
                <h2>Mengapa Memilih Teh Organik Kami?</h2>
                <p>Kami berkomitmen menyediakan produk teh berkualitas tinggi dengan standar organik internasional</p>
            </div>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-leaf"></i>
                        </div>
                        <h3>100% Organik</h3>
                        <p>Dibudidayakan tanpa pestisida sintetis, bebas dari bahan kimia berbahaya, dan ramah lingkungan.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <h3>Sertifikat Internasional</h3>
                        <p>Produk kami telah tersertifikasi organik oleh lembaga internasional terpercaya.</p>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-heart"></i>
                        </div>
                        <h3>Kaya Antioksidan</h3>
                        <p>Mengandung antioksidan tinggi yang baik untuk kesehatan dan kekebalan tubuh.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products" id="produk">
        <div class="container">
            <div class="section-title">
                <h2>Katalog Produk Kami</h2>
                <p>Pilih dari berbagai varian teh celup organik berkualitas tinggi</p>
            </div>

            <!-- Filter Section -->
            <div class="filter-section">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filter Produk</h5>
                    </div>
                    <div class="col-md-6">
                        <select class="form-select" id="categoryFilter">
                            <option value="">Semua Kategori</option>
                            <?php while ($kategori = $kategori_list->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($kategori['nama_kategori']); ?>">
                                    <?php echo htmlspecialchars($kategori['nama_kategori']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="row" id="productsGrid">
                <?php if ($produk_list && $produk_list->num_rows > 0): ?>
                    <?php while ($produk = $produk_list->fetch_assoc()): ?>
                    <div class="col-lg-4 col-md-6 mb-4 product-item" data-category="<?php echo htmlspecialchars($produk['nama_kategori'] ?? ''); ?>">
                        <div class="product-card">
                            <?php if ($produk['gambar']): ?>
                                <img src="assets/img/<?php echo htmlspecialchars($produk['gambar']); ?>" 
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
                                <a href="login.php" class="btn btn-order">
                                    <i class="fas fa-shopping-cart me-2"></i>Pesan Sekarang
                                </a>
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
    </section>

    <!-- CTA Section -->
    <section class="cta">
        <div class="container">
            <h2>Siap Menikmati Teh Organik Terbaik?</h2>
            <p>Bergabunglah dengan ribuan pelanggan yang telah merasakan manfaat teh organik kami</p>
            <a href="register.php" class="btn-hero btn-hero-primary">
                <i class="fas fa-user-plus me-2"></i>Daftar Sekarang
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5><i class="fas fa-leaf me-2"></i>Teh Organik</h5>
                    <p>Menyediakan teh celup organik berkualitas tinggi untuk kesehatan dan kesejahteraan Anda.</p>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Link Cepat</h5>
                    <ul>
                        <li><a href="#produk">Produk</a></li>
                        <li><a href="#tentang">Tentang Kami</a></li>
                        <li><a href="login.php">Masuk</a></li>
                        <li><a href="register.php">Daftar</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h5>Kontak</h5>
                    <ul>
                        <li><i class="fas fa-phone me-2"></i>+62 812-3456-7890</li>
                        <li><i class="fas fa-envelope me-2"></i>info@tehorganik.com</li>
                        <li><i class="fas fa-map-marker-alt me-2"></i>Jakarta, Indonesia</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
            <div class="text-center">
                <p class="mb-0">&copy; 2024 Teh Organik. Hak Cipta Dilindungi.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Filter produk berdasarkan kategori
        document.getElementById('categoryFilter').addEventListener('change', function() {
            const selectedCategory = this.value.toLowerCase();
            const productItems = document.querySelectorAll('.product-item');
            
            productItems.forEach(item => {
                const itemCategory = item.dataset.category.toLowerCase();
                if (selectedCategory === '' || itemCategory.includes(selectedCategory)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });

        // Smooth scrolling untuk anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Navbar background on scroll
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.style.background = 'rgba(255, 255, 255, 0.98)';
            } else {
                navbar.style.background = 'rgba(255, 255, 255, 0.95)';
            }
        });
    </script>
</body>
</html>
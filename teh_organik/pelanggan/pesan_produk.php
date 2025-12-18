<?php
require_once '../config/koneksi.php';

// Cek apakah user sudah login dan role-nya pelanggan
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'pelanggan') {
    header("Location: ../login.php?error=Anda tidak memiliki akses ke halaman ini!");
    exit();
}

// Ambil data produk
$produk_list = $conn->query("SELECT p.*, k.nama_kategori 
                            FROM produk p 
                            LEFT JOIN kategori k ON p.id_kategori = k.id 
                            WHERE p.status = 'tersedia' AND p.stok > 0
                            ORDER BY p.created_at DESC");

// Ambil data user
$user_data = $conn->query("SELECT * FROM users WHERE id = '" . $_SESSION['user_id'] . "'")->fetch_assoc();

// Proses pemesanan
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pesan'])) {
    $produk_ids = $_POST['produk_id'];
    $jumlahs = $_POST['jumlah'];
    $alamat_pengiriman = clean_input($_POST['alamat_pengiriman']);
    $catatan = clean_input($_POST['catatan']);
    
    // Validasi
    if (empty($produk_ids) || empty($alamat_pengiriman)) {
        header("Location: pesan_produk.php?error=Silakan pilih produk dan isi alamat pengiriman!");
        exit();
    }
    
    // Hitung total harga
    $total_harga = 0;
    $detail_pesanan = [];
    
    foreach ($produk_ids as $key => $produk_id) {
        $jumlah = $jumlahs[$key];
        if ($jumlah > 0) {
            $produk = $conn->query("SELECT * FROM produk WHERE id = '$produk_id' AND stok >= '$jumlah'")->fetch_assoc();
            if ($produk) {
                $subtotal = $produk['harga'] * $jumlah;
                $total_harga += $subtotal;
                $detail_pesanan[] = [
                    'id_produk' => $produk_id,
                    'jumlah' => $jumlah,
                    'harga_satuan' => $produk['harga'],
                    'subtotal' => $subtotal
                ];
            }
        }
    }
    
    if (empty($detail_pesanan)) {
        header("Location: pesan_produk.php?error=Tidak ada produk yang valid dipilih!");
        exit();
    }
    
    // Generate nomor pesanan
    $nomor_pesanan = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    
    // Insert pesanan
    $sql = "INSERT INTO pesanan (id_user, nomor_pesanan, total_harga, alamat_pengiriman, catatan) 
            VALUES ('" . $_SESSION['user_id'] . "', '$nomor_pesanan', '$total_harga', '$alamat_pengiriman', '$catatan')";
    
    if ($conn->query($sql) === TRUE) {
        $pesanan_id = $conn->insert_id;
        
        // Insert detail pesanan
        foreach ($detail_pesanan as $detail) {
            $sql_detail = "INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga_satuan, subtotal) 
                          VALUES ('$pesanan_id', '{$detail['id_produk']}', '{$detail['jumlah']}', '{$detail['harga_satuan']}', '{$detail['subtotal']}')";
            $conn->query($sql_detail);
            
            // Update stok produk
            $conn->query("UPDATE produk SET stok = stok - {$detail['jumlah']} WHERE id = '{$detail['id_produk']}'");
        }
        
        header("Location: dashboard.php?success=Pesanan berhasil dibuat! Nomor pesanan: $nomor_pesanan");
        exit();
    } else {
        header("Location: pesan_produk.php?error=Gagal membuat pesanan. Silakan coba lagi!");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesan Produk - Teh Organik</title>
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

        .page-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            color: var(--white);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .page-header h2 {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .content-card {
            background: var(--white);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }

        .product-card {
            border: 1px solid #e8f0e3;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
            position: relative;
        }

        .product-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: var(--light-green);
        }

        .product-card.selected {
            border-color: var(--light-green);
            background: rgba(74, 124, 40, 0.05);
        }

        .product-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }

        .product-info h5 {
            color: var(--primary-green);
            font-weight: 600;
            margin-bottom: 10px;
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

        .product-price {
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--primary-green);
        }

        .product-stock {
            background: #d4edda;
            color: #155724;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
            display: inline-block;
            margin-left: 10px;
        }

        .form-control, .form-select {
            border: 2px solid #e8f0e3;
            border-radius: 8px;
            padding: 10px 15px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--light-green);
            box-shadow: 0 0 0 0.2rem rgba(74, 124, 40, 0.1);
        }

        .quantity-input {
            width: 80px;
            text-align: center;
        }

        .btn-primary-custom {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            color: var(--white);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(74, 124, 40, 0.3);
        }

        .cart-summary {
            background: var(--cream);
            border-radius: 12px;
            padding: 20px;
            position: sticky;
            top: 100px;
        }

        .cart-summary h4 {
            color: var(--primary-green);
            margin-bottom: 20px;
            font-weight: 600;
        }

        .cart-item {
            background: var(--white);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid var(--light-green);
        }

        .total-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-green);
            text-align: center;
            margin: 20px 0;
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

        .checkbox-wrapper {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .form-check-input:checked {
            background-color: var(--light-green);
            border-color: var(--light-green);
        }

        @media (max-width: 768px) {
            .cart-summary {
                position: relative;
                top: 0;
                margin-top: 20px;
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
                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-th-large me-1"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="pesan_produk.php">
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
            <!-- Page Header -->
            <div class="page-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2><i class="fas fa-shopping-cart me-2"></i>Pesan Produk Teh Organik</h2>
                        <p class="mb-0">Pilih produk teh organik favorit Anda dan nikmati kualitas terbaik</p>
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

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i><?php echo htmlspecialchars($_GET['error']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" id="orderForm">
                <div class="row">
                    <!-- Product List -->
                    <div class="col-lg-8">
                        <div class="content-card">
                            <h4 class="mb-4"><i class="fas fa-box me-2"></i>Pilih Produk</h4>
                            
                            <?php if ($produk_list && $produk_list->num_rows > 0): ?>
                                <?php $counter = 0; while ($produk = $produk_list->fetch_assoc()): ?>
                                <div class="product-card" id="product-<?php echo $produk['id']; ?>">
                                    <div class="checkbox-wrapper">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="produk_id[]" 
                                                   value="<?php echo $produk['id']; ?>" id="check-<?php echo $produk['id']; ?>"
                                                   onchange="toggleProduct(<?php echo $produk['id']; ?>)">
                                        </div>
                                    </div>
                                    
                                    <div class="row align-items-center">
                                        <div class="col-md-2">
                                            <?php if ($produk['gambar']): ?>
                                                <img src="../assets/img/<?php echo htmlspecialchars($produk['gambar']); ?>" 
                                                     class="product-image" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>">
                                            <?php else: ?>
                                                <div class="product-image d-flex align-items-center justify-content-center bg-light">
                                                    <i class="fas fa-image fa-2x text-muted"></i>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="product-info">
                                                <div class="product-category"><?php echo htmlspecialchars($produk['nama_kategori'] ?? 'Teh Organik'); ?></div>
                                                <h5><?php echo htmlspecialchars($produk['nama_produk']); ?></h5>
                                                <p class="text-muted mb-2"><?php echo htmlspecialchars(substr($produk['deskripsi'], 0, 100)); ?>...</p>
                                                <div>
                                                    <span class="product-price">Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></span>
                                                    <span class="product-stock">Stok: <?php echo number_format($produk['stok']); ?></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Jumlah</label>
                                            <div class="input-group">
                                                <button type="button" class="btn btn-outline-secondary" onclick="decreaseQuantity(<?php echo $produk['id']; ?>)">-</button>
                                                <input type="number" class="form-control quantity-input" name="jumlah[<?php echo $counter; ?>]" 
                                                       id="jumlah-<?php echo $produk['id']; ?>" value="1" min="1" max="<?php echo $produk['stok']; ?>"
                                                       onchange="updateCart()" disabled>
                                                <button type="button" class="btn btn-outline-secondary" onclick="increaseQuantity(<?php echo $produk['id']; ?>, <?php echo $produk['stok']; ?>)">+</button>
                                            </div>
                                            <input type="hidden" id="harga-<?php echo $produk['id']; ?>" value="<?php echo $produk['harga']; ?>">
                                            <input type="hidden" id="nama-<?php echo $produk['id']; ?>" value="<?php echo htmlspecialchars($produk['nama_produk']); ?>">
                                        </div>
                                    </div>
                                </div>
                                <?php $counter++; endwhile; ?>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
                                    <h4 class="text-muted">Belum ada produk tersedia</h4>
                                    <p class="text-muted">Mohon maaf, saat ini belum ada produk yang tersedia. Silakan cek kembali nanti.</p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Shipping Information -->
                        <div class="content-card">
                            <h4 class="mb-4"><i class="fas fa-truck me-2"></i>Informasi Pengiriman</h4>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Alamat Pengiriman</label>
                                    <textarea class="form-control" name="alamat_pengiriman" rows="3" required><?php echo htmlspecialchars($user_data['alamat'] ?? ''); ?></textarea>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Catatan (Opsional)</label>
                                    <textarea class="form-control" name="catatan" rows="2" placeholder="Masukkan catatan khusus untuk pesanan Anda..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cart Summary -->
                    <div class="col-lg-4">
                        <div class="cart-summary">
                            <h4><i class="fas fa-shopping-basket me-2"></i>Ringkasan Pesanan</h4>
                            <div id="cartItems">
                                <p class="text-muted text-center">Belum ada produk yang dipilih</p>
                            </div>
                            <div class="total-price" id="totalPrice">Rp 0</div>
                            <button type="submit" name="pesan" class="btn btn-primary-custom w-100" disabled id="submitBtn">
                                <i class="fas fa-check-circle me-2"></i>Buat Pesanan
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleProduct(productId) {
            const checkbox = document.getElementById('check-' + productId);
            const quantityInput = document.getElementById('jumlah-' + productId);
            const productCard = document.getElementById('product-' + productId);
            
            if (checkbox.checked) {
                quantityInput.disabled = false;
                productCard.classList.add('selected');
            } else {
                quantityInput.disabled = true;
                productCard.classList.remove('selected');
            }
            
            updateCart();
        }

        function increaseQuantity(productId, maxStock) {
            const input = document.getElementById('jumlah-' + productId);
            const currentValue = parseInt(input.value);
            if (currentValue < maxStock) {
                input.value = currentValue + 1;
                updateCart();
            }
        }

        function decreaseQuantity(productId) {
            const input = document.getElementById('jumlah-' + productId);
            const currentValue = parseInt(input.value);
            if (currentValue > 1) {
                input.value = currentValue - 1;
                updateCart();
            }
        }

        function updateCart() {
            const checkboxes = document.querySelectorAll('input[name="produk_id[]"]:checked');
            const cartItems = document.getElementById('cartItems');
            const totalPriceElement = document.getElementById('totalPrice');
            const submitBtn = document.getElementById('submitBtn');
            
            if (checkboxes.length === 0) {
                cartItems.innerHTML = '<p class="text-muted text-center">Belum ada produk yang dipilih</p>';
                totalPriceElement.textContent = 'Rp 0';
                submitBtn.disabled = true;
                return;
            }
            
            let total = 0;
            let cartHTML = '';
            
            checkboxes.forEach(checkbox => {
                const productId = checkbox.value;
                const quantity = document.getElementById('jumlah-' + productId).value;
                const price = document.getElementById('harga-' + productId).value;
                const name = document.getElementById('nama-' + productId).value;
                const subtotal = price * quantity;
                
                total += subtotal;
                
                cartHTML += `
                    <div class="cart-item">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>${name}</strong><br>
                                <small>${quantity} x Rp ${parseInt(price).toLocaleString('id-ID')}</small>
                            </div>
                            <strong>Rp ${subtotal.toLocaleString('id-ID')}</strong>
                        </div>
                    </div>
                `;
            });
            
            cartItems.innerHTML = cartHTML;
            totalPriceElement.textContent = 'Rp ' + total.toLocaleString('id-ID');
            submitBtn.disabled = false;
        }

        // Auto-select product if coming from dashboard
        const urlParams = new URLSearchParams(window.location.search);
        const selectedProduct = urlParams.get('produk');
        if (selectedProduct) {
            const checkbox = document.getElementById('check-' + selectedProduct);
            if (checkbox) {
                checkbox.checked = true;
                toggleProduct(selectedProduct);
            }
        }
    </script>
</body>
</html>
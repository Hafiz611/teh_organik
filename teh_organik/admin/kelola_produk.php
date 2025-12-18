<?php
require_once '../config/koneksi.php';

// Cek apakah user sudah login dan role-nya admin
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] != 'admin') {
    header("Location: ../login.php?error=Anda tidak memiliki akses ke halaman ini!");
    exit();
}

// Proses tambah produk
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tambah_produk'])) {
    $nama_produk = clean_input($_POST['nama_produk']);
    $id_kategori = clean_input($_POST['id_kategori']);
    $deskripsi = clean_input($_POST['deskripsi']);
    $harga = clean_input($_POST['harga']);
    $stok = clean_input($_POST['stok']);
    
    // Handle upload gambar
    $gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../assets/img/";
        $file_name = time() . '_' . basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $file_name;
        
        // Cek apakah folder ada, jika tidak buat folder
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar = $file_name;
            }
        }
    }
    
    $sql = "INSERT INTO produk (nama_produk, id_kategori, deskripsi, harga, stok, gambar) 
            VALUES ('$nama_produk', '$id_kategori', '$deskripsi', '$harga', '$stok', '$gambar')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: kelola_produk.php?success=Produk berhasil ditambahkan!");
        exit();
    } else {
        header("Location: kelola_produk.php?error=Gagal menambahkan produk!");
        exit();
    }
}

// Proses edit produk
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_produk'])) {
    $id = clean_input($_POST['id']);
    $nama_produk = clean_input($_POST['nama_produk']);
    $id_kategori = clean_input($_POST['id_kategori']);
    $deskripsi = clean_input($_POST['deskripsi']);
    $harga = clean_input($_POST['harga']);
    $stok = clean_input($_POST['stok']);
    
    // Handle upload gambar baru
    $gambar_update = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "../assets/img/";
        $file_name = time() . '_' . basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $file_name;
        
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_file)) {
                $gambar_update = ", gambar = '$file_name'";
            }
        }
    }
    
    $sql = "UPDATE produk SET 
            nama_produk = '$nama_produk', 
            id_kategori = '$id_kategori', 
            deskripsi = '$deskripsi', 
            harga = '$harga', 
            stok = '$stok'
            $gambar_update
            WHERE id = '$id'";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: kelola_produk.php?success=Produk berhasil diperbarui!");
        exit();
    } else {
        header("Location: kelola_produk.php?error=Gagal memperbarui produk!");
        exit();
    }
}

// Proses hapus produk
if (isset($_GET['hapus'])) {
    $id = clean_input($_GET['hapus']);
    
    // Hapus gambar terkait
    $produk = $conn->query("SELECT gambar FROM produk WHERE id = '$id'")->fetch_assoc();
    if ($produk && $produk['gambar']) {
        $file_path = "../assets/img/" . $produk['gambar'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    $sql = "DELETE FROM produk WHERE id = '$id'";
    if ($conn->query($sql) === TRUE) {
        header("Location: kelola_produk.php?success=Produk berhasil dihapus!");
        exit();
    } else {
        header("Location: kelola_produk.php?error=Gagal menghapus produk!");
        exit();
    }
}

// Ambil data produk
$produk_list = $conn->query("SELECT p.*, k.nama_kategori 
                            FROM produk p 
                            LEFT JOIN kategori k ON p.id_kategori = k.id 
                            ORDER BY p.created_at DESC");

// Ambil data kategori
$kategori_list = $conn->query("SELECT * FROM kategori ORDER BY nama_kategori");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Teh Organik</title>
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

        .product-card {
            border: 1px solid #e8f0e3;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .product-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }

        .badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
        }

        .badge-tersedia { background: #d4edda; color: #155724; }
        .badge-habis { background: #f8d7da; color: #721c24; }

        .form-control, .form-select {
            border: 2px solid #e8f0e3;
            border-radius: 8px;
            padding: 10px 15px;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--light-green);
            box-shadow: 0 0 0 0.2rem rgba(74, 124, 40, 0.1);
        }

        .modal-header {
            background: linear-gradient(135deg, var(--primary-green) 0%, var(--light-green) 100%);
            color: var(--white);
            border-radius: 15px 15px 0 0;
        }

        .table thead th {
            background: var(--cream);
            border: none;
            color: var(--primary-green);
            font-weight: 600;
        }

        .action-buttons .btn {
            padding: 5px 10px;
            margin: 0 2px;
            border-radius: 5px;
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
            <a href="dashboard.php" class="nav-link">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a href="kelola_produk.php" class="nav-link active">
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
                <h4 class="mb-0">Kelola Produk</h4>
                <small class="text-muted">Manajemen Produk Teh Organik</small>
            </div>
            <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#tambahModal">
                <i class="fas fa-plus me-2"></i>Tambah Produk
            </button>
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

        <!-- Produk Table -->
        <div class="content-card">
            <h5 class="mb-4"><i class="fas fa-box me-2"></i>Daftar Produk</h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Nama Produk</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($produk = $produk_list->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <?php if ($produk['gambar']): ?>
                                    <img src="../assets/img/<?php echo htmlspecialchars($produk['gambar']); ?>" 
                                         class="product-image" alt="<?php echo htmlspecialchars($produk['nama_produk']); ?>">
                                <?php else: ?>
                                    <div class="product-image d-flex align-items-center justify-content-center bg-light">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($produk['nama_produk']); ?></strong><br>
                                <small class="text-muted"><?php echo substr(htmlspecialchars($produk['deskripsi']), 0, 50); ?>...</small>
                            </td>
                            <td><?php echo htmlspecialchars($produk['nama_kategori'] ?? 'Tidak ada kategori'); ?></td>
                            <td>Rp <?php echo number_format($produk['harga'], 0, ',', '.'); ?></td>
                            <td>
                                <span class="badge <?php echo $produk['stok'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                    <?php echo number_format($produk['stok']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-<?php echo $produk['status']; ?>">
                                    <?php echo ucfirst($produk['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-sm btn-warning" onclick="editProduk(<?php echo $produk['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="hapusProduk(<?php echo $produk['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Produk -->
    <div class="modal fade" id="tambahModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Tambah Produk Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="kelola_produk.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nama Produk</label>
                                <input type="text" class="form-control" name="nama_produk" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Kategori</label>
                                <select class="form-select" name="id_kategori" required>
                                    <option value="">Pilih Kategori</option>
                                    <?php while ($kategori = $kategori_list->fetch_assoc()): ?>
                                        <option value="<?php echo $kategori['id']; ?>"><?php echo htmlspecialchars($kategori['nama_kategori']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="deskripsi" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Harga (Rp)</label>
                                <input type="number" class="form-control" name="harga" min="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Stok</label>
                                <input type="number" class="form-control" name="stok" min="0" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Gambar Produk</label>
                                <input type="file" class="form-control" name="gambar" accept="image/*">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_produk" class="btn btn-primary-custom">Simpan Produk</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editProduk(id) {
            // Implementasi edit produk dengan AJAX atau redirect ke halaman edit
            alert('Fitur edit akan diimplementasikan dengan modal edit');
        }

        function hapusProduk(id) {
            if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
                window.location.href = 'kelola_produk.php?hapus=' + id;
            }
        }
    </script>
</body>
</html>
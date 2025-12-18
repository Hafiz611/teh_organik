-- Database: teh_organik_db


-- Tabel Users (Pengguna)
CREATE TABLE IF NOT EXISTS users (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    no_telepon VARCHAR(15),
    alamat TEXT,
    role ENUM('admin', 'pelanggan') DEFAULT 'pelanggan',
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabel Kategori Produk
CREATE TABLE IF NOT EXISTS kategori (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    nama_kategori VARCHAR(50) NOT NULL,
    deskripsi TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tabel Produk
CREATE TABLE IF NOT EXISTS produk (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    nama_produk VARCHAR(100) NOT NULL,
    id_kategori INT(11),
    deskripsi TEXT,
    harga DECIMAL(10,2) NOT NULL,
    stok INT(11) DEFAULT 0,
    gambar VARCHAR(255),
    status ENUM('tersedia', 'habis') DEFAULT 'tersedia',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_kategori) REFERENCES kategori(id) ON DELETE SET NULL
);

-- Tabel Pesanan
CREATE TABLE IF NOT EXISTS pesanan (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    id_user INT(11) NOT NULL,
    nomor_pesanan VARCHAR(50) UNIQUE NOT NULL,
    total_harga DECIMAL(10,2) NOT NULL,
    status_pesanan ENUM('pending', 'diproses', 'dikirim', 'selesai', 'dibatalkan') DEFAULT 'pending',
    alamat_pengiriman TEXT NOT NULL,
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);

-- Tabel Detail Pesanan
CREATE TABLE IF NOT EXISTS detail_pesanan (
    id INT(11) PRIMARY KEY AUTO_INCREMENT,
    id_pesanan INT(11) NOT NULL,
    id_produk INT(11) NOT NULL,
    jumlah INT(11) NOT NULL,
    harga_satuan DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_pesanan) REFERENCES pesanan(id) ON DELETE CASCADE,
    FOREIGN KEY (id_produk) REFERENCES produk(id) ON DELETE CASCADE
);

-- Insert data kategori default
INSERT INTO kategori (nama_kategori, deskripsi) VALUES
('Teh Hijau', 'Teh celup organik dari daun teh hijau pilihan'),
('Teh Hitam', 'Teh celup organik dari daun teh hitam berkualitas'),
('Teh Herbal', 'Teh celup organik dari campuran rempah-rempah alami'),
('Teh Spesial', 'Teh celup organik dengan formula khusus');

-- Insert admin default (password: admin123)
INSERT INTO users (nama_lengkap, email, password, role) VALUES
('Administrator', 'admin@tehorganik.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Insert contoh produk
INSERT INTO produk (nama_produk, id_kategori, deskripsi, harga, stok, gambar) VALUES
('Teh Hijau Celup Premium', 1, 'Teh celup organik dari daun teh hijau pilihan dengan kandungan antioksidan tinggi', 25000.00, 100, 'teh_hijau_premium.jpg'),
('Teh Hitam Ceylon', 2, 'Teh celup organik dari perkebunan teh Ceylon dengan rasa yang khas dan nikmat', 30000.00, 75, 'teh_hitam_ceylon.jpg'),
('Teh Herbal Relaxing', 3, 'Teh celup organik dengan campuran chamomile, lavender, dan mint untuk relaksasi', 35000.00, 50, 'teh_herbal_relaxing.jpg'),
('Teh Spesial Detox', 4, 'Teh celup organik dengan formula khusus untuk detoksifikasi tubuh', 45000.00, 30, 'teh_spesial_detox.jpg');
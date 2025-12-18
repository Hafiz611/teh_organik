# Sistem Informasi Pengolahan dan Pemasaran Produk Teh Celup Organik

Website e-commerce untuk produk teh celup organik dengan sistem manajemen lengkap untuk admin dan kemudahan berbelanja untuk pelanggan.

## 🌿 Fitur Utama

### 🔐 Sistem Autentikasi
- Login dan Registrasi Pengguna
- Role-based Access Control (Admin/Pelanggan)
- Session Management yang Aman
- Password Hashing dengan bcrypt

### 👨‍💼 Dashboard Admin
- Statistik Real-time (Total Produk, Pesanan, Pelanggan, Pendapatan)
- Manajemen Produk (Tambah, Edit, Hapus)
- Upload Gambar Produk
- Manajemen Pesanan (Update Status)
- Laporan Penjualan (Filter, Print, Export PDF)
- Data Produk Terlaris

### 🛍️ Dashboard Pelanggan
- Katalog Produk Interaktif
- Sistem Pemesanan Mudah
- Tracking Status Pesanan
- Riwayat Pembelian
- Keranjang Belanja

### 🎨 Desain & UX
- Responsive Design (Mobile-Friendly)
- Bootstrap 5 Framework
- Nuansa Organik (Hijau, Emas, Cream)
- Animasi & Transisi Halus
- Loading States & Error Handling

## 📁 Struktur Folder

```
teh_organik/
│
├── assets/
│   ├── css/
│   │   └── style.css              # Custom CSS
│   ├── img/                       # Upload gambar produk
│   └── js/
│       └── main.js                # JavaScript utilities
│
├── config/
│   └── koneksi.php                # Database connection
│
├── admin/
│   ├── dashboard.php              # Admin dashboard
│   ├── kelola_produk.php          # Product management
│   ├── kelola_pesanan.php         # Order management
│   └── laporan.php                # Sales reports
│
├── pelanggan/
│   ├── dashboard.php              # Customer dashboard
│   └── pesan_produk.php           # Product ordering
│
├── index.php                      # Landing page/katalog
├── login.php                      # Login page
├── register.php                   # Registration page
├── logout.php                     # Logout handler
├── proses_login.php               # Login processor
├── proses_register.php            # Registration processor
└── database.sql                   # SQL schema
```

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP 8+ (Native)
- **Database**: MySQL dengan PDO
- **Frontend**: HTML5, CSS3, JavaScript ES6+
- **Framework**: Bootstrap 5.3
- **Icons**: Font Awesome 6
- **Security**: Prepared Statements, Password Hashing, Session Management

## 📋 Persyaratan Sistem

- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Web Server (Apache/Nginx)
- Ekstensi PHP: PDO, mysqli, gd, fileinfo

## 🚀 Instalasi

### 1. Setup Database
```sql
-- Buat database baru
CREATE DATABASE teh_organik_db;

-- Import file database.sql
mysql -u root -p teh_organik_db < database.sql
```

### 2. Konfigurasi Koneksi
Edit file `config/koneksi.php` sesuai dengan database Anda:
```php
$host = "localhost";
$username = "root";
$password = "";
$database = "teh_organik_db";
```

### 3. Deploy ke XAMPP
1. Copy folder `teh_organik` ke `htdocs/`
2. Start Apache dan MySQL di XAMPP
3. Akses via browser: `http://localhost/teh_organik/`

### 4. Default Login
- **Admin**: email: `admin@tehorganik.com`, password: `admin123`
- **Pelanggan**: Register melalui halaman registrasi

### 5. Gambar Produk
Gambar produk telah disediakan dalam folder `assets/img/`:
- `teh_hijau_premium.jpg` - Teh Hijau Celup Premium
- `teh_hitam_ceylon.jpg` - Teh Hitam Ceylon
- `teh_herbal_relaxing.jpg` - Teh Herbal Relaxing
- `teh_spesial_detox.jpg` - Teh Spesial Detox
- `teh_jasmine.jpg` - Teh Jasmine Premium
- `teh_oolong.jpg` - Teh Oolong Spesial
- `teh_mint.jpg` - Teh Mint Segar
- `teh_putih.jpg` - Teh Putih Premium
- `logo.png` - Logo Teh Organik

Untuk menambahkan produk baru, import file `produk_tambahan.sql` ke database.

### 6. File Tambahan
- `.htaccess` - Konfigurasi security dan performance
- `galeri.html` - Halaman showcase gambar produk
- `produk_tambahan.sql` - SQL untuk produk tambahan

## 🔐 Keamanan

- SQL Injection Prevention dengan Prepared Statements
- XSS Protection dengan htmlspecialchars()
- CSRF Protection dengan session tokens
- Password Hashing dengan bcrypt
- Input Validation & Sanitization
- File Upload Security

## 📱 Responsive Design

Website fully responsive untuk:
- Desktop (1200px+)
- Tablet (768px - 1199px)
- Mobile (320px - 767px)

## 🎯 Fitur E-commerce

- Product Catalog dengan Filter & Search
- Shopping Cart Management
- Order Processing System
- Payment Status Tracking
- Inventory Management
- Sales Analytics

## 📊 Laporan & Analytics

- Daily/Monthly Sales Reports
- Best Selling Products
- Customer Analytics
- Revenue Tracking
- Export to PDF/Print

## 🔄 Update Status Pesanan

Flow status pesanan:
1. **Pending** - Pesanan masuk
2. **Diproses** - Sedang disiapkan
3. **Dikirim** - Dalam pengiriman
4. **Selesai** - Pesanan selesai
5. **Dibatalkan** - Pesanan dibatalkan

## 🎨 Tema & Branding

- **Color Scheme**:
  - Primary Green: `#2d5016`
  - Light Green: `#4a7c28`
  - Accent Gold: `#d4af37`
  - Cream: `#f8f5f0`
  - White: `#ffffff`

- **Typography**: Segoe UI, Clean & Modern
- **Icons**: Font Awesome 6
- **Animations**: Smooth transitions & micro-interactions

## 📞 Support & Kontak

Untuk bantuan teknis atau pertanyaan:
- Email: info@tehorganik.com
- Phone: +62 812-3456-7890

## 📄 Lisensi

© 2024 Teh Organik. All rights reserved.

---

**Catatan**: Pastikan untuk mengubah password default admin setelah instalasi untuk keamanan sistem.
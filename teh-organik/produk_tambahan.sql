-- Tambahkan produk tambahan sesuai dengan gambar yang tersedia
INSERT INTO produk (nama_produk, id_kategori, deskripsi, harga, stok, gambar, status) VALUES
('Teh Jasmine Premium', 1, 'Teh hijau premium dengan aroma bunga jasmine yang harum dan eksotis, dipetik dari perkebunan teh terbaik', 28000.00, 80, 'teh_jasmine.jpg', 'tersedia'),
('Teh Oolong Spesial', 1, 'Teh oolong dengan fermentasi sempurna, rasa yang khas dan kaya antioksidan', 35000.00, 60, 'teh_oolong.jpg', 'tersedia'),
('Teh Mint Segar', 3, 'Teh herbal dengan daun mint segar, memberikan sensasi dingin dan menyegarkan', 26000.00, 90, 'teh_mint.jpg', 'tersedia'),
('Teh Putih Premium', 1, 'Teh putih dari pucuk daun teh muda, rasa yang halus dan elegan dengan kandungan antioksidan tertinggi', 45000.00, 40, 'teh_putih.jpg', 'tersedia');

-- Update stok produk yang sudah ada
UPDATE produk SET stok = 120 WHERE nama_produk = 'Teh Hijau Celup Premium';
UPDATE produk SET stok = 85 WHERE nama_produk = 'Teh Hitam Ceylon';
UPDATE produk SET stok = 65 WHERE nama_produk = 'Teh Herbal Relaxing';
UPDATE produk SET stok = 45 WHERE nama_produk = 'Teh Spesial Detox';
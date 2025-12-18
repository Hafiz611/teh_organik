<?php
require_once 'config/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_lengkap = clean_input($_POST['nama_lengkap']);
    $email = clean_input($_POST['email']);
    $no_telepon = clean_input($_POST['no_telepon']);
    $alamat = clean_input($_POST['alamat']);
    $role = clean_input($_POST['role']);
    $password = clean_input($_POST['password']);
    $konfirmasi_password = clean_input($_POST['konfirmasi_password']);
    
    // Validasi input
    if (empty($nama_lengkap) || empty($email) || empty($no_telepon) || empty($alamat) || empty($password) || empty($konfirmasi_password)) {
        header("Location: register.php?error=Semua field harus diisi!");
        exit();
    }
    
    // Validasi email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: register.php?error=Format email tidak valid!");
        exit();
    }
    
    // Validasi password
    if (strlen($password) < 6) {
        header("Location: register.php?error=Password minimal 6 karakter!");
        exit();
    }
    
    // Validasi konfirmasi password
    if ($password !== $konfirmasi_password) {
        header("Location: register.php?error=Password dan konfirmasi password tidak cocok!");
        exit();
    }
    
    // Cek apakah email sudah terdaftar
    $cek_email = "SELECT id FROM users WHERE email = '$email'";
    $result = $conn->query($cek_email);
    
    if ($result && $result->num_rows > 0) {
        header("Location: register.php?error=Email sudah terdaftar! Gunakan email lain.");
        exit();
    }
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user baru
    $sql = "INSERT INTO users (nama_lengkap, email, no_telepon, alamat, role, password) 
            VALUES ('$nama_lengkap', '$email', '$no_telepon', '$alamat', '$role', '$hashed_password')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: login.php?success=Registrasi berhasil! Silakan login dengan akun Anda.");
        exit();
    } else {
        header("Location: register.php?error=Terjadi kesalahan. Silakan coba lagi!");
        exit();
    }
} else {
    header("Location: register.php");
    exit();
}
?>
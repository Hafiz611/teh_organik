<?php
require_once 'config/koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = clean_input($_POST['email']);
    $password = clean_input($_POST['password']);
    
    // Validasi input
    if (empty($email) || empty($password)) {
        header("Location: login.php?error=Email dan password harus diisi!");
        exit();
    }
    
    // Query untuk mencari user berdasarkan email
    $sql = "SELECT * FROM users WHERE email = '$email' AND status = 'aktif'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            
            // Redirect berdasarkan role
            if ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } else {
                header("Location: pelanggan/dashboard.php");
            }
            exit();
        } else {
            header("Location: login.php?error=Password salah!");
            exit();
        }
    } else {
        header("Location: login.php?error=Email tidak ditemukan atau akun tidak aktif!");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
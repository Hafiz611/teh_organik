<?php
require_once 'config/koneksi.php';

// Hancurkan semua session
session_destroy();

// Redirect ke halaman login
header("Location: login.php?success=Anda telah berhasil logout!");
exit();
?>
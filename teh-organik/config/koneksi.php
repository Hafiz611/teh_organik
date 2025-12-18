<?php
// Konfigurasi koneksi database MySQL
$host = "localhost";
$username = "root";
$password = "";
$database = "teh_organik_db";

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Set charset ke utf8mb4 untuk mendukung karakter Indonesia
$conn->set_charset("utf8mb4");

// Fungsi untuk membersihkan input dan mencegah SQL Injection
function clean_input($data) {
    global $conn;
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $conn->real_escape_string($data);
}

// Fungsi untuk membuat alert dan redirect
function alert_redirect($message, $location) {
    echo "<script>alert('$message'); window.location.href='$location';</script>";
    exit();
}

// Fungsi untuk membuat alert saja
function alert($message) {
    echo "<script>alert('$message');</script>";
}

// Fungsi untuk redirect saja
function redirect($location) {
    echo "<script>window.location.href='$location';</script>";
    exit();
}

// Session start untuk autentikasi
session_start();
?>
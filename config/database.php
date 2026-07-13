<?php
// ============================================
// SapaKlinik - Database Configuration
// Koneksi MySQL menggunakan MySQLi
// ============================================

date_default_timezone_set('Asia/Jakarta');
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'sapaklinik';

// Buat koneksi
$conn = new mysqli($host, $username, $password, $database);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

// Set charset UTF-8
$conn->set_charset("utf8mb4");

// Fungsi helper untuk redirect
function redirect($url) {
    header("Location: $url");
    exit();
}

// Fungsi helper untuk cek login
function cekLogin() {
    if (!isset($_SESSION['id_user'])) {
        redirect('../auth/login.php');
    }
}

// Fungsi helper untuk cek role admin
function cekAdmin() {
    cekLogin();
    if ($_SESSION['role'] !== 'admin') {
        redirect('../auth/login.php');
    }
}

// Fungsi helper untuk cek role pasien
function cekPasien() {
    cekLogin();
    if ($_SESSION['role'] !== 'pasien') {
        redirect('../auth/login.php');
    }
}

// Fungsi helper untuk escape output
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>

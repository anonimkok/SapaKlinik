<?php
date_default_timezone_set('Asia/Jakarta');
session_start();

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'sapaklinik';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    die("Koneksi database gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

function redirect($url) {
    header("Location: $url");
    exit();
}

function cekLogin() {
    if (!isset($_SESSION['id_user'])) {
        redirect('../auth/login.php');
    }
}

function cekAdmin() {
    cekLogin();
    if ($_SESSION['role'] !== 'admin') {
        redirect('../auth/login.php');
    }
}

function cekPasien() {
    cekLogin();
    if ($_SESSION['role'] !== 'pasien') {
        redirect('../auth/login.php');
    }
}

function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>

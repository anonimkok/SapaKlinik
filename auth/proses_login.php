<?php
require_once '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('login.php');
}

$action = $_POST['action'] ?? 'login';

// ============================================
// REGISTER
// ============================================
if ($action === 'register') {
    $nama_lengkap = trim($_POST['nama_lengkap'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validasi
    if (empty($nama_lengkap) || empty($email) || empty($password)) {
        $_SESSION['register_error'] = 'Semua field harus diisi.';
        redirect('register.php');
    }
    
    if (strlen($password) < 6) {
        $_SESSION['register_error'] = 'Password minimal 6 karakter.';
        redirect('register.php');
    }
    
    if ($password !== $confirm_password) {
        $_SESSION['register_error'] = 'Konfirmasi password tidak cocok.';
        redirect('register.php');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['register_error'] = 'Format email tidak valid.';
        redirect('register.php');
    }
    
    // Cek apakah email sudah terdaftar
    $stmt = $conn->prepare("SELECT id_user FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $_SESSION['register_error'] = 'Email sudah terdaftar. Silakan gunakan email lain.';
        redirect('register.php');
    }
    $stmt->close();
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user baru
    $stmt = $conn->prepare("INSERT INTO users (nama_lengkap, email, password, role) VALUES (?, ?, ?, 'pasien')");
    $stmt->bind_param("sss", $nama_lengkap, $email, $hashed_password);
    
    if ($stmt->execute()) {
        $_SESSION['login_success'] = 'Registrasi berhasil! Silakan login.';
        redirect('login.php');
    } else {
        $_SESSION['register_error'] = 'Terjadi kesalahan. Silakan coba lagi.';
        redirect('register.php');
    }
    $stmt->close();
}

// ============================================
// LOGIN
// ============================================
else {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validasi
    if (empty($email) || empty($password)) {
        $_SESSION['login_error'] = 'Email dan password harus diisi.';
        redirect('login.php');
    }
    
    // Cari user berdasarkan email
    $stmt = $conn->prepare("SELECT id_user, nama_lengkap, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        $_SESSION['login_error'] = 'Email atau password salah.';
        redirect('login.php');
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Verifikasi password
    if (!password_verify($password, $user['password'])) {
        $_SESSION['login_error'] = 'Email atau password salah.';
        redirect('login.php');
    }
    
    // Set session
    $_SESSION['id_user'] = $user['id_user'];
    $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['role'];
    
    // Redirect berdasarkan role
    if ($user['role'] === 'admin') {
        redirect('../admin/dashboard.php');
    } else {
        redirect('../pasien/dashboard.php');
    }
}

$conn->close();
?>

<?php
require_once '../config/database.php';
cekAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

$type = $_POST['type'] ?? '';
$id = (int) ($_POST['id'] ?? 0);
$redirect_to = $_POST['redirect'] ?? 'dashboard.php';

if ($id <= 0) {
    $_SESSION['admin_error'] = 'Data tidak valid.';
    redirect($redirect_to);
}

if ($type === 'antrian') {
    $stmt = $conn->prepare("DELETE FROM antrian WHERE id_antrian = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['admin_success'] = 'Antrian berhasil dihapus.';
    } else {
        $_SESSION['admin_error'] = 'Gagal menghapus antrian.';
    }
    $stmt->close();
    
} elseif ($type === 'poli') {
    // Hapus antrian terkait dulu, lalu hapus poli
    $stmt = $conn->prepare("DELETE FROM antrian WHERE id_poli = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    $stmt = $conn->prepare("DELETE FROM poli WHERE id_poli = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $_SESSION['admin_success'] = 'Poli dan antrian terkait berhasil dihapus.';
    } else {
        $_SESSION['admin_error'] = 'Gagal menghapus poli.';
    }
    $stmt->close();
    
} else {
    $_SESSION['admin_error'] = 'Tipe data tidak valid.';
}

redirect($redirect_to);
?>

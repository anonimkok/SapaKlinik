<?php
require_once '../config/database.php';
cekAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

$id_antrian = (int) ($_POST['id_antrian'] ?? 0);
$status = $_POST['status'] ?? '';
$redirect_date = $_POST['redirect_date'] ?? date('Y-m-d');

// Validasi status
$valid_statuses = ['Menunggu', 'Diproses', 'Selesai', 'Batal'];
if (!in_array($status, $valid_statuses) || $id_antrian <= 0) {
    $_SESSION['admin_error'] = 'Data tidak valid.';
    redirect('dashboard.php?tanggal=' . $redirect_date);
}

// Update status antrian
$stmt = $conn->prepare("UPDATE antrian SET status = ? WHERE id_antrian = ?");
$stmt->bind_param("si", $status, $id_antrian);

if ($stmt->execute()) {
    $_SESSION['admin_success'] = "Status antrian berhasil diubah menjadi \"$status\".";
} else {
    $_SESSION['admin_error'] = 'Gagal mengubah status antrian.';
}
$stmt->close();

redirect('dashboard.php?tanggal=' . $redirect_date);
?>

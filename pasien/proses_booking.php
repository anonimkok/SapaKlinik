<?php
require_once '../config/database.php';
cekPasien();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

$id_user = $_SESSION['id_user'];
$id_poli = (int) ($_POST['id_poli'] ?? 0);
$tanggal_berobat = $_POST['tanggal_berobat'] ?? '';

// Validasi
if ($id_poli <= 0 || empty($tanggal_berobat)) {
    $_SESSION['pasien_error'] = 'Pilih poli dan tanggal berobat.';
    redirect('dashboard.php');
}

// Validasi tanggal tidak di masa lalu
if ($tanggal_berobat < date('Y-m-d')) {
    $_SESSION['pasien_error'] = 'Tanggal berobat tidak boleh di masa lalu.';
    redirect('dashboard.php');
}

// Cek apakah poli valid
$stmt = $conn->prepare("SELECT id_poli FROM poli WHERE id_poli = ?");
$stmt->bind_param("i", $id_poli);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    $_SESSION['pasien_error'] = 'Poli tidak ditemukan.';
    redirect('dashboard.php');
}
$stmt->close();

// Cek apakah sudah booking di poli & tanggal yang sama
$stmt = $conn->prepare("SELECT id_antrian FROM antrian WHERE id_user = ? AND id_poli = ? AND tanggal_berobat = ? AND status != 'Batal'");
$stmt->bind_param("iis", $id_user, $id_poli, $tanggal_berobat);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    $_SESSION['pasien_error'] = 'Anda sudah memiliki booking di poli dan tanggal ini.';
    redirect('dashboard.php');
}
$stmt->close();

// Generate nomor antrian: ambil nomor terakhir di tanggal & poli tersebut, lalu +1
$stmt = $conn->prepare("SELECT MAX(no_antrian) as last_no FROM antrian WHERE id_poli = ? AND tanggal_berobat = ?");
$stmt->bind_param("is", $id_poli, $tanggal_berobat);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$no_antrian = ($result['last_no'] ?? 0) + 1;
$stmt->close();

// Insert antrian baru
$stmt = $conn->prepare("INSERT INTO antrian (id_user, id_poli, tanggal_berobat, no_antrian, status) VALUES (?, ?, ?, ?, 'Menunggu')");
$stmt->bind_param("iisi", $id_user, $id_poli, $tanggal_berobat, $no_antrian);

if ($stmt->execute()) {
    $_SESSION['pasien_success'] = "Booking berhasil! Nomor antrian Anda: $no_antrian";
} else {
    $_SESSION['pasien_error'] = 'Terjadi kesalahan saat booking. Silakan coba lagi.';
}
$stmt->close();

redirect('dashboard.php');
?>

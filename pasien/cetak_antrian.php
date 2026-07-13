<?php
use Dompdf\Dompdf;
use Dompdf\Options;

require_once '../config/database.php';
cekPasien();

// Cek apakah Dompdf tersedia
$autoload_path = '../vendor/autoload.php';
$dompdf_available = file_exists($autoload_path);

if ($dompdf_available) {
    require_once $autoload_path;
}

$id_antrian = (int) ($_GET['id'] ?? 0);
$id_user = $_SESSION['id_user'];

if ($id_antrian <= 0) {
    $_SESSION['pasien_error'] = 'Data antrian tidak valid.';
    redirect('dashboard.php');
}

// Ambil data antrian (pastikan milik user yang login)
$stmt = $conn->prepare("
    SELECT a.*, u.nama_lengkap, u.email, p.nama_poli 
    FROM antrian a 
    JOIN users u ON a.id_user = u.id_user 
    JOIN poli p ON a.id_poli = p.id_poli 
    WHERE a.id_antrian = ? AND a.id_user = ?
");
$stmt->bind_param("ii", $id_antrian, $id_user);
$stmt->execute();
$data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$data) {
    $_SESSION['pasien_error'] = 'Antrian tidak ditemukan.';
    redirect('dashboard.php');
}

// HTML untuk tiket
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: Arial, Helvetica, sans-serif;
            background: #f8f8f8;
            padding: 20px;
        }
        .ticket {
            max-width: 400px;
            margin: 0 auto;
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        .ticket-header {
            background: linear-gradient(135deg, #10B981, #059669);
            color: white;
            padding: 24px;
            text-align: center;
        }
        .ticket-header h1 {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .ticket-header p {
            font-size: 12px;
            opacity: 0.85;
        }
        .ticket-number {
            background: #f0fdf4;
            padding: 24px;
            text-align: center;
            border-bottom: 2px dashed #d1d5db;
        }
        .ticket-number .label {
            font-size: 11px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 8px;
        }
        .ticket-number .number {
            font-size: 56px;
            font-weight: 800;
            color: #10B981;
            line-height: 1;
        }
        .ticket-body {
            padding: 24px;
        }
        .ticket-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .ticket-row:last-child {
            border-bottom: none;
        }
        .ticket-row .label {
            font-size: 12px;
            color: #9ca3af;
        }
        .ticket-row .value {
            font-size: 13px;
            font-weight: 600;
            color: #1f2937;
            text-align: right;
        }
        .ticket-footer {
            background: #f9fafb;
            padding: 16px 24px;
            text-align: center;
            border-top: 1px solid #f3f4f6;
        }
        .ticket-footer p {
            font-size: 10px;
            color: #9ca3af;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
        }
        .status-menunggu { background: #fef3c7; color: #d97706; }
        .status-diproses { background: #dbeafe; color: #2563eb; }
        .status-selesai { background: #d1fae5; color: #059669; }
        .status-batal { background: #fee2e2; color: #dc2626; }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="ticket-header">
            <h1>SapaKlinik</h1>
            <p>Sistem Antrian Klinik Modern</p>
        </div>
        
        <div class="ticket-number">
            <div class="label">Nomor Antrian</div>
            <div class="number">' . str_pad($data['no_antrian'], 2, '0', STR_PAD_LEFT) . '</div>
        </div>
        
        <div class="ticket-body">
            <div class="ticket-row">
                <span class="label">Nama Pasien</span>
                <span class="value">' . htmlspecialchars($data['nama_lengkap']) . '</span>
            </div>
            <div class="ticket-row">
                <span class="label">Email</span>
                <span class="value">' . htmlspecialchars($data['email']) . '</span>
            </div>
            <div class="ticket-row">
                <span class="label">Poli</span>
                <span class="value">' . htmlspecialchars($data['nama_poli']) . '</span>
            </div>
            <div class="ticket-row">
                <span class="label">Tanggal Berobat</span>
                <span class="value">' . date('d M Y', strtotime($data['tanggal_berobat'])) . '</span>
            </div>
            <div class="ticket-row">
                <span class="label">Status</span>
                <span class="value">
                    <span class="status-badge status-' . strtolower($data['status']) . '">' . $data['status'] . '</span>
                </span>
            </div>
        </div>
        
        <div class="ticket-footer">
            <p>Dicetak pada ' . date('d M Y H:i:s') . '</p>
            <p style="margin-top: 4px;">&copy; ' . date('Y') . ' SapaKlinik - Sistem Antrian Klinik Modern</p>
        </div>
    </div>
</body>
</html>';

// Jika Dompdf tersedia, generate PDF
if ($dompdf_available) {
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Arial');
    
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A6', 'portrait');
    $dompdf->render();
    
    $filename = 'Tiket_Antrian_' . str_pad($data['no_antrian'], 2, '0', STR_PAD_LEFT) . '_' . $data['tanggal_berobat'] . '.pdf';
    $dompdf->stream($filename, ['Attachment' => true]);
} else {
    // Fallback: tampilkan HTML langsung untuk dicetak
    echo $html;
    echo '<div style="text-align:center; margin-top:20px; font-family: Arial;">
        <button onclick="window.print()" style="padding:10px 24px; background:#10B981; color:white; border:none; border-radius:8px; cursor:pointer; font-size:14px;">Cetak Tiket</button>
        <a href="dashboard.php" style="display:inline-block; margin-left:10px; padding:10px 24px; background:#27272A; color:white; border:none; border-radius:8px; text-decoration:none; font-size:14px;">Kembali</a>
        <p style="margin-top:12px; font-size:12px; color:#9ca3af;">Dompdf belum terinstall. Jalankan <code>composer install</code> untuk mengaktifkan fitur download PDF.</p>
    </div>';
}
?>

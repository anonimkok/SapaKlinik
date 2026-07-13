<?php
require_once '../config/database.php';
cekPasien();

$pageTitle = 'Dashboard Pasien';
$id_user = $_SESSION['id_user'];

// Ambil daftar poli
$poli_list = $conn->query("SELECT * FROM poli ORDER BY nama_poli ASC");

// Ambil riwayat antrian pasien
$stmt = $conn->prepare("
    SELECT a.*, p.nama_poli 
    FROM antrian a 
    JOIN poli p ON a.id_poli = p.id_poli 
    WHERE a.id_user = ? 
    ORDER BY a.tanggal_berobat DESC, a.no_antrian DESC
");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$riwayat = $stmt->get_result();
$stmt->close();

// Statistik pasien
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM antrian WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$total_booking = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM antrian WHERE id_user = ? AND status = 'Menunggu'");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$total_menunggu = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$stmt = $conn->prepare("SELECT COUNT(*) as total FROM antrian WHERE id_user = ? AND status = 'Selesai'");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$total_selesai = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

$success = $_SESSION['pasien_success'] ?? '';
$error = $_SESSION['pasien_error'] ?? '';
unset($_SESSION['pasien_success'], $_SESSION['pasien_error']);

include '../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8 animate-fade-in">
        <h1 class="font-display font-semibold text-2xl sm:text-3xl">Halo, <span class="text-primary"><?= e($_SESSION['nama_lengkap']) ?></span></h1>
        <p class="text-text-secondary text-sm font-mono mt-1">Booking antrian dan pantau status kunjungan anda</p>
    </div>
    
    <?php if($success): ?>
    <div class="alert alert-success rounded-lg px-4 py-3 mb-6 text-sm font-mono flex items-center space-x-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span><?= e($success) ?></span>
    </div>
    <?php endif; ?>
    
    <?php if($error): ?>
    <div class="alert alert-error rounded-lg px-4 py-3 mb-6 text-sm font-mono flex items-center space-x-2">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span><?= e($error) ?></span>
    </div>
    <?php endif; ?>

    <!-- Stats Cards -->
    <div class="grid grid-cols-3 gap-4 mb-8">
        <div class="glass-card rounded-xl p-5 animate-fade-in animate-delay-1">
            <div class="font-display font-bold text-2xl text-white"><?= $total_booking ?></div>
            <div class="text-xs text-text-secondary font-mono mt-1">Total Booking</div>
        </div>
        <div class="glass-card rounded-xl p-5 animate-fade-in animate-delay-2">
            <div class="font-display font-bold text-2xl text-yellow-400"><?= $total_menunggu ?></div>
            <div class="text-xs text-text-secondary font-mono mt-1">Menunggu</div>
        </div>
        <div class="glass-card rounded-xl p-5 animate-fade-in animate-delay-3">
            <div class="font-display font-bold text-2xl text-primary"><?= $total_selesai ?></div>
            <div class="text-xs text-text-secondary font-mono mt-1">Selesai</div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Booking Form -->
        <div class="lg:col-span-1">
            <div class="glass-card rounded-xl p-6 animate-fade-in animate-delay-2">
                <div class="flex items-center space-x-3 mb-6">
                    <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center border border-primary/20">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    </div>
                    <h2 class="font-display font-semibold text-lg">Booking Antrian</h2>
                </div>
                
                <form method="POST" action="proses_booking.php" class="space-y-4">
                    <div>
                        <label class="block text-sm text-text-secondary mb-2 font-mono" for="id_poli">Pilih Poli</label>
                        <select id="id_poli" name="id_poli" required
                            class="input-field w-full px-4 py-3 rounded-lg text-white text-sm font-mono">
                            <option value="" class="bg-surface">-- Pilih Poli --</option>
                            <?php 
                            $poli_list->data_seek(0);
                            while($p = $poli_list->fetch_assoc()): 
                            ?>
                            <option value="<?= $p['id_poli'] ?>" class="bg-surface"><?= e($p['nama_poli']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm text-text-secondary mb-2 font-mono" for="tanggal_berobat">Tanggal Berobat</label>
                        <input type="date" id="tanggal_berobat" name="tanggal_berobat" required
                            min="<?= date('Y-m-d') ?>"
                            value="<?= date('Y-m-d') ?>"
                            class="input-field w-full px-4 py-3 rounded-lg text-white text-sm font-mono">
                    </div>
                    
                    <button type="submit" class="btn-primary w-full py-3 rounded-lg text-white font-semibold text-sm font-mono flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        <span>Booking Sekarang</span>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Riwayat Antrian -->
        <div class="lg:col-span-2">
            <div class="glass-card rounded-xl overflow-hidden animate-fade-in animate-delay-3">
                <div class="px-6 py-4 border-b border-border/50 flex items-center justify-between">
                    <h2 class="font-display font-semibold text-lg">Riwayat Antrian</h2>
                    <span class="text-xs text-text-secondary font-mono"><?= $riwayat->num_rows ?> total</span>
                </div>
                
                <?php if ($riwayat->num_rows > 0): ?>
                <div class="divide-y divide-border/20">
                    <?php while($row = $riwayat->fetch_assoc()): ?>
                    <div class="table-row px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                <!-- Nomor Antrian -->
                                <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center border border-primary/20 flex-shrink-0">
                                    <span class="font-display font-bold text-primary text-lg"><?= str_pad($row['no_antrian'], 2, '0', STR_PAD_LEFT) ?></span>
                                </div>
                                
                                <div>
                                    <h3 class="font-semibold text-sm text-white"><?= e($row['nama_poli']) ?></h3>
                                    <p class="text-xs text-text-secondary font-mono mt-0.5">
                                        <?= date('d M Y', strtotime($row['tanggal_berobat'])) ?>
                                    </p>
                                </div>
                            </div>
                            
                            <div class="flex items-center space-x-3">
                                <?php
                                $badgeClass = '';
                                switch($row['status']) {
                                    case 'Menunggu': $badgeClass = 'badge-menunggu'; break;
                                    case 'Diproses': $badgeClass = 'badge-diproses'; break;
                                    case 'Selesai': $badgeClass = 'badge-selesai'; break;
                                    case 'Batal': $badgeClass = 'badge-batal'; break;
                                }
                                ?>
                                <span class="inline-block px-3 py-1 rounded-full text-xs font-mono font-semibold <?= $badgeClass ?>"><?= e($row['status']) ?></span>
                                
                                <!-- Cetak Tiket -->
                                <a href="cetak_antrian.php?id=<?= $row['id_antrian'] ?>" 
                                   class="px-3 py-1.5 rounded-lg text-xs font-mono bg-surface text-text-secondary border border-border hover:border-primary hover:text-primary transition-colors inline-flex items-center space-x-1"
                                   title="Cetak Tiket">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    <span class="hidden sm:inline">Cetak</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <div class="px-6 py-16 text-center">
                    <svg class="w-12 h-12 text-text-secondary/30 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <p class="text-text-secondary text-sm font-mono">Belum ada riwayat antrian.</p>
                    <p class="text-text-secondary/50 text-xs font-mono mt-1">Buat booking pertama anda di form sebelah kiri.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<?php
require_once '../config/database.php';
cekAdmin();

$pageTitle = 'Dashboard Admin';

// Get filter date (default: hari ini)
$filter_date = $_GET['tanggal'] ?? date('Y-m-d');

// Statistik
$stats = [];

// Total antrian hari ini
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM antrian WHERE tanggal_berobat = ?");
$stmt->bind_param("s", $filter_date);
$stmt->execute();
$stats['total'] = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Antrian menunggu
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM antrian WHERE tanggal_berobat = ? AND status = 'Menunggu'");
$stmt->bind_param("s", $filter_date);
$stmt->execute();
$stats['menunggu'] = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Antrian diproses
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM antrian WHERE tanggal_berobat = ? AND status = 'Diproses'");
$stmt->bind_param("s", $filter_date);
$stmt->execute();
$stats['diproses'] = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Antrian selesai
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM antrian WHERE tanggal_berobat = ? AND status = 'Selesai'");
$stmt->bind_param("s", $filter_date);
$stmt->execute();
$stats['selesai'] = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();

// Daftar antrian
$stmt = $conn->prepare("
    SELECT a.*, u.nama_lengkap, u.email, p.nama_poli 
    FROM antrian a 
    JOIN users u ON a.id_user = u.id_user 
    JOIN poli p ON a.id_poli = p.id_poli 
    WHERE a.tanggal_berobat = ? 
    ORDER BY a.no_antrian ASC
");
$stmt->bind_param("s", $filter_date);
$stmt->execute();
$antrian_list = $stmt->get_result();
$stmt->close();

$success = $_SESSION['admin_success'] ?? '';
$error = $_SESSION['admin_error'] ?? '';
unset($_SESSION['admin_success'], $_SESSION['admin_error']);

include '../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8 animate-fade-in">
        <div>
            <h1 class="font-display font-semibold text-2xl sm:text-3xl">Dashboard <span class="text-primary">Admin</span></h1>
            <p class="text-text-secondary text-sm font-mono mt-1">Kelola antrian pasien klinik</p>
        </div>
        
        <!-- Date Filter -->
        <form method="GET" class="flex items-center space-x-3">
            <input type="date" name="tanggal" value="<?= e($filter_date) ?>" 
                class="input-field px-4 py-2.5 rounded-lg text-white text-sm font-mono" 
                onchange="this.form.submit()">
        </form>
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
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <!-- Total -->
        <div class="glass-card rounded-xl p-6 animate-fade-in animate-delay-1">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-surface flex items-center justify-center border border-border">
                    <svg class="w-5 h-5 text-text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
            </div>
            <div class="font-display font-bold text-2xl text-white"><?= $stats['total'] ?></div>
            <div class="text-xs text-text-secondary font-mono mt-1">Total Antrian</div>
        </div>
        
        <!-- Menunggu -->
        <div class="glass-card rounded-xl p-6 animate-fade-in animate-delay-2">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-yellow-500/10 flex items-center justify-center border border-yellow-500/20">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="font-display font-bold text-2xl text-yellow-400"><?= $stats['menunggu'] ?></div>
            <div class="text-xs text-text-secondary font-mono mt-1">Menunggu</div>
        </div>
        
        <!-- Diproses -->
        <div class="glass-card rounded-xl p-6 animate-fade-in animate-delay-3">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center border border-blue-500/20">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </div>
            </div>
            <div class="font-display font-bold text-2xl text-blue-400"><?= $stats['diproses'] ?></div>
            <div class="text-xs text-text-secondary font-mono mt-1">Diproses</div>
        </div>
        
        <!-- Selesai -->
        <div class="glass-card rounded-xl p-6 animate-fade-in animate-delay-4">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center border border-primary/20">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="font-display font-bold text-2xl text-primary"><?= $stats['selesai'] ?></div>
            <div class="text-xs text-text-secondary font-mono mt-1">Selesai</div>
        </div>
    </div>
    
    <!-- Queue Table -->
    <div class="glass-card rounded-xl overflow-hidden animate-fade-in animate-delay-5">
        <div class="px-6 py-4 border-b border-border/50 flex items-center justify-between">
            <h2 class="font-display font-semibold text-lg">Daftar Antrian — <?= date('d M Y', strtotime($filter_date)) ?></h2>
            <span class="text-xs text-text-secondary font-mono"><?= $stats['total'] ?> pasien</span>
        </div>
        
        <?php if ($antrian_list && $antrian_list->num_rows > 0): ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-border/30">
                        <th class="text-center px-6 py-3 text-xs text-text-secondary font-mono font-semibold uppercase tracking-wider">No</th>
                        <th class="text-left px-6 py-3 text-xs text-text-secondary font-mono font-semibold uppercase tracking-wider">Pasien</th>
                        <th class="text-left px-6 py-3 text-xs text-text-secondary font-mono font-semibold uppercase tracking-wider">Poli</th>
                        <th class="text-left px-6 py-3 text-xs text-text-secondary font-mono font-semibold uppercase tracking-wider">Status</th>
                        <th class="text-right px-6 py-3 text-xs text-text-secondary font-mono font-semibold uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $antrian_list->fetch_assoc()): ?>
                    <tr class="table-row border-b border-border/20">
                        <td class="px-6 py-4 text-center">
                            <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center border border-primary/20 mx-auto">
                                <span class="font-display font-bold text-primary text-sm"><?= str_pad($row['no_antrian'], 2, '0', STR_PAD_LEFT) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-white"><?= e($row['nama_lengkap']) ?></div>
                            <div class="text-xs text-text-secondary font-mono mt-0.5"><?= e($row['email']) ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-text-secondary font-mono"><?= e($row['nama_poli']) ?></span>
                        </td>
                        <td class="px-6 py-4">
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
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end space-x-2">
                                <?php if($row['status'] === 'Menunggu'): ?>
                                <form method="POST" action="../admin/proses_update.php" class="inline">
                                    <input type="hidden" name="id_antrian" value="<?= $row['id_antrian'] ?>">
                                    <input type="hidden" name="status" value="Diproses">
                                    <input type="hidden" name="redirect_date" value="<?= e($filter_date) ?>">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-mono bg-blue-500/10 text-blue-400 border border-blue-500/20 hover:bg-blue-500/20 transition-colors" title="Proses">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/></svg>
                                        Proses
                                    </button>
                                </form>
                                <?php elseif($row['status'] === 'Diproses'): ?>
                                <form method="POST" action="../admin/proses_update.php" class="inline">
                                    <input type="hidden" name="id_antrian" value="<?= $row['id_antrian'] ?>">
                                    <input type="hidden" name="status" value="Selesai">
                                    <input type="hidden" name="redirect_date" value="<?= e($filter_date) ?>">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-mono bg-primary/10 text-primary border border-primary/20 hover:bg-primary/20 transition-colors" title="Selesai">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        Selesai
                                    </button>
                                </form>
                                <?php endif; ?>
                                
                                <?php if($row['status'] !== 'Selesai'): ?>
                                <form method="POST" action="../admin/proses_delete.php" class="inline" onsubmit="return confirm('Yakin ingin menghapus antrian ini?')">
                                    <input type="hidden" name="type" value="antrian">
                                    <input type="hidden" name="id" value="<?= $row['id_antrian'] ?>">
                                    <input type="hidden" name="redirect" value="dashboard.php?tanggal=<?= e($filter_date) ?>">
                                    <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-mono bg-accent/10 text-accent border border-accent/20 hover:bg-accent/20 transition-colors" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="px-6 py-16 text-center">
            <svg class="w-12 h-12 text-text-secondary/30 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
            <p class="text-text-secondary text-sm font-mono">Belum ada antrian untuk tanggal ini.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
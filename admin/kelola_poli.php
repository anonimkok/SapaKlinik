<?php
require_once '../config/database.php';
cekAdmin();

$pageTitle = 'Kelola Poli';

// Proses tambah/edit poli
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'tambah') {
        $nama_poli = trim($_POST['nama_poli'] ?? '');
        $keterangan = trim($_POST['keterangan'] ?? '');
        
        if (!empty($nama_poli)) {
            $stmt = $conn->prepare("INSERT INTO poli (nama_poli, keterangan) VALUES (?, ?)");
            $stmt->bind_param("ss", $nama_poli, $keterangan);
            if ($stmt->execute()) {
                $_SESSION['admin_success'] = 'Poli berhasil ditambahkan.';
            } else {
                $_SESSION['admin_error'] = 'Gagal menambahkan poli.';
            }
            $stmt->close();
        }
        redirect('kelola_poli.php');
    }
    
    if ($action === 'edit') {
        $id_poli = (int) $_POST['id_poli'];
        $nama_poli = trim($_POST['nama_poli'] ?? '');
        $keterangan = trim($_POST['keterangan'] ?? '');
        
        if (!empty($nama_poli) && $id_poli > 0) {
            $stmt = $conn->prepare("UPDATE poli SET nama_poli = ?, keterangan = ? WHERE id_poli = ?");
            $stmt->bind_param("ssi", $nama_poli, $keterangan, $id_poli);
            if ($stmt->execute()) {
                $_SESSION['admin_success'] = 'Poli berhasil diperbarui.';
            } else {
                $_SESSION['admin_error'] = 'Gagal memperbarui poli.';
            }
            $stmt->close();
        }
        redirect('kelola_poli.php');
    }
}

// Cek mode edit
$edit_poli = null;
if (isset($_GET['edit'])) {
    $edit_id = (int) $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM poli WHERE id_poli = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_poli = $result->fetch_assoc();
    $stmt->close();
}

// Ambil semua poli
$poli_list = $conn->query("SELECT p.*, (SELECT COUNT(*) FROM antrian a WHERE a.id_poli = p.id_poli AND a.tanggal_berobat = CURDATE()) as antrian_hari_ini FROM poli p ORDER BY p.id_poli ASC");

$success = $_SESSION['admin_success'] ?? '';
$error = $_SESSION['admin_error'] ?? '';
unset($_SESSION['admin_success'], $_SESSION['admin_error']);

include '../includes/header.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-8 animate-fade-in">
        <div>
            <h1 class="font-display font-semibold text-2xl sm:text-3xl">Kelola <span class="text-primary">Poli</span></h1>
            <p class="text-text-secondary text-sm font-mono mt-1">Tambah, edit, dan hapus data poli klinik</p>
        </div>
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form Tambah/Edit Poli -->
        <div class="lg:col-span-1">
            <div class="glass-card rounded-xl p-6 animate-fade-in animate-delay-1">
                <h2 class="font-display font-semibold text-lg mb-4">
                    <?= $edit_poli ? 'Edit Poli' : 'Tambah Poli Baru' ?>
                </h2>
                
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="<?= $edit_poli ? 'edit' : 'tambah' ?>">
                    <?php if($edit_poli): ?>
                    <input type="hidden" name="id_poli" value="<?= $edit_poli['id_poli'] ?>">
                    <?php endif; ?>
                    
                    <div>
                        <label class="block text-sm text-text-secondary mb-2 font-mono" for="nama_poli">Nama Poli</label>
                        <input type="text" id="nama_poli" name="nama_poli" required
                            value="<?= $edit_poli ? e($edit_poli['nama_poli']) : '' ?>"
                            class="input-field w-full px-4 py-3 rounded-lg text-white text-sm font-mono placeholder-text-secondary/50"
                            placeholder="Contoh: Poli Umum">
                    </div>
                    
                    <div>
                        <label class="block text-sm text-text-secondary mb-2 font-mono" for="keterangan">Keterangan</label>
                        <textarea id="keterangan" name="keterangan" rows="4"
                            class="input-field w-full px-4 py-3 rounded-lg text-white text-sm font-mono placeholder-text-secondary/50 resize-none"
                            placeholder="Deskripsi layanan poli..."><?= $edit_poli ? e($edit_poli['keterangan']) : '' ?></textarea>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button type="submit" class="btn-primary flex-1 py-2.5 rounded-lg text-white font-semibold text-sm font-mono">
                            <?= $edit_poli ? 'Simpan Perubahan' : 'Tambah Poli' ?>
                        </button>
                        <?php if($edit_poli): ?>
                        <a href="kelola_poli.php" class="btn-outline px-4 py-2.5 rounded-lg text-text-secondary text-sm font-mono text-center">Batal</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Daftar Poli -->
        <div class="lg:col-span-2">
            <div class="glass-card rounded-xl overflow-hidden animate-fade-in animate-delay-2">
                <div class="px-6 py-4 border-b border-border/50">
                    <h2 class="font-display font-semibold text-lg">Daftar Poli</h2>
                </div>
                
                <?php if (!empty($poli_list) && $poli_list->num_rows > 0): ?>
                <div class="divide-y divide-border/20">
                    <?php while($poli = $poli_list->fetch_assoc()): ?>
                    <div class="table-row px-6 py-4 flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg bg-primary/10 flex items-center justify-center border border-primary/20 flex-shrink-0">
                                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-sm text-white"><?= e($poli['nama_poli']) ?></h3>
                                    <p class="text-xs text-text-secondary font-mono mt-0.5 line-clamp-1"><?= e($poli['keterangan']) ?></p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="flex items-center space-x-3 ml-4">
                            <span class="text-xs text-text-secondary font-mono hidden sm:inline"><?= $poli['antrian_hari_ini'] ?> antrian hari ini</span>
                            <a href="kelola_poli.php?edit=<?= $poli['id_poli'] ?>" class="px-3 py-1.5 rounded-lg text-xs font-mono bg-surface text-text-secondary border border-border hover:border-primary hover:text-primary transition-colors">
                                Edit
                            </a>
                            <form method="POST" action="../admin/proses_delete.php" class="inline" onsubmit="return confirm('Yakin ingin menghapus poli ini? Semua antrian terkait juga akan dihapus.')">
                                <input type="hidden" name="type" value="poli">
                                <input type="hidden" name="id" value="<?= $poli['id_poli'] ?>">
                                <input type="hidden" name="redirect" value="kelola_poli.php">
                                <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-mono bg-accent/10 text-accent border border-accent/20 hover:bg-accent/20 transition-colors">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                    <?php endwhile; ?>
                </div>
                <?php else: ?>
                <div class="px-6 py-16 text-center">
                    <svg class="w-12 h-12 text-text-secondary/30 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    <p class="text-text-secondary text-sm font-mono">Belum ada data poli.</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
<?php
require_once '../config/database.php';

// Jika sudah login, redirect
if (isset($_SESSION['id_user'])) {
    if ($_SESSION['role'] === 'admin') {
        redirect('../admin/dashboard.php');
    } else {
        redirect('../pasien/dashboard.php');
    }
}

$error = $_SESSION['register_error'] ?? '';
unset($_SESSION['register_error']);

$pageTitle = 'Daftar';
include '../includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center px-4 py-12">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8 animate-fade-in">
            <a href="../index.php" class="inline-flex items-center space-x-3">
                <img src="../image/logo_sapaklinik.png" alt="SapaKlinik" width="40" height="40" class="rounded-xl object-cover" style="box-shadow: 0 0 30px rgba(16, 185, 129, 0.3);">
                <span class="font-display font-bold text-2xl text-white">Sapa<span class="text-primary">Klinik</span></span>
            </a>
        </div>
        
        <!-- Register Card -->
        <div class="glass-card rounded-card p-8 animate-fade-in animate-delay-1">
            <div class="mb-6">
                <h1 class="font-display font-semibold text-2xl mb-2">Buat Akun Baru</h1>
                <p class="text-text-secondary text-sm font-mono">Daftar untuk mulai booking antrian klinik</p>
            </div>
            
            <?php if($error): ?>
            <div class="alert alert-error rounded-lg px-4 py-3 mb-6 text-sm font-mono flex items-center space-x-2">
                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span><?= e($error) ?></span>
            </div>
            <?php endif; ?>
            
            <form action="../auth/proses_login.php" method="POST" class="space-y-5">
                <input type="hidden" name="action" value="register">
                
                <div>
                    <label class="block text-sm text-text-secondary mb-2 font-mono" for="nama_lengkap">Nama Lengkap</label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required
                        class="input-field w-full px-4 py-3 rounded-lg text-white text-sm font-mono placeholder-text-secondary/50"
                        placeholder="Masukkan nama lengkap">
                </div>
                
                <div>
                    <label class="block text-sm text-text-secondary mb-2 font-mono" for="email">Email</label>
                    <input type="email" id="email" name="email" required
                        class="input-field w-full px-4 py-3 rounded-lg text-white text-sm font-mono placeholder-text-secondary/50"
                        placeholder="nama@email.com">
                </div>
                
                <div>
                    <label class="block text-sm text-text-secondary mb-2 font-mono" for="password">Password</label>
                    <div class="relative">
                        <input type="password" id="password" name="password" required minlength="6"
                            class="input-field w-full px-4 py-3 rounded-lg text-white text-sm font-mono placeholder-text-secondary/50 pr-12"
                            placeholder="Minimal 6 karakter">
                        <button type="button" onclick="togglePassword('password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-text-secondary hover:text-primary transition-colors">
                            <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm text-text-secondary mb-2 font-mono" for="confirm_password">Konfirmasi Password</label>
                    <div class="relative">
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="6"
                            class="input-field w-full px-4 py-3 rounded-lg text-white text-sm font-mono placeholder-text-secondary/50 pr-12"
                            placeholder="Ulangi password">
                        <button type="button" onclick="togglePassword('confirm_password', this)" class="absolute right-3 top-1/2 -translate-y-1/2 text-text-secondary hover:text-primary transition-colors">
                            <svg class="w-5 h-5 eye-open" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="w-5 h-5 eye-closed hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                        </button>
                    </div>
                </div>
                
                <button type="submit" class="btn-primary w-full py-3 rounded-lg text-white font-semibold text-sm font-mono">
                    Daftar Sekarang
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-text-secondary text-sm font-mono">
                    Sudah punya akun? 
                    <a href="login.php" class="text-primary hover:underline font-semibold">Masuk disini</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const openEye = btn.querySelector('.eye-open');
    const closedEye = btn.querySelector('.eye-closed');
    if (input.type === 'password') {
        input.type = 'text';
        openEye.classList.add('hidden');
        closedEye.classList.remove('hidden');
    } else {
        input.type = 'password';
        openEye.classList.remove('hidden');
        closedEye.classList.add('hidden');
    }
}
</script>

<?php include '../includes/footer.php'; ?>

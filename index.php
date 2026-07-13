<?php
session_start();
// Jika sudah login, redirect ke dashboard sesuai role
if (isset($_SESSION['id_user'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: pasien/dashboard.php');
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SapaKlinik - Sistem Antrian Klinik Modern. Booking antrian online, cepat, mudah, dan tanpa ribet.">
    <title>SapaKlinik — Sistem Antrian Klinik Modern</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#10B981',
                        secondary: '#18181B',
                        accent: '#EF4444',
                        dark: '#030303',
                        surface: '#18181B',
                        'text-secondary': '#A1A1AA',
                        border: '#27272A',
                    },
                    fontFamily: {
                        display: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                }
            }
        }
    </script>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'JetBrains Mono', monospace;
            background-color: #030303;
            color: #FFFFFF;
            overflow-x: hidden;
        }
        #bg-canvas {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 0;
            pointer-events: none;
        }
        .glass-card {
            background: rgba(24, 24, 27, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(39, 39, 42, 0.8);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            border-color: rgba(16, 185, 129, 0.4);
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(16, 185, 129, 0.12);
        }
        .btn-primary {
            background: linear-gradient(135deg, #10B981, #059669);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(16, 185, 129, 0.4);
        }
        .btn-outline {
            border: 1px solid #27272A;
            transition: all 0.3s ease;
        }
        .btn-outline:hover {
            border-color: #10B981;
            background: rgba(16, 185, 129, 0.1);
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeInUp 0.8s ease-out forwards; }
        .anim-d1 { animation-delay: 0.1s; opacity: 0; }
        .anim-d2 { animation-delay: 0.2s; opacity: 0; }
        .anim-d3 { animation-delay: 0.3s; opacity: 0; }
        .anim-d4 { animation-delay: 0.4s; opacity: 0; }
        .anim-d5 { animation-delay: 0.5s; opacity: 0; }
        .anim-d6 { animation-delay: 0.6s; opacity: 0; }
        .anim-d7 { animation-delay: 0.7s; opacity: 0; }
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .animate-float { animation: float 4s ease-in-out infinite; }
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(16, 185, 129, 0.2); }
            50% { box-shadow: 0 0 40px rgba(16, 185, 129, 0.4); }
        }
        .animate-pulse-glow { animation: pulse-glow 3s ease-in-out infinite; }
        @keyframes counter {
            from { opacity: 0; transform: scale(0.5); }
            to { opacity: 1; transform: scale(1); }
        }
        .animate-counter { animation: counter 0.5s ease-out forwards; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #030303; }
        ::-webkit-scrollbar-thumb { background: #27272A; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #10B981; }
    </style>
</head>
<body class="min-h-screen">
    <canvas id="bg-canvas"></canvas>
    
    <script>
        (function() {
            const canvas = document.getElementById('bg-canvas');
            const ctx = canvas.getContext('2d');
            let particles = [];
            let mouse = { x: 0, y: 0 };
            
            function resize() {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            }
            
            function createParticles() {
                particles = [];
                const count = Math.floor((canvas.width * canvas.height) / 12000);
                for (let i = 0; i < count; i++) {
                    particles.push({
                        x: Math.random() * canvas.width,
                        y: Math.random() * canvas.height,
                        vx: (Math.random() - 0.5) * 0.4,
                        vy: (Math.random() - 0.5) * 0.4,
                        radius: Math.random() * 2 + 0.5,
                        opacity: Math.random() * 0.5 + 0.1,
                        color: Math.random() > 0.6 ? '#10B981' : '#A1A1AA'
                    });
                }
            }
            
            function draw() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                // Radial gradient accents
                const g1 = ctx.createRadialGradient(canvas.width * 0.2, canvas.height * 0.3, 0, canvas.width * 0.2, canvas.height * 0.3, canvas.width * 0.5);
                g1.addColorStop(0, 'rgba(16, 185, 129, 0.04)');
                g1.addColorStop(1, 'transparent');
                ctx.fillStyle = g1;
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                
                const g2 = ctx.createRadialGradient(canvas.width * 0.8, canvas.height * 0.7, 0, canvas.width * 0.8, canvas.height * 0.7, canvas.width * 0.4);
                g2.addColorStop(0, 'rgba(239, 68, 68, 0.02)');
                g2.addColorStop(1, 'transparent');
                ctx.fillStyle = g2;
                ctx.fillRect(0, 0, canvas.width, canvas.height);
                
                particles.forEach((p, i) => {
                    p.x += p.vx;
                    p.y += p.vy;
                    
                    if (p.x < 0) p.x = canvas.width;
                    if (p.x > canvas.width) p.x = 0;
                    if (p.y < 0) p.y = canvas.height;
                    if (p.y > canvas.height) p.y = 0;
                    
                    ctx.beginPath();
                    ctx.arc(p.x, p.y, p.radius, 0, Math.PI * 2);
                    ctx.fillStyle = p.color;
                    ctx.globalAlpha = p.opacity;
                    ctx.fill();
                    
                    for (let j = i + 1; j < particles.length; j++) {
                        const dx = particles[j].x - p.x;
                        const dy = particles[j].y - p.y;
                        const dist = Math.sqrt(dx * dx + dy * dy);
                        
                        if (dist < 130) {
                            ctx.beginPath();
                            ctx.moveTo(p.x, p.y);
                            ctx.lineTo(particles[j].x, particles[j].y);
                            ctx.strokeStyle = '#10B981';
                            ctx.globalAlpha = (1 - dist / 130) * 0.1;
                            ctx.lineWidth = 0.5;
                            ctx.stroke();
                        }
                    }
                });
                
                ctx.globalAlpha = 1;
                requestAnimationFrame(draw);
            }
            
            resize();
            createParticles();
            draw();
            window.addEventListener('resize', () => { resize(); createParticles(); });
        })();
    </script>

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50" style="background: rgba(3, 3, 3, 0.8); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(39, 39, 42, 0.5);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="index.php" class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary to-emerald-700 flex items-center justify-center animate-pulse-glow">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <span class="font-display font-bold text-xl text-white">Sapa<span class="text-primary">Klinik</span></span>
                </a>
                
                <div class="flex items-center space-x-3">
                    <a href="auth/login.php" class="btn-outline px-5 py-2 rounded-lg text-sm text-text-secondary hover:text-white font-mono">Masuk</a>
                    <a href="auth/register.php" class="btn-primary px-5 py-2 rounded-lg text-sm text-white font-semibold font-mono">Daftar</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative z-10 min-h-screen flex items-center justify-center px-4">
        <div class="max-w-5xl mx-auto text-center">
            <!-- Badge -->
            <div class="animate-fade-in anim-d1 inline-flex items-center space-x-2 px-4 py-2 rounded-full border border-border/60 bg-surface/40 backdrop-blur-sm mb-8">
                <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                <span class="text-xs text-text-secondary font-mono">Sistem Antrian Digital</span>
            </div>
            
            <!-- Main Heading -->
            <h1 class="animate-fade-in anim-d2 font-display font-medium text-5xl sm:text-6xl lg:text-7xl leading-[1.04] mb-6 tracking-tight">
                Antrian Klinik.
                <br>
                <span class="text-primary" style="text-shadow: 0 0 60px rgba(16, 185, 129, 0.3);">Tanpa Ribet.</span>
            </h1>
            
            <!-- Subheading -->
            <p class="animate-fade-in anim-d3 text-text-secondary text-base sm:text-lg max-w-2xl mx-auto mb-10 leading-relaxed font-mono">
                Booking antrian online. Pantau status real-time.
                <br class="hidden sm:block">
                Cetak tiket instan. Semua dalam satu platform.
            </p>
            
            <!-- CTA Buttons -->
            <div class="animate-fade-in anim-d4 flex flex-col sm:flex-row items-center justify-center gap-4 mb-16">
                <a href="auth/register.php" class="btn-primary w-full sm:w-auto px-8 py-3.5 rounded-lg text-white font-semibold text-sm font-mono inline-flex items-center justify-center space-x-2">
                    <span>Mulai Sekarang</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
                <a href="auth/login.php" class="btn-outline w-full sm:w-auto px-8 py-3.5 rounded-lg text-text-secondary hover:text-white text-sm font-mono inline-flex items-center justify-center space-x-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    <span>Sudah Punya Akun</span>
                </a>
            </div>
            
            <!-- Stats -->
            <div class="animate-fade-in anim-d5 grid grid-cols-3 gap-4 max-w-lg mx-auto">
                <div class="text-center">
                    <div class="font-display font-bold text-2xl sm:text-3xl text-white">4+</div>
                    <div class="text-xs text-text-secondary mt-1 font-mono">Poli Tersedia</div>
                </div>
                <div class="text-center border-x border-border/40">
                    <div class="font-display font-bold text-2xl sm:text-3xl text-primary">24/7</div>
                    <div class="text-xs text-text-secondary mt-1 font-mono">Online Booking</div>
                </div>
                <div class="text-center">
                    <div class="font-display font-bold text-2xl sm:text-3xl text-white">&lt;1<span class="text-lg">min</span></div>
                    <div class="text-xs text-text-secondary mt-1 font-mono">Proses Cepat</div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-bounce">
            <svg class="w-5 h-5 text-text-secondary/40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
            </svg>
        </div>
    </section>

    <!-- Features Section -->
    <section class="relative z-10 py-24 px-4">
        <div class="max-w-6xl mx-auto">
            <!-- Section Header -->
            <div class="text-center mb-16">
                <span class="inline-block text-xs text-primary font-mono mb-4 tracking-widest uppercase">Fitur Unggulan</span>
                <h2 class="font-display font-medium text-3xl sm:text-4xl">Kenapa <span class="text-primary">SapaKlinik</span>?</h2>
            </div>
            
            <!-- Feature Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Card 1 -->
                <div class="glass-card rounded-xl p-8 group">
                    <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center mb-6 group-hover:bg-primary/20 transition-colors">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="font-display font-semibold text-lg mb-3">Booking Instan</h3>
                    <p class="text-text-secondary text-sm leading-relaxed font-mono">Pilih poli dan tanggal, dapatkan nomor antrian otomatis. Tidak perlu datang pagi-pagi untuk mengantri.</p>
                </div>
                
                <!-- Card 2 -->
                <div class="glass-card rounded-xl p-8 group">
                    <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center mb-6 group-hover:bg-primary/20 transition-colors">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                    </div>
                    <h3 class="font-display font-semibold text-lg mb-3">Status Real-time</h3>
                    <p class="text-text-secondary text-sm leading-relaxed font-mono">Pantau status antrian langsung: Menunggu, Diproses, atau Selesai. Transparan dan informatif.</p>
                </div>
                
                <!-- Card 3 -->
                <div class="glass-card rounded-xl p-8 group">
                    <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center mb-6 group-hover:bg-primary/20 transition-colors">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                        </svg>
                    </div>
                    <h3 class="font-display font-semibold text-lg mb-3">Cetak Tiket PDF</h3>
                    <p class="text-text-secondary text-sm leading-relaxed font-mono">Unduh tiket antrian dalam format PDF. Berisi nomor antrian, poli, tanggal, dan data pasien lengkap.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="relative z-10 py-24 px-4">
        <div class="max-w-4xl mx-auto">
            <div class="text-center mb-16">
                <span class="inline-block text-xs text-primary font-mono mb-4 tracking-widest uppercase">Alur Mudah</span>
                <h2 class="font-display font-medium text-3xl sm:text-4xl">Cara Kerja</h2>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="text-center">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mx-auto mb-4 border border-primary/20">
                        <span class="font-display font-bold text-primary text-lg">01</span>
                    </div>
                    <h4 class="font-display font-semibold text-sm mb-2">Daftar Akun</h4>
                    <p class="text-text-secondary text-xs font-mono">Buat akun dengan email dan password</p>
                </div>
                
                <div class="text-center">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mx-auto mb-4 border border-primary/20">
                        <span class="font-display font-bold text-primary text-lg">02</span>
                    </div>
                    <h4 class="font-display font-semibold text-sm mb-2">Pilih Poli</h4>
                    <p class="text-text-secondary text-xs font-mono">Pilih poli dan tanggal berobat</p>
                </div>
                
                <div class="text-center">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mx-auto mb-4 border border-primary/20">
                        <span class="font-display font-bold text-primary text-lg">03</span>
                    </div>
                    <h4 class="font-display font-semibold text-sm mb-2">Dapat Nomor</h4>
                    <p class="text-text-secondary text-xs font-mono">Nomor antrian otomatis ter-generate</p>
                </div>
                
                <div class="text-center">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center mx-auto mb-4 border border-primary/20">
                        <span class="font-display font-bold text-primary text-lg">04</span>
                    </div>
                    <h4 class="font-display font-semibold text-sm mb-2">Cetak Tiket</h4>
                    <p class="text-text-secondary text-xs font-mono">Download tiket PDF lalu datang ke klinik</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="relative z-10 py-24 px-4">
        <div class="max-w-3xl mx-auto">
            <div class="glass-card rounded-2xl p-10 sm:p-14 text-center" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08), rgba(24, 24, 27, 0.6));">
                <h2 class="font-display font-medium text-3xl sm:text-4xl mb-4">Siap Booking Antrian?</h2>
                <p class="text-text-secondary text-sm font-mono mb-8 max-w-md mx-auto">Daftar sekarang dan nikmati kemudahan sistem antrian klinik modern. Gratis, cepat, tanpa antri panjang.</p>
                <a href="auth/register.php" class="btn-primary inline-flex items-center space-x-2 px-8 py-3.5 rounded-lg text-white font-semibold text-sm font-mono">
                    <span>Daftar Gratis</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/></svg>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="relative z-10 border-t border-border/30 py-8 px-4">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-6 h-6 rounded-md bg-gradient-to-br from-primary to-emerald-700 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <span class="font-display font-semibold text-sm text-text-secondary">Sapa<span class="text-primary">Klinik</span></span>
            </div>
            <p class="text-xs text-text-secondary/60 font-mono">&copy; <?= date('Y') ?> SapaKlinik. Sistem Antrian Klinik Modern.</p>
        </div>
    </footer>
</body>
</html>

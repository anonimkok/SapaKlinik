<?php if(!isset($pageTitle)) $pageTitle = 'SapaKlinik'; ?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SapaKlinik - Sistem Antrian Klinik Modern. Booking antrian online, cepat dan mudah.">
    <title><?= e($pageTitle) ?> | SapaKlinik</title>
    
    <!-- Tailwind CSS CDN -->
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
                    borderRadius: {
                        'card': '8px',
                        'control': '8px',
                        'pill': '9999px',
                    },
                }
            }
        }
    </script>
    
    <!-- Google Fonts -->
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
        
        /* Animated Background */
        #bg-canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }
        
        /* Glassmorphism Card */
        .glass-card {
            background: rgba(24, 24, 27, 0.6);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(39, 39, 42, 0.8);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            border-color: rgba(16, 185, 129, 0.4);
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(16, 185, 129, 0.1);
        }
        
        /* Glow Effects */
        .glow-primary {
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.3), 0 0 60px rgba(16, 185, 129, 0.1);
        }
        .glow-text {
            text-shadow: 0 0 40px rgba(16, 185, 129, 0.3);
        }
        
        /* Animated Gradient Border */
        .gradient-border {
            position: relative;
        }
        .gradient-border::before {
            content: '';
            position: absolute;
            inset: -1px;
            border-radius: inherit;
            padding: 1px;
            background: linear-gradient(135deg, #10B981, transparent 40%, transparent 60%, #10B981);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.4s ease;
        }
        .gradient-border:hover::before {
            opacity: 1;
        }
        
        /* Button Animations */
        .btn-primary {
            background: linear-gradient(135deg, #10B981, #059669);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 24px rgba(16, 185, 129, 0.4);
        }
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .btn-outline {
            border: 1px solid #27272A;
            transition: all 0.3s ease;
        }
        .btn-outline:hover {
            border-color: #10B981;
            background: rgba(16, 185, 129, 0.1);
        }
        
        /* Status Badges */
        .badge-menunggu {
            background: rgba(234, 179, 8, 0.15);
            color: #FBBF24;
            border: 1px solid rgba(234, 179, 8, 0.3);
        }
        .badge-diproses {
            background: rgba(59, 130, 246, 0.15);
            color: #60A5FA;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
        .badge-selesai {
            background: rgba(16, 185, 129, 0.15);
            color: #10B981;
            border: 1px solid rgba(16, 185, 129, 0.3);
        }
        .badge-batal {
            background: rgba(239, 68, 68, 0.15);
            color: #EF4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        /* Fade In Animation */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeInUp 0.6s ease-out forwards;
        }
        .animate-delay-1 { animation-delay: 0.1s; opacity: 0; }
        .animate-delay-2 { animation-delay: 0.2s; opacity: 0; }
        .animate-delay-3 { animation-delay: 0.3s; opacity: 0; }
        .animate-delay-4 { animation-delay: 0.4s; opacity: 0; }
        .animate-delay-5 { animation-delay: 0.5s; opacity: 0; }
        
        /* Pulse Ring */
        @keyframes pulseRing {
            0% { transform: scale(0.8); opacity: 0.5; }
            50% { transform: scale(1); opacity: 0.2; }
            100% { transform: scale(0.8); opacity: 0.5; }
        }
        .pulse-ring {
            animation: pulseRing 3s ease-in-out infinite;
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #030303; }
        ::-webkit-scrollbar-thumb { background: #27272A; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #10B981; }
        
        /* Table Styles */
        .table-row {
            transition: all 0.2s ease;
        }
        .table-row:hover {
            background: rgba(16, 185, 129, 0.05);
        }
        
        /* Input Styles */
        .input-field {
            background: rgba(24, 24, 27, 0.8);
            border: 1px solid #27272A;
            transition: all 0.3s ease;
        }
        .input-field:focus {
            border-color: #10B981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
            outline: none;
        }
        
        /* Navigation */
        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #10B981;
            transition: width 0.3s ease;
        }
        .nav-link:hover::after {
            width: 100%;
        }
        .nav-link.active {
            color: #10B981;
        }
        .nav-link.active::after {
            width: 100%;
        }
        
        /* Floating particles */
        .particle {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }
        
        /* Alert Messages */
        .alert {
            animation: fadeInUp 0.4s ease-out;
        }
        .alert-success {
            background: rgba(16, 185, 129, 0.15);
            border: 1px solid rgba(16, 185, 129, 0.3);
            color: #10B981;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #EF4444;
        }
        .alert-warning {
            background: rgba(234, 179, 8, 0.15);
            border: 1px solid rgba(234, 179, 8, 0.3);
            color: #FBBF24;
        }
    </style>
</head>
<body class="min-h-screen bg-dark text-white font-mono">
    <!-- Animated Background Canvas -->
    <canvas id="bg-canvas"></canvas>
    
    <!-- Background Canvas Script -->
    <script>
        (function() {
            const canvas = document.getElementById('bg-canvas');
            const ctx = canvas.getContext('2d');
            let particles = [];
            let animationId;
            
            function resize() {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            }
            
            function createParticles() {
                particles = [];
                const count = Math.floor((canvas.width * canvas.height) / 15000);
                for (let i = 0; i < count; i++) {
                    particles.push({
                        x: Math.random() * canvas.width,
                        y: Math.random() * canvas.height,
                        vx: (Math.random() - 0.5) * 0.3,
                        vy: (Math.random() - 0.5) * 0.3,
                        radius: Math.random() * 1.5 + 0.5,
                        opacity: Math.random() * 0.5 + 0.1,
                        color: Math.random() > 0.7 ? '#10B981' : '#A1A1AA'
                    });
                }
            }
            
            function drawParticles() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                
                // Draw gradient background overlay
                const gradient = ctx.createRadialGradient(
                    canvas.width * 0.3, canvas.height * 0.3, 0,
                    canvas.width * 0.3, canvas.height * 0.3, canvas.width * 0.6
                );
                gradient.addColorStop(0, 'rgba(16, 185, 129, 0.03)');
                gradient.addColorStop(1, 'transparent');
                ctx.fillStyle = gradient;
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
                    
                    // Draw connections
                    for (let j = i + 1; j < particles.length; j++) {
                        const dx = particles[j].x - p.x;
                        const dy = particles[j].y - p.y;
                        const dist = Math.sqrt(dx * dx + dy * dy);
                        
                        if (dist < 120) {
                            ctx.beginPath();
                            ctx.moveTo(p.x, p.y);
                            ctx.lineTo(particles[j].x, particles[j].y);
                            ctx.strokeStyle = '#10B981';
                            ctx.globalAlpha = (1 - dist / 120) * 0.08;
                            ctx.lineWidth = 0.5;
                            ctx.stroke();
                        }
                    }
                });
                
                ctx.globalAlpha = 1;
                animationId = requestAnimationFrame(drawParticles);
            }
            
            resize();
            createParticles();
            drawParticles();
            
            window.addEventListener('resize', () => {
                resize();
                createParticles();
            });
        })();
    </script>

    <!-- Navigation -->
    <?php if(isset($_SESSION['id_user'])): ?>
    <nav class="fixed top-0 left-0 right-0 z-50 glass-card border-t-0 border-l-0 border-r-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="<?= $_SESSION['role'] === 'admin' ? '../admin/dashboard.php' : '../pasien/dashboard.php' ?>" class="flex items-center space-x-3">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary to-emerald-700 flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <span class="font-display font-bold text-lg text-white">Sapa<span class="text-primary">Klinik</span></span>
                </a>
                
                <!-- Nav Links -->
                <div class="hidden md:flex items-center space-x-6">
                    <?php if($_SESSION['role'] === 'admin'): ?>
                        <a href="../admin/dashboard.php" class="nav-link text-sm text-text-secondary hover:text-white <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php' && strpos($_SERVER['PHP_SELF'], 'admin') !== false) ? 'active' : '' ?>">Dashboard</a>
                        <a href="../admin/kelola_poli.php" class="nav-link text-sm text-text-secondary hover:text-white <?= basename($_SERVER['PHP_SELF']) == 'kelola_poli.php' ? 'active' : '' ?>">Kelola Poli</a>
                    <?php else: ?>
                        <a href="../pasien/dashboard.php" class="nav-link text-sm text-text-secondary hover:text-white <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
                    <?php endif; ?>
                </div>
                
                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <div class="hidden sm:flex items-center space-x-2">
                        <div class="w-8 h-8 rounded-full bg-surface border border-border flex items-center justify-center">
                            <span class="text-xs font-bold text-primary"><?= strtoupper(substr($_SESSION['nama_lengkap'], 0, 1)) ?></span>
                        </div>
                        <div class="text-sm">
                            <span class="text-text-secondary"><?= e($_SESSION['nama_lengkap']) ?></span>
                            <span class="inline-block ml-1 px-2 py-0.5 text-[10px] rounded-pill <?= $_SESSION['role'] === 'admin' ? 'bg-accent/20 text-accent' : 'bg-primary/20 text-primary' ?>"><?= ucfirst($_SESSION['role']) ?></span>
                        </div>
                    </div>
                    <a href="../auth/logout.php" class="text-sm text-text-secondary hover:text-accent transition-colors" title="Logout">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                    </a>
                    
                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" class="md:hidden text-text-secondary hover:text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden pb-4">
                <?php if($_SESSION['role'] === 'admin'): ?>
                    <a href="../admin/dashboard.php" class="block py-2 text-sm text-text-secondary hover:text-primary">Dashboard</a>
                    <a href="../admin/kelola_poli.php" class="block py-2 text-sm text-text-secondary hover:text-primary">Kelola Poli</a>
                <?php else: ?>
                    <a href="../pasien/dashboard.php" class="block py-2 text-sm text-text-secondary hover:text-primary">Dashboard</a>
                <?php endif; ?>
                <a href="../auth/logout.php" class="block py-2 text-sm text-text-secondary hover:text-accent">Logout</a>
            </div>
        </div>
    </nav>
    <script>
        document.getElementById('mobile-menu-btn')?.addEventListener('click', function() {
            document.getElementById('mobile-menu').classList.toggle('hidden');
        });
    </script>
    <?php endif; ?>
    
    <!-- Main Content Wrapper -->
    <main class="relative z-10 <?= isset($_SESSION['id_user']) ? 'pt-20' : '' ?>">

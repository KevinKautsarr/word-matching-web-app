<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'LEXORA – Platform Pembelajaran Kosakata Bahasa Inggris Interaktif')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LEXORA') – Pembelajaran Kosakata</title>

    <!-- Google Fonts: Syne + DM Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
    
    <!-- Canvas Confetti -->
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        dark:   'var(--bg)',
                        dark2:  'var(--card)',
                        dark3:  '#121530',
                        primary: '#4F7CFF',
                        primaryLight: '#6AA8FF',
                        secondary: '#6C63FF',
                        success: '#06D6A0',
                        warning: '#FFD166',
                        error: '#FF6363',
                        themeText: 'var(--text)',
                        themeMuted: 'var(--text-muted)',
                        themeBorder: 'var(--border)'
                    },
                    fontFamily: {
                        syne: ['Syne', 'sans-serif'],
                        dm:   ['DM Sans', 'sans-serif'],
                    },
                    borderRadius: {
                        'xl': '16px',
                        '2xl': '20px',
                    }
                }
            }
        }
    </script>

    <style>
        :root {
            --bg: #0F1220;
            --card: rgba(18, 21, 48, 0.7);
            --nav-bg: rgba(13, 15, 32, 0.8);
            --border: rgba(255, 255, 255, 0.08);
            --text: #F1F5F9;
            --text-muted: #94A3B8;
            --primary: #4F7CFF;
            --primary-light: #6AA8FF;
            --secondary: #6C63FF;
            --green: #06D6A0;
            --gold: #FFD166;
            --red: #FF6363;
        }

        * { transition: all 0.18s ease-out; font-style: normal; }

        body { 
            font-family: 'DM Sans', sans-serif; 
            font-style: normal;
            background: radial-gradient(circle at center, rgba(15,18,32,0) 50%, rgba(15,18,32,0.8) 100%), #0f1220;
            color: var(--text); 
            position: relative;
        }

        body::before {
            content: ''; position: fixed; inset: 0; z-index: -2; pointer-events: none;
            background: 
                radial-gradient(circle at 10% 20%, rgba(108,99,255,0.12), transparent 50%),
                radial-gradient(circle at 90% 80%, rgba(0,255,180,0.06), transparent 60%);
        }

        body::after {
            content: ''; position: fixed; inset: 0; z-index: -1; pointer-events: none; opacity: 0.02; mix-blend-mode: overlay;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }

        .main-container { position: relative; z-index: 2; }
        .main-container::before {
            content: ''; position: absolute; inset: 0; z-index: -1;
            background: rgba(0,0,0,0.15); backdrop-filter: blur(50px);
            border-radius: 24px; box-shadow: 0 0 100px rgba(108,99,255,0.05);
        }

        h1,h2,h3,h4,h5,h6,.font-syne { font-family: 'Syne', sans-serif; }

        .glass { background: var(--card); backdrop-filter: blur(16px); border: 1px solid var(--border); }
        .glass-hover:hover { filter: brightness(1.1); border-color: rgba(108,99,255,0.4); }

        .card {
            background: rgba(255,255,255,0.03); backdrop-filter: blur(16px); border: 1px solid var(--border);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15); position: relative; overflow: hidden; border-radius: 1.25rem;
            background-image: linear-gradient(135deg, rgba(255,255,255,0.02) 0%, transparent 100%);
        }
        .card:hover { transform: translateY(-4px) scale(1.02); box-shadow: 0 12px 40px rgba(79, 124, 255, 0.15); border-color: rgba(79, 124, 255, 0.3); }
        .card:active { transform: scale(0.96); }

        .gradient-text { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .btn-primary { background: linear-gradient(135deg, var(--primary), var(--primary-light)); color: white; transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1); }
        .btn-primary:hover { box-shadow: 0 0 24px rgba(79, 124, 255, 0.5); transform: translateY(-2px); }
        .btn-primary:active, .pressable:active { transform: scale(0.95) !important; box-shadow: none !important; }

        .glow-primary { box-shadow: 0 0 24px rgba(79, 124, 255, 0.35); }
        .glow-green  { box-shadow: 0 0 24px rgba(6, 214, 160, 0.35); }
        .glow-gold   { box-shadow: 0 0 24px rgba(255, 209, 102, 0.35); }
        
        .animate-in { animation: fadeSlideUp 0.4s ease forwards; }
        .page-transition { animation: pageFadeIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        @keyframes fadeSlideUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        @keyframes pageFadeIn { 0% { opacity: 0; transform: translateY(15px); } 100% { opacity: 1; transform: translateY(0); } }

        /* Sidebar Styles */
        .sidebar { background: linear-gradient(180deg, var(--nav-bg) 0%, rgba(13,15,26,0.85) 100%); border-right: 1px solid var(--border); backdrop-filter: blur(24px); box-shadow: 4px 0 30px rgba(0,0,0,0.2); }
        .sidebar-item { display: flex; align-items: center; gap: 14px; padding: 12px 18px; border-radius: 14px; color: var(--text-muted); font-weight: 600; }
        .sidebar-item .menu-icon { opacity: 0.6; transition: all 0.25s ease; }
        .sidebar-item:hover { color: var(--text); background: rgba(255,255,255,0.04); transform: scale(1.02) translateX(4px); }
        .sidebar-item:hover .menu-icon { opacity: 1; transform: scale(1.1); filter: drop-shadow(0 0 8px rgba(79, 124, 255, 0.4)); }
        .sidebar-item.active { color: var(--primary); background: linear-gradient(90deg, rgba(79, 124, 255, 0.1) 0%, transparent 100%); }
        .sidebar-item.active .menu-icon { opacity: 1; }
        .sidebar-item.active::before { content: ''; position: absolute; left: 0; height: 40%; width: 4px; background: var(--primary); border-radius: 0 4px 4px 0; box-shadow: 0 0 12px var(--primary); }

        .badge-glass {
            background: linear-gradient(135deg, rgba(19,22,42,0.4) 0%, rgba(19,22,42,0.2) 100%); backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.08); box-shadow: 0 4px 12px rgba(0,0,0,0.1), inset 0 1px 0 rgba(255,255,255,0.03);
            padding: 8px 14px; border-radius: 999px; font-size: 0.8rem; font-weight: 700; display: flex; align-items: center; gap: 8px; cursor: default;
        }
        .badge-glass:hover { transform: translateY(-1px) scale(1.02); box-shadow: 0 4px 16px rgba(108,99,255,0.15); border-color: rgba(255,255,255,0.15); }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen text-themeText font-dm flex bg-[var(--bg)] pb-20 md:pb-0" style="overflow-x:hidden;">

    {{-- SIDEBAR --}}
    <div class="hidden md:block">
        <x-sidebar />
    </div>

    {{-- BOTTOM NAVIGATION (Mobile) --}}
    <nav x-data="{ mobileMoreOpen: false }" class="md:hidden">
        {{-- Overlay Menu --}}
        <div x-show="mobileMoreOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-full"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 translate-y-full"
             @click.away="mobileMoreOpen = false"
             class="fixed bottom-[74px] left-4 right-4 z-50 bg-[#121530]/95 backdrop-blur-2xl border border-white/10 rounded-3xl p-6 shadow-[0_-20px_50px_rgba(0,0,0,0.5)]">
            
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('profile.index') }}" class="flex items-center gap-3 p-4 bg-white/5 rounded-2xl hover:bg-white/10 transition-colors">
                    <span class="text-xl">👤</span>
                    <span class="text-sm font-bold">Profile Settings</span>
                </a>
                <a href="#" class="flex items-center gap-3 p-4 bg-white/5 rounded-2xl hover:bg-white/10 transition-colors">
                    <span class="text-xl">⚙️</span>
                    <span class="text-sm font-bold">Preferences</span>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 p-4 bg-red-500/10 rounded-2xl text-red-400 hover:bg-red-500/20 transition-colors">
                        <span class="text-xl">🚪</span>
                        <span class="text-sm font-bold">Logout</span>
                    </button>
                </form>
            </div>
            
            <div class="mt-4 pt-4 border-t border-white/5 text-center">
                <button @click="mobileMoreOpen = false" class="text-[var(--text-muted)] text-xs font-bold uppercase tracking-widest">Close Menu</button>
            </div>
        </div>

        {{-- Main Bar --}}
        <div class="fixed bottom-0 left-0 right-0 z-50 bg-[#0d0f1a]/85 backdrop-blur-xl border-t border-white/5 px-4 py-2 flex justify-between items-center shadow-[0_-10px_30px_rgba(0,0,0,0.3)]">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 p-2 {{ request()->routeIs('dashboard') ? 'text-[var(--primary)]' : 'text-[var(--text-muted)]' }}">
                <span class="text-xl">🗺️</span>
                <span class="text-[10px] font-bold uppercase tracking-tighter">Journey</span>
            </a>
            <a href="#" class="flex flex-col items-center gap-1 p-2 text-[var(--text-muted)]">
                <span class="text-xl">🎯</span>
                <span class="text-[10px] font-bold uppercase tracking-tighter">Practice</span>
            </a>

            <a href="{{ route('profile.index') }}" class="flex flex-col items-center gap-1 p-2 {{ request()->routeIs('profile.*') ? 'text-[var(--primary)]' : 'text-[var(--text-muted)]' }}">
                <span class="text-xl">👤</span>
                <span class="text-[10px] font-bold uppercase tracking-tighter">Profile</span>
            </a>
            <button @click="mobileMoreOpen = !mobileMoreOpen" class="flex flex-col items-center gap-1 p-2 transition-colors" :class="mobileMoreOpen ? 'text-[var(--primary)]' : 'text-[var(--text-muted)]'">
                <span class="text-xl" x-text="mobileMoreOpen ? '✖️' : '🔘'"></span>
                <span class="text-[10px] font-bold uppercase tracking-tighter">More</span>
            </button>
        </div>
    </nav>

    <div class="flex-1 md:ml-[240px] flex flex-col min-h-screen relative">

        <x-flash-messages />

        <main class="main-container w-full max-w-[1100px] mx-auto p-5 md:p-8 page-transition pt-8 md:pt-10 pb-24 md:pb-12">
            @auth
            {{-- USER STATUS STRIP --}}
            <div class="flex flex-wrap items-center justify-center gap-4 md:gap-8 px-5 py-4 mb-8 rounded-2xl bg-white/[0.02] border border-white/[0.05] shadow-sm animate-in">
                {{-- Streak --}}
                <div class="flex items-center gap-3 pr-4 md:pr-8 border-r border-white/5 last:border-0 h-10">
                    <div class="w-10 h-10 rounded-xl bg-[#FFD166]/10 flex items-center justify-center text-xl">🔥</div>
                    <div class="text-left">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[var(--text-muted)] mb-0.5">Streak</p>
                        <p class="font-syne font-black text-sm text-[#FFD166] leading-none">{{ auth()->user()->streak ?? 0 }} Hari</p>
                    </div>
                </div>
                {{-- XP --}}
                <div class="flex items-center gap-3 pr-4 md:pr-8 border-r border-white/5 last:border-0 h-10">
                    <div class="w-10 h-10 rounded-xl bg-[var(--primary)]/10 flex items-center justify-center text-xl">⚡</div>
                    <div class="text-left">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[var(--text-muted)] mb-0.5">Total XP</p>
                        <p class="font-syne font-black text-sm text-[var(--primary)] leading-none">{{ number_format(auth()->user()->xp ?? 0) }}</p>
                    </div>
                </div>
                {{-- Level --}}
                <div class="flex items-center gap-3 pr-4 md:pr-8 border-r border-white/5 last:border-0 h-10">
                    <div class="w-10 h-10 rounded-xl bg-[#06D6A0]/10 flex items-center justify-center text-xl">⭐</div>
                    <div class="text-left">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[var(--text-muted)] mb-0.5">Level</p>
                        <p class="font-syne font-black text-sm text-[#06D6A0] leading-none">{{ auth()->user()->level ?? 1 }}</p>
                    </div>
                </div>
                {{-- Goal (Optional) --}}
                @if(isset($dailyProgress) && isset($goalTarget))
                <div class="flex items-center gap-3 h-10">
                    <div class="w-10 h-10 rounded-xl bg-[var(--secondary)]/10 flex items-center justify-center text-xl">🎯</div>
                    <div class="text-left">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-[var(--text-muted)] mb-0.5">Target Hari Ini</p>
                        <p class="font-syne font-black text-sm text-[var(--secondary)] leading-none">{{ $dailyProgress }} / {{ $goalTarget }}</p>
                    </div>
                </div>
                @endif
            </div>
            @endauth
            @hasSection('nav_header')
                <header class="flex items-center justify-between mb-8 pb-3 border-b border-[var(--border)]">
                    <a href="javascript:history.back()" class="flex items-center gap-2 text-sm font-semibold tracking-wide text-[var(--text-muted)] hover:text-white transition-colors group px-3 py-1.5 rounded-lg hover:bg-[rgba(255,255,255,0.05)]">
                        <span class="group-hover:-translate-x-1 transition-transform">←</span>
                        Back
                    </a>
                    <div class="text-sm font-medium tracking-wide opacity-80" style="color:var(--text-muted);">
                        @yield('nav_header')
                    </div>
                </header>
            @endif

            @yield('content')
        </main>

    </div>

    {{-- Scripts --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('xpCounter', (targetValue) => ({
                current: 0, target: targetValue,
                init() {
                    let duration = 1500, steps = 60, stepTime = duration / steps;
                    let stepValue = Math.max(1, Math.floor(this.target / steps));
                    let counter = setInterval(() => {
                        if (this.current + stepValue >= this.target) {
                            this.current = this.target; clearInterval(counter);
                        } else { this.current += stepValue; }
                    }, stepTime);
                }
            }));
        });

        document.querySelectorAll('#flash-msg, #game-success-trigger').forEach(el => {
            if (el.id === 'game-success-trigger') {
                confetti({ particleCount: 150, spread: 80, origin: { y: 0.6 }, colors: ['#ffd166', '#6c63ff', '#06d6a0'] });
            }
            setTimeout(() => {
                el.style.transition = 'opacity 0.5s, transform 0.5s';
                el.style.opacity = '0'; el.style.transform = 'translateY(-20px)';
                setTimeout(() => el.remove(), 500);
            }, 4000);
        });
    </script>
    @stack('scripts')
</body>
</html>

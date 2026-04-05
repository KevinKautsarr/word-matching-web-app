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
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">
    
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
                        dark3:  '#1a1f35',
                        purple: '#6c63ff',
                        purpleLight: '#8b85ff',
                        emerald: '#06d6a0',
                        gold:   '#ffd166',
                        themeText: 'var(--text)',
                        themeMuted: 'var(--text-muted)',
                        themeBorder: 'var(--border)'
                    },
                    fontFamily: {
                        syne: ['Syne', 'sans-serif'],
                        dm:   ['DM Sans', 'sans-serif'],
                    },
                }
            }
        }
    </script>

    <style>
        :root {
            --bg: #0f1220;
            --card: rgba(19,22,42,0.8);
            --nav-bg: rgba(13,15,26,0.6);
            --border: rgba(255,255,255,0.06);
            --text: #f1f5f9;
            --text-muted: #94a3b8;
            --purple: #6c63ff;
            --green: #06d6a0;
            --gold: #ffd166;
            --red: #ff6363;
        }

        * {
            transition: all 0.25s ease;
        }

        body { 
            font-family: 'DM Sans', sans-serif; 
            background-color: var(--bg); 
            color: var(--text); 
            position: relative;
        }

        /* --- Ambient Gradient + Grain --- */
        .ambient-bg {
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            background: 
                radial-gradient(circle at 15% 50%, rgba(108,99,255,0.05) 0%, transparent 50%),
                radial-gradient(circle at 85% 30%, rgba(6,214,160,0.04) 0%, transparent 50%);
        }
        .grain-overlay {
            position: fixed;
            inset: 0;
            z-index: -1;
            pointer-events: none;
            opacity: 0.25;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.8' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        }

        h1,h2,h3,h4,h5,h6,.font-syne { font-family: 'Syne', sans-serif; }

        .nav-glass {
            background: var(--nav-bg);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
        }

        .glass { background: var(--card); backdrop-filter: blur(16px); border: 1px solid var(--border); }
        .glass-hover:hover { filter: brightness(1.1); border-color: rgba(108,99,255,0.4); }

        .card {
            background: var(--card);
            backdrop-filter: blur(14px);
            border: 1px solid var(--border);
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
            border-radius: 1.25rem;
        }
        .card:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 12px 40px rgba(108,99,255,0.15);
        }
        .card::before {
            content:'';
            position:absolute;
            inset:0;
            background: radial-gradient(circle at top left, rgba(108,99,255,0.08), transparent);
            pointer-events: none;
        }

        .progress-gradient {
            background: linear-gradient(90deg, #6c63ff, #06d6a0);
        }

        .gradient-text { background: linear-gradient(135deg, #6c63ff 0%, #06d6a0 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .btn-primary { 
            background: linear-gradient(135deg, #6c63ff, #8b85ff); 
            color: white; 
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1); 
        }
        .btn-primary:hover { 
            box-shadow: 0 0 24px rgba(108,99,255,0.5); 
            transform: translateY(-2px); 
        }
        .btn-primary:active, .pressable:active {
            transform: scale(0.95) !important;
            box-shadow: none !important;
        }

        .glow-purple { box-shadow: 0 0 24px rgba(108,99,255,0.35); }
        .glow-green  { box-shadow: 0 0 24px rgba(6,214,160,0.35); }
        .glow-gold   { box-shadow: 0 0 24px rgba(255,209,102,0.35); }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #13162a; }
        ::-webkit-scrollbar-thumb { background: #6c63ff; border-radius: 4px; }
        
        .animate-in { animation: fadeSlideUp 0.4s ease forwards; }
        .page-transition {
            animation: pageFadeIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes fadeSlideUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
        @keyframes pageFadeIn {
            0% { opacity: 0; transform: translateY(15px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        .sidebar {
            background: linear-gradient(180deg, var(--nav-bg) 0%, rgba(13,15,26,0.85) 100%);
            border-right: 1px solid var(--border);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            box-shadow: 4px 0 30px rgba(0,0,0,0.2);
        }
        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 12px 18px;
            border-radius: 14px;
            color: var(--text-muted);
            font-weight: 600;
        }
        .sidebar-item .menu-icon {
            opacity: 0.6;
            transition: all 0.25s ease;
        }
        .sidebar-item:hover {
            color: var(--text);
            background: rgba(255,255,255,0.04);
            transform: translateX(4px);
        }
        .sidebar-item:hover .menu-icon {
            opacity: 1;
            transform: scale(1.1);
        }
        .sidebar-item.active {
            color: var(--purple);
            background: linear-gradient(90deg, rgba(108,99,255,0.1) 0%, transparent 100%);
        }
        .sidebar-item.active .menu-icon {
            opacity: 1;
        }
        .sidebar-item.active::before {
            content: '';
            position: absolute;
            left: 0;
            height: 50%;
            width: 4px;
            background: var(--purple);
            border-radius: 0 4px 4px 0;
            box-shadow: 0 0 12px var(--purple);
        }

        .topbar {
            background: transparent;
        }

        .badge-glass {
            background: rgba(19,22,42,0.4);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.08);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15), inset 0 1px 0 rgba(255,255,255,0.03);
            padding: 8px 14px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: default;
            transition: all 0.25s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .badge-glass:hover {
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 8px 16px rgba(0,0,0,0.25), inset 0 1px 0 rgba(255,255,255,0.08);
            border-color: rgba(255,255,255,0.2);
            background: rgba(19,22,42,0.7);
        }

    </style>

    @stack('styles')
</head>
<body class="min-h-screen text-themeText font-dm flex bg-[var(--bg)]">

    {{-- Ambient Grain + Gradient Layers --}}
    <div class="ambient-bg"></div>
    <div class="grain-overlay"></div>

{{-- ============================================================ --}}
{{-- SIDEBAR NAVIGATION                                           --}}
{{-- ============================================================ --}}
<aside class="sidebar fixed inset-y-0 left-0 w-[240px] flex flex-col z-50">
    
    {{-- Logo --}}
    <div class="h-20 flex items-center px-6 mb-2 mt-2">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl btn-primary flex items-center justify-center glow-purple shrink-0">
                <span class="text-white font-bold text-lg font-syne">L</span>
            </div>
            <span class="font-syne font-bold text-2xl tracking-tight text-white group-hover:drop-shadow-[0_0_8px_rgba(108,99,255,0.8)] transition-all">LEXORA</span>
        </a>
    </div>

    {{-- Menu Items --}}
    <div class="flex-1 px-4 space-y-2 overflow-y-auto pb-6">
        <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <span class="text-xl menu-icon">🗺️</span>
            <span>Journey</span>
        </a>
        <a href="#" class="sidebar-item">
            <span class="text-xl menu-icon">🎯</span>
            <span>Practice</span>
        </a>
        <a href="#" class="sidebar-item">
            <span class="text-xl menu-icon">📁</span>
            <span>Projects</span>
        </a>
        <a href="#" class="sidebar-item">
            <span class="text-xl menu-icon">🏆</span>
            <span>Goals</span>
        </a>
        <a href="#" class="sidebar-item">
            <span class="text-xl menu-icon">👑</span>
            <span>Leaderboard</span>
        </a>
        <a href="#" class="sidebar-item">
            <span class="text-xl menu-icon">🏪</span>
            <span>Store</span>
        </a>
        <a href="{{ route('profile.index') }}" class="sidebar-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
            <span class="text-xl menu-icon">👤</span>
            <span>Profile</span>
        </a>
        <a href="#" class="sidebar-item mt-6">
            <span class="text-xl menu-icon">⋯</span>
            <span>More</span>
        </a>
    </div>

    {{-- Bottom Section (Profile & Logout) --}}
    <div class="p-4 mt-auto border-t border-[var(--border)] pt-4">
        @auth
        <a href="{{ route('profile.index') }}" class="group block flex items-center gap-3 p-2.5 mb-3 rounded-2xl bg-[rgba(255,255,255,0.03)] border border-transparent hover:border-[rgba(108,99,255,0.2)] hover:bg-[rgba(108,99,255,0.05)] transition-all">
            <div class="w-10 h-10 rounded-full btn-primary flex items-center justify-center text-white font-bold text-sm shrink-0 shadow-[0_4px_12px_rgba(108,99,255,0.3)] group-hover:scale-110 group-hover:shadow-[0_4px_24px_rgba(108,99,255,0.5)] transition-all">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="overflow-hidden flex-1">
                <p class="font-syne font-bold text-sm text-[var(--text)] group-hover:text-[var(--purple)] transition-colors truncate">
                    {{ explode(' ', auth()->user()->name)[0] }}
                </p>
                <p class="text-xs text-[var(--text-muted)] truncate">Level {{ auth()->user()->level }}</p>
            </div>
        </a>
        @endauth

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-red-500/80 font-semibold hover:text-red-400 hover:bg-red-500/10 transition-colors">
                <span class="text-xl">🚪</span>
                <span class="text-sm">Logout</span>
            </button>
        </form>
    </div>
</aside>

{{-- ============================================================ --}}
{{-- MAIN WRAPPER (TOP BAR + CONTENT)                             --}}
{{-- ============================================================ --}}
<div class="flex-1 ml-[240px] flex flex-col min-h-screen relative">
    
    {{-- TOP BAR --}}
    <header class="topbar sticky top-0 z-40 h-[80px] px-8 flex items-center justify-end backdrop-blur-md border-b border-transparent">
        <div class="flex items-center gap-4">
            @auth
                @php
                    $lastPlayedDate = auth()->user()->last_played_at ? clone auth()->user()->last_played_at->startOfDay() : null;
                    $isStreakAtRisk = $lastPlayedDate && $lastPlayedDate->eq(now()->subDay()->startOfDay());
                    $streakVal = auth()->user()->streak;
                    $isHotStreak = $streakVal >= 7;
                @endphp
                {{-- Streak Warning Tooltip or Glow --}}
                <div x-data="xpCounter({{ auth()->user()->xp }})" class="flex items-center gap-4">
                    <div title="{{ $isStreakAtRisk ? 'Streak hampir hilang! Latihan hari ini!' : 'Streak aktif kamu' }}" 
                         class="badge-glass pb-1.5 pt-1.5 transition-all transform {{ $isStreakAtRisk ? 'animate-pulse shadow-[0_0_15px_rgba(255,107,107,0.5)] border-[#ff6b6b]/40' : '' }} {{ $isHotStreak ? 'glow-gold border-[#ffd166]/30' : '' }}">
                        <span class="text-xl {{ $isHotStreak ? 'scale-125' : '' }} transition-transform">🔥</span>
                        <span class="tracking-wide" style="color:{{ $isStreakAtRisk ? '#ff6b6b' : 'var(--text)' }};">
                            {{ $streakVal }}d
                        </span>
                    </div>
                    <div title="Total XP yang dikumpulkan" class="badge-glass pb-1.5 pt-1.5 group cursor-default">
                        <span class="text-xl group-hover:scale-110 transition-transform" style="color:var(--gold);">⚡</span>
                        <span class="tracking-wide" style="color:var(--text);"><span x-text="current"></span> XP</span>
                    </div>
                    <div title="Level kamu saat ini" class="badge-glass pb-1.5 pt-1.5 shadow-[0_4px_12px_rgba(108,99,255,0.15)] glow-purple border-[rgba(108,99,255,0.2)]">
                        <span class="text-xl text-[#06d6a0]">⭐</span>
                        <span class="tracking-wide" style="color:var(--text);">Level {{ auth()->user()->level }}</span>
                    </div>
                </div>
            @endauth
        </div>
    </header>

{{-- ============================================================ --}}
{{-- FLASH MESSAGES                                               --}}
{{-- ============================================================ --}}
@if (session('success'))
    <div id="flash-msg" class="fixed top-20 right-4 z-50 animate-in">
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl glass border border-[#06d6a0]/40 glow-green max-w-sm shadow-xl">
            <div class="w-5 h-5 rounded-full bg-[#06d6a0]/20 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-3 h-3 text-[#06d6a0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                </svg>
            </div>
            <p class="text-sm text-white leading-relaxed">{{ session('success') }}</p>
        </div>
    </div>
@endif

@if (session('error'))
    <div id="flash-msg" class="fixed top-20 right-4 z-50 animate-in">
        <div class="flex items-start gap-3 px-4 py-3 rounded-xl glass border border-red-500/40 max-w-sm shadow-xl">
            <div class="w-5 h-5 rounded-full bg-red-500/20 flex items-center justify-center shrink-0 mt-0.5">
                <svg class="w-3 h-3 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <p class="text-sm text-white leading-relaxed">{{ session('error') }}</p>
        </div>
    </div>
@endif

{{-- ============================================================ --}}
{{-- MAIN CONTENT                                                 --}}
{{-- ============================================================ --}}
    <main class="w-full max-w-[1100px] mx-auto p-8 page-transition pt-6">
        
        {{-- ============================================================ --}}
        {{-- CODDY STYLE NAVIGATION HEADER (BERBENTUK BACK + SECTION) --}}
        {{-- ============================================================ --}}
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

</div> {{-- End Main Wrapper --}}

{{-- Alpine.js --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    // Alpine logic for XP Count Up
    document.addEventListener('alpine:init', () => {
        Alpine.data('xpCounter', (targetValue) => ({
            current: 0,
            target: targetValue,
            init() {
                // start counting logic
                let duration = 1500; // ms
                let steps = 60;
                let stepTime = duration / steps;
                let stepValue = Math.max(1, Math.floor(this.target / steps));
                
                let counter = setInterval(() => {
                    if (this.current + stepValue >= this.target) {
                        this.current = this.target;
                        clearInterval(counter);
                    } else {
                        this.current += stepValue;
                    }
                }, stepTime);
            }
        }));
    });

    // Auto-dismiss flash messages + Confetti triggers
    const flashEl = document.getElementById('flash-msg');
    if (flashEl) {
        
        // Trigger generic confetti if success alert happens
        if (flashEl.innerHTML.includes('06d6a0') || flashEl.innerHTML.includes('success') || flashEl.innerHTML.includes('Bonus') || flashEl.innerHTML.includes('Streak')) {
            confetti({
                particleCount: 150,
                spread: 80,
                origin: { y: 0.6 },
                colors: ['#06d6a0', '#6c63ff', '#ffd166', '#ff6b6b']
            });
            // Play success sound if global object injected
            if(window.Audio) {
                let sfx = new Audio('/sounds/success.mp3');
                sfx.volume = 0.5;
                sfx.play().catch(e => {});
            }
        }

        setTimeout(() => {
            flashEl.style.transition = 'opacity 0.5s, transform 0.5s';
            flashEl.style.opacity = '0';
            flashEl.style.transform = 'translateY(-20px)';
            setTimeout(() => flashEl.remove(), 500);
        }, 4000);
    }
</script>

@stack('scripts')
</body>
</html>

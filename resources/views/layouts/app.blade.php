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

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        dark:   '#0d0f1a',
                        dark2:  '#13162a',
                        dark3:  '#1a1f35',
                        purple: '#6c63ff',
                        purpleLight: '#8b85ff',
                        emerald: '#06d6a0',
                        gold:   '#ffd166',
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
        body { font-family: 'DM Sans', sans-serif; background-color: #0d0f1a; color: #e2e4f0; }
        h1,h2,h3,h4,h5,h6,.font-syne { font-family: 'Syne', sans-serif; }
        .glass { background: rgba(255,255,255,0.04); backdrop-filter: blur(16px); border: 1px solid rgba(255,255,255,0.08); }
        .glass-hover:hover { background: rgba(255,255,255,0.07); border-color: rgba(255,255,255,0.12); }
        .gradient-text { background: linear-gradient(135deg, #6c63ff 0%, #06d6a0 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; }
        .btn-primary { background: linear-gradient(135deg, #6c63ff, #8b85ff); color: white; transition: all 0.25s ease; }
        .btn-primary:hover { box-shadow: 0 0 24px rgba(108,99,255,0.5); transform: translateY(-1px); }
        .glow-purple { box-shadow: 0 0 24px rgba(108,99,255,0.35); }
        .glow-green  { box-shadow: 0 0 24px rgba(6,214,160,0.35); }
        .glow-gold   { box-shadow: 0 0 24px rgba(255,209,102,0.35); }
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: #13162a; }
        ::-webkit-scrollbar-thumb { background: #6c63ff; border-radius: 4px; }
        .animate-in { animation: fadeSlideUp 0.4s ease forwards; }
        @keyframes fadeSlideUp { from { opacity:0; transform:translateY(12px); } to { opacity:1; transform:translateY(0); } }
    </style>

    @stack('styles')
</head>
<body class="min-h-screen bg-[#0d0f1a]">

{{-- ============================================================ --}}
{{-- NAVBAR                                                       --}}
{{-- ============================================================ --}}
<nav class="sticky top-0 z-50 glass border-b border-white/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group">
                <div class="w-9 h-9 rounded-xl btn-primary flex items-center justify-center glow-purple shrink-0">
                    <span class="text-white font-bold text-base font-syne">L</span>
                </div>
                <span class="font-syne font-bold text-xl gradient-text tracking-tight">LEXORA</span>
            </a>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('dashboard') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                          {{ request()->routeIs('dashboard') ? 'text-[#6c63ff] bg-[#6c63ff]/10' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                    Dashboard
                </a>
                <a href="{{ route('profile.index') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium transition-all duration-200
                          {{ request()->routeIs('profile.*') ? 'text-[#6c63ff] bg-[#6c63ff]/10' : 'text-white/60 hover:text-white hover:bg-white/5' }}">
                    Profil
                </a>
            </div>

            {{-- Right Side: Badges + User Menu --}}
            <div class="flex items-center gap-2">

                {{-- XP Badge --}}
                <div class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-full glass text-xs font-semibold">
                    <span class="text-[#ffd166]">⚡</span>
                    <span class="text-[#ffd166]">{{ auth()->user()->xp }} XP</span>
                </div>

                {{-- Streak Badge --}}
                <div class="hidden sm:flex items-center gap-1.5 px-3 py-1.5 rounded-full glass text-xs font-semibold">
                    <span>🔥</span>
                    <span class="text-[#ffd166]">{{ auth()->user()->streak }}d</span>
                </div>

                {{-- Avatar Dropdown (Alpine.js) --}}
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open"
                            class="w-9 h-9 rounded-full btn-primary flex items-center justify-center text-white font-bold text-sm tracking-wide shrink-0">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </button>

                    <div x-show="open"
                         @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-52 glass rounded-2xl shadow-2xl py-1.5 z-50 border border-white/10"
                         style="display:none">

                        <div class="px-4 py-2.5 border-b border-white/10">
                            <p class="text-sm font-semibold text-white truncate font-syne">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-white/40 truncate mt-0.5">{{ auth()->user()->email }}</p>
                        </div>

                        <a href="{{ route('profile.index') }}"
                           class="flex items-center gap-2.5 px-4 py-2 mt-1 text-sm text-white/70 hover:text-white hover:bg-white/5 transition-colors rounded-lg mx-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profil Saya
                        </a>

                        <div class="border-t border-white/10 mt-1 pt-1">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="w-full flex items-center gap-2.5 px-4 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-red-500/10 transition-colors rounded-lg mx-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    Logout
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</nav>

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
<main>
    @yield('content')
</main>

{{-- Alpine.js --}}
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<script>
    // Auto-dismiss flash messages
    const flashEl = document.getElementById('flash-msg');
    if (flashEl) {
        setTimeout(() => {
            flashEl.style.transition = 'opacity 0.5s, transform 0.5s';
            flashEl.style.opacity = '0';
            flashEl.style.transform = 'translateX(20px)';
            setTimeout(() => flashEl.remove(), 500);
        }, 4000);
    }
</script>

@stack('scripts')
</body>
</html>

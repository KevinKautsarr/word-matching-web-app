<aside class="sidebar fixed inset-y-0 left-0 w-[240px] flex flex-col z-50">
    
    {{-- Logo --}}
    <div class="h-20 flex items-center px-6 mb-2 mt-2">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
            <div class="w-10 h-10 rounded-xl btn-primary flex items-center justify-center glow-primary shrink-0">
                <span class="text-white font-bold text-lg font-syne">L</span>
            </div>
            <span class="font-syne font-bold text-2xl tracking-tight text-white group-hover:drop-shadow-[0_0_8px_rgba(79, 124, 255, 0.8)] transition-all">LEXORA</span>
        </a>
    </div>

    {{-- Profile Card (Top) --}}
    <div class="px-4 mb-4">
        @auth
        <a href="{{ route('profile.index') }}" class="group block flex items-center gap-3 p-2.5 rounded-2xl bg-[rgba(255,255,255,0.03)] border border-transparent hover:border-[rgba(79,124,255,0.2)] hover:bg-[rgba(79,124,255,0.05)] transition-all shadow-[0_4px_20px_rgba(0,0,0,0.1)] hover:shadow-[0_8px_30px_rgba(79,124,255,0.1)]">
            <div class="w-10 h-10 rounded-full btn-primary flex items-center justify-center text-white font-bold text-sm shrink-0 shadow-[0_4px_12px_rgba(79,124,255,0.3)] group-hover:scale-105 group-hover:shadow-[0_4px_24px_rgba(79,124,255,0.5)] transition-all">
                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
            </div>
            <div class="overflow-hidden flex-1">
                <p class="font-syne font-bold text-sm text-[var(--text)] group-hover:text-[var(--primary)] transition-colors truncate">
                    {{ explode(' ', auth()->user()->name)[0] }}
                </p>
                <p class="text-xs text-[var(--text-muted)] truncate">Level {{ auth()->user()->level }}</p>
            </div>
        </a>
        @endauth
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
    </div>

    {{-- Bottom Area --}}
    <div class="p-4 mt-auto border-t border-[var(--border)]">
        <a href="#" class="sidebar-item mb-2 {{ request()->is('settings*') ? 'active' : '' }}">
            <span class="text-xl menu-icon">⚙️</span>
            <span>Settings</span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full sidebar-item !text-red-400/80 hover:!text-red-400 hover:bg-red-500/10 transition-colors group">
                <span class="text-xl menu-icon group-hover:scale-110 transition-transform">🚪</span>
                <span>Logout</span>
            </button>
        </form>
        <p class="text-[10px] text-center font-syne uppercase tracking-widest opacity-30 mt-4">Lexora v1.0</p>
    </div>
</aside>

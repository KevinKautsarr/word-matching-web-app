<header class="topbar sticky top-0 z-40 h-[70px] md:h-[80px] px-4 md:px-8 flex items-center justify-center md:justify-end backdrop-blur-md border-b border-transparent">
    <div class="flex items-center gap-2 md:gap-4 scale-95 md:scale-100">
        @auth
            @php
                $user = auth()->user();
                $lastPlayedDate = $user->last_played_at 
                    ? \Carbon\Carbon::parse($user->last_played_at)->startOfDay() 
                    : null;
                $isStreakAtRisk = $lastPlayedDate && $lastPlayedDate->eq(now()->subDay()->startOfDay());
                $streakVal = $user->streak;
                $isHotStreak = $streakVal >= 7;
            @endphp

            <div x-data="xpCounter({{ $user->xp }})" class="flex items-center gap-4">
                {{-- Streak --}}
                <div title="{{ $isStreakAtRisk ? 'Streak hampir hilang! Latihan hari ini!' : 'Streak aktif kamu' }}" 
                     class="badge-glass pb-1.5 pt-1.5 transition-all transform {{ $isStreakAtRisk ? 'animate-pulse shadow-[0_0_15px_rgba(255,107,107,0.5)] border-[#ff6b6b]/40' : '' }} {{ $isHotStreak ? 'glow-gold border-[#ffd166]/30' : '' }}">
                    <span class="text-xl {{ $isHotStreak ? 'scale-125' : '' }} transition-transform">🔥</span>
                    <span class="tracking-wide" style="color:{{ $isStreakAtRisk ? '#ff6b6b' : 'var(--text)' }};">
                        {{ $streakVal }}d
                    </span>
                </div>

                {{-- XP --}}
                <div title="Total XP yang dikumpulkan" class="badge-glass pb-1.5 pt-1.5 group cursor-default">
                    <span class="text-xl group-hover:scale-110 transition-transform" style="color:var(--gold);">⚡</span>
                    <span class="tracking-wide" style="color:var(--text);"><span x-text="current"></span> XP</span>
                </div>

                {{-- Level --}}
                <div title="Level kamu saat ini" class="badge-glass pb-1.5 pt-1.5 shadow-[0_4px_12px_rgba(108,99,255,0.15)] glow-purple border-[rgba(108,99,255,0.2)]">
                    <span class="text-xl text-[#06d6a0]">⭐</span>
                    <span class="tracking-wide" style="color:var(--text);">Level {{ $user->level }}</span>
                </div>
            </div>
        @endauth
    </div>
</header>

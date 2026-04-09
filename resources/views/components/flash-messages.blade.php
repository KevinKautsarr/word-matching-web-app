<div>
    {{-- Game Success --}}
    @if (session('game_success'))
        <div id="game-success-trigger" class="fixed top-20 right-4 z-50 animate-in">
            <div class="flex items-start gap-3 px-4 py-3 rounded-xl glass border border-[#ffd166]/40 glow-gold max-w-sm shadow-xl">
                <div class="w-5 h-5 rounded-full bg-[#ffd166]/20 flex items-center justify-center shrink-0 mt-0.5">
                    <span class="text-xs">✨</span>
                </div>
                <p class="text-sm text-white leading-relaxed">{{ session('game_success') }}</p>
            </div>
        </div>
    @endif

    {{-- General Success --}}
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

    {{-- General Error --}}
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
</div>

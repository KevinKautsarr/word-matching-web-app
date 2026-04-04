@extends('layouts.guest')

@section('title', 'LEXORA – Belajar Kosakata Bahasa Inggris')
@section('meta_description', 'LEXORA: Platform interaktif pembelajaran kosakata Bahasa Inggris dengan Word Matching dan sistem gamifikasi. Belajar sambil bermain!')

@section('content')

{{-- ============================================================ --}}
{{-- NAVBAR LANDING                                               --}}
{{-- ============================================================ --}}
<nav class="fixed top-0 left-0 right-0 z-50 glass border-b border-white/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-16">
        <div class="flex items-center gap-2">
            <div class="w-9 h-9 rounded-xl btn-primary flex items-center justify-center glow-purple">
                <span class="font-syne font-bold text-white">L</span>
            </div>
            <span class="font-syne font-bold text-xl gradient-text">LEXORA</span>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('login') }}"
               class="px-4 py-2 text-sm font-medium text-white/70 hover:text-white transition-colors">
                Masuk
            </a>
            <a href="{{ route('register') }}"
               class="px-5 py-2 rounded-xl btn-primary text-sm font-semibold">
                Daftar Gratis
            </a>
        </div>
    </div>
</nav>

{{-- ============================================================ --}}
{{-- HERO SECTION                                                 --}}
{{-- ============================================================ --}}
<section class="relative min-h-screen flex items-center justify-center overflow-hidden pt-16">

    {{-- Background Orbs --}}
    <div class="bg-orb w-[600px] h-[600px] bg-[#6c63ff]/20 top-[-100px] left-[-200px]"></div>
    <div class="bg-orb w-[500px] h-[500px] bg-[#06d6a0]/15 bottom-[-100px] right-[-150px]"></div>
    <div class="bg-orb w-[300px] h-[300px] bg-[#ffd166]/10 top-[30%] left-[60%]"></div>

    <div class="relative z-10 max-w-4xl mx-auto px-4 text-center">

        {{-- Badge --}}
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass border border-[#6c63ff]/30 text-sm font-medium mb-8 animate-in">
            <span class="w-2 h-2 rounded-full bg-[#06d6a0] animate-pulse"></span>
            <span class="text-[#06d6a0]">Platform Pembelajaran Interaktif</span>
        </div>

        {{-- Headline --}}
        <h1 class="font-syne font-bold text-5xl md:text-7xl leading-tight mb-6 animate-in" style="animation-delay:0.1s">
            Kuasai Kosakata<br>
            <span class="gradient-text">Bahasa Inggris</span><br>
            Dengan Cara Seru
        </h1>

        {{-- Sub-headline --}}
        <p class="text-lg md:text-xl text-white/60 max-w-2xl mx-auto mb-10 leading-relaxed animate-in" style="animation-delay:0.2s">
            LEXORA menggabungkan metode <strong class="text-white/80">Word Matching</strong> dengan sistem
            <strong class="text-white/80">gamifikasi</strong> — kumpulkan XP, naik level, dan jaga streak harianmu!
        </p>

        {{-- CTA Buttons --}}
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-in" style="animation-delay:0.3s">
            <a href="{{ route('register') }}"
               class="w-full sm:w-auto px-8 py-4 rounded-2xl btn-primary font-semibold text-base glow-purple">
                🚀 Mulai Belajar Sekarang
            </a>
            <a href="{{ route('login') }}"
               class="w-full sm:w-auto px-8 py-4 rounded-2xl glass border border-white/10 font-semibold text-base text-white/80 hover:text-white hover:bg-white/5 transition-all">
                Sudah Punya Akun →
            </a>
        </div>

        {{-- Stats --}}
        <div class="mt-16 grid grid-cols-3 gap-6 max-w-md mx-auto animate-in" style="animation-delay:0.4s">
            <div class="text-center">
                <div class="text-3xl font-bold font-syne gradient-text">500+</div>
                <div class="text-xs text-white/40 mt-1">Kosakata</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold font-syne gradient-text">10+</div>
                <div class="text-xs text-white/40 mt-1">Unit Belajar</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold font-syne gradient-text">∞</div>
                <div class="text-xs text-white/40 mt-1">Streak</div>
            </div>
        </div>

    </div>
</section>

{{-- ============================================================ --}}
{{-- FEATURES SECTION                                             --}}
{{-- ============================================================ --}}
<section class="py-24 px-4 relative overflow-hidden">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16">
            <h2 class="font-syne font-bold text-4xl md:text-5xl mb-4">Mengapa <span class="gradient-text">LEXORA</span>?</h2>
            <p class="text-white/50 text-lg max-w-xl mx-auto">Belajar yang efektif, menyenangkan, dan terstruktur</p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">

            {{-- Feature 1 --}}
            <div class="glass rounded-2xl p-7 hover:border-[#6c63ff]/30 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-[#6c63ff]/20 flex items-center justify-center mb-5 group-hover:bg-[#6c63ff]/30 transition-colors">
                    <span class="text-2xl">🎯</span>
                </div>
                <h3 class="font-syne font-bold text-lg mb-2">Word Matching</h3>
                <p class="text-white/50 text-sm leading-relaxed">
                    Pasangkan kata Bahasa Inggris dengan artinya secara interaktif. Metode yang terbukti efektif melatih ingatan kosakata.
                </p>
            </div>

            {{-- Feature 2 --}}
            <div class="glass rounded-2xl p-7 hover:border-[#06d6a0]/30 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-[#06d6a0]/20 flex items-center justify-center mb-5 group-hover:bg-[#06d6a0]/30 transition-colors">
                    <span class="text-2xl">⚡</span>
                </div>
                <h3 class="font-syne font-bold text-lg mb-2">Sistem XP & Level</h3>
                <p class="text-white/50 text-sm leading-relaxed">
                    Setiap lesson yang diselesaikan memberikan poin XP. Kumpulkan dan naiki level untuk membuka konten eksklusif.
                </p>
            </div>

            {{-- Feature 3 --}}
            <div class="glass rounded-2xl p-7 hover:border-[#ffd166]/30 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-[#ffd166]/20 flex items-center justify-center mb-5 group-hover:bg-[#ffd166]/30 transition-colors">
                    <span class="text-2xl">🔥</span>
                </div>
                <h3 class="font-syne font-bold text-lg mb-2">Streak Harian</h3>
                <p class="text-white/50 text-sm leading-relaxed">
                    Bangun kebiasaan belajar dengan streak harian. Login tiap hari untuk menjaga rangkaian belajarmu.
                </p>
            </div>

            {{-- Feature 4 --}}
            <div class="glass rounded-2xl p-7 hover:border-[#6c63ff]/30 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-[#6c63ff]/20 flex items-center justify-center mb-5 group-hover:bg-[#6c63ff]/30 transition-colors">
                    <span class="text-2xl">📚</span>
                </div>
                <h3 class="font-syne font-bold text-lg mb-2">Konten Terstruktur</h3>
                <p class="text-white/50 text-sm leading-relaxed">
                    Materi tersusun dalam unit dan lesson yang terstruktur, disesuaikan dengan tingkat kesulitan bertahap.
                </p>
            </div>

            {{-- Feature 5 --}}
            <div class="glass rounded-2xl p-7 hover:border-[#06d6a0]/30 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-[#06d6a0]/20 flex items-center justify-center mb-5 group-hover:bg-[#06d6a0]/30 transition-colors">
                    <span class="text-2xl">📊</span>
                </div>
                <h3 class="font-syne font-bold text-lg mb-2">Lacak Progress</h3>
                <p class="text-white/50 text-sm leading-relaxed">
                    Pantau perkembangan belajarmu secara real-time. Lihat skor, waktu, dan jumlah percobaan di setiap lesson.
                </p>
            </div>

            {{-- Feature 6 --}}
            <div class="glass rounded-2xl p-7 hover:border-[#ffd166]/30 transition-all group">
                <div class="w-12 h-12 rounded-xl bg-[#ffd166]/20 flex items-center justify-center mb-5 group-hover:bg-[#ffd166]/30 transition-colors">
                    <span class="text-2xl">🏆</span>
                </div>
                <h3 class="font-syne font-bold text-lg mb-2">Pencapaian</h3>
                <p class="text-white/50 text-sm leading-relaxed">
                    Raih lencana dan pencapaian saat mencapai milestone tertentu. Motivasi diri dengan rekam jejakmu.
                </p>
            </div>

        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- CTA SECTION                                                  --}}
{{-- ============================================================ --}}
<section class="py-24 px-4">
    <div class="max-w-3xl mx-auto text-center">
        <div class="glass rounded-3xl p-12 border border-[#6c63ff]/20 relative overflow-hidden">
            <div class="bg-orb w-64 h-64 bg-[#6c63ff]/15 top-[-50px] left-[-50px]"></div>
            <div class="bg-orb w-64 h-64 bg-[#06d6a0]/15 bottom-[-50px] right-[-50px]"></div>
            <div class="relative z-10">
                <h2 class="font-syne font-bold text-4xl mb-4">Siap Mulai Belajar?</h2>
                <p class="text-white/50 mb-8 text-lg">Bergabunglah sekarang dan mulai perjalanan belajar kosakata Bahasa Inggrismu bersama LEXORA.</p>
                <a href="{{ route('register') }}"
                   class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl btn-primary font-semibold text-base glow-purple">
                    Daftar Gratis →
                </a>
            </div>
        </div>
    </div>
</section>

{{-- Footer --}}
<footer class="py-8 px-4 border-t border-white/5 text-center">
    <p class="text-white/30 text-sm">© {{ date('Y') }} LEXORA. Tugas Akhir – Perancangan Website Pembelajaran Kosakata Bahasa Inggris.</p>
</footer>

@endsection

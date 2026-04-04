@extends('layouts.guest')

@section('title', 'Daftar Akun LEXORA')
@section('meta_description', 'Buat akun LEXORA gratis dan mulai belajar kosakata Bahasa Inggris dengan cara yang menyenangkan.')

@section('content')

<div class="min-h-screen flex items-center justify-center px-4 py-12 relative overflow-hidden">

    {{-- Background Orbs --}}
    <div class="absolute w-[500px] h-[500px] rounded-full bg-[#6c63ff]/15 blur-[100px] top-[-100px] right-[-150px] pointer-events-none"></div>
    <div class="absolute w-[400px] h-[400px] rounded-full bg-[#06d6a0]/10 blur-[100px] bottom-[-100px] left-[-100px] pointer-events-none"></div>
    <div class="absolute w-[300px] h-[300px] rounded-full bg-[#ffd166]/8 blur-[80px] top-[40%] left-[30%] pointer-events-none"></div>

    <div class="relative z-10 w-full max-w-md">

        {{-- Logo + Header --}}
        <div class="text-center mb-8">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 mb-6 group">
                <div class="w-10 h-10 rounded-xl btn-primary flex items-center justify-center glow-purple">
                    <span class="font-syne font-bold text-white text-lg">L</span>
                </div>
                <span class="font-syne font-bold text-2xl gradient-text">LEXORA</span>
            </a>
            <h1 class="font-syne font-bold text-3xl text-white mb-2">Buat Akun Gratis</h1>
            <p class="text-white/50 text-sm">Mulai perjalanan belajar kosakata Bahasa Inggrismu hari ini</p>
        </div>

        {{-- Perks mini bar --}}
        <div class="flex items-center justify-center gap-4 mb-6">
            <div class="flex items-center gap-1.5 text-xs text-white/40">
                <span class="text-[#06d6a0]">✓</span> Gratis selamanya
            </div>
            <div class="w-px h-3 bg-white/10"></div>
            <div class="flex items-center gap-1.5 text-xs text-white/40">
                <span class="text-[#06d6a0]">✓</span> Tanpa kartu kredit
            </div>
            <div class="w-px h-3 bg-white/10"></div>
            <div class="flex items-center gap-1.5 text-xs text-white/40">
                <span class="text-[#06d6a0]">✓</span> Mulai sekarang
            </div>
        </div>

        {{-- Card --}}
        <div class="glass rounded-3xl p-8 border border-white/8 shadow-2xl animate-in">

            {{-- Global Errors --}}
            @if ($errors->any())
                <div class="mb-5 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/30">
                    <p class="text-red-400 text-sm font-medium mb-1 flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Mohon perbaiki kesalahan berikut:
                    </p>
                    <ul class="list-none space-y-0.5 pl-6">
                        @foreach ($errors->all() as $error)
                            <li class="text-red-400/80 text-xs">• {{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" id="register-form">
                @csrf

                {{-- Nama Lengkap --}}
                <div class="mb-5">
                    <label for="name" class="block text-sm font-medium text-white/70 mb-2">
                        Nama Lengkap
                    </label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        required
                        autofocus
                        autocomplete="name"
                        placeholder="Masukkan nama lengkapmu"
                        class="input-field @error('name') border-red-500/60 bg-red-500/5 @enderror"
                    >
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="mb-5">
                    <label for="email" class="block text-sm font-medium text-white/70 mb-2">
                        Alamat Email
                    </label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autocomplete="email"
                        placeholder="kamu@email.com"
                        class="input-field @error('email') border-red-500/60 bg-red-500/5 @enderror"
                    >
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mb-5">
                    <label for="password" class="block text-sm font-medium text-white/70 mb-2">
                        Password
                        <span class="text-white/30 font-normal ml-1">(min. 8 karakter)</span>
                    </label>
                    <div class="relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                            class="input-field pr-11 @error('password') border-red-500/60 bg-red-500/5 @enderror"
                        >
                        <button type="button" id="toggle-password"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-white/30 hover:text-white/60 transition-colors">
                            <svg id="eye-open" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg id="eye-closed" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>

                    {{-- Password Strength Indicator --}}
                    <div class="mt-2 flex gap-1" id="strength-bars">
                        <div class="h-1 flex-1 rounded-full bg-white/10" id="bar-1"></div>
                        <div class="h-1 flex-1 rounded-full bg-white/10" id="bar-2"></div>
                        <div class="h-1 flex-1 rounded-full bg-white/10" id="bar-3"></div>
                        <div class="h-1 flex-1 rounded-full bg-white/10" id="bar-4"></div>
                    </div>
                    <p class="text-xs text-white/30 mt-1" id="strength-label"></p>

                    @error('password')
                        <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Konfirmasi Password --}}
                <div class="mb-6">
                    <label for="password_confirmation" class="block text-sm font-medium text-white/70 mb-2">
                        Konfirmasi Password
                    </label>
                    <div class="relative">
                        <input
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            placeholder="••••••••"
                            class="input-field pr-11"
                        >
                        <div id="confirm-icon" class="absolute right-3 top-1/2 -translate-y-1/2 hidden">
                            <svg class="w-5 h-5 text-[#06d6a0]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Submit Button --}}
                <button type="submit" id="btn-register"
                        class="w-full py-3.5 rounded-xl btn-primary font-semibold text-base tracking-wide glow-purple relative overflow-hidden">
                    <span id="btn-text">Buat Akun Sekarang 🚀</span>
                    <span id="btn-loading" class="hidden absolute inset-0 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Mendaftarkan...
                    </span>
                </button>

                {{-- Terms note --}}
                <p class="text-center text-xs text-white/25 mt-4 leading-relaxed">
                    Dengan mendaftar, kamu menyetujui penggunaan data untuk keperluan platform pembelajaran ini.
                </p>

            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-white/8"></div>
                <span class="text-white/30 text-xs">sudah punya akun?</span>
                <div class="flex-1 h-px bg-white/8"></div>
            </div>

            {{-- Login Link --}}
            <a href="{{ route('login') }}"
               class="block w-full py-3.5 rounded-xl text-center glass border border-white/10
                      text-sm font-semibold text-white/70 hover:text-white hover:border-white/20
                      hover:bg-white/5 transition-all duration-200">
                Masuk ke Akun →
            </a>

        </div>

        {{-- Back to Landing --}}
        <p class="text-center mt-6">
            <a href="{{ route('landing') }}"
               class="text-xs text-white/30 hover:text-white/50 transition-colors inline-flex items-center gap-1">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Kembali ke halaman utama
            </a>
        </p>

    </div>
</div>

<script>
    // ── Toggle password visibility ──────────────────────────────
    const toggleBtn  = document.getElementById('toggle-password');
    const pwInput    = document.getElementById('password');
    const eyeOpen    = document.getElementById('eye-open');
    const eyeClosed  = document.getElementById('eye-closed');

    toggleBtn.addEventListener('click', () => {
        const isHidden = pwInput.type === 'password';
        pwInput.type   = isHidden ? 'text' : 'password';
        eyeOpen.classList.toggle('hidden', isHidden);
        eyeClosed.classList.toggle('hidden', !isHidden);
    });

    // ── Password strength indicator ─────────────────────────────
    const bars   = [1,2,3,4].map(i => document.getElementById(`bar-${i}`));
    const label  = document.getElementById('strength-label');
    const levels = [
        { color: 'bg-red-500',    text: 'Terlalu lemah',  min: 1 },
        { color: 'bg-orange-400', text: 'Lemah',          min: 2 },
        { color: 'bg-[#ffd166]',  text: 'Cukup kuat',    min: 3 },
        { color: 'bg-[#06d6a0]',  text: 'Kuat 💪',       min: 4 },
    ];

    pwInput.addEventListener('input', () => {
        const val      = pwInput.value;
        let strength   = 0;
        if (val.length >= 8)                    strength++;
        if (/[A-Z]/.test(val))                  strength++;
        if (/[0-9]/.test(val))                  strength++;
        if (/[^A-Za-z0-9]/.test(val))           strength++;

        bars.forEach((bar, i) => {
            bar.className = 'h-1 flex-1 rounded-full transition-colors duration-300';
            if (i < strength) {
                bar.classList.add(levels[strength - 1].color);
            } else {
                bar.classList.add('bg-white/10');
            }
        });

        label.textContent = val.length > 0 ? levels[Math.min(strength, 4) - 1]?.text ?? '' : '';
    });

    // ── Confirm password match indicator ───────────────────────
    const confirmInput = document.getElementById('password_confirmation');
    const confirmIcon  = document.getElementById('confirm-icon');

    confirmInput.addEventListener('input', () => {
        const match = confirmInput.value.length > 0 && confirmInput.value === pwInput.value;
        confirmIcon.classList.toggle('hidden', !match);
    });

    // ── Loading state on submit ─────────────────────────────────
    document.getElementById('register-form').addEventListener('submit', () => {
        document.getElementById('btn-text').classList.add('invisible');
        document.getElementById('btn-loading').classList.remove('hidden');
        document.getElementById('btn-loading').classList.add('flex');
        document.getElementById('btn-register').disabled = true;
    });
</script>

@endsection

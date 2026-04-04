@extends('layouts.guest')

@section('title', 'Masuk ke LEXORA')
@section('meta_description', 'Masuk ke akun LEXORA dan lanjutkan perjalanan belajar kosakata Bahasa Inggrismu.')

@section('content')

<div class="min-h-screen flex items-center justify-center px-4 py-12 relative overflow-hidden">

    {{-- Background Orbs --}}
    <div class="absolute w-[500px] h-[500px] rounded-full bg-[#6c63ff]/15 blur-[100px] top-[-150px] left-[-150px] pointer-events-none"></div>
    <div class="absolute w-[400px] h-[400px] rounded-full bg-[#06d6a0]/10 blur-[100px] bottom-[-100px] right-[-100px] pointer-events-none"></div>

    <div class="relative z-10 w-full max-w-md">

        {{-- Logo + Header --}}
        <div class="text-center mb-8">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 mb-6 group">
                <div class="w-10 h-10 rounded-xl btn-primary flex items-center justify-center glow-purple">
                    <span class="font-syne font-bold text-white text-lg">L</span>
                </div>
                <span class="font-syne font-bold text-2xl gradient-text">LEXORA</span>
            </a>
            <h1 class="font-syne font-bold text-3xl text-white mb-2">Selamat Datang Kembali</h1>
            <p class="text-white/50 text-sm">Masuk untuk melanjutkan perjalanan belajarmu</p>
        </div>

        {{-- Card --}}
        <div class="glass rounded-3xl p-8 border border-white/8 shadow-2xl animate-in">

            {{-- Global Error --}}
            @if ($errors->any() && !$errors->has('email') && !$errors->has('password'))
                <div class="mb-5 flex items-start gap-3 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/30">
                    <svg class="w-4 h-4 text-red-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-red-400 text-sm">{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" id="login-form">
                @csrf

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
                        autofocus
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
                    <div class="flex items-center justify-between mb-2">
                        <label for="password" class="block text-sm font-medium text-white/70">
                            Password
                        </label>
                    </div>
                    <div class="relative">
                        <input
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            placeholder="••••••••"
                            class="input-field pr-11 @error('password') border-red-500/60 bg-red-500/5 @enderror"
                        >
                        {{-- Toggle visibility --}}
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
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-400 flex items-center gap-1">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="checkbox" name="remember" id="remember"
                               class="w-4 h-4 rounded border-white/20 bg-white/5 text-[#6c63ff] cursor-pointer accent-[#6c63ff]">
                        <span class="text-sm text-white/50 group-hover:text-white/70 transition-colors">Ingat saya</span>
                    </label>
                </div>

                {{-- Submit Button --}}
                <button type="submit" id="btn-login"
                        class="w-full py-3.5 rounded-xl btn-primary font-semibold text-base tracking-wide glow-purple relative overflow-hidden">
                    <span id="btn-text">Masuk ke LEXORA</span>
                    <span id="btn-loading" class="hidden absolute inset-0 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Memproses...
                    </span>
                </button>

            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-white/8"></div>
                <span class="text-white/30 text-xs">atau</span>
                <div class="flex-1 h-px bg-white/8"></div>
            </div>

            {{-- Register Link --}}
            <p class="text-center text-sm text-white/50">
                Belum punya akun?
                <a href="{{ route('register') }}"
                   class="text-[#6c63ff] hover:text-[#8b85ff] font-semibold transition-colors ml-1">
                    Daftar sekarang →
                </a>
            </p>

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
    // Toggle password visibility
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

    // Loading state on submit
    document.getElementById('login-form').addEventListener('submit', () => {
        document.getElementById('btn-text').classList.add('invisible');
        document.getElementById('btn-loading').classList.remove('hidden');
        document.getElementById('btn-loading').classList.add('flex');
        document.getElementById('btn-login').disabled = true;
    });
</script>

@endsection

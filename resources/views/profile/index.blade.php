@extends('layouts.app')

@section('title', 'Profil — LEXORA')

@section('content')
<div class="min-h-screen font-dm">

    <style>
        .profile-card { 
            background: var(--card); 
            border: 1px solid var(--border); 
            border-radius: 1.5rem; 
            backdrop-filter: blur(20px); 
            position: relative; 
            overflow: hidden; 
        }
        .profile-card::before { 
            content:''; position:absolute; inset:0; 
            background: radial-gradient(ellipse at 0% 0%, rgba(79, 124, 255, .08) 0%, transparent 60%); 
            pointer-events:none; 
        }

        .stat-box {
            flex: 1;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            padding: 1.25rem 1rem;
            text-align: center;
            transition: all 0.3s ease;
        }
        .stat-box:hover {
            background: rgba(255, 255, 255, 0.06);
            transform: translateY(-2px);
            border-color: rgba(79, 124, 255, 0.3);
        }

        .avatar-circle {
            width: 5rem; height: 5rem;
            border-radius: 9999px;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif;
            font-weight: 800; font-size: 1.75rem;
            color: #fff;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            box-shadow: 0 0 20px rgba(79, 124, 255, 0.3);
            flex-shrink: 0;
        }

        .xp-bar-container { height: 8px; background: rgba(255, 255, 255, 0.05); border-radius: 9999px; overflow: hidden; }
        .xp-bar-fill {
            height: 100%; border-radius: 9999px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            box-shadow: 0 0 12px rgba(79, 124, 255, 0.4);
            transition: width 1s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .form-input-custom {
            width: 100%;
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid var(--border);
            border-radius: 1rem;
            padding: 0.8rem 1.1rem;
            color: #fff;
            font-size: 0.95rem;
            outline: none;
            transition: all 0.2s;
        }
        .form-input-custom:focus {
            background: rgba(255, 255, 255, 0.06);
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 124, 255, 0.1);
        }
        .form-input-custom.is-error { border-color: var(--red); }

        .form-label-custom {
            display: block;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--text-muted);
            margin-bottom: 0.5rem;
            margin-left: 0.25rem;
        }

        .log-item {
            display: flex; align-items: center; justify-content: space-between;
            padding: 1rem; border-radius: 1rem;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid transparent;
            transition: all 0.2s;
        }
        .log-item:hover {
            background: rgba(255, 255, 255, 0.04);
            border-color: var(--border);
        }

        .btn-update {
            width: 100%;
            padding: 1rem;
            border-radius: 1.25rem;
            font-family: 'Syne', sans-serif;
            font-weight: 800;
            color: #fff;
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
            box-shadow: 0 8px 20px rgba(79, 124, 255, 0.25);
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .btn-update:hover { transform: translateY(-2px); box-shadow: 0 12px 24px rgba(79, 124, 255, 0.35); }
    </style>

    @php
        $initials      = collect(explode(' ', $user->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
        $xpPercent     = $stats['xp_percent'] ?? 0;
        $xpForNext     = $stats['xp_to_next_level'] ?? 100;
        $levelName     = 'Pemula';
        $wordsLearned  = $stats['words_learned'] ?? 0;
    @endphp

    <div class="max-w-3xl mx-auto px-4 py-10">

        {{-- ===== BREADCRUMB ===== --}}
        <nav class="fade-up flex items-center gap-2 mb-8 font-dm text-sm" style="color:var(--text-muted);">
            <a href="{{ route('dashboard') }}"
               class="transition-colors duration-200 hover:text-white" style="color:var(--text-muted); text-decoration:none;">
                Dashboard
            </a>
            <span style="color:var(--border);">›</span>
            <span class="text-white font-medium">Profil</span>
        </nav>

        {{-- ===== FLASH ===== --}}
        @if(session('success'))
        <div class="fade-up mb-6 rounded-xl px-5 py-3 flex items-center gap-3 text-sm font-dm"
             style="background:rgba(6,214,160,.1); border:1px solid rgba(6,214,160,.3); color:var(--green);">
            ✅ {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="fade-up mb-6 rounded-xl px-5 py-3 text-sm font-dm"
             style="background:rgba(255,107,107,.08); border:1px solid rgba(255,107,107,.25); color:#ff9999;">
            <p class="font-semibold mb-1">⚠️ Terdapat kesalahan:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            {{-- ============================================================ --}}
            {{-- LEFT COLUMN (lg:3/5)                                          --}}
            {{-- ============================================================ --}}
            <div class="lg:col-span-3 flex flex-col gap-6">

                {{-- ===== IDENTITY CARD ===== --}}
                <div class="profile-card p-6 md:p-8 animate-in delay-1">
                    <div class="flex items-center gap-6 mb-6">
                        <div class="avatar-circle">{{ $initials }}</div>
                        <div class="min-w-0">
                            <h1 class="font-syne font-extrabold text-white text-2xl leading-tight truncate">
                                {{ $user->name }}
                            </h1>
                            <p class="text-sm truncate mt-1" style="color:var(--text-muted);">
                                {{ $user->email }}
                            </p>
                            <div class="flex items-center gap-2 mt-3">
                                <span class="badge-glass" style="color:var(--gold);">
                                    🏆 Level {{ $user->level }}
                                </span>
                                <span class="badge-glass" style="color:var(--primary);">
                                    {{ $levelName }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- XP bar --}}
                    <div>
                        <div class="flex justify-between text-xs font-bold mb-2" style="color:var(--text-muted);">
                            <span>Progress Level</span>
                            <span style="color:var(--primary);">{{ $xpPercent }}%</span>
                        </div>
                        <div class="xp-bar-container">
                            <div class="xp-bar-fill" style="width:{{ $xpPercent }}%;"></div>
                        </div>
                        <p class="text-[11px] mt-2 font-medium" style="color:var(--text-muted);">
                            {{ $xpForNext }} XP lagi untuk naik level
                        </p>
                    </div>
                </div>

                {{-- ===== STATS ===== --}}
                <div class="animate-in delay-2 flex gap-4">
                    <div class="stat-box">
                        <div class="text-2xl font-syne font-extrabold" style="color:var(--primary);">{{ number_format($user->xp) }}</div>
                        <div class="text-[10px] uppercase tracking-widest font-bold mt-1" style="color:var(--text-muted);">Total XP</div>
                    </div>
                    <div class="stat-box">
                        <div class="text-2xl font-syne font-extrabold" style="color:var(--green);">{{ $user->streak }}</div>
                        <div class="text-[10px] uppercase tracking-widest font-bold mt-1" style="color:var(--text-muted);">🔥 Streak</div>
                    </div>
                    <div class="stat-box">
                        <div class="text-2xl font-syne font-extrabold" style="color:var(--gold);">{{ $wordsLearned }}</div>
                        <div class="text-[10px] uppercase tracking-widest font-bold mt-1" style="color:var(--text-muted);">Kosakata</div>
                    </div>
                </div>

                {{-- ===== RECENT XP LOG ===== --}}
                <div class="profile-card p-6 md:p-8 animate-in delay-3">
                    <h2 class="font-syne font-bold text-white text-lg mb-6 flex items-center gap-2">
                        <span>Riwayat Aktivitas</span>
                        <span class="text-xs font-medium px-2 py-0.5 rounded-md bg-white/5 text-[var(--text-muted)]">Terbaru</span>
                    </h2>

                    @if($xpLogs->isEmpty())
                        <div class="text-center py-10">
                            <p class="text-4xl mb-4">✨</p>
                            <p class="font-syne font-bold text-white">Belum ada aktivitas</p>
                            <p class="text-xs mt-2" style="color:var(--text-muted);">
                                Mulailah belajar untuk mengumpulkan XP!
                            </p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($xpLogs as $log)
                            <div class="log-item">
                                <div class="flex items-center gap-4 min-w-0">
                                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0 bg-white/5 border border-white/5 text-lg">
                                        ⚡
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-bold text-sm text-white truncate">
                                            {{ $log->activity ?? 'Aktivitas Belajar' }}
                                        </p>
                                        <p class="text-[11px] mt-0.5" style="color:var(--text-muted);">
                                            {{ $log->created_at ? $log->created_at->diffForHumans() : 'Baru saja' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="font-syne font-extrabold text-sm" style="color:var(--primary);">
                                        +{{ $log->xp_gained ?? 0 }} XP
                                    </p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            {{-- ============================================================ --}}
            {{-- RIGHT COLUMN (lg:2/5) — EDIT FORM                            --}}
            {{-- ============================================================ --}}
            <div class="lg:col-span-2 animate-in delay-3">
                <div class="profile-card p-6 md:p-8 sticky top-8">
                    <h2 class="font-syne font-bold text-white text-lg mb-6">Edit Profil</h2>

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Name --}}
                        <div class="mb-5">
                            <label for="name" class="form-label-custom">Nama Lengkap</label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   class="form-input-custom {{ $errors->has('name') ? 'is-error' : '' }}"
                                   value="{{ old('name', $user->name) }}"
                                   placeholder="Contoh: Budi Santoso"
                                   required>
                            @error('name')
                                <p class="text-xs mt-1.5 ml-1" style="color:var(--red);">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-5">
                            <label for="email" class="form-label-custom">Alamat Email</label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   class="form-input-custom {{ $errors->has('email') ? 'is-error' : '' }}"
                                   value="{{ old('email', $user->email) }}"
                                   placeholder="email@contoh.com"
                                   required>
                            @error('email')
                                <p class="text-xs mt-1.5 ml-1" style="color:var(--red);">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Divider --}}
                        <div class="h-px w-full my-6" style="background:var(--border);"></div>
                        
                        {{-- Current Password --}}
                        <div class="mb-5">
                            <label for="current_password" class="form-label-custom">Password Saat Ini</label>
                            <input type="password"
                                   id="current_password"
                                   name="current_password"
                                   class="form-input-custom {{ $errors->has('current_password') ? 'is-error' : '' }}"
                                   placeholder="••••••••">
                            @error('current_password')
                                <p class="text-xs mt-1.5 ml-1" style="color:var(--red);">{{ $message }}</p>
                            @enderror
                            <p class="text-[10px] mt-2 ml-1" style="color:var(--text-muted);">
                                *Wajib diisi jika ingin mengubah nama, email, atau password.
                            </p>
                        </div>

                        {{-- New Password --}}
                        <div class="mb-5">
                            <label for="password" class="form-label-custom">Password Baru (Opsional)</label>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="form-input-custom {{ $errors->has('password') ? 'is-error' : '' }}"
                                   placeholder="Min. 8 karakter"
                                   autocomplete="new-password">
                            @error('password')
                                <p class="text-xs mt-1.5 ml-1" style="color:var(--red);">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mb-8">
                            <label for="password_confirmation" class="form-label-custom">Konfirmasi Password Baru</label>
                            <input type="password"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   class="form-input-custom"
                                   placeholder="Ulangi password baru"
                                   autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn-update">
                            Simpan Perubahan
                        </button>
                    </form>
                </div>
            </div>

        </div>

        {{-- ===== BACK ===== --}}
        <div class="mt-8 fade-up" style="animation-delay:.35s">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 font-dm text-sm transition-colors duration-200"
               style="color:var(--text-muted); text-decoration:none;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>

    </div>
</div>
@endsection
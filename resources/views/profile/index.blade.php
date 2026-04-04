@extends('layouts.app')

@section('title', 'Profil — LEXORA')

@section('content')
<div class="min-h-screen" style="background:#0d0f1a; font-family:'DM Sans',sans-serif;">

    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg:         #0d0f1a;
            --purple:     #6c63ff;
            --green:      #06d6a0;
            --gold:       #ffd166;
            --card:       #13162a;
            --border:     rgba(108,99,255,0.18);
            --text-muted: #8892b0;
        }
        .font-syne { font-family:'Syne',sans-serif; }
        .font-dm   { font-family:'DM Sans',sans-serif; }

        @keyframes fadeUp {
            from { opacity:0; transform:translateY(16px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .fade-up { animation:fadeUp .45s ease both; }
        .delay-1 { animation-delay:.07s; }
        .delay-2 { animation-delay:.14s; }
        .delay-3 { animation-delay:.21s; }
        .delay-4 { animation-delay:.28s; }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 1.25rem;
        }

        /* avatar */
        .avatar {
            width:5rem; height:5rem;
            border-radius:9999px;
            display:flex; align-items:center; justify-content:center;
            font-family:'Syne',sans-serif;
            font-weight:800; font-size:1.75rem;
            color:#fff;
            background: linear-gradient(135deg, var(--purple) 0%, #9b94ff 100%);
            flex-shrink:0;
        }

        /* stat box */
        .stat-box {
            flex:1;
            background: rgba(255,255,255,.03);
            border: 1px solid var(--border);
            border-radius:1rem;
            padding:1rem .75rem;
            text-align:center;
        }
        .stat-value {
            font-family:'Syne',sans-serif;
            font-weight:800;
            font-size:1.45rem;
            line-height:1;
        }
        .stat-label {
            font-size:.67rem;
            font-weight:600;
            letter-spacing:.06em;
            text-transform:uppercase;
            color:var(--text-muted);
            margin-top:.35rem;
        }

        /* xp bar */
        @keyframes shimmer {
            0%   { background-position:-400px 0; }
            100% { background-position: 400px 0; }
        }
        .xp-fill {
            background: linear-gradient(90deg, var(--purple) 0%, var(--green) 50%, var(--purple) 100%);
            background-size:400px 100%;
            animation:shimmer 2.5s linear infinite;
            height:100%; border-radius:9999px;
            transition: width 1s cubic-bezier(.4,0,.2,1);
        }

        /* form inputs */
        .form-input {
            width:100%;
            background: rgba(255,255,255,.04);
            border: 1px solid var(--border);
            border-radius:.75rem;
            padding:.7rem 1rem;
            color:#fff;
            font-family:'DM Sans',sans-serif;
            font-size:.9rem;
            outline:none;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-input::placeholder { color:var(--text-muted); }
        .form-input:focus {
            border-color: var(--purple);
            box-shadow: 0 0 0 3px rgba(108,99,255,.18);
        }
        .form-input.is-error { border-color:#ff6b6b; }

        .form-label {
            display:block;
            font-family:'DM Sans',sans-serif;
            font-size:.8rem;
            font-weight:500;
            color:var(--text-muted);
            margin-bottom:.4rem;
        }

        /* log row */
        .log-row {
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:1rem;
            padding:.875rem 0;
            border-bottom:1px solid rgba(255,255,255,.04);
        }
        .log-row:last-child { border-bottom:none; }

        /* badge */
        .badge {
            font-family:'Syne',sans-serif;
            font-size:.68rem; font-weight:700;
            letter-spacing:.05em; text-transform:uppercase;
            padding:.2rem .65rem; border-radius:9999px;
        }

        /* submit btn */
        .btn-save {
            width:100%;
            padding:.85rem;
            border-radius:1rem;
            font-family:'Syne',sans-serif;
            font-weight:700; font-size:.95rem;
            color:#fff;
            background: linear-gradient(135deg, var(--purple) 0%, #8b85ff 100%);
            border:none; cursor:pointer;
            transition: transform .2s, opacity .2s;
        }
        .btn-save:hover { transform:translateY(-2px); opacity:.9; }

        ::-webkit-scrollbar { width:6px; }
        ::-webkit-scrollbar-track { background:#0d0f1a; }
        ::-webkit-scrollbar-thumb { background:rgba(108,99,255,.4); border-radius:9999px; }
    </style>

    @php
        $initials      = collect(explode(' ', $user->name))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
        $xpPercent     = $user->xp_progress_percent ?? 0;
        $xpForNext     = $user->xp_for_next_level   ?? 100;
        $levelName     = $user->level_name           ?? 'Pemula';
        $wordsLearned  = method_exists($user, 'wordsLearned') ? $user->wordsLearned() : 0;
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

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">

            {{-- ============================================================ --}}
            {{-- LEFT COLUMN (lg:3/5)                                          --}}
            {{-- ============================================================ --}}
            <div class="lg:col-span-3 flex flex-col gap-5">

                {{-- ===== IDENTITY CARD ===== --}}
                <div class="card p-6 fade-up delay-1">
                    <div class="flex items-center gap-5 mb-5">
                        <div class="avatar">{{ $initials }}</div>
                        <div class="min-w-0">
                            <h1 class="font-syne font-extrabold text-white text-xl leading-tight truncate">
                                {{ $user->name }}
                            </h1>
                            <p class="text-sm truncate mt-0.5" style="color:var(--text-muted);">
                                {{ $user->email }}
                            </p>
                            <div class="flex items-center gap-2 mt-2">
                                <span class="badge" style="background:rgba(255,209,102,.1); color:var(--gold);">
                                    🏆 Level {{ $user->level }}
                                </span>
                                <span class="badge" style="background:rgba(108,99,255,.1); color:var(--purple);">
                                    {{ $levelName }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- XP bar --}}
                    <div>
                        <div class="flex justify-between text-xs font-dm mb-1" style="color:var(--text-muted);">
                            <span>XP ke level berikutnya</span>
                            <span style="color:var(--purple);">{{ $xpPercent }}%</span>
                        </div>
                        <div class="rounded-full overflow-hidden" style="height:7px; background:rgba(108,99,255,.15);">
                            <div class="xp-fill" style="width:{{ $xpPercent }}%;"></div>
                        </div>
                        <p class="text-xs mt-1 font-dm" style="color:var(--text-muted);">
                            {{ $xpForNext }} XP lagi untuk naik level
                        </p>
                    </div>
                </div>

                {{-- ===== STATS ===== --}}
                <div class="fade-up delay-2 flex gap-3">
                    <div class="stat-box">
                        <div class="stat-value" style="color:var(--purple);">{{ number_format($user->xp) }}</div>
                        <div class="stat-label">Total XP</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value" style="color:var(--green);">{{ $user->streak }}</div>
                        <div class="stat-label">🔥 Streak</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-value" style="color:var(--gold);">{{ $wordsLearned }}</div>
                        <div class="stat-label">Kata Dipelajari</div>
                    </div>
                </div>

                {{-- ===== RECENT XP LOG ===== --}}
                <div class="card p-6 fade-up delay-3">
                    <h2 class="font-syne font-bold text-white text-base mb-4">Riwayat XP</h2>

                    @if($recentLogs->isEmpty())
                        <div class="text-center py-8">
                            <p class="text-3xl mb-2">📭</p>
                            <p class="font-syne font-semibold text-white text-sm">Belum ada aktivitas</p>
                            <p class="text-xs mt-1" style="color:var(--text-muted);">
                                Selesaikan lesson pertamamu untuk mendapat XP!
                            </p>
                        </div>
                    @else
                        <div>
                            @foreach($recentLogs as $log)
                            <div class="log-row">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                                         style="background:rgba(108,99,255,.12); border:1px solid var(--border);">
                                        ⚡
                                    </div>
                                    <p class="font-dm text-sm text-white truncate">
                                        {{ $log->activity ?? '—' }}
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <p class="font-syne font-bold text-sm" style="color:var(--gold);">
                                        +{{ $log->xp_gained ?? 0 }} XP
                                    </p>
                                    <p class="text-xs mt-0.5" style="color:var(--text-muted);">
                                        {{ $log->created_at ? $log->created_at->diffForHumans() : '—' }}
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
            <div class="lg:col-span-2 fade-up delay-4">
                <div class="card p-6 sticky top-6">
                    <h2 class="font-syne font-bold text-white text-base mb-5">Edit Profil</h2>

                    <form action="{{ route('profile.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Name --}}
                        <div class="mb-4">
                            <label for="name" class="form-label">Nama</label>
                            <input type="text"
                                   id="name"
                                   name="name"
                                   class="form-input {{ $errors->has('name') ? 'is-error' : '' }}"
                                   value="{{ old('name', $user->name) }}"
                                   placeholder="Nama lengkap"
                                   required>
                            @error('name')
                                <p class="text-xs mt-1" style="color:#ff9999;">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-4">
                            <label for="email" class="form-label">Email</label>
                            <input type="email"
                                   id="email"
                                   name="email"
                                   class="form-input {{ $errors->has('email') ? 'is-error' : '' }}"
                                   value="{{ old('email', $user->email) }}"
                                   placeholder="email@contoh.com"
                                   required>
                            @error('email')
                                <p class="text-xs mt-1" style="color:#ff9999;">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Divider --}}
                        <div style="height:1px; background:var(--border); margin:1.25rem 0;"></div>
                        <p class="font-dm text-xs mb-3" style="color:var(--text-muted);">
                            Kosongkan jika tidak ingin mengubah password.
                        </p>

                        {{-- New Password --}}
                        <div class="mb-4">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password"
                                   id="password"
                                   name="password"
                                   class="form-input {{ $errors->has('password') ? 'is-error' : '' }}"
                                   placeholder="••••••••"
                                   autocomplete="new-password">
                            @error('password')
                                <p class="text-xs mt-1" style="color:#ff9999;">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Confirm Password --}}
                        <div class="mb-6">
                            <label for="password_confirmation" class="form-label">Konfirmasi Password</label>
                            <input type="password"
                                   id="password_confirmation"
                                   name="password_confirmation"
                                   class="form-input"
                                   placeholder="••••••••"
                                   autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn-save">
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
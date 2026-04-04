@extends('layouts.app')

@section('title', 'Dashboard — LEXORA')

@section('content')
<div class="min-h-screen" style="background: #0d0f1a; font-family: 'DM Sans', sans-serif;">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>
        :root {
            --bg:       #0d0f1a;
            --purple:   #6c63ff;
            --green:    #06d6a0;
            --gold:     #ffd166;
            --card:     #13162a;
            --border:   rgba(108,99,255,0.18);
            --text-muted: #8892b0;
        }

        .font-syne  { font-family: 'Syne', sans-serif; }
        .font-dm    { font-family: 'DM Sans', sans-serif; }

        /* --- XP progress bar shimmer --- */
        @keyframes shimmer {
            0%   { background-position: -400px 0; }
            100% { background-position:  400px 0; }
        }
        .xp-bar-fill {
            background: linear-gradient(90deg, var(--purple) 0%, var(--green) 50%, var(--purple) 100%);
            background-size: 400px 100%;
            animation: shimmer 2.5s linear infinite;
            transition: width 1s cubic-bezier(.4,0,.2,1);
        }

        /* --- Stat card glow on hover --- */
        .stat-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 1rem;
            transition: transform .25s, box-shadow .25s;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 32px rgba(108,99,255,.25);
        }

        /* --- Unit card --- */
        .unit-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            transition: transform .25s, box-shadow .25s, border-color .25s;
            position: relative;
            overflow: hidden;
        }
        .unit-card.unlocked:hover {
            transform: translateY(-4px);
            border-color: var(--purple);
            box-shadow: 0 12px 40px rgba(108,99,255,.3);
        }
        .unit-card.locked {
            opacity: .5;
            cursor: not-allowed;
        }
        .unit-card .card-glow {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 30% 20%, rgba(108,99,255,.08) 0%, transparent 70%);
            pointer-events: none;
        }

        /* --- Progress bar inside unit card --- */
        .unit-progress-bar {
            background: rgba(108,99,255,.15);
            border-radius: 9999px;
            height: 6px;
            overflow: hidden;
        }
        .unit-progress-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 1s cubic-bezier(.4,0,.2,1);
        }

        /* --- Fade in on load --- */
        @keyframes fadeUp {
            from { opacity:0; transform:translateY(18px); }
            to   { opacity:1; transform:translateY(0); }
        }
        .fade-up { animation: fadeUp .5s ease both; }
        .delay-1 { animation-delay: .08s; }
        .delay-2 { animation-delay: .16s; }
        .delay-3 { animation-delay: .24s; }
        .delay-4 { animation-delay: .32s; }

        /* --- Badge pill --- */
        .badge {
            font-family: 'Syne', sans-serif;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
            padding: .2rem .65rem;
            border-radius: 9999px;
        }

        /* scrollbar minimal */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0d0f1a; }
        ::-webkit-scrollbar-thumb { background: rgba(108,99,255,.4); border-radius: 9999px; }
    </style>

    <div class="max-w-5xl mx-auto px-4 py-10">

        {{-- ===== GREETING ===== --}}
        <div class="fade-up mb-10">
            <p class="font-dm text-sm mb-1" style="color:var(--text-muted);">
                {{ now()->locale('id')->isoFormat('dddd, D MMMM YYYY') }}
            </p>
            <h1 class="font-syne font-extrabold text-white leading-tight"
                style="font-size:clamp(1.8rem,4vw,2.5rem);">
                Halo, {{ $user->name }} 👋
            </h1>
            <p class="font-dm mt-1" style="color:var(--text-muted);">
                Teruslah belajar — setiap kata membawamu lebih jauh.
            </p>
        </div>

        {{-- ===== STATS ===== --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10 fade-up delay-1">

            {{-- XP --}}
            <div class="stat-card p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl"
                         style="background:rgba(108,99,255,.15);">⚡</div>
                    <span class="font-syne font-semibold text-white text-sm">Total XP</span>
                </div>
                <p class="font-syne font-extrabold text-3xl" style="color:var(--purple);">
                    {{ number_format($user->xp) }}
                </p>

                {{-- XP Progress to next level --}}
                @php
                    $xpInLevel   = $user->xp % 100;
                    $xpNeeded    = 100;
                    $xpPercent   = ($xpInLevel / $xpNeeded) * 100;
                @endphp
                <div class="mt-3">
                    <div class="flex justify-between text-xs mb-1" style="color:var(--text-muted);">
                        <span>{{ $xpInLevel }} / {{ $xpNeeded }} XP</span>
                        <span>Level {{ $user->level + 1 }}</span>
                    </div>
                    <div class="xp-bar-track rounded-full overflow-hidden" style="height:6px;background:rgba(108,99,255,.15);">
                        <div class="xp-bar-fill rounded-full" style="width:{{ $xpPercent }}%; height:100%;"></div>
                    </div>
                </div>
            </div>

            {{-- LEVEL --}}
            <div class="stat-card p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl"
                         style="background:rgba(255,209,102,.12);">🏆</div>
                    <span class="font-syne font-semibold text-white text-sm">Level</span>
                </div>
                <p class="font-syne font-extrabold text-3xl" style="color:var(--gold);">
                    {{ $user->level }}
                </p>
                <p class="text-xs mt-2" style="color:var(--text-muted);">
                    @if($user->level < 5)   Pemula
                    @elseif($user->level < 10) Menengah
                    @elseif($user->level < 20) Mahir
                    @else Expert
                    @endif
                </p>
            </div>

            {{-- STREAK --}}
            <div class="stat-card p-5">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl"
                         style="background:rgba(6,214,160,.12);">🔥</div>
                    <span class="font-syne font-semibold text-white text-sm">Streak</span>
                </div>
                <p class="font-syne font-extrabold text-3xl" style="color:var(--green);">
                    {{ $user->streak }} <span class="text-base font-semibold">hari</span>
                </p>
                <p class="text-xs mt-2" style="color:var(--text-muted);">
                    @if($user->streak >= 7)
                        🏅 Luar biasa! Terus pertahankan!
                    @elseif($user->streak >= 3)
                        💪 Kamu sedang on fire!
                    @elseif($user->streak >= 1)
                        ✨ Bagus, jangan berhenti!
                    @else
                        Mulai streakmu hari ini!
                    @endif
                </p>
            </div>

        </div>

        {{-- ===== FLASH MESSAGE ===== --}}
        @if(session('success'))
        <div class="fade-up delay-2 mb-6 rounded-xl px-5 py-3 flex items-center gap-3 text-sm font-dm"
             style="background:rgba(6,214,160,.1); border:1px solid rgba(6,214,160,.3); color:var(--green);">
            <span>✅</span>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="fade-up delay-2 mb-6 rounded-xl px-5 py-3 flex items-center gap-3 text-sm font-dm"
             style="background:rgba(255,80,80,.08); border:1px solid rgba(255,80,80,.25); color:#ff6b6b;">
            <span>⚠️</span>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        {{-- ===== UNITS SECTION ===== --}}
        <div class="fade-up delay-3">

            <div class="flex items-center justify-between mb-5">
                <h2 class="font-syne font-bold text-white text-xl">Unit Pembelajaran</h2>
                <span class="badge" style="background:rgba(108,99,255,.15); color:var(--purple);">
                    {{ $units->count() }} Unit
                </span>
            </div>

            @if($units->isEmpty())
                <div class="rounded-2xl p-10 text-center" style="background:var(--card); border:1px solid var(--border);">
                    <p class="text-4xl mb-3">📚</p>
                    <p class="font-syne font-semibold text-white">Belum ada unit tersedia</p>
                    <p class="text-sm mt-1" style="color:var(--text-muted);">Hubungi administrator untuk menambahkan konten.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($units as $unit)
                    @php
                        $isUnlocked = $unit->unlocked;
                        $progress   = $unit->progress; // 0–100
                        $isDone     = $progress >= 100;
                    @endphp

                    <div class="unit-card {{ $isUnlocked ? 'unlocked' : 'locked' }}">
                        <div class="card-glow"></div>

                        @if($isUnlocked)
                            <a href="{{ route('units.show', $unit->id) }}" class="block p-6">
                        @else
                            <div class="p-6">
                        @endif

                            {{-- Header: icon + badge --}}
                            <div class="flex items-start justify-between mb-4">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl"
                                     style="background:rgba(108,99,255,.12); border:1px solid var(--border);">
                                    {{ $unit->icon ?? '📖' }}
                                </div>

                                @if(!$isUnlocked)
                                    <span class="badge" style="background:rgba(255,255,255,.06); color:var(--text-muted);">
                                        🔒 Terkunci
                                    </span>
                                @elseif($isDone)
                                    <span class="badge" style="background:rgba(6,214,160,.12); color:var(--green);">
                                        ✅ Selesai
                                    </span>
                                @else
                                    <span class="badge" style="background:rgba(108,99,255,.12); color:var(--purple);">
                                        ▶ Lanjutkan
                                    </span>
                                @endif
                            </div>

                            {{-- Title & desc --}}
                            <h3 class="font-syne font-bold text-white text-base leading-snug mb-1">
                                {{ $unit->title }}
                            </h3>
                            @if($unit->description)
                            <p class="text-xs leading-relaxed mb-4" style="color:var(--text-muted);">
                                {{ Str::limit($unit->description, 80) }}
                            </p>
                            @endif

                            {{-- Lesson count --}}
                            <div class="flex items-center gap-1 text-xs mb-4" style="color:var(--text-muted);">
                                <span>📝</span>
                                <span>{{ $unit->lessons->count() }} Lesson</span>
                            </div>

                            {{-- Progress bar --}}
                            @if($isUnlocked)
                            <div>
                                <div class="flex justify-between text-xs mb-1" style="color:var(--text-muted);">
                                    <span>Progress</span>
                                    <span style="color:{{ $isDone ? 'var(--green)' : 'var(--purple)' }}">
                                        {{ $progress }}%
                                    </span>
                                </div>
                                <div class="unit-progress-bar">
                                    <div class="unit-progress-fill"
                                         style="width:{{ $progress }}%;
                                                background: {{ $isDone
                                                    ? 'linear-gradient(90deg,#06d6a0,#0abf7e)'
                                                    : 'linear-gradient(90deg,#6c63ff,#9b94ff)' }};">
                                    </div>
                                </div>
                            </div>
                            @else
                            <p class="text-xs" style="color:var(--text-muted);">
                                Selesaikan unit sebelumnya untuk membuka unit ini.
                            </p>
                            @endif

                        @if($isUnlocked)
                            </a>
                        @else
                            </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ===== FOOTER AREA ===== --}}
        <div class="fade-up delay-4 mt-12 flex justify-between items-center text-xs" style="color:var(--text-muted);">
            <span class="font-syne">LEXORA &copy; {{ date('Y') }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit"
                        class="font-dm hover:text-white transition-colors duration-200"
                        style="color:var(--text-muted);">
                    Keluar ↗
                </button>
            </form>
        </div>

    </div>
</div>
@endsection
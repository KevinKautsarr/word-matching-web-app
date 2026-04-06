@extends('layouts.app')

@section('title', 'Dashboard — LEXORA')

@section('content')
<div class="min-h-screen font-dm">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    <style>

        .font-syne  { font-family: 'Syne', sans-serif; }
        .font-dm    { font-family: 'DM Sans', sans-serif; }

        /* --- XP progress bar shimmer --- */


        /* --- Unit card --- */
        .unit-card {
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 1.5rem;
            box-shadow: 0 12px 40px rgba(0,0,0,0.2), inset 0 1px 0 rgba(255,255,255,0.04);
            transition: all 0.18s ease-out;
            position: relative;
            overflow: hidden;
        }
        .unit-card.unlocked:hover {
            transform: translateY(-4px) scale(1.015);
            border-color: rgba(108,99,255,0.3);
            box-shadow: 0 16px 45px rgba(0,0,0,0.35), 0 0 30px rgba(108,99,255,0.15), inset 0 1px 0 rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.05);
        }
        .unit-card.unlocked:active {
            transform: translateY(0) scale(0.96);
        }
        .unit-card.locked {
            opacity: .65;
            cursor: not-allowed;
            filter: grayscale(100%);
            box-shadow: none;
        }
        .unit-card .card-glow {
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 50% 0%, rgba(108,99,255,.12) 0%, transparent 60%);
            pointer-events: none;
            opacity: 0.6;
            transition: opacity .3s ease;
        }
        .unit-card.unlocked:hover .card-glow {
            opacity: 1;
        }

        /* --- Progress bar inside unit card --- */
        .unit-progress-bar {
            background: rgba(255,255,255,0.04);
            border-radius: 9999px;
            height: 8px;
            overflow: hidden;
            box-shadow: inset 0 1px 2px rgba(0,0,0,0.2);
        }
        .unit-progress-fill {
            height: 100%;
            border-radius: 9999px;
            transition: width 1.2s cubic-bezier(.4,0,.2,1);
            position: relative;
            overflow: hidden;
        }
        .unit-progress-fill::after {
            content:'';
            position:absolute;
            top:0; left:0; right:0; bottom:0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transform: translateX(-100%);
            animation: shimmer 2s infinite;
        }
        @keyframes shimmer {
            100% { transform: translateX(100%); }
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

        /* --- Custom Stat Card --- */
        .stat-card {
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.06);
            box-shadow: 0 12px 40px rgba(0,0,0,0.2), inset 0 1px 0 rgba(255,255,255,0.04);
            border-radius: 1.5rem;
            transition: all 0.18s ease-out;
        }
        .stat-card:hover {
            transform: translateY(-4px) scale(1.01);
            border-color: rgba(6,214,160,0.3);
            box-shadow: 0 16px 45px rgba(0,0,0,0.35), 0 0 35px rgba(6,214,160,0.15);
            background: rgba(255,255,255,0.05);
        }

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

    <div class="px-4 py-6">

        {{-- ===== GREETING & EMOTIONAL UX ===== --}}
        <div class="fade-up mb-10">
            @php
                $hour = now()->format('H');
                $greeting = 'Halo';
                if ($hour < 12) $greeting = 'Selamat pagi';
                elseif ($hour < 15) $greeting = 'Selamat siang';
                elseif ($hour < 18) $greeting = 'Selamat sore';
                else $greeting = 'Selamat malam';

                $messages = [
                    'Kamu luar biasa, lanjutkan!',
                    'Setiap langkah kecil membawamu lebih dekat ke tujuan.',
                    'Fokus dan konsistensi adalah kunci.',
                    'Terus belajar, jangan menyerah!',
                    'Lexora bangga dengan progresmu hari ini!'
                ];
                $randomMsg = $messages[array_rand($messages)];
            @endphp
            <p class="font-dm text-sm mb-1 font-semibold tracking-wide" style="color:var(--text-muted);">
                {{ now()->locale('id')->isoFormat('dddd, D MMMM') }}
            </p>
            <h1 class="font-syne font-extrabold text-themeText leading-tight mb-2"
                style="font-size:clamp(1.8rem,4vw,2.5rem);">
                {{ $greeting }}, {{ explode(' ', $user->name)[0] }} 👋
            </h1>
            <p class="font-dm text-sm" style="color:var(--purple); opacity: 0.9;">
                {{ $randomMsg }}
            </p>
        </div>

        {{-- ===== DAILY GOAL ===== --}}
        @php
            // Calculate daily goal logic inline for display
            $lastPlayed = $user->last_played_at ? clone $user->last_played_at->startOfDay() : null;
            $today = now()->startOfDay();
            $dailyGoal = ($lastPlayed && $lastPlayed->eq($today)) ? ($user->daily_goal_progress ?? 0) : 0;
            $goalTarget = 3;
            $goalPercent = min(100, ($dailyGoal / $goalTarget) * 100);
            $goalDone = $dailyGoal >= $goalTarget;
        @endphp
        
        <div class="fade-up delay-1 mb-10 stat-card p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-6 group cursor-default shadow-[0_8px_30px_rgba(0,0,0,0.12)] border-[rgba(255,255,255,0.04)]">
            <div class="flex items-center gap-5">
                <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-3xl transition-transform group-hover:scale-105 duration-300
                    {{ $goalDone ? 'bg-[rgba(6,214,160,0.15)] text-[#06d6a0] glow-green' : 'bg-[rgba(108,99,255,0.15)] text-[var(--purple)] glow-purple' }}">
                    {{ $goalDone ? '🎉' : '🎯' }}
                </div>
                <div>
                    <h3 class="font-syne font-extrabold text-xl mb-1 group-hover:text-[var(--text)] transition-colors text-[var(--text-muted)]">Target Harian</h3>
                    <p class="text-sm font-medium" style="color:var(--text-muted); opacity: 0.85;">
                        {{ $goalDone ? 'Luar biasa! Target hari ini selesai. (+50 XP)' : 'Selesaikan ' . $goalTarget . ' lesson hari ini untuk bonus XP!' }}
                    </p>
                </div>
            </div>
            
            <div class="w-full sm:w-1/3 min-w-[200px]">
                <div class="flex justify-between text-xs mb-2 font-bold" style="color:var(--text-muted);">
                    <span>Progress</span>
                    <span style="color:{{ $goalDone ? 'var(--green)' : 'var(--purple)' }}; {{ $goalDone ? 'text-shadow: 0 0 10px rgba(6,214,160,0.5);' : '' }}">
                        {{ $dailyGoal }} / {{ $goalTarget }} Lesson
                    </span>
                </div>
                <div class="unit-progress-bar" x-data="{ width: 0 }" x-init="setTimeout(() => width = {{ $goalPercent }}, 200)">
                    <div class="unit-progress-fill glow-{{ $goalDone ? 'green' : 'purple' }}"
                         :style="`width: ${width}%; background: {{ $goalDone ? 'linear-gradient(90deg, #06d6a0, #0abf7e)' : 'linear-gradient(90deg, #6c63ff, #06d6a0)' }}`">
                    </div>
                </div>
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
                <h2 class="font-syne font-extrabold text-themeText text-2xl tracking-tight">Unit Pembelajaran</h2>
                <span class="badge border border-[var(--border)] shadow-sm" style="background:rgba(108,99,255,.15); color:var(--purple);">
                    {{ $units->count() }} Unit
                </span>
            </div>

            @if($units->isEmpty())
                <div class="rounded-2xl p-10 text-center card">
                    <p class="text-4xl mb-3">📚</p>
                    <p class="font-syne font-semibold text-themeText">Belum ada unit tersedia</p>
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
                                    <span class="badge border border-[var(--border)]" style="background:rgba(255,255,255,.05); color:var(--text-muted); box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                                        🔒 Terkunci
                                    </span>
                                @elseif($isDone)
                                    <span class="badge border border-[#06d6a0]/20" style="background:rgba(6,214,160,.12); color:var(--green); box-shadow: 0 2px 12px rgba(6,214,160,0.15);">
                                        ✅ Selesai
                                    </span>
                                @else
                                    <span class="badge border border-[var(--purple)]/20" style="background:rgba(108,99,255,.12); color:var(--purple); box-shadow: 0 2px 12px rgba(108,99,255,0.15);">
                                        ▶ Lanjutkan
                                    </span>
                                @endif
                            </div>

                            {{-- Title & desc --}}
                            <h3 class="font-syne font-extrabold text-themeText text-lg leading-snug mb-1.5 tracking-tight group-hover:text-[var(--purple)] transition-colors">
                                {{ $unit->title }}
                            </h3>
                            @if($unit->description)
                            <p class="text-sm leading-relaxed mb-5" style="color:var(--text-muted); opacity: 0.85;">
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
                                <div class="flex justify-between text-xs mb-1.5 font-semibold" style="color:var(--text-muted);">
                                    <span>Progress</span>
                                    <span style="color:{{ $isDone ? 'var(--green)' : 'var(--purple)' }}; text-shadow: 0 0 8px {{ $isDone ? 'rgba(6,214,160,0.4)' : 'rgba(108,99,255,0.4)' }};">
                                        {{ $progress }}%
                                    </span>
                                </div>
                                <div class="unit-progress-bar" x-data="{ width: 0 }" x-init="setTimeout(() => width = {{ $progress }}, 100)">
                                    <div class="unit-progress-fill glow-{{ $isDone ? 'green' : 'purple' }}"
                                         :style="`width: ${width}%; background: {{ $isDone ? 'linear-gradient(90deg, #06d6a0, #0abf7e)' : 'linear-gradient(90deg, #6c63ff, #06d6a0)' }}`">
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
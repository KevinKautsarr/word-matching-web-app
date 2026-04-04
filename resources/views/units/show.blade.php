@extends('layouts.app')

@section('title', $unit->title . ' — LEXORA')

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
        .fade-up  { animation:fadeUp .45s ease both; }
        .delay-1  { animation-delay:.07s; }
        .delay-2  { animation-delay:.14s; }
        .delay-3  { animation-delay:.21s; }

        .lesson-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 1.25rem;
            transition: transform .25s, box-shadow .25s, border-color .25s;
            position: relative;
            overflow: hidden;
        }
        .lesson-card.unlocked:hover {
            transform: translateY(-3px);
            border-color: var(--purple);
            box-shadow: 0 10px 36px rgba(108,99,255,.28);
        }
        .lesson-card.locked {
            opacity: .45;
            cursor: not-allowed;
        }
        .lesson-card .card-glow {
            position:absolute; inset:0;
            background: radial-gradient(circle at 20% 20%, rgba(108,99,255,.07) 0%, transparent 65%);
            pointer-events:none;
        }

        .badge {
            font-family:'Syne',sans-serif;
            font-size:.68rem;
            font-weight:700;
            letter-spacing:.05em;
            text-transform:uppercase;
            padding:.22rem .7rem;
            border-radius:9999px;
        }

        .breadcrumb-link {
            color: var(--text-muted);
            font-size:.85rem;
            transition: color .2s;
            text-decoration:none;
        }
        .breadcrumb-link:hover { color:#fff; }

        .unit-header-card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 1.5rem;
            position: relative;
            overflow: hidden;
        }
        .unit-header-card::before {
            content:'';
            position:absolute; inset:0;
            background: radial-gradient(ellipse at 0% 0%, rgba(108,99,255,.12) 0%, transparent 60%);
            pointer-events:none;
        }

        .step-line {
            position:absolute;
            left:1.6rem;
            top:100%;
            width:2px;
            height:1.5rem;
            background: var(--border);
        }

        ::-webkit-scrollbar { width:6px; }
        ::-webkit-scrollbar-track { background:#0d0f1a; }
        ::-webkit-scrollbar-thumb { background:rgba(108,99,255,.4); border-radius:9999px; }
    </style>

    <div class="max-w-3xl mx-auto px-4 py-10">

        {{-- ===== BREADCRUMB ===== --}}
        <nav class="fade-up flex items-center gap-2 mb-8 font-dm text-sm" style="color:var(--text-muted);">
            <a href="{{ route('dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span style="color:var(--border);">›</span>
            <span class="text-white font-medium">{{ $unit->title }}</span>
        </nav>

        {{-- ===== UNIT HEADER ===== --}}
        <div class="unit-header-card p-7 mb-8 fade-up delay-1">
            <div class="flex items-start gap-5">
                <div class="w-16 h-16 rounded-2xl flex items-center justify-center text-3xl flex-shrink-0"
                     style="background:rgba(108,99,255,.13); border:1px solid var(--border);">
                    {{ $unit->icon ?? '📖' }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        <h1 class="font-syne font-extrabold text-white text-2xl leading-tight">
                            {{ $unit->title }}
                        </h1>
                        @php
                            $totalLessons     = $lessons->count();
                            $completedLessons = $lessons->where('completed', true)->count();
                            $unitDone         = $totalLessons > 0 && $completedLessons === $totalLessons;
                        @endphp
                        @if($unitDone)
                            <span class="badge" style="background:rgba(6,214,160,.12); color:var(--green);">✅ Selesai</span>
                        @else
                            <span class="badge" style="background:rgba(108,99,255,.12); color:var(--purple);">▶ In Progress</span>
                        @endif
                    </div>

                    @if($unit->description)
                    <p class="font-dm text-sm leading-relaxed mb-4" style="color:var(--text-muted);">
                        {{ $unit->description }}
                    </p>
                    @endif

                    {{-- Progress --}}
                    @if($totalLessons > 0)
                    @php $progressPercent = round(($completedLessons / $totalLessons) * 100); @endphp
                    <div>
                        <div class="flex justify-between text-xs mb-1 font-dm" style="color:var(--text-muted);">
                            <span>{{ $completedLessons }} / {{ $totalLessons }} lesson selesai</span>
                            <span style="color:{{ $unitDone ? 'var(--green)' : 'var(--purple)' }}">{{ $progressPercent }}%</span>
                        </div>
                        <div class="rounded-full overflow-hidden" style="height:6px; background:rgba(108,99,255,.15);">
                            <div class="rounded-full transition-all duration-700"
                                 style="width:{{ $progressPercent }}%; height:100%;
                                        background:{{ $unitDone
                                            ? 'linear-gradient(90deg,#06d6a0,#0abf7e)'
                                            : 'linear-gradient(90deg,#6c63ff,#9b94ff)' }};"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ===== LESSONS LIST ===== --}}
        <div class="fade-up delay-2">
            <div class="flex items-center justify-between mb-5">
                <h2 class="font-syne font-bold text-white text-lg">Daftar Lesson</h2>
                <span class="badge" style="background:rgba(108,99,255,.1); color:var(--text-muted);">
                    {{ $totalLessons }} Lesson
                </span>
            </div>

            @if($lessons->isEmpty())
                <div class="rounded-2xl p-10 text-center" style="background:var(--card); border:1px solid var(--border);">
                    <p class="text-4xl mb-3">📭</p>
                    <p class="font-syne font-semibold text-white">Belum ada lesson</p>
                    <p class="text-sm mt-1" style="color:var(--text-muted);">Unit ini belum memiliki lesson.</p>
                </div>
            @else
                <div class="flex flex-col gap-3">
                    @foreach($lessons as $index => $lesson)
                    @php
                        $isCompleted = $lesson->completed;
                        $isUnlocked  = $lesson->unlocked;
                        $num         = $index + 1;
                    @endphp

                    <div class="lesson-card {{ $isUnlocked ? 'unlocked' : 'locked' }}">
                        <div class="card-glow"></div>

                        @if($isUnlocked)
                            <a href="{{ route('lessons.show', $lesson->id) }}" class="flex items-center gap-4 p-5 no-underline">
                        @else
                            <div class="flex items-center gap-4 p-5">
                        @endif

                            {{-- Number / status bubble --}}
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center font-syne font-bold text-sm flex-shrink-0"
                                 style="
                                    background: {{ $isCompleted
                                        ? 'rgba(6,214,160,.15)'
                                        : ($isUnlocked ? 'rgba(108,99,255,.15)' : 'rgba(255,255,255,.05)') }};
                                    border: 1px solid {{ $isCompleted
                                        ? 'rgba(6,214,160,.3)'
                                        : ($isUnlocked ? 'rgba(108,99,255,.3)' : 'rgba(255,255,255,.08)') }};
                                    color: {{ $isCompleted
                                        ? 'var(--green)'
                                        : ($isUnlocked ? 'var(--purple)' : 'var(--text-muted)') }};
                                 ">
                                {{ $isCompleted ? '✓' : $num }}
                            </div>

                            {{-- Lesson info --}}
                            <div class="flex-1 min-w-0">
                                <p class="font-syne font-semibold text-sm leading-snug {{ $isUnlocked ? 'text-white' : '' }}"
                                   style="{{ !$isUnlocked ? 'color:var(--text-muted)' : '' }}">
                                    {{ $lesson->title }}
                                </p>
                                @if($lesson->description)
                                <p class="text-xs mt-0.5 truncate" style="color:var(--text-muted);">
                                    {{ $lesson->description }}
                                </p>
                                @endif
                            </div>

                            {{-- Right side info --}}
                            <div class="flex items-center gap-3 flex-shrink-0">
                                @if(isset($lesson->xp_reward) && $lesson->xp_reward > 0)
                                <span class="badge" style="background:rgba(255,209,102,.1); color:var(--gold);">
                                    +{{ $lesson->xp_reward }} XP
                                </span>
                                @endif

                                @if(!$isUnlocked)
                                    <span class="text-lg">🔒</span>
                                @elseif($isCompleted)
                                    <span class="text-lg">✅</span>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                                         viewBox="0 0 24 24" stroke="currentColor" style="color:var(--purple);">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 5l7 7-7 7"/>
                                    </svg>
                                @endif
                            </div>

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

        {{-- ===== BACK BUTTON ===== --}}
        <div class="fade-up delay-3 mt-10">
            <a href="{{ route('dashboard') }}"
               class="inline-flex items-center gap-2 font-dm text-sm transition-colors duration-200"
               style="color:var(--text-muted);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>

    </div>
</div>
@endsection
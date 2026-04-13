@extends('layouts.app')

@section('title', $unit->title . ' — LEXORA')

@section('content')
<div class="min-h-screen font-dm overflow-x-hidden">

    {{-- ========= STYLES ========= --}}
    <style>
        .lesson-card { background: var(--card); border: 1px solid var(--border); border-radius: 1.25rem; transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275); position: relative; overflow: hidden; backdrop-filter: blur(16px); box-sizing: border-box; max-width: 100%; }
        .lesson-card.unlocked:hover { transform: translateY(-4px); border-color: rgba(79, 124, 255, 0.4); box-shadow: 0 10px 36px rgba(79, 124, 255, 0.2); background: rgba(255, 255, 255, 0.05); }
        .lesson-card.locked { opacity: .45; filter: grayscale(1); }
        .lesson-card .card-glow { position:absolute; inset:0; background: radial-gradient(circle at 20% 20%, rgba(79, 124, 255, .1) 0%, transparent 65%); pointer-events:none; }

        .unit-header-card { background: var(--card); border: 1px solid var(--border); border-radius: 1.5rem; position: relative; overflow: hidden; backdrop-filter: blur(20px); }
        .unit-header-card::before { content:''; position:absolute; inset:0; background: radial-gradient(ellipse at 0% 0%, rgba(79, 124, 255, .12) 0%, transparent 60%); pointer-events:none; }

        .badge { font-family:'Syne',sans-serif; font-size:.68rem; font-weight:700; text-transform:uppercase; padding:.22rem .7rem; border-radius:9999px; }
        .breadcrumb-link { color: var(--text-muted); font-size:.85rem; transition: color .2s; }
        .breadcrumb-link:hover { color:#fff; }

        @keyframes fadeUp { from { opacity:0; transform:translateY(16px); } to { opacity:1; transform:translateY(0); } }
        .fade-up  { animation:fadeUp .45s ease both; }
        .delay-1  { animation-delay:.07s; }
        .delay-2  { animation-delay:.14s; }
        .delay-3  { animation-delay:.21s; }
    </style>

    <div class="max-w-3xl mx-auto px-4 py-10" style="overflow-x:hidden; max-width:100%; box-sizing:border-box;">

        {{-- ===== BREADCRUMB ===== --}}
        <nav class="fade-up flex items-center gap-2 mb-8 text-sm" style="color:var(--text-muted);">
            <a href="{{ route('dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span style="color:var(--border);">›</span>
            <span class="text-white font-medium">{{ $unit->title }}</span>
        </nav>

        {{-- ===== UNIT HEADER ===== --}}
        <div class="unit-header-card p-7 mb-8 fade-up delay-1">
            <div class="flex flex-col sm:flex-row items-start gap-4 sm:gap-5">
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
                            <span class="badge" style="background:rgba(79, 124, 255, .12); color:var(--primary);">▶ In Progress</span>
                        @endif
                    </div>

                    @if($unit->description)
                    <p class="font-dm text-sm leading-relaxed mb-4" style="color:var(--text-muted);">
                        {{ $unit->description }}
                    </p>
                    @endif

                    {{-- Progress Bar --}}
                    @if($totalLessons > 0)
                    @php $progressPercent = round(($completedLessons / $totalLessons) * 100); @endphp
                    <div>
                        <div class="flex justify-between text-xs mb-1.5 font-bold" style="color:var(--text-muted);">
                            <span>{{ $completedLessons }} / {{ $totalLessons }} lesson selesai</span>
                            <span style="color:{{ $unitDone ? 'var(--green)' : 'var(--primary)' }}">{{ $progressPercent }}%</span>
                        </div>
                        <div class="rounded-full overflow-hidden" style="height:10px; background:rgba(255,255,255,.05);">
                            <div class="rounded-full transition-all duration-700 shadow-[0_0_12px_rgba(79,124,255,0.4)]"
                                 style="width:{{ $progressPercent }}%; height:100%;
                                        background:{{ $unitDone
                                            ? 'linear-gradient(90deg,#06d6a0,#0abf7e)'
                                            : 'linear-gradient(90deg, var(--primary), var(--primary-light))' }};"></div>
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
                <div class="rounded-2xl p-10 text-center card">
                    <p class="text-4xl mb-3">📭</p>
                    <p class="font-syne font-semibold text-white">Belum ada lesson</p>
                </div>
            @else
                <div class="flex flex-col gap-3">
                    @foreach($lessons as $index => $lesson)
                        <x-lesson-card :lesson="$lesson" :num="$index + 1" />
                    @endforeach
                </div>
            @endif
        </div>

        {{-- ===== BACK BUTTON ===== --}}
        <div class="fade-up delay-3 mt-10">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 text-sm" style="color:var(--text-muted);">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke Dashboard
            </a>
        </div>

    </div>
</div>
@endsection
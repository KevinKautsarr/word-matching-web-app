@extends('layouts.app')

@section('title', 'Word Matching — ' . $lesson->title . ' — LEXORA')

@section('content')
<div class="min-h-screen font-dm">

    {{-- ========= EXTERNAL ASSETS ========= --}}
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/game/match-madness.css') }}">

    {{-- ========= GAME CONFIG (Pass Blade data to JS) ========= --}}
    <script>
        window.GameConfig = {
            allVocab: @json($vocabularies->map(fn($v) => ['id'=>$v->id,'word'=>$v->word,'meaning'=>$v->meaning])),
            attemptToken: '{{ $attemptToken }}',
            csrfToken: '{{ csrf_token() }}',
            submitUrl: '{{ route("game.submit", $lesson->id) }}',
            exitUrl: '{{ route("lessons.show", $lesson->id) }}'
        };
    </script>

    <div class="max-w-3xl mx-auto px-4 py-6">

        {{-- ========= TOP BAR ========= --}}
        <div class="fade-up flex items-center justify-between mb-5">
            <div>
                <p class="text-xs font-dm" style="color:var(--text-muted);">
                    {{ $lesson->unit->title ?? 'Unit' }}
                </p>
                <h1 class="font-syne font-bold text-white text-lg leading-tight">
                    {{ $lesson->title }}
                </h1>
            </div>
            <button onclick="confirmExit()"
                    class="flex items-center gap-1.5 font-dm text-sm px-4 py-2 rounded-xl transition-all"
                    style="background:rgba(255,99,99,.08); border:1px solid rgba(255,99,99,.2); color:#ff9999;">
                ✕ Keluar
            </button>
        </div>

        {{-- ========= HUD CLEAN (Premium) ========= --}}
        <div class="hud-clean fade-up">
            <div class="stage-badge" id="stage-badge">
                Stage <span id="stage-number">1</span>
            </div>

            <div class="stage-dots">
                <div class="dot active" id="dot-1"></div>
                <div class="dot" id="dot-2"></div>
                <div class="dot" id="dot-3"></div>
            </div>

            <div class="hud-row">
                <div class="hud-timer" id="hud-timer">50</div>
                <div class="hud-progress" id="hud-progress">0/10</div>
            </div>

            <div class="progress-bar">
                <div id="progress-fill"></div>
            </div>
        </div>

        {{-- ========= GAME GRID ========= --}}
        @if($vocabularies->isEmpty())
            <div class="empty-card fade-up delay-2">
                <p class="text-4xl mb-3">📭</p>
                <p class="font-syne font-semibold text-white">Tidak ada vocabulary</p>
                <p class="text-sm mt-1" style="color:var(--text-muted);">Lesson ini belum memiliki kosakata.</p>
                <a href="{{ route('lessons.show', $lesson->id) }}"
                   class="inline-block mt-4 font-dm text-sm px-5 py-2 rounded-xl transition-all"
                   style="background:rgba(108,99,255,.15);border:1px solid var(--border);color:var(--purple);">
                    ← Kembali
                </a>
            </div>
        @else
            <div class="game-grid fade-up delay-2" id="game-grid">
                <div>
                    <p class="text-xs text-center mb-2 font-dm uppercase tracking-wider" style="color:var(--text-muted);">🇮🇩 Indonesia</p>
                    <div id="col-left" class="col-container"></div>
                </div>
                <div>
                    <p class="text-xs text-center mb-2 font-dm uppercase tracking-wider" style="color:var(--text-muted);">🇬🇧 English</p>
                    <div id="col-right" class="col-container"></div>
                </div>
            </div>
        @endif

    </div>

    {{-- ========= OVERLAYS ========= --}}
    <div class="submit-overlay" id="submit-overlay">
        <div class="submit-card">
            <div class="spinner"></div>
            <p class="font-syne font-bold text-white text-lg mb-1" id="submit-status">Menyimpan hasil...</p>
            <p class="font-dm text-sm" style="color:var(--text-muted);">Mohon tunggu sebentar</p>
        </div>
    </div>

    <div class="stage-overlay" id="stage-overlay">
        <h2 class="stage-title" id="stage-msg">Stage 1</h2>
        <p class="text-white opacity-60 font-dm tracking-widest uppercase text-center text-xs mb-1" id="stage-sub">Mulai Tantangan</p>
        <p id="stage-time-info" class="text-white mt-1 mb-8 font-dm text-sm opacity-70"></p>
        
        <div id="countdown-area" class="hidden">
            <div id="countdown-number" class="text-7xl font-syne font-black text-white glow-primary animate-bounce">3</div>
        </div>

        <button id="stage-start-btn" class="stage-btn" onclick="handleStartClick()">Mulai Stage 1</button>
    </div>

    {{-- ========= LOAD GAME ENGINE ========= --}}
    <script src="{{ asset('js/game/match-madness.js') }}"></script>

</div>
@endsection
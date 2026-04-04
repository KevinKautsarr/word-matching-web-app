@extends('layouts.app')

@section('title', $lesson->title . ' — LEXORA')

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
            position: relative;
            overflow: hidden;
        }
        .card::before {
            content:'';
            position:absolute; inset:0;
            background: radial-gradient(ellipse at 0% 0%, rgba(108,99,255,.07) 0%, transparent 60%);
            pointer-events:none;
        }

        .badge {
            font-family:'Syne',sans-serif;
            font-size:.68rem; font-weight:700;
            letter-spacing:.05em; text-transform:uppercase;
            padding:.22rem .7rem; border-radius:9999px;
        }

        .breadcrumb-link {
            color: var(--text-muted);
            font-size:.85rem;
            transition: color .2s;
            text-decoration:none;
        }
        .breadcrumb-link:hover { color:#fff; }

        /* vocab table */
        .vocab-table { width:100%; border-collapse:collapse; }
        .vocab-table th {
            font-family:'Syne',sans-serif;
            font-size:.7rem; font-weight:700;
            letter-spacing:.07em; text-transform:uppercase;
            color:var(--text-muted);
            padding:.6rem 1rem;
            text-align:left;
            border-bottom:1px solid var(--border);
        }
        .vocab-table th:first-child { width:3rem; text-align:center; }
        .vocab-table td {
            font-family:'DM Sans',sans-serif;
            font-size:.875rem;
            color:#c8d0e0;
            padding:.85rem 1rem;
            border-bottom:1px solid rgba(255,255,255,.04);
            vertical-align:top;
            line-height:1.5;
        }
        .vocab-table td:first-child {
            text-align:center;
            color:var(--text-muted);
            font-size:.8rem;
        }
        .vocab-table tr:last-child td { border-bottom:none; }
        .vocab-table tbody tr {
            transition: background .18s;
        }
        .vocab-table tbody tr:hover {
            background: rgba(108,99,255,.05);
        }
        .word-cell {
            font-family:'Syne',sans-serif;
            font-weight:600;
            color:#fff;
        }
        .meaning-cell { color:var(--green); }
        .example-cell {
            color:var(--text-muted);
            font-style:italic;
            font-size:.82rem;
        }

        /* CTA button */
        .btn-cta {
            display:block; width:100%;
            padding:1rem;
            border-radius:1rem;
            font-family:'Syne',sans-serif;
            font-weight:700; font-size:1rem;
            text-align:center; text-decoration:none;
            transition: transform .2s, box-shadow .2s, opacity .2s;
            border:none; cursor:pointer;
        }
        .btn-cta:hover {
            transform:translateY(-2px);
            box-shadow:0 10px 32px rgba(108,99,255,.35);
            opacity:.92;
        }
        .btn-cta-start {
            background: linear-gradient(135deg, var(--purple) 0%, #8b85ff 100%);
            color:#fff;
        }
        .btn-cta-retry {
            background: rgba(108,99,255,.12);
            border:1px solid rgba(108,99,255,.3) !important;
            color:var(--purple);
        }

        ::-webkit-scrollbar { width:6px; }
        ::-webkit-scrollbar-track { background:#0d0f1a; }
        ::-webkit-scrollbar-thumb { background:rgba(108,99,255,.4); border-radius:9999px; }
    </style>

    <div class="max-w-3xl mx-auto px-4 py-10">

        {{-- ===== BREADCRUMB ===== --}}
        <nav class="fade-up flex items-center gap-2 mb-8 font-dm text-sm flex-wrap" style="color:var(--text-muted);">
            <a href="{{ route('dashboard') }}" class="breadcrumb-link">Dashboard</a>
            <span style="color:var(--border);">›</span>
            <a href="{{ route('units.show', $lesson->unit->id) }}" class="breadcrumb-link">
                {{ $lesson->unit->title ?? 'Unit' }}
            </a>
            <span style="color:var(--border);">›</span>
            <span class="text-white font-medium">{{ $lesson->title }}</span>
        </nav>

        {{-- ===== COMPLETED BANNER ===== --}}
        @if($completed)
        <div class="fade-up mb-5 rounded-xl px-5 py-3 flex items-center gap-3 text-sm font-dm"
             style="background:rgba(6,214,160,.1); border:1px solid rgba(6,214,160,.3); color:var(--green);">
            <span class="text-lg">✅</span>
            <span class="font-semibold">Sudah Diselesaikan</span>
            <span style="color:rgba(6,214,160,.6);">— Kamu boleh mengulang untuk meningkatkan skor.</span>
        </div>
        @endif

        {{-- ===== LESSON HEADER ===== --}}
        <div class="card p-7 mb-5 fade-up delay-1">
            <div class="flex items-start justify-between gap-4 flex-wrap">
                <div class="flex-1 min-w-0">
                    <h1 class="font-syne font-extrabold text-white text-2xl leading-tight mb-2">
                        {{ $lesson->title }}
                    </h1>
                    @if($lesson->description)
                    <p class="font-dm text-sm leading-relaxed" style="color:var(--text-muted);">
                        {{ $lesson->description }}
                    </p>
                    @endif
                </div>
                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    @if($lesson->xp_reward && $lesson->xp_reward > 0)
                    <span class="badge" style="background:rgba(255,209,102,.1); color:var(--gold);">
                        ⚡ +{{ $lesson->xp_reward }} XP
                    </span>
                    @endif
                    <span class="badge" style="background:rgba(108,99,255,.1); color:var(--text-muted);">
                        📝 {{ $lesson->vocabularies->count() }} Kata
                    </span>
                </div>
            </div>
        </div>

        {{-- ===== VOCABULARY TABLE ===== --}}
        <div class="card mb-5 fade-up delay-2">
            <div class="flex items-center justify-between px-6 pt-5 pb-4">
                <h2 class="font-syne font-bold text-white text-base">Daftar Kosakata</h2>
                <span class="badge" style="background:rgba(108,99,255,.1); color:var(--purple);">
                    {{ $lesson->vocabularies->count() }} Vocab
                </span>
            </div>

            @if($lesson->vocabularies->isEmpty())
                <div class="text-center px-6 pb-8 pt-2">
                    <p class="text-3xl mb-2">📭</p>
                    <p class="font-syne font-semibold text-white text-sm">Belum ada vocabulary</p>
                    <p class="text-xs mt-1" style="color:var(--text-muted);">
                        Lesson ini belum memiliki kosakata.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="vocab-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Word</th>
                                <th>Meaning</th>
                                <th>Example Sentence</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($lesson->vocabularies as $vocab)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="word-cell">{{ $vocab->word }}</td>
                                <td class="meaning-cell">{{ $vocab->meaning }}</td>
                                <td class="example-cell">
                                    {{ $vocab->example_sentence ?? '—' }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- ===== CTA BUTTON ===== --}}
        <div class="fade-up delay-3">
            <a href="{{ route('game.play', $lesson->id) }}"
               class="btn-cta {{ $completed ? 'btn-cta-retry' : 'btn-cta-start' }}">
                {{ $completed ? '🔄 Ulangi Game' : '🎯 Mulai Word Matching' }}
            </a>
        </div>

        {{-- ===== BACK ===== --}}
        <div class="fade-up delay-4 mt-6">
            <a href="{{ route('units.show', $lesson->unit->id) }}"
               class="inline-flex items-center gap-2 font-dm text-sm transition-colors duration-200"
               style="color:var(--text-muted); text-decoration:none;">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Kembali ke {{ $lesson->unit->title ?? 'Unit' }}
            </a>
        </div>

    </div>
</div>
@endsection